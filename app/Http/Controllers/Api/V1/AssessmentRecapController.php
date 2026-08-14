<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\StudentAssessment;
use App\Models\StudentDocument;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AssessmentRecapController extends Controller
{
    public function recap(Request $request): JsonResponse
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'internship_id' => 'sometimes|exists:internships,id',
        ]);

        $classId = $request->class_id;

        // Get all students in this class
        $students = User::where('role', 'student')
            ->whereHas('classes', function ($query) use ($classId) {
                $query->where('classes.id', $classId);
            })
            ->get();

        $assessmentQuery = StudentAssessment::with('scores')
            ->whereIn('student_id', $students->pluck('id'));

        if ($request->has('internship_id')) {
            $assessmentQuery->where('internship_id', $request->internship_id);
        }

        $assessments = $assessmentQuery->get()->keyBy('student_id');

        $recap = $students->map(function ($student) use ($assessments) {
            $assessment = $assessments->get($student->id);

            if (!$assessment) {
                return [
                    'student_id' => $student->id,
                    'student_name' => $student->name,
                    'status' => 'not_started',
                    'section_averages' => [],
                    'overall_average' => null,
                ];
            }

            $sectionAverages = $assessment->scores->groupBy('section_number')->map(function ($scores, $section) {
                return [
                    'section_number' => $section,
                    'average' => round($scores->avg('score'), 2),
                ];
            })->values();

            return [
                'student_id' => $student->id,
                'student_name' => $student->name,
                'status' => $assessment->status,
                'assessment_id' => $assessment->id,
                'section_averages' => $sectionAverages,
                'overall_average' => $assessment->scores->count() > 0
                    ? round($assessment->scores->avg('score'), 2)
                    : null,
            ];
        });

        return response()->json([
            'class' => SchoolClass::find($classId),
            'recap' => $recap,
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $assessment = StudentAssessment::with(['student', 'internship.company', 'teacher', 'scores'])
            ->findOrFail($id);

        $sectionAverages = $assessment->scores->groupBy('section_number')->map(function ($scores, $section) {
            return [
                'section_number' => $section,
                'average' => round($scores->avg('score'), 2),
                'count' => $scores->count(),
                'scores' => $scores,
            ];
        })->values();

        $documents = StudentDocument::where('student_id', $assessment->student_id)
            ->where('internship_id', $assessment->internship_id)
            ->whereNull('parent_id')
            ->with('approvedBy')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'assessment' => $assessment,
            'sections' => $sectionAverages,
            'overall_average' => $assessment->scores->count() > 0
                ? round($assessment->scores->avg('score'), 2)
                : null,
            'documents' => $documents,
        ]);
    }

    public function index(Request $request): JsonResponse
    {
        $query = StudentAssessment::with(['student.classes', 'teacher']);

        if ($request->has('class_id')) {
            $classId = $request->class_id;
            $query->whereHas('student', function ($q) use ($classId) {
                $q->whereHas('classes', function ($q2) use ($classId) {
                    $q2->where('classes.id', $classId);
                });
            });
        }

        if ($request->has('internship_id')) {
            $query->where('internship_id', $request->internship_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('academic_year')) {
            $academicYear = $request->academic_year;
            $query->whereHas('student', function ($q) use ($academicYear) {
                $q->whereHas('classes', function ($q2) use ($academicYear) {
                    $q2->where('academic_year', $academicYear);
                });
            });
        }

        $assessments = $query->paginate($request->get('per_page', 15));

        return response()->json($assessments);
    }
}
