<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class StudentController extends Controller
{
    public function upcomingClasses(): JsonResponse
    {
        $student = Auth::user();
        $now = now();

        $classes = SchoolClass::where('batch_id', $student->batch_id)
            ->where('start_time', '>', $now)
            ->with('instructor:id,full_name') // Load instructor only with name
            ->orderBy('start_time')
            ->paginate(10); // optional: customize pagination

        return response()->json(['data' => $classes]);
    }

    public function markAttendance(Request $request): JsonResponse
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
        ]);

        $class = SchoolClass::findOrFail($request->class_id);
        $student = Auth::user();
        $now = Carbon::now();
        $start = Carbon::parse($class->start_time);
        $windowStart = $start->copy()->subMinutes(10);
        $windowEnd = $start->copy()->addMinutes(10);

        // Check time window
        if (!$now->between($windowStart, $windowEnd)) {
            return response()->json([
                'message' => 'You can only mark attendance within 10 minutes before or after class start time'
            ], 403);
        }

        // Prevent double attendance
        $already = Attendance::where('student_id', $student->id)
            ->where('class_id', $class->id)
            ->exists();

        if ($already) {
            return response()->json(['message' => 'Attendance already marked for this class'], 409);
        }

        // Determine status
        $status = $now->lessThanOrEqualTo($start) ? 'present' : 'late';

        Attendance::create([
            'student_id' => $student->id,
            'class_id' => $class->id,
            'status' => $status,
            'marked_at' => now(),
        ]);

        return response()->json(['message' => "Attendance marked as $status"]);
    }
}
