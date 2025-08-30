<?php

namespace App\Livewire\Career;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use App\Models\JobPortal;
use App\Models\JobCategory;
use App\Models\JobApplication;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Carbon\Carbon;

#[Layout('layouts.dashboard', [
    'title' => 'Job Management',
    'description' => 'Manage job postings and applications',
    'icon' => 'fas fa-briefcase',
    'active' => 'admin.jobs'
])]

class JobManagement extends Component
{
    use WithFileUploads, WithPagination;

    // Job Form Data
    public $title = '';
    public $description = '';
    public $requirements = '';
    public $responsibilities = '';
    public $benefits = '';
    public $company_description = '';
    public $company_name = '';
    public $company_logo = null;
    public $company_website = '';
    public $company_size = '';
    public $company_industry = '';
    public $employment_type = 'full-time';
    public $work_type = 'on-site';
    public $experience_level = 'mid';
    public $category = '';
    public $skills_required = '';
    public $tags = '';
    public $location = '';
    public $state = '';
    public $city = '';
    public $salary_min = '';
    public $salary_max = '';
    public $salary_currency = 'NGN';
    public $salary_period = 'monthly';
    public $salary_negotiable = false;
    public $hide_salary = false;
    public $application_method = 'internal';
    public $application_email = '';
    public $application_url = '';
    public $application_phone = '';
    public $application_instructions = '';
    public $required_documents = [];
    public $application_deadline = '';
    public $start_date = '';
    public $positions_available = 1;
    public $screening_questions = [];
    public $enable_ai_screening = false;
    public $interview_process = [];
    public $allow_remote_interview = false;
    public $referral_bonus = '';
    public $diversity_hiring = false;

    // Premium Features
    public $is_premium = false;
    public $is_featured = false;
    public $is_urgent = false;
    public $highlight_job = false;
    public $premium_features = [];
    public $featured_duration = 7;
    public $premium_duration = 30;

    // UI State
    public $showForm = false;
    public $editingJobId = null;
    public $activeTab = 'overview';
    public $viewMode = 'grid';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $filterStatus = '';
    public $filterCategory = '';
    public $filterEmploymentType = '';
    public $filterWorkType = '';
    public $searchTerm = '';
    public $selectedJobs = [];
    public $showBulkActions = false;

    // Application Management
    public $selectedApplication = null;
    public $showApplicationModal = false;
    public $applicationFeedback = '';
    public $applicationStatus = '';

    // Analytics
    public $jobStats = [];
    public $applicationStats = [];
    public $recentActivity = [];

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

    public $companySizes = [
        '1-10' => '1-10 employees',
        '11-50' => '11-50 employees',
        '51-200' => '51-200 employees',
        '201-500' => '201-500 employees',
        '501-1000' => '501-1000 employees',
        '1000+' => '1000+ employees'
    ];

    public $currencies = [
        'NGN' => '₦ Nigerian Naira',
        'USD' => '$ US Dollar',
        'GBP' => '£ British Pound',
        'EUR' => '€ Euro'
    ];

    public $salaryPeriods = [
        'hourly' => 'Per Hour',
        'daily' => 'Per Day',
        'weekly' => 'Per Week',
        'monthly' => 'Per Month',
        'yearly' => 'Per Year'
    ];

    public $documentTypes = [
        'resume' => 'Resume/CV',
        'cover_letter' => 'Cover Letter',
        'portfolio' => 'Portfolio',
        'references' => 'References',
        'certificates' => 'Certificates',
        'transcript' => 'Academic Transcript'
    ];

    public $availablePremiumFeatures = [
        'featured_listing' => 'Featured Listing',
        'urgent_hiring' => 'Urgent Hiring Badge',
        'highlight_job' => 'Highlight Job Posting',
        'premium_placement' => 'Premium Placement',
        'ai_screening' => 'AI-Powered Screening',
        'video_interviews' => 'Video Interview Integration',
        'advanced_analytics' => 'Advanced Analytics',
        'social_media_boost' => 'Social Media Promotion',
        'candidate_matching' => 'Smart Candidate Matching',
        'priority_support' => 'Priority Support'
    ];

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'required|string|min:50',
        'company_name' => 'required|string|max:255',
        'employment_type' => 'required|in:full-time,part-time,contract,temporary,internship,freelance',
        'work_type' => 'required|in:on-site,remote,hybrid',
        'experience_level' => 'required|in:entry,junior,mid,senior,executive,director',
        'category' => 'required|string',
        'location' => 'required|string|max:255',
        'positions_available' => 'required|integer|min:1',
        'application_method' => 'required|in:internal,email,external_link,phone',
        'application_email' => 'required_if:application_method,email|email',
        'application_url' => 'required_if:application_method,external_link|url',
        'application_phone' => 'required_if:application_method,phone|string',
        'application_deadline' => 'nullable|date|after:today',
        'start_date' => 'nullable|date',
        'salary_min' => 'nullable|numeric|min:0',
        'salary_max' => 'nullable|numeric|min:0|gte:salary_min',
        'company_logo' => 'nullable|image|max:2048',
        'company_website' => 'nullable|url'
    ];

    public function mount()
    {
        $this->loadCategories();
        $this->loadJobStats();
        $this->loadApplicationStats();
        $this->loadRecentActivity();
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['searchTerm', 'filterStatus', 'filterCategory', 'filterEmploymentType', 'filterWorkType', 'sortBy', 'sortDirection'])) {
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

    public function loadJobStats()
    {
        $this->jobStats = [
            'total' => JobPortal::count(),
            'active' => JobPortal::where('status', JobPortal::STATUS_ACTIVE)->count(),
            'draft' => JobPortal::where('status', JobPortal::STATUS_DRAFT)->count(),
            'expired' => JobPortal::where('status', JobPortal::STATUS_EXPIRED)->count(),
            'filled' => JobPortal::where('status', JobPortal::STATUS_FILLED)->count(),
            'premium' => JobPortal::where('is_premium', true)->count(),
            'featured' => JobPortal::where('is_featured', true)->count(),
            'this_month' => JobPortal::whereMonth('created_at', now()->month)->count(),
            'total_views' => JobPortal::sum('views_count'),
            'avg_applications' => JobPortal::avg('applications_count')
        ];
    }

    public function loadApplicationStats()
    {
        $this->applicationStats = [
            'total' => JobApplication::count(),
            'pending' => JobApplication::where('status', JobPortal::APPLICATION_PENDING)->count(),
            'reviewing' => JobApplication::where('status', JobPortal::APPLICATION_REVIEWING)->count(),
            'shortlisted' => JobApplication::where('status', JobPortal::APPLICATION_SHORTLISTED)->count(),
            'interviewed' => JobApplication::where('status', JobPortal::APPLICATION_INTERVIEWED)->count(),
            'hired' => JobApplication::where('status', JobPortal::APPLICATION_HIRED)->count(),
            'rejected' => JobApplication::where('status', JobPortal::APPLICATION_REJECTED)->count(),
            'this_week' => JobApplication::where('created_at', '>=', now()->subWeek())->count(),
            'conversion_rate' => $this->calculateOverallConversionRate()
        ];
    }

    public function loadRecentActivity()
    {
        $this->recentActivity = [
            'recent_jobs' => JobPortal::with('postedBy')->latest()->limit(5)->get(),
            'recent_applications' => JobApplication::with(['job', 'user'])->latest()->limit(5)->get(),
            'top_viewed_jobs' => JobPortal::orderBy('views_count', 'desc')->limit(5)->get(),
            'expiring_soon' => JobPortal::where('application_deadline', '<=', now()->addWeek())
                ->where('application_deadline', '>', now())
                ->orderBy('application_deadline')
                ->limit(5)
                ->get()
        ];
    }

    public function saveJob()
    {
        $this->validate();

        try {
            $jobData = [
                'posted_by' => Auth::id(),
                'title' => $this->title,
                'description' => $this->description,
                'requirements' => $this->requirements,
                'responsibilities' => $this->responsibilities,
                'benefits' => $this->benefits,
                'company_description' => $this->company_description,
                'company_name' => $this->company_name,
                'company_website' => $this->company_website,
                'company_size' => $this->company_size,
                'company_industry' => $this->company_industry,
                'employment_type' => $this->employment_type,
                'work_type' => $this->work_type,
                'experience_level' => $this->experience_level,
                'category' => $this->category,
                'skills_required' => $this->parseSkills($this->skills_required),
                'tags' => $this->parseTags($this->tags),
                'location' => $this->location,
                'state' => $this->state,
                'city' => $this->city,
                'salary_min' => $this->salary_min ?: null,
                'salary_max' => $this->salary_max ?: null,
                'salary_currency' => $this->salary_currency,
                'salary_period' => $this->salary_period,
                'salary_negotiable' => $this->salary_negotiable,
                'hide_salary' => $this->hide_salary,
                'application_method' => $this->application_method,
                'application_email' => $this->application_email,
                'application_url' => $this->application_url,
                'application_phone' => $this->application_phone,
                'application_instructions' => $this->application_instructions,
                'required_documents' => $this->required_documents,
                'application_deadline' => $this->application_deadline ? Carbon::parse($this->application_deadline) : null,
                'start_date' => $this->start_date ? Carbon::parse($this->start_date) : null,
                'positions_available' => $this->positions_available,
                'screening_questions' => $this->screening_questions,
                'enable_ai_screening' => $this->enable_ai_screening,
                'interview_process' => $this->interview_process,
                'allow_remote_interview' => $this->allow_remote_interview,
                'referral_bonus' => $this->referral_bonus,
                'diversity_hiring' => $this->diversity_hiring,
                'is_premium' => $this->is_premium,
                'is_featured' => $this->is_featured,
                'is_urgent' => $this->is_urgent,
                'highlight_job' => $this->highlight_job,
                'premium_features' => $this->premium_features,
                'status' => JobPortal::STATUS_ACTIVE,
                'is_public' => true,
                'expires_at' => now()->addDays(30)
            ];

            // Handle company logo upload
            if ($this->company_logo) {
                $logoPath = $this->company_logo->store('job-logos', 'public');
                $jobData['company_logo'] = $logoPath;
            }

            // Set premium durations
            if ($this->is_premium) {
                $jobData['premium_until'] = now()->addDays($this->premium_duration);
            }

            if ($this->is_featured) {
                $jobData['featured_until'] = now()->addDays($this->featured_duration);
            }

            if ($this->editingJobId) {
                $job = JobPortal::findOrFail($this->editingJobId);
                
                // Delete old logo if new one uploaded
                if ($this->company_logo && $job->company_logo) {
                    Storage::disk('public')->delete($job->company_logo);
                }
                
                $job->update($jobData);
                session()->flash('message', 'Job updated successfully!');
            } else {
                $job = JobPortal::create($jobData);
                session()->flash('message', 'Job created successfully!');
            }

            $this->resetForm();
            $this->loadJobStats();
            $this->loadApplicationStats();

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to save job: ' . $e->getMessage());
            \Log::error('Job save error: ' . $e->getMessage());
        }
    }

    public function editJob($jobId)
    {
        $job = JobPortal::findOrFail($jobId);
        
        $this->editingJobId = $job->id;
        $this->title = $job->title;
        $this->description = $job->description;
        $this->requirements = $job->requirements;
        $this->responsibilities = $job->responsibilities;
        $this->benefits = $job->benefits;
        $this->company_description = $job->company_description;
        $this->company_name = $job->company_name;
        $this->company_website = $job->company_website;
        $this->company_size = $job->company_size;
        $this->company_industry = $job->company_industry;
        $this->employment_type = $job->employment_type;
        $this->work_type = $job->work_type;
        $this->experience_level = $job->experience_level;
        $this->category = $job->category;
        $this->skills_required = implode(', ', $job->skills_required ?? []);
        $this->tags = implode(', ', $job->tags ?? []);
        $this->location = $job->location;
        $this->state = $job->state;
        $this->city = $job->city;
        $this->salary_min = $job->salary_min;
        $this->salary_max = $job->salary_max;
        $this->salary_currency = $job->salary_currency;
        $this->salary_period = $job->salary_period;
        $this->salary_negotiable = $job->salary_negotiable;
        $this->hide_salary = $job->hide_salary;
        $this->application_method = $job->application_method;
        $this->application_email = $job->application_email;
        $this->application_url = $job->application_url;
        $this->application_phone = $job->application_phone;
        $this->application_instructions = $job->application_instructions;
        $this->required_documents = $job->required_documents ?? [];
        $this->application_deadline = $job->application_deadline?->format('Y-m-d\TH:i');
        $this->start_date = $job->start_date?->format('Y-m-d');
        $this->positions_available = $job->positions_available;
        $this->screening_questions = $job->screening_questions ?? [];
        $this->enable_ai_screening = $job->enable_ai_screening;
        $this->interview_process = $job->interview_process ?? [];
        $this->allow_remote_interview = $job->allow_remote_interview;
        $this->referral_bonus = $job->referral_bonus;
        $this->diversity_hiring = $job->diversity_hiring;
        $this->is_premium = $job->is_premium;
        $this->is_featured = $job->is_featured;
        $this->is_urgent = $job->is_urgent;
        $this->highlight_job = $job->highlight_job;
        $this->premium_features = $job->premium_features ?? [];

        $this->showForm = true;
    }

    public function duplicateJob($jobId)
    {
        $job = JobPortal::findOrFail($jobId);
        
        $newJob = $job->replicate();
        $newJob->title = $job->title . ' (Copy)';
        $newJob->slug = null; // Will be auto-generated
        $newJob->status = JobPortal::STATUS_DRAFT;
        $newJob->views_count = 0;
        $newJob->applications_count = 0;
        $newJob->created_at = now();
        $newJob->save();

        session()->flash('message', 'Job duplicated successfully!');
        $this->loadJobStats();
    }

    public function deleteJob($jobId)
    {
        $job = JobPortal::findOrFail($jobId);
        
        // Delete company logo if exists
        if ($job->company_logo) {
            Storage::disk('public')->delete($job->company_logo);
        }
        
        $job->delete();
        
        session()->flash('message', 'Job deleted successfully.');
        $this->loadJobStats();
        $this->loadApplicationStats();
    }

    public function changeJobStatus($jobId, $status)
    {
        $job = JobPortal::findOrFail($jobId);
        $job->update(['status' => $status]);
        
        $statusLabel = match($status) {
            JobPortal::STATUS_ACTIVE => 'activated',
            JobPortal::STATUS_PAUSED => 'paused',
            JobPortal::STATUS_FILLED => 'marked as filled',
            JobPortal::STATUS_CANCELLED => 'cancelled',
            default => 'updated'
        };
        
        session()->flash('message', "Job {$statusLabel} successfully.");
        $this->loadJobStats();
    }

    public function togglePremiumFeature($jobId, $feature)
    {
        $job = JobPortal::findOrFail($jobId);
        
        if ($job->hasPremiumFeature($feature)) {
            $features = array_diff($job->premium_features ?? [], [$feature]);
            $job->update(['premium_features' => $features]);
            
            if ($feature === 'featured_listing') {
                $job->update(['is_featured' => false]);
            }
        } else {
            $job->enablePremiumFeature($feature, 30);
        }
        
        session()->flash('message', 'Premium feature updated successfully.');
    }

    public function viewApplication($applicationId)
    {
        $this->selectedApplication = JobApplication::with(['job', 'user'])->findOrFail($applicationId);
        $this->applicationStatus = $this->selectedApplication->status;
        $this->showApplicationModal = true;
    }

    public function updateApplicationStatus()
    {
        if ($this->selectedApplication) {
            $this->selectedApplication->update([
                'status' => $this->applicationStatus,
                'feedback' => $this->applicationFeedback ? ['admin_notes' => $this->applicationFeedback] : null
            ]);
            
            session()->flash('message', 'Application status updated successfully.');
            $this->closeApplicationModal();
            $this->loadApplicationStats();
        }
    }

    public function closeApplicationModal()
    {
        $this->selectedApplication = null;
        $this->applicationFeedback = '';
        $this->applicationStatus = '';
        $this->showApplicationModal = false;
    }

    public function bulkAction($action)
    {
        if (empty($this->selectedJobs)) {
            session()->flash('error', 'Please select jobs first.');
            return;
        }

        $count = count($this->selectedJobs);
        
        switch ($action) {
            case 'activate':
                JobPortal::whereIn('id', $this->selectedJobs)->update(['status' => JobPortal::STATUS_ACTIVE]);
                session()->flash('message', "{$count} jobs activated successfully.");
                break;
                
            case 'pause':
                JobPortal::whereIn('id', $this->selectedJobs)->update(['status' => JobPortal::STATUS_PAUSED]);
                session()->flash('message', "{$count} jobs paused successfully.");
                break;
                
            case 'delete':
                JobPortal::whereIn('id', $this->selectedJobs)->delete();
                session()->flash('message', "{$count} jobs deleted successfully.");
                break;
                
            case 'feature':
                JobPortal::whereIn('id', $this->selectedJobs)->update([
                    'is_featured' => true,
                    'featured_until' => now()->addDays(7)
                ]);
                session()->flash('message', "{$count} jobs featured successfully.");
                break;
        }

        $this->selectedJobs = [];
        $this->showBulkActions = false;
        $this->loadJobStats();
    }

    public function addScreeningQuestion()
    {
        $this->screening_questions[] = [
            'id' => Str::uuid(),
            'question' => '',
            'type' => 'text',
            'required' => true,
            'options' => []
        ];
    }

    public function removeScreeningQuestion($index)
    {
        unset($this->screening_questions[$index]);
        $this->screening_questions = array_values($this->screening_questions);
    }

    public function addInterviewStep()
    {
        $this->interview_process[] = [
            'id' => Str::uuid(),
            'step' => '',
            'description' => '',
            'duration' => '',
            'type' => 'interview'
        ];
    }

    public function removeInterviewStep($index)
    {
        unset($this->interview_process[$index]);
        $this->interview_process = array_values($this->interview_process);
    }

    private function parseSkills($skillsString)
    {
        return array_map('trim', explode(',', $skillsString));
    }

    private function parseTags($tagsString)
    {
        return array_map('trim', explode(',', $tagsString));
    }

    private function calculateOverallConversionRate()
    {
        $totalViews = JobPortal::sum('views_count');
        $totalApplications = JobApplication::count();
        
        return $totalViews > 0 ? round(($totalApplications / $totalViews) * 100, 2) : 0;
    }

    public function resetForm()
    {
        $this->reset([
            'title', 'description', 'requirements', 'responsibilities', 'benefits',
            'company_description', 'company_name', 'company_logo', 'company_website',
            'company_size', 'company_industry', 'employment_type', 'work_type',
            'experience_level', 'category', 'skills_required', 'tags', 'location',
            'state', 'city', 'salary_min', 'salary_max', 'salary_currency',
            'salary_period', 'salary_negotiable', 'hide_salary', 'application_method',
            'application_email', 'application_url', 'application_phone',
            'application_instructions', 'required_documents', 'application_deadline',
            'start_date', 'positions_available', 'screening_questions',
            'enable_ai_screening', 'interview_process', 'allow_remote_interview',
            'referral_bonus', 'diversity_hiring', 'is_premium', 'is_featured',
            'is_urgent', 'highlight_job', 'premium_features', 'editingJobId'
        ]);
        
        $this->showForm = false;
    }

    public function render()
    {
        $query = JobPortal::with(['postedBy', 'applications']);

        // Apply search
        if ($this->searchTerm) {
            $query->search($this->searchTerm);
        }

        // Apply filters
        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        if ($this->filterCategory) {
            $query->where('category', $this->filterCategory);
        }

        if ($this->filterEmploymentType) {
            $query->where('employment_type', $this->filterEmploymentType);
        }

        if ($this->filterWorkType) {
            $query->where('work_type', $this->filterWorkType);
        }

        // Apply sorting
        $query->orderBy($this->sortBy, $this->sortDirection);

        $jobs = $query->paginate(12);

        return view('livewire.career.job-management', [
            'jobs' => $jobs
        ]);
    }
}