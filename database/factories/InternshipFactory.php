<?php

namespace Database\Factories;

use App\Models\Internship;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class InternshipFactory extends Factory
{
    protected $model = Internship::class;

    public function definition(): array
    {
        $statuses = ['active', 'completed', 'pending'];
        $startDate = $this->faker->dateTimeBetween('-6 months', 'now');
        $endDate = $this->faker->dateTimeBetween($startDate, '+6 months');

        // Get a student
        $student = User::where('role', 'student')->inRandomOrder()->first();
        if (!$student) {
            $student = User::factory()->state(['role' => 'student']);
        }

        // Get a supervisor
        $supervisor = User::where('role', 'company_supervisor')->inRandomOrder()->first();
        if (!$supervisor) {
            $supervisor = User::factory()->state(['role' => 'company_supervisor']);
        }

        return [
            'student_id' => $student->id,
            'supervisor_id' => $supervisor->id,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => $this->faker->randomElement($statuses),
        ];
    }
}
