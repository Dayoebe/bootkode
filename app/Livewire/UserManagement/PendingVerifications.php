<?php

namespace App\Livewire\UserManagement;

use App\Models\User;
use App\Notifications\VerifyEmailNotification;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Response;

#[Layout('layouts.dashboard', ['title' => 'Pending Verifications', 'description' => 'Manage unverified user accounts', 'icon' => 'fas fa-user-clock', 'active' => 'pending-verifications'])]
class PendingVerifications extends Component
{
    use WithPagination;

    public $search = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';
    public $perPage = 20;
    public $roleFilter = '';
    public $selectedUsers = [];
    public $selectedUserId = null;
    public $exportFormat = 'csv';

    public function mount()
    {
        // Defense-in-depth: check permission
        if (! Gate::allows('manage-users')) {
            session()->flash('error', 'Unauthorized access to pending verifications.');
            $this->redirectRoute('dashboard');
        }
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

    public function verifyUser($userId)
    {
        $user = User::findOrFail($userId);
        if (! $user->hasVerifiedEmail()) {
            $user->forceFill(['email_verified_at' => now()])->save();
            $user->logCustomActivity("Email verified by admin", ['by' => auth()->user()->name]);
            session()->flash('success', "User {$user->name} verified successfully.");
        }
    }

    public function sendVerificationReminder($userId)
    {
        $user = User::findOrFail($userId);
        if (! $user->hasVerifiedEmail()) {
            $user->notify(new VerifyEmailNotification());
            $user->logCustomActivity("Verification reminder sent", ['by' => auth()->user()->name]);
            session()->flash('success', "Verification reminder sent to {$user->name}.");
        }
    }

    public function bulkVerify()
    {
        User::whereIn('id', $this->selectedUsers)
            ->whereNull('email_verified_at')
            ->update(['email_verified_at' => now()])
            ->each(function ($user) {
                $user->logCustomActivity("Email verified by bulk action", ['by' => auth()->user()->name]);
            });
        $this->selectedUsers = [];
        session()->flash('success', 'Selected users verified successfully.');
    }

    public function openUserDetailsModal($userId)
    {
        $this->selectedUserId = $userId;
        $this->dispatch('open-user-details-modal');
    }

    protected function getUsersQuery()
    {
        return User::query()
            ->whereNull('email_verified_at')
            ->with(['roles', 'enrollments'])
            ->when($this->search, fn ($query) => $query->where(fn ($q) => $q
                ->where('name', 'like', '%' . $this->search . '%')
                ->orWhere('email', 'like', '%' . $this->search . '%')
            ))
            ->when($this->roleFilter, fn ($query) => $query->role($this->roleFilter))
            ->orderBy($this->sortField, $this->sortDirection);
    }

    public function export()
    {
        $users = $this->getUsersQuery()->get();
        $filename = 'pending_verifications_export_' . now()->format('Ymd_His') . '.' . $this->exportFormat;

        if ($this->exportFormat === 'json') {
            return response()->streamDownload(function () use ($users) {
                echo json_encode($users->map(fn ($user) => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'roles' => $user->getRoleNames()->toArray(),
                    'enrollments_count' => $user->enrollments_count,
                    'created_at' => $user->created_at->toDateTimeString(),
                    'last_login_at' => $user->last_login_at ? $user->last_login_at->toDateTimeString() : 'Never',
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
            fputcsv($file, ['Name', 'Email', 'Roles', 'Enrollments', 'Created At', 'Last Login', 'Active']);
            foreach ($users as $user) {
                fputcsv($file, [
                    $user->name,
                    $user->email,
                    implode(', ', $user->getRoleNames()->toArray()),
                    $user->enrollments_count,
                    $user->created_at->toDateTimeString(),
                    $user->last_login_at ? $user->last_login_at->toDateTimeString() : 'Never',
                    $user->is_active ? 'Yes' : 'No',
                ]);
            }
            fclose($file);
        }, 200, $headers);
    }

    public function getUserDetails($userId)
    {
        $user = User::with(['enrollments.course', 'roles'])->findOrFail($userId);
        return [
            'name' => $user->name,
            'email' => $user->email,
            'roles' => $user->getRoleNames()->toArray(),
            'enrollments' => $user->enrollments->map(fn ($e) => [
                'course_title' => $e->course->title,
                'progress_percentage' => $e->progress_percentage,
                'is_completed' => $e->is_completed,
            ])->toArray(),
            'activity' => activity()
                ->causedBy($user)
                ->orWhere('subject_id', $userId)
                ->where('subject_type', User::class)
                ->latest()
                ->take(5)
                ->get()
                ->map(fn ($log) => [
                    'description' => $log->description,
                    'created_at' => $log->created_at->diffForHumans(),
                ])->toArray(),
        ];
    }

    public function render()
    {
        return view('livewire.user-management.pending-verifications', [
            'users' => $this->getUsersQuery()->paginate($this->perPage),
            'roles' => cache()->remember('user_roles', now()->addHours(24), fn () => User::getRoles()),
        ]);
    }
}