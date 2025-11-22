<?php

namespace App\Jobs;

use App\Models\Assessment\Assessment;
use App\Models\Core\User;
use App\Services\ExamResultsEmailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendExamResultsEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;
    public $assessment;
    public $attemptNumber;
    public $results;

    /**
     * Create a new job instance.
     */
    public function __construct(
        User $user,
        Assessment $assessment,
        int $attemptNumber,
        array $results
    ) {
        $this->user = $user;
        $this->assessment = $assessment;
        $this->attemptNumber = $attemptNumber;
        $this->results = $results;
    }

    /**
     * Execute the job.
     */
    public function handle(ExamResultsEmailService $emailService): void
    {
        $emailService->sendResultsEmail(
            $this->user,
            $this->assessment,
            $this->attemptNumber,
            $this->results
        );
    }
}