<?php

namespace App\Livewire\Content\Partial;

use Livewire\Component;
use App\Models\Document;
use App\Models\LearningMaterial;

class Localization extends Component
{
    public $selectedLanguage = 'en';
    public $selectedContentType = 'documents';
    public $search = '';
    
    public $showTranslationModal = false;
    public $selectedContent = null;
    public $contentType = '';
    public $targetLanguage = '';
    public $translatedTitle = '';
    public $translatedContent = '';
    public $translationProgress = [];

    public $availableLanguages = [
        'en' => 'English',
        'es' => 'Spanish',
        'fr' => 'French',
        'de' => 'German',
        'it' => 'Italian',
        'pt' => 'Portuguese',
        'ru' => 'Russian',
        'zh' => 'Chinese',
        'ja' => 'Japanese',
        'ko' => 'Korean',
        'ar' => 'Arabic',
        'hi' => 'Hindi',
    ];

    protected $rules = [
        'translatedTitle' => 'required|string|max:255',
        'translatedContent' => 'required|string',
        'targetLanguage' => 'required|string',
    ];

    public function mount()
    {
        $this->loadTranslationProgress();
    }

    private function loadTranslationProgress()
    {
        // In a real app, this would come from a translations table
        $this->translationProgress = [
            'documents' => [
                'en' => 100,
                'es' => 75,
                'fr' => 60,
                'de' => 45,
                'pt' => 30,
            ],
            'materials' => [
                'en' => 100,
                'es' => 65,
                'fr' => 50,
                'de' => 35,
                'pt' => 20,
            ],
        ];
    }

    public function openTranslationModal($contentId, $type, $language)
    {
        $this->contentType = $type;
        $this->targetLanguage = $language;
        
        switch ($type) {
            case 'document':
                $this->selectedContent = Document::findOrFail($contentId);
                break;
            case 'material':
                $this->selectedContent = LearningMaterial::findOrFail($contentId);
                break;
        }
        
        $this->translatedTitle = '';
        $this->translatedContent = '';
        $this->showTranslationModal = true;
    }

    public function closeTranslationModal()
    {
        $this->showTranslationModal = false;
        $this->selectedContent = null;
        $this->contentType = '';
        $this->targetLanguage = '';
        $this->translatedTitle = '';
        $this->translatedContent = '';
    }

    public function saveTranslation()
    {
        $this->validate();

        try {
            // In a real app, you'd save to a translations table
            // For now, we'll just show a success message
            
            session()->flash('message', 'Translation saved successfully!');
            $this->closeTranslationModal();
            
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to save translation: ' . $e->getMessage());
        }
    }

    public function autoTranslate()
    {
        // Simulate auto-translation (in real app, integrate with Google Translate API)
        $this->translatedTitle = '[AUTO] ' . $this->selectedContent->title;
        $this->translatedContent = '[AUTO TRANSLATED] ' . $this->selectedContent->content ?? $this->selectedContent->description;
        
        session()->flash('message', 'Auto-translation completed! Please review and edit as needed.');
    }

    public function exportTranslations()
    {
        try {
            // In a real app, generate and download translation files
            session()->flash('message', 'Translation files exported successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to export translations: ' . $e->getMessage());
        }
    }

    public function importTranslations()
    {
        try {
            // In a real app, handle translation file imports
            session()->flash('message', 'Translation files imported successfully!');
            $this->loadTranslationProgress();
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to import translations: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $content = collect();

        if ($this->selectedContentType === 'documents') {
            $documents = Document::query()
                ->when($this->search, function($query) {
                    $query->where('title', 'like', '%' . $this->search . '%');
                })
                ->where('language', $this->selectedLanguage)
                ->with('creator')
                ->get()
                ->map(function($item) {
                    $item->content_type = 'document';
                    return $item;
                });
            $content = $documents;
        } else {
            $materials = LearningMaterial::query()
                ->when($this->search, function($query) {
                    $query->where('title', 'like', '%' . $this->search . '%');
                })
                ->with('creator')
                ->get()
                ->map(function($item) {
                    $item->content_type = 'material';
                    $item->language = 'en'; // Default for materials
                    return $item;
                });
            $content = $materials;
        }

        $contentTypes = [
            'documents' => 'Documents',
            'materials' => 'Learning Materials',
        ];

        return view('livewire.content.partial.localization', compact('content', 'contentTypes'));
    }
}