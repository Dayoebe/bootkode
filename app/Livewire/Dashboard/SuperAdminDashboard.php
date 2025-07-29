<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;

class SuperAdminDashboard extends Component
{
    // Current active tab/section for SPA navigation
    public $activeSection = 'users';
    
    // Dashboard stats (cached for performance)
    public $stats = [];
    
    // Listeners for global dashboard events
    protected $listeners = [
        'refreshStats' => 'loadStats',
        'sectionChanged' => 'updateSection'
    ];

    /**
     * Mount the dashboard with initial data
     */
    public function mount()
    {
        $this->loadStats();
    }

    /**
     * Load dashboard statistics
     */
    public function loadStats()
    {
        // Cache stats for 5 minutes to improve performance
        $this->stats = cache()->remember('super_admin_stats', 300, function () {
            return [
                'total_users' => \App\Models\User::count(),
                'verified_users' => \App\Models\User::whereNotNull('email_verified_at')->count(),
                'pending_users' => \App\Models\User::whereNull('email_verified_at')->count(),
                'total_admins' => \App\Models\User::whereIn('role', [
                    \App\Models\User::ROLE_SUPER_ADMIN,
                    \App\Models\User::ROLE_ACADEMY_ADMIN
                ])->count(),
                // Add more stats as needed for your SPA sections
                'last_updated' => now()->format('M d, Y h:i A')
            ];
        });
    }

    /**
     * Update active section for SPA navigation
     */
    public function updateSection($section)
    {
        $this->activeSection = $section;
    }

    /**
     * Clear stats cache when needed
     */
    public function refreshStats()
    {
        cache()->forget('super_admin_stats');
        $this->loadStats();
    }

    /**
     * Render the dashboard
     */
    public function render()
    {
        return view('livewire.dashboard.super-admin-dashboard')
                    ->layout('layouts.dashboard', [
                        'title' => 'Super Admin Dashboard'
                    ]);
    }
}