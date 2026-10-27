<?php

namespace App\Livewire\Concerns;

trait WithdrawsQnaSharing
{
    public function withdrawQnaSharing(int $answerId): void
    {
        [$assessment, $attempt] = $this->markReviewContext();
        abort_unless($assessment && $attempt, 422);
        $assessment->studentAnswers()->where('user_id', auth()->id())->where('attempt_number', $attempt)->findOrFail($answerId);
        app(\App\Services\QnaDiscussionService::class)->withdraw(auth()->user(), $assessment, $answerId);
        $this->reviewMessage = 'Q&A sharing withdrawn. Your assessment history is retained.';
        $this->dispatch('bootkode-assessment-focus', id: 'response-result-'.$answerId);
    }
}
