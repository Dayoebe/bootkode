<?php

namespace App\Livewire\Institution;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Core\Institution;
use App\Models\Core\User;
use Illuminate\Support\Str;
use App\Services\InstitutionService;
use Illuminate\Support\Facades\Storage;

class PartnerInstitutions extends Component
{
    use WithPagination, WithFileUploads;

    // Filters and search
    public $search = '';
    public $statusFilter = 'all';
    public $typeFilter = 'all';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 10;

    // Modal states
    public $showCreateModal = false;
    public $showEditModal = false;
    public $showViewModal = false;
    public $showDeleteModal = false;
    public $selectedInstitution = null;

    // Form data
    public $form = [
        'name' => '',
        'email' => '',
        'phone' => '',
        'address' => '',
        'city' => '',
        'state' => '',
        'country' => '',
        'postal_code' => '',
        'institution_type' => 'school',
        'website' => '',
        'description' => '',
        'license_type' => 'basic',
        'max_users' => 100,
        'license_start_date' => '',
        'license_end_date' => '',
        'admin_email' => '',
        'billing_email' => '',
        'billing_address' => ''
    ];
    
    public $logo;
    public $existingLogo = '';

    protected $rules = [
        'form.name' => 'required|string|max:255',
        'form.email' => 'required|email|unique:institutions,email',
        'form.phone' => 'nullable|string|max:20',
        'form.address' => 'nullable|string|max:500',
        'form.city' => 'nullable|string|max:100',
        'form.state' => 'nullable|string|max:100',
        'form.country' => 'nullable|string|max:100',
        'form.postal_code' => 'nullable|string|max:20',
        'form.institution_type' => 'required|in:university,college,school,training_center,corporate,government,non_profit,other',
        'form.website' => 'nullable|url|max:255',
        'form.description' => 'nullable|string|max:1000',
        'form.license_type' => 'required|in:basic,standard,premium,enterprise,custom',
        'form.max_users' => 'required|integer|min:1',
        'form.license_start_date' => 'nullable|date',
        'form.license_end_date' => 'nullable|date|after:form.license_start_date',
        'form.admin_email' => 'required|email',
        'form.billing_email' => 'nullable|email',
        'form.billing_address' => 'nullable|string|max:500',
        'logo' => 'nullable|image|max:2048'
    ];

    public function mount()
    {
        // Set default license dates
        $this->form['license_start_date'] = now()->format('Y-m-d');
        $this->form['license_end_date'] = now()->addYear()->format('Y-m-d');
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedTypeFilter()
    {
        $this->resetPage();
    }

    public function updatedFormLicenseType()
    {
        // Update max users based on license type
        $this->form['max_users'] = match($this->form['license_type']) {
            'basic' => 100,
            'standard' => 500,
            'premium' => 1000,
            'enterprise' => 999999,
            'custom' => $this->form['max_users']
        };
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

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function openEditModal($institutionId)
    {
        $institution = Institution::findOrFail($institutionId);
        $this->selectedInstitution = $institution;
        $this->fillForm($institution);
        $this->showEditModal = true;
    }

    public function openViewModal($institutionId)
    {
        $this->selectedInstitution = Institution::with(['adminUser', 'users', 'creator'])
            ->findOrFail($institutionId);
        $this->showViewModal = true;
    }

    public function openDeleteModal($institutionId)
    {
        $this->selectedInstitution = Institution::findOrFail($institutionId);
        $this->showDeleteModal = true;
    }

    public function closeModals()
    {
        $this->showCreateModal = false;
        $this->showEditModal = false;
        $this->showViewModal = false;
        $this->showDeleteModal = false;
        $this->selectedInstitution = null;
        $this->resetForm();
        $this->resetValidation();
    }

    public function create()
    {
        $this->validate();

        try {
            // Handle logo upload
            $logoPath = null;
            if ($this->logo) {
                $logoPath = $this->logo->store('institution-logos', 'public');
            }

            // Find or create admin user
            $adminUser = User::where('email', $this->form['admin_email'])->first();
            if (!$adminUser) {
                $adminUser = User::create([
                    'name' => explode('@', $this->form['admin_email'])[0],
                    'email' => $this->form['admin_email'],
                    'password' => bcrypt(Str::random(16)), // Temporary password
                    'role' => User::ROLE_ACADEMY_ADMIN
                ]);
                
                // Send welcome email with temporary password reset link
                $adminUser->sendEmailVerificationNotification();
            }

            $institution = Institution::create(array_merge($this->form, [
                'logo' => $logoPath,
                'admin_user_id' => $adminUser->id,
                'created_by' => auth()->id()
            ]));

            // Create admin user relationship
            $institution->users()->create([
                'user_id' => $adminUser->id,
                'role' => 'admin',
                'status' => 'active',
                'added_by' => auth()->id()
            ]);

            app(InstitutionService::class)->sendWelcomeNotification($institution);

            session()->flash('message', 'Institution created successfully!');
            $this->closeModals();

        } catch (\Exception $e) {
            \Log::error('Error creating institution', ['error' => $e->getMessage()]);
            session()->flash('error', 'Failed to create institution. Please try again.');
        }
    }

    public function update()
    {
        $rules = $this->rules;
        $rules['form.email'] = 'required|email|unique:institutions,email,' . $this->selectedInstitution->id;
        
        $this->validate($rules);

        try {
            $updateData = $this->form;

            // Handle logo upload
            if ($this->logo) {
                // Delete old logo
                if ($this->selectedInstitution->logo) {
                    Storage::disk('public')->delete($this->selectedInstitution->logo);
                }
                $updateData['logo'] = $this->logo->store('institution-logos', 'public');
            }

            $this->selectedInstitution->update($updateData);

            session()->flash('message', 'Institution updated successfully!');
            $this->closeModals();

        } catch (\Exception $e) {
            \Log::error('Error updating institution', ['error' => $e->getMessage()]);
            session()->flash('error', 'Failed to update institution. Please try again.');
        }
    }

    public function delete()
    {
        try {
            // Delete logo file
            if ($this->selectedInstitution->logo) {
                Storage::disk('public')->delete($this->selectedInstitution->logo);
            }

            $this->selectedInstitution->delete();

            session()->flash('message', 'Institution deleted successfully!');
            $this->closeModals();

        } catch (\Exception $e) {
            \Log::error('Error deleting institution', ['error' => $e->getMessage()]);
            session()->flash('error', 'Failed to delete institution. Please try again.');
        }
    }

    public function approve($institutionId)
    {
        try {
            $institution = Institution::findOrFail($institutionId);
            $institution->activate();

            session()->flash('message', 'Institution approved successfully!');

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to approve institution.');
        }
    }

    public function suspend($institutionId)
    {
        try {
            $institution = Institution::findOrFail($institutionId);
            $institution->suspend('Suspended by administrator');

            session()->flash('message', 'Institution suspended successfully!');

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to suspend institution.');
        }
    }

    public function unsuspend($institutionId)
{
    try {
        $institution = Institution::findOrFail($institutionId);
        $institution->update(['status' => 'active']);

        // Log the unsuspension in license history
        $institution->licenseHistory()->create([
            'action' => 'activated',
            'reason' => 'Unsuspended by administrator',
            'performed_by' => auth()->id(),
            'performed_at' => now()
        ]);

        session()->flash('message', 'Institution unsuspended successfully!');

    } catch (\Exception $e) {
        session()->flash('error', 'Failed to unsuspend institution.');
    }
}
    public function export()
    {
        return response()->streamDownload(function () {
            $institutions = $this->getInstitutionsQuery()->get();
            
            $csv = "Name,Email,Type,Status,Users,License Type,Created At\n";
            
            foreach ($institutions as $institution) {
                $csv .= sprintf(
                    "%s,%s,%s,%s,%d,%s,%s\n",
                    $institution->name,
                    $institution->email,
                    $institution->institution_type_name,
                    $institution->status_name,
                    $institution->current_users,
                    $institution->license_type_name,
                    $institution->created_at->format('Y-m-d H:i:s')
                );
            }
            
            echo $csv;
        }, 'institutions-export-' . now()->format('Y-m-d') . '.csv');
    }

    private function resetForm()
    {
        $this->form = [
            'name' => '',
            'email' => '',
            'phone' => '',
            'address' => '',
            'city' => '',
            'state' => '',
            'country' => '',
            'postal_code' => '',
            'institution_type' => 'school',
            'website' => '',
            'description' => '',
            'license_type' => 'basic',
            'max_users' => 100,
            'license_start_date' => now()->format('Y-m-d'),
            'license_end_date' => now()->addYear()->format('Y-m-d'),
            'admin_email' => '',
            'billing_email' => '',
            'billing_address' => ''
        ];
        $this->logo = null;
        $this->existingLogo = '';
    }

    private function fillForm($institution)
    {
        $this->form = [
            'name' => $institution->name,
            'email' => $institution->email,
            'phone' => $institution->phone,
            'address' => $institution->address,
            'city' => $institution->city,
            'state' => $institution->state,
            'country' => $institution->country,
            'postal_code' => $institution->postal_code,
            'institution_type' => $institution->institution_type,
            'website' => $institution->website,
            'description' => $institution->description,
            'license_type' => $institution->license_type,
            'max_users' => $institution->max_users,
            'license_start_date' => $institution->license_start_date?->format('Y-m-d') ?? '',
            'license_end_date' => $institution->license_end_date?->format('Y-m-d') ?? '',
            'admin_email' => $institution->adminUser?->email ?? '',
            'billing_email' => $institution->billing_email,
            'billing_address' => $institution->billing_address
        ];
        $this->existingLogo = $institution->logo ?? '';
        $this->logo = null;
    }

    private function getInstitutionsQuery()
    {
        $query = Institution::with(['adminUser', 'users'])
            ->withCount(['users as active_users_count' => function($q) {
                $q->where('status', 'active');
            }]);

        // Apply search
        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
                  ->orWhere('city', 'like', '%' . $this->search . '%');
            });
        }

        // Apply filters
        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        if ($this->typeFilter !== 'all') {
            $query->where('institution_type', $this->typeFilter);
        }

        // Apply sorting
        $query->orderBy($this->sortBy, $this->sortDirection);

        return $query;
    }

    public function render()
    {
        $institutions = $this->getInstitutionsQuery()->paginate($this->perPage);

        return view('livewire.institution.partner-institutions', [
            'institutions' => $institutions,
            'institutionTypes' => Institution::INSTITUTION_TYPES,
            'licenseTypes' => Institution::LICENSE_TYPES,
            'statuses' => Institution::STATUSES
        ]);
    }
}