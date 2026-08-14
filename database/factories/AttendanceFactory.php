<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttendanceFactory extends Factory
{
    protected $model = Attendance::class;

    public function definition(): array
    {
        $statuses = ['present', 'absent', 'sick', 'permission'];
        $statusWeights = ['present' => 70, 'absent' => 15, 'sick' => 10, 'permission' => 5];

        $student = User::where('role', 'student')->inRandomOrder()->first();
        if (!$student) {
            $student = User::factory()->state(['role' => 'student']);
        }

        // Weighted random status
        $rand = $this->faker->numberBetween(1, 100);
        $cumulative = 0;
        $status = 'present';
        foreach ($statusWeights as $st => $weight) {
            $cumulative += $weight;
            if ($rand <= $cumulative) {
                $status = $st;
                break;
            }
        }

        return [
            'student_id' => $student->id,
            'date' => $this->faker->dateTimeBetween('-3 months', 'now'),
            'status' => $status,
        ];
    }
}
