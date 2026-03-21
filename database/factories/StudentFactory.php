<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->email(),
            'password' => 'password', // Will be hashed by model
            'role' => 'student',
            'school_id' => School::factory(),
        ];
    }
}
