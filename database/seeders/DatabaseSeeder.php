<?php

namespace Database\Seeders;

use App\Models\Attendance;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Batch;
use App\Models\SchoolClass;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // 1. Create Admin
        User::factory()->admin()->create([
            'email' => 'admin@example.com',
            'full_name' => 'System Administrator',
        ]);

        // 2. Create Instructors
        $instructors = User::factory()->count(20)->instructor()->create();

        // 3. Create Batches
        $batches = Batch::factory()->count(100)->create();

        // Attach instructors to random batches
        $batches->each(function (Batch $batch) use ($instructors) {
        $assigned = $instructors->random(rand(1, 3))->pluck('id');
            $batch->instructors()->attach($assigned);
        });

        // 4. Create Students and assign to batches (500+)
        $students = User::factory()->count(500)->student()->create()->chunk(100)->flatMap(function ($chunk) use ($batches) {
        return $chunk->map(function (User $student) use ($batches) {
            $batch = $batches->random();
                $student->batch()->associate($batch);
                $student->save();
                return $student;
            });
        });

        // 5. Create Classes (3000+)
        $classes = collect();
        foreach ($batches as $batch) {
        $count = rand(20, 40); // average per batch
            for ($i = 0; $i < $count; $i++) {
            $instructorId = $batch->instructors->random()->id;
                $class = SchoolClass::factory()->make([
                'batch_id' => $batch->id,
                    'instructor_id' => $instructorId,
                ]);
                $class->save();
                $classes->push($class);
            }
        }

        // 6. Create Attendance (150k+)
        $attendanceData = [];
        foreach ($classes as $class) {
        $studentIds = $class->batch->students->pluck('id');
            foreach ($studentIds as $studentId) {
            $status = ['present','absent','late'][array_rand([0,1,2])];
                $attendanceData[] = [
                    'student_id' => $studentId,
                    'class_id' => $class->id,
                    'status' => $status,
                    'marked_at' => now()->subDays(rand(0, 30))->addMinutes(rand(0, 1440)),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                if (count($attendanceData) >= 1000) {
                Attendance::insert($attendanceData);
                    $attendanceData = [];
                }
            }
        }
        if (!empty($attendanceData)) {
            Attendance::insert($attendanceData);
        }
    }
}

