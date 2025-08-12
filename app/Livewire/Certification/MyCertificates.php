<?php

namespace App\Livewire\Certification;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Certificate;

use Livewire\Attributes\Layout;

#[Layout('layouts.dashboard', ['title' => 'My Certificates', 'description' => 'View and manage your certificates', 'icon' => 'fas fa-certificate', 'active' => 'certificates.index'])]

class MyCertificates extends Component
{
    use WithPagination;

    public $search = '';
    public $filter = 'all'; // all, approved, pending, rejected
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'filter' => ['except' => 'all'],
        'perPage' => ['except' => 10]
    ];

    public function render()
    {
        $certificates = Certificate::where('user_id', auth()->id())
            ->when($this->search, function ($query) {
                $query->whereHas('course', function ($q) {
                    $q->where('title', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->filter !== 'all', function ($query) {
                $query->where('status', $this->filter);
            })
            ->with(['course', 'template'])
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.certification.my-certificates', [
            'certificates' => $certificates
        ]);
    }

    public function resetFilters()
    {
        $this->reset(['search', 'filter', 'perPage']);
    }
}