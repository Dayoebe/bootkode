<?php

namespace App\Livewire\Certification;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Certificate;
use Illuminate\Support\Facades\Mail;
use App\Mail\CertificateApproved;
use App\Mail\CertificateRejected;
use Livewire\Attributes\Layout;

#[Layout('layouts.dashboard', ['title' => 'Certificate Approvals', 'description' => 'Manage certificate approval requests', 'icon' => 'fas fa-check-circle', 'active' => 'certificates.approvals'])]

class CertificateApprovals extends Component
{
    use WithPagination;

    public $search = '';
    public $filterStatus = 'pending'; // pending, approved, rejected
    public $perPage = 10;
    public $showRejectionModal = false;
    public $rejectionReason = '';
    public $selectedCertificateId = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'filterStatus' => ['except' => 'pending'],
        'perPage' => ['except' => 10]
    ];

    public function render()
    {
        if (!auth()->user()->hasPermissionTo('manage_certificates')) {
            abort(403, 'Unauthorized action.');
        }

        $certificates = Certificate::with(['user', 'course', 'template'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->whereHas('user', function ($userQuery) {
                        $userQuery->where('name', 'like', '%'.$this->search.'%')
                                  ->orWhere('email', 'like', '%'.$this->search.'%');
                    })->orWhereHas('course', function ($courseQuery) {
                        $courseQuery->where('title', 'like', '%'.$this->search.'%');
                    });
                });
            })
            ->where('status', $this->filterStatus)
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.certification.certificate-approvals', [
            'certificates' => $certificates
        ]);
    }

    public function approveCertificate($certificateId)
    {
        if (!auth()->user()->hasPermissionTo('manage_certificates')) {
            $this->addError('auth', 'You are not authorized to approve certificates.');
            return;
        }

        $certificate = Certificate::findOrFail($certificateId);
        
        $certificate->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'rejection_reason' => null
        ]);

        if ($certificate->user->email) {
            Mail::to($certificate->user->email)->queue(new CertificateApproved($certificate));
        }

        session()->flash('success', 'Certificate approved successfully!');
    }

    public function openRejectionModal($certificateId)
    {
        if (!auth()->user()->hasPermissionTo('manage_certificates')) {
            $this->addError('auth', 'You are not authorized to reject certificates.');
            return;
        }

        $this->selectedCertificateId = $certificateId;
        $this->rejectionReason = '';
        $this->showRejectionModal = true;
    }

    public function rejectCertificate()
    {
        if (!auth()->user()->hasPermissionTo('manage_certificates')) {
            $this->addError('auth', 'You are not authorized to reject certificates.');
            return;
        }

        $this->validate([
            'rejectionReason' => 'required|string|min:10|max:500'
        ]);

        $certificate = Certificate::findOrFail($this->selectedCertificateId);
        
        $certificate->update([
            'status' => 'rejected',
            'rejection_reason' => $this->rejectionReason,
            'approved_by' => null,
            'approved_at' => null
        ]);

        if ($certificate->user->email) {
            Mail::to($certificate->user->email)->queue(new CertificateRejected($certificate));
        }

        $this->showRejectionModal = false;
        session()->flash('success', 'Certificate rejected successfully!');
    }

    public function resetFilters()
    {
        $this->reset(['search', 'filterStatus', 'perPage']);
    }
}