<?php

namespace App\Livewire\CertificateManagement;

use App\Livewire\Concerns\AuthorizesCertificateAdministration;
use App\Models\Credentials\CertificateTemplate;
use App\Services\CertificateService;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.dashboard')]
#[Title('Certificate Templates')]
class CertificateTemplates extends Component
{
    use AuthorizesCertificateAdministration;

    #[Locked]
    public ?int $selectedTemplate = null;

    #[Locked]
    public string $previewHtml = '';

    public bool $showEditor = false;
    public bool $showPreview = false;
    public string $templateName = '';
    public string $templateDescription = '';
    public bool $isDefault = false;
    public string $backgroundColor = '#ffffff';
    public string $borderColor = '#0f172a';
    public string $textColor = '#0f172a';
    public string $accentColor = '#0f766e';
    public string $headerFont = 'DejaVu Serif';
    public string $bodyFont = 'DejaVu Sans';

    #[Computed]
    public function templates()
    {
        return CertificateTemplate::orderByDesc('default_slot')->orderBy('name')->get();
    }

    protected function rules(): array
    {
        return [
            'templateName' => ['required', 'string', 'min:3', 'max:100'],
            'templateDescription' => ['nullable', 'string', 'max:255'],
            'isDefault' => ['boolean'],
            'backgroundColor' => ['required', 'regex:/^#(?:[a-f0-9]{3}|[a-f0-9]{6})$/iD'],
            'borderColor' => ['required', 'regex:/^#(?:[a-f0-9]{3}|[a-f0-9]{6})$/iD'],
            'textColor' => ['required', 'regex:/^#(?:[a-f0-9]{3}|[a-f0-9]{6})$/iD'],
            'accentColor' => ['required', 'regex:/^#(?:[a-f0-9]{3}|[a-f0-9]{6})$/iD'],
            'headerFont' => ['required', Rule::in(CertificateTemplate::FONTS)],
            'bodyFont' => ['required', Rule::in(CertificateTemplate::FONTS)],
        ];
    }

    protected function authorizeManagement(): void
    {
        abort_unless(auth()->user()?->hasAnyRole(['super_admin', 'academy_admin']), 403);
    }

    public function showCreateModal(): void
    {
        $this->authorizeManagement();
        $this->resetForm();
        $this->showPreview = false;
        $this->showEditor = true;
    }

    public function showEditModal(int $templateId): void
    {
        $this->authorizeManagement();
        $template = CertificateTemplate::findOrFail($templateId);
        $this->resetForm();
        $this->selectedTemplate = $template->id;
        $this->templateName = $template->name;
        $this->templateDescription = $template->description ?? '';
        $this->isDefault = $template->is_default;
        foreach (CertificateTemplate::safeSettings($template->settings) as $key => $value) {
            $this->{$key} = $value;
        }
        $this->showPreview = false;
        $this->showEditor = true;
    }

    public function saveTemplate(): void
    {
        $this->authorizeManagement();
        abort_if($this->selectedTemplate !== null, 422);
        $this->persistTemplate();
    }

    public function updateTemplate(): void
    {
        $this->authorizeManagement();
        abort_if($this->selectedTemplate === null, 422);
        $this->persistTemplate($this->selectedTemplate);
    }

    protected function persistTemplate(?int $id = null): void
    {
        $this->validate();
        $settings = [];
        foreach (CertificateTemplate::DEFAULT_SETTINGS as $key => $fallback) {
            $settings[$key] = $this->{$key};
        }
        // Editing a default preserves its status; only super admins can assign it.
        $alreadyDefault = $id !== null && CertificateTemplate::findOrFail($id)->is_default;
        app(CertificateService::class)->saveTemplate([
            'name' => $this->templateName,
            'description' => $this->templateDescription,
            'settings' => $settings,
        ], $id, $this->isDefault && !$alreadyDefault);
        $this->closeModals();
        unset($this->templates);
        $this->dispatch('notify', message: 'Certificate template saved.', type: 'success');
    }

    public function setDefaultTemplate(int $templateId): void
    {
        app(CertificateService::class)->setDefaultTemplate($templateId);
        unset($this->templates);
        $this->dispatch('notify', message: 'Default template updated for future certificates.', type: 'success');
    }

    public function deleteTemplate(int $templateId): void
    {
        app(CertificateService::class)->deleteTemplate($templateId);
        unset($this->templates);
        $this->dispatch('notify', message: 'Certificate template deleted. Issued designs are preserved.', type: 'success');
    }

    public function resetForm(): void
    {
        $this->resetValidation();
        $this->reset('selectedTemplate', 'templateName', 'templateDescription', 'isDefault');
        foreach (CertificateTemplate::DEFAULT_SETTINGS as $key => $value) {
            $this->{$key} = $value;
        }
    }

    public function closeModals(): void
    {
        $this->showEditor = false;
        $this->showPreview = false;
        $this->previewHtml = '';
        $this->resetForm();
    }

    public function previewTemplate(int $templateId): void
    {
        $template = CertificateTemplate::findOrFail($templateId);
        // Explicitly labelled design placeholders, never learner records or credentials.
        $preview = (object) [
            'certificate_number' => 'Not issued', 'verification_code' => 'DESIGN PREVIEW',
            'verification_url' => 'Not issued', 'user' => (object) ['name' => 'Recipient name'],
            'course' => (object) ['title' => 'Course title', 'subtitle' => null,
                'instructor' => (object) ['name' => 'Instructor name']],
            'grade' => null, 'completion_date' => null, 'issued_date' => null,
            'credits' => null, 'approver' => null, 'qr_code_path' => null,
            'metadata' => ['template_snapshot' => ['settings' => $template->settings]],
        ];
        $this->previewHtml = view('certificates.public-view', [
            'certificate' => $preview, 'isPdf' => false, 'isPreview' => true,
        ])->render();
        $this->showEditor = false;
        $this->showPreview = true;
    }

    public function render()
    {
        return view('livewire.certificate-management.certificate-templates');
    }
}
