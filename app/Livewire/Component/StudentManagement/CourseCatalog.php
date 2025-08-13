<?php

namespace App\Livewire\Component\StudentManagement;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;

#[Layout('layouts.dashboard', ['title' => 'Course Catalog', 'description' => 'Browse and discover courses available for enrollment', 'icon' => 'fas fa-book-open', 'active' => 'student.course-catalog'])]

class CourseCatalog extends Component
{
    use WithPagination;

    public $search = '';
    public $categoryFilter = '';
    public $difficultyFilter = '';
    public $durationFilter = '';
    public $priceFilter = '';
    public $ratingFilter = '';
    public $sortBy = 'newest';
    public $perPage = 12;
    public $previewCourse = null;

    protected $listeners = ['closePreview'];

    public function resetFilters()
    {
        $this->reset([
            'search', 
            'categoryFilter',
            'difficultyFilter',
            'durationFilter',
            'priceFilter',
            'ratingFilter',
            'sortBy'
        ]);
        $this->resetPage();
    }

    public function toggleWishlist($courseId)
    {
        if (!Auth::check()) {
            return $this->redirect(route('login'));
        }

        $wishlistItem = Wishlist::firstOrNew([
            'user_id' => Auth::id(),
            'course_id' => $courseId
        ]);

        if ($wishlistItem->exists) {
            $wishlistItem->delete();
            $this->dispatch('notify', 'Removed from wishlist', 'info');
        } else {
            $wishlistItem->save();
            $this->dispatch('notify', 'Added to wishlist!');
        }
    }

    public function showPreview($courseId)
    {
        $this->previewCourse = Course::with(['instructor', 'category', 'sections.lessons'])
            ->withAvg('reviews', 'rating')
            ->withCount('enrollments')
            ->findOrFail($courseId);
    }

    public function closePreview()
    {
        $this->reset('previewCourse');
    }

    public function enroll($courseId)
    {
        $user = Auth::user();
        
        if (!$user->courses()->where('course_id', $courseId)->exists()) {
            $user->courses()->attach($courseId, ['progress' => 0]);
            $this->dispatch('notify', 'Successfully enrolled in course!');
            return $this->redirect(route('student.enrolled-courses'), navigate: true);
        }
        
        $this->dispatch('notify', 'You are already enrolled in this course!', 'info');
    }

    public function getCourses()
    {
        return Course::query()
            ->where('is_published', true)
            ->where('is_approved', true)
            ->with(['category', 'instructor'])
            ->withCount(['sections', 'enrollments as enrollments_count'])
            ->withAvg('reviews as reviews_avg_rating', 'rating')
            ->when($this->search, fn($q) => $q->where('title', 'like', "%{$this->search}%"))
            ->when($this->categoryFilter, fn($q) => $q->where('category_id', $this->categoryFilter))
            ->when($this->difficultyFilter, fn($q) => $q->where('difficulty_level', $this->difficultyFilter))
            ->when($this->durationFilter === 'short', fn($q) => $q->where('estimated_duration_minutes', '<', 60))
            ->when($this->durationFilter === 'medium', fn($q) => $q->whereBetween('estimated_duration_minutes', [60, 180]))
            ->when($this->durationFilter === 'long', fn($q) => $q->where('estimated_duration_minutes', '>', 180))
            ->when($this->priceFilter === 'free', fn($q) => $q->where('is_premium', false))
            ->when($this->priceFilter === 'premium', fn($q) => $q->where('is_premium', true))
            ->when($this->ratingFilter, fn($q) => $q->having('reviews_avg_rating', '>=', $this->ratingFilter))
            ->when($this->sortBy === 'newest', fn($q) => $q->latest())
            ->when($this->sortBy === 'popular', fn($q) => $q->orderBy('enrollments_count', 'desc'))
            ->when($this->sortBy === 'rating', fn($q) => $q->orderBy('reviews_avg_rating', 'desc'))
            ->when($this->sortBy === 'duration', fn($q) => $q->orderBy('estimated_duration_minutes', 'desc'))
            ->paginate($this->perPage);
    }

    public function render()
    {
        return view('livewire.component.student-management.course-catalog', [
            'courses' => $this->getCourses(),
            'categories' => CourseCategory::all(),
            'enrolledCourseIds' => Auth::check() ? Auth::user()->courses()->pluck('courses.id')->toArray() : [],
            'wishlistCourseIds' => Auth::check() ? Auth::user()->wishlists()->pluck('course_id')->toArray() : []
        ]);
    }
}