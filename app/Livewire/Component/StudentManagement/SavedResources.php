<?php

namespace App\Livewire\Component\StudentManagement;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\SavedResource;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.dashboard', ['title' => 'Saved Resources', 'description' => 'Manage your saved resources including lessons, notes, PDFs, and videos', 'icon' => 'fas fa-bookmark', 'active' => 'student.saved-resources'])]

class SavedResources extends Component
{
    use WithPagination;

    public $search = '';
    public $filter = 'all'; // all, lessons, notes, pdfs, videos
    public $sort = 'newest'; // newest, oldest, course
    public $groupByCourse = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'filter' => ['except' => 'all'],
        'sort' => ['except' => 'newest'],
    ];

    public function render()
    {
        $user = Auth::user();
        
        $query = $user->savedResources()
            ->with(['resourceable', 'course'])
            ->when($this->search, function ($query) {
                $query->whereHasMorph('resourceable', [Lesson::class], function ($query) {
                    $query->where('title', 'like', '%'.$this->search.'%')
                          ->orWhere('description', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->filter !== 'all', function ($query) {
                $query->where('type', $this->filter);
            });

        switch ($this->sort) {
            case 'oldest':
                $query->orderBy('created_at');
                break;
            case 'course':
                $query->orderBy('course_id');
                break;
            default:
                $query->orderByDesc('created_at');
        }

        if ($this->groupByCourse) {
            $savedResources = $query->get()->groupBy('course_id');
            
            $courses = Course::whereIn('id', $savedResources->keys())
                ->with('instructor')
                ->get()
                ->keyBy('id');
                
            return view('livewire.component.student-management.saved-resources', [
                'groupedResources' => $savedResources,
                'courses' => $courses,
                'groupByCourse' => true
            ]);
        }

        $savedResources = $query->paginate(12);

        return view('livewire.component.student-management.saved-resources', [
            'savedResources' => $savedResources,
            'groupByCourse' => false
        ]);
    }

    public function toggleBookmark($resourceableType, $resourceableId)
    {
        $user = Auth::user();
        
        // Check if already bookmarked
        $existing = $user->savedResources()
            ->where('resourceable_type', $resourceableType)
            ->where('resourceable_id', $resourceableId)
            ->first();

        if ($existing) {
            $existing->delete();
            $this->dispatch('notify', type: 'success', message: 'Resource removed from saved items');
        } else {
            $user->savedResources()->create([
                'resourceable_type' => $resourceableType,
                'resourceable_id' => $resourceableId,
                'type' => $this->determineResourceType($resourceableType)
            ]);
            $this->dispatch('notify', type: 'success', message: 'Resource saved for later');
        }
    }

    public function removeBookmark($bookmarkId)
    {
        Auth::user()->savedResources()->where('id', $bookmarkId)->delete();
        $this->dispatch('notify', type: 'success', message: 'Resource removed from saved items');
    }

    protected function determineResourceType($resourceableType)
    {
        // You can expand this based on your resource types
        return match($resourceableType) {
            Lesson::class => 'lesson',
            default => 'other'
        };
    }
}