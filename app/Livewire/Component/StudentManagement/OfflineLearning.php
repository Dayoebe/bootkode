<?php

namespace App\Livewire\Component\StudentManagement;

use App\Models\Course;
use App\Models\DownloadableContent;
use App\Models\Lesson;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.dashboard', ['title' => 'Offline Learning', 'description' => 'Manage your saved resources including lessons, notes, PDFs, and videos', 'icon' => 'fas fa-bookmark', 'active' => 'student.saved-resources'])]  

class OfflineLearning extends Component
{
    use WithPagination, WithFileUploads;

    public $selectedCourse;
    public $downloadProgress = 0;
    public $isDownloading = false;
    public $availableSpace;
    public $storageUsage;
    public $newNote = '';
    public $activeTab = 'downloaded'; // downloaded, available, notes
    public $search = '';
    public $selectedTypes = ['lesson', 'pdf', 'audio'];
    public $noteContent = '';
    // public $offlineNotes = [];
    

    protected $listeners = [
        'updateDownloadProgress' => 'updateDownloadProgress',
        'startDownload' => 'simulateDownload',
    ];

    protected $rules = [
        'newNote' => 'required|string|max:2000',
    ];

    protected $messages = [
        'newNote.required' => 'Please enter a note before saving.',
        'newNote.max' => 'Note cannot exceed 2000 characters.',
    ];
    protected $queryString = [
        'search' => ['except' => ''],
        'activeTab' => ['except' => 'downloaded'],
        'selectedTypes' => ['except' => ['lesson', 'pdf', 'audio']],
    ];

    public function mount()
    {
        $this->calculateStorage();
        $this->offlineNotes = [];
    }

    public function calculateStorage()
    {
        $user = Auth::user();
        $this->storageUsage = $user->downloadedContent()->sum('size_mb');
        $this->availableSpace = max(0, config('app.offline_storage_limit_mb', 500) - $this->storageUsage);
    }

    public function downloadCourseContent($courseId)
    {
        $course = Course::findOrFail($courseId);
        
        $this->validate([
            'availableSpace' => ['required', 'numeric', 'min:'.$course->offline_size_mb],
        ], [
            'availableSpace.min' => 'You need at least '.$course->offline_size_mb.'MB of free space to download this content.',
        ]);

        $this->isDownloading = true;
        $this->selectedCourse = $courseId;
        $this->downloadProgress = 0;

        // Simulate download progress (replace with actual job in production)
        $this->simulateDownload($course);
    }

    protected function simulateDownload(Course $course)
    {
        // This would be replaced with an actual job in production
        // Using Livewire's polling to simulate progress
        $this->dispatch('start-download', courseId: $course->id);
    }

    // Called from frontend via Livewire events
    public function updateDownloadProgress($progress)
    {
        $this->downloadProgress = $progress;
        
        if ($progress >= 100) {
            $this->completeDownload(Course::find($this->selectedCourse));
        }
    }

    protected function completeDownload(Course $course)
    {
        DownloadableContent::updateOrCreate(
            ['user_id' => Auth::id(), 'course_id' => $course->id],
            [
                'downloaded_at' => now(),
                'size_mb' => $course->offline_size_mb,
                'content_types' => $course->offline_content_types
            ]
        );

        $this->isDownloading = false;
        $this->downloadProgress = 0;
        $this->calculateStorage();
        $this->dispatch('notify', type: 'success', message: 'Course downloaded for offline access!');
    }

    public function deleteDownloadedContent($contentId)
    {
        $content = DownloadableContent::findOrFail($contentId);
        
        Storage::deleteDirectory("offline-content/user_{$content->user_id}/course_{$content->course_id}");
        
        Auth::user()->decrement('offline_content_size_mb', $content->size_mb);
        $content->delete();
        
        $this->calculateStorage();
        $this->dispatch('notify', type: 'success', message: 'Content removed from offline storage');
    }

    public function render()
    {
        $user = Auth::user();
        
        $downloadedContent = $user->downloadedContent()
            ->with('course')
            ->when($this->search, function($query) {
                $query->whereHas('course', function($q) {
                    $q->where('title', 'like', '%'.$this->search.'%');
                });
            })
            ->latest()
            ->paginate(6);

        // Remove has_offline_content filter or add the column to your database
        $availableCourses = $user->courses()
            ->whereNotIn('id', $user->downloadedContent()->pluck('course_id'))
            //->where('has_offline_content', true)  // Remove or implement this column
            ->when($this->search, function($query) {
                $query->where('title', 'like', '%'.$this->search.'%');
            })
            ->paginate(6);

        $offlineNotes = $user->offlineNotes()
            ->when($this->selectedCourse, function($query) {
                $query->where('course_id', $this->selectedCourse);
            })
            ->latest()
            ->paginate(5);

        return view('livewire.component.student-management.offline-learning', [
            'downloadedContent' => $downloadedContent,
            'availableCourses' => $availableCourses,
            'offlineNotes' => $offlineNotes,
            'enrolledCourses' => $user->courses()->get(),
        ]);
    }
















    // protected $queryString = [
    //     'search' => ['except' => ''],
    //     'activeTab' => ['except' => 'downloaded'],
    //     'selectedTypes' => ['except' => ['lesson', 'pdf', 'audio']],
    // ];

    // public function mount()
    // {
    //     $this->calculateStorage();
    // }

    // public function calculateStorage()
    // {
    //     $user = Auth::user();
    //     $this->storageUsage = $user->offline_content_size_mb ?? 0;
    //     $this->availableSpace = max(0, config('app.offline_storage_limit_mb', 500) - $this->storageUsage);
    // }

    // public function downloadCourseContent($courseId)
    // {
    //     $this->validate([
    //         'availableSpace' => 'required|numeric|min:50',
    //     ], [
    //         'availableSpace.min' => 'You need at least 50MB of free space to download course content.',
    //     ]);

    //     $this->isDownloading = true;
    //     $this->downloadProgress = 0;

    //     // Simulate download progress (in a real app, this would be a job)
    //     $interval = setInterval(function() {
    //         $this->downloadProgress += 10;
    //         if ($this->downloadProgress >= 100) {
    //             $this->isDownloading = false;
    //             $this->downloadProgress = 0;
                
    //             // Save downloaded content reference
    //             DownloadableContent::create([
    //                 'user_id' => Auth::id(),
    //                 'course_id' => $courseId,
    //                 'downloaded_at' => now(),
    //                 'size_mb' => Course::find($courseId)->offline_size_mb,
    //             ]);

    //             // Update user storage
    //             Auth::user()->increment('offline_content_size_mb', Course::find($courseId)->offline_size_mb);
    //             $this->calculateStorage();

    //             $this->dispatch('notify', type: 'success', message: 'Course downloaded for offline access!');
    //         }
    //     }, 500);
    // }


    public function saveNote()
    {
        $this->validate([
            'newNote' => 'required|string|max:2000',
        ]);

        Auth::user()->offlineNotes()->create([
            'content' => $this->newNote,
            'course_id' => $this->selectedCourse,
        ]);

        $this->newNote = '';
        $this->dispatch('notify', type: 'success', message: 'Note saved for offline reference');
    }

    public function deleteNote($noteId)
    {
        Auth::user()->offlineNotes()->findOrFail($noteId)->delete();
        $this->dispatch('notify', type: 'success', message: 'Note deleted');
    }

    // public function render()
    // {
    //     $user = Auth::user();
        
    //     $downloadedContent = $user->downloadedContent()
    //         ->with('course')
    //         ->when($this->search, function($query) {
    //             $query->whereHas('course', function($q) {
    //                 $q->where('title', 'like', '%'.$this->search.'%');
    //             });
    //         })
    //         ->latest()
    //         ->paginate(6);

    //     $availableCourses = $user->courses()
    //         ->whereNotIn('id', $user->downloadedContent()->pluck('course_id'))
    //         ->where('has_offline_content', true)
    //         ->when($this->search, function($query) {
    //             $query->where('title', 'like', '%'.$this->search.'%');
    //         })
    //         ->paginate(6);

    //     $offlineNotes = $user->offlineNotes()
    //         ->when($this->selectedCourse, function($query) {
    //             $query->where('course_id', $this->selectedCourse);
    //         })
    //         ->latest()
    //         ->paginate(5);

    //     return view('livewire.component.student-management.offline-learning', [
    //         'downloadedContent' => $downloadedContent,
    //         'availableCourses' => $availableCourses,
    //         'offlineNotes' => $offlineNotes,
    //         'enrolledCourses' => $user->courses()->get(),
    //     ]);
    // }
}