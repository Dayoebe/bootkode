<?php

namespace App\Http\Controllers;

use App\Models\Learning\Course;
use App\Services\OfflineLearningPackService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OfflineLearningController extends Controller
{
    public function manifest(Request $request, Course $course, OfflineLearningPackService $packs): JsonResponse
    {
        $types = $request->filled('types')
            ? array_filter(explode(',', (string) $request->query('types')))
            : OfflineLearningPackService::DEFAULT_TYPES;

        $storageLimitMb = max(50, min(5000, (int) $request->query('storage_limit_mb', 500)));

        return response()->json(
            $packs->buildManifest($request->user(), $course, $types, $storageLimitMb)
        );
    }

    public function sync(Request $request, Course $course, OfflineLearningPackService $packs): JsonResponse
    {
        $validated = $request->validate([
            'events' => 'required|array|max:500',
            'events.*.type' => 'nullable|string|max:50',
            'events.*.lesson_id' => 'required|integer',
            'events.*.completed_at' => 'nullable|date',
            'events.*.time_spent_seconds' => 'nullable|integer|min:0|max:864000',
        ]);

        return response()->json(
            $packs->syncProgress($request->user(), $course, $validated['events'])
        );
    }
}
