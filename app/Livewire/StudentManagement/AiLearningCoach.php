<?php

namespace App\Livewire\StudentManagement;

use App\Models\Learning\CourseEnrollment;
use App\Services\LearningAiService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dashboard', [
    'title' => 'AI Learning Coach',
    'description' => 'Skill diagnosis, adaptive path, tutor, assessment feedback, and course recommendations',
    'icon' => 'fas fa-robot',
    'active' => 'ai.learning.coach',
])]
class AiLearningCoach extends Component
{
    public string $activeTab = 'diagnosis';
    public string $goal = '';
    public string $question = '';
    public string $selectedCourseId = '';

    public function mount(): void
    {
        $ai = app(LearningAiService::class);
        $profile = $ai->profile(Auth::user());
        $this->goal = (string) ($profile->goal ?? '');
    }

    public function setTab(string $tab): void
    {
        if (in_array($tab, ['diagnosis', 'path', 'tutor', 'feedback', 'recommendations'], true)) {
            $this->activeTab = $tab;
        }
    }

    public function refreshProfile(): void
    {
        $ai = app(LearningAiService::class);
        $ai->profile(Auth::user(), $this->goal, true);
        session()->flash('message', 'AI learning profile refreshed from your latest platform activity.');
    }

    public function saveGoal(): void
    {
        $this->validate([
            'goal' => 'nullable|string|max:255',
        ]);

        $ai = app(LearningAiService::class);
        $ai->profile(Auth::user(), $this->goal, true);
        session()->flash('message', 'Goal saved and your adaptive path was recalculated.');
    }

    public function askTutor(): void
    {
        $rules = [
            'question' => 'required|string|min:5|max:1200',
        ];

        if ($this->selectedCourseId !== '') {
            $rules['selectedCourseId'] = 'integer|exists:courses,id';
        }

        $this->validate($rules);

        $courseId = $this->selectedCourseId !== '' ? (int) $this->selectedCourseId : null;

        $ai = app(LearningAiService::class);
        $message = $ai->askTutor(
            Auth::user(),
            $this->question,
            $courseId
        );

        $this->question = '';
        $this->activeTab = 'tutor';
        session()->flash('message', 'AI tutor answered from the available course context.');
        $this->dispatch('ai-tutor-answered', messageId: $message->id);
    }

    public function render()
    {
        $ai = app(LearningAiService::class);
        $user = Auth::user();
        $profile = $ai->profile($user, $this->goal);
        $enrolledCourses = CourseEnrollment::with('course')
            ->where('user_id', $user->id)
            ->latest('updated_at')
            ->get()
            ->pluck('course')
            ->filter()
            ->values();

        return view('livewire.student-management.ai-learning-coach', [
            'profile' => $profile,
            'skillDiagnosis' => $profile->skill_diagnosis ?? [],
            'adaptivePath' => $profile->adaptive_path ?? [],
            'assessmentFeedback' => $profile->assessment_feedback ?? [],
            'recommendations' => $profile->course_recommendations ?? [],
            'signals' => $profile->signals ?? [],
            'tutorMessages' => $ai->recentTutorMessages($user),
            'enrolledCourses' => $enrolledCourses,
            'aiProviderReady' => (bool) config('services.openai.key'),
        ]);
    }
}
