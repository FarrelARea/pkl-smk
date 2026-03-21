<?php

namespace Database\Factories;

use App\Models\ClockInOut;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClockInOutFactory extends Factory
{
    protected $model = ClockInOut::class;

    public function definition(): array
    {
        $student = User::where('role', 'student')->inRandomOrder()->first();
        if (!$student) {
            $student = User::factory()->state(['role' => 'student']);
        }

        $clockInTime = $this->faker->dateTimeBetween('-3 months', 'now');
        $clockOutTime = clone $clockInTime;
        $clockOutTime->modify('+' . $this->faker->numberBetween(8, 10) . ' hours');

        return [
            'student_id' => $student->id,
            'clock_in_time' => $clockInTime,
            'clock_out_time' => $clockOutTime,
        ];
    }
}
