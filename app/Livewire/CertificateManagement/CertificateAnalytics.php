<?php

namespace App\Livewire\CertificateManagement;

use Livewire\Component;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.dashboard')]
#[Title('Certificate Analytics')]
class CertificateAnalytics extends Component
{
    public $dateRange = '30';
    public $selectedCourse = 'all';

    // Analytics data properties
    public $totalCertificates = 0;
    public $approvedCertificates = 0;
    public $pendingCertificates = 0;
    public $thisMonthCertificates = 0;
    public $approvalRate = 0;
    public $topCourses = [];
    public $recentActivity = [];
    public $monthlyStats = [];

    protected $queryString = [
        'dateRange' => ['except' => '30'],
        'selectedCourse' => ['except' => 'all'],
    ];

    public function mount()
    {
        $this->loadAnalyticsData();
    }

    public function updated($property)
    {
        if (in_array($property, ['dateRange', 'selectedCourse'])) {
            $this->loadAnalyticsData();
        }
    }

    public function loadAnalyticsData()
    {
        $dateFrom = $this->getDateFrom();
        
        // Base query with date filtering
        $baseQuery = Certificate::with(['user', 'course', 'course.instructor'])
            ->when($dateFrom, function($q) use ($dateFrom) {
                return $q->where('created_at', '>=', $dateFrom);
            })
            ->when($this->selectedCourse !== 'all', function($q) {
                return $q->where('course_id', $this->selectedCourse);
            });

        // If user is instructor, only show their course certificates
        if (Auth::user()->hasRole('instructor')) {
            $baseQuery->whereHas('course', function($q) {
                $q->where('instructor_id', Auth::id());
            });
        }

        // Basic statistics
        $this->totalCertificates = (clone $baseQuery)->count();
        $this->approvedCertificates = (clone $baseQuery)->where('status', Certificate::STATUS_APPROVED)->count();
        $this->pendingCertificates = (clone $baseQuery)->where('status', Certificate::STATUS_REQUESTED)->count();
        $this->thisMonthCertificates = (clone $baseQuery)->whereMonth('created_at', now()->month)->count();

        // Calculate approval rate
        $this->approvalRate = $this->totalCertificates > 0 
            ? round(($this->approvedCertificates / $this->totalCertificates) * 100, 1) 
            : 0;

        // Top courses by certificate count
        $this->loadTopCourses();

        // Recent activity
        $this->loadRecentActivity();

        // Monthly statistics for charts
        $this->loadMonthlyStats();
    }

    private function getDateFrom()
    {
        return match($this->dateRange) {
            '7' => now()->subDays(7),
            '30' => now()->subDays(30),
            '90' => now()->subDays(90),
            '180' => now()->subDays(180),
            '365' => now()->subYear(),
            'all' => null,
            default => now()->subDays(30),
        };
    }

    private function loadTopCourses()
    {
        $query = Certificate::selectRaw('course_id, count(*) as certificate_count')
            ->with(['course', 'course.instructor'])
            ->groupBy('course_id')
            ->orderByDesc('certificate_count');

        // Apply date filter
        $dateFrom = $this->getDateFrom();
        if ($dateFrom) {
            $query->where('created_at', '>=', $dateFrom);
        }

        // Apply course filter
        if ($this->selectedCourse !== 'all') {
            $query->where('course_id', $this->selectedCourse);
        }

        // If user is instructor, only show their courses
        if (Auth::user()->hasRole('instructor')) {
            $query->whereHas('course', function($q) {
                $q->where('instructor_id', Auth::id());
            });
        }

        $this->topCourses = $query->limit(10)->get()->map(function($item) {
            return [
                'course' => $item->course,
                'certificate_count' => $item->certificate_count,
                'instructor_name' => $item->course->instructor->name ?? 'N/A',
            ];
        })->toArray();
    }

    private function loadRecentActivity()
    {
        $query = Certificate::with(['user', 'course', 'approver', 'rejecter'])
            ->latest();

        // Apply date filter
        $dateFrom = $this->getDateFrom();
        if ($dateFrom) {
            $query->where('created_at', '>=', $dateFrom);
        }

        // Apply course filter
        if ($this->selectedCourse !== 'all') {
            $query->where('course_id', $this->selectedCourse);
        }

        // If user is instructor, only show their course certificates
        if (Auth::user()->hasRole('instructor')) {
            $query->whereHas('course', function($q) {
                $q->where('instructor_id', Auth::id());
            });
        }

        $this->recentActivity = $query->limit(20)->get()->map(function($certificate) {
            return [
                'id' => $certificate->id,
                'user_name' => $certificate->user->name,
                'course_title' => $certificate->course->title,
                'status' => $certificate->status,
                'status_color' => $this->getStatusColor($certificate->status),
                'created_at' => $certificate->created_at,
                'action_text' => $this->getActionText($certificate),
            ];
        })->toArray();
    }

    private function loadMonthlyStats()
    {
        $months = [];
        $dateFrom = $this->getDateFrom() ?? now()->subYear();
        
        for ($i = 0; $i <= $dateFrom->diffInMonths(now()); $i++) {
            $month = $dateFrom->copy()->addMonths($i);
            
            $query = Certificate::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month);

            // Apply filters
            if ($this->selectedCourse !== 'all') {
                $query->where('course_id', $this->selectedCourse);
            }

            if (Auth::user()->hasRole('instructor')) {
                $query->whereHas('course', function($q) {
                    $q->where('instructor_id', Auth::id());
                });
            }

            $months[] = [
                'month' => $month->format('M Y'),
                'total' => (clone $query)->count(),
                'approved' => (clone $query)->where('status', Certificate::STATUS_APPROVED)->count(),
                'pending' => (clone $query)->where('status', Certificate::STATUS_REQUESTED)->count(),
                'rejected' => (clone $query)->where('status', Certificate::STATUS_REJECTED)->count(),
            ];
        }

        $this->monthlyStats = $months;
    }

    private function getStatusColor($status)
    {
        return match($status) {
            Certificate::STATUS_APPROVED => 'green',
            Certificate::STATUS_REQUESTED => 'yellow',
            Certificate::STATUS_REJECTED => 'red',
            Certificate::STATUS_REVOKED => 'gray',
            default => 'blue',
        };
    }

    private function getActionText($certificate)
    {
        return match($certificate->status) {
            Certificate::STATUS_APPROVED => 'earned certificate for',
            Certificate::STATUS_REQUESTED => 'requested certificate for',
            Certificate::STATUS_REJECTED => 'had certificate rejected for',
            Certificate::STATUS_REVOKED => 'had certificate revoked for',
            default => 'submitted certificate for',
        };
    }

    public function getAvailableCoursesProperty()
    {
        $query = Course::select('id', 'title');
        
        // If instructor, only show their courses
        if (Auth::user()->hasRole('instructor')) {
            $query->where('instructor_id', Auth::id());
        }
        
        return $query->orderBy('title')->get();
    }

    public function exportReport()
    {
        // This would generate and download a CSV/Excel report
        // Implementation depends on your export library (e.g., Laravel Excel)
        
        $this->dispatch('notify', [
            'message' => 'Export functionality will be implemented based on your export library.',
            'type' => 'info'
        ]);
    }

    public function render()
    {
        return view('livewire.certificate-management.certificate-analytics', [
            'availableCourses' => $this->availableCourses,
        ]);
    }
}