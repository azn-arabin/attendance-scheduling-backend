<?php

namespace Database\Factories;
// Factory for the SchoolClass model
use App\Models\SchoolClass;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class SchoolClassFactory extends Factory
{
    protected $model = SchoolClass::class;

    /**
     * Define the model's default state.
     */
    public function definition()
    {
        $start = now()->addDays(rand(-15, 15))->addMinutes(rand(0, 1440));
        return [
            'batch_id' => null, // assigned in seeder
            'instructor_id' => null, // assigned in seeder
            'topic' => $this->faker->sentence(3),
            'start_time' => $start,
            'duration' => $this->faker->numberBetween(30, 120),
        ];
    }
}

