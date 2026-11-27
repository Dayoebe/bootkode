<?php

namespace App\Livewire\StudentManagement\CourseView;

use App\Livewire\Concerns\RequiresAuthenticatedAccount;
use App\Models\Learning\Lesson;
use App\Models\Learning\SavedResource;
use App\Services\CbtAttemptService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;
use Livewire\Component;

class LessonToolsPanel extends Component
{
    use RequiresAuthenticatedAccount;

    #[Locked]
    public Lesson $lesson;

    public string $lessonNotes = '';

    public bool $isBookmarked = false;

    public string $statusMessage = '';

    public function boot(): void
    {
        if (isset($this->lesson)) {
            app(CbtAttemptService::class)->authorizeLesson(Auth::user(), $this->lesson);
        }
    }

    public function mount(Lesson $lesson): void
    {
        $this->lesson = $lesson;
        app(CbtAttemptService::class)->authorizeLesson(Auth::user(), $this->lesson);
        $this->loadSavedTools();
    }

    protected function loadSavedTools(): void
    {
        $resources = SavedResource::query()
            ->where('user_id', Auth::id())
            ->where('resourceable_type', Lesson::class)
            ->where('resourceable_id', $this->lesson->id)
            ->whereIn('type', ['lesson', 'note'])
            ->get()
            ->keyBy('type');

        $this->isBookmarked = $resources->has('lesson');
        $this->lessonNotes = (string) ($resources->get('note')?->notes ?? '');
    }

    public function toggleBookmark(): void
    {
        $query = SavedResource::query()
            ->where('user_id', Auth::id())
            ->where('resourceable_type', Lesson::class)
            ->where('resourceable_id', $this->lesson->id)
            ->where('type', 'lesson');

        if ($query->exists()) {
            $query->delete();
            $this->isBookmarked = false;
            $this->statusMessage = 'Bookmark removed. Your private note is unchanged.';

            return;
        }

        SavedResource::create([
            'user_id' => Auth::id(),
            'course_id' => $this->lesson->section->course_id,
            'resourceable_type' => Lesson::class,
            'resourceable_id' => $this->lesson->id,
            'type' => 'lesson',
        ]);

        $this->isBookmarked = true;
        $this->statusMessage = 'Lesson bookmarked.';
    }

    public function saveNotes(): void
    {
        $this->validate([
            'lessonNotes' => ['nullable', 'string', 'max:5000'],
        ]);

        $notes = trim($this->lessonNotes);
        $keys = [
            'user_id' => Auth::id(),
            'resourceable_type' => Lesson::class,
            'resourceable_id' => $this->lesson->id,
            'type' => 'note',
        ];

        if ($notes === '') {
            SavedResource::query()->where($keys)->delete();
            $this->lessonNotes = '';
            $this->statusMessage = 'Private note removed.';

            return;
        }

        SavedResource::updateOrCreate($keys, [
            'course_id' => $this->lesson->section->course_id,
            'notes' => $notes,
        ]);

        $this->lessonNotes = $notes;
        $this->statusMessage = 'Private note saved.';
    }

    public function render()
    {
        return view('livewire.student-management.course-view.lesson-tools-panel');
    }
}
