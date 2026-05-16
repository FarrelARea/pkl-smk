<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\TeacherStudentAssignment;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TeacherStudentAssignmentController extends Controller
{
    private function resolveAccessibleTeacher(Request $request, int $teacherId): User
    {
        $teacher = User::findOrFail($teacherId);

        if (!$teacher->isTeacher()) {
            abort(response()->json(['error' => 'User yang dipilih bukan guru.'], 422));
        }

        if (!$teacher->belongsToAdminSchool($request->user())) {
            abort(response()->json(['error' => 'Forbidden - insufficient permissions'], 403));
        }

        return $teacher;
    }

    private function resolveAccessibleStudent(Request $request, int $studentId): User
    {
        $student = User::findOrFail($studentId);

        if (!$student->isStudent()) {
            abort(response()->json(['error' => 'User yang dipilih bukan murid.'], 422));
        }

        if (!$student->belongsToAdminSchool($request->user())) {
            abort(response()->json(['error' => 'Forbidden - insufficient permissions'], 403));
        }

        return $student;
    }

    public function index(Request $request): JsonResponse
    {
        $query = TeacherStudentAssignment::with(['teacher', 'student']);

        if (!$request->user()->isSuperAdmin()) {
            $query->where('school_id', $request->user()->school_id);
        }

        if ($request->has('teacher_id')) {
            $teacher = $this->resolveAccessibleTeacher($request, (int) $request->teacher_id);
            $query->where('teacher_id', $teacher->id);
        }

        if ($request->has('student_id')) {
            $student = $this->resolveAccessibleStudent($request, (int) $request->student_id);
            $query->where('student_id', $student->id);
        }

        return response()->json($query->orderBy('created_at', 'desc')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'teacher_id' => 'required|exists:users,id',
            'student_id' => 'required|exists:users,id',
        ]);

        $teacher = $this->resolveAccessibleTeacher($request, $data['teacher_id']);
        $student = $this->resolveAccessibleStudent($request, $data['student_id']);

        if ($teacher->school_id !== $student->school_id) {
            return response()->json(['error' => 'Guru dan murid harus berasal dari sekolah yang sama.'], 422);
        }

        $assignment = TeacherStudentAssignment::firstOrCreate(
            [
                'teacher_id' => $teacher->id,
                'student_id' => $student->id,
            ],
            [
                'school_id' => $student->school_id,
            ]
        );

        if (!$assignment->school_id) {
            $assignment->update(['school_id' => $student->school_id]);
        }

        return response()->json($assignment->load(['teacher', 'student']), 201);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $assignment = TeacherStudentAssignment::findOrFail($id);

        if (!$request->user()->isSuperAdmin() && $assignment->school_id !== $request->user()->school_id) {
            return response()->json(['error' => 'Forbidden - insufficient permissions'], 403);
        }

        $assignment->delete();

        return response()->json(['message' => 'Assignment berhasil dihapus.']);
    }
}
