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

        $classes = SchoolClass::whereHas('batches.students', function ($query) use ($student) {
            $query->where('users.id', $student->id);
        })
            ->where('start_time', '>', $now)
            ->orderBy('start_time')
            ->with('batches')
            ->get();

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

        // Check time window
        $start = Carbon::parse($class->start_time)->subMinutes(10);
        $end = Carbon::parse($class->start_time)->addMinutes(10);
        if (!$now->between($start, $end)) {
            return response()->json(['message' => 'You can only mark attendance within 10 minutes before or after class start time'], 403);
        }

        // Prevent double attendance
        $already = Attendance::where('student_id', $student->id)
            ->where('class_id', $class->id)
            ->exists();

        if ($already) {
            return response()->json(['message' => 'Attendance already marked for this class'], 409);
        }

        Attendance::create([
            'student_id' => $student->id,
            'class_id' => $class->id,
            'status' => 'present', // assume present by default
            'marked_at' => now(),
        ]);

        return response()->json(['message' => 'Attendance marked']);
    }
}
