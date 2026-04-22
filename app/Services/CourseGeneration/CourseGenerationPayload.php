<?php

namespace App\Services\CourseGeneration;

use Illuminate\Support\Str;

class CourseGenerationPayload
{
    public function __construct(
        protected array $attributes
    ) {
    }

    public static function fromArray(array $attributes): self
    {
        return new self([
            'topic' => self::cleanString($attributes['topic'] ?? ''),
            'title' => self::nullableString($attributes['title'] ?? null),
            'subtitle' => self::nullableString($attributes['subtitle'] ?? null),
            'slug' => self::nullableString($attributes['slug'] ?? null),
            'category' => self::normalizeReference(
                $attributes['category']
                    ?? $attributes['category_id']
                    ?? $attributes['category_slug']
                    ?? $attributes['category_name']
                    ?? null
            ),
            'instructor' => self::normalizeReference(
                $attributes['instructor']
                    ?? $attributes['instructor_id']
                    ?? $attributes['instructor_email']
                    ?? null
            ),
            'target_audience' => self::nullableString($attributes['target_audience'] ?? null),
            'skill_level' => self::cleanString($attributes['skill_level'] ?? $attributes['difficulty_level'] ?? 'beginner'),
            'course_goals' => self::normalizeStringList($attributes['course_goals'] ?? $attributes['goals'] ?? []),
            'section_count' => (int) ($attributes['section_count'] ?? $attributes['number_of_sections'] ?? 5),
            'lessons_per_section' => (int) ($attributes['lessons_per_section'] ?? 4),
            'video_lessons_per_section' => max(0, (int) ($attributes['video_lessons_per_section'] ?? 1)),
            'include_faqs' => (bool) ($attributes['include_faqs'] ?? true),
            'include_resources' => (bool) ($attributes['include_resources'] ?? true),
            'include_quizzes' => (bool) ($attributes['include_quizzes'] ?? false),
            'create_category_if_missing' => (bool) ($attributes['create_category_if_missing'] ?? true),
            'duplicate_strategy' => self::cleanString($attributes['duplicate_strategy'] ?? 'return_existing'),
            'publish' => (bool) ($attributes['publish'] ?? false),
            'approve' => (bool) ($attributes['approve'] ?? false),
            'scheduled_publish_at' => self::nullableString($attributes['scheduled_publish_at'] ?? null),
            'price' => (float) ($attributes['price'] ?? 0),
            'currency' => strtoupper(self::cleanString($attributes['currency'] ?? 'NGN')),
            'is_premium' => (bool) ($attributes['is_premium'] ?? false),
            'completion_rate_threshold' => (int) ($attributes['completion_rate_threshold'] ?? 80),
            'estimated_duration_minutes' => isset($attributes['estimated_duration_minutes'])
                ? (int) $attributes['estimated_duration_minutes']
                : null,
            'learning_outcomes' => self::normalizeStringList($attributes['learning_outcomes'] ?? []),
            'prerequisites' => self::normalizeStringList($attributes['prerequisites'] ?? []),
            'materials_included' => self::normalizeStringList($attributes['materials_included'] ?? []),
            'tags' => self::normalizeStringList($attributes['tags'] ?? []),
            'syllabus_overview' => self::nullableString($attributes['syllabus_overview'] ?? null),
            'faqs' => self::normalizeFaqs($attributes['faqs'] ?? []),
            'video_references' => self::normalizeLinks($attributes['video_references'] ?? []),
            'resource_links' => self::normalizeLinks($attributes['resource_links'] ?? []),
            'sections' => self::normalizeSections($attributes['sections'] ?? []),
        ]);
    }

    public function all(): array
    {
        return $this->attributes;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->attributes[$key] ?? $default;
    }

    public function hasCustomSections(): bool
    {
        return !empty($this->attributes['sections']);
    }

    protected static function cleanString(mixed $value): string
    {
        return Str::of(strip_tags((string) $value))->squish()->toString();
    }

    protected static function nullableString(mixed $value): ?string
    {
        $clean = self::cleanString($value ?? '');

        return $clean !== '' ? $clean : null;
    }

    protected static function normalizeStringList(mixed $items): array
    {
        if (!is_array($items)) {
            $items = [$items];
        }

        return array_values(array_filter(array_map(
            fn (mixed $item) => self::nullableString($item),
            $items
        )));
    }

    protected static function normalizeFaqs(mixed $faqs): array
    {
        if (!is_array($faqs)) {
            return [];
        }

        $normalized = [];

        foreach ($faqs as $faq) {
            if (!is_array($faq)) {
                continue;
            }

            $question = self::nullableString($faq['question'] ?? null);
            $answer = self::nullableString($faq['answer'] ?? null);

            if (!$question || !$answer) {
                continue;
            }

            $normalized[] = [
                'question' => $question,
                'answer' => $answer,
            ];
        }

        return $normalized;
    }

    protected static function normalizeReference(mixed $reference): ?array
    {
        if (is_null($reference) || $reference === '') {
            return null;
        }

        if (is_numeric($reference)) {
            return ['id' => (int) $reference];
        }

        if (is_string($reference)) {
            $reference = trim($reference);

            if ($reference === '') {
                return null;
            }

            if (filter_var($reference, FILTER_VALIDATE_EMAIL)) {
                return ['email' => $reference];
            }

            return ['name' => self::cleanString($reference)];
        }

        if (!is_array($reference)) {
            return null;
        }

        $normalized = [];

        foreach (['id', 'slug', 'name', 'email', 'description'] as $key) {
            if (!array_key_exists($key, $reference)) {
                continue;
            }

            $value = $reference[$key];

            if ($key === 'id' && is_numeric($value)) {
                $normalized[$key] = (int) $value;
                continue;
            }

            $clean = self::nullableString($value);
            if ($clean) {
                $normalized[$key] = $clean;
            }
        }

        return $normalized !== [] ? $normalized : null;
    }

    protected static function normalizeLinks(mixed $links): array
    {
        if (!is_array($links)) {
            return [];
        }

        $normalized = [];

        foreach ($links as $link) {
            if (is_string($link)) {
                $url = trim($link);

                if ($url !== '') {
                    $normalized[] = [
                        'title' => null,
                        'url' => $url,
                    ];
                }

                continue;
            }

            if (!is_array($link)) {
                continue;
            }

            $url = self::nullableString($link['url'] ?? null);
            $title = self::nullableString($link['title'] ?? null);

            if (!$url) {
                continue;
            }

            $normalized[] = array_filter([
                'title' => $title,
                'url' => $url,
                'section_title' => self::nullableString($link['section_title'] ?? null),
                'lesson_title' => self::nullableString($link['lesson_title'] ?? null),
            ], fn (mixed $value) => !is_null($value) && $value !== '');
        }

        return $normalized;
    }

    protected static function normalizeSections(mixed $sections): array
    {
        if (!is_array($sections)) {
            return [];
        }

        $normalized = [];

        foreach ($sections as $section) {
            if (!is_array($section)) {
                continue;
            }

            $lessons = [];

            foreach (($section['lessons'] ?? []) as $lesson) {
                if (!is_array($lesson)) {
                    continue;
                }

                $lessons[] = array_filter([
                    'title' => self::nullableString($lesson['title'] ?? null),
                    'description' => self::nullableString($lesson['description'] ?? null),
                    'content' => isset($lesson['content']) ? trim((string) $lesson['content']) : null,
                    'content_type' => self::nullableString($lesson['content_type'] ?? null),
                    'video_url' => self::nullableString($lesson['video_url'] ?? null),
                    'duration_minutes' => isset($lesson['duration_minutes']) ? (int) $lesson['duration_minutes'] : null,
                    'completion_time_type' => self::nullableString($lesson['completion_time_type'] ?? null),
                    'difficulty_level' => self::nullableString($lesson['difficulty_level'] ?? null),
                    'external_links' => self::normalizeLinks($lesson['external_links'] ?? []),
                ], fn (mixed $value) => !is_null($value) && $value !== []);
            }

            $assessment = [];
            if (is_array($section['assessment'] ?? null)) {
                $assessment = array_filter([
                    'title' => self::nullableString($section['assessment']['title'] ?? null),
                    'description' => self::nullableString($section['assessment']['description'] ?? null),
                    'type' => self::nullableString($section['assessment']['type'] ?? null),
                    'pass_percentage' => isset($section['assessment']['pass_percentage']) ? (int) $section['assessment']['pass_percentage'] : null,
                    'estimated_duration_minutes' => isset($section['assessment']['estimated_duration_minutes']) ? (int) $section['assessment']['estimated_duration_minutes'] : null,
                    'questions' => is_array($section['assessment']['questions'] ?? null) ? $section['assessment']['questions'] : [],
                ], fn (mixed $value) => !is_null($value));
            }

            $normalized[] = array_filter([
                'title' => self::nullableString($section['title'] ?? null),
                'description' => self::nullableString($section['description'] ?? null),
                'lessons' => $lessons,
                'assessment' => $assessment,
            ], fn (mixed $value) => !is_null($value));
        }

        return $normalized;
    }
}
