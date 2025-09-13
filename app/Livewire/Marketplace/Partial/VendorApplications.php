<?php 
// app/Livewire/Marketplace/Partial/VendorApplications.php
namespace App\Livewire\Marketplace\Partial;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\MarketplaceItem;
use Illuminate\Support\Facades\DB;

class VendorApplications extends Component
{
    use WithPagination;

    public $status = 'all';
    public $search = '';
    public $showApprovalModal = false;
    public $showRejectionModal = false;
    public $selectedUser = null;
    public $rejectionReason = '';
    public $commissionRate = 80; // Default vendor gets 80%

    protected $queryString = [
        'status' => ['except' => 'all'],
        'search' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function openApprovalModal($userId)
    {
        $this->selectedUser = User::findOrFail($userId);
        $this->showApprovalModal = true;
    }

    public function closeApprovalModal()
    {
        $this->showApprovalModal = false;
        $this->selectedUser = null;
        $this->commissionRate = 80;
    }

    public function openRejectionModal($userId)
    {
        $this->selectedUser = User::findOrFail($userId);
        $this->showRejectionModal = true;
    }

    public function closeRejectionModal()
    {
        $this->showRejectionModal = false;
        $this->selectedUser = null;
        $this->rejectionReason = '';
    }

    public function approveVendor()
    {
        if (!$this->selectedUser) return;

        try {
            DB::beginTransaction();

            // Give vendor permissions by updating role if they're a student
            if ($this->selectedUser->isStudent()) {
                $this->selectedUser->update(['role' => User::ROLE_INSTRUCTOR]);
                $this->selectedUser->syncRoles([User::ROLE_INSTRUCTOR]);
            }

            // Update user metadata to mark as approved vendor
            $metadata = $this->selectedUser->metadata ?? [];
            $metadata['vendor_approved'] = true;
            $metadata['vendor_approved_at'] = now();
            $metadata['vendor_approved_by'] = auth()->id();
            $metadata['vendor_commission_rate'] = $this->commissionRate;
            
            $this->selectedUser->update(['metadata' => $metadata]);

            // Log the approval activity
            activity()
                ->causedBy(auth()->user())
                ->performedOn($this->selectedUser)
                ->withProperties([
                    'commission_rate' => $this->commissionRate,
                    'approved_by' => auth()->user()->name
                ])
                ->log("Approved as marketplace vendor");

            DB::commit();

            // Send notification to the user
            $this->selectedUser->notify(new \App\Notifications\VendorApplicationApproved($this->commissionRate));

            session()->flash('message', "Vendor application approved! {$this->selectedUser->name} can now sell on the marketplace.");
            $this->closeApprovalModal();

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Failed to approve vendor: ' . $e->getMessage());
        }
    }

    public function rejectVendor()
    {
        if (!$this->selectedUser || !$this->rejectionReason) return;

        try {
            // Update user metadata to mark as rejected
            $metadata = $this->selectedUser->metadata ?? [];
            $metadata['vendor_rejected'] = true;
            $metadata['vendor_rejected_at'] = now();
            $metadata['vendor_rejected_by'] = auth()->id();
            $metadata['vendor_rejection_reason'] = $this->rejectionReason;
            
            $this->selectedUser->update(['metadata' => $metadata]);

            // Log the rejection activity
            activity()
                ->causedBy(auth()->user())
                ->performedOn($this->selectedUser)
                ->withProperties([
                    'reason' => $this->rejectionReason,
                    'rejected_by' => auth()->user()->name
                ])
                ->log("Rejected vendor application");

            // Send notification to the user
            $this->selectedUser->notify(new \App\Notifications\VendorApplicationRejected($this->rejectionReason));

            session()->flash('message', "Vendor application rejected. {$this->selectedUser->name} has been notified.");
            $this->closeRejectionModal();

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to reject vendor: ' . $e->getMessage());
        }
    }

    public function suspendVendor($userId)
    {
        $user = User::findOrFail($userId);
        
        if (!$user->canManageCourses()) {
            session()->flash('error', 'User is not a vendor.');
            return;
        }

        // Update metadata to mark as suspended
        $metadata = $user->metadata ?? [];
        $metadata['vendor_suspended'] = true;
        $metadata['vendor_suspended_at'] = now();
        $metadata['vendor_suspended_by'] = auth()->id();
        
        $user->update(['metadata' => $metadata]);

        // Suspend all their active listings
        MarketplaceItem::byVendor($userId)
            ->where('status', MarketplaceItem::STATUS_APPROVED)
            ->update(['status' => MarketplaceItem::STATUS_SUSPENDED]);

        session()->flash('message', "Vendor suspended. All their active listings have been suspended.");
    }

    public function reactivateVendor($userId)
    {
        $user = User::findOrFail($userId);
        
        // Remove suspension from metadata
        $metadata = $user->metadata ?? [];
        unset($metadata['vendor_suspended'], $metadata['vendor_suspended_at'], $metadata['vendor_suspended_by']);
        
        $user->update(['metadata' => $metadata]);

        session()->flash('message', "Vendor reactivated. They can now create and manage listings again.");
    }

    public function getVendorStats($userId)
    {
        return [
            'total_items' => MarketplaceItem::byVendor($userId)->count(),
            'published_items' => MarketplaceItem::byVendor($userId)->published()->count(),
            'pending_items' => MarketplaceItem::byVendor($userId)->where('status', MarketplaceItem::STATUS_PENDING)->count(),
            'total_orders' => \App\Models\MarketplaceOrder::byVendor($userId)->count(),
            'total_earnings' => \App\Models\MarketplaceOrder::byVendor($userId)->where('payment_status', 'paid')->sum('vendor_earning'),
        ];
    }

    public function render()
    {
        // Get users who can potentially be vendors or are already vendors
        $query = User::query()
            ->whereIn('role', [User::ROLE_STUDENT, User::ROLE_INSTRUCTOR, User::ROLE_MENTOR])
            ->with(['marketplaceItems', 'vendorOrders']);

        // Apply search filter
        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        // Apply status filter
        switch ($this->status) {
            case 'pending':
                // Students who might want to become vendors (no vendor metadata)
                $query->where('role', User::ROLE_STUDENT)
                      ->where(function($q) {
                          $q->whereNull('metadata')
                            ->orWhere('metadata', 'NOT LIKE', '%"vendor_approved"%')
                            ->orWhere('metadata', 'NOT LIKE', '%"vendor_rejected"%');
                      });
                break;
            
            case 'approved':
                // Users who are approved vendors or instructors
                $query->where(function($q) {
                    $q->where('role', User::ROLE_INSTRUCTOR)
                      ->orWhere('metadata', 'LIKE', '%"vendor_approved":true%');
                });
                break;
            
            case 'rejected':
                $query->where('metadata', 'LIKE', '%"vendor_rejected":true%');
                break;
            
            case 'suspended':
                $query->where('metadata', 'LIKE', '%"vendor_suspended":true%');
                break;
        }

        $users = $query->latest()->paginate(15);

        // Add stats to each user
        foreach ($users as $user) {
            $user->vendor_stats = $this->getVendorStats($user->id);
            $user->is_vendor = $user->canManageCourses();
            $user->is_suspended = (isset($user->metadata['vendor_suspended']) && $user->metadata['vendor_suspended']);
            $user->is_rejected = (isset($user->metadata['vendor_rejected']) && $user->metadata['vendor_rejected']);
        }

        // Calculate counts for each status
        $totalPending = User::where('role', User::ROLE_STUDENT)
            ->where(function($q) {
                $q->whereNull('metadata')
                  ->orWhere('metadata', 'NOT LIKE', '%"vendor_approved"%')
                  ->orWhere('metadata', 'NOT LIKE', '%"vendor_rejected"%');
            })
            ->count();

        $totalApproved = User::where(function($q) {
                $q->where('role', User::ROLE_INSTRUCTOR)
                  ->orWhere('metadata', 'LIKE', '%"vendor_approved":true%');
            })
            ->count();

        $totalRejected = User::where('metadata', 'LIKE', '%"vendor_rejected":true%')->count();
        $totalSuspended = User::where('metadata', 'LIKE', '%"vendor_suspended":true%')->count();

        return view('livewire.marketplace.partial.vendor-applications', [
            'users' => $users,
            'totalPending' => $totalPending,
            'totalApproved' => $totalApproved,
            'totalRejected' => $totalRejected,
            'totalSuspended' => $totalSuspended,
        ]);
    }
}