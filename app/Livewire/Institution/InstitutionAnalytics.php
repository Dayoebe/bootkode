<?php

namespace App\Livewire\Institution;

use Livewire\Component;
use App\Models\Core\Institution;
use App\Models\Admin\InstitutionUser;
use App\Models\Learning\CourseEnrollment;
use App\Models\Credentials\Certificate;
use App\Services\InstitutionService;

class InstitutionAnalytics extends Component
{
    public $selectedInstitution = 'all';
    public $dateRange = '30';
    public $analyticsData = [];

    public function mount()
    {
        $this->loadAnalytics();
    }

    public function updatedSelectedInstitution()
    {
        $this->loadAnalytics();
    }

    public function updatedDateRange()
    {
        $this->loadAnalytics();
    }

    public function loadAnalytics()
    {
        try {
            $startDate = now()->subDays((int) $this->dateRange);
            $endDate = now();

            if ($this->selectedInstitution === 'all') {
                $this->analyticsData = $this->getGlobalAnalytics($startDate, $endDate);
            } else {
                $institution = Institution::find($this->selectedInstitution);
                if ($institution) {
                    $this->analyticsData = app(InstitutionService::class)->generateAnalytics($institution);
                    $this->analyticsData['usage_report'] = app(InstitutionService::class)->generateUsageReport($institution, $startDate, $endDate);
                }
            }
        } catch (\Exception $e) {
            \Log::error('Institution Analytics Error', ['error' => $e->getMessage()]);
            $this->analyticsData = [];
        }
    }

    private function getGlobalAnalytics($startDate, $endDate)
    {
        return [
            'institutions' => [
                'total' => Institution::count(),
                'active' => Institution::where('status', 'active')->count(),
                'by_type' => Institution::selectRaw('institution_type, COUNT(*) as count')
                    ->groupBy('institution_type')
                    ->pluck('count', 'institution_type')
                    ->toArray()
            ],
            'users' => [
                'total' => InstitutionUser::count(),
                'active' => InstitutionUser::where('status', 'active')->count(),
                'new_in_period' => InstitutionUser::whereBetween('joined_at', [$startDate, $endDate])->count()
            ],
            'enrollments' => [
                'total' => CourseEnrollment::whereIn('user_id', InstitutionUser::pluck('user_id'))->count(),
                'completed' => CourseEnrollment::whereIn('user_id', InstitutionUser::pluck('user_id'))
                    ->where('is_completed', true)->count(),
                'in_period' => CourseEnrollment::whereIn('user_id', InstitutionUser::pluck('user_id'))
                    ->whereBetween('enrolled_at', [$startDate, $endDate])->count()
            ],
            'certificates' => [
                'total' => Certificate::whereIn('user_id', InstitutionUser::pluck('user_id'))
                    ->where('status', 'approved')->count(),
                'in_period' => Certificate::whereIn('user_id', InstitutionUser::pluck('user_id'))
                    ->where('status', 'approved')
                    ->whereBetween('issued_date', [$startDate, $endDate])->count()
            ],
            'top_institutions' => Institution::withCount(['users' => function($q) {
                    $q->where('status', 'active');
                }])
                ->orderBy('users_count', 'desc')
                ->limit(10)
                ->get(),
            'growth_trend' => $this->getGrowthTrendData()
        ];
    }

    private function getGrowthTrendData()
    {
        $months = [];
        $institutions = [];
        $users = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M Y');

            $institutionCount = Institution::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $institutions[] = $institutionCount;

            $userCount = InstitutionUser::whereYear('joined_at', $date->year)
                ->whereMonth('joined_at', $date->month)
                ->count();
            $users[] = $userCount;
        }

        return [
            'months' => $months,
            'institutions' => $institutions,
            'users' => $users
        ];
    }

    public function exportAnalytics()
    {
        try {
            $data = $this->analyticsData;
            
            return response()->streamDownload(function () use ($data) {
                $csv = "Metric,Value\n";
                
                if (isset($data['institutions'])) {
                    $csv .= "Total Institutions," . $data['institutions']['total'] . "\n";
                    $csv .= "Active Institutions," . $data['institutions']['active'] . "\n";
                }
                
                if (isset($data['users'])) {
                    $csv .= "Total Users," . $data['users']['total'] . "\n";
                    $csv .= "Active Users," . $data['users']['active'] . "\n";
                }
                
                if (isset($data['enrollments'])) {
                    $csv .= "Total Enrollments," . $data['enrollments']['total'] . "\n";
                    $csv .= "Completed Enrollments," . $data['enrollments']['completed'] . "\n";
                }
                
                if (isset($data['certificates'])) {
                    $csv .= "Total Certificates," . $data['certificates']['total'] . "\n";
                }
                
                echo $csv;
            }, 'institution-analytics-' . now()->format('Y-m-d') . '.csv');

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to export analytics: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.institution.institution-analytics', [
            'institutions' => Institution::where('status', 'active')->get()
        ]);
    }
}