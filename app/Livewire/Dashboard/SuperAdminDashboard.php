<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\User;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\SupportTicket;
use App\Models\Certificate;
use App\Models\SystemStatus;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Models\Activity;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;

#[Layout('layouts.dashboard', [
    'title' => 'Super Admin Dashboard',
    'description' => 'Comprehensive control center for managing users, courses, and system operations',
    'icon' => 'fas fa-user-shield',
    'active' => 'super_admin_dashboard',
])]


class SuperAdminDashboard extends Component
{
    public $activeSection = 'overview';
    public $stats = [];
    public $recentActivities = [];
    public $pendingCourses = [];
    public $pendingCertificates = [];
    public $systemStatus = [];
    public $recentUsers = [];
    public $notifications = [];
    public $showQuickActionModal = false;
    public $showWidgets = [
        'stats' => true,
        'activities' => true,
        'approvals' => true,
        'system_status' => true,
        'recent_users' => true,
        'calendar' => true,
        'revenue' => true,
        'engagement' => true,
    ];

    protected $listeners = [
        'refreshStats' => 'loadStats',
        'sectionChanged' => 'updateSection',
        'refreshActivities' => 'loadRecentActivities',
        'refreshNotifications' => 'loadNotifications',
        'toggleWidget' => 'toggleWidget',
    ];

    public function mount()
    {
        if (!Auth::user()->hasRole(User::ROLE_SUPER_ADMIN)) {
            abort(403, 'Unauthorized access to Super Admin Dashboard.');
        }

        $this->loadStats();
        $this->loadRecentActivities();
        $this->loadPendingApprovals();
        $this->loadSystemStatus();
        $this->loadRecentUsers();
        $this->loadNotifications();
    }

    #[Computed]
    public function userGrowthData()
    {
        $data = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $data[] = [
                'month' => $month->format('M Y'),
                'users' => User::where('created_at', '<=', $month->endOfMonth())->count(),
            ];
        }
        return $data;
    }

    #[Computed]
    public function courseEngagementData()
    {
        $courses = Course::withCount('enrollments')
            ->orderBy('enrollments_count', 'desc')
            ->take(5)
            ->get()
            ->map(function ($course) {
                return [
                    'title' => $course->title,
                    'enrollments' => $course->enrollments_count,
                    'completion_rate' => $course->enrollments->count() > 0
                        ? ($course->enrollments->whereNotNull('completed_at')->count() / $course->enrollments->count() * 100)
                        : 0,
                ];
            })->toArray(); // Convert Collection to array

        return $courses ?: []; // Return empty array if no data
    }

    public function loadStats()
    {
        $this->stats = cache()->remember('super_admin_stats', 300, function () {
            return [
                'total_users' => User::count(),
                'role_counts' => [
                    'super_admin' => User::where('role', User::ROLE_SUPER_ADMIN)->count(),
                    'academy_admin' => User::where('role', User::ROLE_ACADEMY_ADMIN)->count(),
                    'instructor' => User::where('role', User::ROLE_INSTRUCTOR)->count(),
                    'student' => User::where('role', User::ROLE_STUDENT)->count(),
                ],
                'total_courses' => Course::count(),
                'published_courses' => Course::where('is_published', true)->count(),
                'course_categories' => CourseCategory::count(),
                'pending_course_approvals' => Course::where('is_approved', false)->count(),
                'open_tickets' => SupportTicket::where('status', 'open')->count(),
                'total_certificates' => Certificate::count(),
                'pending_certificate_approvals' => Certificate::where('status', 'pending')->count(),
                'revenue' => Course::where('is_premium', true)->sum('price'),
                'last_updated' => now()->format('M d, Y h:i A'),
            ];
        });
    }

    public function loadRecentActivities()
    {
        $this->recentActivities = Activity::where('causer_type', User::class)
            ->with('causer')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($activity) {
                return [
                    'description' => $activity->description,
                    'causer' => $activity->causer ? $activity->causer->name : 'System',
                    'created_at' => $activity->created_at->diffForHumans(),
                ];
            })->toArray(); // Convert to array
    }

    public function loadPendingApprovals()
    {
        $this->pendingCourses = Course::where('is_approved', false)
            ->take(5)
            ->get()
            ->map(function ($course) {
                return [
                    'id' => $course->id,
                    'title' => $course->title,
                    'instructor' => $course->instructor ? $course->instructor->name : 'N/A',
                ];
            })->toArray();

        $this->pendingCertificates = Certificate::where('status', 'pending')
            ->take(5)
            ->get()
            ->map(function ($certificate) {
                return [
                    'id' => $certificate->id,
                    'user' => $certificate->user ? $certificate->user->name : 'N/A',
                    'course' => $certificate->course ? $certificate->course->title : 'N/A',
                ];
            })->toArray();
    }

    public function loadSystemStatus()
    {
        $this->systemStatus = cache()->remember('system_status', 300, function () {
            $status = SystemStatus::latest()->first();
            return [
                'status' => $status ? $status->status : 'operational',
                'message' => $status ? $status->message : 'All systems operational.',
                'updated_at' => $status ? $status->updated_at->diffForHumans() : now()->diffForHumans(),
            ];
        });
    }

    public function loadRecentUsers()
    {
        $this->recentUsers = User::orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'is_active' => $user->is_active,
                    'created_at' => $user->created_at->diffForHumans(),
                ];
            })->toArray();
    }

    public function loadNotifications()
    {
        $this->notifications = Activity::whereIn('description', ['created', 'updated', 'deleted'])
            ->where('causer_type', User::class)
            ->with('causer')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(function ($activity) {
                return [
                    'message' => sprintf('%s %s by %s', $activity->subject_type, $activity->description, $activity->causer ? $activity->causer->name : 'System'),
                    'created_at' => $activity->created_at->diffForHumans(),
                ];
            })->toArray();
    }

    public function toggleWidget($widget)
    {
        $this->showWidgets[$widget] = !$this->showWidgets[$widget];
    }

    public function toggleUserStatus($userId)
    {
        $user = User::findOrFail($userId);
        if ($user->id === 1 && $user->hasRole(User::ROLE_SUPER_ADMIN)) {
            $this->dispatch('notify', type: 'error', message: 'Cannot modify the primary super admin.');
            return;
        }

        $user->is_active ? $user->deactivateAccount() : $user->activateAccount();
        $this->loadRecentUsers();
        $this->dispatch('notify', type: 'success', message: 'User status updated successfully!');
    }

    public function quickAction($action)
    {
        switch ($action) {
            case 'create_course':
                return $this->redirect(route('create_course'));
            case 'manage_users':
                return $this->redirect(route('user-management'));
            case 'view_tickets':
                return $this->redirect(route('support.tickets'));
            case 'manage_faqs':
                return $this->redirect(route('faq.management'));
            case 'view_courses':
                return $this->redirect(route('all-course'));
            case 'manage_categories':
                return $this->redirect(route('course-categories'));
            default:
                $this->dispatch('notify', type: 'error', message: 'Invalid action.');
        }
    }

    public function toggleQuickActionModal()
    {
        $this->showQuickActionModal = !$this->showQuickActionModal;
    }

    public function render()
    {
        return view('livewire.dashboard.super-admin-dashboard');
    }
}