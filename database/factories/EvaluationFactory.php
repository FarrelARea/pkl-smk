<?php

namespace Database\Factories;

use App\Models\Evaluation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EvaluationFactory extends Factory
{
    protected $model = Evaluation::class;

    public function definition(): array
    {
        $student = User::where('role', 'student')->inRandomOrder()->first();
        if (!$student) {
            $student = User::factory()->state(['role' => 'student']);
        }

        // Evaluator can be teacher or supervisor
        $evaluatorRole = $this->faker->randomElement(['teacher', 'company_supervisor']);
        $evaluator = User::where('role', $evaluatorRole)->inRandomOrder()->first();
        if (!$evaluator) {
            $evaluator = User::factory()->state(['role' => $evaluatorRole]);
        }

        return [
            'student_id' => $student->id,
            'evaluator_id' => $evaluator->id,
            'score' => $this->faker->numberBetween(60, 100),
            'feedback' => $this->faker->paragraph(),
        ];
    }
}
