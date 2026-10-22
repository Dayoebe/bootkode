<?php

namespace App\Traits;

use App\Models\Learning\Course;
use App\Models\Learning\Section;
use App\Models\Learning\Lesson;
use App\Models\Assessment\Assessment;
use Illuminate\Support\Facades\DB;

trait PreservesCourseHistory
{
    public function delete()
    {
        if (!$this->exists) return null;
        $courseId = match (true) {
            $this instanceof Course => $this->id,
            $this instanceof Section => $this->course_id,
            $this instanceof Lesson => $this->section?->course_id,
            $this instanceof Assessment => $this->course_id ?: $this->lesson?->section?->course_id,
            default => $this->assessment?->course_id ?: $this->assessment?->lesson?->section?->course_id,
        };
        return DB::transaction(function () use ($courseId) {
            if ($courseId) {
                Course::whereKey($courseId)->lockForUpdate()->firstOrFail();
                app(\App\Services\LearningHistoryGuard::class)->ensureRemovable((int) $courseId);
            }
            return parent::delete();
        });
    }
}
