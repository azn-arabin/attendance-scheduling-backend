<?php

namespace Database\Factories;
// Factory for the Attendance model (records of student attending a class)
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttendanceFactory extends Factory
{
    protected $model = Attendance::class;

    /**
     * Define the model's default state.
     * NOTE: In seeding, we'll use chunked inserts rather than many factory calls to handle volume.
     */
    public function definition()
    {
        $statuses = ['present', 'absent', 'late'];
        $marked = Carbon::now()->subDays(rand(0, 30))->addMinutes(rand(0, 1440));
        return [
            'student_id' => null, // assigned in seeder
            'class_id' => null, // assigned in seeder
            'status' => $this->faker->randomElement($statuses),
            'marked_at' => $marked,
        ];
    }
}
