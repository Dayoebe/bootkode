<?php

namespace App\Services;

use App\Models\Core\User;
use App\Models\Learning\Course;
use App\Models\Learning\CourseQualityCheck;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class CourseQualityService
{
    public const STATUS_NOT_CHECKED = 'not_checked';
    public const STATUS_NEEDS_WORK = 'needs_work';
    public const STATUS_READY = 'ready';
    public const STATUS_VERIFIED = 'verified';
    public const STATUS_STALE = 'stale';

    public function scanAndPersist(Course $course, ?User $checker = null, bool $checkRemoteMedia = false): CourseQualityCheck
    {
        $course->loadMissing([
            'category',
            'instructor',
            'sections.lessons.assessments.questions',
            'sections.assessments.questions',
            'directAssessments.questions',
        ]);

        $result = $this->evaluate($course, $checkRemoteMedia);

        $check = CourseQualityCheck::create([
            'course_id' => $course->id,
            'checked_by' => $checker?->id,
            'score' => $result['score'],
            'status' => $result['status'],
            'public_label' => $result['public_label'],
            'completeness_percent' => $result['summary']['completeness_percent'],
            'assessment_coverage_percent' => $result['summary']['assessment_coverage_percent'],
            'media_health_percent' => $result['summary']['media_health_percent'],
            'freshness_percent' => $result['summary']['freshness_percent'],
            'broken_media_count' => $result['summary']['broken_media_count'],
            'unchecked_external_media_count' => $result['summary']['unchecked_external_media_count'],
            'remote_media_checked' => $checkRemoteMedia,
            'issues' => $result['issues'],
            'media_results' => $result['media_results'],
            'summary' => $result['summary'],
            'checked_at' => now(),
        ]);

        $course->forceFill([
            'quality_score' => $result['score'],
            'quality_status' => $result['status'],
            'quality_public_label' => $result['public_label'],
            'quality_summary' => $result['summary'],
            'quality_issues' => $result['issues'],
            'quality_last_checked_at' => now(),
            'quality_checked_by' => $checker?->id,
        ])->save();

        return $check;
    }

    public function markReviewed(Course $course, ?User $reviewer = null, int $daysUntilNextReview = 180): void
    {
        $course->forceFill([
            'quality_reviewed_at' => now(),
            'quality_review_due_at' => now()->addDays($daysUntilNextReview),
            'quality_checked_by' => $reviewer?->id ?? $course->quality_checked_by,
        ])->save();

        if ($course->quality_last_checked_at) {
            $this->scanAndPersist($course->fresh(), $reviewer, false);
        }
    }

    public function evaluate(Course $course, bool $checkRemoteMedia = false): array
    {
        $issues = [];
        $completeness = $this->completeness($course, $issues);
        $assessment = $this->assessmentCoverage($course, $issues);
        $media = $this->mediaHealth($course, $issues, $checkRemoteMedia);
        $freshness = $this->freshness($course, $issues);

        $score = (int) round(
            ($completeness['percent'] * 0.45)
            + ($assessment['percent'] * 0.25)
            + ($media['percent'] * 0.20)
            + ($freshness['percent'] * 0.10)
        );

        $status = $this->statusFor($score, $media['broken_count'], $assessment['percent'], $course);
        $publicLabel = $this->publicLabelFor($status);

        return [
            'score' => $score,
            'status' => $status,
            'public_label' => $publicLabel,
            'issues' => $issues,
            'media_results' => $media['results'],
            'summary' => [
                'completeness_percent' => $completeness['percent'],
                'assessment_coverage_percent' => $assessment['percent'],
                'media_health_percent' => $media['percent'],
                'freshness_percent' => $freshness['percent'],
                'broken_media_count' => $media['broken_count'],
                'unchecked_external_media_count' => $media['unchecked_external_count'],
                'media_count' => $media['total_count'],
                'lessons_count' => $completeness['lessons_count'],
                'sections_count' => $completeness['sections_count'],
                'assessments_count' => $assessment['assessments_count'],
                'questions_count' => $assessment['questions_count'],
                'review_due_at' => $course->quality_review_due_at?->toDateTimeString(),
                'approval_ready' => $score >= 70 && $media['broken_count'] === 0 && $assessment['percent'] >= 50 && ! in_array($status, [self::STATUS_NEEDS_WORK, self::STATUS_STALE], true),
            ],
        ];
    }

    private function completeness(Course $course, array &$issues): array
    {
        $sections = $course->sections;
        $lessons = $sections->flatMap(fn ($section) => $section->lessons);
        $lessonCount = $lessons->count();
        $sectionCount = $sections->count();

        $score = 0;
        $score += $this->filled($course->title) ? 5 : $this->issue($issues, 'missing_title', 'critical', 'Course title is missing.');
        $score += Str::length(strip_tags((string) $course->description)) >= 80 ? 10 : $this->issue($issues, 'thin_description', 'warning', 'Course description should explain the outcome, audience, and value clearly.');
        $score += $this->filled($course->thumbnail) ? 8 : $this->issue($issues, 'missing_thumbnail', 'warning', 'Course thumbnail is missing.');
        $score += $course->category_id ? 5 : $this->issue($issues, 'missing_category', 'warning', 'Course category is not set.');
        $score += $course->estimated_duration_minutes ? 4 : $this->issue($issues, 'missing_duration', 'warning', 'Estimated duration is missing.');
        $score += $this->filled($course->target_audience) ? 4 : $this->issue($issues, 'missing_target_audience', 'info', 'Target audience is not defined.');
        $score += count(array_filter($course->learning_outcomes ?? [])) >= 3 ? 8 : $this->issue($issues, 'weak_learning_outcomes', 'warning', 'Add at least three measurable learning outcomes.');
        $score += $this->filled($course->syllabus_overview) ? 4 : $this->issue($issues, 'missing_syllabus', 'info', 'Syllabus overview is missing.');

        $score += $sectionCount > 0 ? 8 : $this->issue($issues, 'missing_sections', 'critical', 'Course has no sections/modules.');
        $score += $lessonCount >= 3 ? 8 : $this->issue($issues, 'too_few_lessons', 'critical', 'Course should have at least three lessons before approval.');

        $lessonsWithContent = $lessons->filter(fn ($lesson) => $this->filled($lesson->content) || $this->filled($lesson->text_content) || $this->filled($lesson->video_url))->count();
        $contentRatio = $lessonCount > 0 ? $lessonsWithContent / $lessonCount : 0;
        $score += $contentRatio >= 0.8 ? 18 : $this->issue($issues, 'thin_lesson_content', 'critical', 'At least 80% of lessons should contain text, video, or structured content.');

        $lessonsWithDuration = $lessons->filter(fn ($lesson) => (int) $lesson->duration_minutes > 0)->count();
        $durationRatio = $lessonCount > 0 ? $lessonsWithDuration / $lessonCount : 0;
        $score += $durationRatio >= 0.8 ? 8 : $this->issue($issues, 'missing_lesson_durations', 'warning', 'Most lessons need estimated durations.');

        $publishedLessons = $lessons->filter(fn ($lesson) => $lesson->published_at || ($lesson->scheduled_publish_at && $lesson->scheduled_publish_at->isPast()))->count();
        $publishedRatio = $lessonCount > 0 ? $publishedLessons / $lessonCount : 0;
        $score += $publishedRatio >= 0.8 ? 10 : $this->issue($issues, 'unpublished_lessons', 'warning', 'Most lessons should be published or scheduled before public approval.');

        return [
            'percent' => min(100, $score),
            'sections_count' => $sectionCount,
            'lessons_count' => $lessonCount,
        ];
    }

    private function assessmentCoverage(Course $course, array &$issues): array
    {
        $assessments = $this->courseAssessments($course);
        $lessonCount = $course->sections->flatMap(fn ($section) => $section->lessons)->count();
        $sectionCount = $course->sections->count();
        $assessmentCount = $assessments->count();
        $questionsCount = $assessments->sum(fn ($assessment) => $assessment->questions->count());
        $mandatoryCount = $assessments->where('is_mandatory', true)->count();
        $sectionCoverage = $sectionCount > 0
            ? $course->sections->filter(fn ($section) => $section->assessments->isNotEmpty() || $section->lessons->contains(fn ($lesson) => $lesson->assessments->isNotEmpty()))->count() / $sectionCount
            : 0;

        $requiredAssessments = max(1, (int) ceil(max($lessonCount, 1) / 4));
        $score = 0;
        $score += min(35, (int) round(($assessmentCount / $requiredAssessments) * 35));
        $score += min(25, (int) round($sectionCoverage * 25));
        $score += $questionsCount >= max(5, $assessmentCount * 3) ? 25 : (int) min(25, $questionsCount * 3);
        $score += $mandatoryCount > 0 ? 15 : 0;

        if ($assessmentCount === 0) {
            $this->issue($issues, 'missing_assessments', 'critical', 'Course has no assessment, project, assignment, or quiz.');
        }

        if ($questionsCount === 0 && $assessmentCount > 0) {
            $this->issue($issues, 'assessments_without_questions', 'critical', 'Assessments exist but have no questions or criteria.');
        }

        if ($sectionCoverage < 0.5 && $sectionCount > 1) {
            $this->issue($issues, 'weak_section_assessment_coverage', 'warning', 'Less than half of the sections have assessment coverage.');
        }

        if ($mandatoryCount === 0 && $assessmentCount > 0) {
            $this->issue($issues, 'no_mandatory_assessment', 'info', 'Mark at least one assessment as mandatory for clearer completion standards.');
        }

        return [
            'percent' => min(100, $score),
            'assessments_count' => $assessmentCount,
            'questions_count' => $questionsCount,
        ];
    }

    private function mediaHealth(Course $course, array &$issues, bool $checkRemoteMedia): array
    {
        $items = $this->mediaCandidates($course);
        $results = [];
        $broken = 0;
        $uncheckedExternal = 0;

        foreach ($items as $item) {
            $result = $this->checkMediaItem($item, $checkRemoteMedia);
            $results[] = $result;

            if ($result['status'] === 'broken') {
                $broken++;
            }

            if ($result['status'] === 'unchecked') {
                $uncheckedExternal++;
            }
        }

        $total = count($results);
        $percent = $total === 0 ? 100 : max(0, (int) round((($total - $broken) / $total) * 100));

        if ($broken > 0) {
            $this->issue($issues, 'broken_media', 'critical', "{$broken} media item(s) are broken or missing.");
        }

        if ($uncheckedExternal > 0) {
            $this->issue($issues, 'unchecked_external_media', 'info', "{$uncheckedExternal} external media URL(s) need a full remote check.");
        }

        return [
            'percent' => $percent,
            'broken_count' => $broken,
            'unchecked_external_count' => $uncheckedExternal,
            'total_count' => $total,
            'results' => collect($results)->take(50)->values()->all(),
        ];
    }

    private function freshness(Course $course, array &$issues): array
    {
        if (! $course->quality_review_due_at) {
            $this->issue($issues, 'missing_review_date', 'warning', 'No editorial review due date is set.');

            return ['percent' => 60];
        }

        if ($course->quality_review_due_at->isFuture()) {
            return ['percent' => 100];
        }

        $daysOverdue = $course->quality_review_due_at->diffInDays(now());
        $this->issue($issues, 'review_overdue', 'critical', "Editorial review is overdue by {$daysOverdue} day(s).");

        return ['percent' => $daysOverdue > 90 ? 35 : 60];
    }

    private function statusFor(int $score, int $brokenMediaCount, int $assessmentCoverage, Course $course): string
    {
        if ($brokenMediaCount > 0 || $score < 70 || $assessmentCoverage < 50) {
            return self::STATUS_NEEDS_WORK;
        }

        if ($course->quality_review_due_at && $course->quality_review_due_at->isPast()) {
            return self::STATUS_STALE;
        }

        return $score >= 85 ? self::STATUS_VERIFIED : self::STATUS_READY;
    }

    private function publicLabelFor(string $status): ?string
    {
        return match ($status) {
            self::STATUS_VERIFIED => 'Editor verified',
            self::STATUS_READY => 'Quality checked',
            self::STATUS_STALE => 'Review due',
            default => null,
        };
    }

    private function courseAssessments(Course $course): Collection
    {
        $sectionAssessments = $course->sections->flatMap(fn ($section) => $section->assessments);
        $lessonAssessments = $course->sections->flatMap(fn ($section) => $section->lessons)->flatMap(fn ($lesson) => $lesson->assessments);

        return $course->directAssessments
            ->concat($sectionAssessments)
            ->concat($lessonAssessments)
            ->unique('id')
            ->values();
    }

    private function mediaCandidates(Course $course): array
    {
        $items = [];
        $this->addMediaItem($items, $course->thumbnail, 'thumbnail', 'Course thumbnail');
        $this->addMediaArray($items, $course->images, 'image', 'Course images');
        $this->addMediaArray($items, $course->documents, 'document', 'Course documents');
        $this->addMediaArray($items, $course->videos, 'video', 'Course videos');
        $this->addMediaArray($items, $course->external_links, 'external_link', 'Course external links');

        foreach ($course->sections as $section) {
            foreach ($section->lessons as $lesson) {
                $location = "Lesson: {$lesson->title}";
                $this->addMediaItem($items, $lesson->video_url, 'video', $location);
                $this->addMediaItem($items, $lesson->image_path, 'image', $location);
                $this->addMediaItem($items, $lesson->audio_path, 'audio', $location);
                $this->addMediaItem($items, $lesson->file_path, 'file', $location);
                $this->addMediaArray($items, $lesson->images, 'image', $location);
                $this->addMediaArray($items, $lesson->documents, 'document', $location);
                $this->addMediaArray($items, $lesson->audios, 'audio', $location);
                $this->addMediaArray($items, $lesson->videos, 'video', $location);
                $this->addMediaArray($items, $lesson->external_links, 'external_link', $location);
                $this->addHtmlMedia($items, $lesson->content, $location);
                $this->addHtmlMedia($items, $lesson->text_content, $location);
            }
        }

        foreach ($this->courseAssessments($course) as $assessment) {
            $this->addMediaArray($items, $assessment->resources, 'assessment_resource', "Assessment: {$assessment->title}");
        }

        return collect($items)
            ->filter(fn ($item) => $this->filled($item['value']))
            ->unique(fn ($item) => $item['type'] . '|' . $item['location'] . '|' . $item['value'])
            ->values()
            ->all();
    }

    private function addMediaArray(array &$items, mixed $value, string $type, string $location): void
    {
        foreach ($this->extractMediaValues($value) as $media) {
            $this->addMediaItem($items, $media, $type, $location);
        }
    }

    private function addHtmlMedia(array &$items, ?string $html, string $location): void
    {
        if (! $this->filled($html)) {
            return;
        }

        preg_match_all('/(?:src|href)=["\']([^"\']+)["\']/i', $html, $matches);

        foreach ($matches[1] ?? [] as $url) {
            $this->addMediaItem($items, html_entity_decode($url), 'embedded_media', $location);
        }
    }

    private function addMediaItem(array &$items, mixed $value, string $type, string $location): void
    {
        if (! is_string($value) && ! is_numeric($value)) {
            return;
        }

        $value = trim((string) $value);

        if ($value === '' || Str::startsWith($value, ['#', 'mailto:', 'tel:', 'javascript:', 'data:'])) {
            return;
        }

        $items[] = [
            'type' => $type,
            'location' => $location,
            'value' => $value,
        ];
    }

    private function extractMediaValues(mixed $value): array
    {
        if (! $value) {
            return [];
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $this->extractMediaValues($decoded);
            }

            return [$value];
        }

        if (! is_array($value)) {
            return [];
        }

        $results = [];
        $mediaKeys = ['url', 'href', 'src', 'path', 'file', 'file_path', 'image_path', 'audio_path', 'video_url', 'download_url', 'public_url', 'secure_url'];

        foreach ($value as $key => $item) {
            if (is_string($key) && in_array($key, $mediaKeys, true) && is_scalar($item)) {
                $results[] = (string) $item;
                continue;
            }

            if (is_array($item)) {
                $results = array_merge($results, $this->extractMediaValues($item));
                continue;
            }

            if (is_int($key) && is_scalar($item)) {
                $results[] = (string) $item;
            }
        }

        return array_values(array_filter($results));
    }

    private function checkMediaItem(array $item, bool $checkRemoteMedia): array
    {
        $value = $item['value'];

        if ($this->isExternalUrl($value)) {
            if (! $checkRemoteMedia) {
                return [...$item, 'status' => 'unchecked', 'detail' => 'External URL queued for remote check.'];
            }

            return $this->checkExternalMedia($item);
        }

        $path = $this->normalizeLocalPath($value);
        $exists = $path && (
            file_exists(public_path($path))
            || Storage::disk('public')->exists($path)
            || file_exists(storage_path('app/public/' . $path))
        );

        return [
            ...$item,
            'status' => $exists ? 'ok' : 'broken',
            'detail' => $exists ? 'Local file found.' : 'Local file is missing.',
        ];
    }

    private function checkExternalMedia(array $item): array
    {
        try {
            $response = Http::timeout(5)->withoutVerifying()->head($item['value']);

            if ($response->status() === 405 || $response->status() === 403) {
                $response = Http::timeout(5)->withoutVerifying()->get($item['value']);
            }

            return [
                ...$item,
                'status' => ($response->successful() || ($response->status() >= 300 && $response->status() < 400)) ? 'ok' : 'broken',
                'detail' => 'HTTP ' . $response->status(),
            ];
        } catch (Throwable $exception) {
            return [
                ...$item,
                'status' => 'broken',
                'detail' => Str::limit($exception->getMessage(), 180),
            ];
        }
    }

    private function normalizeLocalPath(string $value): ?string
    {
        $path = trim(parse_url($value, PHP_URL_PATH) ?: $value);
        $path = ltrim(urldecode($path), '/');

        if ($path === '') {
            return null;
        }

        if (Str::startsWith($path, 'storage/')) {
            $path = Str::after($path, 'storage/');
        }

        return $path;
    }

    private function isExternalUrl(string $value): bool
    {
        return (bool) filter_var($value, FILTER_VALIDATE_URL) && Str::startsWith($value, ['http://', 'https://']);
    }

    private function filled(mixed $value): bool
    {
        if (is_array($value)) {
            return count(array_filter($value)) > 0;
        }

        return trim((string) $value) !== '';
    }

    private function issue(array &$issues, string $code, string $severity, string $message): int
    {
        $issues[] = compact('code', 'severity', 'message');

        return 0;
    }
}
