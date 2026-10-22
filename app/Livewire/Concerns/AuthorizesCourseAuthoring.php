<?php

namespace App\Livewire\Concerns;

use App\Services\CourseAuthoringAccess;

trait AuthorizesCourseAuthoring
{
    public function bootAuthorizesCourseAuthoring(): void
    {
        app(CourseAuthoringAccess::class)->authorizeRole();
        $this->authorizeAuthoringContext();
    }

    protected function authorizeAuthoringContext(): void
    {
        $access = app(CourseAuthoringAccess::class);
        if (isset($this->course)) $access->course($this->course->id);
        if (isset($this->lessonId)) $access->lesson($this->lessonId);
        if (isset($this->assessmentId)) $access->assessment($this->assessmentId);
    }
}
