<?php

namespace Tests\Feature\Api\V1;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanySupervisorAssignmentTest extends TestCase
{
    use RefreshDatabase;

    protected User $schoolAdmin;
    protected Company $company;
    protected User $supervisor;
    protected User $student;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->schoolAdmin = User::factory()->create([
            'role' => 'school_admin',
        ]);
        
        $this->company = Company::factory()->create();
        
        $this->supervisor = User::factory()->create([
            'role' => 'company_supervisor',
            'company_id' => null,
        ]);
        
        $this->student = User::factory()->create(['role' => 'student']);
    }

    public function test_unauthenticated_cannot_access_search(): void
    {
        $response = $this->getJson('/api/v1/companies/search');

        $response->assertStatus(401);
    }

    public function test_unauthenticated_cannot_assign_supervisor(): void
    {
        $response = $this->postJson("/api/v1/companies/{$this->company->id}/assign-supervisor", [
            'supervisor_id' => $this->supervisor->id,
        ]);

        $response->assertStatus(401);
    }

    public function test_student_cannot_access_company_search(): void
    {
        $response = $this->actingAs($this->student, 'api')
            ->getJson('/api/v1/companies/search');

        $response->assertStatus(403);
    }

    public function test_student_cannot_assign_supervisor(): void
    {
        $response = $this->actingAs($this->student, 'api')
            ->postJson("/api/v1/companies/{$this->company->id}/assign-supervisor", [
                'supervisor_id' => $this->supervisor->id,
            ]);

        $response->assertStatus(403);
    }

    public function test_school_admin_can_search_companies(): void
    {
        Company::factory()->create(['name' => 'Test Company']);
        
        $response = $this->actingAs($this->schoolAdmin, 'api')
            ->getJson('/api/v1/companies/search');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => ['id', 'name', 'industry', 'address']
            ]
        ]);
    }

    public function test_school_admin_can_filter_search_by_query(): void
    {
        $company = Company::factory()->create(['name' => 'Test Company ABC']);
        Company::factory()->create(['name' => 'Other Company XYZ']);

        $response = $this->actingAs($this->schoolAdmin, 'api')
            ->getJson('/api/v1/companies/search?q=ABC');

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
    }

    public function test_school_admin_can_filter_search_by_industry(): void
    {
        Company::factory()->create(['industry' => 'Technology']);
        Company::factory()->create(['industry' => 'Healthcare']);

        $response = $this->actingAs($this->schoolAdmin, 'api')
            ->getJson('/api/v1/companies/search?industry=Technology');

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
    }

    public function test_school_admin_can_assign_supervisor(): void
    {
        $response = $this->actingAs($this->schoolAdmin, 'api')
            ->postJson("/api/v1/companies/{$this->company->id}/assign-supervisor", [
                'supervisor_id' => $this->supervisor->id,
            ]);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Supervisor assigned to company']);

        $this->assertDatabaseHas('users', [
            'id' => $this->supervisor->id,
            'company_id' => $this->company->id,
        ]);
    }

    public function test_school_admin_can_reassign_supervisor(): void
    {
        $anotherCompany = Company::factory()->create();
        $this->supervisor->update(['company_id' => $anotherCompany->id]);

        $response = $this->actingAs($this->schoolAdmin, 'api')
            ->postJson("/api/v1/companies/{$this->company->id}/assign-supervisor", [
                'supervisor_id' => $this->supervisor->id,
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('users', [
            'id' => $this->supervisor->id,
            'company_id' => $this->company->id,
        ]);
    }

    public function test_assign_supervisor_validates_supervisor_exists(): void
    {
        $response = $this->actingAs($this->schoolAdmin, 'api')
            ->postJson("/api/v1/companies/{$this->company->id}/assign-supervisor", [
                'supervisor_id' => 99999,
            ]);

        $response->assertStatus(422);
    }

    public function test_assign_supervisor_validates_supervisor_role(): void
    {
        $nonSupervisor = User::factory()->create(['role' => 'student']);

        $response = $this->actingAs($this->schoolAdmin, 'api')
            ->postJson("/api/v1/companies/{$this->company->id}/assign-supervisor", [
                'supervisor_id' => $nonSupervisor->id,
            ]);

        $response->assertStatus(422);
    }
}