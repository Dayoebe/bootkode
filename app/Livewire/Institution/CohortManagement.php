<?php

namespace App\Livewire\Institution;

use App\Models\Admin\InstitutionCohort;
use App\Models\Admin\InstitutionUser;
use App\Models\Core\Institution;
use App\Models\Learning\Course;
use App\Models\Learning\CourseEnrollment;
use App\Services\InstitutionService;
use Illuminate\Support\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class CohortManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $institutionFilter = 'all';
    public $statusFilter = 'active';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 10;

    public $showCohortModal = false;
    public $showManageModal = false;
    public $showReportModal = false;
    public $selectedCohort = null;
    public $editingCohortId = null;

    public $form = [
        'institution_id' => '',
        'name' => '',
        'description' => '',
        'starts_at' => '',
        'ends_at' => '',
        'status' => 'active',
    ];

    public $selectedMembers = [];
    public $selectedCourses = [];
    public $assignmentDueDate = '';

    protected function rules(): array
    {
        return [
            'form.institution_id' => 'required|exists:institutions,id',
            'form.name' => 'required|string|max:255',
            'form.description' => 'nullable|string|max:1000',
            'form.starts_at' => 'nullable|date',
            'form.ends_at' => 'nullable|date|after_or_equal:form.starts_at',
            'form.status' => 'required|in:active,archived',
        ];
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatedInstitutionFilter(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showCohortModal = true;
    }

    public function openEditModal(int $cohortId): void
    {
        $cohort = InstitutionCohort::findOrFail($cohortId);
        $this->editingCohortId = $cohort->id;
        $this->form = [
            'institution_id' => (string) $cohort->institution_id,
            'name' => $cohort->name,
            'description' => $cohort->description,
            'starts_at' => $cohort->starts_at?->format('Y-m-d') ?? '',
            'ends_at' => $cohort->ends_at?->format('Y-m-d') ?? '',
            'status' => $cohort->status,
        ];
        $this->showCohortModal = true;
    }

    public function saveCohort(): void
    {
        $this->validate();

        $payload = $this->form;
        $payload['created_by'] = auth()->id();
        $payload['starts_at'] = $payload['starts_at'] ?: null;
        $payload['ends_at'] = $payload['ends_at'] ?: null;

        if ($this->editingCohortId) {
            $cohort = InstitutionCohort::findOrFail($this->editingCohortId);
            unset($payload['created_by']);
            $cohort->update($payload);
            session()->flash('message', 'Cohort updated successfully.');
        } else {
            InstitutionCohort::create($payload);
            session()->flash('message', 'Cohort created successfully.');
        }

        $this->closeModals();
    }

    public function openManageModal(int $cohortId): void
    {
        $this->selectedCohort = InstitutionCohort::with(['institution', 'members.user', 'courses'])
            ->findOrFail($cohortId);
        $this->selectedMembers = $this->selectedCohort->members->pluck('id')->map(fn ($id) => (string) $id)->all();
        $this->selectedCourses = $this->selectedCohort->courses->pluck('id')->map(fn ($id) => (string) $id)->all();
        $dueAt = $this->selectedCohort->courses->first()?->pivot?->due_at;
        $this->assignmentDueDate = $dueAt ? Carbon::parse($dueAt)->format('Y-m-d') : '';
        $this->showManageModal = true;
    }

    public function saveAssignments(): void
    {
        if (! $this->selectedCohort) {
            return;
        }

        $memberSync = collect($this->selectedMembers)
            ->mapWithKeys(fn ($id) => [
                (int) $id => [
                    'assigned_by' => auth()->id(),
                    'joined_at' => now(),
                ],
            ])
            ->all();

        $courseSync = collect($this->selectedCourses)
            ->mapWithKeys(fn ($id) => [
                (int) $id => [
                    'assigned_by' => auth()->id(),
                    'assigned_at' => now(),
                    'due_at' => $this->assignmentDueDate ?: null,
                ],
            ])
            ->all();

        $this->selectedCohort->members()->sync($memberSync);
        $this->selectedCohort->courses()->sync($courseSync);

        $results = app(InstitutionService::class)->enrollCohortInAssignedCourses($this->selectedCohort->fresh());

        session()->flash(
            'message',
            'Cohort assignments saved. ' . number_format($results['successful']) . ' course enrollment(s) are ready.'
        );

        $this->closeModals();
    }

    public function openReportModal(int $cohortId): void
    {
        $this->selectedCohort = InstitutionCohort::with(['institution', 'members.user', 'courses'])
            ->findOrFail($cohortId);
        $this->showReportModal = true;
    }

    public function archiveCohort(int $cohortId): void
    {
        InstitutionCohort::findOrFail($cohortId)->update(['status' => 'archived']);
        session()->flash('message', 'Cohort archived successfully.');
    }

    public function activateCohort(int $cohortId): void
    {
        InstitutionCohort::findOrFail($cohortId)->update(['status' => 'active']);
        session()->flash('message', 'Cohort activated successfully.');
    }

    public function exportCohortReport(int $cohortId)
    {
        $cohort = InstitutionCohort::with(['institution', 'members.user', 'courses'])->findOrFail($cohortId);
        $rows = $this->buildReportRows($cohort);

        return response()->streamDownload(function () use ($cohort, $rows) {
            echo "Institution,Cohort,Student,Email,Department,Course,Progress,Completed,Completed At\n";

            foreach ($rows as $row) {
                echo implode(',', array_map(fn ($value) => '"' . str_replace('"', '""', (string) $value) . '"', [
                    $cohort->institution->name,
                    $cohort->name,
                    $row['student'],
                    $row['email'],
                    $row['department'],
                    $row['course'],
                    $row['progress'],
                    $row['completed'],
                    $row['completed_at'],
                ])) . "\n";
            }
        }, 'cohort-report-' . $cohort->slug . '-' . now()->format('Y-m-d') . '.csv');
    }

    public function closeModals(): void
    {
        $this->showCohortModal = false;
        $this->showManageModal = false;
        $this->showReportModal = false;
        $this->selectedCohort = null;
        $this->editingCohortId = null;
        $this->selectedMembers = [];
        $this->selectedCourses = [];
        $this->assignmentDueDate = '';
        $this->resetValidation();
    }

    public function getAvailableMembersProperty()
    {
        $institutionId = $this->selectedCohort?->institution_id ?: $this->form['institution_id'];

        if (! $institutionId) {
            return collect();
        }

        return InstitutionUser::with('user')
            ->where('institution_id', $institutionId)
            ->whereIn('status', ['active', 'pending'])
            ->orderBy('role')
            ->get()
            ->sortBy(fn ($member) => $member->user?->name ?? '');
    }

    public function getAvailableCoursesProperty()
    {
        return Course::query()
            ->where('is_published', true)
            ->where('is_approved', true)
            ->orderBy('title')
            ->get(['id', 'title', 'difficulty_level']);
    }

    public function buildReportRows(InstitutionCohort $cohort): array
    {
        $members = $cohort->members()->with('user')->get();
        $courses = $cohort->courses()->get();
        $userIds = $members->pluck('user_id')->all();
        $courseIds = $courses->pluck('id')->all();

        $enrollments = CourseEnrollment::whereIn('user_id', $userIds)
            ->whereIn('course_id', $courseIds)
            ->get()
            ->keyBy(fn ($enrollment) => $enrollment->user_id . '-' . $enrollment->course_id);

        $rows = [];

        foreach ($members as $member) {
            foreach ($courses as $course) {
                $enrollment = $enrollments->get($member->user_id . '-' . $course->id);
                $completed = $enrollment && (
                    $enrollment->is_completed ||
                    $enrollment->progress_percentage >= 100 ||
                    $enrollment->completed_at
                );

                $rows[] = [
                    'student' => $member->user?->name ?? 'Unknown',
                    'email' => $member->user?->email ?? '',
                    'department' => $member->department ?? '',
                    'course' => $course->title,
                    'progress' => $enrollment ? (int) $enrollment->progress_percentage . '%' : 'Not enrolled',
                    'completed' => $completed ? 'Yes' : 'No',
                    'completed_at' => $enrollment?->completed_at?->format('Y-m-d') ?? '',
                ];
            }
        }

        return $rows;
    }

    private function resetForm(): void
    {
        $this->form = [
            'institution_id' => '',
            'name' => '',
            'description' => '',
            'starts_at' => '',
            'ends_at' => '',
            'status' => 'active',
        ];
        $this->editingCohortId = null;
    }

    private function cohortsQuery()
    {
        return InstitutionCohort::with(['institution'])
            ->withCount(['members', 'courses'])
            ->when($this->search, function ($query) {
                $query->where(function ($inner) {
                    $inner->where('name', 'like', '%' . $this->search . '%')
                        ->orWhereHas('institution', fn ($institutionQuery) => $institutionQuery->where('name', 'like', '%' . $this->search . '%'));
                });
            })
            ->when($this->institutionFilter !== 'all', fn ($query) => $query->where('institution_id', $this->institutionFilter))
            ->when($this->statusFilter !== 'all', fn ($query) => $query->where('status', $this->statusFilter))
            ->orderBy($this->sortBy, $this->sortDirection);
    }

    public function render()
    {
        $cohorts = $this->cohortsQuery()->paginate($this->perPage);

        return view('livewire.institution.cohort-management', [
            'cohorts' => $cohorts,
            'institutions' => Institution::where('status', 'active')->orderBy('name')->get(),
            'availableMembers' => $this->availableMembers,
            'availableCourses' => $this->availableCourses,
            'reportRows' => $this->selectedCohort ? $this->buildReportRows($this->selectedCohort) : [],
            'statuses' => InstitutionCohort::STATUSES,
        ]);
    }
}
