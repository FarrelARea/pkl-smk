<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\ClockInOut;
use App\Models\PermissionRequest;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

class AttendanceInferenceService
{
    public function inferredPresentDates(int $studentId, ?int $internshipId = null, CarbonInterface|string|null $start = null, CarbonInterface|string|null $end = null): Collection
    {
        $query = ClockInOut::query()
            ->where('user_id', $studentId)
            ->where('type', 'clock_in');

        if ($internshipId) {
            $query->where('internship_id', $internshipId);
        }

        if ($start) {
            $query->whereDate('created_at', '>=', $this->normalizeDate($start)->toDateString());
        }

        if ($end) {
            $query->whereDate('created_at', '<=', $this->normalizeDate($end)->toDateString());
        }

        return $query->orderBy('created_at')
            ->get(['created_at'])
            ->map(fn ($clock) => $clock->created_at->format('Y-m-d'))
            ->unique()
            ->values();
    }

    public function mergedAttendanceByDate(int $studentId, ?int $internshipId = null, CarbonInterface|string|null $start = null, CarbonInterface|string|null $end = null): Collection
    {
        $attendanceQuery = Attendance::query()
            ->where('student_id', $studentId);

        if ($internshipId) {
            $attendanceQuery->where('internship_id', $internshipId);
        }

        if ($start) {
            $attendanceQuery->whereDate('attendance_date', '>=', $this->normalizeDate($start)->toDateString());
        }

        if ($end) {
            $attendanceQuery->whereDate('attendance_date', '<=', $this->normalizeDate($end)->toDateString());
        }

        $attendanceRecords = $attendanceQuery
            ->get(['attendance_date', 'status', 'notes'])
            ->keyBy(fn ($attendance) => Carbon::parse($attendance->attendance_date)->format('Y-m-d'));

        $permissionQuery = PermissionRequest::query()
            ->where('student_id', $studentId)
            ->where('status', 'approved')
            ->whereIn('type', ['sick', 'permit']);

        if ($internshipId) {
            $permissionQuery->where('internship_id', $internshipId);
        }

        if ($start || $end) {
            $rangeStart = $this->normalizeDate($start ?? $end)->toDateString();
            $rangeEnd = $this->normalizeDate($end ?? $start)->toDateString();

            $permissionQuery->whereDate('request_date', '<=', $rangeEnd)
                ->where(function ($query) use ($rangeStart) {
                    $query->whereDate('end_date', '>=', $rangeStart)
                        ->orWhereNull('end_date');
                });
        }

        $permissionRecords = [];
        foreach ($permissionQuery->get(['request_date', 'end_date', 'type', 'reason']) as $permission) {
            $rangeStart = $this->normalizeDate($permission->request_date);
            $rangeEnd = $this->normalizeDate($permission->end_date ?? $permission->request_date);

            if ($start && $rangeStart->lt($this->normalizeDate($start))) {
                $rangeStart = $this->normalizeDate($start);
            }

            if ($end && $rangeEnd->gt($this->normalizeDate($end))) {
                $rangeEnd = $this->normalizeDate($end);
            }

            for ($date = $rangeStart->copy(); $date->lte($rangeEnd); $date->addDay()) {
                $dateKey = $date->format('Y-m-d');
                $permissionRecords[$dateKey] = [
                    'attendance_date' => $dateKey,
                    'status' => $permission->type === 'permit' ? 'permission' : 'sick',
                    'notes' => $permission->reason,
                ];
            }
        }

        $merged = [];

        foreach ($this->inferredPresentDates($studentId, $internshipId, $start, $end) as $date) {
            if (!isset($attendanceRecords[$date]) && !isset($permissionRecords[$date])) {
                $merged[$date] = [
                    'attendance_date' => $date,
                    'status' => 'present',
                    'notes' => null,
                ];
            }
        }

        foreach ($permissionRecords as $date => $permissionRecord) {
            if (!isset($attendanceRecords[$date])) {
                $merged[$date] = $permissionRecord;
            }
        }

        foreach ($attendanceRecords as $date => $attendance) {
            $merged[$date] = [
                'attendance_date' => $date,
                'status' => $attendance->status,
                'notes' => $attendance->notes,
            ];
        }

        ksort($merged);

        return collect($merged)->values();
    }

    public function totalPresentDays(int $studentId, ?int $internshipId = null, CarbonInterface|string|null $start = null, CarbonInterface|string|null $end = null): int
    {
        return $this->mergedAttendanceByDate($studentId, $internshipId, $start, $end)
            ->where('status', 'present')
            ->count();
    }

    public function attendanceForStudentsOnDate(Collection|array $studentIds, CarbonInterface|string $date): Collection
    {
        $studentIds = collect($studentIds)->map(fn ($id) => (int) $id)->unique()->values();
        $date = $this->normalizeDate($date)->toDateString();

        if ($studentIds->isEmpty()) {
            return collect();
        }

        $attendanceRecords = Attendance::query()
            ->whereIn('student_id', $studentIds)
            ->whereDate('attendance_date', $date)
            ->get(['student_id', 'attendance_date', 'status', 'notes'])
            ->keyBy('student_id');

        $clockedStudentIds = ClockInOut::query()
            ->whereIn('user_id', $studentIds)
            ->where('type', 'clock_in')
            ->whereDate('created_at', $date)
            ->pluck('user_id')
            ->map(fn ($id) => (int) $id)
            ->unique();

        $rows = [];

        foreach ($clockedStudentIds as $studentId) {
            if (!$attendanceRecords->has($studentId)) {
                $rows[$studentId] = [
                    'student_id' => $studentId,
                    'attendance_date' => $date,
                    'status' => 'present',
                    'notes' => null,
                ];
            }
        }

        foreach ($attendanceRecords as $studentId => $attendance) {
            $rows[(int) $studentId] = [
                'student_id' => (int) $studentId,
                'attendance_date' => Carbon::parse($attendance->attendance_date)->format('Y-m-d'),
                'status' => $attendance->status,
                'notes' => $attendance->notes,
            ];
        }

        ksort($rows);

        return collect($rows)->values();
    }

    private function normalizeDate(CarbonInterface|string $date): CarbonInterface
    {
        return $date instanceof CarbonInterface ? $date : Carbon::parse($date);
    }
}
