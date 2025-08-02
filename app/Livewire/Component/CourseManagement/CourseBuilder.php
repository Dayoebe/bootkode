<?php

namespace App\Livewire\Component\CourseManagement;

use App\Models\Course;
use App\Models\CourseSection;
use App\Models\ContentBlock;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

#[Layout('layouts.dashboard', ['title' => 'Course Builder'])]
class CourseBuilder extends Component
{
    use WithFileUploads;

    // Core properties
    public Course $course;
    public $sections = [];
    
    // Section management
    public $activeSectionId = null;
    public $activeSection = null;
    public $newSectionTitle = '';
    public $isAddingSection = false;
    
    // Content editing
    public $sectionContent = '';
    public $sectionSettings = [
        'estimated_duration' => '',
        'difficulty_level' => 'beginner',
        'status' => 'draft'
    ];
    
    // Content blocks (images, videos, files, etc.)
    public $contentBlocks = [];
    public $uploadingFile = null;
    public $blockType = null; // 'image', 'video', 'file', 'code'
    public $blockData = [];
    
    // UI state
    public $showMediaModal = false;
    public $editingBlockId = null;
    
    // Auto-save
    public $lastSaved = null;
    public $isDirty = false;

    protected function rules()
    {
        return [
            'newSectionTitle' => 'required|string|max:255',
            'sectionContent' => 'nullable|string',
            'sectionSettings.estimated_duration' => 'nullable|string|max:50',
            'sectionSettings.difficulty_level' => 'required|in:beginner,intermediate,advanced',
            'sectionSettings.status' => 'required|in:draft,published,archived',
            'uploadingFile' => 'nullable|file|max:51200', // 50MB max
            'blockData.title' => 'nullable|string|max:255',
            'blockData.description' => 'nullable|string|max:500',
            'blockData.url' => 'nullable|url',
            'blockData.code' => 'nullable|string',
            'blockData.language' => 'nullable|string|max:50',
        ];
    }

    protected $listeners = [
        'reorder-sections' => 'reorderSections',
        'content-changed' => 'markAsDirty',
        'auto-save' => 'autoSave'
    ];

    /**
     * Mount the component
     */
    public function mount(Course $course)
    {
        $this->course = $course;
        $this->loadSections();
        
        // Auto-select first section if available
        if (!empty($this->sections)) {
            $this->selectSection($this->sections[0]['id']);
        }
    }

    /**
     * Load sections with their content blocks
     */
    public function loadSections()
    {
        $this->sections = $this->course->sections()
            ->with(['contentBlocks' => function($query) {
                $query->orderBy('order');
            }])
            ->orderBy('order')
            ->get()
            ->map(function($section) {
                return [
                    'id' => $section->id,
                    'title' => $section->title,
                    'content' => $section->content ?? '',
                    'estimated_duration' => $section->estimated_duration ?? '',
                    'difficulty_level' => $section->difficulty_level ?? 'beginner',
                    'status' => $section->status ?? 'draft',
                    'order' => $section->order,
                    'content_blocks' => $section->contentBlocks->toArray(),
                    'lessons_count' => 0, // For backward compatibility
                    'duration_display' => $this->formatDuration($section->estimated_duration)
                ];
            })
            ->toArray();
    }

    /**
     * Select a section for editing
     */
    public function selectSection($sectionId)
    {
        // Save current section if dirty
        if ($this->isDirty && $this->activeSectionId) {
            $this->saveCurrentSection();
        }

        $this->activeSectionId = $sectionId;
        $this->activeSection = collect($this->sections)->firstWhere('id', $sectionId);
        
        if ($this->activeSection) {
            $this->sectionContent = $this->activeSection['content'];
            $this->sectionSettings = [
                'estimated_duration' => $this->activeSection['estimated_duration'],
                'difficulty_level' => $this->activeSection['difficulty_level'],
                'status' => $this->activeSection['status']
            ];
            $this->contentBlocks = $this->activeSection['content_blocks'];
        }
        
        $this->isDirty = false;
        $this->dispatch('section-selected', $sectionId);
    }

    /**
     * Add a new section
     */
    public function addSection()
    {
        $this->validate(['newSectionTitle' => 'required|string|max:255']);

        $section = $this->course->sections()->create([
            'title' => $this->newSectionTitle,
            'slug' => Str::slug($this->newSectionTitle),
            'content' => '',
            'order' => $this->course->sections()->count(),
            'estimated_duration' => '30 minutes',
            'difficulty_level' => 'beginner',
            'status' => 'draft'
        ]);

        $this->reset(['newSectionTitle', 'isAddingSection']);
        $this->loadSections();
        $this->selectSection($section->id);
        
        $this->dispatch('notify', 'Section added successfully!', 'success');
    }

    /**
     * Delete a section
     */
    public function deleteSection($sectionId)
    {
        $section = CourseSection::findOrFail($sectionId);
        
        // Delete associated content blocks and files
        foreach ($section->contentBlocks as $block) {
            $this->deleteContentBlockFiles($block);
        }
        
        $section->contentBlocks()->delete();
        $section->delete();
        
        // Clear active section if it was deleted
        if ($this->activeSectionId == $sectionId) {
            $this->activeSectionId = null;
            $this->activeSection = null;
            $this->reset(['sectionContent', 'sectionSettings', 'contentBlocks']);
        }
        
        $this->loadSections();
        $this->dispatch('notify', 'Section deleted successfully!', 'success');
    }

    /**
     * Save the current section
     */
    public function saveCurrentSection()
    {
        if (!$this->activeSectionId) {
            return;
        }

        $this->validate([
            'sectionContent' => 'nullable|string',
            'sectionSettings.estimated_duration' => 'nullable|string|max:50',
            'sectionSettings.difficulty_level' => 'required|in:beginner,intermediate,advanced',
            'sectionSettings.status' => 'required|in:draft,published,archived'
        ]);

        $section = CourseSection::findOrFail($this->activeSectionId);
        $section->update([
            'content' => $this->sectionContent,
            'estimated_duration' => $this->sectionSettings['estimated_duration'],
            'difficulty_level' => $this->sectionSettings['difficulty_level'],
            'status' => $this->sectionSettings['status']
        ]);

        $this->isDirty = false;
        $this->lastSaved = now()->format('H:i:s');
        $this->loadSections();
        
        $this->dispatch('notify', 'Section saved successfully!', 'success');
        $this->dispatch('content-saved');
    }

    /**
     * Auto-save functionality
     */
    public function autoSave()
    {
        if ($this->isDirty && $this->activeSectionId) {
            $this->saveCurrentSection();
            $this->dispatch('auto-saved');
        }
    }

    /**
     * Mark content as dirty (changed)
     */
    public function markAsDirty()
    {
        $this->isDirty = true;
    }

    /**
     * Add content block
     */
    public function addContentBlock($type, $position = null)
    {
        if (!$this->activeSectionId) {
            $this->dispatch('notify', 'Please select a section first', 'error');
            return;
        }

        $this->blockType = $type;
        $this->blockData = [
            'title' => '',
            'description' => '',
            'url' => '',
            'code' => '',
            'language' => 'javascript'
        ];
        
        $this->showMediaModal = true;
        $this->dispatch('show-modal', $type);
    }

    /**
     * Save content block
     */
    public function saveContentBlock()
    {
        $rules = [
            'blockData.title' => 'nullable|string|max:255',
            'blockData.description' => 'nullable|string|max:500'
        ];

        // Add specific validation based on block type
        switch ($this->blockType) {
            case 'video':
                $rules['blockData.url'] = 'required|url';
                break;
            case 'code':
                $rules['blockData.code'] = 'required|string';
                $rules['blockData.language'] = 'required|string|max:50';
                break;
            case 'image':
            case 'file':
                if (!$this->uploadingFile && !$this->editingBlockId) {
                    $rules['uploadingFile'] = 'required|file';
                }
                break;
        }

        $this->validate($rules);

        $blockData = [
            'type' => $this->blockType,
            'title' => $this->blockData['title'] ?? '',
            'description' => $this->blockData['description'] ?? '',
            'data' => []
        ];

        // Handle different block types
        switch ($this->blockType) {
            case 'image':
            case 'file':
                if ($this->uploadingFile) {
                    $path = $this->uploadingFile->store('course-content/' . $this->course->id, 'public');
                    $blockData['data'] = [
                        'file_path' => $path,
                        'file_name' => $this->uploadingFile->getClientOriginalName(),
                        'file_size' => $this->uploadingFile->getSize(),
                        'mime_type' => $this->uploadingFile->getMimeType()
                    ];
                }
                break;
                
            case 'video':
                $blockData['data'] = [
                    'url' => $this->blockData['url'],
                    'embed_url' => $this->getVideoEmbedUrl($this->blockData['url'])
                ];
                break;
                
            case 'code':
                $blockData['data'] = [
                    'code' => $this->blockData['code'],
                    'language' => $this->blockData['language']
                ];
                break;
        }

        if ($this->editingBlockId) {
            // Update existing block
            $block = ContentBlock::findOrFail($this->editingBlockId);
            $block->update($blockData);
        } else {
            // Create new block
            $section = CourseSection::findOrFail($this->activeSectionId);
            $blockData['order'] = $section->contentBlocks()->count();
            $section->contentBlocks()->create($blockData);
        }

        $this->resetBlockForm();
        $this->loadSections();
        $this->selectSection($this->activeSectionId);
        
        $this->dispatch('notify', 'Content block saved successfully!', 'success');
    }

    /**
     * Delete content block
     */
    public function deleteContentBlock($blockId)
    {
        $block = ContentBlock::findOrFail($blockId);
        $this->deleteContentBlockFiles($block);
        $block->delete();
        
        $this->loadSections();
        $this->selectSection($this->activeSectionId);
        
        $this->dispatch('notify', 'Content block deleted successfully!', 'success');
    }

    /**
     * Edit content block
     */
    public function editContentBlock($blockId)
    {
        $block = ContentBlock::findOrFail($blockId);
        
        $this->editingBlockId = $blockId;
        $this->blockType = $block->type;
        $this->blockData = [
            'title' => $block->title,
            'description' => $block->description,
            'url' => $block->data['url'] ?? '',
            'code' => $block->data['code'] ?? '',
            'language' => $block->data['language'] ?? 'javascript'
        ];
        
        $this->showMediaModal = true;
        $this->dispatch('show-modal', $block->type);
    }

    /**
     * Reorder sections
     */
    #[On('reorder-sections')]
    public function reorderSections($orderedIds)
    {
        foreach ($orderedIds as $index => $id) {
            CourseSection::where('id', $id)->update(['order' => $index]);
        }
        
        $this->loadSections();
        $this->dispatch('notify', 'Sections reordered successfully!', 'success');
    }

    /**
     * Reorder content blocks
     */
    #[On('reorder-content-blocks')]
    public function reorderContentBlocks($orderedIds)
    {
        foreach ($orderedIds as $index => $id) {
            ContentBlock::where('id', $id)->update(['order' => $index]);
        }
        
        $this->loadSections();
        $this->selectSection($this->activeSectionId);
        
        $this->dispatch('notify', 'Content blocks reordered successfully!', 'success');
    }

    /**
     * Toggle course published status
     */
    public function togglePublished()
    {
        $this->course->is_published = !$this->course->is_published;
        $this->course->save();

        $status = $this->course->is_published ? 'published' : 'draft';
        $this->dispatch('notify', "Course status updated to '{$status}'!", 'success');
    }

    /**
     * Save all sections at once
     */
    public function saveAll()
    {
        DB::transaction(function () {
            if ($this->activeSectionId) {
                $this->saveCurrentSection();
            }
        });
        
        $this->dispatch('notify', 'All changes saved successfully!', 'success');
    }

    /**
     * Reset block form
     */
    private function resetBlockForm()
    {
        $this->reset([
            'showMediaModal',
            'blockType',
            'blockData',
            'uploadingFile',
            'editingBlockId'
        ]);
    }

    /**
     * Delete files associated with content block
     */
    private function deleteContentBlockFiles($block)
    {
        if (isset($block->data['file_path'])) {
            Storage::disk('public')->delete($block->data['file_path']);
        }
    }

    /**
     * Get video embed URL
     */
    private function getVideoEmbedUrl($url)
    {
        if (strpos($url, 'youtube.com') !== false) {
            if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $url, $matches)) {
                return 'https://www.youtube.com/embed/' . $matches[1];
            }
        } elseif (strpos($url, 'vimeo.com') !== false) {
            if (preg_match('/vimeo\.com\/(\d+)/', $url, $matches)) {
                return 'https://player.vimeo.com/video/' . $matches[1];
            }
        }
        
        return $url;
    }

    /**
     * Format duration for display
     */
    private function formatDuration($duration)
    {
        if (!$duration) return '0 min';
        
        // Simple formatting - you can make this more sophisticated
        if (strpos($duration, 'hour') !== false || strpos($duration, 'hr') !== false) {
            return $duration;
        }
        
        return $duration;
    }

    /**
     * Get section statistics
     */
    public function getSectionStats()
    {
        return [
            'total_sections' => count($this->sections),
            'total_duration' => $this->calculateTotalDuration(),
            'published_sections' => collect($this->sections)->where('status', 'published')->count(),
            'draft_sections' => collect($this->sections)->where('status', 'draft')->count()
        ];
    }

    /**
     * Calculate total course duration
     */
    private function calculateTotalDuration()
    {
        $totalMinutes = 0;
        
        foreach ($this->sections as $section) {
            $duration = $section['estimated_duration'];
            if (preg_match('/(\d+)/', $duration, $matches)) {
                $totalMinutes += (int) $matches[1];
            }
        }
        
        if ($totalMinutes >= 60) {
            $hours = floor($totalMinutes / 60);
            $minutes = $totalMinutes % 60;
            return $hours . 'h ' . ($minutes > 0 ? $minutes . 'm' : '');
        }
        
        return $totalMinutes . ' min';
    }

    /**
     * Updated lifecycle hooks for auto-save
     */
    public function updatedSectionContent()
    {
        $this->markAsDirty();
    }

    public function updatedSectionSettings()
    {
        $this->markAsDirty();
    }

    public function render()
    {
        return view('livewire.component.course-management.course-builder', [
            'stats' => $this->getSectionStats()
        ]);
    }
}