<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\FinalAssessment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FinalAssessmentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = FinalAssessment::with(['student', 'internship', 'schoolAdmin']);

        if ($request->has('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->has('internship_id')) {
            $query->where('internship_id', $request->internship_id);
        }

        $assessments = $query->paginate($request->get('per_page', 15));

        return response()->json($assessments);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'student_id' => 'required|exists:users,id',
            'internship_id' => 'required|exists:internships,id',
            'final_score' => 'required|integer|min:0|max:100',
            'comments' => 'nullable|string',
            'grade' => 'required|in:A,B,C,D,E,F',
        ]);

        $data['school_admin_id'] = auth('api')->id();

        $assessment = FinalAssessment::create($data);

        return response()->json($assessment, 201);
    }

    public function show(int $id): JsonResponse
    {
        $assessment = FinalAssessment::with(['student', 'internship', 'schoolAdmin'])->findOrFail($id);

        return response()->json($assessment);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $assessment = FinalAssessment::findOrFail($id);

        $data = $request->validate([
            'final_score' => 'sometimes|required|integer|min:0|max:100',
            'comments' => 'nullable|string',
            'grade' => 'sometimes|required|in:A,B,C,D,E,F',
        ]);

        $assessment->update($data);

        return response()->json($assessment);
    }

    public function destroy(int $id): JsonResponse
    {
        $assessment = FinalAssessment::findOrFail($id);
        $assessment->delete();

        return response()->json(['message' => 'Final assessment deleted successfully']);
    }
}
