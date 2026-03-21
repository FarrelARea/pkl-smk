<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\StudentDocument;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DocumentApprovalController extends Controller
{
    public function teacherDocuments(int $studentId): JsonResponse
    {
        $teacher = auth('api')->user();

        $student = User::findOrFail($studentId);

        // Validate teacher has student in their class
        $teacherClassIds = $teacher->teacherClasses()->pluck('classes.id');
        $studentClassIds = $student->studentClasses()->pluck('classes.id');

        $sharedClasses = $teacherClassIds->intersect($studentClassIds);

        if ($sharedClasses->isEmpty()) {
            return response()->json(['error' => 'Unauthorized: student bukan bagian dari kelas Anda.'], 403);
        }

        $documents = StudentDocument::where('student_id', $studentId)
            ->with(['approvedBy'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['documents' => $documents]);
    }

    public function approve(int $id): JsonResponse
    {
        $user = auth('api')->user();
        $document = StudentDocument::findOrFail($id);

        if ($document->status !== 'pending') {
            return response()->json(['error' => 'Hanya dokumen berstatus pending yang dapat di-approve.'], 422);
        }

        $document->update([
            'status' => 'approved',
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);

        return response()->json([
            'message' => 'Dokumen berhasil di-approve.',
            'document' => $document->fresh(['approvedBy']),
        ]);
    }

    public function reject(Request $request, int $id): JsonResponse
    {
        $document = StudentDocument::findOrFail($id);

        $data = $request->validate([
            'teacher_note' => 'required|string',
        ]);

        if ($document->status !== 'pending') {
            return response()->json(['error' => 'Hanya dokumen berstatus pending yang dapat di-reject.'], 422);
        }

        $document->update([
            'status' => 'rejected',
            'teacher_note' => $data['teacher_note'],
        ]);

        return response()->json([
            'message' => 'Dokumen berhasil di-reject.',
            'document' => $document->fresh(),
        ]);
    }

    public function supervisorApprove(int $id): JsonResponse
    {
        $user = auth('api')->user();

        if (!$user->isCompanySupervisor()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $document = StudentDocument::findOrFail($id);

        $document->update([
            'status' => 'approved',
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);

        return response()->json([
            'message' => 'Dokumen berhasil di-approve oleh supervisor.',
            'document' => $document->fresh(['approvedBy']),
        ]);
    }
}
