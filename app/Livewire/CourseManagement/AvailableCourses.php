<?php

namespace App\Livewire\CourseManagement;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseEnrollment;
use App\Notifications\CourseUpdateNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;

#[Layout('layouts.dashboard', ['title' => 'Available Courses', 'description' => 'Browse and enroll in available courses', 'icon' => 'fas fa-book-open', 'active' => 'courses.available'])]
class AvailableCourses extends Component
{
    use WithPagination;

    public string $search = '';
    public string $categoryFilter = '';
    public string $difficultyFilter = '';
    public string $sortBy = 'latest';
    public bool $showOnlyFree = false;
    public bool $showOnlyWithCertificate = false;
    protected int $perPage = 9;

    // Enrollment states
    public array $enrollingCourseIds = [];
    public array $droppingCourseIds = [];
    public array $wishlistingCourseIds = [];

    // Statistics
    public int $totalAvailable = 0;
    public int $totalEnrolled = 0;
    public int $totalCompleted = 0;

    public function mount()
    {
        $this->updateStatistics();
    }

    public function updateStatistics()
    {
        $user = Auth::user();
        
        $this->totalAvailable = Course::where('is_published', true)
            ->where('is_approved', true)
            ->count();
            
        $this->totalEnrolled = $user->enrollments()->count();
        
        $this->totalCompleted = $user->enrollments()
            ->where('is_completed', true)
            ->count();
    }

    /**
     * Enroll user in a course with enhanced UX
     */
    public function enroll(int $courseId): void
    {
        $user = Auth::user();
        $this->enrollingCourseIds[] = $courseId;

        try {
            // Validate course exists and is available
            $course = Course::where('id', $courseId)
                ->where('is_published', true)
                ->where('is_approved', true)
                ->firstOrFail();

            // Check if already enrolled
            if (CourseEnrollment::where('course_id', $courseId)->where('user_id', $user->id)->exists()) {
                $this->dispatch('notify', [
                    'message' => 'You are already enrolled in this course!', 
                    'type' => 'info',
                    'icon' => 'fas fa-info-circle'
                ]);
                return;
            }

            // Create enrollment
            DB::transaction(function () use ($course, $user) {
                CourseEnrollment::create([
                    'course_id' => $course->id,
                    'user_id' => $user->id,
                    'enrolled_at' => now(),
                    'progress_percentage' => 0,
                    'is_completed' => false
                ]);

                // Log activity
                $user->logCustomActivity('Enrolled in course: ' . $course->title, [
                    'course_id' => $course->id,
                    'course_title' => $course->title
                ]);
            });

            // Send notification
            $user->notify(new CourseUpdateNotification($course));
            
            $this->updateStatistics();
            $this->dispatch('enrollment-updated');
            
            $this->dispatch('notify', [
                'message' => "Successfully enrolled in '{$course->title}'! Welcome aboard!", 
                'type' => 'success',
                'icon' => 'fas fa-graduation-cap',
                'action' => [
                    'label' => 'Start Learning',
                    'url' => route('courses.preview', $course->slug)
                ]
            ]);

            // Add confetti effect for first enrollment
            if ($this->totalEnrolled === 0) {
                $this->dispatch('confetti');
            }

        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'message' => 'Enrollment failed: ' . $e->getMessage(), 
                'type' => 'error',
                'icon' => 'fas fa-exclamation-triangle'
            ]);
        } finally {
            $this->enrollingCourseIds = array_diff($this->enrollingCourseIds, [$courseId]);
        }
    }

    /**
     * Drop/unenroll from a course
     */
    public function dropCourse(int $courseId): void
    {
        $user = Auth::user();
        $this->droppingCourseIds[] = $courseId;

        try {
            $enrollment = CourseEnrollment::where('course_id', $courseId)
                ->where('user_id', $user->id)
                ->firstOrFail();

            $course = $enrollment->course;

            DB::transaction(function () use ($enrollment, $user, $course) {
                // Store progress before deletion for potential re-enrollment
                Cache::put("user_{$user->id}_course_{$course->id}_progress", [
                    'progress_percentage' => $enrollment->progress_percentage,
                    'dropped_at' => now()
                ], now()->addMonths(6));

                $enrollment->delete();

                $user->logCustomActivity('Dropped course: ' . $course->title, [
                    'course_id' => $course->id,
                    'course_title' => $course->title,
                    'progress_lost' => $enrollment->progress_percentage
                ]);
            });

            $this->updateStatistics();
            $this->dispatch('enrollment-updated');
            
            $this->dispatch('notify', [
                'message' => "You have been unenrolled from '{$course->title}'. Your progress has been saved for 6 months.",
                'type' => 'warning',
                'icon' => 'fas fa-sign-out-alt',
                'action' => [
                    'label' => 'Undo',
                    'method' => 'enroll',
                    'params' => [$courseId]
                ]
            ]);

        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'message' => 'Failed to drop course: ' . $e->getMessage(),
                'type' => 'error',
                'icon' => 'fas fa-exclamation-triangle'
            ]);
        } finally {
            $this->droppingCourseIds = array_diff($this->droppingCourseIds, [$courseId]);
        }
    }

    /**
     * Toggle wishlist
     */
    public function toggleWishlist(int $courseId): void
    {
        $user = Auth::user();
        $this->wishlistingCourseIds[] = $courseId;

        try {
            $wishlist = $user->wishlists()->where('course_id', $courseId)->first();

            if ($wishlist) {
                $wishlist->delete();
                $message = 'Removed from wishlist';
                $icon = 'fas fa-heart-broken';
            } else {
                $user->wishlists()->create(['course_id' => $courseId]);
                $message = 'Added to wishlist';
                $icon = 'fas fa-heart';
            }

            $this->dispatch('notify', [
                'message' => $message,
                'type' => 'info',
                'icon' => $icon
            ]);

        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'message' => 'Wishlist action failed',
                'type' => 'error',
                'icon' => 'fas fa-exclamation-triangle'
            ]);
        } finally {
            $this->wishlistingCourseIds = array_diff($this->wishlistingCourseIds, [$courseId]);
        }
    }

    /**
     * Check if user is enrolled in course
     */
    public function isEnrolled(int $courseId): bool
    {
        return Auth::user()->enrollments()->where('course_id', $courseId)->exists();
    }

    /**
     * Check if course is in wishlist
     */
    public function isWishlisted(int $courseId): bool
    {
        return Auth::user()->wishlists()->where('course_id', $courseId)->exists();
    }

    /**
     * Get user's progress for a course
     */
    public function getCourseProgress(int $courseId): int
    {
        $enrollment = Auth::user()->enrollments()->where('course_id', $courseId)->first();
        return $enrollment ? $enrollment->progress_percentage : 0;
    }

    /**
     * Reset all filters
     */
    public function resetFilters(): void
    {
        $this->search = '';
        $this->categoryFilter = '';
        $this->difficultyFilter = '';
        $this->sortBy = 'latest';
        $this->showOnlyFree = false;
        $this->showOnlyWithCertificate = false;
        $this->resetPage();
    }

    /**
     * Update pagination when filters change
     */
    public function updating($key): void
    {
        if (in_array($key, ['search', 'categoryFilter', 'difficultyFilter', 'sortBy', 'showOnlyFree', 'showOnlyWithCertificate'])) {
            $this->resetPage();
        }
    }

    /**
     * Listen for enrollment updates
     */
    #[On('enrollment-updated')]
    public function refreshEnrollments(): void
    {
        $this->updateStatistics();
    }

    /**
     * Get cached categories
     */
    #[Computed]
    public function categories()
    {
        return Cache::remember('course_categories_active', 3600, function () {
            return CourseCategory::whereHas('courses', function ($query) {
                $query->where('is_published', true)->where('is_approved', true);
            })->orderBy('name')->get();
        });
    }

    public function render()
    {
        $courses = Course::where('is_published', true)
            ->where('is_approved', true)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%')
                      ->orWhereHas('instructor', function ($subq) {
                          $subq->where('name', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->when($this->categoryFilter, fn($query) => $query->where('category_id', $this->categoryFilter))
            ->when($this->difficultyFilter, fn($query) => $query->where('difficulty_level', $this->difficultyFilter))
            ->when($this->showOnlyFree, fn($query) => $query->where('is_free', true))
            ->when($this->showOnlyWithCertificate, fn($query) => $query->whereNotNull('certificate_template'))
            ->with(['category', 'instructor', 'enrollments' => function ($query) {
                $query->where('user_id', Auth::id());
            }])
            ->withAvg('reviews as average_rating', 'rating')
            ->withCount(['reviews as rating_count', 'enrollments as total_enrollments'])
            ->when($this->sortBy === 'latest', fn($query) => $query->latest())
            ->when($this->sortBy === 'popular', fn($query) => $query->orderBy('total_enrollments', 'desc'))
            ->when($this->sortBy === 'rating', fn($query) => $query->orderBy('average_rating', 'desc'))
            ->when($this->sortBy === 'title', fn($query) => $query->orderBy('title'))
            ->select([
                'id', 'title', 'description', 'thumbnail', 'difficulty_level', 
                'category_id', 'instructor_id', 'slug', 'is_free', 'price',
                'certificate_template', 'estimated_duration_minutes'
            ])
            ->paginate($this->perPage);

        return view('livewire.course-management.available-courses', [
            'courses' => $courses,
            'categories' => $this->categories,
            'totalAvailable' => $this->totalAvailable,
            'totalEnrolled' => $this->totalEnrolled,
            'totalCompleted' => $this->totalCompleted
        ]);
    }
}