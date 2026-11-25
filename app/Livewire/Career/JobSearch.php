<?php

namespace App\Livewire\Career;

use App\Services\Career\AdzunaSearchService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.dashboard', ['title' => 'Job Search', 'description' => 'Search external vacancies and follow provider application links', 'icon' => 'fas fa-briefcase', 'active' => 'job.available'])]

class JobSearch extends Component
{
    use WithPagination;

    // Search parameters
    #[Locked]
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

    public $opened_jobs = [];

    #[Locked]
    public $selected_job = null;

    public $show_job_modal = false;

    // Analytics
    public $search_history = [];

    public $popular_searches = [];

    protected $queryString = [
        'sector' => ['except' => ''],
        'location' => ['except' => ''],
        'country_code' => ['except' => 'gb'],
        'salary_min' => ['except' => ''],
        'salary_max' => ['except' => ''],
        'contract_type' => ['except' => ''],
        'sort_by' => ['except' => 'date'],
        'currentPage' => ['except' => 1],
    ];

    public function mount()
    {
        $this->loadSavedJobs();
        $this->loadOpenedJobs();
        $this->loadPopularSearches();
        $this->fetchJobs();
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['sector', 'location', 'country_code', 'salary_min', 'salary_max', 'contract_type', 'company', 'sort_by', 'distance'], true)) {
            $this->currentPage = 1;
            $this->fetchJobs();
        }
    }

    public function search()
    {
        $this->saveSearchToHistory();
        $this->currentPage = 1;
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
        $this->currentPage = 1;
        $this->fetchJobs();
    }

    public function fetchJobs()
    {
        $this->resetValidation();
        $this->loading = true;
        $started = microtime(true);
        try {
            $result = app(AdzunaSearchService::class)->search(collect(['country_code', 'currentPage', 'sector', 'location', 'salary_min', 'salary_max', 'contract_type', 'company', 'sort_by', 'distance'])
                ->mapWithKeys(fn ($key) => [$key => $this->$key])->all());
            $this->jobs = $this->enhanceJobData($result['jobs']);
            $this->total_jobs = $result['total'];
            $this->search_time = number_format((microtime(true) - $started) * 1000, 2);
        } catch (\Illuminate\Validation\ValidationException $exception) {
            $this->jobs = [];
            $this->total_jobs = 0;
            $this->search_time = '';
            foreach ($exception->errors() as $field => $messages) {
                $this->addError($field, $messages[0]);
            }
            session()->flash('error', 'External vacancy search is unavailable or its filters need updating.');
        } finally {
            $this->loading = false;
        }
    }

    protected function enhanceJobData($jobs)
    {
        return collect($jobs)->map(function ($job) {
            // Add calculated fields
            $job['is_saved'] = in_array($job['id'], $this->saved_jobs);
            $job['was_opened'] = in_array($job['id'], $this->opened_jobs);
            $job['salary_formatted'] = $this->formatSalary($job);
            $job['posted_time'] = $this->formatPostedTime($job['created']);

            return $job;
        })->toArray();
    }

    protected function formatSalary($job)
    {
        if ($job['salary_is_predicted'] ?? false) {
            return 'See salary estimate on Adzuna';
        }
        $currency = ['gb' => 'GBP', 'us' => 'USD', 'za' => 'ZAR', 'au' => 'AUD', 'ca' => 'CAD', 'de' => 'EUR', 'fr' => 'EUR', 'in' => 'INR', 'br' => 'BRL', 'nz' => 'NZD', 'pl' => 'PLN', 'at' => 'EUR', 'it' => 'EUR', 'nl' => 'EUR', 'sg' => 'SGD', 'es' => 'EUR', 'ch' => 'CHF', 'be' => 'EUR', 'mx' => 'MXN'][$this->country_code] ?? null;
        if (! $currency) {
            return 'See salary details on Adzuna';
        }
        if (isset($job['salary_min'], $job['salary_max'])) {
            return $currency.' '.number_format($job['salary_min']).' – '.number_format($job['salary_max']);
        }
        if (isset($job['salary_min'])) {
            return $currency.' '.number_format($job['salary_min']).'+';
        }

        return 'Salary not supplied';
    }

    protected function formatPostedTime($created)
    {
        try {
            return $created ? \Carbon\Carbon::parse($created)->diffForHumans() : 'Date not supplied';
        } catch (\Throwable) {
            return 'Date not supplied';
        }
    }

    public function saveJob($jobId)
    {
        abort_unless(collect($this->jobs)->contains(fn ($job) => (string) $job['id'] === (string) $jobId), 404);
        if (! in_array($jobId, $this->saved_jobs)) {
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

    public function openExternalJob($jobId)
    {
        abort_unless(collect($this->jobs)->contains(fn ($job) => (string) $job['id'] === (string) $jobId), 404);
        if (! in_array($jobId, $this->opened_jobs)) {
            $this->opened_jobs[] = $jobId;
            session()->put('opened_external_jobs', $this->opened_jobs);
            session()->flash('success', 'Provider link opened. Complete the application there; submission has not been confirmed by BootKode.');
        }

        $this->jobs = $this->enhanceJobData($this->jobs);
    }

    public function viewJobDetails($jobId)
    {
        $this->selected_job = collect($this->jobs)->firstWhere('id', $jobId);
        abort_unless($this->selected_job, 404);
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

    protected function loadOpenedJobs()
    {
        $this->opened_jobs = session()->get('opened_external_jobs', []);
    }

    protected function saveSearchToHistory()
    {
        if ($this->sector || $this->location) {
            $search = [
                'sector' => $this->sector,
                'location' => $this->location,
                'timestamp' => now(),
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
        $this->popular_searches = [];
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
