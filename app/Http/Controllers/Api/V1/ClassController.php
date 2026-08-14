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
        $user = $request->user();
        $query = SchoolClass::with('school')->accessibleByAdmin($user);

        if ($request->has('school_id') && $user->isSuperAdmin()) {
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

    public function academicYears(Request $request): JsonResponse
    {
        $years = SchoolClass::query()
            ->accessibleByAdmin($request->user())
            ->distinct()
            ->orderBy('academic_year', 'desc')
            ->pluck('academic_year');

        return response()->json($years);
    }

    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'academic_year' => 'required|string|max:9',
            'school_id' => 'required|exists:schools,id',
        ]);

        if (!$user->isSuperAdmin()) {
            $data['school_id'] = $user->school_id;
        }

        $schoolClass = SchoolClass::create($data);

        return response()->json($schoolClass, 201);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $schoolClass = SchoolClass::with('school')->findOrFail($id);

        if (!$schoolClass->belongsToAdminSchool($request->user())) {
            return response()->json(['error' => 'Forbidden - insufficient permissions'], 403);
        }

        return response()->json($schoolClass);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $schoolClass = SchoolClass::findOrFail($id);

        if (!$schoolClass->belongsToAdminSchool($user)) {
            return response()->json(['error' => 'Forbidden - insufficient permissions'], 403);
        }

        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'academic_year' => 'sometimes|required|string|max:9',
            'school_id' => 'sometimes|required|exists:schools,id',
        ]);

        if (!$user->isSuperAdmin()) {
            $data['school_id'] = $user->school_id;
        }

        $schoolClass->update($data);

        return response()->json($schoolClass);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $schoolClass = SchoolClass::findOrFail($id);

        if (!$schoolClass->belongsToAdminSchool($request->user())) {
            return response()->json(['error' => 'Forbidden - insufficient permissions'], 403);
        }

        $schoolClass->delete();

        return response()->json(['message' => 'Class deleted successfully']);
    }
}
