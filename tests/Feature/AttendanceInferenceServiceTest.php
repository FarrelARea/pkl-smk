<?php

namespace Tests\Feature;

use App\Models\ClockInOut;
use App\Models\Company;
use App\Models\Internship;
use App\Models\PermissionRequest;
use App\Models\User;
use App\Services\AttendanceInferenceService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceInferenceServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_approved_holiday_is_applied_only_to_the_requesting_student(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-10-12 08:00:00'));

        $company = Company::factory()->create();
        $holidayStudent = User::factory()->create(['role' => 'student']);
        $presentStudent = User::factory()->create(['role' => 'student']);
        $holidayInternship = $this->activeInternship($holidayStudent, $company);
        $presentInternship = $this->activeInternship($presentStudent, $company);

        PermissionRequest::create([
            'student_id' => $holidayStudent->id,
            'internship_id' => $holidayInternship->id,
            'request_date' => '2026-10-12',
            'type' => 'holiday',
            'status' => 'approved',
            'reason' => 'Libur tidak terjadwal',
        ]);

        ClockInOut::create([
            'user_id' => $presentStudent->id,
            'internship_id' => $presentInternship->id,
            'type' => 'clock_in',
            'latitude' => 0,
            'longitude' => 0,
        ]);

        $records = app(AttendanceInferenceService::class)
            ->attendanceForStudentsOnDate([$holidayStudent->id, $presentStudent->id], '2026-10-12');

        $this->assertSame('holiday', $records->firstWhere('student_id', $holidayStudent->id)['status']);
        $this->assertSame('present', $records->firstWhere('student_id', $presentStudent->id)['status']);
    }

    private function activeInternship(User $student, Company $company): Internship
    {
        return Internship::create([
            'student_id' => $student->id,
            'company_id' => $company->id,
            'start_date' => '2026-10-01',
            'end_date' => '2026-10-31',
            'status' => 'active',
        ]);
    }
}
