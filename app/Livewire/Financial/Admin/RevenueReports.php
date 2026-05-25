<?php

namespace App\Livewire\Financial\Admin;

use App\Services\CommercialReadinessService;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.dashboard')]


class RevenueReports extends Component
{
    public string $dateFrom = '';
    public string $dateTo = '';

    public function mount(): void
    {
        $this->dateFrom = now()->subDays(30)->toDateString();
        $this->dateTo = now()->toDateString();
    }

    public function exportCsv()
    {
        $report = app(CommercialReadinessService::class)->revenueReport($this->dateFrom, $this->dateTo);
        $filename = 'bootkode_revenue_report_' . now()->format('Y_m_d_His') . '.csv';

        return response()->streamDownload(function () use ($report) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Metric', 'Value']);

            foreach ($report['totals'] as $metric => $value) {
                fputcsv($handle, [str($metric)->headline()->toString(), $value]);
            }

            fputcsv($handle, []);
            fputcsv($handle, ['Date', 'Payments', 'Marketplace Orders', 'Refunds', 'Net']);

            foreach ($report['breakdown'] as $row) {
                fputcsv($handle, [$row['date'], $row['payments'], $row['orders'], $row['refunds'], $row['net']]);
            }

            fclose($handle);
        }, $filename);
    }

    public function render()
    {
        return view('livewire.financial.admin.revenue-reports', [
            'report' => app(CommercialReadinessService::class)->revenueReport($this->dateFrom, $this->dateTo),
        ]);
    }
}
