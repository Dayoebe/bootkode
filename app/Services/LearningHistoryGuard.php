<?php

namespace App\Services;

use App\Models\Learning\Course;
use Illuminate\Support\Facades\DB;

class LearningHistoryGuard
{
    public function ensureRemovable(int $courseId): void
    {
        foreach (['course_enrollments', 'course_user', 'certificates', 'user_progress', 'course_reviews', 'direct_conversations', 'referral_transactions'] as $table) {
            if (DB::table($table)->where('course_id', $courseId)->exists()) $this->refuse();
        }
        $lessons = DB::table('lessons')->join('sections', 'sections.id', '=', 'lessons.section_id')->where('sections.course_id', $courseId)->select('lessons.id');
        foreach (['lesson_user', 'lesson_progress', 'user_progress'] as $table) {
            if (DB::table($table)->whereIn('lesson_id', clone $lessons)->exists()) $this->refuse();
        }
        if (DB::table('user_progress')->whereIn('assessment_id', DB::table('assessments')->where('course_id', $courseId)->select('id'))->exists()) $this->refuse();
        if (DB::table('wallet_transactions')->where('transactionable_type', (new Course)->getMorphClass())->where('transactionable_id', $courseId)->exists()) $this->refuse();
    }

    private function refuse(): never
    {
        throw new \DomainException('Learning or financial history exists. Keep these records and unpublish the course instead.');
    }
}
