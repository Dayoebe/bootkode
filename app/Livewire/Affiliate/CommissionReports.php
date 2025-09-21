<?php

// app/Livewire/Affiliate/CommissionReports.php
namespace App\Livewire\Affiliate;

use Livewire\Component;
use App\Models\ReferralTransaction;
use App\Models\Referral;
use Carbon\Carbon;
use Livewire\Attributes\Layout;

#[Layout('layouts.dashboard', ['title' => 'Affiliate Commission Report'])]
class CommissionReports extends Component
{
    public $reportType = 'overview';
    public $dateFrom = '';
    public $dateTo = '';
    public $exportFormat = 'csv';

    public function mount()
    {
        $user = auth()->user();
        
        if (!$user->isAffiliate()) {
            return redirect()->route('affiliate.dashboard');
        }
        
        $this->dateFrom = Carbon::now()->subDays(30)->format('Y-m-d');
        $this->dateTo = Carbon::now()->format('Y-m-d');
    }

    public function setReportType($type)
    {
        $this->reportType = $type;
    }

    public function exportReport()
    {
        $user = auth()->user();
        
        if (!$user->isAffiliate()) {
            return;
        }

        // Generate export data based on report type and format
        $data = $this->getReportData();
        
        if ($this->exportFormat === 'csv') {
            return response()->streamDownload(function() use ($data) {
                $handle = fopen('php://output', 'w');
                
                // Add headers based on report type
                $headers = match($this->reportType) {
                    'commissions' => ['Date', 'Course', 'Student', 'Commission', 'Status'],
                    'referrals' => ['Date', 'Referred User', 'Email', 'Total Spent', 'Total Commission', 'Status'],
                    'performance' => ['Period', 'Referrals', 'Sales', 'Commission', 'Conversion Rate'],
                    default => ['Date', 'Description', 'Amount']
                };
                
                fputcsv($handle, $headers);
                
                foreach ($data as $row) {
                    fputcsv($handle, $row);
                }
                
                fclose($handle);
            }, "affiliate-report-{$this->reportType}-" . now()->format('Y-m-d') . '.csv');
        }
    }

    private function getReportData()
    {
        $user = auth()->user();
        $affiliate = $user->affiliate;
        
        $startDate = Carbon::parse($this->dateFrom);
        $endDate = Carbon::parse($this->dateTo);

        return match($this->reportType) {
            'commissions' => $this->getCommissionData($affiliate->id, $startDate, $endDate),
            'referrals' => $this->getReferralData($affiliate->id, $startDate, $endDate),
            'performance' => $this->getPerformanceData($affiliate->id, $startDate, $endDate),
            default => []
        };
    }

    private function getCommissionData($affiliateId, $startDate, $endDate)
    {
        return ReferralTransaction::whereHas('referral', function($q) use ($affiliateId) {
                $q->where('affiliate_id', $affiliateId);
            })
            ->with(['course', 'referral.referredUser'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get()
            ->map(function($transaction) {
                return [
                    $transaction->created_at->format('Y-m-d'),
                    $transaction->course->title,
                    $transaction->referral->referredUser->name,
                    '₦' . number_format($transaction->commission_amount, 2),
                    ucfirst($transaction->status)
                ];
            })
            ->toArray();
    }

    private function getReferralData($affiliateId, $startDate, $endDate)
    {
        return Referral::where('affiliate_id', $affiliateId)
            ->with('referredUser')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get()
            ->map(function($referral) {
                return [
                    $referral->created_at->format('Y-m-d'),
                    $referral->referredUser->name,
                    $referral->referredUser->email,
                    '₦' . number_format($referral->total_spent, 2),
                    '₦' . number_format($referral->total_commission_earned, 2),
                    ucfirst($referral->status)
                ];
            })
            ->toArray();
    }

    private function getPerformanceData($affiliateId, $startDate, $endDate)
    {
        // Weekly performance breakdown
        $weeks = [];
        $current = $startDate->copy()->startOfWeek();
        
        while ($current->lte($endDate)) {
            $weekEnd = $current->copy()->endOfWeek();
            
            $referrals = Referral::where('affiliate_id', $affiliateId)
                ->whereBetween('created_at', [$current, $weekEnd])
                ->count();
                
            $commissions = ReferralTransaction::whereHas('referral', function($q) use ($affiliateId) {
                    $q->where('affiliate_id', $affiliateId);
                })
                ->where('status', ReferralTransaction::STATUS_PAID)
                ->whereBetween('paid_at', [$current, $weekEnd])
                ->sum('commission_amount');
                
            $sales = ReferralTransaction::whereHas('referral', function($q) use ($affiliateId) {
                    $q->where('affiliate_id', $affiliateId);
                })
                ->whereBetween('created_at', [$current, $weekEnd])
                ->count();
                
            $conversionRate = $referrals > 0 ? round(($sales / $referrals) * 100, 2) : 0;
            
            $weeks[] = [
                $current->format('M d') . ' - ' . $weekEnd->format('M d'),
                $referrals,
                $sales,
                '₦' . number_format($commissions, 2),
                $conversionRate . '%'
            ];
            
            $current->addWeek();
        }
        
        return $weeks;
    }

    public function render()
    {
        $user = auth()->user();
        $affiliate = $user->affiliate;
        $startDate = Carbon::parse($this->dateFrom);
        $endDate = Carbon::parse($this->dateTo);

        // Get report data for display
        $reportData = $this->getReportData();
        
        // Calculate summary statistics
        $totalCommissions = ReferralTransaction::whereHas('referral', function($q) use ($affiliate) {
                $q->where('affiliate_id', $affiliate->id);
            })
            ->where('status', ReferralTransaction::STATUS_PAID)
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->sum('commission_amount');

        $totalReferrals = Referral::where('affiliate_id', $affiliate->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $totalSales = ReferralTransaction::whereHas('referral', function($q) use ($affiliate) {
                $q->where('affiliate_id', $affiliate->id);
            })
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        return view('livewire.affiliate.commission-reports', [
            'reportData' => $reportData,
            'summaryStats' => [
                'total_commissions' => $totalCommissions,
                'total_referrals' => $totalReferrals,
                'total_sales' => $totalSales,
                'conversion_rate' => $totalReferrals > 0 ? round(($totalSales / $totalReferrals) * 100, 2) : 0
            ]
        ]);
    }
}