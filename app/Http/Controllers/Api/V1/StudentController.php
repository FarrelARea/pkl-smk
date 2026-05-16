<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = User::with('studentClasses')
            ->where('role', 'student')
            ->accessibleByAdmin($user);

        if ($request->has('school_id') && $user->isSuperAdmin()) {
            $query->where('school_id', $request->school_id);
        }

        if ($request->has('class_id')) {
            $query->whereHas('studentClasses', function ($q) use ($request) {
                $q->where('class_user.class_id', $request->class_id);
            });
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        $students = $query->paginate($request->get('per_page', 15));

        return response()->json($students);
    }

    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'school_id' => 'required|exists:schools,id',
        ]);

        if (!$user->isSuperAdmin()) {
            $data['school_id'] = $user->school_id;
        }

        $data['role'] = 'student';

        $student = User::create($data);

        return response()->json($student, 201);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $student = User::with('school', 'studentClasses', 'school.school')
            ->where('role', 'student')
            ->findOrFail($id);

        if (!$student->belongsToAdminSchool($request->user())) {
            return response()->json(['error' => 'Forbidden - insufficient permissions'], 403);
        }

        return response()->json($student);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $student = User::where('role', 'student')->findOrFail($id);

        if (!$student->belongsToAdminSchool($user)) {
            return response()->json(['error' => 'Forbidden - insufficient permissions'], 403);
        }

        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $id,
            'password' => 'sometimes|required|string|min:6',
            'school_id' => 'sometimes|required|exists:schools,id',
        ]);

        if (!$user->isSuperAdmin()) {
            $data['school_id'] = $user->school_id;
        }

        $student->update($data);

        return response()->json($student);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $student = User::where('role', 'student')->findOrFail($id);

        if (!$student->belongsToAdminSchool($request->user())) {
            return response()->json(['error' => 'Forbidden - insufficient permissions'], 403);
        }

        $student->delete();

        return response()->json(['message' => 'Student deleted successfully']);
    }

    public function assignClass(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $student = User::where('role', 'student')->findOrFail($id);

        if (!$student->belongsToAdminSchool($user)) {
            return response()->json(['error' => 'Forbidden - insufficient permissions'], 403);
        }

        $data = $request->validate([
            'class_ids' => 'sometimes|array',
            'class_ids.*' => 'exists:classes,id',
            'class_id' => 'sometimes|exists:classes,id',
        ]);

        $classIds = collect($data['class_ids'] ?? [])->merge(isset($data['class_id']) ? [$data['class_id']] : [])->filter();

        if (!$user->isSuperAdmin() && $classIds->isNotEmpty()) {
            $allowedClassIds = SchoolClass::query()->accessibleByAdmin($user)->pluck('id');
            if ($classIds->diff($allowedClassIds)->isNotEmpty()) {
                return response()->json(['error' => 'Forbidden - invalid class selection'], 403);
            }
        }

        if (isset($data['class_ids'])) {
            $syncData = collect($data['class_ids'])->mapWithKeys(fn($classId) => [$classId => ['role' => 'student']])->all();
            $student->classes()->sync($syncData);
        } elseif (isset($data['class_id'])) {
            $student->classes()->sync([$data['class_id'] => ['role' => 'student']]);
        }

        return response()->json(['message' => 'Student class assignments updated']);
    }

    public function internshipStatus(Request $request, int $id): JsonResponse
    {
        $student = User::where('role', 'student')->findOrFail($id);

        if (!$student->belongsToAdminSchool($request->user())) {
            return response()->json(['error' => 'Forbidden - insufficient permissions'], 403);
        }

        $activeInternship = $student->internships()
            ->with('company', 'supervisor')
            ->where('status', 'active')
            ->first();

        return response()->json([
            'student_id' => $student->id,
            'has_active_internship' => !is_null($activeInternship),
            'data' => $activeInternship,
        ]);
    }
}
