<?php

namespace App\Livewire\Pages;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use App\Models\User;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Certificate;
use App\Models\Faq;
use App\Models\Announcement;
use App\Models\SystemStatus;
use App\Models\CourseCategory;
use Illuminate\Support\Facades\Cache;

#[Layout('layouts.app', [
    'title' => 'About Us - BootKode', 
    'description' => "Empowering Africa's youth with digital skills, mentorship & careers.", 
    'developer' => 'Bootkode', 
    'developer_url' => 'https://bootkode.com'
])]

class AboutUs extends Component
{
    public $stats = [];
    public $teamStats = [];
    public $recentAnnouncements = [];
    public $faqs = [];
    public $systemStatus = [];

    public function mount()
    {
        $this->loadStatistics();
        $this->loadTeamStatistics();
        $this->loadRecentContent();
    }

    private function loadStatistics()
    {
        $this->stats = Cache::remember('about_us_stats', 3600, function () {
            return [
                'total_users' => User::count(),
                'total_courses' => Course::published()->approved()->count(),
                'total_lessons' => Lesson::whereHas('section.course', function($query) {
                    $query->published()->approved();
                })->count(),
                'certificates_issued' => Certificate::approved()->count(),
                'course_categories' => CourseCategory::has('courses')->count(),
                'active_instructors' => User::whereHas('roles', function($query) {
                    $query->where('name', User::ROLE_INSTRUCTOR);
                })->count(),
                'completed_courses' => Certificate::approved()->distinct('course_id')->count('course_id'),
                'total_course_hours' => Course::published()->approved()->sum('estimated_duration_minutes'),
            ];
        });
    }

    private function loadTeamStatistics()
    {
        $this->teamStats = Cache::remember('team_stats', 3600, function () {
            $roles = User::getRoles();
            $roleStats = [];
            
            foreach ($roles as $role) {
                $count = User::whereHas('roles', function($query) use ($role) {
                    $query->where('name', $role);
                })->count();
                
                if ($count > 0) {
                    $roleStats[] = [
                        'role' => $role,
                        'count' => $count,
                        'label' => $this->getRoleLabel($role),
                        'icon' => $this->getRoleIcon($role),
                        'color' => $this->getRoleColor($role)
                    ];
                }
            }
            
            return $roleStats;
        });
    }

    private function loadRecentContent()
    {
        $this->recentAnnouncements = Announcement::where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->orderBy('published_at', 'desc')
            ->take(3)
            ->get();

        $this->faqs = Faq::where('is_published', true)
            ->orderBy('order')
            ->take(5)
            ->get();

        $this->systemStatus = SystemStatus::whereNull('resolved_at')
            ->orWhere('resolved_at', '>=', now()->subDays(7))
            ->orderBy('started_at', 'desc')
            ->take(3)
            ->get();
    }

    private function getRoleLabel($role)
    {
        return match($role) {
            User::ROLE_SUPER_ADMIN => 'Super Administrators',
            User::ROLE_ACADEMY_ADMIN => 'Academy Administrators',
            User::ROLE_INSTRUCTOR => 'Instructors',
            User::ROLE_MENTOR => 'Mentors',
            User::ROLE_CONTENT_EDITOR => 'Content Editors',
            User::ROLE_AFFILIATE_AMBASSADOR => 'Affiliate Ambassadors',
            User::ROLE_STUDENT => 'Students',
            default => ucwords(str_replace('_', ' ', $role))
        };
    }

    private function getRoleIcon($role)
    {
        return match($role) {
            User::ROLE_SUPER_ADMIN => 'fas fa-crown',
            User::ROLE_ACADEMY_ADMIN => 'fas fa-user-shield',
            User::ROLE_INSTRUCTOR => 'fas fa-chalkboard-teacher',
            User::ROLE_MENTOR => 'fas fa-user-friends',
            User::ROLE_CONTENT_EDITOR => 'fas fa-edit',
            User::ROLE_AFFILIATE_AMBASSADOR => 'fas fa-handshake',
            User::ROLE_STUDENT => 'fas fa-graduation-cap',
            default => 'fas fa-user'
        };
    }

    private function getRoleColor($role)
    {
        return match($role) {
            User::ROLE_SUPER_ADMIN => 'purple',
            User::ROLE_ACADEMY_ADMIN => 'blue',
            User::ROLE_INSTRUCTOR => 'green',
            User::ROLE_MENTOR => 'yellow',
            User::ROLE_CONTENT_EDITOR => 'pink',
            User::ROLE_AFFILIATE_AMBASSADOR => 'indigo',
            User::ROLE_STUDENT => 'gray',
            default => 'gray'
        };
    }

    public function render()
    {
        return view('livewire.pages.about-us');
    }
}