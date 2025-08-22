<?php

namespace App\Livewire\SystemManagement;

// use App\Models\SystemStatus;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.dashboard', [
    'title' => 'System Status',
    'description' => 'View the operational status of platform services',
    'icon' => 'fas fa-server',
    'active' => 'system-status'
])]
class SystemStatus extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = 'all';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => 'all'],
    ];

    public function getServicesProperty()
    {
        return [
            'website' => [
                'name' => 'Website',
                'status' => $this->getServiceStatus('website'),
            ],
            'database' => [
                'name' => 'Database',
                'status' => $this->getServiceStatus('database'),
            ],
            'api' => [
                'name' => 'API',
                'status' => $this->getServiceStatus('api'),
            ],
        ];
    }

    private function getServiceStatus($service)
    {
        $latestIncident = \App\Models\SystemStatus::where('service', $service)
            ->whereNull('resolved_at')
            ->latest('started_at')
            ->first();

        return $latestIncident ? $latestIncident->status : 'operational';
    }

    public function render()
    {
        $incidents = \App\Models\SystemStatus::when($this->search, function ($query) {
            $query->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
        })
            ->when($this->statusFilter !== 'all', function ($query) {
                if ($this->statusFilter === 'active') {
                    $query->whereNull('resolved_at');
                } else {
                    $query->whereNotNull('resolved_at');
                }
            })
            ->with('user')
            ->orderBy('started_at', 'desc')
            ->paginate(5);

        return view('livewire.system-management.system-status', [
            'services' => $this->services,
            'incidents' => $incidents,
        ]);
    }
}