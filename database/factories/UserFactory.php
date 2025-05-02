<?php

namespace Database\Factories;
// Factory for the User model (students/instructors)
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        $roles = ['student', 'instructor', 'admin'];
        return [
            'full_name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => bcrypt('password'), // default password
            'gender' => Arr::random(['male','female','other']),
            'role' => 'student', // default, override in states
            'batch_id' => null, // assigned later for students
            'remember_token' => Str::random(10),
        ];
    }

    public function student()
    {
        return $this->state(fn (array $attributes) => [
        'role' => 'student',
        ]);
    }

    public function instructor()
    {
        return $this->state(fn (array $attributes) => [
        'role' => 'instructor',
        ]);
    }

    public function admin()
    {
        return $this->state(fn (array $attributes) => [
        'role' => 'admin',
        ]);
    }
}

