<?php

namespace App\Livewire\Component\User;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Response;

#[Layout('layouts.dashboard', ['title' => 'All Users', 'description' => 'Manage all users in the system', 'icon' => 'fas fa-users', 'active' => 'all-users'])]
class AllUser extends Component
{
    use WithPagination;

    public $search = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';
    public $perPage = 20;
    public $roleFilter = '';
    public $lastLoginStart = '';
    public $lastLoginEnd = '';

    // public function mount()
    // {
    //     if (! Gate::allows('manage-users')) {
    //         session()->flash('error', 'Unauthorized access to user management.');
    //         $this->redirectRoute('dashboard');
    //     }
    // }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    protected function getUsersQuery()
    {
        return User::query()
            ->withCount('enrollments')
            ->withCount([
                'enrollments as completed_enrollments_count' => fn($query) => $query->where('is_completed', true),
                'enrollments as in_progress_enrollments_count' => fn($query) => $query->where('progress_percentage', '>', 0)->where('is_completed', false),
            ])
            ->when($this->search, fn($query) => $query->where(
                fn($q) => $q
                    ->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
            ))
            ->when($this->roleFilter, fn($query) => $query->role($this->roleFilter))
            ->when($this->lastLoginStart, fn($query) => $query->where('last_login_at', '>=', $this->lastLoginStart))
            ->when($this->lastLoginEnd, fn($query) => $query->where('last_login_at', '<=', $this->lastLoginEnd))
            ->orderBy($this->sortField, $this->sortDirection);
    }

    public function viewUser($userId)
    {
        if (Gate::allows('view-user-activity')) {
            $this->redirectRoute('user.activity', ['user' => $userId]);
        } else {
            session()->flash('error', 'Unauthorized to view user activity.');
        }
    }

    public function exportCsv()
    {
        $users = $this->getUsersQuery()->get();
        $filename = 'users_export_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        return Response::stream(function () use ($users) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Name', 'Email', 'Role', 'Verified', 'Courses Registered', 'Completed Courses', 'In-Progress Courses', 'Last Login', 'Active']);
            foreach ($users as $user) {
                fputcsv($file, [
                    $user->name,
                    $user->email,
                    $user->getRoleNames()->first() ?? 'N/A',
                    $user->email_verified_at ? 'Yes' : 'No',
                    $user->enrollments_count,
                    $user->completed_enrollments_count,
                    $user->in_progress_enrollments_count,
                    $user->last_login_at ? $user->last_login_at->toDateTimeString() : 'Never',
                    $user->is_active ? 'Yes' : 'No',
                ]);
            }
            fclose($file);
        }, 200, $headers);
    }












    public function getUserActivity($userId)
    {
        $user = User::findOrFail($userId);
        return [
            'enrollments' => $user->enrollments()->with('course')->get()->map(fn($e) => [
                'course_title' => $e->course->title,
                'progress' => $e->progress_percentage,
                'completed' => $e->is_completed,
            ]),
            'certificates' => $user->certificates()->count(),
            'recent_activity' => activity()->causedBy($user)->take(5)->get()->pluck('description'),
        ];
    }

    public function getRoleStats()
    {
        return cache()->remember('user_role_stats', now()->addMinutes(10), fn() => User::select('role')
            ->groupBy('role')
            ->pluck('role')
            ->mapWithKeys(fn($role) => [$role => User::where('role', $role)->count()]));
    }



    public function render()
    {
        return view('livewire.component.user.alluser', [
            'users' => $this->getUsersQuery()->paginate($this->perPage),
            'roles' => cache()->remember('user_roles', now()->addHours(24), fn() => User::getRoles()),
        ]);
    }
}