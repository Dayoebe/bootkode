<?php

namespace App\Livewire\Career;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use App\Models\Career\JobCategory;
use App\Models\Career\JobPortal as Job;
use App\Services\JobApplicationService;
use App\Models\Career\JobApplication;
use App\Models\Career\JobSave;
use App\Models\Core\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Livewire\Attributes\Layout;

#[Layout('layouts.dashboard', [
    'title' => 'Job Portal',
    'description' => 'Find your dream job and advance your career',
    'icon' => 'fas fa-search',
    'active' => 'jobs.portal'
])]

class JobPortal extends Component
{
    use WithFileUploads, WithPagination;

    // Search and Filter Properties
    public $searchTerm = '';
    public $filterCategory = '';
    public $filterLocation = '';
    public $filterEmploymentType = '';
    public $filterWorkType = '';
    public $filterExperienceLevel = '';
    public $filterSalaryMin = '';
    public $filterSalaryMax = '';
    public string $filterSalaryCurrency = 'NGN';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $showAdvancedFilters = false;

    // View Properties
    public $viewMode = 'list'; // list, grid
    public $showJobDetails = false;
    public $selectedJob = null;
    public $activeTab = 'browse'; // browse, applications, saved

    // Application Properties
    public $showApplicationModal = false;
    public $applicationJob = null;
    public $coverLetter = '';
    public $resume = null;
    public $customResponses = [];

    // Job Alerts
    public $showJobAlertModal = false;
    public $alertKeywords = '';
    public $alertLocation = '';
    public $alertCategory = '';
    public string $alertTitle = '';
    public string $alertFrequency = 'daily';
    public bool $jobAlertEmailEnabled = false;
    public string $alertMessage = '';

    // User's Data
    public $userApplications = [];
    public $savedJobs = [];
    public $recommendedJobs = [];
    public $jobStats = [];

    // Categories and Options
    public $categories = [];
    public $employmentTypes = [
        'full-time' => 'Full Time',
        'part-time' => 'Part Time',
        'contract' => 'Contract',
        'temporary' => 'Temporary',
        'internship' => 'Internship',
        'freelance' => 'Freelance'
    ];

    public $workTypes = [
        'on-site' => 'On-site',
        'remote' => 'Remote',
        'hybrid' => 'Hybrid'
    ];

    public $experienceLevels = [
        'entry' => 'Entry Level',
        'junior' => 'Junior Level',
        'mid' => 'Mid Level',
        'senior' => 'Senior Level',
        'executive' => 'Executive',
        'director' => 'Director'
    ];

    protected $rules = [
        'coverLetter' => 'required|string|min:50|max:2000',
        'resume' => 'nullable|file|mimes:pdf,doc,docx|max:5120', // 5MB
        'customResponses' => 'array|max:20',
        'customResponses.*' => 'nullable|string|max:2000',
    ];

    protected $messages = [
        'coverLetter.required' => 'Please provide a cover letter for your application.',
        'coverLetter.min' => 'Cover letter should be at least 50 characters long.',
        'coverLetter.max' => 'Cover letter should not exceed 2000 characters.',
        'resume.mimes' => 'Resume must be a PDF or Word document.',
        'resume.max' => 'Resume file size should not exceed 5MB.'
    ];

    public function mount()
    {
        $this->loadCategories();
        $this->loadUserData();
        $this->loadRecommendations();
        $this->loadJobStats();
        $this->jobAlertEmailEnabled = auth()->user()?->shouldReceiveEmailNotification('job_alert') ?? false;
        if (auth()->check() && request()->integer('alert')) $this->loadSavedAlert(request()->integer('alert'));
    }

    public function updated($propertyName)
    {
        $this->resetValidation($propertyName);
        if (in_array($propertyName, [
            'searchTerm', 'filterCategory', 'filterLocation', 'filterEmploymentType',
            'filterWorkType', 'filterExperienceLevel', 'filterSalaryMin', 'filterSalaryMax',
            'filterSalaryCurrency', 'sortBy', 'sortDirection'
        ])) {
            $this->resetPage();
        }
    }

    public function loadCategories()
    {
        $this->categories = JobCategory::where('is_active', true)
            ->orderBy('sort_order')
            ->pluck('name', 'slug')
            ->toArray();
    }

    public function loadUserData()
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            $this->userApplications = JobApplication::where('user_id', $user->id)
                ->with(['job'])
                ->latest()
                ->limit(10)
                ->get();

            $this->savedJobs = JobSave::where('user_id', $user->id)
                ->with(['job'])
                ->latest()
                ->limit(10)
                ->get();
        }
    }

    public function loadRecommendations()
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Use the full model namespace instead of JobPortal (which refers to this Livewire component)
            $this->recommendedJobs = Job::active()
                ->when($user->resume_profile ?? false, function ($query) use ($user) {
                    $profile = $user->resume_profile;
                    $userSkills = collect($profile->skills ?? [])->pluck('name')->toArray();
                    
                    if (!empty($userSkills)) {
                        $query->whereJsonContains('skills_required', $userSkills[0] ?? '');
                    }
                })
                ->limit(6)
                ->get();
        } else {
            // Show popular jobs for guests
            $this->recommendedJobs = Job::active()
                ->orderBy('views_count', 'desc')
                ->limit(6)
                ->get();
        }
    }

    public function loadJobStats()
    {
        $this->jobStats = [
            'total_active' => Job::active()->count(),
            'new_this_week' => Job::active()->where('created_at', '>=', now()->subWeek())->count(),
            'remote_jobs' => Job::active()->where('work_type', 'remote')->count(),
            'premium_jobs' => Job::active()->where('is_premium', true)->count(),
        ];
    
        if (Auth::check()) {
            $user = Auth::user();
            $this->jobStats['user_applications'] = JobApplication::where('user_id', $user->id)->count();
            $this->jobStats['user_saved'] = JobSave::where('user_id', $user->id)->count();
        }
    }

    public function viewJob($jobId)
    {
        $this->selectedJob = Job::active()->with('postedBy:id,name')->findOrFail($jobId);
        $this->selectedJob->incrementViews();
        $this->showJobDetails = true;
    }
    public function closeJobDetails()
    {
        $this->showJobDetails = false;
        $this->selectedJob = null;
    }

    public function openApplicationModal($jobId)
    {
        if (!Auth::check()) {
            session()->flash('error', 'Please login to apply for jobs.');
            return redirect()->route('login');
        }
    
        $this->applicationJob = $this->availableApplicationJob($jobId);
        
        // Check if already applied
        $existingApplication = JobApplication::where('job_id', $jobId)
            ->where('user_id', Auth::id())
            ->first();

        if ($existingApplication) {
            session()->flash('error', 'You have already applied for this job.');
            return;
        }

        // Load screening questions if any
        if ($this->applicationJob->screening_questions) {
            foreach ($this->applicationJob->screening_questions as $index => $question) {
                $this->customResponses[$index] = '';
            }
        }

        $this->showJobDetails = false;
        $this->selectedJob = null;
        $this->showApplicationModal = true;
    }

    public function closeApplicationModal()
    {
        $this->reset(['applicationJob', 'coverLetter', 'resume', 'customResponses']);
        $this->showApplicationModal = false;
    }

    protected function availableApplicationJob($id)
    {
        abort_unless(is_numeric($id), 404);

        return app(JobApplicationService::class)->availableJob((int) $id);
    }

    public function submitApplication()
    {
        abort_unless(Auth::check(), 403);
        $this->validate();
        // Always resolve availability again: a modal may be stale or client-controlled.
        $job = $this->availableApplicationJob($this->applicationJob?->id);
        try {
            app(JobApplicationService::class)->submit(Auth::user(), $job->id,
                $this->coverLetter, $this->customResponses, $this->resume);
            $this->closeApplicationModal();
            $this->loadUserData();
            $this->loadJobStats();
            session()->flash('message', 'Application received. You can track it in My Applications.');
        } catch (\Exception $e) {
            \Log::error('Job application persistence failed', ['exception' => get_class($e)]);
            session()->flash('error', 'The application could not be saved. Please try again.');
        }
    }

    public function toggleSaveJob($jobId)
    {
        if (!Auth::check()) {
            session()->flash('error', 'Please login to save jobs.');
            return;
        }

        Job::active()->findOrFail($jobId);
        $existingSave = JobSave::where('job_id', $jobId)
            ->where('user_id', Auth::id())
            ->first();

        if ($existingSave) {
            $existingSave->delete();
            session()->flash('message', 'Job removed from saved jobs.');
        } else {
            JobSave::create([
                'job_id' => $jobId,
                'user_id' => Auth::id()
            ]);
            session()->flash('message', 'Job saved successfully!');
        }

        $this->loadUserData();
    }

    public function isJobSaved($jobId)
    {
        if (!Auth::check()) {
            return false;
        }

        return JobSave::where('job_id', $jobId)
            ->where('user_id', Auth::id())
            ->exists();
    }

    public function hasAppliedToJob($jobId)
    {
        if (!Auth::check()) {
            return false;
        }

        return JobApplication::where('job_id', $jobId)
            ->where('user_id', Auth::id())
            ->exists();
    }

    public function clearFilters()
    {
        $this->reset([
            'searchTerm', 'filterCategory', 'filterLocation', 'filterEmploymentType',
            'filterWorkType', 'filterExperienceLevel', 'filterSalaryMin', 'filterSalaryMax', 'filterSalaryCurrency'
        ]);
        $this->resetPage();
    }

    private function alertActor(): User { abort_unless(auth()->check(), 403); return auth()->user(); }
    public function openJobAlert(): void
    {
        abort_unless(auth()->check(), 403);
        $this->resetValidation();
        $this->alertTitle = ''; $this->alertFrequency = 'daily';
        $this->alertKeywords = $this->searchTerm; $this->alertLocation = $this->filterLocation; $this->alertCategory = $this->filterCategory;
        $this->showJobAlertModal = true;
    }
    public function createJobAlert(): void
    {
        abort_unless(auth()->check(), 403);
        $this->validate(['alertTitle'=>'required|string|max:100','alertKeywords'=>'nullable|string|max:1000','alertLocation'=>'nullable|string|max:255','alertCategory'=>'nullable|string|max:100','alertFrequency'=>'required|in:daily,weekly']);
        $alert = \Illuminate\Support\Facades\DB::transaction(function () {
            $alert = app(\App\Services\JobAlertService::class)->create($this->alertActor(),[
                'title'=>$this->alertTitle,'keywords'=>array_values(array_filter(array_map('trim',explode(',', $this->alertKeywords)))),
                'location'=>$this->alertLocation,'category'=>$this->alertCategory,'frequency'=>$this->alertFrequency,
                'employment_type'=>$this->filterEmploymentType,'work_type'=>$this->filterWorkType,'experience_level'=>$this->filterExperienceLevel,
                'salary_min'=>$this->filterSalaryMin,'salary_max'=>$this->filterSalaryMax,'salary_currency'=>$this->filterSalaryCurrency]);
            app(\App\Services\PortalNotificationService::class)->setEmailPreference($this->alertActor(),'job_alert',$this->jobAlertEmailEnabled);
            return $alert;
        });
        $this->showJobAlertModal=false;$this->alertMessage='Saved alert: '.$alert->title.'. Use Check now to record new matches. Email alerts follow your saved preference when email delivery is available.';
    }
    public function toggleJobAlert(int $id,bool $active): void { app(\App\Services\JobAlertService::class)->pause($this->alertActor(),$id,$active);$this->alertMessage=$active?'Job alert resumed.':'Job alert paused.'; }
    public function setJobAlertFrequency(int $id,string $frequency): void
    {
        app(\App\Services\JobAlertService::class)->frequency($this->alertActor(),$id,$frequency);
        $this->alertMessage='Saved alert frequency updated.';
        $this->dispatch('bootkode-assessment-focus',id:'job-alert-'.$id);
    }
    public function removeJobAlert(int $id): void { app(\App\Services\JobAlertService::class)->remove($this->alertActor(),$id);$this->alertMessage='Saved job alert removed.'; }
    public function checkJobAlert(int $id): void
    {
        $count=app(\App\Services\JobAlertService::class)->checkOwn($this->alertActor(),$id);
        $this->alertMessage=$count ? $count.' new match(es) recorded in your portal notifications.' : 'No new available public jobs match this alert.';
    }
    public function loadSavedAlert(int $id): void
    {
        try { $alert=app(\App\Services\JobAlertService::class)->owned($this->alertActor(),$id); }
        catch (\Illuminate\Database\Eloquent\ModelNotFoundException) { $this->alertMessage='That saved alert is no longer available. Current public jobs are below.';return; }
        $this->searchTerm=implode(' ', $alert->keywords ?? []); $this->filterCategory=$alert->category ?? ''; $this->filterLocation=$alert->location ?? '';
        $this->filterEmploymentType=$alert->employment_type ?? ''; $this->filterWorkType=$alert->work_type ?? ''; $this->filterExperienceLevel=$alert->experience_level ?? '';
        $this->filterSalaryCurrency=$alert->salary_currency ?? 'NGN';
        $this->filterSalaryMin=$alert->salary_min ?? ''; $this->filterSalaryMax=$alert->salary_max ?? '';
        $this->resetPage();$this->alertMessage='Showing the saved filters for '.$alert->title.'.';
    }

    public function quickApply($jobId)
    {
        // Reuse the real application form so a cover letter and optional resume are explicit.
        return $this->openApplicationModal($jobId);
    }

    public function render()
    {
        try { $this->validate([
            'searchTerm'=>'nullable|string|max:250','filterCategory'=>'nullable|string|max:100','filterLocation'=>'nullable|string|max:255',
            'filterEmploymentType'=>'nullable|in:full-time,part-time,contract,temporary,internship,freelance','filterWorkType'=>'nullable|in:on-site,remote,hybrid',
            'filterExperienceLevel'=>'nullable|in:entry,junior,mid,senior,executive,director','filterSalaryMin'=>'nullable|numeric|min:0','filterSalaryMax'=>'nullable|numeric|min:0',
            'sortBy'=>'required|in:created_at,salary,relevance,views_count,applications_count,title','sortDirection'=>'required|in:asc,desc',
            'filterSalaryCurrency'=>['required',\Illuminate\Validation\Rule::in(\App\Services\JobAlertService::CURRENCIES)],
        ]); } catch (\Illuminate\Validation\ValidationException $exception) {
            foreach ($exception->errors() as $field => $messages) { $this->reset($field); $this->addError($field, $messages[0]); }
        }
        $query = Job::active()->with(['postedBy:id,name']);


        // Apply search
        if ($this->searchTerm) {
            $query->search($this->searchTerm);
        }

        // Apply filters
        if ($this->filterCategory) {
            $query->byCategory($this->filterCategory);
        }

        if ($this->filterLocation) {
            $query->byLocation($this->filterLocation);
        }

        if ($this->filterEmploymentType) {
            $query->byEmploymentType($this->filterEmploymentType);
        }

        if ($this->filterWorkType) {
            $query->where('work_type', $this->filterWorkType);
        }

        if ($this->filterExperienceLevel) {
            $query->byExperience($this->filterExperienceLevel);
        }

        if (($this->filterSalaryMin !== '' && $this->filterSalaryMin !== null) || ($this->filterSalaryMax !== '' && $this->filterSalaryMax !== null)) {
            $this->validate(['filterSalaryMin'=>'nullable|numeric|min:0','filterSalaryMax'=>'nullable|numeric|min:0']);
            $query->where('hide_salary',false)->where('salary_currency',$this->filterSalaryCurrency);
            if ($this->filterSalaryMin !== '' && $this->filterSalaryMin !== null) $query->where('salary_min','>=',$this->filterSalaryMin);
            if ($this->filterSalaryMax !== '' && $this->filterSalaryMax !== null) $query->where('salary_max','<=',$this->filterSalaryMax);
        }

        // Apply sorting
        if ($this->sortBy === 'salary') {
            $query->where('hide_salary',false)->where('salary_currency',$this->filterSalaryCurrency)->orderByRaw('(salary_min + salary_max) / 2 ' . $this->sortDirection);
        } elseif ($this->sortBy === 'relevance') {
            // Relevance sorting would need full-text search scoring
            $query->orderBy('created_at', 'desc');
        } else {
            $query->orderBy($this->sortBy, $this->sortDirection);
        }

        $jobs = $query->paginate(12);

        $alertOwner = auth()->check() ? User::find(auth()->id()) : null;
        $alertsEligible = $alertOwner && $alertOwner->is_active && $alertOwner->email_verified_at;
        return view('livewire.career.job-portal', [
            'jobs' => $jobs,
            'alertsEligible' => $alertsEligible,
            'alertReady' => app(\App\Services\JobAlertService::class)->ready(),
            'savedAlerts' => $alertsEligible && app(\App\Services\JobAlertService::class)->ready() ? \App\Models\Career\JobAlert::where('user_id',auth()->id())->latest()->get() : collect(),
        ]);
    }
}