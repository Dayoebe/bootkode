<?php

namespace App\Livewire\Certification;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\CertificateTemplate;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;

#[Layout('layouts.dashboard', ['title' => 'List Certificate', 'description' => 'Manage all courses including creation, editing, and deletion', 'icon' => 'fas fa-book', 'active' => 'admin.all-courses'])]

class CertificateTemplates extends Component
{
    use WithPagination, WithFileUploads;

    public $showForm = false;
    public $isEditing = false;
    public $templateId = null;
    
    // Form fields
    public $name;
    public $description;
    public $backgroundImage;
    public $backgroundImagePreview;
    public $contentAreas = [
        ['name' => 'recipient_name', 'x' => 100, 'y' => 200, 'width' => 300, 'height' => 50],
        ['name' => 'course_title', 'x' => 100, 'y' => 260, 'width' => 300, 'height' => 50],
        ['name' => 'issue_date', 'x' => 100, 'y' => 320, 'width' => 300, 'height' => 50],
    ];
    public $defaultFont = 'Arial';
    public $defaultFontSize = 14;
    public $defaultFontColor = '#000000';
    public $isActive = true;

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'backgroundImage' => 'nullable|image|max:2048',
        'contentAreas' => 'required|array|min:1',
        'contentAreas.*.name' => 'required|string',
        'contentAreas.*.x' => 'required|numeric|min:0',
        'contentAreas.*.y' => 'required|numeric|min:0',
        'contentAreas.*.width' => 'required|numeric|min:1',
        'contentAreas.*.height' => 'required|numeric|min:1',
        'defaultFont' => 'required|string',
        'defaultFontSize' => 'required|numeric|min:8|max:72',
        'defaultFontColor' => 'required|string',
        'isActive' => 'required|boolean',
    ];

    public function render()
    {
        $templates = CertificateTemplate::paginate(10);
        return view('livewire.certification.certificate-templates', [
            'templates' => $templates,
            'fontOptions' => ['Arial', 'Times New Roman', 'Courier New', 'Helvetica', 'Verdana']
        ]);
    }

    public function addContentArea()
    {
        $this->contentAreas[] = [
            'name' => '',
            'x' => 100,
            'y' => 100,
            'width' => 300,
            'height' => 50
        ];
    }

    public function removeContentArea($index)
    {
        unset($this->contentAreas[$index]);
        $this->contentAreas = array_values($this->contentAreas);
    }

    public function createTemplate()
    {
        $this->resetForm();
        $this->isEditing = false;
        $this->showForm = true;
    }

    public function editTemplate($id)
    {
        $template = CertificateTemplate::findOrFail($id);
        
        $this->templateId = $template->id;
        $this->name = $template->name;
        $this->description = $template->description;
        $this->backgroundImagePreview = $template->background_image_path;
        $this->contentAreas = $template->content_areas;
        $this->defaultFont = $template->default_font;
        $this->defaultFontSize = $template->default_font_size;
        $this->defaultFontColor = $template->default_font_color;
        $this->isActive = $template->is_active;
        
        $this->isEditing = true;
        $this->showForm = true;
    }

    public function saveTemplate()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'description' => $this->description,
            'content_areas' => $this->contentAreas,
            'default_font' => $this->defaultFont,
            'default_font_size' => $this->defaultFontSize,
            'default_font_color' => $this->defaultFontColor,
            'is_active' => $this->isActive,
        ];

        if ($this->backgroundImage) {
            $path = $this->backgroundImage->store('certificate-templates', 'public');
            $data['background_image_path'] = $path;
        }

        if ($this->isEditing) {
            $template = CertificateTemplate::findOrFail($this->templateId);
            $template->update($data);
            session()->flash('success', 'Template updated successfully!');
        } else {
            CertificateTemplate::create($data);
            session()->flash('success', 'Template created successfully!');
        }

        $this->resetForm();
        $this->showForm = false;
    }

    public function deleteTemplate($id)
    {
        $template = CertificateTemplate::findOrFail($id);
        
        // Delete the background image if it exists
        if ($template->background_image_path) {
            Storage::disk('public')->delete($template->background_image_path);
        }
        
        $template->delete();
        
        session()->flash('success', 'Template deleted successfully!');
    }

    public function toggleTemplateStatus($id)
    {
        $template = CertificateTemplate::findOrFail($id);
        $template->update(['is_active' => !$template->is_active]);
    }

    private function resetForm()
    {
        $this->reset([
            'templateId',
            'name',
            'description',
            'backgroundImage',
            'backgroundImagePreview',
            'contentAreas',
            'defaultFont',
            'defaultFontSize',
            'defaultFontColor',
            'isActive',
        ]);
    }
}