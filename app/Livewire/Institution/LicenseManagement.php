<?php

namespace App\Livewire\Institution;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Core\Institution;
use App\Services\InstitutionService;

class LicenseManagement extends Component
{
    use WithPagination;

    // Filters
    public $statusFilter = 'all';
    public $expiryFilter = 'all';
    public $sortBy = 'license_end_date';
    public $sortDirection = 'asc';
    public $perPage = 10;

    // Modal states
    public $showRenewModal = false;
    public $showUpgradeModal = false;
    public $selectedInstitution = null;

    // Form data
    public $newEndDate;
    public $newLicenseType;
    public $newMaxUsers;
    public $renewalReason;

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedExpiryFilter()
    {
        $this->resetPage();
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

    public function openRenewModal($institutionId)
    {
        $this->selectedInstitution = Institution::findOrFail($institutionId);
        $this->newEndDate = $this->selectedInstitution->license_end_date?->addYear()->format('Y-m-d') ?? now()->addYear()->format('Y-m-d');
        $this->renewalReason = '';
        $this->showRenewModal = true;
    }

    public function openUpgradeModal($institutionId)
    {
        $this->selectedInstitution = Institution::findOrFail($institutionId);
        $this->selectedInstitution->updateUserCount();
        $this->newLicenseType = $this->selectedInstitution->license_type;
        $this->newMaxUsers = $this->selectedInstitution->max_users;
        $this->showUpgradeModal = true;
    }

    public function updatedNewLicenseType()
    {
        if ($this->newLicenseType === 'custom') {
            return;
        }

        $this->newMaxUsers = match($this->newLicenseType) {
            'basic' => 100,
            'standard' => 500,
            'premium' => 1000,
            'enterprise' => 999999,
            default => $this->newMaxUsers,
        };
    }

    public function closeModals()
    {
        $this->showRenewModal = false;
        $this->showUpgradeModal = false;
        $this->selectedInstitution = null;
        $this->resetValidation();
    }

    public function renewLicense()
    {
        $this->validate([
            'newEndDate' => 'required|date|after:today',
            'renewalReason' => 'required|string|max:500'
        ]);

        try {
            $oldEndDate = $this->selectedInstitution->license_end_date;

            $this->selectedInstitution->update([
                'license_end_date' => $this->newEndDate,
                'status' => 'active'
            ]);

            // Log the renewal
            $this->selectedInstitution->licenseHistory()->create([
                'action' => 'renewed',
                'old_values' => ['license_end_date' => $oldEndDate?->toDateString()],
                'new_values' => ['license_end_date' => $this->newEndDate],
                'reason' => $this->renewalReason,
                'performed_by' => auth()->id(),
                'performed_at' => now()
            ]);

            session()->flash('message', 'License renewed successfully!');
            $this->closeModals();

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to renew license: ' . $e->getMessage());
        }
    }

    public function upgradeLicense()
    {
        $this->validate([
            'newLicenseType' => 'required|in:basic,standard,premium,enterprise,custom',
            'newMaxUsers' => 'required|integer|min:1'
        ]);

        try {
            $this->selectedInstitution->updateUserCount();

            if ($this->newLicenseType !== 'enterprise' && (int) $this->newMaxUsers < (int) $this->selectedInstitution->current_users) {
                throw new \RuntimeException('The new license limit is below the institution seats already in use.');
            }

            $oldValues = [
                'license_type' => $this->selectedInstitution->license_type,
                'max_users' => $this->selectedInstitution->max_users
            ];

            $this->selectedInstitution->update([
                'license_type' => $this->newLicenseType,
                'max_users' => $this->newMaxUsers
            ]);

            // Log the upgrade
            $this->selectedInstitution->licenseHistory()->create([
                'action' => 'upgraded',
                'old_values' => $oldValues,
                'new_values' => [
                    'license_type' => $this->newLicenseType,
                    'max_users' => $this->newMaxUsers
                ],
                'performed_by' => auth()->id(),
                'performed_at' => now()
            ]);

            session()->flash('message', 'License upgraded successfully!');
            $this->closeModals();

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to upgrade license: ' . $e->getMessage());
        }
    }

    public function sendExpiryNotifications()
    {
        try {
            app(InstitutionService::class)->checkExpiringLicenses();
            session()->flash('message', 'Expiry notifications sent successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to send notifications: ' . $e->getMessage());
        }
    }

    private function getInstitutionsQuery()
    {
        $query = Institution::query();

        // Apply status filter
        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        // Apply expiry filter
        if ($this->expiryFilter === 'expiring') {
            $query->whereBetween('license_end_date', [now(), now()->addDays(30)]);
        } elseif ($this->expiryFilter === 'expired') {
            $query->where('license_end_date', '<', now());
        }

        // Apply sorting
        $query->orderBy($this->sortBy, $this->sortDirection);

        return $query;
    }

    public function render()
    {
        $institutions = $this->getInstitutionsQuery()->paginate($this->perPage);

        return view('livewire.institution.license-management', [
            'institutions' => $institutions,
            'licenseTypes' => Institution::LICENSE_TYPES
        ]);
    }
}
