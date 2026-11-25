<?php

namespace App\Livewire\ManualPages;

use Livewire\Component;
use App\Models\Learning\Course;
use App\Models\Learning\Lesson;
use App\Models\Learning\CourseEnrollment;
use App\Models\Assessment\Assessment;
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
        $this->stats['totalCourses'] = Course::publiclyAvailable()->count();
        
        // Lesson statistics
        $this->stats['totalLessons'] = Lesson::whereHas('section.course', function($q) {
            $q->publiclyAvailable();
        })->count();

        // Assessment statistics
        $this->stats['totalAssessments'] = Assessment::whereHas('course', function($q) {
            $q->publiclyAvailable();
        })->count();

        // Enrollment statistics
        $this->stats['totalEnrollments'] = CourseEnrollment::count();
        $this->stats['completedCourses'] = CourseEnrollment::where('is_completed', true)->count();
        
        $this->stats['successRate'] = $this->stats['totalEnrollments'] > 0 ?
            round(($this->stats['completedCourses'] / $this->stats['totalEnrollments']) * 100, 1) : 0;
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
