<?php

namespace App\Livewire\Component;

use App\Models\SystemStatus;
use App\Notifications\SystemStatusUpdateNotification;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
// use App\Livewire\Component\User
use App\Models\User;
use Livewire\Attributes\Layout;

#[Layout('layouts.dashboard', ['title' => 'System Status Management', 'description' => 'Manage system status and incidents', 'icon' => 'fas fa-server', 'active' => 'system-status.management'])]
class SystemStatusManagement extends Component
{
    use WithPagination;

    public $service = 'website';
    public $status = 'operational';
    public $title = '';
    public $description = '';
    public $severity = 'low';
    public $started_at;
    public $editId = null;
    public $search = '';
    public $statusFilter = 'all';

    protected $rules = [
        'service' => ['required', 'in:website,database,api'],
        'status' => ['required', 'in:operational,degraded,down,maintenance'],
        'title' => ['required', 'string', 'max:255'],
        'description' => ['required', 'string', 'max:2000'],
        'severity' => ['required', 'in:low,medium,high'],
        'started_at' => ['required', 'date'],
    ];

    public function mount()
    {
        $this->started_at = now()->toDateTimeString();
    }

    public function saveIncident()
    {
        if (!Auth::user()->hasAnyRole(['super_admin', 'academy_admin'])) {
            $this->dispatch('notify', 'Unauthorized!', 'error');
            return;
        }

        $this->validate();

        $data = [
            'user_id' => Auth::id(),
            'service' => $this->service,
            'status' => $this->status,
            'title' => $this->title,
            'description' => $this->description,
            'severity' => $this->severity,
            'started_at' => $this->started_at,
        ];

        if ($this->editId) {
            $incident = SystemStatus::findOrFail($this->editId);
            $incident->update($data);
            $message = 'Incident updated successfully!';
        } else {
            $incident = SystemStatus::create($data);
            $message = 'Incident reported successfully!';
        }

        if ($this->status !== 'operational') {
            User::all()->each(function ($user) use ($incident) {
                $user->notify(new SystemStatusUpdateNotification($incident));
            });
            $this->dispatchTo('notifications', 'notify', [
                'message' => 'New system status issue: ' . $incident->title,
                'type' => 'error'
            ]);
        }

        Auth::user()->logCustomActivity($this->editId ? 'Updated system status incident' : 'Reported system status incident', ['incident_id' => $incident->id]);
        $this->dispatch('notify', $message, 'success');
        $this->reset(['service', 'status', 'title', 'description', 'severity', 'started_at', 'editId']);
        $this->started_at = now()->toDateTimeString();
    }

    public function resolveIncident($incidentId)
    {
        if (!Auth::user()->hasAnyRole(['super_admin', 'academy_admin'])) {
            $this->dispatch('notify', 'Unauthorized!', 'error');
            return;
        }

        $incident = SystemStatus::findOrFail($incidentId);
        $incident->update(['resolved_at' => now()]);
        User::all()->each(function ($user) use ($incident) {
            $user->notify(new SystemStatusUpdateNotification($incident));
        });
        $this->dispatchTo('notifications', 'notify', [
            'message' => 'System status resolved: ' . $incident->title,
            'type' => 'success'
        ]);
        Auth::user()->logCustomActivity('Resolved system status incident', ['incident_id' => $incidentId]);
        $this->dispatch('notify', 'Incident resolved successfully!', 'success');
    }

    public function editIncident($incidentId)
    {
        if (!Auth::user()->hasAnyRole(['super_admin', 'academy_admin'])) {
            $this->dispatch('notify', 'Unauthorized!', 'error');
            return;
        }

        $incident = SystemStatus::findOrFail($incidentId);
        $this->service = $incident->service;
        $this->status = $incident->status;
        $this->title = $incident->title;
        $this->description = $incident->description;
        $this->severity = $incident->severity;
        $this->started_at = $incident->started_at->toDateTimeString();
        $this->editId = $incident->id;
    }

    public function deleteIncident($incidentId)
    {
        if (!Auth::user()->hasAnyRole(['super_admin', 'academy_admin'])) {
            $this->dispatch('notify', 'Unauthorized!', 'error');
            return;
        }

        $incident = SystemStatus::findOrFail($incidentId);
        $incident->delete();
        Auth::user()->logCustomActivity('Deleted system status incident', ['incident_id' => $incidentId]);
        $this->dispatch('notify', 'Incident deleted successfully!', 'success');
    }

    public function render()
    {
        $incidents = SystemStatus::when($this->search, function ($query) {
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

        return view('livewire.component.system-status-management', [
            'incidents' => $incidents,
        ]);
    }
}