<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\DocumentRequirement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DocumentRequirementController extends Controller
{
    public function index(): JsonResponse
    {
        $requirements = DocumentRequirement::with(['schoolClass', 'teacher'])->get();
        return response()->json($requirements);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'class_id' => 'nullable|exists:classes,id',
            'teacher_id' => 'nullable|exists:users,id',
            'min_documents' => 'required|integer|min:0',
            'max_documents' => 'required|integer|min:1',
        ]);

        if (empty($data['class_id']) && empty($data['teacher_id'])) {
            return response()->json(['error' => 'class_id atau teacher_id harus diisi.'], 422);
        }

        if ($data['max_documents'] < $data['min_documents']) {
            return response()->json(['error' => 'max_documents tidak boleh lebih kecil dari min_documents.'], 422);
        }

        $requirement = DocumentRequirement::create($data);

        return response()->json($requirement->load(['schoolClass', 'teacher']), 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $requirement = DocumentRequirement::findOrFail($id);

        $data = $request->validate([
            'class_id' => 'nullable|exists:classes,id',
            'teacher_id' => 'nullable|exists:users,id',
            'min_documents' => 'sometimes|required|integer|min:0',
            'max_documents' => 'sometimes|required|integer|min:1',
        ]);

        $min = $data['min_documents'] ?? $requirement->min_documents;
        $max = $data['max_documents'] ?? $requirement->max_documents;

        if ($max < $min) {
            return response()->json(['error' => 'max_documents tidak boleh lebih kecil dari min_documents.'], 422);
        }

        $requirement->update($data);

        return response()->json($requirement->fresh(['schoolClass', 'teacher']));
    }

    public function destroy(int $id): JsonResponse
    {
        $requirement = DocumentRequirement::findOrFail($id);
        $requirement->delete();

        return response()->json(['message' => 'Konfigurasi berhasil dihapus.']);
    }
}
