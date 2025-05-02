<?php

namespace App\Http\Controllers\Api\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Attendance;
use App\Models\SchoolClass;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InstructorController extends Controller
{
    public function myBatches(Request $request): JsonResponse
    {
        $batches = $request->user()->batches()->get();

        return response()->json([
            'message' => 'Batches retrieved successfully',
            'data' => $batches
        ]);
    }
    public function batchStats($batchId)
    {
        $studentIds = Batch::findOrFail($batchId)->students()->pluck('id');
        $stats = Attendance::whereIn('student_id', $studentIds)
            ->selectRaw("status, COUNT(*) as total")
            ->groupBy('status')
            ->get();

        return response()->json($stats);
    }

    public function topStudent($batchId)
    {
        $studentStats = Attendance::selectRaw('student_id, COUNT(*) as total, SUM(status = "present") as present')
            ->whereIn('student_id', Batch::findOrFail($batchId)->students()->pluck('id'))
            ->groupBy('student_id')
            ->get()
            ->map(function ($item) {
                $item->rate = $item->present / $item->total;
                return $item;
            })
            ->sortByDesc('rate')
            ->first();

        if (!$studentStats) return response()->json(['message' => 'No data'], 404);

        $student = User::find($studentStats->student_id);
        return response()->json([
            'student' => $student,
            'present_rate' => round($studentStats->rate * 100, 2)
        ]);
    }

    public function attendanceTrend($batchId)
    {
        $studentIds = Batch::findOrFail($batchId)->students()->pluck('id');
        $trend = Attendance::whereIn('student_id', $studentIds)
            ->where('marked_at', '>=', Carbon::now()->subDays(30))
            ->selectRaw('DATE(marked_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json($trend);
    }
}

