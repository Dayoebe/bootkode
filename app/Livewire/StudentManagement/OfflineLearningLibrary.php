<?php

namespace App\Livewire\StudentManagement;

use App\Models\Learning\DownloadableContent;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dashboard', ['title' => 'Offline Learning', 'description' => 'Manage downloaded course packs and sync progress', 'icon' => 'fas fa-mobile-screen-button', 'active' => 'student.offline-learning'])]
class OfflineLearningLibrary extends Component
{
    public function render()
    {
        return view('livewire.student-management.offline-learning-library', [
            'packs' => DownloadableContent::query()
                ->with(['course.sections.lessons'])
                ->where('user_id', Auth::id())
                ->latest('downloaded_at')
                ->get(),
        ]);
    }
}
