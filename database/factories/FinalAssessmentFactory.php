<?php

namespace Database\Factories;

use App\Models\FinalAssessment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FinalAssessmentFactory extends Factory
{
    protected $model = FinalAssessment::class;

    public function definition(): array
    {
        $student = User::where('role', 'student')->inRandomOrder()->first();
        if (!$student) {
            $student = User::factory()->state(['role' => 'student']);
        }

        $evaluator = User::where('role', 'teacher')->inRandomOrder()->first();
        if (!$evaluator) {
            $evaluator = User::factory()->state(['role' => 'teacher']);
        }

        return [
            'student_id' => $student->id,
            'evaluator_id' => $evaluator->id,
            'score' => $this->faker->numberBetween(60, 100),
        ];
    }
}
