<?php

namespace App\Livewire\StudentManagement;

use App\Models\Learning\CourseCategory;
use App\Services\LearnerProfileService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Layout('layouts.dashboard', ['title' => 'Your learning profile', 'active' => 'learning.profile'])]
class LearningProfile extends Component
{
    public array $form = [];

    #[Locked]
    public int $step = 1;

    public string $categorySearch = '';

    #[Locked]
    public bool $saved = false;

    public function boot(LearnerProfileService $profiles): void
    {
        $profiles->authorize();
    }

    public function mount(LearnerProfileService $profiles): void
    {
        $this->form = $profiles->defaults($profiles->profile());
    }

    public function next(LearnerProfileService $profiles): void
    {
        $this->validateStep($profiles, $this->step);
        $this->step = min(3, $this->step + 1);
        $this->dispatch('learning-profile-step');
    }

    public function previous(): void
    {
        $this->step = max(1, $this->step - 1);
        $this->resetValidation();
        $this->dispatch('learning-profile-step');
    }

    public function save(LearnerProfileService $profiles): void
    {
        // Validate all steps again, including fields outside the current screen.
        foreach ([1, 2, 3] as $step) {
            try {
                $this->validateStep($profiles, $step);
            } catch (\Illuminate\Validation\ValidationException $exception) {
                $this->step = $step;
                $this->dispatch('learning-profile-step');
                throw $exception;
            }
        }
        $profiles->save($this->form);
        $this->saved = true;
        $this->dispatch('learning-profile-step');
    }

    public function skip(LearnerProfileService $profiles)
    {
        $profiles->skip();

        return $this->redirectRoute(auth()->user()->getDashboardRouteName());
    }

    private function validateStep(LearnerProfileService $profiles, int $step): void
    {
        $rules = collect($profiles->rules($step))->mapWithKeys(fn ($rules, $field) => ['form.'.$field => $rules])->all();
        $attributes = collect($profiles->attributes())->mapWithKeys(fn ($label, $field) => ['form.'.$field => $label])->all();
        $this->validate($rules, [], $attributes);
    }

    public function render(LearnerProfileService $profiles)
    {
        $query = CourseCategory::query();
        $search = mb_substr(trim($this->categorySearch), 0, 80);
        if ($search !== '') {
            $query->where('name', 'like', '%'.addcslashes($search, '\\%_').'%');
        }

        return view('livewire.student-management.learning-profile', [
            'categories' => $query->orderBy('name')->limit(100)->get(['id', 'name']),
            'levels' => LearnerProfileService::LEVELS,
            'paces' => LearnerProfileService::PACES,
            'formats' => LearnerProfileService::FORMATS,
            'completed' => (bool) $profiles->profile()?->onboarding_completed_at,
        ]);
    }
}
