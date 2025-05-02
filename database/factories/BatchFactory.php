<?php

namespace Database\Factories;
// Factory for the Batch model
use App\Models\Batch;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BatchFactory extends Factory
{
    protected $model = Batch::class;

    /**
     * Define the model's default state.
     */
    public function definition()
    {
        return [
            'name' => 'Batch ' . $this->faker->unique()->numberBetween(1, 1000),
            'description' => $this->faker->sentence(),
        ];
    }
}
