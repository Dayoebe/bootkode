<?php

// app/Livewire/Financial/Admin/Partials/PaymentReports.php
namespace App\Livewire\Financial\Admin\Partials;

use Livewire\Component;
use App\Models\PaystackTransaction;
use App\Models\Withdrawal;
use App\Models\WalletTransaction;
use App\Services\WalletService;
use Carbon\Carbon;

class PaymentReports extends Component
{
    public $reportDateRange = 'month';
    public $reportFormat = 'csv';
    public $reportDetails = 'summary';
    public $customStartDate = '';
    public $customEndDate = '';

    public function mount()
    {
        $this->customStartDate = Carbon::now()->subMonth()->format('Y-m-d');
        $this->customEndDate = Carbon::now()->format('Y-m-d');
    }

    public function generateReport($reportType)
    {
        try {
            switch ($reportType) {
                case 'daily_transactions':
                    return $this->generateDailyTransactionsReport();
                case 'payment_summary':
                    return $this->generatePaymentSummaryReport();
                case 'revenue_analysis':
                    return $this->generateRevenueAnalysisReport();
                case 'withdrawal_summary':
                    return $this->generateWithdrawalSummaryReport();
                case 'instructor_payments':
                    return $this->generateInstructorPaymentsReport();
                case 'cash_flow':
                    return $this->generateCashFlowReport();
                default:
                    session()->flash('error', 'Unknown report type');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Report generation failed: ' . $e->getMessage());
        }
    }

    private function generateDailyTransactionsReport()
    {
        $dateRange = $this->getDateRangeForReports();
        
        $transactions = PaystackTransaction::whereBetween('created_at', $dateRange)
            ->orderBy('created_at', 'desc')
            ->get();
        
        $reportData = [
            'title' => 'Daily Transactions Report',
            'date_range' => $dateRange,
            'total_transactions' => $transactions->count(),
            'total_amount' => $transactions->where('status', 'success')->sum('amount'),
            'success_rate' => $transactions->count() > 0 ? 
                round(($transactions->where('status', 'success')->count() / $transactions->count()) * 100, 2) : 0,
            'transactions' => $transactions
        ];
        
        return $this->downloadReport('daily_transactions', $reportData);
    }

    private function generatePaymentSummaryReport()
    {
        $dateRange = $this->getDateRangeForReports();
        
        $summary = [
            'successful_payments' => PaystackTransaction::where('status', 'success')
                ->whereBetween('created_at', $dateRange)->count(),
            'pending_payments' => PaystackTransaction::where('status', 'pending')
                ->whereBetween('created_at', $dateRange)->count(),
            'failed_payments' => PaystackTransaction::where('status', 'failed')
                ->whereBetween('created_at', $dateRange)->count(),
            'total_revenue' => PaystackTransaction::where('status', 'success')
                ->whereBetween('created_at', $dateRange)->sum('amount'),
            'average_transaction' => PaystackTransaction::where('status', 'success')
                ->whereBetween('created_at', $dateRange)->avg('amount') ?? 0,
        ];
        
        return $this->downloadReport('payment_summary', $summary);
    }

    private function generateRevenueAnalysisReport()
    {
        $dateRange = $this->getDateRangeForReports();
        
        $totalRevenue = PaystackTransaction::where('status', 'success')
            ->whereBetween('created_at', $dateRange)
            ->sum('amount');

        $instructorEarnings = WalletTransaction::where('category', WalletTransaction::CATEGORY_INSTRUCTOR_EARNING)
            ->whereBetween('created_at', $dateRange)
            ->sum('amount');

        $platformCommission = $totalRevenue - $instructorEarnings;

        $analytics = [
            'total_course_sales' => $totalRevenue,
            'instructor_earnings' => $instructorEarnings,
            'platform_commission' => $platformCommission,
            'commission_rate' => $totalRevenue > 0 ? round(($platformCommission / $totalRevenue) * 100, 2) : 0,
        ];
        
        return $this->downloadReport('revenue_analysis', $analytics);
    }

    private function generateWithdrawalSummaryReport()
    {
        $dateRange = $this->getDateRangeForReports();
        
        $withdrawals = Withdrawal::whereBetween('created_at', $dateRange)->get();
        
        $summary = [
            'total_withdrawals' => $withdrawals->count(),
            'pending_withdrawals' => $withdrawals->where('status', Withdrawal::STATUS_PENDING)->count(),
            'completed_withdrawals' => $withdrawals->where('status', Withdrawal::STATUS_COMPLETED)->count(),
            'total_amount' => $withdrawals->sum('amount'),
            'completed_amount' => $withdrawals->where('status', Withdrawal::STATUS_COMPLETED)->sum('amount'),
            'pending_amount' => $withdrawals->where('status', Withdrawal::STATUS_PENDING)->sum('amount'),
            'withdrawals' => $withdrawals
        ];
        
        return $this->downloadReport('withdrawal_summary', $summary);
    }

    private function generateInstructorPaymentsReport()
    {
        $dateRange = $this->getDateRangeForReports();
        
        $instructorPayments = WalletTransaction::where('category', WalletTransaction::CATEGORY_INSTRUCTOR_EARNING)
            ->whereBetween('created_at', $dateRange)
            ->with('wallet.user')
            ->get()
            ->groupBy('wallet.user.name');
        
        $reportData = [];
        foreach ($instructorPayments as $instructorName => $payments) {
            $reportData[] = [
                'instructor' => $instructorName,
                'total_earnings' => $payments->sum('amount'),
                'transaction_count' => $payments->count(),
                'average_per_transaction' => round($payments->avg('amount'), 2)
            ];
        }
        
        return $this->downloadReport('instructor_payments', $reportData);
    }

    private function generateCashFlowReport()
    {
        $dateRange = $this->getDateRangeForReports();
        
        $cashIn = PaystackTransaction::where('status', 'success')
            ->whereBetween('created_at', $dateRange)
            ->sum('amount');
            
        $cashOut = Withdrawal::where('status', Withdrawal::STATUS_COMPLETED)
            ->whereBetween('completed_at', $dateRange)
            ->sum('amount');
        
        $cashFlow = [
            'cash_in' => $cashIn,
            'cash_out' => $cashOut,
            'net_cash_flow' => $cashIn - $cashOut,
            'cash_flow_ratio' => $cashOut > 0 ? round(($cashIn / $cashOut) * 100, 2) : 0,
            'period' => $this->reportDateRange
        ];
        
        return $this->downloadReport('cash_flow', $cashFlow);
    }

    public function scheduleReport()
    {
        session()->flash('info', 'Report scheduling feature will be available in the next update');
    }

    private function getDateRangeForReports(): array
    {
        switch ($this->reportDateRange) {
            case 'today':
                return [today()->startOfDay(), today()->endOfDay()];
            case 'yesterday':
                return [yesterday()->startOfDay(), yesterday()->endOfDay()];
            case 'week':
                return [now()->subWeek(), now()];
            case 'month':
                return [now()->subMonth(), now()];
            case 'quarter':
                return [now()->subMonths(3), now()];
            case 'year':
                return [now()->subYear(), now()];
            case 'custom':
                return [
                    Carbon::parse($this->customStartDate)->startOfDay(),
                    Carbon::parse($this->customEndDate)->endOfDay()
                ];
            default:
                return [now()->subMonth(), now()];
        }
    }

    private function downloadReport(string $type, array $data)
    {
        $filename = $type . '_report_' . now()->format('Y_m_d_H_i_s');
        
        switch ($this->reportFormat) {
            case 'pdf':
                session()->flash('info', 'PDF generation will be implemented with a PDF library');
                break;
            case 'excel':
                session()->flash('info', 'Excel generation will be implemented with Laravel Excel');
                break;
            case 'csv':
                return $this->generateCSVReport($filename, $data);
            default:
                session()->flash('error', 'Unsupported report format');
        }
    }

    private function generateCSVReport(string $filename, array $data)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}.csv\"",
        ];
        
        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');
            
            // Add headers based on data structure
            if (isset($data['transactions'])) {
                fputcsv($file, ['Reference', 'Customer', 'Amount', 'Status', 'Date']);
                foreach ($data['transactions'] as $transaction) {
                    fputcsv($file, [
                        $transaction->reference,
                        $transaction->customer_name,
                        $transaction->amount,
                        $transaction->status,
                        $transaction->created_at->format('Y-m-d H:i:s')
                    ]);
                }
            } elseif (isset($data['withdrawals'])) {
                fputcsv($file, ['Withdrawal ID', 'User', 'Amount', 'Status', 'Requested At']);
                foreach ($data['withdrawals'] as $withdrawal) {
                    fputcsv($file, [
                        $withdrawal->withdrawal_id,
                        $withdrawal->user->name,
                        $withdrawal->amount,
                        $withdrawal->status,
                        $withdrawal->requested_at->format('Y-m-d H:i:s')
                    ]);
                }
            } else {
                // Generic key-value export for summary data
                fputcsv($file, ['Metric', 'Value']);
                foreach ($data as $key => $value) {
                    if (!is_array($value) && !is_object($value)) {
                        fputcsv($file, [ucfirst(str_replace('_', ' ', $key)), $value]);
                    }
                }
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    private function getRecentReports()
    {
        // This would typically come from a reports table in your database
        return collect([
            [
                'name' => 'Daily Transactions - ' . now()->format('M d, Y'),
                'type' => 'daily_transactions',
                'generated_at' => now()->subHours(2),
                'status' => 'completed',
                'size' => '2.4 MB'
            ],
            [
                'name' => 'Payment Summary - ' . now()->subDays(1)->format('M d, Y'),
                'type' => 'payment_summary',
                'generated_at' => now()->subDays(1),
                'status' => 'completed',
                'size' => '1.8 MB'
            ],
            [
                'name' => 'Revenue Analysis - ' . now()->format('F Y'),
                'type' => 'revenue_analysis',
                'generated_at' => now()->subDays(3),
                'status' => 'completed',
                'size' => '3.2 MB'
            ],
        ]);
    }

    public function render()
    {
        $recentReports = $this->getRecentReports();

        return view('livewire.financial.admin.partials.payment-reports', [
            'recentReports' => $recentReports,
        ]);
    }
}