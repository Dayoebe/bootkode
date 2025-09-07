<?php

namespace App\Livewire\Financial\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.dashboard')]


class RevenueReports extends Component
{
    public function render()
    {
        return view('livewire.financial.admin.revenue-reports');
    }
}
