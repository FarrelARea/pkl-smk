<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\DocumentRequirement;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DocumentRequirementController extends Controller
{
    private function resolveAccessibleClass(Request $request, ?int $classId): ?SchoolClass
    {
        if (!$classId) {
            return null;
        }

        $class = SchoolClass::findOrFail($classId);

        if (!$class->belongsToAdminSchool($request->user())) {
            abort(response()->json(['error' => 'Forbidden - insufficient permissions'], 403));
        }

        return $class;
    }

    private function resolveAccessibleTeacher(Request $request, ?int $teacherId): ?User
    {
        if (!$teacherId) {
            return null;
        }

        $teacher = User::findOrFail($teacherId);

        if (!$teacher->isTeacher()) {
            abort(response()->json(['error' => 'User yang dipilih bukan guru.'], 422));
        }

        if (!$teacher->belongsToAdminSchool($request->user())) {
            abort(response()->json(['error' => 'Forbidden - insufficient permissions'], 403));
        }

        return $teacher;
    }

    public function index(Request $request): JsonResponse
    {
        $query = DocumentRequirement::with(['schoolClass', 'teacher']);

        if (!$request->user()->isSuperAdmin()) {
            $query->where('school_id', $request->user()->school_id);
        }

        $requirements = $query->get();
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

        $class = $this->resolveAccessibleClass($request, $data['class_id'] ?? null);
        $teacher = $this->resolveAccessibleTeacher($request, $data['teacher_id'] ?? null);
        $schoolId = $class?->school_id ?? $teacher?->school_id;

        if ($class && $teacher && $class->school_id !== $teacher->school_id) {
            return response()->json(['error' => 'Kelas dan guru harus berasal dari sekolah yang sama.'], 422);
        }

        $data['school_id'] = $schoolId;
        $requirement = DocumentRequirement::create($data);

        return response()->json($requirement->load(['schoolClass', 'teacher']), 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $requirement = DocumentRequirement::findOrFail($id);

        if (!$request->user()->isSuperAdmin() && $requirement->school_id !== $request->user()->school_id) {
            return response()->json(['error' => 'Forbidden - insufficient permissions'], 403);
        }

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

        $class = $this->resolveAccessibleClass($request, $data['class_id'] ?? $requirement->class_id);
        $teacher = $this->resolveAccessibleTeacher($request, $data['teacher_id'] ?? $requirement->teacher_id);

        if ($class && $teacher && $class->school_id !== $teacher->school_id) {
            return response()->json(['error' => 'Kelas dan guru harus berasal dari sekolah yang sama.'], 422);
        }

        $data['school_id'] = $class?->school_id ?? $teacher?->school_id;
        $requirement->update($data);

        return response()->json($requirement->fresh(['schoolClass', 'teacher']));
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $requirement = DocumentRequirement::findOrFail($id);

        if (!$request->user()->isSuperAdmin() && $requirement->school_id !== $request->user()->school_id) {
            return response()->json(['error' => 'Forbidden - insufficient permissions'], 403);
        }

        $requirement->delete();

        return response()->json(['message' => 'Konfigurasi berhasil dihapus.']);
    }
}
