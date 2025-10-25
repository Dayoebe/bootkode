<?php

// UPDATED: TemplateManagement.php
namespace App\Livewire\Newsletter\Partials;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Admin\NewsletterCampaign;

class TemplateManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 12;

    // Create/Edit Template
    public $showCreateModal = false;
    public $showEditModal = false;
    public $editingId = null;

    public $name = '';
    public $description = '';
    public $htmlContent = '';
    public $variables = [];
    public $isDefault = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string|max:500',
        'htmlContent' => 'required|string',
        'variables' => 'nullable|array',
        'isDefault' => 'boolean',
    ];

    public function getTemplatesProperty()
    {
        $query = NewsletterCampaign::templates()->with('creator');

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
        }

        return $query->latest()->paginate($this->perPage);
    }

    public function createTemplate()
    {
        $this->validate();

        // If this template is set as default, remove default from others
        if ($this->isDefault) {
            NewsletterCampaign::templates()->update(['is_default' => false]);
        }

        NewsletterCampaign::create([
            'name' => $this->name,
            'subject' => 'Template: ' . $this->name, // Required field
            'description' => $this->description,
            'html_content' => $this->htmlContent,
            'from_name' => 'Template', // Required field
            'from_email' => 'template@bootkode.com', // Required field
            'type' => NewsletterCampaign::TYPE_TEMPLATE,
            'status' => 'active',
            'variables' => $this->variables,
            'is_default' => $this->isDefault,
            'created_by' => auth()->id(),
        ]);

        $this->resetForm();
        session()->flash('message', 'Template created successfully!');
    }

    public function editTemplate($id)
    {
        $template = NewsletterCampaign::templates()->findOrFail($id);

        $this->editingId = $id;
        $this->name = $template->name;
        $this->description = $template->description;
        $this->htmlContent = $template->html_content;
        $this->variables = $template->variables ?? [];
        $this->isDefault = $template->is_default;
        $this->showEditModal = true;
    }

    public function updateTemplate()
    {
        $this->validate();

        $template = NewsletterCampaign::templates()->findOrFail($this->editingId);

        // If this template is set as default, remove default from others
        if ($this->isDefault && !$template->is_default) {
            NewsletterCampaign::templates()->update(['is_default' => false]);
        }

        $template->update([
            'name' => $this->name,
            'description' => $this->description,
            'html_content' => $this->htmlContent,
            'variables' => $this->variables,
            'is_default' => $this->isDefault,
        ]);

        $this->resetForm();
        session()->flash('message', 'Template updated successfully!');
    }

    public function duplicateTemplate($id)
    {
        $original = NewsletterCampaign::templates()->findOrFail($id);

        $duplicate = $original->replicate();
        $duplicate->name = $original->name . ' (Copy)';
        $duplicate->is_default = false;
        $duplicate->created_by = auth()->id();
        $duplicate->save();

        session()->flash('message', 'Template duplicated successfully!');
    }

    public function deleteTemplate($id)
    {
        $template = NewsletterCampaign::templates()->findOrFail($id);

        // Check if template is being used by campaigns
        $campaignCount = NewsletterCampaign::campaigns()
            ->where('html_content', 'like', '%' . $template->name . '%')
            ->count();

        if ($campaignCount > 0) {
            session()->flash('error', 'Cannot delete template that might be used by campaigns.');
            return;
        }

        $template->delete();
        session()->flash('message', 'Template deleted successfully!');
    }

    public function setAsDefault($id)
    {
        NewsletterCampaign::templates()->update(['is_default' => false]);
        NewsletterCampaign::templates()->findOrFail($id)->update(['is_default' => true]);

        session()->flash('message', 'Default template updated successfully!');
    }

    public function addVariable()
    {
        $this->variables[] = [
            'name' => '',
            'label' => '',
            'type' => 'text',
            'default' => '',
            'required' => false
        ];
    }

    public function removeVariable($index)
    {
        unset($this->variables[$index]);
        $this->variables = array_values($this->variables);
    }

    private function resetForm()
    {
        $this->reset([
            'name', 'description', 'htmlContent', 'variables', 'isDefault',
            'showCreateModal', 'showEditModal', 'editingId'
        ]);
    }

    public function render()
    {
        return view('livewire.newsletter.partials.template-management', [
            'templates' => $this->templates,
        ]);
    }
}
