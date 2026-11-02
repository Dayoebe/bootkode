<?php

namespace App\Livewire\Concerns;

use App\Models\Assessment\Assessment;
use App\Services\AssessmentReviewService;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

trait RequestsAssessmentMarkReview
{
    public array $reviewReasons = [];
    public string $reviewMessage = '';

    abstract protected function markReviewContext(): array;

    public function requestMarkReview(int $answerId): void
    {
        [$assessment, $attempt] = $this->markReviewContext();
        abort_unless($assessment instanceof Assessment && $attempt, 422);
        $field = 'reviewReasons.'.$answerId;
        $this->validate([$field => 'required|string|min:10|max:2000']);
        try {
            $request = app(AssessmentReviewService::class)->requestMarkReview(auth()->user(), $assessment, $attempt, $answerId, $this->reviewReasons[$answerId]);
        } catch (ValidationException $exception) {
            $this->addError($field, $exception->validator->errors()->first());
            return;
        } catch (HttpException $exception) {
            if (!in_array($exception->getStatusCode(), [409, 422], true)) throw $exception;
            $this->addError($field, $exception->getMessage());
            return;
        }
        unset($this->reviewReasons[$answerId]);
        $this->reviewMessage = $request['status'] === 'open' ? 'Mark review requested. Your recorded result stays in place until an authorized reviewer decides.'
            : 'This request has already been decided. The decision remains in your result history.';
        $this->dispatch('bootkode-assessment-focus', id: 'response-result-'.$answerId);
    }
}
