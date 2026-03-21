<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyFactory extends Factory
{
    protected $model = Company::class;

    public function definition(): array
    {
        $industries = ['Technology', 'Finance', 'Healthcare', 'Retail', 'Manufacturing', 'Education', 'Consulting', 'Energy', 'Transportation', 'Real Estate'];

        return [
            'name' => $this->faker->unique()->company(),
            'address' => $this->faker->address(),
            'industry' => $this->faker->randomElement($industries),
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->unique()->companyEmail(),
        ];
    }
}
