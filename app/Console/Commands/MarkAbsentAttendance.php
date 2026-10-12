<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use App\Models\ClockInOut;
use App\Models\Internship;
use App\Models\PermissionRequest;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MarkAbsentAttendance extends Command
{
    protected $signature = 'attendance:mark-absent';

    protected $description = 'Mark active internship students absent when they have not clocked in';

    public function handle(): int
    {
        $configuredTime = env('ATTENDANCE_CHECK_TIME', '08:00');
        $configuredHour = str_pad((string) ((int) explode(':', $configuredTime)[0]), 2, '0', STR_PAD_LEFT);
        $today = now()->toDateString();

        if (now()->format('H') !== $configuredHour) {
            $this->line('Skipped, time not reached.');

            return self::SUCCESS;
        }

        $marked = 0;
        $skipped = 0;

        DB::transaction(function () use ($today, $configuredTime, &$marked, &$skipped): void {
            Internship::query()
                ->where('status', 'active')
                ->whereDate('start_date', '<=', $today)
                ->whereDate('end_date', '>=', $today)
                ->with('student')
                ->each(function (Internship $internship) use ($today, $configuredTime, &$marked, &$skipped): void {
                    $studentId = $internship->student_id;

                    if (Attendance::where('student_id', $studentId)->whereDate('attendance_date', $today)->exists()) {
                        $skipped++;
                        return;
                    }

                    $hasPermission = PermissionRequest::where('student_id', $studentId)
                        ->where('status', 'approved')
                        ->whereDate('request_date', '<=', $today)
                        ->where(function ($query) use ($today): void {
                            $query->whereDate('end_date', '>=', $today)->orWhereNull('end_date');
                        })
                        ->exists();

                    $hasClockedIn = ClockInOut::where('user_id', $studentId)
                        ->where('type', 'clock_in')
                        ->whereDate('created_at', $today)
                        ->exists();

                    if ($hasPermission || $hasClockedIn) {
                        $skipped++;
                        return;
                    }

                    $attendance = Attendance::firstOrCreate(
                        ['student_id' => $studentId, 'attendance_date' => $today],
                        [
                            'internship_id' => $internship->id,
                            'status' => 'absent',
                            'notes' => "Otomatis: belum clock in sebelum {$configuredTime}",
                            'recorded_by' => null,
                        ]
                    );

                    $attendance->wasRecentlyCreated ? $marked++ : $skipped++;
                });
        });

        $this->line("Marked absent: {$marked}; skipped: {$skipped}.");

        return self::SUCCESS;
    }
}
