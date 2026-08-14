<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Evaluation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EvaluationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Evaluation::with(['student', 'internship', 'evaluator']);

        if ($request->has('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->has('evaluator_id')) {
            $query->where('evaluator_id', $request->evaluator_id);
        }

        if ($request->has('internship_id')) {
            $query->where('internship_id', $request->internship_id);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        $evaluations = $query->paginate($request->get('per_page', 15));

        return response()->json($evaluations);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'student_id' => 'required|exists:users,id',
            'internship_id' => 'required|exists:internships,id',
            'score' => 'required|integer|min:0|max:100',
            'comments' => 'nullable|string',
            'type' => 'required|in:teacher,supervisor',
        ]);

        $data['evaluator_id'] = auth('api')->id();

        $evaluation = Evaluation::create($data);

        return response()->json($evaluation, 201);
    }

    public function show(int $id): JsonResponse
    {
        $evaluation = Evaluation::with(['student', 'internship', 'evaluator'])->findOrFail($id);

        return response()->json($evaluation);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $evaluation = Evaluation::findOrFail($id);

        $data = $request->validate([
            'score' => 'sometimes|required|integer|min:0|max:100',
            'comments' => 'nullable|string',
        ]);

        $evaluation->update($data);

        return response()->json($evaluation);
    }

    public function destroy(int $id): JsonResponse
    {
        $evaluation = Evaluation::findOrFail($id);
        $evaluation->delete();

        return response()->json(['message' => 'Evaluation deleted successfully']);
    }

    public function summary(int $studentId): JsonResponse
    {
        $evaluations = Evaluation::where('student_id', $studentId)->get();

        $teacherEvaluations = $evaluations->where('type', 'teacher');
        $supervisorEvaluations = $evaluations->where('type', 'supervisor');

        return response()->json([
            'student_id' => $studentId,
            'total_evaluations' => $evaluations->count(),
            'teacher_evaluations' => [
                'count' => $teacherEvaluations->count(),
                'average_score' => $teacherEvaluations->avg('score'),
            ],
            'supervisor_evaluations' => [
                'count' => $supervisorEvaluations->count(),
                'average_score' => $supervisorEvaluations->avg('score'),
            ],
            'overall_average' => $evaluations->avg('score'),
            'evaluations' => $evaluations,
        ]);
    }
}
