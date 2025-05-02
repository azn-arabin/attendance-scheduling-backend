<?php

namespace App\Http\Controllers\Api\Instructor;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClassController extends Controller
{
    public function index(): JsonResponse
    {
        $instructor = auth()->user();
        $now = now();

        $classes = $instructor->classes()
            ->where('start_time', '>', $now)
            ->with([
                'batch' => fn($q) => $q->withCount('students')
            ])
            ->orderBy('start_time')
            ->paginate(10); // Customize pagination as needed

        return response()->json([
            'data' => $classes,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'topic' => 'required|string|max:255',
            'start_time' => 'required|date|after:now',
            'duration' => 'required|integer|min:1',
            'batch_id' => 'required|exists:batches,id',
        ]);

        $class = SchoolClass::create([
            'topic' => $validated['topic'],
            'start_time' => $validated['start_time'],
            'batch_id' => $validated['batch_id'],
            'duration' => $validated['duration'],
            'instructor_id' => auth()->id(), // assuming instructor is logged in
        ]);

        // Reload batch with student_count
        $class->load(['batch' => fn($query) => $query->withCount('students')]);

        return response()->json([
            'message' => 'Class created successfully',
            'data' => $class
        ]);
    }

    public function show($id): JsonResponse
    {
        $class = SchoolClass::where('id', $id)
            ->where('instructor_id', Auth::id())
            ->with('batch')
            ->firstOrFail();

        return response()->json([
            'message' => 'Class fetched successfully',
            'data' => $class
        ]);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $class = SchoolClass::where('id', $id)
            ->where('instructor_id', Auth::id())
            ->firstOrFail();

        $validated = $request->validate([
            'topic' => 'sometimes|string|max:255',
            'start_time' => 'sometimes|date',
            'duration' => 'sometimes|integer|min:1',
            'batch_id' => 'sometimes|exists:batches,id',
        ]);

        $class->update($validated);

        // Reload batch with student_count
        $class->load(['batch' => fn($query) => $query->withCount('students')]);

        return response()->json([
            'message' => 'Class updated successfully',
            'data' => $class
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $class = SchoolClass::where('id', $id)
            ->where('instructor_id', Auth::id())
            ->firstOrFail();

        $class->delete();

        return response()->json([
            'message' => 'Class deleted successfully'
        ]);
    }
}
