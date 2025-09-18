<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\User;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\SupportTicket;
use App\Models\Certificate;
use App\Models\CourseEnrollment;
use App\Models\BlogPost;
use App\Models\Announcement;
use App\Models\Faq;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Carbon\Carbon;

#[Layout('layouts.dashboard', [
    'title' => 'Academy Admin Dashboard',
    'description' => 'Comprehensive academy management and oversight center',
    'icon' => 'fas fa-graduation-cap',
    'active' => 'academy_admin_dashboard',
])]
class AcademyAdminDashboard extends Component
{
    public $selectedTimeframe = '7days';
    public $showQuickActionModal = false;
    public $refreshInterval = 45000; // 45 seconds
    
    // Dashboard sections visibility
    public $showWidgets = [
        'overview_stats' => true,
        'course_management' => true,
        'student_analytics' => true,
        'instructor_performance' => true,
        'content_approval' => true,
        'recent_activities' => true,
        'support_overview' => true,
        'learning_progress' => true,
    ];

    protected $listeners = [
        'refreshDashboard' => 'loadAllData',
        'timeframeChanged' => 'updateTimeframe',
        'toggleWidget' => 'toggleWidget',
    ];

    public function mount()
    {
        if (!Auth::user()->hasRole(User::ROLE_ACADEMY_ADMIN)) {
            abort(403, 'Unauthorized access to Academy Admin Dashboard.');
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
        return [
            'total_students' => User::where('role', User::ROLE_STUDENT)->count(),
            'new_students_today' => User::where('role', User::ROLE_STUDENT)
                ->whereDate('created_at', today())->count(),
            'active_students' => User::where('role', User::ROLE_STUDENT)
                ->where('is_active', true)->count(),
            'total_instructors' => User::where('role', User::ROLE_INSTRUCTOR)->count(),
            'active_instructors' => User::where('role', User::ROLE_INSTRUCTOR)
                ->where('is_active', true)->count(),
            'total_courses' => Course::count(),
            'published_courses' => Course::where('is_published', true)->count(),
            'pending_courses' => Course::where('is_approved', false)->count(),
            'course_categories' => CourseCategory::count(),
            'total_enrollments' => CourseEnrollment::count(),
            'completed_courses' => CourseEnrollment::where('is_completed', true)->count(),
            'completion_rate' => $this->getOverallCompletionRate(),
            'open_tickets' => SupportTicket::where('status', 'open')->count(),
            'pending_certificates' => Certificate::where('status', 'pending')->count(),
            'blog_posts' => BlogPost::published()->count(),
            'announcements' => Announcement::where('status', 'published')->count(),
        ];
    }

    #[Computed]
    public function studentGrowthData()
    {
        $days = $this->getTimeframeDays();
        $data = [];
        
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $data[] = [
                'date' => $date->format('M d'),
                'new_students' => User::where('role', User::ROLE_STUDENT)
                    ->whereDate('created_at', $date)->count(),
                'total_students' => User::where('role', User::ROLE_STUDENT)
                    ->where('created_at', '<=', $date->endOfDay())->count(),
                'enrollments' => CourseEnrollment::whereDate('created_at', $date)->count(),
            ];
        }
        
        return $data;
    }

    #[Computed]
    public function coursePerformance()
    {
        return Course::with(['enrollments', 'instructor', 'reviews'])
            ->withCount(['enrollments', 'reviews'])
            ->where('is_published', true)
            ->orderBy('enrollments_count', 'desc')
            ->take(10)
            ->get()
            ->map(function ($course) {
                $totalEnrollments = $course->enrollments_count;
                $completedEnrollments = $course->enrollments()
                    ->where('is_completed', true)->count();
                
                return [
                    'id' => $course->id,
                    'title' => $course->title,
                    'instructor' => $course->instructor->name ?? 'Unknown',
                    'enrollments' => $totalEnrollments,
                    'completion_rate' => $totalEnrollments > 0 
                        ? round(($completedEnrollments / $totalEnrollments) * 100, 1)
                        : 0,
                    'rating' => $course->reviews_avg_rating ?? 0,
                    'status' => $course->is_approved ? 'Approved' : 'Pending',
                    'created_at' => $course->created_at->diffForHumans(),
                ];
            });
    }

    #[Computed]
    public function instructorPerformance()
    {
        return User::where('role', User::ROLE_INSTRUCTOR)
            ->withCount(['courses'])
            ->with(['courses' => function($query) {
                $query->where('is_published', true)
                      ->withCount('enrollments');
            }])
            ->orderBy('courses_count', 'desc')
            ->take(8)
            ->get()
            ->map(function ($instructor) {
                $totalEnrollments = $instructor->courses->sum('enrollments_count');
                $averageRating = $instructor->courses->avg('average_rating');
                
                return [
                    'id' => $instructor->id,
                    'name' => $instructor->name,
                    'email' => $instructor->email,
                    'courses_count' => $instructor->courses_count,
                    'total_enrollments' => $totalEnrollments,
                    'average_rating' => $averageRating ? round($averageRating, 1) : 0,
                    'last_active' => $instructor->last_login_at?->diffForHumans() ?? 'Never',
                    'profile_picture' => $instructor->profile_picture,
                ];
            });
    }

    #[Computed]
    public function contentApprovalQueue()
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
                        'category' => $course->category->name ?? 'Uncategorized',
                        'type' => $course->is_premium ? 'Premium' : 'Free',
                    ];
                }),
            
            'certificates' => Certificate::where('status', Certificate::STATUS_PENDING)
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
                        'completion_date' => $certificate->completion_date?->format('M d, Y'),
                    ];
                }),

            'blog_posts' => BlogPost::where('status', 'draft')
                ->orWhere('status', 'pending')
                ->with('author')
                ->latest()
                ->take(3)
                ->get()
                ->map(function ($post) {
                    return [
                        'id' => $post->id,
                        'title' => $post->title,
                        'author' => $post->author->name ?? 'Unknown',
                        'status' => $post->status,
                        'created_at' => $post->created_at->diffForHumans(),
                    ];
                }),
        ];
    }

    #[Computed]
    public function learningAnalytics()
    {
        $days = $this->getTimeframeDays();
        $data = [];
        
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $completions = CourseEnrollment::whereDate('completed_at', $date)
                ->where('is_completed', true)->count();
            
            $data[] = [
                'date' => $date->format('M d'),
                'completions' => $completions,
                'enrollments' => CourseEnrollment::whereDate('created_at', $date)->count(),
            ];
        }
        
        return $data;
    }

    #[Computed]
    public function recentActivities()
    {
        return Activity::with('causer')
            ->whereIn('log_name', ['course', 'user', 'certificate', 'enrollment'])
            ->latest()
            ->take(12)
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
    public function supportOverview()
    {
        return [
            'total_tickets' => SupportTicket::count(),
            'open_tickets' => SupportTicket::where('status', 'open')->count(),
            'pending_tickets' => SupportTicket::where('status', 'pending')->count(),
            'resolved_tickets' => SupportTicket::where('status', 'resolved')->count(),
            'response_time' => $this->getAverageResponseTime(),
            'resolution_rate' => $this->getResolutionRate(),
            'recent_tickets' => SupportTicket::with('user')
                ->latest()
                ->take(4)
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

    #[Computed]
    public function categoryStats()
    {
        return CourseCategory::withCount(['courses'])
            ->orderBy('courses_count', 'desc')
            ->get()
            ->map(function ($category) {
                $totalEnrollments = CourseEnrollment::whereHas('course', function($q) use ($category) {
                    $q->where('category_id', $category->id);
                })->count();
                
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'courses_count' => $category->courses_count,
                    'enrollments' => $totalEnrollments,
                    'popularity' => $category->courses_count > 0 ? 
                        round($totalEnrollments / $category->courses_count, 1) : 0,
                ];
            });
    }

    // Helper Methods
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

    private function getOverallCompletionRate()
    {
        $totalEnrollments = CourseEnrollment::count();
        $completedEnrollments = CourseEnrollment::where('is_completed', true)->count();
        
        return $totalEnrollments > 0 ? round(($completedEnrollments / $totalEnrollments) * 100, 1) : 0;
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
            str_contains(strtolower($description), 'student') => 'fas fa-user-graduate',
            str_contains(strtolower($description), 'course') => 'fas fa-book-open',
            str_contains(strtolower($description), 'certificate') => 'fas fa-award',
            str_contains(strtolower($description), 'enrollment') => 'fas fa-user-plus',
            str_contains(strtolower($description), 'instructor') => 'fas fa-chalkboard-teacher',
            str_contains(strtolower($description), 'completed') => 'fas fa-check-circle',
            default => 'fas fa-info-circle',
        };
    }

    private function getActivityColor($description)
    {
        return match (true) {
            str_contains(strtolower($description), 'created') => 'text-green-600',
            str_contains(strtolower($description), 'updated') => 'text-blue-600',
            str_contains(strtolower($description), 'deleted') => 'text-red-600',
            str_contains(strtolower($description), 'completed') => 'text-purple-600',
            str_contains(strtolower($description), 'enrolled') => 'text-indigo-600',
            default => 'text-gray-600',
        };
    }

    public function quickAction($action)
    {
        return match ($action) {
            'manage_courses' => redirect()->route('all-course'),
            'manage_students' => redirect()->route('user-management', ['role' => 'student']),
            'manage_instructors' => redirect()->route('user-management', ['role' => 'instructor']),
            'approve_content' => redirect()->route('course-approvals'), 
            'view_certificates' => redirect()->route('admin.certificates.manage'), 
            'view_tickets' => redirect()->route('support.tickets'),
            'manage_categories' => redirect()->route('course-categories'),
            'create_announcement' => redirect()->route('announcement.management'), 
            'learning_analytics' => redirect()->route('learning.analytics'),
            'manage_blog' => redirect()->route('admin.blog.posts.index'), 
            default => $this->dispatch('notify', type: 'error', message: 'Invalid action.'),
        };
    }

    public function toggleQuickActionModal()
    {
        $this->showQuickActionModal = !$this->showQuickActionModal;
    }

    public function approveCourse($courseId)
    {
        $course = Course::find($courseId);
        if ($course) {
            $course->update(['is_approved' => true]);
            $this->dispatch('notify', type: 'success', message: 'Course approved successfully');
            $this->loadAllData();
        }
    }

    public function approveCertificate($certificateId)
    {
        $certificate = Certificate::find($certificateId);
        if ($certificate && $certificate->canBeApproved()) {
            $certificate->approve(auth()->id());
            $this->dispatch('notify', type: 'success', message: 'Certificate approved successfully');
            $this->loadAllData();
        }
    }

    public function render()
    {
        return view('livewire.dashboard.academy-admin-dashboard');
    }
}