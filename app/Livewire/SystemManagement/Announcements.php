<?php

namespace App\Livewire\SystemManagement;

use App\Models\Announcement;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.dashboard', [
    'title' => 'Announcements',
    'description' => 'View platform and course announcements',
    'icon' => 'fas fa-bullhorn',
    'active' => 'announcements'
])]
class Announcements extends Component
{
    use WithPagination;

    public $activeTab = 'all';
    public $search = '';
    public $courseFilter = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'courseFilter' => ['except' => ''],
    ];

    public function render()
    {
        $userCourses = Auth::user()->enrolledCourses()->pluck('courses.id')->toArray();

        $announcements = Announcement::where('status', 'published')
            ->when($this->activeTab === 'my_courses', function ($query) use ($userCourses) {
                $query->whereIn('course_id', $userCourses)->orWhereNull('course_id');
            })
            ->when($this->search, function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('content', 'like', '%' . $this->search . '%');
            })
            ->when($this->courseFilter, function ($query) {
                $query->where('course_id', $this->courseFilter);
            })
            ->with(['user', 'course'])
            ->orderBy('published_at', 'desc')
            ->paginate(10);

        return view('livewire.system-management.announcements', [
            'announcements' => $announcements,
            'courses' => Course::where('is_published', true)->where('is_approved', true)->get(),
        ]);
    }
}