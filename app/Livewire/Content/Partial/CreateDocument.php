<?php

namespace App\Livewire\Content\Partial;

use Livewire\Component;
use App\Models\Document;
use App\Models\DocumentCategory;

class CreateDocument extends Component
{
    public $title = '';
    public $content = '';
    public $excerpt = '';
    public $type = 'guide';
    public $category_id = '';
    public $status = 'draft';
    public $visibility = 'public';
    public $tags = '';
    public $meta_title = '';
    public $meta_description = '';
    public $featured = false;
    public $difficulty_level = 'beginner';
    
    public $currentStep = 1;
    public $totalSteps = 4;

    protected $rules = [
        'title' => 'required|string|max:255',
        'content' => 'required|string',
        'excerpt' => 'nullable|string|max:500',
        'type' => 'required|string',
        'category_id' => 'nullable|exists:document_categories,id',
        'status' => 'required|string',
        'visibility' => 'required|string',
        'tags' => 'nullable|string',
        'meta_title' => 'nullable|string|max:60',
        'meta_description' => 'nullable|string|max:160',
        'featured' => 'boolean',
        'difficulty_level' => 'required|string',
    ];

    public function nextStep()
    {
        $this->validateCurrentStep();
        
        if ($this->currentStep < $this->totalSteps) {
            $this->currentStep++;
        }
    }

    public function previousStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function goToStep($step)
    {
        if ($step >= 1 && $step <= $this->totalSteps) {
            if ($step <= $this->currentStep || $this->validateStepsUpTo($step - 1)) {
                $this->currentStep = $step;
            }
        }
    }

    private function validateCurrentStep()
    {
        switch ($this->currentStep) {
            case 1:
                $this->validate([
                    'title' => 'required|string|max:255',
                    'type' => 'required|string',
                    'category_id' => 'nullable|exists:document_categories,id',
                ]);
                break;
            case 2:
                $this->validate([
                    'content' => 'required|string',
                    'excerpt' => 'nullable|string|max:500',
                ]);
                break;
            case 3:
                $this->validate([
                    'tags' => 'nullable|string',
                    'meta_title' => 'nullable|string|max:60',
                    'meta_description' => 'nullable|string|max:160',
                ]);
                break;
        }
    }

    private function validateStepsUpTo($step)
    {
        try {
            for ($i = 1; $i <= $step; $i++) {
                $this->currentStep = $i;
                $this->validateCurrentStep();
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function saveDraft()
    {
        try {
            $this->status = 'draft';
            $this->save();
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to save draft: ' . $e->getMessage());
        }
    }

    public function saveAndPublish()
    {
        try {
            $this->status = 'published';
            $this->save();
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to publish document: ' . $e->getMessage());
        }
    }

    public function submitForReview()
    {
        try {
            $this->status = 'pending_review';
            $this->save();
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to submit for review: ' . $e->getMessage());
        }
    }

    private function save()
    {
        $this->validate();

        Document::create([
            'title' => $this->title,
            'content' => $this->content,
            'excerpt' => $this->excerpt,
            'type' => $this->type,
            'category_id' => $this->category_id ?: null,
            'status' => $this->status,
            'visibility' => $this->visibility,
            'tags' => $this->tags,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'featured' => $this->featured,
            'difficulty_level' => $this->difficulty_level,
            'created_by' => auth()->id(),
        ]);

        $statusMessage = match($this->status) {
            'draft' => 'Document saved as draft successfully!',
            'published' => 'Document published successfully!',
            'pending_review' => 'Document submitted for review successfully!',
            default => 'Document created successfully!'
        };

        session()->flash('message', $statusMessage);
        $this->reset();
        $this->currentStep = 1;
    }

    public function reset(...$properties)
    {
        $this->title = '';
        $this->content = '';
        $this->excerpt = '';
        $this->type = 'guide';
        $this->category_id = '';
        $this->status = 'draft';
        $this->visibility = 'public';
        $this->tags = '';
        $this->meta_title = '';
        $this->meta_description = '';
        $this->featured = false;
        $this->difficulty_level = 'beginner';
    }

    public function render()
    {
        $categories = DocumentCategory::active()->ordered()->get();
        $types = Document::TYPES;
        $statuses = Document::STATUSES;
        $visibilities = Document::VISIBILITY_LEVELS;
        $difficultyLevels = Document::DIFFICULTY_LEVELS;

        return view('livewire.content.partial.create-document', compact(
            'categories', 'types', 'statuses', 'visibilities', 'difficultyLevels'
        ));
    }
}