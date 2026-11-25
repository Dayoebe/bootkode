<?php

namespace App\Http\Controllers;

use App\Models\Learning\Course;
use App\Services\OfflineLearningPackService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OfflineLearningController extends Controller
{
    public function session(Request $request): JsonResponse
    {
        return response()->json(['user_id' => $request->user()->id, 'csrf_token' => csrf_token()])
            ->header('Cache-Control', 'private, no-store');
    }

    public function manifest(Request $request, Course $course, OfflineLearningPackService $packs): JsonResponse
    {
        $types = $request->filled('types')
            ? array_filter(explode(',', (string) $request->query('types')))
            : OfflineLearningPackService::DEFAULT_TYPES;

        $storageLimitMb = max(50, min(5000, (int) $request->query('storage_limit_mb', 500)));

        return response()->json(
            $packs->buildManifest($request->user(), $course, $types, $storageLimitMb)
        )->header('Cache-Control', 'private, no-store');
    }

    public function sync(Request $request, Course $course, OfflineLearningPackService $packs): JsonResponse
    {
        $validated = $request->validate([
            'owner_id' => 'required|integer',
            'events' => 'required|array|max:500',
            'events.*.type' => 'nullable|string|max:50',
            'events.*.lesson_id' => 'required|integer',
            'events.*.completed_at' => 'nullable|date',
            'events.*.time_spent_seconds' => 'nullable|integer|min:0|max:864000',
            'events.*.event_id' => 'nullable|string|max:128',
        ]);
        abort_unless((int) $validated['owner_id'] === (int) $request->user()->id, 403, 'Sign in to the account that downloaded this pack.');

        return response()->json(
            $packs->syncProgress($request->user(), $course, $validated['events'])
        )->header('Cache-Control', 'private, no-store');
    }
}
