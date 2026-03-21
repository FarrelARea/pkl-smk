<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\StudentDocument;
use App\Services\DocumentRequirementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StudentDocumentController extends Controller
{
    public function __construct(
        protected DocumentRequirementService $requirementService
    ) {}

    public function store(Request $request): JsonResponse
    {
        $user = auth('api')->user();

        $data = $request->validate([
            'internship_id' => 'required|exists:internships,id',
            'title' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf,doc,docx|max:10240',
            'parent_id' => 'nullable|exists:student_documents,id',
        ]);

        // Validate parent_id belongs to this student and is rejected
        if (!empty($data['parent_id'])) {
            $parent = StudentDocument::where('id', $data['parent_id'])
                ->where('student_id', $user->id)
                ->first();

            if (!$parent) {
                return response()->json(['error' => 'Invalid parent document'], 422);
            }
        }

        // Validate max_documents limit (count approved docs)
        $requirement = $this->requirementService->findForStudent($user);
        if ($requirement) {
            $approvedCount = StudentDocument::where('student_id', $user->id)
                ->where('internship_id', $data['internship_id'])
                ->where('status', 'approved')
                ->count();

            if ($approvedCount >= $requirement->max_documents) {
                return response()->json([
                    'error' => "Batas maksimum dokumen ({$requirement->max_documents}) sudah tercapai.",
                ], 422);
            }
        }

        $file = $request->file('file');
        $filePath = $file->store('student-documents', 'public');
        $fileType = $file->getClientOriginalExtension();

        $document = StudentDocument::create([
            'student_id' => $user->id,
            'internship_id' => $data['internship_id'],
            'parent_id' => $data['parent_id'] ?? null,
            'title' => $data['title'],
            'file_path' => $filePath,
            'file_type' => strtolower($fileType),
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Dokumen berhasil diupload.',
            'document' => $document,
        ], 201);
    }

    public function destroy(int $id): JsonResponse
    {
        $user = auth('api')->user();
        $document = StudentDocument::findOrFail($id);

        if ($document->student_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($document->status !== 'pending') {
            return response()->json(['error' => 'Hanya dokumen berstatus pending yang dapat dihapus.'], 403);
        }

        Storage::disk('public')->delete($document->file_path);
        $document->delete();

        return response()->json(['message' => 'Dokumen berhasil dihapus.']);
    }
}
