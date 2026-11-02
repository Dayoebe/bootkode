<?php

namespace App\Jobs;

use App\Models\Learning\CourseContentRun;
use App\Services\CourseImport\CourseImportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Throwable;

class ImportCourseContent implements ShouldQueue
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
        return [(new WithoutOverlapping('course-import-run-'.$this->runId))->expireAfter(900)];
    }

    public function backoff(): array
    {
        return [75, 180];
    }

    public function handle(CourseImportService $imports): void
    {
        $run = CourseContentRun::query()->findOrFail($this->runId);
        if (in_array($run->status, ['completed', 'partial', 'skipped'], true)) {
            return;
        }

        $imports->process($this->runId);
    }

    public function failed(Throwable $exception): void
    {
        CourseContentRun::query()->whereKey($this->runId)->whereNotIn('status', ['completed', 'partial', 'skipped'])->update([
            'status' => 'failed',
            'failure_reason' => 'The import could not be completed after retry. Review the source path and retry.',
        ]);
    }
}
