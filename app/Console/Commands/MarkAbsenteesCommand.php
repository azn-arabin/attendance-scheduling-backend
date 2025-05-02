<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use App\Models\SchoolClass;
use Carbon\Carbon;
use Illuminate\Console\Command;

class MarkAbsenteesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:mark-absent';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Marks students as absent if attendance not marked 10 minutes after class start';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $cutoff = Carbon::now()->subMinutes(10);

        // Classes started 10+ mins ago
        $classes = SchoolClass::with('batch.students')
            ->where('start_time', '<=', $cutoff) // adjust field if needed
            ->whereDoesntHave('attendances') // optional: reduce load
            ->get();

        $insertData = [];

        foreach ($classes as $class) {
            $studentIds = $class->batch->students->pluck('id');

            // Get already marked students for this class
            $alreadyMarked = Attendance::where('class_id', $class->id)
                ->pluck('student_id')
                ->toArray();

            // Find unmarked students
            $unmarkedStudents = $studentIds->diff($alreadyMarked);

            foreach ($unmarkedStudents as $studentId) {
                $insertData[] = [
                    'student_id' => $studentId,
                    'class_id' => $class->id,
                    'status' => 'absent',
                    'marked_at' => Carbon::now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Bulk insert
        if (!empty($insertData)) {
            Attendance::insert($insertData);
        }

        $this->info('Unmarked attendances marked as absent.');
    }

}
