<?php

namespace Database\Factories;

use App\Models\PermissionRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PermissionRequestFactory extends Factory
{
    protected $model = PermissionRequest::class;

    public function definition(): array
    {
        $statuses = ['pending', 'approved', 'rejected'];
        $statusWeights = ['pending' => 30, 'approved' => 50, 'rejected' => 20];

        $student = User::where('role', 'student')->inRandomOrder()->first();
        if (!$student) {
            $student = User::factory()->state(['role' => 'student']);
        }

        // Get optional handler (teacher)
        $handler = $this->faker->boolean(60) ? User::where('role', 'teacher')->inRandomOrder()->first() : null;

        // Weighted random status
        $rand = $this->faker->numberBetween(1, 100);
        $cumulative = 0;
        $status = 'pending';
        foreach ($statusWeights as $st => $weight) {
            $cumulative += $weight;
            if ($rand <= $cumulative) {
                $status = $st;
                break;
            }
        }

        return [
            'student_id' => $student->id,
            'reason' => $this->faker->sentence(),
            'status' => $status,
            'handled_by' => $handler?->id,
        ];
    }
}
