<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ClockInOut;
use App\Models\Attendance;
use App\Models\User;
use App\Services\AttendanceInferenceService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminPanelController extends Controller
{
    public function __construct(private AttendanceInferenceService $attendanceInferenceService)
    {
    }

    public function attendanceCalendar(Request $request): JsonResponse
    {
        $admin = auth('api')->user();
        $date = $request->query('date', Carbon::today()->toDateString());
        $parsedDate = Carbon::parse($date)->toDateString();

        $studentQuery = User::where('role', 'student');

        if (!$admin->isSuperAdmin()) {
            $studentQuery->where('school_id', $admin->school_id);
        }

        $studentIds = $studentQuery->pluck('id');
        $studentNames = User::whereIn('id', $studentIds)->pluck('name', 'id');

        $records = $this->attendanceInferenceService
            ->attendanceForStudentsOnDate($studentIds, $parsedDate)
            ->map(fn ($record) => [
                'student_id' => $record['student_id'],
                'student_name' => $studentNames[$record['student_id']] ?? null,
                'attendance_date' => $record['attendance_date'],
                'status' => $record['status'],
                'notes' => $record['notes'],
                'attendance_id' => null,
                'internship_id' => null,
                'clock_in_id' => null,
                'clock_out_id' => null,
                'clock_in' => null,
                'clock_out' => null,
                'location_verified' => null,
                'all_clocks' => [],
            ])
            ->values();

        $clockRecords = ClockInOut::whereIn('user_id', $studentIds)
            ->whereDate('created_at', $parsedDate)
            ->orderBy('created_at')
            ->get();
        $clockByStudent = $clockRecords->groupBy('user_id');
        $attendanceByStudent = Attendance::query()
            ->whereIn('student_id', $studentIds)
            ->whereDate('attendance_date', $parsedDate)
            ->get()
            ->keyBy('student_id');

        $records = $records->map(function ($record) use ($clockByStudent, $attendanceByStudent) {
            $attendance = $attendanceByStudent->get($record['student_id']);
            $record['attendance_id'] = $attendance?->id;
            $record['internship_id'] = $attendance?->internship_id;
            $record['location_verified'] = $attendance?->location_verified;
            if (!$clockByStudent->has($record['student_id'])) {
                return $record;
            }
            $clocks = $clockByStudent[$record['student_id']];
            $internshipId = $record['internship_id'] ?? $clocks->first()?->internship_id;
            $record['internship_id'] = $internshipId;
            if ($internshipId) {
                $internshipClocks = $clocks->where('internship_id', $internshipId);
                if ($internshipClocks->isNotEmpty()) {
                    $clocks = $internshipClocks;
                }
            }
            $clockIn = $clocks->firstWhere('type', 'clock_in');
            $clockOut = $clocks->filter(fn ($c) => $c->type === 'clock_out')->first();

            $record['clock_in'] = $clockIn ? $clockIn->created_at : null;
            $record['clock_out'] = $clockOut ? $clockOut->created_at : null;
            $record['clock_in_id'] = $clockIn?->id;
            $record['clock_out_id'] = $clockOut?->id;
            $record['location_verified'] = $clocks->every(fn ($clock) => (bool) $clock->is_within_range);
            $record['all_clocks'] = $clocks->map(fn ($c) => [
                'id' => $c->id,
                'type' => $c->type,
                'time' => $c->created_at,
                'within_range' => (bool) $c->is_within_range,
                'photo' => $c->photo,
            ])->values();

            return $record;
        });

        return response()->json([
            'date' => $parsedDate,
            'attendance' => $records,
            'present_count' => $records->where('status', 'present')->count(),
            'tracked_count' => $records->count(),
        ]);
    }
}
