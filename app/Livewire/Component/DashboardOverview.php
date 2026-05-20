<?php

namespace App\Livewire\Component;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardOverview extends Component
{
    public function render()
    {
        $user = auth()->user();
        $enrolledCourses = $this->enrolledCourseCount($user->id);
        $journey = $this->journeySnapshot($user->id);

        return view('livewire.component.dashboard-overview', compact('enrolledCourses', 'journey'))
            ->layout('layouts.dashboard');
    }

    private function enrolledCourseCount(int $userId): int
    {
        $ids = collect();

        if (Schema::hasTable('course_user')) {
            $ids = $ids->merge(DB::table('course_user')->where('user_id', $userId)->pluck('course_id'));
        }

        if (Schema::hasTable('course_enrollments')) {
            $ids = $ids->merge(DB::table('course_enrollments')->where('user_id', $userId)->pluck('course_id'));
        }

        return $ids->filter()->unique()->count();
    }

    private function journeySnapshot(int $userId): array
    {
        $goalSelected = filled(data_get(auth()->user()->metadata, 'learner_journey.goal'));
        $enrolled = $this->enrolledCourseCount($userId) > 0;
        $learned = $this->tableExists('lesson_user') && DB::table('lesson_user')->where('user_id', $userId)->whereNotNull('completed_at')->exists();
        $submitted = $this->tableExists('student_answers') && DB::table('student_answers')->where('user_id', $userId)->whereNotNull('submitted_at')->exists();
        $reviewed = $this->tableExists('code_reviews') && DB::table('code_reviews')->where('requested_by', $userId)->whereIn('status', ['in_review', 'completed'])->exists();
        $certified = $this->tableExists('certificates') && DB::table('certificates')->where('user_id', $userId)->whereIn('status', ['approved', 'issued'])->whereNull('deleted_at')->exists();
        $careerReady = ($this->tableExists('portfolios') && DB::table('portfolios')->where('user_id', $userId)->exists())
            || ($this->tableExists('resume_profiles') && DB::table('resume_profiles')->where('user_id', $userId)->exists())
            || ($this->tableExists('job_applications') && DB::table('job_applications')->where('user_id', $userId)->exists());

        $steps = [
            'goal' => $goalSelected,
            'enrolled' => $enrolled,
            'learned' => $learned,
            'submitted' => $submitted,
            'reviewed' => $reviewed,
            'certified' => $certified,
            'career' => $careerReady,
        ];

        $completed = collect($steps)->filter()->count();

        return [
            'completed' => $completed,
            'total' => count($steps),
            'progress' => (int) round(($completed / count($steps)) * 100),
            'next' => collect([
                'goal' => 'Choose your learning goal',
                'enrolled' => 'Enroll in a course',
                'learned' => 'Continue lessons',
                'submitted' => 'Submit work',
                'reviewed' => 'Get reviewed',
                'certified' => 'Earn certificate',
                'career' => 'Use career tools',
            ])->first(fn ($label, $key) => ! $steps[$key]) ?? 'Keep building',
        ];
    }

    private function tableExists(string $table): bool
    {
        return Schema::hasTable($table);
    }
}
