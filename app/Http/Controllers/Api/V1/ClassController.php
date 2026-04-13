<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = SchoolClass::with('school');

        if ($request->has('school_id')) {
            $query->where('school_id', $request->school_id);
        }

        if ($request->has('academic_year')) {
            $query->where('academic_year', $request->academic_year);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('name', 'like', '%' . $search . '%');
        }

        $classes = $query->paginate($request->get('per_page', 15));

        return response()->json($classes);
    }

    public function academicYears(): JsonResponse
    {
        $years = SchoolClass::distinct()->orderBy('academic_year', 'desc')->pluck('academic_year');

        return response()->json($years);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'academic_year' => 'required|string|max:9',
            'school_id' => 'required|exists:schools,id',
        ]);

        $schoolClass = SchoolClass::create($data);

        return response()->json($schoolClass, 201);
    }

    public function show(int $id): JsonResponse
    {
        $schoolClass = SchoolClass::with('school')->findOrFail($id);

        return response()->json($schoolClass);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $schoolClass = SchoolClass::findOrFail($id);

        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'academic_year' => 'sometimes|required|string|max:9',
            'school_id' => 'sometimes|required|exists:schools,id',
        ]);

        $schoolClass->update($data);

        return response()->json($schoolClass);
    }

    public function destroy(int $id): JsonResponse
    {
        $schoolClass = SchoolClass::findOrFail($id);
        $schoolClass->delete();

        return response()->json(['message' => 'Class deleted successfully']);
    }
}
