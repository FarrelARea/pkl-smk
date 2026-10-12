<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\ClockInOut;
use App\Models\Company;
use App\Models\Internship;
use App\Models\PermissionRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarkAbsentAttendanceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::parse('2026-10-12 08:00:00'));
        putenv('ATTENDANCE_CHECK_TIME=08:00');
        $_ENV['ATTENDANCE_CHECK_TIME'] = '08:00';
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        putenv('ATTENDANCE_CHECK_TIME');
        unset($_ENV['ATTENDANCE_CHECK_TIME']);
        parent::tearDown();
    }

    public function test_marks_active_student_absent_without_clock_in(): void
    {
        [$student, $internship] = $this->activeInternship();

        $this->artisan('attendance:mark-absent')->assertExitCode(0);

        $this->assertDatabaseHas('attendance', [
            'student_id' => $student->id,
            'internship_id' => $internship->id,
            'attendance_date' => '2026-10-12 00:00:00',
            'status' => 'absent',
            'notes' => 'Otomatis: belum clock in sebelum 08:00',
        ]);
    }

    public function test_does_not_mark_student_with_clock_in(): void
    {
        [$student, $internship] = $this->activeInternship();
        ClockInOut::create([
            'user_id' => $student->id,
            'internship_id' => $internship->id,
            'type' => 'clock_in',
            'latitude' => 1,
            'longitude' => 1,
        ]);

        $this->artisan('attendance:mark-absent');

        $this->assertDatabaseCount('attendance', 0);
    }

    public function test_does_not_mark_student_with_approved_permission(): void
    {
        [$student, $internship] = $this->activeInternship();
        PermissionRequest::create([
            'student_id' => $student->id,
            'internship_id' => $internship->id,
            'request_date' => '2026-10-12',
            'type' => 'permit',
            'status' => 'approved',
            'reason' => 'Medical leave',
        ]);

        $this->artisan('attendance:mark-absent');

        $this->assertDatabaseCount('attendance', 0);
    }

    public function test_does_not_mark_when_hour_does_not_match(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-10-12 09:00:00'));
        [$student] = $this->activeInternship();

        $this->artisan('attendance:mark-absent')->assertExitCode(0);

        $this->assertDatabaseCount('attendance', 0);
        $this->assertDatabaseMissing('attendance', ['student_id' => $student->id]);
    }

    public function test_running_command_twice_is_idempotent(): void
    {
        $this->activeInternship();

        $this->artisan('attendance:mark-absent');
        $this->artisan('attendance:mark-absent');

        $this->assertDatabaseCount('attendance', 1);
    }

    private function activeInternship(): array
    {
        $student = User::factory()->create(['role' => 'student']);
        $internship = Internship::create([
            'student_id' => $student->id,
            'company_id' => Company::factory()->create()->id,
            'start_date' => '2026-10-01',
            'end_date' => '2026-10-31',
            'status' => 'active',
        ]);

        return [$student, $internship];
    }
}
