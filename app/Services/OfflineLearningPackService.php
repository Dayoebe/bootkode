<?php

namespace App\Services;

use App\Models\Core\User;
use App\Models\Learning\Course;
use App\Models\Learning\CourseEnrollment;
use App\Models\Learning\DownloadableContent;
use App\Models\Learning\Lesson;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class OfflineLearningPackService
{
    public const DEFAULT_TYPES = ['lessons', 'documents', 'images', 'audio'];
    public const ALL_TYPES = ['lessons', 'documents', 'images', 'audio', 'video'];

    public function buildManifest(User $user, Course $course, array $types = self::DEFAULT_TYPES, int $storageLimitMb = 500): array
    {
        $this->ensureUserCanAccessCourse($user, $course);

        $types = $this->normalizeTypes($types);
        $course = $this->loadCourseForPack($course);
        $assetUrls = collect([
            asset('offline-learning.html'),
            asset('js/offline-learning.js'),
            asset('manifest.webmanifest'),
            asset('icons/icon-192.png'),
            asset('icons/icon-512.png'),
            route('course.view', $course->slug),
        ]);
        $sections = [];
        $estimatedBytes = 0;

        foreach ($course->sections as $section) {
            $lessons = [];

            foreach ($section->lessons as $lesson) {
                $resources = $this->lessonResources($lesson, $types);
                $lessonAssets = collect($resources)->pluck('url')->filter();
                $assetUrls = $assetUrls->merge($lessonAssets);
                $assetUrls->push(route('course.view', ['course' => $course->slug, 'lesson' => $lesson->id]));

                $estimatedBytes += strlen(strip_tags((string) ($lesson->content ?? $lesson->text_content ?? '')));
                $estimatedBytes += collect($resources)->sum('size_bytes');

                $lessons[] = [
                    'id' => $lesson->id,
                    'title' => $lesson->title,
                    'slug' => $lesson->slug,
                    'description' => $lesson->description,
                    'content' => in_array('lessons', $types, true) ? (string) ($lesson->content ?: $lesson->text_content) : '',
                    'duration_minutes' => $lesson->duration_minutes,
                    'difficulty_level' => $lesson->difficulty_level,
                    'resources' => $resources,
                    'online_url' => route('course.view', ['course' => $course->slug, 'lesson' => $lesson->id]),
                ];
            }

            $sections[] = [
                'id' => $section->id,
                'title' => $section->title,
                'description' => $section->description,
                'lessons' => $lessons,
            ];
        }

        $cacheUrls = $assetUrls
            ->filter()
            ->unique()
            ->values()
            ->all();

        $manifest = [
            'id' => 'course-' . $course->id . '-' . now()->timestamp,
            'generated_at' => now()->toIso8601String(),
            'csrf_token' => csrf_token(),
            'storage_limit_mb' => $storageLimitMb,
            'estimated_size_mb' => round($estimatedBytes / 1024 / 1024, 2),
            'estimated_bytes' => $estimatedBytes,
            'content_types' => $types,
            'cache_urls' => $cacheUrls,
            'sync_url' => route('offline-learning.sync', $course->slug),
            'manifest_url' => route('offline-learning.manifest', $course->slug),
            'reader_url' => asset('offline-learning.html?course=' . $course->slug),
            'course' => [
                'id' => $course->id,
                'slug' => $course->slug,
                'title' => $course->title,
                'description' => $course->description,
                'thumbnail' => $course->thumbnail ? asset('storage/' . $course->thumbnail) : asset('images/default-course.png'),
                'instructor' => $course->instructor?->name,
                'lesson_count' => collect($sections)->sum(fn ($section) => count($section['lessons'])),
                'section_count' => count($sections),
                'online_url' => route('course.view', $course->slug),
            ],
            'sections' => $sections,
            'completed_lesson_ids' => $user->completedLessons()
                ->whereIn('lesson_id', $course->sections->flatMap->lessons->pluck('id'))
                ->pluck('lesson_id')
                ->map(fn ($id) => (int) $id)
                ->values()
                ->all(),
        ];

        DownloadableContent::updateOrCreate(
            [
                'user_id' => $user->id,
                'course_id' => $course->id,
            ],
            [
                'downloaded_at' => now(),
                'last_accessed_at' => now(),
                'size_mb' => $manifest['estimated_size_mb'],
                'storage_limit_mb' => $storageLimitMb,
                'storage_bytes' => $estimatedBytes,
                'cached_asset_count' => count($cacheUrls),
                'content_types' => $types,
                'status' => DownloadableContent::STATUS_READY,
                'manifest' => Arr::except($manifest, ['csrf_token']),
            ]
        );

        return $manifest;
    }

    public function syncProgress(User $user, Course $course, array $events): array
    {
        $this->ensureUserCanAccessCourse($user, $course);
        $course = $this->loadCourseForPack($course);
        $lessonIds = $course->sections->flatMap->lessons->pluck('id')->map(fn ($id) => (int) $id)->all();
        $synced = 0;
        $ignored = 0;

        DB::transaction(function () use ($user, $course, $events, $lessonIds, &$synced, &$ignored) {
            foreach ($events as $event) {
                $lessonId = (int) ($event['lesson_id'] ?? 0);

                if (! in_array($lessonId, $lessonIds, true)) {
                    $ignored++;
                    continue;
                }

                if (($event['type'] ?? 'lesson_completed') === 'lesson_completed') {
                    $completedAt = filled($event['completed_at'] ?? null)
                        ? Carbon::parse($event['completed_at'])
                        : now();

                    $user->completedLessons()->syncWithoutDetaching([
                        $lessonId => ['completed_at' => $completedAt],
                    ]);

                    \App\Models\Learning\LessonProgress::updateOrCreate(
                        ['user_id' => $user->id, 'lesson_id' => $lessonId],
                        [
                            'time_spent_seconds' => max(0, (int) ($event['time_spent_seconds'] ?? 0)),
                            'last_accessed_at' => now(),
                        ]
                    );

                    $synced++;
                }
            }

            $this->syncCourseProgress($user, $course);
        });

        DownloadableContent::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->update([
                'status' => DownloadableContent::STATUS_SYNCED,
                'last_synced_at' => now(),
                'last_accessed_at' => now(),
            ]);

        return [
            'synced' => $synced,
            'ignored' => $ignored,
            'progress_percentage' => $this->courseProgressPercentage($user, $course),
        ];
    }

    public function syncCourseProgress(User $user, Course $course): void
    {
        $progress = $this->courseProgressPercentage($user, $course);

        CourseEnrollment::where('course_id', $course->id)
            ->where('user_id', $user->id)
            ->update([
                'progress_percentage' => $progress,
                'is_completed' => $progress >= 100,
                'completed_at' => $progress >= 100 ? now() : null,
                'updated_at' => now(),
            ]);

        if (Schema::hasTable('course_user')) {
            DB::table('course_user')
                ->where('course_id', $course->id)
                ->where('user_id', $user->id)
                ->update([
                    'progress' => $progress,
                    'completed_at' => $progress >= 100 ? now() : null,
                    'last_accessed_at' => now(),
                    'updated_at' => now(),
                ]);
        }
    }

    public function courseProgressPercentage(User $user, Course $course): int
    {
        $course = $this->loadCourseForPack($course);
        $lessonIds = $course->sections->flatMap->lessons->pluck('id');
        $total = $lessonIds->count();

        if ($total === 0) {
            return 0;
        }

        $completed = $user->completedLessons()
            ->whereIn('lesson_id', $lessonIds)
            ->count();

        return (int) round(($completed / $total) * 100);
    }

    private function loadCourseForPack(Course $course): Course
    {
        return Course::query()
            ->with([
                'instructor',
                'sections' => fn ($query) => $query->orderBy('order')->with([
                    'lessons' => fn ($lessonQuery) => $lessonQuery->published()->orderBy('order'),
                ]),
            ])
            ->whereKey($course->id)
            ->firstOrFail();
    }

    private function ensureUserCanAccessCourse(User $user, Course $course): void
    {
        $hasModernEnrollment = CourseEnrollment::where('course_id', $course->id)
            ->where('user_id', $user->id)
            ->exists();

        $hasLegacyEnrollment = Schema::hasTable('course_user') && DB::table('course_user')
            ->where('course_id', $course->id)
            ->where('user_id', $user->id)
            ->exists();

        if (! $hasModernEnrollment && ! $hasLegacyEnrollment) {
            abort(403, 'You are not enrolled in this course.');
        }
    }

    private function normalizeTypes(array $types): array
    {
        $types = array_values(array_intersect($types ?: self::DEFAULT_TYPES, self::ALL_TYPES));

        return $types ?: self::DEFAULT_TYPES;
    }

    private function lessonResources(Lesson $lesson, array $types): array
    {
        $resources = [];

        if (in_array('documents', $types, true)) {
            foreach ($lesson->getDocumentsArray() as $document) {
                $resources[] = $this->fileResource($document, 'document');
            }
        }

        if (in_array('images', $types, true) && $lesson->image_path) {
            $resources[] = $this->fileResource([
                'path' => $lesson->image_path,
                'name' => 'Main lesson image',
                'size' => 0,
            ], 'image');
        }

        if (in_array('images', $types, true)) {
            foreach ($lesson->getImagesArray() as $image) {
                $resources[] = $this->fileResource($image, 'image');
            }
        }

        if (in_array('audio', $types, true) && $lesson->audio_path) {
            $resources[] = $this->fileResource([
                'path' => $lesson->audio_path,
                'name' => 'Audio lesson',
                'size' => 0,
            ], 'audio');
        }

        if (in_array('audio', $types, true)) {
            foreach ($lesson->getAudiosArray() as $audio) {
                $resources[] = $this->fileResource($audio, 'audio');
            }
        }

        if (in_array('video', $types, true)) {
            foreach ($lesson->getVideosArray() as $video) {
                $resources[] = $this->fileResource($video, 'video');
            }
        }

        if (in_array('video', $types, true) && $lesson->video_url) {
            $resources[] = [
                'type' => 'video_link',
                'name' => 'Video lesson',
                'url' => $lesson->video_url,
                'size_bytes' => 0,
                'offline_ready' => str_starts_with($lesson->video_url, url('/')),
            ];
        }

        return array_values(array_filter($resources));
    }

    private function fileResource(array $file, string $type): array
    {
        $path = $file['path'] ?? null;

        if (! $path) {
            return [];
        }

        return [
            'type' => $type,
            'name' => $file['name'] ?? ucfirst($type),
            'url' => asset('storage/' . ltrim($path, '/')),
            'size_bytes' => (int) ($file['size'] ?? 0),
            'offline_ready' => true,
        ];
    }
}
