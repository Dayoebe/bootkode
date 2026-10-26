<?php

namespace App\Jobs;

use App\Models\Learning\CourseContentRun;
use App\Services\CourseGeneration\AiCourseGenerationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Throwable;

class GenerateCourseWithAi implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    public int $timeout = 600;

    public bool $failOnTimeout = true;

    public function __construct(public readonly int $runId)
    {
        $this->onConnection('course-content')->onQueue('course-content');
    }

    public function middleware(): array
    {
        return [(new WithoutOverlapping('course-ai-run-'.$this->runId))->expireAfter(1800)];
    }

    public function handle(AiCourseGenerationService $generation): void
    {
        $run = CourseContentRun::query()->findOrFail($this->runId);
        if ($run->status === 'completed' && $run->course_id) {
            return;
        }

        $generation->process($this->runId);
    }

    public function failed(Throwable $exception): void
    {
        CourseContentRun::query()->whereKey($this->runId)->where('status', '!=', 'completed')->update([
            'status' => 'failed',
            'failure_reason' => 'Course generation could not be completed after retry. Retry to resume saved stages.',
        ]);
    }
}
