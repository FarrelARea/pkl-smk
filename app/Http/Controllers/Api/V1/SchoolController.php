<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\School;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SchoolController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = School::query();
        
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        $schools = $query->paginate($request->get('per_page', 15));
        
        return response()->json($schools);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
        ]);

        $school = School::create($data);
        
        return response()->json($school, 201);
    }

    public function show(int $id): JsonResponse
    {
        $school = School::with(['classes', 'users'])->findOrFail($id);
        $school->teachers_count = $school->users()->where('role', 'teacher')->count();
        $school->students_count = $school->users()->where('role', 'student')->count();
        
        return response()->json($school);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $school = School::findOrFail($id);
        
        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
        ]);

        $school->update($data);
        
        return response()->json($school);
    }

    public function destroy(int $id): JsonResponse
    {
        $school = School::findOrFail($id);
        $school->delete();
        
        return response()->json(['message' => 'School deleted successfully']);
    }
}
