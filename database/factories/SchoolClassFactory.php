<?php

namespace Database\Factories;

use App\Models\SchoolClass;
use App\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;

class SchoolClassFactory extends Factory
{
    protected $model = SchoolClass::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->text(20),
            'academic_year' => '2025/2026',
            'school_id' => School::factory(),
        ];
    }
}
