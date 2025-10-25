<?php

namespace App\Livewire\Institution;

use Livewire\Component;
use App\Models\Core\User;
use App\Models\Core\Institution;
use App\Models\Admin\InstitutionUser;
use App\Models\Admin\BulkEnrollmentBatch;
use App\Models\Learning\CourseEnrollment;
use App\Models\Credentials\Certificate;
use Illuminate\Support\Facades\Route;
use Livewire\Attributes\Layout;

#[Layout('layouts.dashboard', ['title' => 'Institution Portal', 'description' => 'Manage institutional partnerships and licensing', 'icon' => 'fas fa-university', 'active' => 'institution.portal'])]

class InstitutionPortal extends Component
{
    public $activeTab = 'overview';
    public $user;

    // Statistics
    public $stats = [
        'total_institutions' => 0,
        'active_institutions' => 0,
        'total_users' => 0,
        'pending_approvals' => 0,
        'expiring_licenses' => 0,
        'total_revenue' => 0,
        'recent_enrollments' => 0,
        'certificates_issued' => 0,
    ];

    // Quick filters
    public $selectedPeriod = '30';
    public $selectedInstitutionType = 'all';

    public function mount()
    {
        $this->user = auth()->user();

        // Check permissions
        if (!$this->user->isSuperAdmin() && !$this->user->isAcademyAdmin()) {
            abort(403, 'Unauthorized access to Institution Portal');
        }

        // Set active tab based on route
        $currentRoute = Route::currentRouteName();
        $this->activeTab = match ($currentRoute) {
            'institution.overview' => 'overview',
            'institution.partners' => 'partners',
            'institution.licenses' => 'licenses',
            'institution.bulk-enrollment' => 'bulk-enrollment',
            'institution.analytics' => 'analytics',
            'institution.whitelabel' => 'whitelabel',
            'institution.settings' => 'settings',
            default => 'overview'
        };

        $this->loadStatistics();
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        $this->loadStatistics();
    }

    public function updatedSelectedPeriod()
    {
        $this->loadStatistics();
    }

    public function updatedSelectedInstitutionType()
    {
        $this->loadStatistics();
    }

    protected function loadStatistics()
    {
        try {
            $periodDays = (int) $this->selectedPeriod;
            $periodStart = now()->subDays($periodDays);

            // Base query for institutions
            $institutionsQuery = Institution::query();
            
            if ($this->selectedInstitutionType !== 'all') {
                $institutionsQuery->where('institution_type', $this->selectedInstitutionType);
            }

            // Basic institution stats
            $this->stats['total_institutions'] = $institutionsQuery->count();
            $this->stats['active_institutions'] = $institutionsQuery->where('status', 'active')->count();
            $this->stats['pending_approvals'] = $institutionsQuery->where('status', 'pending')->count();
            
            // License expiry warnings (next 30 days)
            $this->stats['expiring_licenses'] = $institutionsQuery
                ->where('status', 'active')
                ->whereBetween('license_end_date', [now(), now()->addDays(30)])
                ->count();

            // User stats
            $institutionIds = $institutionsQuery->pluck('id');
            $this->stats['total_users'] = InstitutionUser::whereIn('institution_id', $institutionIds)
                ->where('status', 'active')
                ->count();

            // Revenue calculation (placeholder - integrate with your billing system)
            $this->stats['total_revenue'] = $this->stats['active_institutions'] * 100; // $100 per institution

            // Recent enrollments
            $this->stats['recent_enrollments'] = CourseEnrollment::whereIn('user_id',
                InstitutionUser::whereIn('institution_id', $institutionIds)->pluck('user_id')
            )->where('enrolled_at', '>=', $periodStart)->count();

            // Certificates issued
            $this->stats['certificates_issued'] = Certificate::whereIn('user_id',
                InstitutionUser::whereIn('institution_id', $institutionIds)->pluck('user_id')
            )->where('issued_date', '>=', $periodStart)->count();

        } catch (\Exception $e) {
            // Fallback if models don't exist yet or database errors
            $this->stats = [
                'total_institutions' => 0,
                'active_institutions' => 0,
                'total_users' => 0,
                'pending_approvals' => 0,
                'expiring_licenses' => 0,
                'total_revenue' => 0,
                'recent_enrollments' => 0,
                'certificates_issued' => 0,
            ];
            
            \Log::warning('Institution Portal: Error loading statistics', [
                'error' => $e->getMessage(),
                'user_id' => $this->user->id
            ]);
        }
    }

    public function refreshStats()
    {
        $this->loadStatistics();
        session()->flash('message', 'Statistics refreshed successfully!');
    }

    public function getRecentActivities()
    {
        try {
            $activities = collect();

            // Recent institution approvals
            $recentApprovals = Institution::where('approved_at', '>=', now()->subDays(7))
                ->with('approver')
                ->latest('approved_at')
                ->limit(5)
                ->get()
                ->map(function ($institution) {
                    return [
                        'type' => 'approval',
                        'icon' => 'fas fa-check-circle',
                        'color' => 'green',
                        'title' => "Institution Approved",
                        'description' => "{$institution->name} was approved",
                        'time' => $institution->approved_at,
                        'user' => $institution->approver?->name ?? 'System'
                    ];
                });

            // Recent bulk enrollments
            $recentBulkEnrollments = BulkEnrollmentBatch::where('completed_at', '>=', now()->subDays(7))
                ->with(['institution', 'creator'])
                ->latest('completed_at')
                ->limit(5)
                ->get()
                ->map(function ($batch) {
                    return [
                        'type' => 'bulk_enrollment',
                        'icon' => 'fas fa-users',
                        'color' => 'blue',
                        'title' => "Bulk Enrollment Completed",
                        'description' => "{$batch->successful_enrollments} users enrolled at {$batch->institution->name}",
                        'time' => $batch->completed_at,
                        'user' => $batch->creator?->name ?? 'System'
                    ];
                });

            // Recent license renewals (from history)
            $recentRenewals = \DB::table('institution_license_histories')
                ->join('institutions', 'institution_license_histories.institution_id', '=', 'institutions.id')
                ->join('users', 'institution_license_histories.performed_by', '=', 'users.id')
                ->where('institution_license_histories.action', 'renewed')
                ->where('institution_license_histories.performed_at', '>=', now()->subDays(7))
                ->select([
                    'institutions.name as institution_name',
                    'users.name as performer_name',
                    'institution_license_histories.performed_at',
                    'institution_license_histories.action'
                ])
                ->latest('institution_license_histories.performed_at')
                ->limit(5)
                ->get()
                ->map(function ($record) {
                    return [
                        'type' => 'license_renewal',
                        'icon' => 'fas fa-key',
                        'color' => 'purple',
                        'title' => "License Renewed",
                        'description' => "License renewed for {$record->institution_name}",
                        'time' => \Carbon\Carbon::parse($record->performed_at),
                        'user' => $record->performer_name
                    ];
                });

            return $activities
                ->merge($recentApprovals)
                ->merge($recentBulkEnrollments)
                ->merge($recentRenewals)
                ->sortByDesc('time')
                ->take(10)
                ->values();

        } catch (\Exception $e) {
            \Log::warning('Institution Portal: Error loading recent activities', [
                'error' => $e->getMessage()
            ]);
            return collect();
        }
    }

    public function getTopInstitutionsByUsers($limit = 5)
    {
        try {
            return Institution::withCount(['users' => function ($query) {
                $query->where('status', 'active');
            }])
            ->where('status', 'active')
            ->orderBy('users_count', 'desc')
            ->limit($limit)
            ->get();
        } catch (\Exception $e) {
            return collect();
        }
    }

    public function getInstitutionTypeBreakdown()
    {
        try {
            return Institution::selectRaw('institution_type, COUNT(*) as count')
                ->where('status', 'active')
                ->groupBy('institution_type')
                ->orderBy('count', 'desc')
                ->get()
                ->mapWithKeys(function ($item) {
                    $typeName = Institution::INSTITUTION_TYPES[$item->institution_type] ?? ucfirst($item->institution_type);
                    return [$typeName => $item->count];
                });
        } catch (\Exception $e) {
            return collect();
        }
    }

    public function getLicenseStatusBreakdown()
    {
        try {
            $stats = [
                'Active' => 0,
                'Expiring Soon' => 0,
                'Expired' => 0,
                'Suspended' => 0
            ];

            $institutions = Institution::select(['status', 'license_end_date'])->get();

            foreach ($institutions as $institution) {
                if ($institution->status === 'suspended') {
                    $stats['Suspended']++;
                } elseif ($institution->status === 'active') {
                    if ($institution->license_end_date) {
                        $daysUntilExpiry = now()->diffInDays($institution->license_end_date, false);
                        if ($daysUntilExpiry <= 0) {
                            $stats['Expired']++;
                        } elseif ($daysUntilExpiry <= 30) {
                            $stats['Expiring Soon']++;
                        } else {
                            $stats['Active']++;
                        }
                    } else {
                        $stats['Active']++;
                    }
                }
            }

            return collect($stats);
        } catch (\Exception $e) {
            return collect([
                'Active' => 0,
                'Expiring Soon' => 0,
                'Expired' => 0,
                'Suspended' => 0
            ]);
        }
    }

    public function getMonthlyGrowthData()
    {
        try {
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
        } catch (\Exception $e) {
            return [
                'months' => [],
                'institutions' => [],
                'users' => []
            ];
        }
    }

    public function render()
    {
        return view('livewire.institution.institution-portal', [
            'user' => $this->user,
            'stats' => $this->stats,
            'recentActivities' => $this->getRecentActivities(),
            'topInstitutions' => $this->getTopInstitutionsByUsers(),
            'institutionTypeBreakdown' => $this->getInstitutionTypeBreakdown(),
            'licenseStatusBreakdown' => $this->getLicenseStatusBreakdown(),
            'monthlyGrowthData' => $this->getMonthlyGrowthData(),
            'institutionTypes' => Institution::INSTITUTION_TYPES
        ]);
    }
}