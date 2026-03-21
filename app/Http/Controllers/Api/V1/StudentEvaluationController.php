<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\ClockInOut;
use App\Models\StudentDocument;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentEvaluationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = auth('api')->user();
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        // Clock in days = present
        $clockDays = ClockInOut::where('user_id', $user->id)
            ->where('type', 'clock_in')
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->get(['created_at'])
            ->map(fn($c) => $c->created_at->format('Y-m-d'))
            ->unique()
            ->values();

        // Attendance records (sick, permission, absent, present from teacher)
        $attendanceRecords = Attendance::where('student_id', $user->id)
            ->whereMonth('attendance_date', $month)
            ->whereYear('attendance_date', $year)
            ->get(['attendance_date', 'status', 'notes'])
            ->keyBy(fn($a) => \Carbon\Carbon::parse($a->attendance_date)->format('Y-m-d'));

        // Merge: attendance table takes priority, clock_in fills the rest as present
        $merged = [];

        foreach ($clockDays as $date) {
            if (!isset($attendanceRecords[$date])) {
                $merged[$date] = ['attendance_date' => $date, 'status' => 'present', 'notes' => null];
            }
        }

        foreach ($attendanceRecords as $date => $record) {
            $merged[$date] = [
                'attendance_date' => $date,
                'status' => $record->status,
                'notes' => $record->notes,
            ];
        }

        ksort($merged);

        $documents = StudentDocument::where('student_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get(['id', 'title', 'file_type', 'status', 'teacher_note', 'parent_id', 'approved_at', 'created_at']);

        return response()->json([
            'month' => (int) $month,
            'year' => (int) $year,
            'attendance' => array_values($merged),
            'documents' => $documents,
        ]);
    }

    public function myAttendance(Request $request): JsonResponse
    {
        $user = auth('api')->user();

        // All clock records
        $clocks = ClockInOut::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get(['created_at', 'type', 'is_within_range', 'distance_meters', 'photo']);

        // All attendance records (sick, permission, absent, present)
        $attendanceRecords = Attendance::where('student_id', $user->id)
            ->orderBy('attendance_date', 'desc')
            ->get(['attendance_date', 'status', 'notes']);

        return response()->json([
            'clock_records' => $clocks,
            'attendance_records' => $attendanceRecords,
        ]);
    }
}
