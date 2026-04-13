<?php

namespace Tests\Feature\Api\V1;

use App\Models\AssessmentTemplate;
use App\Models\Company;
use App\Models\Internship;
use App\Models\SchoolClass;
use App\Models\StudentAssessment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class StudentAssessmentTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $teacher;
    protected User $student;
    protected SchoolClass $class;
    protected Internship $internship;
    protected AssessmentTemplate $template;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'school_admin']);
        $this->teacher = User::factory()->create(['role' => 'teacher']);
        $this->student = User::factory()->create(['role' => 'student']);
        $this->class = SchoolClass::factory()->create();

        // Assign student to class
        DB::table('class_user')->insert([
            'class_id' => $this->class->id,
            'user_id' => $this->student->id,
            'role' => 'student',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create internship
        $company = Company::factory()->create();
        $this->internship = Internship::factory()->create([
            'student_id' => $this->student->id,
            'company_id' => $company->id,
            'status' => 'active',
        ]);

        // Create template via admin
        $response = $this->actingAs($this->admin, 'api')
            ->postJson('/api/v1/assessment-templates', [
                'name' => 'Template Test',
                'class_id' => $this->class->id,
                'sections' => [
                    [
                        'number' => 1,
                        'title' => 'Soft Skills',
                        'indicators' => [
                            ['number' => '1.1', 'description' => 'Komunikasi'],
                            ['number' => '1.2', 'description' => 'Integritas'],
                        ],
                    ],
                ],
            ]);

        $this->template = AssessmentTemplate::find($response->json('id'));
    }

    private function scoresPayload(): array
    {
        return [
            ['section_number' => '1', 'indicator_number' => '1.1', 'indicator_description' => 'Komunikasi', 'score' => 85],
            ['section_number' => '1', 'indicator_number' => '1.2', 'indicator_description' => 'Integritas', 'score' => 90],
        ];
    }

    public function test_teacher_can_get_or_init_assessment(): void
    {
        $response = $this->actingAs($this->teacher, 'api')
            ->getJson("/api/v1/teacher/panel/students/{$this->student->id}/assessment");

        $response->assertStatus(200);
        $response->assertJsonPath('assessment', null);
        $response->assertJsonStructure(['template', 'student_id', 'internship_id']);
    }

    public function test_teacher_can_save_draft(): void
    {
        $response = $this->actingAs($this->teacher, 'api')
            ->postJson("/api/v1/teacher/panel/students/{$this->student->id}/assessment", [
                'internship_id' => $this->internship->id,
                'scores' => $this->scoresPayload(),
                'status' => 'draft',
            ]);

        $response->assertStatus(201);
        $response->assertJsonPath('assessment.status', 'draft');
        $response->assertJsonPath('overall_average', 87.5);
        $this->assertDatabaseCount('student_assessments', 1);
        $this->assertDatabaseCount('assessment_scores', 2);
    }

    public function test_teacher_can_submit_assessment(): void
    {
        $response = $this->actingAs($this->teacher, 'api')
            ->postJson("/api/v1/teacher/panel/students/{$this->student->id}/assessment", [
                'internship_id' => $this->internship->id,
                'scores' => $this->scoresPayload(),
                'status' => 'submitted',
                'teacher_notes' => 'Siswa sangat baik',
            ]);

        $response->assertStatus(201);
        $response->assertJsonPath('assessment.status', 'submitted');
        $response->assertJsonPath('assessment.teacher_notes', 'Siswa sangat baik');
    }

    public function test_duplicate_assessment_returns_409(): void
    {
        $this->actingAs($this->teacher, 'api')
            ->postJson("/api/v1/teacher/panel/students/{$this->student->id}/assessment", [
                'internship_id' => $this->internship->id,
                'scores' => $this->scoresPayload(),
                'status' => 'draft',
            ]);

        $response = $this->actingAs($this->teacher, 'api')
            ->postJson("/api/v1/teacher/panel/students/{$this->student->id}/assessment", [
                'internship_id' => $this->internship->id,
                'scores' => $this->scoresPayload(),
                'status' => 'draft',
            ]);

        $response->assertStatus(409);
    }

    public function test_teacher_can_update_assessment(): void
    {
        $createResponse = $this->actingAs($this->teacher, 'api')
            ->postJson("/api/v1/teacher/panel/students/{$this->student->id}/assessment", [
                'internship_id' => $this->internship->id,
                'scores' => $this->scoresPayload(),
                'status' => 'draft',
            ]);

        $id = $createResponse->json('assessment.id');

        $response = $this->actingAs($this->teacher, 'api')
            ->putJson("/api/v1/teacher/panel/assessments/{$id}", [
                'scores' => [
                    ['section_number' => '1', 'indicator_number' => '1.1', 'indicator_description' => 'Komunikasi', 'score' => 95],
                    ['section_number' => '1', 'indicator_number' => '1.2', 'indicator_description' => 'Integritas', 'score' => 90],
                ],
                'status' => 'submitted',
            ]);

        $response->assertStatus(200);
        $response->assertJsonPath('assessment.status', 'submitted');
        $response->assertJsonPath('overall_average', 92.5);
    }

    public function test_teacher_can_add_custom_indicator(): void
    {
        $scores = $this->scoresPayload();
        $scores[] = [
            'section_number' => '1',
            'indicator_number' => '1.3',
            'indicator_description' => 'Custom: Kerja Tim',
            'score' => 80,
            'is_additional' => true,
        ];

        $response = $this->actingAs($this->teacher, 'api')
            ->postJson("/api/v1/teacher/panel/students/{$this->student->id}/assessment", [
                'internship_id' => $this->internship->id,
                'scores' => $scores,
                'status' => 'draft',
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseCount('assessment_scores', 3);
        $this->assertDatabaseHas('assessment_scores', ['is_additional' => true, 'indicator_number' => '1.3']);
    }

    public function test_score_validation_rejects_out_of_range(): void
    {
        $scores = [
            ['section_number' => '1', 'indicator_number' => '1.1', 'indicator_description' => 'Komunikasi', 'score' => 50],
        ];

        $response = $this->actingAs($this->teacher, 'api')
            ->postJson("/api/v1/teacher/panel/students/{$this->student->id}/assessment", [
                'internship_id' => $this->internship->id,
                'scores' => $scores,
                'status' => 'draft',
            ]);

        $response->assertStatus(422);
    }
}
