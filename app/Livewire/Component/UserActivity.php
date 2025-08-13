<?php

namespace App\Livewire\Component;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Activitylog\Models\Activity;
use Livewire\Attributes\Layout;

#[Layout('layouts.dashboard', [
    'title' => 'User Activity',
    'description' => 'View and manage user activity logs',
    'icon' => 'fas fa-history',
    'active' => 'user.activity'
])]
class UserActivity extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $userFilter = '';
    public $actionFilter = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 10],
        'userFilter' => ['except' => ''],
        'actionFilter' => ['except' => '']
    ];

    public function mount()
    {
        // Ensure only super admins can access
        if (!auth()->user()->hasRole('super_admin')) {
            abort(403, 'Unauthorized');
        }
    }

    public function getActivitiesProperty()
    {
        return Activity::query()
            ->when($this->search, function ($query) {
                $query->where('description', 'like', '%' . $this->search . '%')
                    ->orWhereHas('causer', function ($query) {
                        $query->where('name', 'like', '%' . $this->search . '%')
                            ->orWhere('email', 'like', '%' . $this->search . '%');
                    });
            })
            ->when($this->userFilter, function ($query) {
                $query->where('causer_id', $this->userFilter);
            })
            ->when($this->actionFilter, function ($query) {
                $query->where('event', $this->actionFilter);
            })
            ->orderBy('created_at', 'desc')
            ->with('causer') // Eager load user
            ->paginate($this->perPage);
    }

    public function getUsersForFilter()
    {
        return User::orderBy('name')->pluck('name', 'id')->toArray();
    }

    public function getActionTypes()
    {
        return Activity::select('event')->distinct()->pluck('event')->toArray();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedUserFilter()
    {
        $this->resetPage();
    }

    public function updatedActionFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.component.user-activity', [
            'activities' => $this->activities,
            'users' => $this->getUsersForFilter(),
            'actionTypes' => $this->getActionTypes()
        ]);
    }
}