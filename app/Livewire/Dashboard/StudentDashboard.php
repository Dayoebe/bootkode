<?php

namespace App\Livewire\Dashboard;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;
use App\Models\UserProgress;
use App\Models\Announcement;
use App\Models\Certificate;
use App\Models\SupportTicket;
use App\Models\UserAchievement;
use App\Models\SystemStatus;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

#[Layout('layouts.dashboard', ['title' => 'Student Dashboard'])]
class StudentDashboard extends Component
{
    public $quickStats = [];
    public $enrolledCourses = [];
    public $recentAnnouncements = [];
    public $nextLessons = [];
    public $supportTickets = [];
    public $recentAchievements = [];
    public $learningStats = [];
    public $systemStatus = [];
    public $recommendedCourses = [];

    public function mount()
    {
        /** @var User $user */
        $user = Auth::user();

        // Check if the user is a student, if not, redirect them
        if (!$user->isStudent()) {
            redirect()->route($user->getDashboardRouteName());
        }

        // Fetch and prepare all the data for the dashboard
        $this->loadQuickStats($user);
        $this->loadEnrolledCourses($user);
        $this->loadRecentAnnouncements();
        $this->loadNextLessons($user);
        $this->loadSupportTickets($user);
        $this->loadRecentAchievements($user);
        $this->loadLearningStats($user);
        $this->loadSystemStatus();
        $this->loadRecommendedCourses($user);
    }

    private function loadQuickStats(User $user)
    {
        // Get the number of enrolled courses by the user's progress
        $enrolledCourseCount = UserProgress::where('user_id', $user->id)
            ->distinct('course_id')
            ->count();

        // Get the number of certificates earned by the user
        $certificatesEarnedCount = Certificate::where('user_id', $user->id)
            ->approved()
            ->count();

        // Get the number of active courses (not 100% completed)
        $activeCourses = UserProgress::where('user_id', $user->id)
            ->select('course_id')
            ->distinct('course_id')
            ->pluck('course_id');

        $activeCoursesCount = 0;
        $lessonsCompletedCount = 0;
        foreach ($activeCourses as $courseId) {
            $course = Course::find($courseId);
            if ($course) {
                $totalLessons = $course->total_lessons;
                if ($totalLessons > 0) {
                    $completedLessons = UserProgress::where('user_id', $user->id)
                        ->where('course_id', $courseId)
                        ->where('is_completed', true)
                        ->count();
                    $lessonsCompletedCount += $completedLessons;
                    $completionPercentage = round(($completedLessons / $totalLessons) * 100);
                    if ($completionPercentage < 100) {
                        $activeCoursesCount++;
                    }
                }
            }
        }

        // Calculate current study streak
        $studyStreak = 0;
        $lastStudyDate = UserProgress::where('user_id', $user->id)
            ->latest('updated_at')
            ->value('updated_at');
            
        if ($lastStudyDate) {
            $lastStudy = Carbon::parse($lastStudyDate);
            if ($lastStudy->isToday()) {
                $studyStreak = 1;
                $checkDate = Carbon::yesterday();
                
                // Check previous days for streak
                while (UserProgress::where('user_id', $user->id)
                    ->whereDate('updated_at', $checkDate->format('Y-m-d'))
                    ->exists()) {
                    $studyStreak++;
                    $checkDate->subDay();
                }
            }
        }

        $this->quickStats = [
            'enrolledCourses' => $enrolledCourseCount,
            'certificatesEarned' => $certificatesEarnedCount,
            'activeCourses' => $activeCoursesCount,
            'lessonsCompleted' => $lessonsCompletedCount,
            'studyStreak' => $studyStreak,
        ];
    }

    private function loadEnrolledCourses(User $user)
    {
        // Fetch user's progress, group by course, and join with course details
        $progress = UserProgress::where('user_id', $user->id)
            ->get()
            ->groupBy('course_id');

        $this->enrolledCourses = [];
        foreach ($progress as $courseId => $items) {
            $course = Course::find($courseId);
            if (!$course) {
                continue;
            }

            // Calculate progress percentage
            $totalLessons = $course->total_lessons;
            $completedLessons = $items->where('is_completed', true)->count();
            $completionPercentage = ($totalLessons > 0) ? round(($completedLessons / $totalLessons) * 100) : 0;

            // Determine status based on completion
            if ($completionPercentage === 100) {
                $status = 'Completed';
            } elseif ($completionPercentage > 0) {
                $status = 'In Progress';
            } else {
                $status = 'Not Started';
            }

            $this->enrolledCourses[] = (object)[
                'id' => $course->id,
                'title' => $course->title,
                'subtitle' => $course->subtitle,
                'thumbnail' => $course->thumbnail,
                'completion_percentage' => $completionPercentage,
                'status' => $status,
                'is_completed' => ($status === 'Completed'),
                'last_accessed' => $items->max('updated_at'),
            ];
        }

        // Sort by last accessed date (most recent first)
        usort($this->enrolledCourses, function($a, $b) {
            return $b->last_accessed <=> $a->last_accessed;
        });
    }

    private function loadRecentAnnouncements()
    {
        // Fetch recent announcements ordered by published date
        $this->recentAnnouncements = Announcement::where('published_at', '<=', now())
            ->orderBy('published_at', 'desc')
            ->take(3)
            ->get();
    }

    private function loadNextLessons(User $user)
    {
        $this->nextLessons = [];
        
        // Get all active courses (not completed)
        $activeCourseIds = [];
        foreach ($this->enrolledCourses as $course) {
            if (!$course->is_completed) {
                $activeCourseIds[] = $course->id;
            }
        }
        
        if (!empty($activeCourseIds)) {
            // For each active course, find the next lesson
            foreach ($activeCourseIds as $courseId) {
                $course = Course::find($courseId);
                if ($course) {
                    // Get completed lesson IDs for this course
                    $completedLessonIds = UserProgress::where('user_id', $user->id)
                        ->where('course_id', $courseId)
                        ->where('is_completed', true)
                        ->pluck('lesson_id')
                        ->toArray();
                    
                    // Find the first lesson that hasn't been completed
                    $nextLesson = Lesson::whereHas('section', function($query) use ($courseId) {
                            $query->where('course_id', $courseId);
                        })
                        ->whereNotIn('id', $completedLessonIds)
                        ->orderBy('order')
                        ->first();
                    
                    if ($nextLesson) {
                        $this->nextLessons[] = (object)[
                            'course_title' => $course->title,
                            'lesson_title' => $nextLesson->title,
                            'lesson_id' => $nextLesson->id,
                            'course_id' => $courseId,
                            'due_date' => now()->addDays(3)->format('M j, Y'), // Simulated due date
                        ];
                    }
                }
            }
            
            // Limit to 3 next lessons
            $this->nextLessons = array_slice($this->nextLessons, 0, 3);
        }
    }

    private function loadSupportTickets(User $user)
    {
        // Fetch user's support tickets
        $this->supportTickets = SupportTicket::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
    }

    private function loadRecentAchievements(User $user)
    {
        // Fetch recent achievements
        $this->recentAchievements = UserAchievement::where('user_id', $user->id)
            ->orderBy('earned_at', 'desc')
            ->take(5)
            ->get();
    }

    private function loadLearningStats(User $user)
    {
        // Calculate learning statistics
        $lastWeek = Carbon::now()->subDays(7);
        
        // Lessons completed in the last 7 days
        $recentLessons = UserProgress::where('user_id', $user->id)
            ->where('is_completed', true)
            ->where('updated_at', '>=', $lastWeek)
            ->count();
            
        // Calculate study days using date part of updated_at
        $studyDays = UserProgress::where('user_id', $user->id)
            ->where('is_completed', true)
            ->where('updated_at', '>=', $lastWeek)
            ->select(DB::raw('DATE(updated_at) as study_date'))
            ->distinct()
            ->count();
            
        $avgStudyTime = $studyDays > 0 ? round($recentLessons * 15 / $studyDays) : 0;
        
        $this->learningStats = [
            'recentLessons' => $recentLessons,
            'avgStudyTime' => $avgStudyTime,
            'consistencyScore' => min(100, $studyDays * 15), // Simulated consistency score
        ];
    }

    private function loadSystemStatus()
    {
        // Get system status information
        $this->systemStatus = SystemStatus::where('status', 'active')
            ->orderBy('severity', 'desc')
            ->take(2)
            ->get();
    }

    private function loadRecommendedCourses(User $user)
    {
        // Get recommended courses based on user's current enrollments
        $enrolledCourseIds = UserProgress::where('user_id', $user->id)
            ->distinct('course_id')
            ->pluck('course_id')
            ->toArray();
            
        if (!empty($enrolledCourseIds)) {
            // Get categories of enrolled courses
            $enrolledCategories = Course::whereIn('id', $enrolledCourseIds)
                ->pluck('category_id')
                ->toArray();
                
            // Recommend courses in the same categories
            $this->recommendedCourses = Course::whereNotIn('id', $enrolledCourseIds)
                ->whereIn('category_id', $enrolledCategories)
                ->where('is_published', true)
                ->where('is_approved', true)
                ->inRandomOrder()
                ->take(3)
                ->get();
        } else {
            // If no enrollments, get popular courses
            $this->recommendedCourses = Course::where('is_published', true)
                ->where('is_approved', true)
                ->orderBy('views_count', 'desc')
                ->take(3)
                ->get();
        }
    }

    public function render()
    {
        return view('livewire.dashboard.student-dashboard');
    }
}