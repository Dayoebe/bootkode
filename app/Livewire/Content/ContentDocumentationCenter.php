<?php

namespace App\Livewire\Content;

use Livewire\Component;
use App\Models\User;
use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\LearningMaterial;
use App\Models\VideoLibrary;
use Illuminate\Support\Facades\Route;
use Livewire\Attributes\Layout;

#[Layout('layouts.dashboard', ['title' => 'Content & Documentation Management', 'description' => 'Manage learning materials, videos, and documentation', 'icon' => 'fas fa-edit', 'active' => 'admin.content'])]

class ContentDocumentationCenter extends Component
{
    public $activeTab = 'learning-materials';
    public $user;

    // Statistics
    public $stats = [
        'total_documents' => 0,
        'total_videos' => 0,
        'total_materials' => 0,
        'pending_reviews' => 0,
    ];

    public function mount()
    {
        $this->user = auth()->user();

        // Set active tab based on route
        $currentRoute = Route::currentRouteName();
        $this->activeTab = match ($currentRoute) {
            'content.learning-materials' => 'learning-materials',
            'content.video-library' => 'video-library',
            'content.documentation' => 'documentation',
            'content.localization' => 'localization',
            'content.moderation' => 'moderation',
            'content.all-documents' => 'all-documents',
            'content.create-document' => 'create-document',
            'content.categories' => 'categories',
            'content.reviews' => 'reviews',
            'content.settings' => 'settings',
            default => 'learning-materials'
        };

        $this->loadStatistics();
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        $this->loadStatistics();
    }

    protected function loadStatistics()
    {
        // Load statistics - removed role-based filtering, now shows all data
        try {
            $this->stats['total_documents'] = Document::count();
            $this->stats['total_videos'] = VideoLibrary::count();
            $this->stats['total_materials'] = LearningMaterial::count();
            $this->stats['pending_reviews'] = Document::where('status', 'pending_review')->count();
        } catch (\Exception $e) {
            // Fallback if models don't exist yet
            $this->stats = [
                'total_documents' => 0,
                'total_videos' => 0,
                'total_materials' => 0,
                'pending_reviews' => 0,
            ];
        }
    }

    public function refreshStats()
    {
        $this->loadStatistics();
        session()->flash('message', 'Statistics refreshed successfully!');
    }

    public function render()
    {
        return view('livewire.content.content-documentation-center', [
            'user' => $this->user,
            'stats' => $this->stats,
        ]);
    }
}