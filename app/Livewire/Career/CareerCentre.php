<?php

namespace App\Livewire\Career;

use App\Exceptions\AiProviderException;
use App\Models\Assessment\LearningSession;
use App\Models\Career\JobApplication;
use App\Models\Career\JobPortal;
use App\Models\Content\Portfolio;
use App\Models\Credentials\Certificate;
use App\Services\Ai\AiManager;
use App\Services\Career\CareerAccess;
use App\Services\Career\CareerAiService;
use App\Services\Career\CompetencyEvidenceService;
use App\Services\Career\JobEligibilityService;
use App\Services\Career\JobRequirementService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.dashboard', ['title' => 'Career Centre', 'active' => 'career.centre'])]
class CareerCentre extends Component
{
    use WithPagination;

    public string $search = '';

    public string $location = '';

    public string $workType = '';

    public string $employmentType = '';

    public string $industry = '';

    #[Locked]
    public ?int $jobId = null;

    public array $claim = ['kind' => 'skill', 'label' => '', 'source_type' => 'external', 'source_id' => '', 'reference' => '', 'years_experience' => ''];

    public string $question = '';

    public bool $shareContext = false;

    #[Locked]
    public string $advice = '';

    #[Locked]
    public string $notice = '';

    #[Locked]
    public string $aiError = '';

    public function boot(): void
    {
        app(CareerAccess::class)->account();
    }

    public function mount(): void
    {
        if (request()->integer('job')) {
            $this->selectJob(request()->integer('job'));
        }
    }

    public function updated($field): void
    {
        if (in_array($field, ['search', 'location', 'workType', 'employmentType', 'industry'])) {
            $this->resetPage();
        }
    }

    public function selectJob(int $id): void
    {
        JobPortal::where('is_public', true)->whereIn('status', ['active', 'expired'])->findOrFail($id);
        $this->jobId = $id;
        $this->dispatch('bootkode-assessment-focus', id: 'career-opportunity');
    }

    public function updatedClaimSourceType(string $value): void
    {
        $this->claim['source_id'] = '';
        if (in_array($value, ['certificate', 'portfolio', 'assessment'], true)) {
            $this->claim['kind'] = match ($value) {
                'certificate' => 'certification', 'portfolio' => 'portfolio', default => 'skill'
            };
        }
    }

    public function submitEvidence(): void
    {
        try {
            app(CompetencyEvidenceService::class)->claim($this->claim);
        } catch (\Illuminate\Validation\ValidationException $exception) {
            foreach ($exception->errors() as $field => $messages) {
                $this->addError('claim.'.$field, $messages[0]);
            }

            return;
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $exception) {
            if ($exception->getStatusCode() !== 422) {
                throw $exception;
            }
            $this->addError('claim.reference', $exception->getMessage());

            return;
        }
        $this->claim = ['kind' => 'skill', 'label' => '', 'source_type' => 'external', 'source_id' => '', 'reference' => '', 'years_experience' => ''];
        $this->resetValidation();
        $this->notice = 'Evidence request saved for review. No competency has been verified by this submission.';
    }

    public function requestReassessment(int $id): void
    {
        app(CompetencyEvidenceService::class)->reassess($id);
        $this->notice = 'Reassessment requested. This evidence is pending and will not establish eligibility until reviewed.';
    }

    public function askAdvisor(): void
    {
        $this->validate(['question' => 'required|string|max:2000', 'shareContext' => 'accepted'], [], ['shareContext' => 'permission to share the listed context']);
        $this->aiError = '';
        $this->advice = '';
        try {
            $this->advice = app(CareerAiService::class)->advise($this->question, $this->shareContext);
        } catch (AiProviderException $exception) {
            $this->aiError = $exception->getMessage();
        }
    }

    public function render()
    {
        $this->validate([
            'search' => 'nullable|string|max:150', 'location' => 'nullable|string|max:150', 'industry' => 'nullable|string|max:100',
            'workType' => 'nullable|in:remote,hybrid,on-site', 'employmentType' => 'nullable|in:full-time,part-time,contract,temporary,internship,freelance',
        ]);
        $user = app(CareerAccess::class)->account();
        $query = JobPortal::where('is_public', true)->whereIn('status', ['active', 'expired']);
        foreach (['search' => 'title', 'location' => 'location', 'industry' => 'company_industry'] as $field => $column) {
            if (trim($this->$field) !== '') {
                $query->where($column, 'like', '%'.addcslashes(trim($this->$field), '\\%_').'%');
            }
        }
        if ($this->workType !== '') {
            $query->where('work_type', $this->workType);
        }
        if ($this->employmentType !== '') {
            $query->where('employment_type', $this->employmentType);
        }
        $jobs = $query->latest('id')->paginate(8);
        $job = $this->jobId ? JobPortal::where('is_public', true)->whereIn('status', ['active', 'expired'])->findOrFail($this->jobId) : null;
        $eligibility = $job ? app(JobEligibilityService::class)->evaluate($user, $job) : null;
        $evidence = app(CompetencyEvidenceService::class)->owned($user);

        return view('livewire.career.career-centre', [
            'jobs' => $jobs, 'job' => $job, 'eligibility' => $eligibility,
            'learningOptions' => $eligibility ? app(JobEligibilityService::class)->learningOptions($eligibility) : [],
            'evidence' => $evidence, 'verifiedCount' => $evidence->filter(fn ($item) => app(CompetencyEvidenceService::class)->usable($item))->count(),
            'applications' => JobApplication::where('user_id', $user->id)->with('job:id,title')->latest('id')->limit(10)->get(),
            'kinds' => JobRequirementService::KINDS, 'aiConfigured' => app(AiManager::class)->status()['ready'],
            'assessmentSources' => LearningSession::where('user_id', $user->id)->where('activity_type', 'assessment')->whereNotNull('ended_at')->latest('id')->limit(50)->get(['id', 'ended_at', 'metadata']),
            'certificateSources' => Certificate::where('user_id', $user->id)->approved()->whereNull('revoked_at')->with('course:id,title')->limit(50)->get(),
            'portfolioSources' => Portfolio::where('user_id', $user->id)->where('status', 'completed')->limit(50)->get(['id', 'title']),
        ]);
    }
}
