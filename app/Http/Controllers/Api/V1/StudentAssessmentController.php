<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\AssessmentTemplate;
use App\Models\StudentAssessment;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentAssessmentController extends Controller
{
    public function getOrInit(int $studentId): JsonResponse
    {
        $student = User::findOrFail($studentId);

        // Find active internship for the student
        $internship = $student->internships()->where('status', 'active')->first();

        if (!$internship) {
            return response()->json(['message' => 'Student has no active internship'], 404);
        }

        // Check if assessment already exists
        $assessment = StudentAssessment::with('scores')
            ->where('student_id', $studentId)
            ->where('internship_id', $internship->id)
            ->first();

        if ($assessment) {
            return response()->json($this->formatAssessmentResponse($assessment));
        }

        // Get active template for student's class
        $classId = $student->studentClasses()->first()?->id;

        if (!$classId) {
            return response()->json(['message' => 'Student is not assigned to a class'], 404);
        }

        $template = AssessmentTemplate::with('sections.indicators.children')
            ->where('class_id', $classId)
            ->active()
            ->first();

        if (!$template) {
            return response()->json(['message' => 'No active assessment template for this class'], 404);
        }

        return response()->json([
            'assessment' => null,
            'template' => $template,
            'student_id' => $studentId,
            'internship_id' => $internship->id,
        ]);
    }

    public function store(Request $request, int $studentId): JsonResponse
    {
        $data = $request->validate([
            'internship_id' => 'required|exists:internships,id',
            'scores' => 'required|array|min:1',
            'scores.*.section_number' => 'required|string',
            'scores.*.indicator_number' => 'required|string',
            'scores.*.indicator_description' => 'required|string',
            'scores.*.score' => 'required|integer|min:75|max:95',
            'scores.*.notes' => 'nullable|string',
            'scores.*.is_additional' => 'sometimes|boolean',
            'teacher_notes' => 'nullable|string',
            'teacher_checklist' => 'nullable|array',
            'status' => 'required|in:draft,submitted',
        ]);

        // Check for existing assessment
        $existing = StudentAssessment::where('student_id', $studentId)
            ->where('internship_id', $data['internship_id'])
            ->exists();

        if ($existing) {
            return response()->json(['message' => 'Assessment already exists for this student and internship'], 409);
        }

        // Get and snapshot template
        $student = User::findOrFail($studentId);
        $classId = $student->studentClasses()->first()?->id;
        $template = AssessmentTemplate::with('sections.indicators.children')
            ->where('class_id', $classId)
            ->active()
            ->first();

        if (!$template) {
            return response()->json(['message' => 'No active assessment template for this class'], 404);
        }

        $snapshot = $this->buildSnapshot($template);

        $assessment = DB::transaction(function () use ($data, $studentId, $snapshot) {
            $assessment = StudentAssessment::create([
                'student_id' => $studentId,
                'internship_id' => $data['internship_id'],
                'teacher_id' => auth('api')->id(),
                'template_snapshot' => $snapshot,
                'teacher_notes' => $data['teacher_notes'] ?? null,
                'teacher_checklist' => $data['teacher_checklist'] ?? null,
                'status' => $data['status'],
            ]);

            foreach ($data['scores'] as $scoreData) {
                $assessment->scores()->create([
                    'section_number' => $scoreData['section_number'],
                    'indicator_number' => $scoreData['indicator_number'],
                    'indicator_description' => $scoreData['indicator_description'],
                    'score' => $scoreData['score'],
                    'notes' => $scoreData['notes'] ?? null,
                    'is_additional' => $scoreData['is_additional'] ?? false,
                ]);
            }

            return $assessment;
        });

        return response()->json(
            $this->formatAssessmentResponse($assessment->load('scores')),
            201
        );
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $assessment = StudentAssessment::findOrFail($id);

        $data = $request->validate([
            'scores' => 'sometimes|array',
            'scores.*.section_number' => 'required_with:scores|string',
            'scores.*.indicator_number' => 'required_with:scores|string',
            'scores.*.indicator_description' => 'required_with:scores|string',
            'scores.*.score' => 'required_with:scores|integer|min:75|max:95',
            'scores.*.notes' => 'nullable|string',
            'scores.*.is_additional' => 'sometimes|boolean',
            'teacher_notes' => 'nullable|string',
            'teacher_checklist' => 'nullable|array',
            'status' => 'sometimes|in:draft,submitted',
        ]);

        DB::transaction(function () use ($assessment, $data) {
            $assessment->update(collect($data)->only(['teacher_notes', 'teacher_checklist', 'status'])->toArray());

            if (isset($data['scores'])) {
                // Delete existing and re-create (upsert)
                $assessment->scores()->delete();

                foreach ($data['scores'] as $scoreData) {
                    $assessment->scores()->create([
                        'section_number' => $scoreData['section_number'],
                        'indicator_number' => $scoreData['indicator_number'],
                        'indicator_description' => $scoreData['indicator_description'],
                        'score' => $scoreData['score'],
                        'notes' => $scoreData['notes'] ?? null,
                        'is_additional' => $scoreData['is_additional'] ?? false,
                    ]);
                }
            }
        });

        return response()->json(
            $this->formatAssessmentResponse($assessment->fresh()->load('scores'))
        );
    }

    private function buildSnapshot(AssessmentTemplate $template): array
    {
        return [
            'template_id' => $template->id,
            'template_name' => $template->name,
            'sections' => $template->sections->map(function ($section) {
                return [
                    'number' => $section->number,
                    'title' => $section->title,
                    'indicators' => $section->indicators->map(function ($indicator) {
                        return [
                            'number' => $indicator->number,
                            'description' => $indicator->description,
                            'children' => $indicator->children->map(function ($child) {
                                return [
                                    'number' => $child->number,
                                    'description' => $child->description,
                                ];
                            })->toArray(),
                        ];
                    })->toArray(),
                ];
            })->toArray(),
        ];
    }

    private function formatAssessmentResponse(StudentAssessment $assessment): array
    {
        $scoresBySection = $assessment->scores->groupBy('section_number');

        $sectionAverages = $scoresBySection->map(function ($scores, $sectionNumber) {
            return [
                'section_number' => $sectionNumber,
                'average' => round($scores->avg('score'), 2),
                'count' => $scores->count(),
                'scores' => $scores,
            ];
        })->values();

        return [
            'assessment' => $assessment->only([
                'id', 'student_id', 'internship_id', 'teacher_id',
                'template_snapshot', 'teacher_notes', 'teacher_checklist',
                'status', 'created_at', 'updated_at',
            ]),
            'sections' => $sectionAverages,
            'overall_average' => $assessment->scores->count() > 0
                ? round($assessment->scores->avg('score'), 2)
                : null,
        ];
    }
}
