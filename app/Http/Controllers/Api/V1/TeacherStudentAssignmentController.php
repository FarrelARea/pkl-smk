<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\TeacherStudentAssignment;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TeacherStudentAssignmentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = TeacherStudentAssignment::with(['teacher', 'student']);

        if ($request->has('teacher_id')) {
            $query->where('teacher_id', $request->teacher_id);
        }

        if ($request->has('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        return response()->json($query->orderBy('created_at', 'desc')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'teacher_id' => 'required|exists:users,id',
            'student_id' => 'required|exists:users,id',
        ]);

        $teacher = User::findOrFail($data['teacher_id']);
        $student = User::findOrFail($data['student_id']);

        if (!$teacher->isTeacher()) {
            return response()->json(['error' => 'User yang dipilih bukan guru.'], 422);
        }

        if (!$student->isStudent()) {
            return response()->json(['error' => 'User yang dipilih bukan murid.'], 422);
        }

        $assignment = TeacherStudentAssignment::firstOrCreate([
            'teacher_id' => $data['teacher_id'],
            'student_id' => $data['student_id'],
        ]);

        return response()->json($assignment->load(['teacher', 'student']), 201);
    }

    public function destroy(int $id): JsonResponse
    {
        $assignment = TeacherStudentAssignment::findOrFail($id);
        $assignment->delete();

        return response()->json(['message' => 'Assignment berhasil dihapus.']);
    }
}
