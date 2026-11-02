<?php

namespace App\Jobs;

use App\Models\Core\User;
use App\Models\Learning\Course;
use App\Services\CourseQualityService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckCourseMedia implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    public int $timeout = 600;

    public int $uniqueFor = 900;

    public bool $failOnTimeout = true;

    public function __construct(public readonly int $courseId, public readonly int $editorId)
    {
        $this->onConnection('course-content')->onQueue('course-content')->afterCommit();
    }

    public function uniqueId(): string
    {
        return 'course-media-'.$this->courseId;
    }

    public function backoff(): array
    {
        return [60, 180];
    }

    public function handle(CourseQualityService $quality): void
    {
        $editor = User::find($this->editorId);
        abort_unless($editor?->is_active && $editor->hasVerifiedEmail() && $editor->hasAnyRole(['super_admin', 'academy_admin', 'content_editor']), 403);
        $quality->scanAndPersist(Course::findOrFail($this->courseId), $editor, true);
    }
}
