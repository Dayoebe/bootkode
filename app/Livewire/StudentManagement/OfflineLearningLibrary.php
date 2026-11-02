<?php

namespace App\Livewire\StudentManagement;

use App\Models\Learning\DownloadableContent;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.dashboard', ['title' => 'Offline Learning', 'description' => 'Manage downloaded course packs and sync progress', 'icon' => 'fas fa-mobile-screen-button', 'active' => 'student.offline-learning'])]
class OfflineLearningLibrary extends Component
{
    use WithPagination;

    public function render()
    {
        return view('livewire.student-management.offline-learning-library', [
            'packs' => DownloadableContent::query()
                ->select(['id', 'course_id', 'status', 'size_mb', 'storage_limit_mb', 'storage_bytes', 'downloaded_at', 'last_synced_at',
                    'manifest->course->lesson_count as offline_lesson_count'])
                ->with(['course' => fn ($query) => $query->select('id', 'title', 'slug')->withCount('allLessons')])
                ->where('user_id', Auth::id())
                ->latest('downloaded_at')
                ->paginate(10),
        ]);
    }
}
