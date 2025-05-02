<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Attendance;
use App\Models\SchoolClass;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Response;

class AdminController extends Controller
{
    public function listBatches()
    {
        $batches = Batch::withCount(['students', 'instructors'])->get();
        return response()->json($batches);
    }

    public function listBatchStudents($batchId)
    {
        $batch = Batch::with('students')->findOrFail($batchId);
        return response()->json($batch->students);
    }

    public function listBatchInstructors($batchId)
    {
        $batch = Batch::with('instructors')->findOrFail($batchId);
        return response()->json($batch->instructors);
    }

    public function getBatchStats($batchId)
    {
        $batch = Batch::with('classes:id,batch_id')->findOrFail($batchId);
        $classIds = $batch->classes->pluck('id');

        // 1. Total attendance stats
        $totalStats = Attendance::whereIn('class_id', $classIds)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        // 2. Most present student
        $mostPresent = Attendance::whereIn('class_id', $classIds)
            ->where('status', 'present')
            ->select('student_id', DB::raw('count(*) as total_present'))
            ->groupBy('student_id')
            ->orderByDesc('total_present')
            ->with('student:id,full_name,email') // adjust as per your user table
            ->first();

        // 3. Attendance trend (past 30 days)
        $trend = Attendance::whereIn('class_id', $classIds)
            ->where('marked_at', '>=', Carbon::now()->subDays(30))
            ->where('status', 'present')
            ->select(DB::raw('DATE(marked_at) as date'), DB::raw('count(*) as present_count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'total_stats' => [
                'present' => $totalStats['present'] ?? 0,
                'absent' => $totalStats['absent'] ?? 0,
                'late' => $totalStats['late'] ?? 0,
            ],
            'most_present_student' => $mostPresent ? [
                'id' => $mostPresent->student->id,
                'name' => $mostPresent->student->full_name,
                'email' => $mostPresent->student->email,
                'total_present' => $mostPresent->total_present,
            ] : null,
            'attendance_trend' => $trend,
        ]);
    }

    public function exportBatchAttendance(Request $request, $batchId): StreamedResponse
    {
        $classes = SchoolClass::where('batch_id', $batchId)->pluck('id');

        $attendances = Attendance::with(['student', 'schoolClass'])
            ->whereIn('class_id', $classes)
            ->orderBy('marked_at')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="batch_' . $batchId . '_attendance.csv"',
        ];

        $columns = ['Student Name', 'Email', 'Class Date', 'Status', 'Marked At'];

        return Response::stream(function () use ($attendances, $columns) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $columns);

            foreach ($attendances as $record) {
                fputcsv($handle, [
                    $record->student->full_name,
                    $record->student->email,
                    $record->schoolClass->start_time->format('Y-m-d H:i'),
                    ucfirst($record->status),
                    $record->marked_at->format('Y-m-d H:i'),
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }
}
