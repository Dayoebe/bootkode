<?php

namespace App\Livewire\Dashboard;

use App\Services\DashboardSearchService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dashboard', [
    'title' => 'Workspace Search',
    'description' => 'Search courses, lessons, users, certificates, content, and platform records.',
    'icon' => 'fas fa-search',
    'active' => 'dashboard.search',
])]
class GlobalSearch extends Component
{
    public string $q = '';

    protected $queryString = [
        'q' => ['except' => ''],
    ];

    public function mount(): void
    {
        $this->q = trim((string) request()->query('q', $this->q));
    }

    public function render()
    {
        $payload = app(DashboardSearchService::class)->search($this->q, auth()->user(), 12);

        return view('livewire.dashboard.global-search', $payload);
    }
}
