<?php

namespace App\Livewire\Community;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Livewire\Attributes\Layout;


#[Layout('layouts.dashboard', ['title' => 'Community Center', 'description' => 'Manage community activities, templates, and analytics', 'icon' => 'fas fa-users', 'active' => 'admin.community'])]


class CommunityCenter extends Component
{
    public $activeTab = 'forums';
    public $user;

    public function mount()
    {
        $this->user = auth()->user();
        
        // Set active tab based on route
        $currentRoute = Route::currentRouteName();
        $this->activeTab = match($currentRoute) {
            'community.forums' => 'forums',
            'community.study-groups' => 'study-groups', 
            'community.code-challenges' => 'code-challenges',
            'community.live-events' => 'live-events',
            'community.moderation' => 'moderation',
            'community.feedback' => 'feedback',
            default => 'forums'
        };
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        return view('livewire.community.community-center', [
            'user' => $this->user,
        ]);
    }
}