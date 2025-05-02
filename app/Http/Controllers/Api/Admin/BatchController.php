<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BatchController extends Controller
{
    // Public: Get all batches (no pagination)
    public function index(): JsonResponse
    {
        $batches = Batch::withCount("instructors")->withCount("students")->get();
        return response()->json(['data' => $batches]);
    }

    // Public: Get single batch by ID
    public function show($id): JsonResponse
    {
        $batch = Batch::findOrFail($id);
        return response()->json(['data' => $batch]);
    }

    // Admin only: Create a new batch
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:batches,name',
            'description' => 'nullable|string',
        ]);

        $batch = Batch::create($validated);

        // Reload the batch with instructor and student counts
        $batch->loadCount(['instructors', 'students']);

        return response()->json(['data' => $batch, 'message' => 'Batch created successfully.']);
    }

    // Admin only: Update a batch
    public function update(Request $request, $id): JsonResponse
    {
        $batch = Batch::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|unique:batches,name,' . $batch->id,
            'description' => 'nullable|string',
        ]);

        $batch->update($validated);

        // Reload the batch with instructor and student counts
        $batch->loadCount(['instructors', 'students']);

        return response()->json(['data' => $batch, 'message' => 'Batch updated successfully.']);
    }

    // Optional: Delete a batch (if needed)
    public function destroy($id): JsonResponse
    {
        $batch = Batch::findOrFail($id);
        $batch->delete();

        return response()->json(['message' => 'Batch deleted successfully.']);
    }

}
