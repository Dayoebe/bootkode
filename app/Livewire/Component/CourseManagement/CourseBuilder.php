<?php

namespace App\Livewire\Component\CourseManagement;

use App\Models\Course;
use App\Models\CourseSection;
use App\Models\CourseLesson;
use App\Models\Module;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\Option;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app', ['title' => 'Course Builder'])]
class CourseBuilder extends Component
{
    use WithFileUploads;

    public Course $course;
    
    // Section management
    public $newSectionTitle = '';
    public $newSectionDescription = '';
    public $isAddingSection = false;
    public $editingSectionId = null;
    public $editingSectionTitle = '';
    public $editingSectionDescription = '';
    
    // Lesson management
    public $activeLesson = null;
    public $activeLessonContent = '';
    public $activeLessonFile = null;
    public $newLessonTitle = '';
    public $newLessonDescription = '';
    public $newLessonContentType = 'text';
    public $newLessonDuration = 0;
    public $isAddingLessonToSectionId = null;
    public $editingLessonId = null;
    public $editingLessonTitle = '';
    public $editingLessonDescription = '';
    
    // Media management
    public $showImageModal = false;
    public $showVideoModal = false;
    public $showFileModal = false;
    public $showAudioModal = false;
    public $mediaFile = null;
    public $mediaCaption = '';
    public $videoUrl = '';
    public $videoTitle = '';
    public $fileDescription = '';
    public $audioTitle = '';
    
    // Quiz management
    public $showQuizModal = false;
    public $newQuizTitle = '';
    public $newQuizDescription = '';
    public $newQuizPassPercentage = 70;
    public $editingQuizId = null;
    public $activeQuiz = null;
    public $newQuestionText = '';
    public $newQuestionType = 'multiple_choice';
    public $newQuestionOptions = ['', '', '', ''];
    public $correctOptionIndex = 0;
    public $newQuestionCorrectAnswer = '';
    
    // Content blocks
    public $contentBlocks = [];
    
    // Auto-save
    public $autoSaveMessage = '';
    public $isDirty = false;
    
    // Search and filter
    public $searchTerm = '';
    public $filterType = 'all';
    
    // Bulk operations
    public $selectedLessons = [];
    public $showBulkActions = false;

    protected function rules()
    {
        return [
            'newSectionTitle' => 'required|string|max:255',
            'newSectionDescription' => 'nullable|string|max:1000',
            'newLessonTitle' => 'required|string|max:255',
            'newLessonDescription' => 'nullable|string|max:1000',
            'newLessonDuration' => 'nullable|integer|min:0|max:1440',
            'activeLessonContent' => 'nullable',
            'mediaFile' => 'nullable|file|max:102400', // 100MB max
            'videoUrl' => 'nullable|url',
            'editingSectionTitle' => 'required|string|max:255',
            'editingSectionDescription' => 'nullable|string|max:1000',
            'editingLessonTitle' => 'required|string|max:255',
            'editingLessonDescription' => 'nullable|string|max:1000',
            'newQuizTitle' => 'required|string|max:255',
            'newQuizDescription' => 'nullable|string|max:1000',
            'newQuizPassPercentage' => 'required|integer|min:1|max:100',
            'newQuestionText' => 'required|string|max:1000',
            'newQuestionType' => 'required|in:multiple_choice,true_false,short_answer,essay',
            'newQuestionCorrectAnswer' => 'required_if:newQuestionType,short_answer,essay',
        ];
    }

    public function mount(Course $course)
    {
        // Check if user has permission to edit this course
        if (!Auth::user()->isInstructor() && !Auth::user()->isAcademyAdmin() && !Auth::user()->isSuperAdmin()) {
            abort(403, 'Unauthorized access to course builder.');
        }
        
        if (Auth::user()->isInstructor() && $course->instructor_id !== Auth::id()) {
            abort(403, 'You can only edit your own courses.');
        }

        $this->course = $course;
        $this->course->load(['sections.lessons', 'modules.lessons', 'modules.quizzes.questions.options']);
        
        // Select first lesson if available
        if ($this->course->sections->isNotEmpty() && $this->course->sections->first()->lessons->isNotEmpty()) {
            $firstLesson = $this->course->sections->first()->lessons->first();
            $this->selectLesson($firstLesson->id);
        }
    }

    public function selectLesson($lessonId)
    {
        // Save current lesson before switching
        if ($this->activeLesson && $this->isDirty) {
            $this->autoSave();
        }

        $this->activeLesson = CourseLesson::find($lessonId);
        $this->isAddingLessonToSectionId = null;
        $this->editingLessonId = null;
        
        if ($this->activeLesson) {
            $content = is_string($this->activeLesson->content) 
                ? json_decode($this->activeLesson->content, true) 
                : $this->activeLesson->content;
                
            $this->activeLessonContent = $content['body'] ?? '';
            $this->contentBlocks = $content['blocks'] ?? [];
        } else {
            $this->activeLessonContent = '';
            $this->contentBlocks = [];
        }
        
        $this->resetMediaFields();
        $this->isDirty = false;
        $this->dispatch('lesson-selected', $lessonId);
    }

    public function clearActiveLesson()
    {
        if ($this->activeLesson && $this->isDirty) {
            $this->autoSave();
        }
        
        $this->reset(['activeLesson', 'activeLessonContent', 'contentBlocks', 'isDirty']);
        $this->resetMediaFields();
    }

    private function resetMediaFields()
    {
        $this->reset([
            'mediaFile', 'mediaCaption', 'videoUrl', 'videoTitle', 
            'fileDescription', 'audioTitle', 'showImageModal', 
            'showVideoModal', 'showFileModal', 'showAudioModal'
        ]);
    }

    public function saveContent()
    {
        if (!$this->activeLesson) {
            $this->dispatch('notify', 'Please select a lesson first.', 'error');
            return;
        }

        $content = [
            'body' => $this->activeLessonContent,
            'blocks' => $this->contentBlocks,
            'last_updated' => now()->toISOString(),
            'word_count' => str_word_count(strip_tags($this->activeLessonContent))
        ];

        $this->activeLesson->update(['content' => $content]);
        
        $this->isDirty = false;
        $this->autoSaveMessage = 'Content saved successfully!';
        $this->dispatch('notify', 'Lesson content saved successfully!', 'success');
        $this->dispatch('content-saved');
        
        // Update course last modified
        $this->course->touch();
        
        // Clear auto-save message after 3 seconds
        $this->dispatch('clear-auto-save-message');
    }

    public function autoSave()
    {
        if ($this->activeLesson && $this->isDirty) {
            $content = [
                'body' => $this->activeLessonContent,
                'blocks' => $this->contentBlocks,
                'last_updated' => now()->toISOString(),
                'word_count' => str_word_count(strip_tags($this->activeLessonContent))
            ];

            $this->activeLesson->update(['content' => $content]);
            $this->autoSaveMessage = 'Auto-saved at ' . now()->format('g:i A');
            $this->dispatch('auto-saved');
            $this->isDirty = false;
        }
    }

    // Section Management
    public function showAddSectionForm()
    {
        $this->isAddingSection = true;
        $this->reset(['newSectionTitle', 'newSectionDescription']);
    }

    public function cancelAddSection()
    {
        $this->isAddingSection = false;
        $this->reset(['newSectionTitle', 'newSectionDescription']);
    }

    public function addSection()
    {
        $this->validate([
            'newSectionTitle' => 'required|string|max:255',
            'newSectionDescription' => 'nullable|string|max:1000'
        ]);
        
        $this->course->sections()->create([
            'title' => $this->newSectionTitle,
            'description' => $this->newSectionDescription,
            'order' => $this->course->sections()->count(),
        ]);
    
        $this->reset(['newSectionTitle', 'newSectionDescription', 'isAddingSection']);
        $this->course->load('sections.lessons');
        $this->dispatch('notify', 'New section added successfully!', 'success');
    }

    public function editSection($sectionId)
    {
        $section = CourseSection::find($sectionId);
        $this->editingSectionId = $sectionId;
        $this->editingSectionTitle = $section->title;
        $this->editingSectionDescription = $section->description;
    }

    public function updateSection()
    {
        $this->validate([
            'editingSectionTitle' => 'required|string|max:255',
            'editingSectionDescription' => 'nullable|string|max:1000'
        ]);
        
        $section = CourseSection::find($this->editingSectionId);
        $section->update([
            'title' => $this->editingSectionTitle,
            'description' => $this->editingSectionDescription
        ]);
        
        $this->reset(['editingSectionId', 'editingSectionTitle', 'editingSectionDescription']);
        $this->course->load('sections.lessons');
        $this->dispatch('notify', 'Section updated successfully!', 'success');
    }

    public function cancelEditSection()
    {
        $this->reset(['editingSectionId', 'editingSectionTitle', 'editingSectionDescription']);
    }

    public function deleteSection($sectionId)
    {
        $this->dispatch('confirm-delete', [
            'title' => 'Delete Section',
            'message' => 'Are you sure you want to delete this section and all its lessons? This action cannot be undone.',
            'action' => 'confirmDeleteSection',
            'params' => [$sectionId]
        ]);
    }

    public function confirmDeleteSection($sectionId)
    {
        $section = CourseSection::findOrFail($sectionId);

        if ($this->activeLesson && $this->activeLesson->course_section_id === $section->id) {
            $this->clearActiveLesson();
        }

        // Delete associated files
        foreach ($section->lessons as $lesson) {
            $this->deleteAssociatedFiles($lesson);
        }

        $section->lessons()->delete();
        $section->delete();
        
        $this->course->load('sections.lessons');
        $this->dispatch('notify', 'Section and all lessons deleted successfully!', 'success');
    }

    // Lesson Management
    public function showAddLessonForm($sectionId)
    {
        $this->reset(['newLessonTitle', 'newLessonDescription', 'newLessonContentType', 'newLessonDuration']);
        $this->isAddingLessonToSectionId = $sectionId;
        $this->clearActiveLesson();
    }

    public function cancelAddLesson()
    {
        $this->reset(['isAddingLessonToSectionId', 'newLessonTitle', 'newLessonDescription', 'newLessonContentType', 'newLessonDuration']);
    }

    public function addLesson()
    {
        $this->validate([
            'newLessonTitle' => 'required|string|max:255',
            'newLessonDescription' => 'nullable|string|max:1000',
            'newLessonDuration' => 'nullable|integer|min:0|max:1440'
        ]);
        
        $section = CourseSection::find($this->isAddingLessonToSectionId);
        
        $newLesson = $section->lessons()->create([
            'title' => $this->newLessonTitle,
            'slug' => Str::slug($this->newLessonTitle),
            'content_type' => $this->newLessonContentType,
            'order' => $section->lessons()->count(),
            'duration_minutes' => $this->newLessonDuration,
            'content' => json_encode(['body' => '', 'blocks' => []])
        ]);
    
        $this->course->load('sections.lessons');
        $this->dispatch('notify', 'New lesson added successfully!', 'success');
        $this->selectLesson($newLesson->id);
        $this->reset(['isAddingLessonToSectionId', 'newLessonTitle', 'newLessonDescription', 'newLessonContentType', 'newLessonDuration']);
    }

    public function editLesson($lessonId)
    {
        $lesson = CourseLesson::find($lessonId);
        $this->editingLessonId = $lessonId;
        $this->editingLessonTitle = $lesson->title;
        $this->editingLessonDescription = $lesson->description ?? '';
    }

    public function updateLesson()
    {
        $this->validate([
            'editingLessonTitle' => 'required|string|max:255',
            'editingLessonDescription' => 'nullable|string|max:1000'
        ]);
        
        $lesson = CourseLesson::find($this->editingLessonId);
        $lesson->update([
            'title' => $this->editingLessonTitle,
            'slug' => Str::slug($this->editingLessonTitle),
            'description' => $this->editingLessonDescription
        ]);
        
        $this->reset(['editingLessonId', 'editingLessonTitle', 'editingLessonDescription']);
        $this->course->load('sections.lessons');
        $this->dispatch('notify', 'Lesson updated successfully!', 'success');
    }

    public function cancelEditLesson()
    {
        $this->reset(['editingLessonId', 'editingLessonTitle', 'editingLessonDescription']);
    }

    public function deleteLesson($lessonId)
    {
        $this->dispatch('confirm-delete', [
            'title' => 'Delete Lesson',
            'message' => 'Are you sure you want to delete this lesson? This action cannot be undone.',
            'action' => 'confirmDeleteLesson',
            'params' => [$lessonId]
        ]);
    }

    public function confirmDeleteLesson($lessonId)
    {
        $lesson = CourseLesson::findOrFail($lessonId);

        if ($this->activeLesson && $this->activeLesson->id === $lesson->id) {
            $this->clearActiveLesson();
        }

        $this->deleteAssociatedFiles($lesson);
        $lesson->delete();
        
        $this->course->load('sections.lessons');
        $this->dispatch('notify', 'Lesson deleted successfully!', 'success');
    }

    private function deleteAssociatedFiles($lesson)
    {
        $content = is_string($lesson->content) ? json_decode($lesson->content, true) : $lesson->content;
        
        if (isset($content['blocks'])) {
            foreach ($content['blocks'] as $block) {
                if (isset($block['file_path'])) {
                    Storage::disk('public')->delete($block['file_path']);
                }
            }
        }
    }

    // Media Management
    public function showImageModal()
    {
        $this->showImageModal = true;
        $this->resetMediaFields();
    }

    public function showVideoModal()
    {
        $this->showVideoModal = true;
        $this->resetMediaFields();
    }

    public function showFileModal()
    {
        $this->showFileModal = true;
        $this->resetMediaFields();
    }

    public function showAudioModal()
    {
        $this->showAudioModal = true;
        $this->resetMediaFields();
    }

    public function closeModals()
    {
        $this->reset([
            'showImageModal', 'showVideoModal', 'showFileModal', 
            'showAudioModal', 'showQuizModal'
        ]);
        $this->resetMediaFields();
    }

    public function addImage()
    {
        $this->validate([
            'mediaFile' => 'required|image|max:10240', // 10MB max for images
        ]);

        $filePath = $this->mediaFile->store('lessons/images', 'public');
        
        $block = [
            'id' => uniqid(),
            'type' => 'image',
            'file_path' => $filePath,
            'file_name' => $this->mediaFile->getClientOriginalName(),
            'caption' => $this->mediaCaption,
            'file_size' => $this->mediaFile->getSize(),
            'created_at' => now()->toISOString()
        ];

        $this->contentBlocks[] = $block;
        $this->isDirty = true;
        $this->closeModals();
        $this->dispatch('notify', 'Image added successfully!', 'success');
    }

    public function addVideo()
    {
        if ($this->videoUrl) {
            $this->validate(['videoUrl' => 'required|url']);
            
            $block = [
                'id' => uniqid(),
                'type' => 'video',
                'video_url' => $this->videoUrl,
                'title' => $this->videoTitle ?: 'Video',
                'created_at' => now()->toISOString()
            ];
        } else {
            $this->validate([
                'mediaFile' => 'required|mimes:mp4,avi,mov,wmv,webm|max:102400', // 100MB max for videos
            ]);

            $filePath = $this->mediaFile->store('lessons/videos', 'public');
            
            $block = [
                'id' => uniqid(),
                'type' => 'video',
                'file_path' => $filePath,
                'file_name' => $this->mediaFile->getClientOriginalName(),
                'file_size' => $this->mediaFile->getSize(),
                'title' => $this->videoTitle ?: $this->mediaFile->getClientOriginalName(),
                'created_at' => now()->toISOString()
            ];
        }

        $this->contentBlocks[] = $block;
        $this->isDirty = true;
        $this->closeModals();
        $this->dispatch('notify', 'Video added successfully!', 'success');
    }

    public function addFile()
    {
        $this->validate([
            'mediaFile' => 'required|file|max:51200', // 50MB max for files
        ]);

        $filePath = $this->mediaFile->store('lessons/files', 'public');
        
        $block = [
            'id' => uniqid(),
            'type' => 'file',
            'file_path' => $filePath,
            'file_name' => $this->mediaFile->getClientOriginalName(),
            'description' => $this->fileDescription,
            'file_size' => $this->mediaFile->getSize(),
            'mime_type' => $this->mediaFile->getMimeType(),
            'created_at' => now()->toISOString()
        ];

        $this->contentBlocks[] = $block;
        $this->isDirty = true;
        $this->closeModals();
        $this->dispatch('notify', 'File added successfully!', 'success');
    }

    public function addAudio()
    {
        $this->validate([
            'mediaFile' => 'required|file|mimes:mp3,wav,aac,ogg|max:51200', // 50MB max for audio
        ]);

        $filePath = $this->mediaFile->store('lessons/audio', 'public');
        
        $block = [
            'id' => uniqid(),
            'type' => 'audio',
            'file_path' => $filePath,
            'file_name' => $this->mediaFile->getClientOriginalName(),
            'title' => $this->audioTitle ?: $this->mediaFile->getClientOriginalName(),
            'file_size' => $this->mediaFile->getSize(),
            'created_at' => now()->toISOString()
        ];

        $this->contentBlocks[] = $block;
        $this->isDirty = true;
        $this->closeModals();
        $this->dispatch('notify', 'Audio added successfully!', 'success');
    }

    public function addCodeBlock()
    {
        $block = [
            'id' => uniqid(),
            'type' => 'code',
            'language' => 'javascript',
            'code' => '// Enter your code here...',
            'title' => 'Code Example',
            'created_at' => now()->toISOString()
        ];

        $this->contentBlocks[] = $block;
        $this->isDirty = true;
        $this->dispatch('notify', 'Code block added!', 'success');
    }

    public function addNoteBlock()
    {
        $block = [
            'id' => uniqid(),
            'type' => 'note',
            'note_type' => 'tip',
            'title' => 'Tip',
            'content' => 'Enter your tip or note here...',
            'created_at' => now()->toISOString()
        ];

        $this->contentBlocks[] = $block;
        $this->isDirty = true;
        $this->dispatch('notify', 'Note block added!', 'success');
    }

    public function removeContentBlock($blockId)
    {
        $blockIndex = collect($this->contentBlocks)->search(function ($block) use ($blockId) {
            return $block['id'] === $blockId;
        });

        if ($blockIndex !== false) {
            // Delete associated file if exists
            $block = $this->contentBlocks[$blockIndex];
            if (isset($block['file_path'])) {
                Storage::disk('public')->delete($block['file_path']);
            }

            array_splice($this->contentBlocks, $blockIndex, 1);
            $this->isDirty = true;
            $this->dispatch('notify', 'Content block removed!', 'success');
        }
    }

    public function updateContentBlock($blockId, $field, $value)
    {
        $blockIndex = collect($this->contentBlocks)->search(function ($block) use ($blockId) {
            return $block['id'] === $blockId;
        });

        if ($blockIndex !== false) {
            $this->contentBlocks[$blockIndex][$field] = $value;
            $this->isDirty = true;
        }
    }

    // Quiz Management
    public function showQuizModal()
    {
        $this->showQuizModal = true;
        $this->reset(['newQuizTitle', 'newQuizDescription', 'newQuizPassPercentage']);
        $this->newQuizPassPercentage = 70;
    }

    public function addQuiz()
    {
        $this->validate([
            'newQuizTitle' => 'required|string|max:255',
            'newQuizDescription' => 'nullable|string|max:1000',
            'newQuizPassPercentage' => 'required|integer|min:1|max:100'
        ]);

        if (!$this->activeLesson) {
            $this->dispatch('notify', 'Please select a lesson first.', 'error');
            return;
        }

        // Find corresponding lesson in the modules system
        $moduleLesson = Lesson::where('slug', $this->activeLesson->slug)->first();
        
        if (!$moduleLesson) {
            // Create corresponding lesson in modules system
            $module = $this->course->modules()->first();
            if (!$module) {
                $module = $this->course->modules()->create([
                    'title' => 'Default Module',
                    'description' => 'Auto-created module for lessons',
                    'order' => 0
                ]);
            }

            $moduleLesson = $module->lessons()->create([
                'title' => $this->activeLesson->title,
                'slug' => $this->activeLesson->slug,
                'description' => $this->activeLesson->description ?? '',
                'type' => $this->activeLesson->content_type ?? 'text',
                'order' => $module->lessons()->count(),
                'content' => $this->activeLesson->content
            ]);
        }

        $quiz = $moduleLesson->quizzes()->create([
            'title' => $this->newQuizTitle,
            'description' => $this->newQuizDescription,
            'pass_percentage' => $this->newQuizPassPercentage,
        ]);

        $this->activeQuiz = $quiz;
        $this->closeModals();
        $this->dispatch('notify', 'Quiz created successfully!', 'success');
    }

    public function selectQuiz($quizId)
    {
        $this->activeQuiz = Quiz::with('questions.options')->find($quizId);
        $this->reset(['newQuestionText', 'newQuestionType', 'newQuestionOptions', 'correctOptionIndex', 'newQuestionCorrectAnswer']);
        $this->newQuestionOptions = ['', '', '', ''];
    }

    public function addQuestion()
    {
        $this->validate([
            'newQuestionText' => 'required|string|max:1000',
            'newQuestionType' => 'required|in:multiple_choice,true_false,short_answer,essay'
        ]);

        if (!$this->activeQuiz) {
            $this->dispatch('notify', 'Please select a quiz first.', 'error');
            return;
        }

        $questionData = [
            'question_text' => $this->newQuestionText,
            'type' => $this->newQuestionType,
        ];

        if (in_array($this->newQuestionType, ['short_answer', 'essay'])) {
            $this->validate(['newQuestionCorrectAnswer' => 'required']);
            $questionData['correct_answer'] = $this->newQuestionCorrectAnswer;
        }

        $question = $this->activeQuiz->questions()->create($questionData);

        // Add options for multiple choice questions
        if ($this->newQuestionType === 'multiple_choice') {
            $validOptions = array_filter($this->newQuestionOptions, function($option) {
                return !empty(trim($option));
            });

            if (count($validOptions) < 2) {
                $question->delete();
                $this->dispatch('notify', 'Multiple choice questions need at least 2 options.', 'error');
                return;
            }

            foreach ($this->newQuestionOptions as $index => $optionText) {
                if (!empty(trim($optionText))) {
                    $question->options()->create([
                        'option_text' => $optionText,
                        'is_correct' => $index === $this->correctOptionIndex
                    ]);
                }
            }
        } elseif ($this->newQuestionType === 'true_false') {
            $question->options()->create([
                'option_text' => 'True',
                'is_correct' => $this->correctOptionIndex === 0
            ]);
            $question->options()->create([
                'option_text' => 'False',
                'is_correct' => $this->correctOptionIndex === 1
            ]);
        }

        $this->activeQuiz->load('questions.options');
        $this->reset(['newQuestionText', 'newQuestionType', 'newQuestionOptions', 'correctOptionIndex', 'newQuestionCorrectAnswer']);
        $this->newQuestionOptions = ['', '', '', ''];
        $this->dispatch('notify', 'Question added successfully!', 'success');
    }

    public function deleteQuestion($questionId)
    {
        $question = Question::findOrFail($questionId);
        $question->options()->delete();
        $question->delete();
        
        $this->activeQuiz->load('questions.options');
        $this->dispatch('notify', 'Question deleted successfully!', 'success');
    }

    // Reordering
    #[On('reorder-sections')]
    public function reorderSections($orderedIds)
    {
        foreach ($orderedIds as $index => $id) {
            CourseSection::find($id)->update(['order' => $index]);
        }
        $this->course->load('sections.lessons');
        $this->dispatch('notify', 'Sections reordered successfully!', 'success');
    }

    #[On('reorder-lessons')]
    public function reorderLessons($orderedIds, $sectionId)
    {
        foreach ($orderedIds as $index => $id) {
            CourseLesson::find($id)->update([
                'order' => $index,
                'course_section_id' => $sectionId
            ]);
        }
        $this->course->load('sections.lessons');
        $this->dispatch('notify', 'Lessons reordered successfully!', 'success');
    }

    #[On('reorder-content-blocks')]
    public function reorderContentBlocks($orderedIds)
    {
        $reorderedBlocks = [];
        foreach ($orderedIds as $id) {
            $block = collect($this->contentBlocks)->firstWhere('id', $id);
            if ($block) {
                $reorderedBlocks[] = $block;
            }
        }
        $this->contentBlocks = $reorderedBlocks;
        $this->isDirty = true;
    }

    // Course Management
    public function togglePublished()
    {
        $this->course->is_published = !$this->course->is_published;
        $this->course->save();

        $status = $this->course->is_published ? 'published' : 'unpublished';
        $this->dispatch('notify', "Course {$status} successfully!", 'success');
    }

    public function duplicateLesson($lessonId)
    {
        $lesson = CourseLesson::findOrFail($lessonId);
        
        $duplicatedLesson = $lesson->replicate();
        $duplicatedLesson->title = $lesson->title . ' (Copy)';
        $duplicatedLesson->slug = Str::slug($duplicatedLesson->title);
        $duplicatedLesson->order = $lesson->section->lessons()->count();
        $duplicatedLesson->save();

        $this->course->load('sections.lessons');
        $this->dispatch('notify', 'Lesson duplicated successfully!', 'success');
    }

    public function duplicateSection($sectionId)
    {
        $section = CourseSection::with('lessons')->findOrFail($sectionId);
        
        $duplicatedSection = $section->replicate();
        $duplicatedSection->title = $section->title . ' (Copy)';
        $duplicatedSection->order = $this->course->sections()->count();
        $duplicatedSection->save();

        // Duplicate all lessons in the section
        foreach ($section->lessons as $lesson) {
            $duplicatedLesson = $lesson->replicate();
            $duplicatedLesson->course_section_id = $duplicatedSection->id;
            $duplicatedLesson->save();
        }

        $this->course->load('sections.lessons');
        $this->dispatch('notify', 'Section duplicated successfully!', 'success');
    }

    // Bulk operations
    public function toggleLessonSelection($lessonId)
    {
        if (in_array($lessonId, $this->selectedLessons)) {
            $this->selectedLessons = array_diff($this->selectedLessons, [$lessonId]);
        } else {
            $this->selectedLessons[] = $lessonId;
        }
        
        $this->showBulkActions = count($this->selectedLessons) > 0;
    }

    public function selectAllLessons()
    {
        $this->selectedLessons = $this->course->sections
            ->flatMap(function($section) {
                return $section->lessons->pluck('id');
            })->toArray();
        
        $this->showBulkActions = true;
    }

    public function deselectAllLessons()
    {
        $this->selectedLessons = [];
        $this->showBulkActions = false;
    }

    public function bulkDeleteLessons()
    {
        $this->dispatch('confirm-delete', [
            'title' => 'Delete Selected Lessons',
            'message' => 'Are you sure you want to delete ' . count($this->selectedLessons) . ' lessons? This action cannot be undone.',
            'action' => 'confirmBulkDeleteLessons',
            'params' => []
        ]);
    }

    public function confirmBulkDeleteLessons()
    {
        foreach ($this->selectedLessons as $lessonId) {
            $lesson = CourseLesson::find($lessonId);
            if ($lesson) {
                if ($this->activeLesson && $this->activeLesson->id === $lesson->id) {
                    $this->clearActiveLesson();
                }
                $this->deleteAssociatedFiles($lesson);
                $lesson->delete();
            }
        }

        $count = count($this->selectedLessons);
        $this->selectedLessons = [];
        $this->showBulkActions = false;
        $this->course->load('sections.lessons');
        $this->dispatch('notify', "{$count} lessons deleted successfully!", 'success');
    }

    public function bulkMoveToSection($targetSectionId)
    {
        foreach ($this->selectedLessons as $lessonId) {
            $lesson = CourseLesson::find($lessonId);
            if ($lesson) {
                $lesson->update([
                    'course_section_id' => $targetSectionId,
                    'order' => CourseLesson::where('course_section_id', $targetSectionId)->count()
                ]);
            }
        }

        $count = count($this->selectedLessons);
        $this->selectedLessons = [];
        $this->showBulkActions = false;
        $this->course->load('sections.lessons');
        $this->dispatch('notify', "{$count} lessons moved successfully!", 'success');
    }

    // Search and filter
    public function updatedSearchTerm()
    {
        // Search functionality handled in the computed property
    }

    public function updatedFilterType()
    {
        // Filter functionality handled in the computed property
    }

    public function getFilteredSectionsProperty()
    {
        $sections = $this->course->sections;

        if (!empty($this->searchTerm)) {
            $sections = $sections->filter(function($section) {
                $titleMatch = stripos($section->title, $this->searchTerm) !== false;
                $descriptionMatch = stripos($section->description, $this->searchTerm) !== false;
                $lessonMatch = $section->lessons->some(function($lesson) {
                    return stripos($lesson->title, $this->searchTerm) !== false;
                });
                
                return $titleMatch || $descriptionMatch || $lessonMatch;
            });
        }

        if ($this->filterType !== 'all') {
            $sections = $sections->filter(function($section) {
                return $section->lessons->some(function($lesson) {
                    return $lesson->content_type === $this->filterType;
                });
            });
        }

        return $sections;
    }

    // Statistics
    public function getCourseStatsProperty()
    {
        $totalLessons = $this->course->sections->sum(function($section) {
            return $section->lessons->count();
        });

        $totalDuration = $this->course->sections->sum(function($section) {
            return $section->lessons->sum('duration_minutes');
        });

        $publishedLessons = $this->course->sections->sum(function($section) {
            return $section->lessons->where('content', '!=', null)->count();
        });

        $contentTypes = $this->course->sections->flatMap(function($section) {
            return $section->lessons->pluck('content_type');
        })->countBy();

        return [
            'total_sections' => $this->course->sections->count(),
            'total_lessons' => $totalLessons,
            'total_duration' => $totalDuration,
            'published_lessons' => $publishedLessons,
            'completion_percentage' => $totalLessons > 0 ? round(($publishedLessons / $totalLessons) * 100) : 0,
            'content_types' => $contentTypes
        ];
    }

    // Export functionality
    public function exportCourseOutline()
    {
        $outline = [
            'course' => [
                'title' => $this->course->title,
                'description' => $this->course->description,
                'created_at' => $this->course->created_at->toISOString(),
                'sections' => []
            ]
        ];

        foreach ($this->course->sections as $section) {
            $sectionData = [
                'title' => $section->title,
                'description' => $section->description,
                'order' => $section->order,
                'lessons' => []
            ];

            foreach ($section->lessons as $lesson) {
                $sectionData['lessons'][] = [
                    'title' => $lesson->title,
                    'content_type' => $lesson->content_type,
                    'duration_minutes' => $lesson->duration_minutes,
                    'order' => $lesson->order
                ];
            }

            $outline['course']['sections'][] = $sectionData;
        }

        $filename = Str::slug($this->course->title) . '-outline-' . now()->format('Y-m-d') . '.json';
        
        return response()->json($outline)
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    // Auto-save when content changes
    public function updatedActiveLessonContent()
    {
        $this->isDirty = true;
        $this->autoSave();
    }

    public function updatedContentBlocks()
    {
        $this->isDirty = true;
    }

    // Keyboard shortcuts handler
    public function handleKeyboardShortcut($shortcut)
    {
        switch ($shortcut) {
            case 'save':
                $this->saveContent();
                break;
            case 'new-section':
                $this->showAddSectionForm();
                break;
            case 'preview':
                $this->dispatch('preview-lesson');
                break;
            case 'focus-search':
                $this->dispatch('focus-search');
                break;
        }
    }

    // Validation helpers
    public function validateLessonContent()
    {
        $errors = [];
        
        if (empty(trim($this->activeLessonContent)) && empty($this->contentBlocks)) {
            $errors[] = 'Lesson content cannot be empty';
        }

        if (str_word_count($this->activeLessonContent) > 10000) {
            $errors[] = 'Lesson content is too long (maximum 10,000 words)';
        }

        return $errors;
    }

    public function previewLesson()
    {
        if (!$this->activeLesson) {
            $this->dispatch('notify', 'Please select a lesson first.', 'error');
            return;
        }

        // Save current changes before preview
        if ($this->isDirty) {
            $this->autoSave();
        }

        $this->dispatch('open-preview-modal', [
            'lesson' => $this->activeLesson->toArray(),
            'content' => $this->activeLessonContent,
            'blocks' => $this->contentBlocks
        ]);
    }

    public function render()
    {
        return view('livewire.component.course-management.course-builder', [
            'filteredSections' => $this->filteredSections,
            'courseStats' => $this->courseStats
        ]);
    }
}