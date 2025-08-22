<?php

namespace App\Livewire\StudentManagement;

use Livewire\Component;
use App\Models\Course;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.dashboard', ['title' => 'Learning Analytics', 'description' => 'Track your learning progress and analytics', 'icon' => 'fas fa-chart-line', 'active' => 'student.learning-analytics'])]

class LearningAnalytics extends Component
{
    public $timeRange = 'month';
    public $activityData = [];
    public $progressData = [];
    public $timeSpentData = [];

    protected $listeners = ['refreshCharts' => 'prepareChartData'];

    public function mount()
    {
        $this->prepareChartData();
    }

    public function prepareChartData()
    {
        $user = auth()->user();
        
        // Determine date range based on timeRange selection
        $dateRange = match($this->timeRange) {
            'week' => now()->subWeek(),
            'month' => now()->subMonth(),
            'year' => now()->subYear(),
            default => now()->subMonth(),
        };
    
        // Activity Data - Query pivot table directly
        $activity = DB::table('lesson_user')
            ->select(DB::raw('DATE(completed_at) as date, COUNT(*) as count'))
            ->where('user_id', $user->id)
            ->where('completed_at', '>=', $dateRange)
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->mapWithKeys(fn($item) => [$item->date => $item->count]);
    
        $this->activityData = [
            'labels' => $activity->keys()->toArray(),
            'datasets' => [[
                'label' => 'Lessons Completed',
                'data' => $activity->values()->toArray(),
                'backgroundColor' => 'rgba(59, 130, 246, 0.5)',
                'borderColor' => 'rgba(59, 130, 246, 1)',
                'borderWidth' => 1
            ]]
        ];
    
        // Course Progress Data
        $courses = $user->courses()
            ->withPivot(['progress', 'updated_at'])
            ->orderBy('course_user.progress', 'desc')
            ->limit(5)
            ->get();
    
        $this->progressData = [
            'labels' => $courses->pluck('title')->toArray(),
            'datasets' => [[
                'label' => 'Progress %',
                'data' => $courses->pluck('pivot.progress')->toArray(),
                'backgroundColor' => [
                    'rgba(59, 130, 246, 0.7)',
                    'rgba(16, 185, 129, 0.7)',
                    'rgba(245, 158, 11, 0.7)',
                    'rgba(139, 92, 246, 0.7)',
                    'rgba(239, 68, 68, 0.7)'
                ],
                'borderWidth' => 1
            ]]
        ];
    
        // Time Spent Data - Query without using relationships
        $timeSpent = DB::table('course_user')
            ->select(
                'course_categories.name as category',
                DB::raw('SUM(courses.estimated_duration_minutes * course_user.progress / 100) as minutes')
            )
            ->join('courses', 'course_user.course_id', '=', 'courses.id')
            ->join('course_categories', 'courses.category_id', '=', 'course_categories.id')
            ->where('course_user.user_id', $user->id)
            ->groupBy('course_categories.id', 'course_categories.name')
            ->orderByDesc('minutes')
            ->limit(5)
            ->get();
    
        $this->timeSpentData = [
            'labels' => $timeSpent->pluck('category')->toArray(),
            'datasets' => [[
                'label' => 'Hours Spent',
                'data' => $timeSpent->pluck('minutes')->map(fn($m) => round($m / 60, 1))->toArray(),
                'backgroundColor' => [
                    'rgba(59, 130, 246, 0.7)',
                    'rgba(16, 185, 129, 0.7)',
                    'rgba(245, 158, 11, 0.7)',
                    'rgba(139, 92, 246, 0.7)',
                    'rgba(239, 68, 68, 0.7)'
                ],
                'borderWidth' => 1
            ]]
        ];
    }
   
//     public function prepareChartData()
// {
//     $user = auth()->user();
    
//     // Determine date range based on timeRange selection
//     $dateRange = match($this->timeRange) {
//         'week' => now()->subWeek(),
//         'month' => now()->subMonth(),
//         'year' => now()->subYear(),
//         default => now()->subMonth(),
//     };

//     // Activity Data - Fixed query
//     $activity = $user->completedLessons()
//         ->newPivotStatement() // Start fresh query on pivot table
//         ->selectRaw('DATE(completed_at) as date, COUNT(*) as count')
//         ->where('user_id', $user->id)
//         ->where('completed_at', '>=', $dateRange)
//         ->groupBy('date')
//         ->orderBy('date')
//         ->get()
//         ->mapWithKeys(fn($item) => [$item->date => $item->count]);

//     $this->activityData = [
//         'labels' => $activity->keys()->toArray(),
//         'datasets' => [[
//             'label' => 'Lessons Completed',
//             'data' => $activity->values()->toArray(),
//             'backgroundColor' => 'rgba(59, 130, 246, 0.5)',
//             'borderColor' => 'rgba(59, 130, 246, 1)',
//             'borderWidth' => 1
//         ]]
//     ];

//     // Rest of the method remains the same...
//     // Course Progress Data
//     $courses = $user->courses()
//         ->withPivot(['progress', 'updated_at'])
//         ->orderBy('course_user.progress', 'desc')
//         ->limit(5)
//         ->get();

//     $this->progressData = [
//         'labels' => $courses->pluck('title')->toArray(),
//         'datasets' => [[
//             'label' => 'Progress %',
//             'data' => $courses->pluck('pivot.progress')->toArray(),
//             'backgroundColor' => [
//                 'rgba(59, 130, 246, 0.7)',
//                 'rgba(16, 185, 129, 0.7)',
//                 'rgba(245, 158, 11, 0.7)',
//                 'rgba(139, 92, 246, 0.7)',
//                 'rgba(239, 68, 68, 0.7)'
//             ],
//             'borderWidth' => 1
//         ]]
//     ];

//     // Time Spent Data - Calculate actual time spent per category
//     $timeSpent = $user->courses()
//         ->with('category')
//         ->selectRaw('course_categories.name as category, 
//                      SUM(courses.estimated_duration_minutes * course_user.progress / 100) as minutes')
//         ->join('course_categories', 'courses.category_id', '=', 'course_categories.id')
//         ->groupBy('course_categories.name', 'course_categories.id') // Include id to be safe
//         ->orderByDesc('minutes')
//         ->limit(5)
//         ->get();

//     $this->timeSpentData = [
//         'labels' => $timeSpent->pluck('category')->toArray(),
//         'datasets' => [[
//             'label' => 'Hours Spent',
//             'data' => $timeSpent->pluck('minutes')->map(fn($m) => round($m / 60, 1))->toArray(),
//             'backgroundColor' => [
//                 'rgba(59, 130, 246, 0.7)',
//                 'rgba(16, 185, 129, 0.7)',
//                 'rgba(245, 158, 11, 0.7)',
//                 'rgba(139, 92, 246, 0.7)',
//                 'rgba(239, 68, 68, 0.7)'
//             ],
//             'borderWidth' => 1
//         ]]
//     ];
// }
    public function render()
    {
        $user = auth()->user();
        
        $stats = [
            'totalCourses' => $user->courses()->count(),
            'completedCourses' => $user->courses()->wherePivot('progress', 100)->count(),
            'totalLessons' => $user->completedLessons()->count(),
            'activeStreak' => $this->calculateStreak($user),
        ];

        return view('livewire.student-management.learning-analytics', [
            'stats' => $stats,
            'courses' => $user->courses()
                ->withPivot(['progress', 'updated_at'])
                ->orderBy('course_user.progress', 'desc')
                ->get()
        ]);
    }

    protected function calculateStreak($user)
    {
        $streak = 0;
        $currentDate = now();
        
        // Check if user completed any lessons today
        $hasActivityToday = $user->completedLessons()
            ->whereDate('completed_at', $currentDate->toDateString())
            ->exists();
            
        if ($hasActivityToday) {
            $streak = 1;
            
            // Check previous days
            for ($i = 1; $i < 365; $i++) {
                $checkDate = $currentDate->copy()->subDays($i);
                
                $hasActivity = $user->completedLessons()
                    ->whereDate('completed_at', $checkDate->toDateString())
                    ->exists();
                    
                if ($hasActivity) {
                    $streak++;
                } else {
                    break;
                }
            }
        }
        
        return $streak;
    }
}