<?php

namespace App\Livewire\CourseManagement;

use App\Models\Learning\Course;
use App\Services\CourseQualityService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.dashboard', [
    'title' => 'Course Quality Control',
    'description' => 'Editorial QA for course completeness, media health, assessment coverage, and review dates',
    'icon' => 'fas fa-clipboard-check',
    'active' => 'course-quality.control',
])]
class CourseQualityControl extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = 'all';
    public string $sortBy = 'lowest_score';
    public ?int $expandedCourseId = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => 'all'],
    ];

    public function mount(): void
    {
        abort_unless(Auth::user()?->hasAnyRole(['super_admin', 'academy_admin', 'content_editor']), 403);
    }

    public function updating($property): void
    {
        if (in_array($property, ['search', 'statusFilter', 'sortBy'], true)) {
            $this->resetPage();
        }
    }

    public function toggleDetails(int $courseId): void
    {
        $this->expandedCourseId = $this->expandedCourseId === $courseId ? null : $courseId;
    }

    public function runQualityCheck(int $courseId, bool $checkRemoteMedia = false): void
    {
        $course = Course::findOrFail($courseId);
        app(CourseQualityService::class)->scanAndPersist($course, Auth::user(), $checkRemoteMedia);

        Auth::user()?->logCustomActivity('Ran course quality check', [
            'course_id' => $courseId,
            'remote_media_checked' => $checkRemoteMedia,
        ]);

        $this->dispatch('notify', $checkRemoteMedia ? 'Full media check completed.' : 'Course QA check completed.', 'success');
    }

    public function markReviewed(int $courseId): void
    {
        $course = Course::findOrFail($courseId);
        app(CourseQualityService::class)->markReviewed($course, Auth::user());

        Auth::user()?->logCustomActivity('Marked course editorial review complete', ['course_id' => $courseId]);
        $this->dispatch('notify', 'Review date updated for the next 6 months.', 'success');
    }

    public function togglePublicLabel(int $courseId): void
    {
        $course = Course::findOrFail($courseId);
        $course->update([
            'quality_public_label_enabled' => ! $course->quality_public_label_enabled,
        ]);

        $this->dispatch('notify', 'Public quality label visibility updated.', 'success');
    }

    public function approveIfReady(int $courseId): void
    {
        abort_unless(Auth::user()?->hasAnyRole(['super_admin', 'academy_admin']), 403);

        $course = Course::findOrFail($courseId);

        if (! $course->quality_last_checked_at) {
            app(CourseQualityService::class)->scanAndPersist($course, Auth::user(), false);
            $course->refresh();
        }

        if (! $course->quality_review_due_at) {
            app(CourseQualityService::class)->markReviewed($course, Auth::user());
            $course->refresh();
        }

        if (! $course->quality_approval_ready) {
            $this->dispatch('notify', 'Course still needs QA fixes before approval.', 'error');
            $this->expandedCourseId = $courseId;
            return;
        }

        $course->update([
            'is_approved' => true,
            'is_published' => true,
            'published_at' => $course->published_at ?: now(),
        ]);

        Auth::user()?->logCustomActivity('Approved course through quality control', ['course_id' => $courseId]);
        $this->dispatch('notify', 'Course approved and published.', 'success');
    }

    public function render()
    {
        $baseQuery = Course::query()
            ->with(['instructor:id,name,email', 'category:id,name', 'latestQualityCheck'])
            ->withCount(['sections', 'allLessons as lessons_count', 'directAssessments as direct_assessments_count'])
            ->when($this->search !== '', function ($query) {
                $query->where(function ($nested) {
                    $nested->where('title', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%')
                        ->orWhereHas('instructor', fn ($instructor) => $instructor->where('name', 'like', '%' . $this->search . '%'));
                });
            })
            ->when($this->statusFilter === 'pending', fn ($query) => $query->where('is_approved', false))
            ->when($this->statusFilter === 'not_checked', fn ($query) => $query->where(function ($nested) {
                $nested->whereNull('quality_last_checked_at')->orWhere('quality_status', CourseQualityService::STATUS_NOT_CHECKED);
            }))
            ->when($this->statusFilter === 'needs_work', fn ($query) => $query->where('quality_status', CourseQualityService::STATUS_NEEDS_WORK))
            ->when($this->statusFilter === 'ready', fn ($query) => $query->whereIn('quality_status', [CourseQualityService::STATUS_READY, CourseQualityService::STATUS_VERIFIED]))
            ->when($this->statusFilter === 'stale', fn ($query) => $query->where(function ($nested) {
                $nested->where('quality_status', CourseQualityService::STATUS_STALE)
                    ->orWhere('quality_review_due_at', '<', now());
            }));

        $courses = (clone $baseQuery)
            ->when($this->sortBy === 'lowest_score', fn ($query) => $query->orderBy('quality_score')->oldest('quality_last_checked_at'))
            ->when($this->sortBy === 'highest_score', fn ($query) => $query->orderByDesc('quality_score'))
            ->when($this->sortBy === 'review_due', fn ($query) => $query->orderByRaw('quality_review_due_at IS NULL DESC')->orderBy('quality_review_due_at'))
            ->when($this->sortBy === 'newest', fn ($query) => $query->latest())
            ->paginate(10);

        return view('livewire.course-management.course-quality-control', [
            'courses' => $courses,
            'stats' => [
                'total' => Course::count(),
                'not_checked' => Course::whereNull('quality_last_checked_at')->orWhere('quality_status', CourseQualityService::STATUS_NOT_CHECKED)->count(),
                'needs_work' => Course::where('quality_status', CourseQualityService::STATUS_NEEDS_WORK)->count(),
                'ready' => Course::whereIn('quality_status', [CourseQualityService::STATUS_READY, CourseQualityService::STATUS_VERIFIED])->count(),
                'stale' => Course::where('quality_status', CourseQualityService::STATUS_STALE)->orWhere('quality_review_due_at', '<', now())->count(),
            ],
        ]);
    }
}
