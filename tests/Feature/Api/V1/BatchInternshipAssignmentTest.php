<?php

namespace Tests\Feature\Api\V1;

use App\Models\Company;
use App\Models\Internship;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BatchInternshipAssignmentTest extends TestCase
{
    use RefreshDatabase;

    protected User $schoolAdmin;
    protected Company $company;
    protected User $supervisor;
    protected $students;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->schoolAdmin = User::factory()->create(['role' => 'school_admin']);
        $this->company = Company::factory()->create();
        $this->supervisor = User::factory()->create(['role' => 'company_supervisor', 'company_id' => $this->company->id]);
        
        $this->students = User::factory()->count(5)->create(['role' => 'student']);
    }

    public function test_batch_create_internships(): void
    {
        $studentIds = $this->students->take(3)->pluck('id')->toArray();
        
        $response = $this->actingAs($this->schoolAdmin, 'api')
            ->postJson('/api/v1/internships/batch', [
                'student_ids' => $studentIds,
                'company_id' => $this->company->id,
                'supervisor_id' => $this->supervisor->id,
                'start_date' => '2024-01-01',
                'end_date' => '2024-06-01',
            ]);

        $response->assertStatus(201);
        $response->assertJson(['message' => 'Internships created successfully', 'count' => 3]);

        $this->assertDatabaseCount('internships', 3);
    }

    public function test_batch_fails_with_duplicate_students(): void
    {
        $student = $this->students->first();
        Internship::create([
            'student_id' => $student->id,
            'company_id' => $this->company->id,
            'supervisor_id' => $this->supervisor->id,
            'start_date' => '2024-01-01',
            'end_date' => '2024-06-01',
            'status' => 'active',
        ]);

        $studentIds = $this->students->take(3)->pluck('id')->toArray();
        
        $response = $this->actingAs($this->schoolAdmin, 'api')
            ->postJson('/api/v1/internships/batch', [
                'student_ids' => $studentIds,
                'company_id' => $this->company->id,
                'supervisor_id' => $this->supervisor->id,
                'start_date' => '2024-01-01',
                'end_date' => '2024-06-01',
            ]);

        $response->assertStatus(422);
        $response->assertJsonStructure(['error']);
    }

    public function test_batch_fails_over_50_students(): void
    {
        $studentIds = array_map(fn($i) => $i, range(1, 51));
        
        $response = $this->actingAs($this->schoolAdmin, 'api')
            ->postJson('/api/v1/internships/batch', [
                'student_ids' => $studentIds,
                'company_id' => $this->company->id,
                'start_date' => '2024-01-01',
                'end_date' => '2024-06-01',
            ]);

        $response->assertStatus(422);
    }

    public function test_non_admin_cannot_access_batch(): void
    {
        $student = $this->students->first();
        
        $response = $this->actingAs($student, 'api')
            ->postJson('/api/v1/internships/batch', [
                'student_ids' => [$student->id],
                'company_id' => $this->company->id,
                'start_date' => '2024-01-01',
                'end_date' => '2024-06-01',
            ]);

        $response->assertStatus(403);
    }
}