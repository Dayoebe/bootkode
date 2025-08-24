<?php

namespace App\Livewire\CertificateManagement;

use Livewire\Component;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.dashboard')]
#[Title('Certificate Management')]
class CertificateManagement extends Component
{
    use WithPagination;

    public $selectedCertificate;
    public $showApprovalModal = false;
    public $showRejectionModal = false;
    public $showRevocationModal = false;
    public $rejectionReason = '';
    public $revocationReason = '';
    
    // Filters
    public $statusFilter = 'all';
    public $courseFilter = 'all';
    public $searchTerm = '';
    public $dateFrom = '';
    public $dateTo = '';

    protected $queryString = [
        'statusFilter' => ['except' => 'all'],
        'courseFilter' => ['except' => 'all'],
        'searchTerm' => ['except' => ''],
    ];

    protected $rules = [
        'rejectionReason' => 'required|min:10|max:500',
        'revocationReason' => 'required|min:10|max:500',
    ];

    public function mount()
    {
        // Set default date range (last 3 months)
        $this->dateTo = now()->format('Y-m-d');
        $this->dateFrom = now()->subMonths(3)->format('Y-m-d');
    }

    public function getCertificatesProperty()
    {
        $query = Certificate::with(['user', 'course', 'approver', 'rejecter', 'revoker'])
            ->when($this->statusFilter !== 'all', function($q) {
                $q->where('status', $this->statusFilter);
            })
            ->when($this->courseFilter !== 'all', function($q) {
                $q->where('course_id', $this->courseFilter);
            })
            ->when($this->searchTerm, function($q) {
                $q->whereHas('user', function($subq) {
                    $subq->where('name', 'like', '%' . $this->searchTerm . '%')
                         ->orWhere('email', 'like', '%' . $this->searchTerm . '%');
                })
                ->orWhere('certificate_number', 'like', '%' . $this->searchTerm . '%')
                ->orWhere('verification_code', 'like', '%' . $this->searchTerm . '%');
            })
            ->when($this->dateFrom, function($q) {
                $q->whereDate('created_at', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function($q) {
                $q->whereDate('created_at', '<=', $this->dateTo);
            });

        // If user is instructor, only show certificates for their courses
        if (Auth::user()->isInstructor()) {
            $query->whereHas('course', function($q) {
                $q->where('instructor_id', Auth::id());
            });
        }

        return $query->latest()->paginate(15);
    }

    public function getCoursesProperty()
    {
        $query = Course::select('id', 'title');
        
        // If instructor, only show their courses
        if (Auth::user()->isInstructor()) {
            $query->where('instructor_id', Auth::id());
        }
        
        return $query->orderBy('title')->get();
    }

    public function selectCertificate($certificateId)
    {
        $this->selectedCertificate = Certificate::with(['user', 'course', 'approver', 'rejecter', 'revoker'])
            ->findOrFail($certificateId);
    }

    public function showApprovalModal($certificateId)
    {
        $this->selectCertificate($certificateId);
        $this->showApprovalModal = true;
    }

    public function showRejectionModal($certificateId)
    {
        $this->selectCertificate($certificateId);
        $this->showRejectionModal = true;
        $this->rejectionReason = '';
    }

    public function showRevocationModal($certificateId)
    {
        $this->selectCertificate($certificateId);
        $this->showRevocationModal = true;
        $this->revocationReason = '';
    }

    public function approveCertificate()
    {
        if (!$this->selectedCertificate || !$this->selectedCertificate->isRequested()) {
            $this->dispatch('notify', [
                'message' => 'Certificate cannot be approved.',
                'type' => 'error'
            ]);
            return;
        }

        // Check permission
        if (!$this->canManageCertificate($this->selectedCertificate)) {
            $this->dispatch('notify', [
                'message' => 'You do not have permission to approve this certificate.',
                'type' => 'error'
            ]);
            return;
        }

        try {
            $this->selectedCertificate->approve(Auth::id());
            
            $this->dispatch('notify', [
                'message' => 'Certificate approved successfully!',
                'type' => 'success'
            ]);

            $this->showApprovalModal = false;
            $this->selectedCertificate = null;
            $this->resetPage();

        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'message' => 'Error approving certificate: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function rejectCertificate()
    {
        $this->validate([
            'rejectionReason' => 'required|min:10|max:500'
        ]);

        if (!$this->selectedCertificate || !$this->selectedCertificate->isRequested()) {
            $this->dispatch('notify', [
                'message' => 'Certificate cannot be rejected.',
                'type' => 'error'
            ]);
            return;
        }

        // Check permission
        if (!$this->canManageCertificate($this->selectedCertificate)) {
            $this->dispatch('notify', [
                'message' => 'You do not have permission to reject this certificate.',
                'type' => 'error'
            ]);
            return;
        }

        try {
            $this->selectedCertificate->reject($this->rejectionReason, Auth::id());
            
            $this->dispatch('notify', [
                'message' => 'Certificate rejected.',
                'type' => 'info'
            ]);

            $this->showRejectionModal = false;
            $this->selectedCertificate = null;
            $this->rejectionReason = '';
            $this->resetPage();

        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'message' => 'Error rejecting certificate: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function revokeCertificate()
    {
        $this->validate([
            'revocationReason' => 'required|min:10|max:500'
        ]);

        if (!$this->selectedCertificate || !$this->selectedCertificate->isApproved()) {
            $this->dispatch('notify', [
                'message' => 'Only approved certificates can be revoked.',
                'type' => 'error'
            ]);
            return;
        }

        // Check permission
        if (!$this->canManageCertificate($this->selectedCertificate)) {
            $this->dispatch('notify', [
                'message' => 'You do not have permission to revoke this certificate.',
                'type' => 'error'
            ]);
            return;
        }

        try {
            $this->selectedCertificate->revoke($this->revocationReason, Auth::id());
            
            $this->dispatch('notify', [
                'message' => 'Certificate revoked.',
                'type' => 'warning'
            ]);

            $this->showRevocationModal = false;
            $this->selectedCertificate = null;
            $this->revocationReason = '';
            $this->resetPage();

        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'message' => 'Error revoking certificate: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function bulkApprove($certificateIds)
    {
        $certificates = Certificate::whereIn('id', $certificateIds)
            ->where('status', Certificate::STATUS_REQUESTED)
            ->get();

        $approved = 0;
        foreach ($certificates as $certificate) {
            if ($this->canManageCertificate($certificate)) {
                try {
                    $certificate->approve(Auth::id());
                    $approved++;
                } catch (\Exception $e) {
                    // Log error but continue with others
                    \Log::error('Error bulk approving certificate: ' . $e->getMessage());
                }
            }
        }

        $this->dispatch('notify', [
            'message' => "Approved {$approved} certificates.",
            'type' => 'success'
        ]);

        $this->resetPage();
    }

    private function canManageCertificate($certificate)
    {
        $user = Auth::user();
        
        // Super admin can manage all certificates
        if ($user->isSuperAdmin()) {
            return true;
        }
        
        // Academy admin can manage all certificates
        if ($user->isAcademyAdmin()) {
            return true;
        }
        
        // Instructor can only manage certificates for their courses
        if ($user->isInstructor()) {
            return $certificate->course->instructor_id === $user->id;
        }
        
        return false;
    }

    public function clearFilters()
    {
        $this->statusFilter = 'all';
        $this->courseFilter = 'all';
        $this->searchTerm = '';
        $this->dateFrom = now()->subMonths(3)->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
        $this->resetPage();
    }

    public function closeModals()
    {
        $this->showApprovalModal = false;
        $this->showRejectionModal = false;
        $this->showRevocationModal = false;
        $this->selectedCertificate = null;
        $this->rejectionReason = '';
        $this->revocationReason = '';
    }

    public function render()
    {
        return view('livewire.certificate-management.certificate-management', [
            'certificates' => $this->certificates,
            'courses' => $this->courses,
        ]);
    }
}