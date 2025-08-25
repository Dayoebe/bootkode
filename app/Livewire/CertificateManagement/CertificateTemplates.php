<?php

namespace App\Livewire\CertificateManagement;

use Livewire\Component;
use App\Models\Certificate;
use App\Models\CertificateTemplate;
use Illuminate\Support\Facades\Auth;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.dashboard')]
#[Title('Certificate Templates')]
class CertificateTemplates extends Component
{
    use WithFileUploads;

    public $templates = [];
    public $selectedTemplate = null;
    public $showPreview = false;
    public $previewCertificate = null;
    
    // Template settings
    public $templateName = '';
    public $templateDescription = '';
    public $isDefault = false;
    public $backgroundColor = '#ffffff';
    public $borderColor = '#1a365d';
    public $textColor = '#2d3748';
    public $accentColor = '#4f46e5';
    
    // Font settings
    public $headerFont = 'Playfair Display';
    public $bodyFont = 'Crimson Text';
    public $fontSize = 'medium';
    
    public $showCreateModal = false;
    public $showEditModal = false;

    protected $rules = [
        'templateName' => 'required|min:3|max:100',
        'templateDescription' => 'nullable|max:255',
        'backgroundColor' => 'required|regex:/^#([0-9a-f]{3}|[0-9a-f]{6})$/i',
        'borderColor' => 'required|regex:/^#([0-9a-f]{3}|[0-9a-f]{6})$/i',
        'textColor' => 'required|regex:/^#([0-9a-f]{3}|[0-9a-f]{6})$/i',
        'accentColor' => 'required|regex:/^#([0-9a-f]{3}|[0-9a-f]{6})$/i',
    ];

    public function mount()
    {
        $this->loadTemplates();
    }

    public function loadTemplates()
    {
        // In a real app, you'd load from database
        $this->templates = collect([
            [
                'id' => 1,
                'name' => 'Classic Professional',
                'description' => 'Traditional certificate with formal styling',
                'is_default' => true,
                'preview_url' => '/images/template-classic.png',
                'settings' => [
                    'backgroundColor' => '#ffffff',
                    'borderColor' => '#1a365d',
                    'textColor' => '#2d3748',
                    'accentColor' => '#4f46e5',
                    'headerFont' => 'Playfair Display',
                    'bodyFont' => 'Crimson Text',
                ]
            ],
            [
                'id' => 2,
                'name' => 'Modern Minimalist',
                'description' => 'Clean and modern certificate design',
                'is_default' => false,
                'preview_url' => '/images/template-modern.png',
                'settings' => [
                    'backgroundColor' => '#f7fafc',
                    'borderColor' => '#4a5568',
                    'textColor' => '#1a202c',
                    'accentColor' => '#38b2ac',
                    'headerFont' => 'Inter',
                    'bodyFont' => 'Inter',
                ]
            ],
            [
                'id' => 3,
                'name' => 'Elegant Gold',
                'description' => 'Luxurious certificate with gold accents',
                'is_default' => false,
                'preview_url' => '/images/template-gold.png',
                'settings' => [
                    'backgroundColor' => '#fffbf0',
                    'borderColor' => '#d69e2e',
                    'textColor' => '#2d3748',
                    'accentColor' => '#d69e2e',
                    'headerFont' => 'Playfair Display',
                    'bodyFont' => 'Lora',
                ]
            ],
        ]);
    }

    public function showCreateModal()
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function showEditModal($templateId)
    {
        $template = $this->templates->firstWhere('id', $templateId);
        if ($template) {
            $this->selectedTemplate = $template;
            $this->templateName = $template['name'];
            $this->templateDescription = $template['description'];
            $this->isDefault = $template['is_default'];
            
            // Load template settings
            $settings = $template['settings'];
            $this->backgroundColor = $settings['backgroundColor'];
            $this->borderColor = $settings['borderColor'];
            $this->textColor = $settings['textColor'];
            $this->accentColor = $settings['accentColor'];
            $this->headerFont = $settings['headerFont'];
            $this->bodyFont = $settings['bodyFont'];
            
            $this->showEditModal = true;
        }
    }

    public function saveTemplate()
    {
        $this->validate();

        // In a real app, save to database
        $templateData = [
            'name' => $this->templateName,
            'description' => $this->templateDescription,
            'is_default' => $this->isDefault,
            'settings' => [
                'backgroundColor' => $this->backgroundColor,
                'borderColor' => $this->borderColor,
                'textColor' => $this->textColor,
                'accentColor' => $this->accentColor,
                'headerFont' => $this->headerFont,
                'bodyFont' => $this->bodyFont,
            ]
        ];

        $this->dispatch('notify', [
            'message' => 'Template saved successfully!',
            'type' => 'success'
        ]);

        $this->closeModals();
        $this->loadTemplates();
    }

    public function updateTemplate()
    {
        $this->validate();

        // In a real app, update database
        $this->dispatch('notify', [
            'message' => 'Template updated successfully!',
            'type' => 'success'
        ]);

        $this->closeModals();
        $this->loadTemplates();
    }

    public function setDefaultTemplate($templateId)
    {
        // Only super admin can set default templates
        if (!Auth::user()->isSuperAdmin()) {
            $this->dispatch('notify', [
                'message' => 'Only super administrators can set default templates.',
                'type' => 'error'
            ]);
            return;
        }

        // In a real app, update database
        $this->dispatch('notify', [
            'message' => 'Default template updated!',
            'type' => 'success'
        ]);

        $this->loadTemplates();
    }

    public function deleteTemplate($templateId)
    {
        $template = $this->templates->firstWhere('id', $templateId);
        
        if ($template && $template['is_default']) {
            $this->dispatch('notify', [
                'message' => 'Cannot delete the default template.',
                'type' => 'error'
            ]);
            return;
        }

        // In a real app, delete from database
        $this->dispatch('notify', [
            'message' => 'Template deleted successfully.',
            'type' => 'info'
        ]);

        $this->loadTemplates();
    }

    public function resetForm()
    {
        $this->templateName = '';
        $this->templateDescription = '';
        $this->isDefault = false;
        $this->backgroundColor = '#ffffff';
        $this->borderColor = '#1a365d';
        $this->textColor = '#2d3748';
        $this->accentColor = '#4f46e5';
        $this->headerFont = 'Playfair Display';
        $this->bodyFont = 'Crimson Text';
        $this->selectedTemplate = null;
    }

    public function closeModals()
    {
        $this->showCreateModal = false;
        $this->showEditModal = false;
        $this->showPreview = false;
        $this->resetForm();
    }

    public function previewTemplate($templateId)
    {
        $template = $this->templates->firstWhere('id', $templateId);
        if ($template) {
            $this->selectedTemplate = $template;
            
            // Create a sample certificate for preview with all required properties
            $this->previewCertificate = (object) [
                'certificate_number' => 'CERT-' . strtoupper(uniqid()),
                'verification_code' => 'VERIFY-' . strtoupper(uniqid()),
                'user' => (object) ['name' => 'John Doe'],
                'course' => (object) [
                    'title' => 'Sample Course Title',
                    'subtitle' => 'Sample Course Subtitle',
                    'instructor' => (object) ['name' => 'Jane Smith']
                ],
                'grade' => 'A',
                'completion_date' => now(),
                'issued_date' => now(),
                'credits' => 3,
                'approver' => (object) ['name' => 'Dr. James Wilson'],
                'qr_code_path' => null // Set to null for preview
            ];
            
            $this->showPreview = true;
        }
    }

    public function render()
    {
        $viewData = [];
        
        // Always pass the certificate if we have preview data
        if ($this->previewCertificate) {
            $viewData['certificate'] = $this->previewCertificate;
        }
    
        return view('livewire.certificate-management.certificate-templates', $viewData);
    }
}