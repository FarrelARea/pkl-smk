<?php

namespace Tests\Feature\Api\V1;

use App\Models\AssessmentTemplate;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssessmentTemplateTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected SchoolClass $class;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'school_admin']);
        $this->class = SchoolClass::factory()->create();
    }

    private function templatePayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Template AKL',
            'class_id' => $this->class->id,
            'sections' => [
                [
                    'number' => 1,
                    'title' => 'Menerapkan soft skills',
                    'indicators' => [
                        ['number' => '1.1', 'description' => 'Komunikasi'],
                        ['number' => '1.2', 'description' => 'Integritas'],
                    ],
                ],
                [
                    'number' => 2,
                    'title' => 'Menerapkan norma dan K3LH',
                    'indicators' => [
                        [
                            'number' => '2.1',
                            'description' => 'Menggunakan APD',
                            'children' => [
                                ['number' => '2.1.1', 'description' => 'APD lengkap'],
                            ],
                        ],
                    ],
                ],
            ],
        ], $overrides);
    }

    public function test_admin_can_create_template(): void
    {
        $response = $this->actingAs($this->admin, 'api')
            ->postJson('/api/v1/assessment-templates', $this->templatePayload());

        $response->assertStatus(201);
        $response->assertJsonPath('name', 'Template AKL');
        $response->assertJsonPath('is_active', true);

        $this->assertDatabaseCount('assessment_templates', 1);
        $this->assertDatabaseCount('template_sections', 2);
        $this->assertDatabaseCount('template_indicators', 4); // 1.1, 1.2, 2.1, 2.1.1
    }

    public function test_creating_template_deactivates_previous(): void
    {
        $this->actingAs($this->admin, 'api')
            ->postJson('/api/v1/assessment-templates', $this->templatePayload());

        $this->actingAs($this->admin, 'api')
            ->postJson('/api/v1/assessment-templates', $this->templatePayload(['name' => 'Template v2']));

        $this->assertDatabaseCount('assessment_templates', 2);
        $this->assertEquals(1, AssessmentTemplate::where('class_id', $this->class->id)->where('is_active', true)->count());
    }

    public function test_admin_can_view_template_with_structure(): void
    {
        $response = $this->actingAs($this->admin, 'api')
            ->postJson('/api/v1/assessment-templates', $this->templatePayload());

        $id = $response->json('id');

        $response = $this->actingAs($this->admin, 'api')
            ->getJson("/api/v1/assessment-templates/{$id}");

        $response->assertStatus(200);
        $response->assertJsonCount(2, 'sections');
        $response->assertJsonPath('sections.0.indicators.0.number', '1.1');
    }

    public function test_admin_can_update_template(): void
    {
        $response = $this->actingAs($this->admin, 'api')
            ->postJson('/api/v1/assessment-templates', $this->templatePayload());

        $id = $response->json('id');

        $response = $this->actingAs($this->admin, 'api')
            ->putJson("/api/v1/assessment-templates/{$id}", [
                'name' => 'Updated Name',
            ]);

        $response->assertStatus(200);
        $response->assertJsonPath('name', 'Updated Name');
    }

    public function test_admin_can_delete_template(): void
    {
        $response = $this->actingAs($this->admin, 'api')
            ->postJson('/api/v1/assessment-templates', $this->templatePayload());

        $id = $response->json('id');

        $response = $this->actingAs($this->admin, 'api')
            ->deleteJson("/api/v1/assessment-templates/{$id}");

        $response->assertStatus(200);
        $this->assertDatabaseCount('assessment_templates', 0);
    }

    public function test_validation_requires_sections(): void
    {
        $response = $this->actingAs($this->admin, 'api')
            ->postJson('/api/v1/assessment-templates', [
                'name' => 'Test',
                'class_id' => $this->class->id,
            ]);

        $response->assertStatus(422);
    }
}
