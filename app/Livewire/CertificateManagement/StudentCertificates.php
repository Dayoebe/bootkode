<?php

namespace App\Livewire\CertificateManagement;

use Livewire\Component;
use App\Models\Certificate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.dashboard')]
#[Title('My Certificates')]
class StudentCertificates extends Component
{
    use WithPagination;

    public $statusFilter = 'all';
    public $searchTerm = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';

    protected $queryString = [
        'statusFilter' => ['except' => 'all'],
        'searchTerm' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function mount()
    {
        // Any initialization logic
    }

    public function getCertificatesProperty()
    {
        return Auth::user()->certificates()
            ->with(['course', 'course.instructor', 'approver', 'rejecter'])
            ->when($this->statusFilter !== 'all', function($q) {
                $q->where('status', $this->statusFilter);
            })
            ->when($this->searchTerm, function($q) {
                $q->whereHas('course', function($subq) {
                    $subq->where('title', 'like', '%' . $this->searchTerm . '%');
                })
                ->orWhere('certificate_number', 'like', '%' . $this->searchTerm . '%');
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(12);
    }

    public function getStatsProperty()
    {
        $certificates = Auth::user()->certificates();
        
        return [
            'total' => $certificates->count(),
            'approved' => $certificates->where('status', Certificate::STATUS_APPROVED)->count(),
            'pending' => $certificates->where('status', Certificate::STATUS_REQUESTED)->count(),
            'rejected' => $certificates->where('status', Certificate::STATUS_REJECTED)->count(),
        ];
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
        
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->statusFilter = 'all';
        $this->searchTerm = '';
        $this->sortBy = 'created_at';
        $this->sortDirection = 'desc';
        $this->resetPage();
    }

    public function downloadCertificate($certificateId)
    {
        $certificate = Certificate::where('id', $certificateId)
            ->where('user_id', Auth::id())
            ->where('status', Certificate::STATUS_APPROVED)
            ->first();

        if (!$certificate || !$certificate->pdf_path) {
            $this->dispatch('notify', [
                'message' => 'Certificate not available for download.',
                'type' => 'error'
            ]);
            return;
        }

        return redirect()->route('certificate.download', $certificate->verification_code);
    }

    public function viewCertificate($certificateId)
    {
        $certificate = Certificate::where('id', $certificateId)
            ->where('user_id', Auth::id())
            ->first();

        if (!$certificate) {
            $this->dispatch('notify', [
                'message' => 'Certificate not found.',
                'type' => 'error'
            ]);
            return;
        }

        $this->dispatch('open-url', ['url' => route('certificate.view', $certificate->verification_code)]);
    }

    public function requestNewCertificate()
    {
        return redirect()->route('student.certificate.request');
    }

    public function render()
    {
        return view('livewire.certificate-management.student-certificates', [
            'certificates' => $this->certificates,
            'stats' => $this->stats,
        ]);
    }
}