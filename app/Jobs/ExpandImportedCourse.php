<?php

namespace App\Jobs;

use App\Models\Learning\CourseContentRun;
use App\Services\CourseGeneration\AiCourseExpansionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Throwable;

class ExpandImportedCourse implements ShouldQueue
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
        return [(new WithoutOverlapping('course-expansion-run-'.$this->runId))->releaseAfter(30)->expireAfter(900)];
    }

    public function backoff(): array
    {
        return [75, 180];
    }

    public function handle(AiCourseExpansionService $expansion): void
    {
        $run = CourseContentRun::query()->where('source_type', 'ai_expansion')->findOrFail($this->runId);
        if (in_array($run->status, ['completed', 'conflict'], true)) {
            return;
        }
        $expansion->process($this->runId);
    }

    public function failed(Throwable $exception): void
    {
        $run = CourseContentRun::query()->find($this->runId);
        if ($run && ! in_array($run->status, ['completed', 'conflict'], true)) {
            $run->forceFill(['status' => 'failed', 'failure_reason' => $run->failure_reason ?: 'Expansion could not finish after retry. Saved sections are retained for recovery.'])->save();
        }
    }
}
