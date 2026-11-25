<?php

namespace App\Livewire\Career;

use App\Exceptions\AiProviderException;
use App\Models\Career\CompetencyEvidence;
use App\Models\Career\JobPortal;
use App\Services\Career\CareerAccess;
use App\Services\Career\CareerAiService;
use App\Services\Career\CompetencyEvidenceService;
use App\Services\Career\JobRequirementService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.dashboard', ['title' => 'Career review', 'active' => 'career.review'])]
class CareerReview extends Component
{
    use WithPagination;

    public string $evidenceFilter = 'pending';

    #[Locked]
    public ?int $jobId = null;

    #[Locked]
    public string $sourceHash = '';

    public array $items = [];

    public string $reason = '';

    #[Locked]
    public ?int $evidenceId = null;

    public string $evidenceReason = '';

    public string $expiresAt = '';

    #[Locked]
    public string $notice = '';

    #[Locked]
    public string $aiError = '';

    public function boot(): void
    {
        app(CareerAccess::class)->administrator();
    }

    public function updatedEvidenceFilter(): void
    {
        $this->resetPage('evidencePage');
    }

    public function loadJob(int $id): void
    {
        $job = JobPortal::findOrFail($id);
        $this->jobId = $job->id;
        $this->sourceHash = app(JobRequirementService::class)->hash($job);
        $this->items = $job->competency_requirements ?? [];
        $this->reason = '';
        $this->resetValidation();
        $this->dispatch('bootkode-assessment-focus', id: 'career-requirements');
    }

    public function addRequirement(): void
    {
        abort_if(count($this->items) >= 30, 422);
        $this->items[] = ['kind' => 'skill', 'label' => '', 'mandatory' => false, 'minimum_years' => '', 'excerpt' => ''];
    }

    public function removeRequirement(int $index): void
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function extract(): void
    {
        abort_unless($this->jobId, 422);
        $this->aiError = '';
        try {
            $this->items = app(CareerAiService::class)->extract($this->jobId, $this->sourceHash);
            $this->notice = 'AI draft loaded for your review. Nothing has been published and eligibility has not changed.';
        } catch (AiProviderException $exception) {
            $this->aiError = $exception->getMessage();
        }
    }

    public function publish(): void
    {
        abort_unless($this->jobId, 422);
        try {
            app(JobRequirementService::class)->publish($this->jobId, $this->items, $this->sourceHash, $this->reason);
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $exception) {
            if ($exception->getStatusCode() !== 409) {
                throw $exception;
            }
            $this->notice = $exception->getMessage();

            return;
        }
        $this->notice = 'Reviewed requirements saved. New applications now check these requirements against current evidence.';
    }

    public function selectEvidence(int $id): void
    {
        CompetencyEvidence::findOrFail($id);
        $this->evidenceId = $id;
        $this->evidenceReason = '';
        $this->expiresAt = '';
        $this->dispatch('bootkode-assessment-focus', id: 'career-evidence-review');
    }

    public function decide(string $decision): void
    {
        abort_unless($this->evidenceId, 422);
        try {
            app(CompetencyEvidenceService::class)->review($this->evidenceId, $decision, $this->evidenceReason, $this->expiresAt ?: null);
        } catch (\Illuminate\Validation\ValidationException $exception) {
            foreach ($exception->errors() as $field => $messages) {
                $this->addError($field === 'expires_at' ? 'expiresAt' : 'evidenceReason', $messages[0]);
            }

            return;
        }
        $this->notice = 'Evidence decision recorded. Eligibility uses the current decision and source validity.';
        $this->evidenceId = null;
    }

    public function render()
    {
        $this->validate(['evidenceFilter' => 'required|in:pending,verified,rejected,revoked,all']);
        $selected = $this->evidenceId ? CompetencyEvidence::with('user:id,name')->findOrFail($this->evidenceId) : null;

        return view('livewire.career.career-review', [
            'jobs' => JobPortal::latest('id')->paginate(8, ['id', 'title', 'requirements_reviewed_at'], 'jobsPage'),
            'job' => $this->jobId ? JobPortal::findOrFail($this->jobId) : null,
            'pendingEvidence' => CompetencyEvidence::with('user:id,name')->when($this->evidenceFilter !== 'all', fn ($query) => $query->where('review_status', $this->evidenceFilter))->orderBy('created_at')->paginate(10, ['*'], 'evidencePage'),
            'selectedEvidence' => $selected,
            'sourceSummary' => $selected ? app(CompetencyEvidenceService::class)->sourceSummary($selected) : [],
            'kinds' => JobRequirementService::KINDS,
        ]);
    }
}
