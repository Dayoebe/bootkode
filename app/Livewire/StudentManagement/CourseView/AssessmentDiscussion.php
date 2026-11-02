<?php

namespace App\Livewire\StudentManagement\CourseView;

use App\Models\Assessment\Assessment;
use App\Services\QnaDiscussionService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.dashboard', ['title' => 'Q&A participation'])]
class AssessmentDiscussion extends Component
{
    use \App\Livewire\Concerns\RequiresAuthenticatedAccount;
    use WithPagination;

    #[Locked] public Assessment $assessment;
    public array $feedback = [];
    public array $reasons = [];
    public string $message = '';

    public function boot(): void { if (isset($this->assessment)) app(QnaDiscussionService::class)->authorize(auth()->user(), $this->assessment); }
    public function mount(Assessment $assessment): void { $this->assessment = $assessment; app(QnaDiscussionService::class)->authorize(auth()->user(), $assessment); }

    private function act(int $answerId, callable $action, string $message): void
    {
        $this->resetValidation('entry.'.$answerId);
        try { $action(app(QnaDiscussionService::class)); }
        catch (\Illuminate\Validation\ValidationException $exception) { $this->addError('entry.'.$answerId, $exception->validator->errors()->first()); return; }
        catch (\Symfony\Component\HttpKernel\Exception\HttpException $exception) {
            if (!in_array($exception->getStatusCode(), [409, 422], true)) throw $exception;
            $this->addError('entry.'.$answerId, $exception->getMessage()); return;
        }
        unset($this->feedback[$answerId], $this->reasons[$answerId]);
        $this->message = $message;
        $this->dispatch('bootkode-assessment-focus', id: 'discussion-entry-'.$answerId);
    }

    public function share(int $id): void { $this->act($id, fn ($s) => $s->share(auth()->user(), $this->assessment, $id), 'Sharing request recorded. Your official response and mark are unchanged.'); }
    public function withdraw(int $id): void { $this->act($id, fn ($s) => $s->withdraw(auth()->user(), $this->assessment, $id), 'Sharing withdrawn. Original assessment work and feedback history are retained.'); }
    public function sendFeedback(int $id): void { $this->act($id, fn ($s) => $s->review(auth()->user(), $this->assessment, $id, $this->feedback[$id] ?? ''), 'Peer feedback recorded. Approval follows the original topic setting.'); }
    public function moderate(int $id, int $version, string $decision): void { $this->act($id, fn ($s) => $s->moderate(auth()->user(), $this->assessment, $id, $version, $decision, $this->reasons[$id] ?? ''), 'Sharing decision recorded.'); }
    public function moderateReview(int $id, int $version, string $reviewId, string $decision): void { $this->act($id, fn ($s) => $s->moderateReview(auth()->user(), $this->assessment, $id, $version, $reviewId, $decision, $this->reasons[$id] ?? ''), 'Feedback decision recorded.'); }
    public function reloadDiscussion(): void { $this->resetValidation(); $this->message = 'Discussion reloaded.'; }

    public function render()
    {
        $service = app(QnaDiscussionService::class);
        return view('livewire.student-management.course-view.assessment-discussion', [
            'moderator' => $service->authorize(auth()->user(), $this->assessment), 'entries' => $service->entries(auth()->user(), $this->assessment),
        ]);
    }
}
