<?php

namespace App\Livewire\ManualPages;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\CourseEnrollment;
use App\Models\Certificate;
use App\Models\Assessment;
use Livewire\Attributes\Layout;

#[Layout('layouts.app', ['title' => 'Guidelines', 'description' => "Empowering Africa's youth with digital skills, mentorship & careers.", 'developer' => 'Bootkode', 'developer_url' => 'https://bootkode.com'])]

class Guideline extends Component
{
    public $stats = [];
    public $activeTab = 'student';

    public function mount()
    {
        $this->loadStatistics();
    }

    private function loadStatistics()
    {
        // User statistics
        $this->stats['totalUsers'] = User::count();
        $this->stats['totalInstructors'] = User::where('role', User::ROLE_INSTRUCTOR)->count();
        $this->stats['totalMentors'] = User::where('role', User::ROLE_MENTOR)->count();
        $this->stats['totalStudents'] = User::where('role', User::ROLE_STUDENT)->count();
        $this->stats['totalAdmins'] = User::whereIn('role', [
            User::ROLE_SUPER_ADMIN, 
            User::ROLE_ACADEMY_ADMIN
        ])->count();

        // Course statistics
        $this->stats['totalCourses'] = Course::where('is_published', true)
            ->where('is_approved', true)
            ->count();
        
        // Lesson statistics
        $this->stats['totalLessons'] = Lesson::whereHas('section.course', function($q) {
            $q->where('is_published', true)->where('is_approved', true);
        })->count();

        // Assessment statistics
        $this->stats['totalAssessments'] = Assessment::whereHas('course', function($q) {
            $q->where('is_published', true)->where('is_approved', true);
        })->count();

        // Enrollment statistics
        $this->stats['totalEnrollments'] = CourseEnrollment::count();
        $this->stats['completedCourses'] = CourseEnrollment::where('is_completed', true)->count();
        
        // Certificate statistics
        $this->stats['issuedCertificates'] = Certificate::where('status', Certificate::STATUS_APPROVED)->count();
        $this->stats['pendingCertificates'] = Certificate::where('status', Certificate::STATUS_PENDING)->count();
        
        // Calculate success rate
        $this->stats['successRate'] = $this->stats['totalEnrollments'] > 0 ? 
            round(($this->stats['completedCourses'] / $this->stats['totalEnrollments']) * 100, 1) : 0;

        // Calculate average course rating
        $this->stats['averageRating'] = Course::where('is_published', true)
            ->where('is_approved', true)
            ->avg('average_rating') ?: 4.8;

        // Additional metrics for mentoring
        $this->stats['jobPlacementRate'] = 85; // You can calculate this based on your mentorship success data
        $this->stats['mentorRating'] = 4.9;
        
        // System uptime (you can integrate with your monitoring system)
        $this->stats['systemUptime'] = 99.9;
        
        // Response time for support
        $this->stats['avgResponseTime'] = '< 24h';
    }

    public function selectTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        return view('livewire.manual-pages.guideline', [
            'activeTab' => $this->activeTab,
            'stats' => $this->stats
        ]);
    }
}