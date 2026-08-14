<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ClockInOut;
use App\Models\DailyLog;
use App\Models\DailyLogComment;
use App\Models\StudentDocument;
use App\Services\AttendanceInferenceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentEvaluationController extends Controller
{
    public function __construct(private AttendanceInferenceService $attendanceInferenceService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $user = auth('api')->user();
        $month = (int) $request->get('month', now()->month);
        $year = (int) $request->get('year', now()->year);
        $start = now()->setYear($year)->setMonth($month)->startOfMonth();
        $end = (clone $start)->endOfMonth();

        $documents = StudentDocument::where('student_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get(['id', 'title', 'file_type', 'status', 'teacher_note', 'parent_id', 'approved_at', 'created_at']);

        return response()->json([
            'month' => $month,
            'year' => $year,
            'attendance' => $this->attendanceInferenceService
                ->mergedAttendanceByDate($user->id, null, $start, $end)
                ->values()
                ->all(),
            'documents' => $documents,
        ]);
    }

    public function myAttendance(Request $request): JsonResponse
    {
        $user = auth('api')->user();

        $clocks = ClockInOut::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get(['created_at', 'type', 'is_within_range', 'distance_meters', 'photo']);

        $attendanceRecords = $this->attendanceInferenceService
            ->mergedAttendanceByDate($user->id)
            ->sortByDesc('attendance_date')
            ->values();

        return response()->json([
            'clock_records' => $clocks,
            'attendance_records' => $attendanceRecords,
        ]);
    }

    public function addComment(Request $request, int $logId): JsonResponse
    {
        $student = auth('api')->user();
        $log = DailyLog::findOrFail($logId);

        if ($log->student_id !== $student->id) {
            return response()->json(['error' => 'Anda tidak bisa komentar pada log ini.'], 403);
        }

        $data = $request->validate(['comment' => 'required|string']);

        $comment = DailyLogComment::create([
            'daily_log_id' => $log->id,
            'author_id' => $student->id,
            'author_role' => 'student',
            'comment' => $data['comment'],
        ]);

        $comment->load('author:id,name');

        return response()->json($comment, 201);
    }
}
