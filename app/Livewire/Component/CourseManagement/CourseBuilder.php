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
  use Illuminate\Validation\Rule;
  
  #[Layout('layouts.app', ['title' => 'Course Builder'])]
  class CourseBuilder extends Component
  {
      use WithFileUploads;
  
      public Course $course;
      public $activeLesson;
      public $activeLessonContent = '';
      public $contentBlocks = [];
      public $isDirty = false;
      public $autoSaveInterval;
      
      // Form properties
      public $newSectionTitle;
      public $newSectionDescription;
      public $newLessonTitle;
      public $newLessonType = 'text';
      public $newLessonDuration = 10;
      public $newQuizTitle;
      public $newQuizDescription;
      public $newQuizPassPercentage = 70;
      
      // Modal states
      public $showAddSectionForm = false;
      public $showAddLessonForm = false;
      public $showEditSectionForm = false;
      public $showConfirmationModal = false;
      public $confirmationTitle;
      public $confirmationMessage;
      public $confirmationAction;
      public $confirmationParams = [];
      
      // Section/lesson management
      public $activeSectionId;
      public $expandedSections = [];
      public $selectedLessons = [];
      public $showBulkActions = false;
      public $searchTerm = '';
      public $filterType = 'all';
      
      // Quiz management
      public $questions = [];
      public $selectedPrerequisites = [];
      public $allLessons = [];
      
      protected $listeners = ['lesson-selected', 'content-saved', 'notify'];
      
      public function mount(Course $course)
      {
          // Check if user has permission to edit this course
          if (!Auth::user()->isInstructor() && !Auth::user()->isAcademyAdmin() && !Auth::user()->isSuperAdmin()) {
              abort(403, 'Unauthorized access to course builder.');
          }
          
          if (Auth::user()->isInstructor() && $course->instructor_id !== Auth::id()) {
              abort(403, 'You are not the instructor for this course.');
          }
          
          $this->course = $course->load('sections.lessons');
          // Fix 1: Pluck section IDs, not course IDs
          $this->expandedSections = $this->course->sections->pluck('id')->toArray();
          
          // Fix 2: Get all lessons from already loaded relationship instead of querying again
          $this->allLessons = $this->course->sections->flatMap->lessons;
          
          // Start auto-save interval
          $this->autoSaveInterval = now()->addMinutes(2);
      }
      
      public function updatedSearchTerm()
      {
          // Reset to first section if searching
          if ($this->searchTerm) {
              $this->expandedSections = [];
          }
      }
      
      #[On('lesson-selected')]
      public function selectLesson($lessonId)
      {
          $this->activeLesson = CourseLesson::with('section')->findOrFail($lessonId);
          $this->activeLessonContent = $this->activeLesson->content;
          $this->contentBlocks = json_decode($this->activeLesson->content_blocks, true) ?? [];
          $this->isDirty = false;
          
          // Load quiz questions if this is a quiz lesson
          if ($this->activeLesson->content_type === 'quiz') {
              $this->loadQuizQuestions();
          }
          
          // Load prerequisites
          $this->selectedPrerequisites = $this->activeLesson->prerequisites->pluck('id')->toArray();
      }
      
      private function loadQuizQuestions()
      {
          $this->questions = $this->activeLesson->quiz->questions->map(function($question) {
              return [
                  'id' => $question->id,
                  'text' => $question->text,
                  'type' => $question->type,
                  'points' => $question->points,
                  'correct_answer' => $question->correct_answer,
                  'grading_guidelines' => $question->grading_guidelines,
                  'options' => $question->options->pluck('text')->toArray(),
              ];
          })->toArray();
      }
      
      public function updatedActiveLessonContent()
      {
          $this->isDirty = true;
          $this->autoSave();
      }
      
      public function updatedContentBlocks()
      {
          $this->isDirty = true;
      }
      
      public function updatedSelectedPrerequisites()
      {
          $this->isDirty = true;
      }
      
      public function autoSave()
      {
          if (!$this->activeLesson || !$this->isDirty) {
              return;
          }
          
          // Only auto-save if 2 minutes have passed since last save
          if (now()->lt($this->autoSaveInterval)) {
              return;
          }
          
          $this->saveContent();
          $this->autoSaveInterval = now()->addMinutes(2);
      }
      
      public function saveContent()
      {
          if (!$this->activeLesson) {
              $this->dispatch('notify', 'Please select a lesson first.', 'error');
              return;
          }
          
          $this->validate([
              'activeLesson.title' => 'required|string|max:255',
              'activeLesson.content_type' => 'required|in:text,video,quiz',
              'activeLesson.duration_minutes' => 'required|integer|min:1|max:300',
          ]);
          
          try {
              // Save lesson content
              $this->activeLesson->content = $this->activeLessonContent;
              $this->activeLesson->content_blocks = json_encode($this->contentBlocks);
              $this->activeLesson->prerequisites()->sync($this->selectedPrerequisites);
              
              // Handle quiz-specific data
              if ($this->activeLesson->content_type === 'quiz') {
                  $this->saveQuizData();
              }
              
              $this->activeLesson->save();
              
              $this->isDirty = false;
              $this->dispatch('content-saved');
              $this->dispatch('notify', 'Lesson content saved successfully!', 'success');
          } catch (\Exception $e) {
              $this->dispatch('notify', 'Failed to save lesson: ' . $e->getMessage(), 'error');
          }
      }
      
      private function saveQuizData()
      {
          // Create or update quiz
          $quiz = $this->activeLesson->quiz ?? new Quiz();
          $quiz->title = $this->activeLesson->quiz_title;
          $quiz->description = $this->activeLesson->quiz_description;
          $quiz->pass_percentage = $this->activeLesson->quiz_pass_percentage;
          $quiz->save();
          
          // Associate quiz with lesson
          $this->activeLesson->quiz()->associate($quiz);
          
          // Save questions
          foreach ($this->questions as $questionData) {
              $question = Question::updateOrCreate(
                  ['id' => $questionData['id'] ?? null],
                  [
                      'quiz_id' => $quiz->id,
                      'text' => $questionData['text'],
                      'type' => $questionData['type'],
                      'points' => $questionData['points'],
                      'correct_answer' => $questionData['correct_answer'],
                      'grading_guidelines' => $questionData['grading_guidelines'] ?? null,
                  ]
              );
              
              // Save options for multiple choice questions
              if (in_array($questionData['type'], ['multiple_choice', 'true_false'])) {
                  foreach ($questionData['options'] as $index => $optionText) {
                      Option::updateOrCreate(
                          ['id' => $questionData['options_ids'][$index] ?? null],
                          [
                              'question_id' => $question->id,
                              'text' => $optionText,
                              'is_correct' => $index == $questionData['correct_answer']
                          ]
                      );
                  }
              }
          }
      }
      
      public function showAddSectionForm()
      {
          $this->resetForm();
          $this->showAddSectionForm = true;
      }
      
      public function addSection()
      {
          $this->validate([
              'newSectionTitle' => 'required|string|max:255',
              'newSectionDescription' => 'nullable|string|max:1000',
          ]);
          
          try {
              $section = new CourseSection();
              $section->title = $this->newSectionTitle;
              $section->description = $this->newSectionDescription;
              $section->order = $this->course->sections()->count();
              $section->course_id = $this->course->id;
              $section->save();
              
              $this->course->load('sections.lessons');
              $this->resetForm();
              $this->dispatch('notify', 'Section created successfully!', 'success');
          } catch (\Exception $e) {
              $this->dispatch('notify', 'Failed to create section: ' . $e->getMessage(), 'error');
          }
      }
      
      public function showEditSectionForm($sectionId)
      {
          $section = CourseSection::findOrFail($sectionId);
          $this->newSectionTitle = $section->title;
          $this->newSectionDescription = $section->description;
          $this->activeSectionId = $sectionId;
          $this->showAddSectionForm = true;
      }
      
      public function updateSection()
      {
          $this->validate([
              'newSectionTitle' => 'required|string|max:255',
              'newSectionDescription' => 'nullable|string|max:1000',
          ]);
          
          try {
              $section = CourseSection::findOrFail($this->activeSectionId);
              $section->title = $this->newSectionTitle;
              $section->description = $this->newSectionDescription;
              $section->save();
              
              $this->course->load('sections.lessons');
              $this->resetForm();
              $this->dispatch('notify', 'Section updated successfully!', 'success');
          } catch (\Exception $e) {
              $this->dispatch('notify', 'Failed to update section: ' . $e->getMessage(), 'error');
          }
      }
      
      public function confirmDeleteSection($sectionId)
      {
          $section = CourseSection::withCount('lessons')->findOrFail($sectionId);
          
          $this->confirmationTitle = 'Delete Section';
          $this->confirmationMessage = 'Are you sure you want to delete "' . $section->title . 
              '"? This will also delete all ' . $section->lessons_count . ' lessons in this section. This action cannot be undone.';
          $this->confirmationAction = 'deleteSection';
          $this->confirmationParams = [$sectionId];
          $this->showConfirmationModal = true;
      }
      
      public function deleteSection($sectionId)
      {
          $section = CourseSection::findOrFail($sectionId);
          
          // Delete all lessons in the section
          foreach ($section->lessons as $lesson) {
              $this->deleteAssociatedFiles($lesson);
              $lesson->delete();
          }
          
          $section->delete();
          $this->course->load('sections.lessons');
          
          // Remove from expanded sections if it was expanded
          $this->expandedSections = array_diff($this->expandedSections, [$sectionId]);
          
          $this->dispatch('notify', 'Section deleted successfully!', 'success');
          $this->showConfirmationModal = false;
      }
      
      public function toggleSection($sectionId)
      {
          if (in_array($sectionId, $this->expandedSections)) {
              $this->expandedSections = array_diff($this->expandedSections, [$sectionId]);
          } else {
              $this->expandedSections[] = $sectionId;
          }
      }
      
      public function showAddLessonForm($sectionId)
      {
          $this->activeSectionId = $sectionId;
          $this->resetLessonForm();
          $this->showAddLessonForm = true;
      }
      
      public function addLesson()
      {
          $this->validate([
              'newLessonTitle' => 'required|string|max:255',
              'newLessonType' => 'required|in:text,video,quiz',
              'newLessonDuration' => 'required|integer|min:1|max:300',
          ]);
          
          try {
              $lesson = new CourseLesson();
              $lesson->title = $this->newLessonTitle;
              $lesson->content_type = $this->newLessonType;
              $lesson->duration_minutes = $this->newLessonDuration;
              $lesson->order = CourseLesson::where('course_section_id', $this->activeSectionId)->count();
              $lesson->course_section_id = $this->activeSectionId;
              $lesson->save();
              
              $this->course->load('sections.lessons');
              $this->resetLessonForm();
              $this->dispatch('notify', 'Lesson created successfully!', 'success');
              $this->showAddLessonForm = false;
          } catch (\Exception $e) {
              $this->dispatch('notify', 'Failed to create lesson: ' . $e->getMessage(), 'error');
          }
      }
      
      public function duplicateLesson($lessonId)
      {
          $lesson = CourseLesson::findOrFail($lessonId);
          
          $duplicatedLesson = $lesson->replicate();
          $duplicatedLesson->title = $lesson->title . ' (Copy)';
          $duplicatedLesson->order = CourseLesson::where('course_section_id', $lesson->course_section_id)->count();
          $duplicatedLesson->save();
          
          // Duplicate associated files if needed
          if ($lesson->content_type === 'video' && $lesson->video_url) {
              $duplicatedLesson->video_url = $lesson->video_url;
              $duplicatedLesson->save();
          }
          
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
      
      public function confirmDeleteLesson($lessonId)
      {
          $lesson = CourseLesson::findOrFail($lessonId);
          
          $this->confirmationTitle = 'Delete Lesson';
          $this->confirmationMessage = 'Are you sure you want to delete "' . $lesson->title . 
              '"? This action cannot be undone.';
          $this->confirmationAction = 'deleteLesson';
          $this->confirmationParams = [$lessonId];
          $this->showConfirmationModal = true;
      }
      
      public function deleteLesson($lessonId)
      {
          $lesson = CourseLesson::findOrFail($lessonId);
          
          if ($this->activeLesson && $this->activeLesson->id === $lesson->id) {
              $this->clearActiveLesson();
          }
          
          $this->deleteAssociatedFiles($lesson);
          $lesson->delete();
          
          $this->course->load('sections.lessons');
          $this->dispatch('notify', 'Lesson deleted successfully!', 'success');
          $this->showConfirmationModal = false;
      }
      
      private function deleteAssociatedFiles($lesson)
      {
          // Delete any associated files (videos, images, etc.)
          if ($lesson->content_type === 'video' && $lesson->video_url) {
              // Extract filename from URL if it's a stored file
              $filename = basename(parse_url($lesson->video_url, PHP_URL_PATH));
              
              if (Storage::disk('public')->exists('course_videos/' . $filename)) {
                  Storage::disk('public')->delete('course_videos/' . $filename);
              }
          }
      }
      
      public function clearActiveLesson()
      {
          $this->activeLesson = null;
          $this->activeLessonContent = '';
          $this->contentBlocks = [];
          $this->isDirty = false;
      }
      
      public function togglePublishLesson()
      {
          if (!$this->activeLesson) {
              $this->dispatch('notify', 'Please select a lesson first.', 'error');
              return;
          }
          
          $this->activeLesson->is_published = !$this->activeLesson->is_published;
          $this->activeLesson->save();
          
          $this->dispatch('notify', 
              $this->activeLesson->is_published ? 'Lesson published!' : 'Lesson unpublished!',
              'success'
          );
      }
      
      public function previewLesson()
      {
          if (!$this->activeLesson) {
              $this->dispatch('notify', 'Please select a lesson first.', 'error');
              return;
          }
          
          // Save current changes before preview
          if ($this->isDirty) {
              $this->saveContent();
          }
          
          $this->dispatch('open-preview-modal', [
              'lesson' => $this->activeLesson->toArray(),
              'content' => $this->activeLessonContent,
              'blocks' => $this->contentBlocks
          ]);
      }
      
      public function previewCourse()
      {
          if ($this->course->sections->isEmpty()) {
              $this->dispatch('notify', 'Please add at least one section to preview the course.', 'error');
              return;
          }
          
          // Redirect to course preview page
          return redirect()->route('course.preview', $this->course);
      }
      
      public function reorderSections($oldIndex, $newIndex)
      {
          $sections = $this->course->sections;
          
          // Reorder sections
          $movedSection = $sections[$oldIndex];
          $sections->forget($oldIndex);
          $sections->splice($newIndex, 0, [$movedSection]);
          
          // Update order for all sections
          foreach ($sections->values() as $index => $section) {
              $section->order = $index;
              $section->save();
          }
          
          $this->course->load('sections.lessons');
          $this->dispatch('notify', 'Course sections reordered!', 'success');
      }
      
      public function reorderLessons($sectionId, $oldIndex, $newIndex)
      {
          $section = CourseSection::with('lessons')->find($sectionId);
          
          if (!$section) {
              $this->dispatch('notify', 'Section not found.', 'error');
              return;
          }
          
          $lessons = $section->lessons;
          
          // Reorder lessons
          $movedLesson = $lessons[$oldIndex];
          $lessons->forget($oldIndex);
          $lessons->splice($newIndex, 0, [$movedLesson]);
          
          // Update order for all lessons in section
          foreach ($lessons->values() as $index => $lesson) {
              $lesson->order = $index;
              $lesson->save();
          }
          
          $this->course->load('sections.lessons');
          $this->dispatch('notify', 'Lessons reordered!', 'success');
      }
      
      public function moveLesson($lessonId, $targetSectionId)
      {
          $lesson = CourseLesson::findOrFail($lessonId);
          $targetSection = CourseSection::findOrFail($targetSectionId);
          
          // Check if moving to the same section
          if ($lesson->course_section_id == $targetSectionId) {
              return;
          }
          
          // Update lesson's section
          $lesson->course_section_id = $targetSectionId;
          $lesson->order = CourseLesson::where('course_section_id', $targetSectionId)->count();
          $lesson->save();
          
          $this->course->load('sections.lessons');
          $this->dispatch('notify', 'Lesson moved to new section!', 'success');
      }
      
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
          $this->selectedLessons = $this->course->sections->flatMap(function($section) {
              return $section->lessons->pluck('id');
          })->toArray();
          
          $this->showBulkActions = !empty($this->selectedLessons);
      }
      
      public function clearSelection()
      {
          $this->selectedLessons = [];
          $this->showBulkActions = false;
      }
      
      public function publishSelectedLessons()
      {
          $count = 0;
          foreach ($this->selectedLessons as $lessonId) {
              $lesson = CourseLesson::find($lessonId);
              if ($lesson) {
                  $lesson->is_published = true;
                  $lesson->save();
                  $count++;
              }
          }
          
          $this->course->load('sections.lessons');
          $this->clearSelection();
          $this->dispatch('notify', "{$count} lessons published successfully!", 'success');
      }
      
      public function unpublishSelectedLessons()
      {
          $count = 0;
          foreach ($this->selectedLessons as $lessonId) {
              $lesson = CourseLesson::find($lessonId);
              if ($lesson) {
                  $lesson->is_published = false;
                  $lesson->save();
                  $count++;
              }
          }
          
          $this->course->load('sections.lessons');
          $this->clearSelection();
          $this->dispatch('notify', "{$count} lessons unpublished successfully!", 'success');
      }
      
      public function moveSelectedLessons()
      {
          $this->dispatch('notify', 'Select target section to move lessons', 'info');
          // In a real implementation, this would open a modal to select target section
      }
      
      public function deleteSelectedLessons()
      {
          $this->confirmationTitle = 'Delete Selected Lessons';
          $this->confirmationMessage = 'Are you sure you want to delete the selected lessons? This action cannot be undone.';
          $this->confirmationAction = 'confirmDeleteSelectedLessons';
          $this->confirmationParams = [];
          $this->showConfirmationModal = true;
      }
      
      public function confirmDeleteSelectedLessons()
      {
          $count = count($this->selectedLessons);
          
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
          
          $this->course->load('sections.lessons');
          $this->clearSelection();
          $this->dispatch('notify', "{$count} lessons deleted successfully!", 'success');
          $this->showConfirmationModal = false;
      }
      
      public function cancelConfirmation()
      {
          $this->showConfirmationModal = false;
      }
      
      public function getFilteredSectionsProperty()
      {
          $sections = $this->course->sections;
          
          if ($this->searchTerm) {
              $search = strtolower($this->searchTerm);
              $sections = $sections->filter(function($section) use ($search) {
                  return str_contains(strtolower($section->title), $search) ||
                         $section->lessons->contains(function($lesson) use ($search) {
                             return str_contains(strtolower($lesson->title), $search);
                         });
              });
          }
          
          if ($this->filterType !== 'all') {
              $sections = $sections->map(function($section) {
                  $filteredLessons = $section->lessons;
                  
                  if ($this->filterType === 'published') {
                      $filteredLessons = $filteredLessons->where('is_published', true);
                  } elseif ($this->filterType === 'draft') {
                      $filteredLessons = $filteredLessons->where('is_published', false);
                  } elseif ($this->filterType === 'video') {
                      $filteredLessons = $filteredLessons->where('content_type', 'video');
                  } elseif ($this->filterType === 'text') {
                      $filteredLessons = $filteredLessons->where('content_type', 'text');
                  }
                  
                  $section->setRelation('lessons', $filteredLessons);
                  return $section;
              })->filter(function($section) {
                  return $section->lessons->isNotEmpty();
              });
          }
          
          return $sections;
      }
      
      public function getCourseStatsProperty()
      {
          $totalLessons = $this->course->sections->sum(function($section) {
              return $section->lessons->count();
          });
          
          $publishedLessons = $this->course->sections->sum(function($section) {
              return $section->lessons->where('is_published', true)->count();
          });
          
          $totalDuration = $this->course->sections->sum(function($section) {
              return $section->lessons->sum('duration_minutes');
          });
          
          $contentTypes = [
              'text' => $this->course->sections->sum(function($section) {
                  return $section->lessons->where('content_type', 'text')->count();
              }),
              'video' => $this->course->sections->sum(function($section) {
                  return $section->lessons->where('content_type', 'video')->count();
              }),
              'quiz' => $this->course->sections->sum(function($section) {
                  return $section->lessons->where('content_type', 'quiz')->count();
              }),
          ];
          
          return [
              'total_sections' => $this->course->sections->count(),
              'total_lessons' => $totalLessons,
              'total_duration' => $totalDuration,
              'published_lessons' => $publishedLessons,
              'completion_percentage' => $totalLessons > 0 ? round(($publishedLessons / $totalLessons) * 100) : 0,
              'content_types' => $contentTypes,
          ];
      }
      
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
                      'description' => $lesson->description,
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
      
      public function addQuestion()
      {
          $this->questions[] = [
              'id' => null,
              'text' => '',
              'type' => 'multiple_choice',
              'points' => 1,
              'correct_answer' => 0,
              'grading_guidelines' => '',
              'options' => ['Option 1', 'Option 2'],
              'options_ids' => [null, null]
          ];
      }
      
      public function removeQuestion($index)
      {
          unset($this->questions[$index]);
          $this->questions = array_values($this->questions);
      }
      
      public function addOption($questionIndex)
      {
          $this->questions[$questionIndex]['options'][] = '';
          $this->questions[$questionIndex]['options_ids'][] = null;
      }
      
      public function removeOption($questionIndex, $optionIndex)
      {
          unset($this->questions[$questionIndex]['options'][$optionIndex]);
          unset($this->questions[$questionIndex]['options_ids'][$optionIndex]);
          
          $this->questions[$questionIndex]['options'] = array_values($this->questions[$questionIndex]['options']);
          $this->questions[$questionIndex]['options_ids'] = array_values($this->questions[$questionIndex]['options_ids']);
          
          // Adjust correct answer if needed
          if ($this->questions[$questionIndex]['correct_answer'] >= count($this->questions[$questionIndex]['options'])) {
              $this->questions[$questionIndex]['correct_answer'] = 0;
          }
      }
      
      private function resetForm()
      {
          $this->newSectionTitle = '';
          $this->newSectionDescription = '';
          $this->activeSectionId = null;
          $this->showAddSectionForm = false;
      }
      
      private function resetLessonForm()
      {
          $this->newLessonTitle = '';
          $this->newLessonType = 'text';
          $this->newLessonDuration = 10;
      }
      
      public function render()
      {
          return view('livewire.component.course-management.course-builder', [
              'filteredSections' => $this->filteredSections,
              'courseStats' => $this->courseStats
          ]);
      }
  }