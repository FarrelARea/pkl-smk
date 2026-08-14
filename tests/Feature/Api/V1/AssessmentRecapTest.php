<?php

namespace Tests\Feature\Api\V1;

use App\Models\AssessmentScore;
use App\Models\AssessmentTemplate;
use App\Models\Company;
use App\Models\Internship;
use App\Models\SchoolClass;
use App\Models\StudentAssessment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AssessmentRecapTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $teacher;
    protected SchoolClass $class;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'school_admin']);
        $this->teacher = User::factory()->create(['role' => 'teacher']);
        $this->class = SchoolClass::factory()->create();
    }

    private function createStudentWithAssessment(string $status = 'submitted'): array
    {
        $student = User::factory()->create(['role' => 'student']);
        DB::table('class_user')->insert([
            'class_id' => $this->class->id,
            'user_id' => $student->id,
            'role' => 'student',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $company = Company::factory()->create();
        $internship = Internship::factory()->create([
            'student_id' => $student->id,
            'company_id' => $company->id,
            'status' => 'active',
        ]);

        $assessment = StudentAssessment::create([
            'student_id' => $student->id,
            'internship_id' => $internship->id,
            'teacher_id' => $this->teacher->id,
            'template_snapshot' => ['sections' => []],
            'status' => $status,
        ]);

        AssessmentScore::create([
            'student_assessment_id' => $assessment->id,
            'section_number' => '1',
            'indicator_number' => '1.1',
            'indicator_description' => 'Komunikasi',
            'score' => 85,
        ]);

        return ['student' => $student, 'internship' => $internship, 'assessment' => $assessment];
    }

    public function test_admin_can_view_recap_per_class(): void
    {
        $this->createStudentWithAssessment();
        $this->createStudentWithAssessment('draft');

        $response = $this->actingAs($this->admin, 'api')
            ->getJson("/api/v1/assessment-recap?class_id={$this->class->id}");

        $response->assertStatus(200);
        $response->assertJsonCount(2, 'recap');
    }

    public function test_recap_shows_not_started_for_students_without_assessment(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        DB::table('class_user')->insert([
            'class_id' => $this->class->id,
            'user_id' => $student->id,
            'role' => 'student',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($this->admin, 'api')
            ->getJson("/api/v1/assessment-recap?class_id={$this->class->id}");

        $response->assertStatus(200);
        $response->assertJsonPath('recap.0.status', 'not_started');
    }

    public function test_admin_can_view_assessment_detail(): void
    {
        $data = $this->createStudentWithAssessment();

        $response = $this->actingAs($this->admin, 'api')
            ->getJson("/api/v1/student-assessments/{$data['assessment']->id}");

        $response->assertStatus(200);
        $response->assertJsonStructure(['assessment', 'sections', 'overall_average']);
        $response->assertJsonPath('overall_average', 85.0);
    }

    public function test_admin_can_list_assessments_with_filters(): void
    {
        $this->createStudentWithAssessment('submitted');
        $this->createStudentWithAssessment('draft');

        $response = $this->actingAs($this->admin, 'api')
            ->getJson("/api/v1/student-assessments?class_id={$this->class->id}&status=submitted");

        $response->assertStatus(200);
        $response->assertJsonPath('total', 1);
    }

    public function test_recap_requires_class_id(): void
    {
        $response = $this->actingAs($this->admin, 'api')
            ->getJson('/api/v1/assessment-recap');

        $response->assertStatus(422);
    }
}
