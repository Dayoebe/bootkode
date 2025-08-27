<?php

namespace App\Livewire\Career;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;

#[Layout('layouts.dashboard', ['title' => 'Job Search', 'description' => 'Browse and enroll in job opportunities', 'icon' => 'fas fa-briefcase', 'active' => 'job.available'])]

class JobSearch extends Component
{
    use WithPagination;

    // Search parameters
    public $jobs = [];
    public $country_code = 'gb';
    public $sector = '';
    public $location = '';
    public $salary_min = '';
    public $salary_max = '';
    public $contract_type = '';
    public $company = '';
    public $sort_by = 'date';
    public $distance = 10;
    
    // UI state
    public $loading = false;
    public $view_mode = 'grid'; // grid or list
    public $show_filters = false;
    public $currentPage = 1;
    public $total_jobs = 0;
    public $search_time = '';
    
    // Job management
    public $saved_jobs = [];
    public $applied_jobs = [];
    public $selected_job = null;
    public $show_job_modal = false;
    
    // Analytics
    public $search_history = [];
    public $popular_searches = [];

    protected $adzunaApiBaseUrl;
    protected $adzunaAppId;
    protected $adzunaAppKey;

    protected $queryString = [
        'sector' => ['except' => ''],
        'location' => ['except' => ''],
        'country_code' => ['except' => 'gb'],
        'salary_min' => ['except' => ''],
        'salary_max' => ['except' => ''],
        'contract_type' => ['except' => ''],
        'sort_by' => ['except' => 'date'],
        'currentPage' => ['except' => 1]
    ];

    public function mount()
    {
        $this->adzunaApiBaseUrl = config('services.adzuna.base_url');
        $this->adzunaAppId = config('services.adzuna.app_id');
        $this->adzunaAppKey = config('services.adzuna.app_key');

        $this->loadSavedJobs();
        $this->loadAppliedJobs();
        $this->loadPopularSearches();
        $this->fetchJobs();
    }

    public function updated($propertyName)
    {
        // Don't reset page for view mode changes
        if (!in_array($propertyName, ['view_mode', 'show_filters', 'show_job_modal'])) {
            $this->resetPage();
            $this->fetchJobs();
        }
    }

    public function search()
    {
        $this->saveSearchToHistory();
        $this->resetPage();
        $this->fetchJobs();
    }

    public function clearFilters()
    {
        $this->sector = '';
        $this->location = '';
        $this->salary_min = '';
        $this->salary_max = '';
        $this->contract_type = '';
        $this->company = '';
        $this->sort_by = 'date';
        $this->distance = 10;
        $this->resetPage();
        $this->fetchJobs();
    }

    public function fetchJobs()
    {
        $this->loading = true;
        $start_time = microtime(true);

        try {
            // Create cache key for this search
            $cache_key = md5(serialize([
                'country' => $this->country_code,
                'sector' => $this->sector,
                'location' => $this->location,
                'salary_min' => $this->salary_min,
                'salary_max' => $this->salary_max,
                'contract' => $this->contract_type,
                'company' => $this->company,
                'sort' => $this->sort_by,
                'page' => $this->currentPage
            ]));

            // Try to get from cache first (cache for 5 minutes)
            $cached_results = Cache::get($cache_key);
            if ($cached_results) {
                $this->jobs = $cached_results['jobs'];
                $this->total_jobs = $cached_results['total'];
                $this->search_time = $cached_results['search_time'];
                $this->loading = false;
                return;
            }

            $endpoint = "https://api.adzuna.com/v1/api/jobs/{$this->country_code}/search/{$this->currentPage}";

            $queryParams = array_filter([
                'app_id' => $this->adzunaAppId,
                'app_key' => $this->adzunaAppKey,
                'results_per_page' => 12,
                'what' => $this->sector,
                'where' => $this->location,
                'salary_min' => $this->salary_min ? $this->salary_min * 1000 : null,
                'salary_max' => $this->salary_max ? $this->salary_max * 1000 : null,
                'full_time' => $this->contract_type === 'full_time' ? 1 : null,
                'part_time' => $this->contract_type === 'part_time' ? 1 : null,
                'contract' => $this->contract_type === 'contract' ? 1 : null,
                'permanent' => $this->contract_type === 'permanent' ? 1 : null,
                'company' => $this->company,
                'sort_by' => $this->sort_by,
                'distance' => $this->distance,
            ]);

            $response = Http::timeout(30)->get($endpoint, $queryParams);

            if ($response->successful()) {
                $data = $response->json();
                $this->jobs = $this->enhanceJobData($data['results'] ?? []);
                $this->total_jobs = $data['count'] ?? 0;
                
                $end_time = microtime(true);
                $this->search_time = number_format(($end_time - $start_time) * 1000, 2);

                // Cache the results
                Cache::put($cache_key, [
                    'jobs' => $this->jobs,
                    'total' => $this->total_jobs,
                    'search_time' => $this->search_time
                ], now()->addMinutes(5));

            } else {
                Log::error('Adzuna API request failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'params' => $queryParams
                ]);
                $this->jobs = [];
                $this->total_jobs = 0;
                session()->flash('error', 'Could not retrieve jobs at this time. Please try again later.');
            }
        } catch (\Exception $e) {
            Log::error('Adzuna API exception', ['message' => $e->getMessage()]);
            $this->jobs = [];
            $this->total_jobs = 0;
            session()->flash('error', 'An error occurred while fetching jobs.');
        } finally {
            $this->loading = false;
        }
    }

    protected function enhanceJobData($jobs)
    {
        return collect($jobs)->map(function ($job) {
            // Add calculated fields
            $job['is_saved'] = in_array($job['id'], $this->saved_jobs);
            $job['is_applied'] = in_array($job['id'], $this->applied_jobs);
            $job['salary_formatted'] = $this->formatSalary($job);
            $job['posted_time'] = $this->formatPostedTime($job['created']);
            $job['match_score'] = $this->calculateMatchScore($job);
            
            return $job;
        })->toArray();
    }

    protected function formatSalary($job)
    {
        if (isset($job['salary_min']) && isset($job['salary_max'])) {
            $min = number_format($job['salary_min']);
            $max = number_format($job['salary_max']);
            return "£{$min} - £{$max}";
        } elseif (isset($job['salary_min'])) {
            return "£" . number_format($job['salary_min']) . "+";
        }
        return 'Salary not specified';
    }

    protected function formatPostedTime($created)
    {
        return \Carbon\Carbon::parse($created)->diffForHumans();
    }

    protected function calculateMatchScore($job)
    {
        $score = 0;
        
        // Basic scoring based on search terms
        if ($this->sector && stripos($job['title'], $this->sector) !== false) {
            $score += 30;
        }
        
        if ($this->location && stripos($job['location']['display_name'], $this->location) !== false) {
            $score += 20;
        }

        // Add randomness to simulate AI matching
        $score += rand(20, 50);
        
        return min(100, $score);
    }

    public function saveJob($jobId)
    {
        if (!in_array($jobId, $this->saved_jobs)) {
            $this->saved_jobs[] = $jobId;
            session()->put('saved_jobs', $this->saved_jobs);
            session()->flash('success', 'Job saved to your favorites!');
        } else {
            $this->saved_jobs = array_diff($this->saved_jobs, [$jobId]);
            session()->put('saved_jobs', $this->saved_jobs);
            session()->flash('success', 'Job removed from favorites.');
        }
        
        $this->jobs = $this->enhanceJobData($this->jobs);
    }

    public function applyToJob($jobId)
    {
        if (!in_array($jobId, $this->applied_jobs)) {
            $this->applied_jobs[] = $jobId;
            session()->put('applied_jobs', $this->applied_jobs);
            session()->flash('success', 'Application tracked! Good luck!');
        }
        
        $this->jobs = $this->enhanceJobData($this->jobs);
    }

    public function viewJobDetails($jobId)
    {
        $this->selected_job = collect($this->jobs)->firstWhere('id', $jobId);
        $this->show_job_modal = true;
    }

    public function closeJobModal()
    {
        $this->show_job_modal = false;
        $this->selected_job = null;
    }

    protected function loadSavedJobs()
    {
        $this->saved_jobs = session()->get('saved_jobs', []);
    }

    protected function loadAppliedJobs()
    {
        $this->applied_jobs = session()->get('applied_jobs', []);
    }

    protected function saveSearchToHistory()
    {
        if ($this->sector || $this->location) {
            $search = [
                'sector' => $this->sector,
                'location' => $this->location,
                'timestamp' => now()
            ];
            
            $history = session()->get('search_history', []);
            array_unshift($history, $search);
            
            // Keep only last 10 searches
            $history = array_slice($history, 0, 10);
            session()->put('search_history', $history);
            $this->search_history = $history;
        }
    }

    protected function loadPopularSearches()
    {
        // In a real app, this would come from database analytics
        $this->popular_searches = [
            ['term' => 'Software Developer', 'count' => 1250],
            ['term' => 'Data Scientist', 'count' => 890],
            ['term' => 'Marketing Manager', 'count' => 670],
            ['term' => 'Sales Representative', 'count' => 540],
            ['term' => 'Project Manager', 'count' => 480]
        ];
    }

    public function usePopularSearch($term)
    {
        $this->sector = $term;
        $this->search();
    }

    public function nextPage()
    {
        $this->currentPage++;
        $this->fetchJobs();
    }

    public function previousPage()
    {
        if ($this->currentPage > 1) {
            $this->currentPage--;
            $this->fetchJobs();
        }
    }

    public function render()
    {
        return view('livewire.career.job-search');
    }
}