<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TeacherController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = User::with('teacherClasses')->where('role', 'teacher');

        if ($request->has('school_id')) {
            $query->where('school_id', $request->school_id);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        $teachers = $query->paginate($request->get('per_page', 15));

        return response()->json($teachers);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'school_id' => 'required|exists:schools,id',
        ]);

        $data['password'] = Hash::make($data['password']);
        $data['role'] = 'teacher';

        $teacher = User::create($data);

        return response()->json($teacher, 201);
    }

    public function show(int $id): JsonResponse
    {
        $teacher = User::with('school', 'teacherClasses')->findOrFail($id);

        return response()->json($teacher);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $teacher = User::findOrFail($id);

        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $id,
            'password' => 'sometimes|required|string|min:6',
            'school_id' => 'sometimes|required|exists:schools,id',
        ]);

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $teacher->update($data);

        return response()->json($teacher);
    }

    public function destroy(int $id): JsonResponse
    {
        $teacher = User::findOrFail($id);
        $teacher->delete();

        return response()->json(['message' => 'Teacher deleted successfully']);
    }

    public function assignClass(Request $request, int $id): JsonResponse
    {
        $teacher = User::where('role', 'teacher')->findOrFail($id);

        $data = $request->validate([
            'class_ids' => 'sometimes|array',
            'class_ids.*' => 'exists:classes,id',
            'class_id' => 'sometimes|exists:classes,id',
        ]);

        if (isset($data['class_ids'])) {
            $syncData = collect($data['class_ids'])->mapWithKeys(fn($id) => [$id => ['role' => 'teacher']])->all();
            $teacher->classes()->sync($syncData);
        } elseif (isset($data['class_id'])) {
            $teacher->classes()->syncWithoutDetaching([$data['class_id'] => ['role' => 'teacher']]);
        }

        return response()->json(['message' => 'Teacher class assignments updated']);
    }
}
