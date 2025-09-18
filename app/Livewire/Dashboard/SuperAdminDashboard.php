<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\User;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\SupportTicket;
use App\Models\Certificate;
use App\Models\SystemStatus;
use App\Models\JobPortal;
use App\Models\Wallet;
use App\Models\BlogPost;
use App\Models\Faq;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Carbon\Carbon;

#[Layout('layouts.dashboard', [
    'title' => 'Super Admin Dashboard',
    'description' => 'Comprehensive control center for managing users, courses, and system operations',
    'icon' => 'fas fa-user-shield',
    'active' => 'super_admin_dashboard',
])]
class SuperAdminDashboard extends Component
{
    public $selectedTimeframe = '7days';
    public $showQuickActionModal = false;
    public $refreshInterval = 30000; // 30 seconds
    
    // Dashboard sections visibility
    public $showWidgets = [
        'overview_stats' => true,
        'revenue_analytics' => true,
        'user_analytics' => true,
        'course_performance' => true,
        'system_health' => true,
        'recent_activities' => true,
        'pending_approvals' => true,
        'support_overview' => true,
    ];

    protected $listeners = [
        'refreshDashboard' => 'loadAllData',
        'timeframeChanged' => 'updateTimeframe',
        'toggleWidget' => 'toggleWidget',
    ];

    public function mount()
    {
        if (!Auth::user()->hasRole(User::ROLE_SUPER_ADMIN)) {
            abort(403, 'Unauthorized access to Super Admin Dashboard.');
        }

        $this->loadAllData();
    }

    public function loadAllData()
    {
        // Load all dashboard data
        $this->dispatch('dashboard-updated');
    }

    public function updateTimeframe($timeframe)
    {
        $this->selectedTimeframe = $timeframe;
        $this->loadAllData();
    }

    public function toggleWidget($widget)
    {
        $this->showWidgets[$widget] = !$this->showWidgets[$widget];
    }

    #[Computed]
    public function overviewStats()
    {
        $timeframe = $this->getTimeframeQuery();
        
        return [
            'total_users' => User::count(),
            'new_users_today' => User::whereDate('created_at', today())->count(),
            'active_users' => User::where('is_active', true)->count(),
            'total_courses' => Course::count(),
            'published_courses' => Course::where('is_published', true)->count(),
            'pending_courses' => Course::where('is_approved', false)->count(),
            'total_revenue' => $this->getTotalRevenue(),
            'monthly_revenue' => $this->getMonthlyRevenue(),
            'open_tickets' => SupportTicket::where('status', 'open')->count(),
            'pending_certificates' => Certificate::where('status', 'pending')->count(),
            'job_postings' => JobPortal::where('status', JobPortal::STATUS_ACTIVE)->count(),
            'blog_posts' => BlogPost::published()->count(),
            
        ];
    }

    #[Computed]
    public function userGrowthData()
    {
        $days = $this->getTimeframeDays();
        $data = [];
        
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $data[] = [
                'date' => $date->format('M d'),
                'new_users' => User::whereDate('created_at', $date)->count(),
                'total_users' => User::where('created_at', '<=', $date->endOfDay())->count(),
            ];
        }
        
        return $data;
    }

    #[Computed]
    public function revenueAnalytics()
    {
        $days = $this->getTimeframeDays();
        $data = [];
        
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $revenue = $this->getDailyRevenue($date);
            
            $data[] = [
                'date' => $date->format('M d'),
                'revenue' => $revenue,
                'formatted_revenue' => '₦' . number_format($revenue, 2),
            ];
        }
        
        return $data;
    }

    #[Computed]
    public function coursePerformance()
    {
        return Course::with(['enrollments', 'instructor'])
            ->withCount(['enrollments', 'reviews'])
            ->where('is_published', true)
            ->orderBy('enrollments_count', 'desc')
            ->take(10)
            ->get()
            ->map(function ($course) {
                $totalEnrollments = $course->enrollments_count;
                $completedEnrollments = $course->enrollments()
                ->where('progress_percentage', 100)
                ->count();
                
                return [
                    'id' => $course->id,
                    'title' => $course->title,
                    'instructor' => $course->instructor->name ?? 'Unknown',
                    'enrollments' => $totalEnrollments,
                    'completion_rate' => $totalEnrollments > 0 
                        ? round(($completedEnrollments / $totalEnrollments) * 100, 1)
                        : 0,
                    'rating' => $course->reviews_avg_rating ?? 0,
                    'revenue' => $course->is_premium ? ($course->price * $totalEnrollments) : 0,
                ];
            });
    }

    #[Computed]
    public function systemHealth()
    {
        return [
            'status' => $this->getSystemStatus(),
            'database_health' => $this->checkDatabaseHealth(),
            'storage_usage' => $this->getStorageUsage(),
            'cache_status' => $this->getCacheStatus(),
            'queue_status' => $this->getQueueStatus(),
            'last_backup' => $this->getLastBackupTime(),
        ];
    }

    #[Computed]
    public function recentActivities()
    {
        return Activity::with('causer')
            ->latest()
            ->take(15)
            ->get()
            ->map(function ($activity) {
                return [
                    'id' => $activity->id,
                    'description' => $activity->description,
                    'causer' => $activity->causer->name ?? 'System',
                    'subject_type' => class_basename($activity->subject_type ?? ''),
                    'created_at' => $activity->created_at->diffForHumans(),
                    'icon' => $this->getActivityIcon($activity->description),
                    'color' => $this->getActivityColor($activity->description),
                ];
            });
    }

    #[Computed]
    public function pendingApprovals()
    {
        return [
            'courses' => Course::where('is_approved', false)
                ->with('instructor')
                ->latest()
                ->take(5)
                ->get()
                ->map(function ($course) {
                    return [
                        'id' => $course->id,
                        'title' => $course->title,
                        'instructor' => $course->instructor->name ?? 'Unknown',
                        'created_at' => $course->created_at->diffForHumans(),
                        'category' => $course->category,
                    ];
                }),
            
            'certificates' => Certificate::where('status', 'pending')
                ->with(['user', 'course'])
                ->latest()
                ->take(5)
                ->get()
                ->map(function ($certificate) {
                    return [
                        'id' => $certificate->id,
                        'user' => $certificate->user->name ?? 'Unknown',
                        'course' => $certificate->course->title ?? 'Unknown',
                        'created_at' => $certificate->created_at->diffForHumans(),
                        'grade' => $certificate->grade,
                    ];
                }),
        ];
    }

    #[Computed]
    public function supportOverview()
    {
        return [
            'total_tickets' => SupportTicket::count(),
            'open_tickets' => SupportTicket::where('status', 'open')->count(),
            'pending_tickets' => SupportTicket::where('status', 'pending')->count(),
            'resolved_tickets' => SupportTicket::where('status', 'resolved')->count(),
            'average_response_time' => $this->getAverageResponseTime(),
            'resolution_rate' => $this->getResolutionRate(),
            'recent_tickets' => SupportTicket::with('user')
                ->latest()
                ->take(5)
                ->get()
                ->map(function ($ticket) {
                    return [
                        'id' => $ticket->id,
                        'subject' => $ticket->subject,
                        'user' => $ticket->user->name ?? 'Guest',
                        'status' => $ticket->status,
                        'priority' => $ticket->priority ?? 'normal',
                        'created_at' => $ticket->created_at->diffForHumans(),
                    ];
                }),
        ];
    }

    // Helper Methods
    private function getTimeframeQuery()
    {
        return match ($this->selectedTimeframe) {
            '24hours' => now()->subHours(24),
            '7days' => now()->subDays(7),
            '30days' => now()->subDays(30),
            '90days' => now()->subDays(90),
            default => now()->subDays(7),
        };
    }

    private function getTimeframeDays()
    {
        return match ($this->selectedTimeframe) {
            '24hours' => 1,
            '7days' => 7,
            '30days' => 30,
            '90days' => 90,
            default => 7,
        };
    }

    private function getTotalRevenue()
    {
        return DB::table('wallet_transactions')
            ->where('category', 'course_purchase')
            ->where('type', 'credit')
            ->sum('amount');
    }

    private function getMonthlyRevenue()
    {
        return DB::table('wallet_transactions')
            ->where('category', 'course_purchase')
            ->where('type', 'credit')
            ->whereMonth('created_at', now()->month)
            ->sum('amount');
    }

    private function getDailyRevenue($date)
    {
        return DB::table('wallet_transactions')
            ->where('category', 'course_purchase')
            ->where('type', 'credit')
            ->whereDate('created_at', $date)
            ->sum('amount') ?? 0;
    }

    private function getSystemStatus()
    {
        $status = SystemStatus::latest()->first();
        return [
            'status' => $status->status ?? 'operational',
            'message' => $status->message ?? 'All systems operational',
            'updated_at' => $status ? $status->updated_at->diffForHumans() : 'Never',
        ];
    }

    private function checkDatabaseHealth()
    {
        try {
            DB::connection()->getPdo();
            return ['status' => 'healthy', 'message' => 'Database connection active'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Database connection failed'];
        }
    }

    private function getStorageUsage()
    {
        // This would need to be implemented based on your storage setup
        return ['used' => '2.3 GB', 'total' => '10 GB', 'percentage' => 23];
    }

    private function getCacheStatus()
    {
        try {
            cache()->put('health_check', 'ok', 60);
            return cache()->get('health_check') === 'ok' ? 'operational' : 'error';
        } catch (\Exception $e) {
            return 'error';
        }
    }

    private function getQueueStatus()
    {
        // This would need to be implemented based on your queue setup
        return ['pending' => 5, 'failed' => 0, 'processed' => 1250];
    }

    private function getLastBackupTime()
    {
        // This would need to be implemented based on your backup strategy
        return now()->subHours(6)->diffForHumans();
    }

    private function getAverageResponseTime()
    {
        return DB::table('support_tickets')
            ->whereNotNull('responded_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, responded_at)) as avg_hours')
            ->value('avg_hours') ?? 0;
    }

    private function getResolutionRate()
    {
        $total = SupportTicket::count();
        $resolved = SupportTicket::where('status', 'resolved')->count();
        
        return $total > 0 ? round(($resolved / $total) * 100, 1) : 0;
    }

    private function getActivityIcon($description)
    {
        return match (true) {
            str_contains(strtolower($description), 'user') => 'fas fa-user',
            str_contains(strtolower($description), 'course') => 'fas fa-book',
            str_contains(strtolower($description), 'certificate') => 'fas fa-certificate',
            str_contains(strtolower($description), 'payment') => 'fas fa-credit-card',
            str_contains(strtolower($description), 'login') => 'fas fa-sign-in-alt',
            default => 'fas fa-info-circle',
        };
    }

    private function getActivityColor($description)
    {
        return match (true) {
            str_contains(strtolower($description), 'created') => 'text-green-600',
            str_contains(strtolower($description), 'updated') => 'text-blue-600',
            str_contains(strtolower($description), 'deleted') => 'text-red-600',
            str_contains(strtolower($description), 'login') => 'text-purple-600',
            default => 'text-gray-600',
        };
    }

    public function quickAction($action)
    {
        return match ($action) {
            'create_course' => redirect()->route('create_course'),
            'manage_users' => redirect()->route('user-management'),
            'view_tickets' => redirect()->route('support.tickets'),
            'manage_faqs' => redirect()->route('faq.management'),
            'view_courses' => redirect()->route('all-course'),
            'manage_categories' => redirect()->route('course-categories'),
            'view_analytics' => redirect()->route('learning.analytics'),
            'system_settings' => redirect()->route('settings'),
            default => $this->dispatch('notify', type: 'error', message: 'Invalid action.'),
        };
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