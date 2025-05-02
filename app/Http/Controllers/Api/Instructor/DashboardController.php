<?php

namespace App\Http\Controllers\Api\Instructor;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function stats(Request $request): JsonResponse
    {
        $instructor = auth()->user();

        $totalClasses = $instructor->classes()->count();
        $upcomingClasses = $instructor->classes()->where('start_time', '>', now())->count();
        $studentIds = User::whereIn('batch_id', $instructor->batches->pluck('id'))->pluck('id')->unique();

        return response()->json([
            'total_classes' => $totalClasses,
            'upcoming_classes' => $upcomingClasses,
            'students_taught' => $studentIds->count(),
        ]);
    }

    public function classDistribution(): JsonResponse
    {
        $instructor = auth()->user();

        $distribution = $instructor->classes()
            ->select('batch_id', DB::raw('count(*) as total'))
            ->groupBy('batch_id')
            ->with('batch:id,name')
            ->get()
            ->map(fn($item) => [
                'batch_name' => $item->batch->name,
                'total' => $item->total,
            ]);

        return response()->json($distribution);
    }

    public function monthlyClasses(): JsonResponse
    {
        $instructor = auth()->user();

        $monthly = $instructor->classes()
            ->where('start_time', '>=', now()->subMonths(6))
            ->selectRaw("DATE_FORMAT(start_time, '%Y-%m') as month, COUNT(*) as total")
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return response()->json($monthly);
    }

}
