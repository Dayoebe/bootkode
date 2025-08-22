<?php

namespace App\Livewire\UserManagement;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\Rule;

#[Layout('layouts.dashboard', ['title' => 'Roles & Permissions', 'description' => 'Manage user roles and permissions', 'icon' => 'fas fa-user-tag', 'active' => 'roles-permissions'])]
class RolesPermissions extends Component
{
    use WithPagination;

    public $search = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';
    public $perPage = 20;
    public $roleFilter = '';
    public $selectedUsers = [];
    public $selectedUserId = null;
    public $selectedRoles = [];
    public $exportFormat = 'csv';
    public $bulkRoleAction = 'assign'; // 'assign' or 'remove'
    public $bulkRole = '';
    public $newRoleName = '';
    public $newRolePermissions = [];
    public $createdAtStart = '';
    public $createdAtEnd = '';

    public function mount()
    {
        // Rely on navigation link visibility; add gate for defense-in-depth
        // if (!Gate::allows('manage-roles')) {
        //     session()->flash('error', 'Unauthorized access to role management.');
        //     $this->redirectRoute('dashboard');
        // }
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function openRoleModal($userId)
    {
        $this->selectedUserId = $userId;
        $user = User::findOrFail($userId);
        $this->selectedRoles = $user->getRoleNames()->toArray();
        $this->dispatch('open-role-modal');
    }

    public function saveRoles()
    {
        $user = User::findOrFail($this->selectedUserId);
        $user->syncRoles($this->selectedRoles);
        $user->logCustomActivity("Roles updated to: " . implode(', ', $this->selectedRoles), ['by' => auth()->user()->name]);
        session()->flash('success', 'Roles updated successfully.');
        $this->reset(['selectedUserId', 'selectedRoles']);
        $this->dispatch('close-role-modal');
    }

    public function openCreateRoleModal()
    {
        $this->reset(['newRoleName', 'newRolePermissions']);
        $this->dispatch('open-create-role-modal');
    }

    public function createRole()
    {
        $this->validate([
            'newRoleName' => ['required', 'string', 'max:255', Rule::unique('roles', 'name')],
            'newRolePermissions' => 'array',
        ]);

        $role = Role::create(['name' => $this->newRoleName]);
        $role->syncPermissions($this->newRolePermissions);
        session()->flash('success', 'Role created successfully.');
        $this->reset(['newRoleName', 'newRolePermissions']);
        $this->dispatch('close-create-role-modal');
    }

    public function openActivityModal($userId)
    {
        $this->selectedUserId = $userId;
        $activity = $this->getUserActivity($userId);
        $this->dispatch('open-activity-modal', activity: $activity);
    }

    public function bulkRoleAction()
    {
        if (empty($this->bulkRole)) {
            session()->flash('error', 'Select a role for bulk action.');
            return;
        }

        User::whereIn('id', $this->selectedUsers)
            ->where('id', '!=', 1) // Protect super admin ID 1
            ->where(fn($query) => $query->whereNot('id', auth()->id())) // Prevent self-modification
            ->get()
            ->each(function ($user) {
                if ($this->bulkRoleAction === 'assign') {
                    $user->assignRole($this->bulkRole);
                    $logMessage = "Role assigned: $this->bulkRole";
                } else {
                    $user->removeRole($this->bulkRole);
                    $logMessage = "Role removed: $this->bulkRole";
                }
                $user->logCustomActivity($logMessage, ['by' => auth()->user()->name]);
            });
        $this->selectedUsers = [];
        session()->flash('success', 'Bulk role action completed.');
    }


    public function export()
    {
        $users = $this->getUsersQuery()->get();
        $filename = 'roles_permissions_export_' . now()->format('Ymd_His') . '.' . $this->exportFormat;

        if ($this->exportFormat === 'json') {
            return response()->streamDownload(function () use ($users) {
                echo json_encode($users->map(fn($user) => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'roles' => $user->getRoleNames()->toArray(),
                    'permissions' => $user->getAllPermissions()->pluck('name')->toArray(),
                    'active' => $user->is_active ? 'Yes' : 'No',
                ])->toArray());
            }, $filename, ['Content-Type' => 'application/json']);
        }

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        return Response::stream(function () use ($users) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Name', 'Email', 'Roles', 'Permissions', 'Active']);
            foreach ($users as $user) {
                fputcsv($file, [
                    $user->name,
                    $user->email,
                    implode(', ', $user->getRoleNames()->toArray()),
                    implode(', ', $user->getAllPermissions()->pluck('name')->toArray()),
                    $user->is_active ? 'Yes' : 'No',
                ]);
            }
            fclose($file);
        }, 200, $headers);
    }

    public function getUserActivity($userId)
    {
        return activity()
            ->causedBy(User::findOrFail($userId))
            ->orWhere('subject_id', $userId)
            ->where('subject_type', User::class)
            ->latest()
            ->take(10)
            ->get()
            ->map(fn($log) => [
                'description' => $log->description,
                'created_at' => $log->created_at->diffForHumans(),
            ]);
    }
    protected function getUsersQuery()
    {
        return User::query()
            ->whereNull('email_verified_at')
            ->with(['roles', 'enrollments'])
            ->when($this->search, fn($query) => $query->where(
                fn($q) => $q
                    ->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
            ))
            ->when($this->roleFilter, fn($query) => $query->role($this->roleFilter))
            ->when($this->createdAtStart, fn($query) => $query->where('created_at', '>=', $this->createdAtStart))
            ->when($this->createdAtEnd, fn($query) => $query->where('created_at', '<=', $this->createdAtEnd))
            ->orderBy($this->sortField, $this->sortDirection);
    }


    public function bulkSendReminders()
    {
        User::whereIn('id', $this->selectedUsers)
            ->whereNull('email_verified_at')
            ->get()
            ->each(function ($user) {
                $user->notify(new VerifyEmailNotification());
                $user->logCustomActivity("Verification reminder sent via bulk action", ['by' => auth()->user()->name]);
            });
        $this->selectedUsers = [];
        session()->flash('success', 'Verification reminders sent successfully.');
    }
    public function getRoleStats()
    {
        return cache()->remember('unverified_role_stats', now()->addMinutes(10), fn() => User::whereNull('email_verified_at')
            ->select('role')
            ->groupBy('role')
            ->pluck('role')
            ->mapWithKeys(fn($role) => [$role => User::whereNull('email_verified_at')->where('role', $role)->count()]));
    }
    public function render()
    {
        return view('livewire.user-management.roles-permissions', [
            'users' => $this->getUsersQuery()->paginate($this->perPage),
            'roles' => cache()->remember('user_roles', now()->addHours(24), fn() => User::getRoles()),
            'allRoles' => Role::pluck('name')->toArray(),
            'allPermissions' => Permission::pluck('name')->toArray(),
        ]);
    }
}