<?php

namespace App\Livewire\StudentManagement;

use App\Services\LearningAnalyticsService;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dashboard', ['title' => 'Learning analytics', 'active' => 'student.learning-analytics'])]
class LearningAnalytics extends Component
{
    use \App\Livewire\Concerns\RequiresAuthenticatedAccount;
    use \App\Livewire\Concerns\HasActionFeedback;

    public string $timeRange = 'month';
    public string $selectedCategory = 'all';
    public int $studyGoal = 30;
    public int $weeklyGoal = 5;
    public bool $autoRefresh = true;

    public function mount($userId = null): void
    {
        abort_if($userId !== null && (int) $userId !== (int) auth()->id(), 403);
        $goals = DB::table('learning_goals')->where('user_id', auth()->id())->where('is_active', true)->latest('id')->get()->unique('goal_type');
        $this->studyGoal = (int) ($goals->firstWhere('goal_type', 'study_minutes')?->target_value ?? 30);
        $this->weeklyGoal = (int) ($goals->firstWhere('goal_type', 'lesson_completions')?->target_value ?? 5);
    }

    private function filters(): void
    {
        $this->validate(['timeRange' => 'in:week,month,quarter,year', 'selectedCategory' => ['required', 'regex:/^(all|[1-9][0-9]*)$/']]);
    }

    public function saveGoals(): void
    {
        $this->validate(['studyGoal' => 'required|integer|min:1|max:600', 'weeklyGoal' => 'required|integer|min:1|max:100']);
        DB::transaction(function () {
            \App\Models\Core\User::whereKey(auth()->id())->lockForUpdate()->firstOrFail();
            foreach ([['study_minutes', 'Daily study minutes', 'daily', $this->studyGoal], ['lesson_completions', 'Weekly completed lessons', 'weekly', $this->weeklyGoal]] as [$type, $name, $period, $target]) {
                $query = DB::table('learning_goals')->where('user_id', auth()->id())->where('goal_type', $type)->where('time_period', $period);
                $existing = (clone $query)->latest('id')->first();
                $values = ['goal_name' => $name, 'target_value' => $target, 'is_active' => true, 'start_date' => today()->format('Y-m-d'), 'end_date' => null, 'last_updated_at' => now(), 'updated_at' => now()];
                if ($existing) $query->where('id', $existing->id)->update($values);
                else DB::table('learning_goals')->insert(array_merge($values, ['user_id' => auth()->id(), 'goal_type' => $type, 'time_period' => $period, 'current_value' => 0, 'created_at' => now()]));
            }
        });
        $this->flashFeedback('success', 'Your learning goals were saved.');
    }

    public function refreshData(): void
    {
        $this->flashFeedback('success', 'Analytics refreshed from your recorded learning activity.');
    }

    public function pollAnalytics(): void
    {
        if (!$this->autoRefresh) $this->skipRender();
    }

    public function exportReport()
    {
        $this->filters();
        $snapshot = app(LearningAnalyticsService::class)->snapshot(auth()->user(), $this->timeRange, $this->selectedCategory);
        return response()->streamDownload(function () use ($snapshot) {
            $output = fopen('php://output', 'w');
            fputcsv($output, ['Date', 'Completed lessons', 'Recorded session minutes']);
            foreach ($snapshot['activity'] as $entry) fputcsv($output, array_values($entry));
            fclose($output);
        }, 'my-learning-'.now()->format('Y-m-d').'.csv', ['Cache-Control' => 'private, no-store']);
    }

    public function render()
    {
        $this->filters();
        $user = auth()->user();
        $ids = $user->enrollments()->pluck('course_id')->merge($user->courses()->pluck('courses.id'))->unique();
        return view('livewire.student-management.learning-analytics', [
            'analytics' => app(LearningAnalyticsService::class)->snapshot($user, $this->timeRange, $this->selectedCategory),
            'categories' => \App\Models\Learning\CourseCategory::whereIn('id', \App\Models\Learning\Course::whereIn('id', $ids)->select('category_id'))->orderBy('name')->get(),
        ]);
    }
}
