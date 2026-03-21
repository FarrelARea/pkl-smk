<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = User::with('studentClasses')->where('role', 'student');

        if ($request->has('school_id')) {
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
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'school_id' => 'required|exists:schools,id',
        ]);

        $data['role'] = 'student';
        // Note: Password is automatically hashed by User model's 'hashed' password cast

        $student = User::create($data);

        return response()->json($student, 201);
    }

    public function show(int $id): JsonResponse
    {
        $student = User::with('school', 'studentClasses', 'school.school')->findOrFail($id);

        return response()->json($student);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $student = User::findOrFail($id);

        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $id,
            'password' => 'sometimes|required|string|min:6',
            'school_id' => 'sometimes|required|exists:schools,id',
        ]);

        // Password is automatically hashed by User model's 'hashed' password cast
        // No need to call Hash::make() explicitly

        $student->update($data);

        return response()->json($student);
    }

    public function destroy(int $id): JsonResponse
    {
        $student = User::findOrFail($id);
        $student->delete();

        return response()->json(['message' => 'Student deleted successfully']);
    }

    public function assignClass(Request $request, int $id): JsonResponse
    {
        $student = User::where('role', 'student')->findOrFail($id);

        $data = $request->validate([
            'class_ids' => 'sometimes|array',
            'class_ids.*' => 'exists:classes,id',
            'class_id' => 'sometimes|exists:classes,id',
        ]);

        if (isset($data['class_ids'])) {
            $syncData = collect($data['class_ids'])->mapWithKeys(fn($id) => [$id => ['role' => 'student']])->all();
            $student->classes()->sync($syncData);
        } elseif (isset($data['class_id'])) {
            $student->classes()->sync([$data['class_id'] => ['role' => 'student']]);
        }

        return response()->json(['message' => 'Student class assignments updated']);
    }

    public function internshipStatus(int $id): JsonResponse
    {
        $student = User::findOrFail($id);

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
