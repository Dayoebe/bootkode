<?php

namespace App\Livewire\Institution;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Core\Institution;
use Illuminate\Support\Facades\Storage;

class WhitelabelSettings extends Component
{
    use WithPagination, WithFileUploads;

    public $selectedInstitution = null;
    public $showEditModal = false;

    // Whitelabel form data
    public $settings = [
        'platform_name' => '',
        'primary_color' => '#3B82F6',
        'secondary_color' => '#1E40AF',
        'logo_url' => '',
        'favicon_url' => '',
        'custom_domain' => '',
        'hide_powered_by' => false,
        'custom_css' => '',
        'email_template_header' => '',
        'email_template_footer' => ''
    ];

    public $logoFile;
    public $faviconFile;

    protected $rules = [
        'settings.platform_name' => 'required|string|max:255',
        'settings.primary_color' => 'required|regex:/^#[A-Fa-f0-9]{6}$/',
        'settings.secondary_color' => 'required|regex:/^#[A-Fa-f0-9]{6}$/',
        'settings.custom_domain' => 'nullable|string|regex:/^[a-zA-Z0-9][a-zA-Z0-9-]{1,61}[a-zA-Z0-9]\.[a-zA-Z]{2,}$/',
        'settings.custom_css' => 'nullable|string',
        'settings.email_template_header' => 'nullable|string',
        'settings.email_template_footer' => 'nullable|string',
        'logoFile' => 'nullable|image|max:2048',
        'faviconFile' => 'nullable|image|max:1024'
    ];

    public function openEditModal($institutionId)
    {
        $this->selectedInstitution = Institution::findOrFail($institutionId);
        $this->settings = array_merge($this->settings, $this->selectedInstitution->whitelabel_settings ?? []);
        $this->showEditModal = true;
    }

    public function closeModal()
    {
        $this->showEditModal = false;
        $this->selectedInstitution = null;
        $this->logoFile = null;
        $this->faviconFile = null;
        $this->resetValidation();
    }

    public function saveSettings()
    {
        $this->validate();

        try {
            // Handle file uploads
            if ($this->logoFile) {
                $logoPath = $this->logoFile->store('whitelabel/logos', 'public');
                $this->settings['logo_url'] = Storage::url($logoPath);
            }

            if ($this->faviconFile) {
                $faviconPath = $this->faviconFile->store('whitelabel/favicons', 'public');
                $this->settings['favicon_url'] = Storage::url($faviconPath);
            }

            // Update institution whitelabel settings
            $this->selectedInstitution->update([
                'whitelabel_settings' => $this->settings
            ]);

            session()->flash('message', 'White-label settings updated successfully!');
            $this->closeModal();

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update settings: ' . $e->getMessage());
        }
    }

    public function resetToDefaults()
    {
        $this->settings = [
            'platform_name' => $this->selectedInstitution->name,
            'primary_color' => '#3B82F6',
            'secondary_color' => '#1E40AF',
            'logo_url' => '',
            'favicon_url' => '',
            'custom_domain' => '',
            'hide_powered_by' => false,
            'custom_css' => '',
            'email_template_header' => '',
            'email_template_footer' => ''
        ];
    }

    public function previewSettings()
    {
        // This would open a preview modal or new tab
        $this->dispatch('open-preview', $this->settings);
    }

    public function render()
    {
        $institutions = Institution::where('status', 'active')
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.institution.whitelabel-settings', [
            'institutions' => $institutions
        ]);
    }
}