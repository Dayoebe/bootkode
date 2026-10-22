<?php

namespace App\Services;

use App\Models\Learning\Course;
use App\Models\Learning\CourseCategory;
use App\Models\Learning\Lesson;
use Illuminate\Database\Eloquent\Builder;

class PublicCatalogService
{
    private ?array $statistics = null;

    public function courses(): Builder
    {
        return Course::publiclyAvailable()->with('category')->withCount('allLessons');
    }

    public function statistics(): array
    {
        return $this->statistics ??= [
            'courses' => Course::publiclyAvailable()->count(),
            'lessons' => Lesson::whereHas('section.course', fn ($q) => $q->publiclyAvailable())->count(),
            'tracks' => CourseCategory::whereHas('courses', fn ($q) => $q->publiclyAvailable())->count(),
        ];
    }
}
