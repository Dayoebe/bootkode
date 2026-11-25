<?php

namespace App\Services\Career;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AdzunaSearchService
{
    public function search(array $input): array
    {
        $input = validator($input, [
            'country_code' => ['required', 'regex:/^[a-z]{2}$/D'], 'currentPage' => 'required|integer|between:1,100',
            'sector' => 'nullable|string|max:200', 'location' => 'nullable|string|max:200', 'company' => 'nullable|string|max:200',
            'salary_min' => 'nullable|numeric|between:0,1000000', 'salary_max' => 'nullable|numeric|between:0,1000000',
            'contract_type' => 'nullable|in:full_time,part_time,contract,permanent', 'sort_by' => 'required|in:date,relevance,salary',
            'distance' => 'required|integer|between:0,500',
        ])->validate();
        if (filled($input['salary_min'] ?? null) && filled($input['salary_max'] ?? null) && $input['salary_max'] < $input['salary_min']) {
            throw ValidationException::withMessages(['salary_max' => 'Maximum salary must be at least the minimum.']);
        }
        $id = config('services.adzuna.app_id');
        $key = config('services.adzuna.app_key');
        if (! filled($id) || ! filled($key)) {
            throw ValidationException::withMessages(['sector' => 'External vacancy search is not configured. The BootKode job portal remains available.']);
        }
        $cacheKey = 'adzuna:'.hash('sha256', json_encode([$id, $input]));

        return Cache::remember($cacheKey, now()->addMinutes(5), function () use ($input, $id, $key) {
            $limiter = 'adzuna-search:'.auth()->id();
            if (RateLimiter::tooManyAttempts($limiter, 20)) {
                throw ValidationException::withMessages(['sector' => 'Too many searches. Please try again in a minute.']);
            }
            RateLimiter::hit($limiter, 60);
            $parameters = array_filter([
                'app_id' => $id, 'app_key' => $key, 'results_per_page' => 12,
                'what' => $input['sector'] ?? null, 'where' => $input['location'] ?? null, 'company' => $input['company'] ?? null,
                'salary_min' => filled($input['salary_min'] ?? null) ? $input['salary_min'] * 1000 : null,
                'salary_max' => filled($input['salary_max'] ?? null) ? $input['salary_max'] * 1000 : null,
                'full_time' => ($input['contract_type'] ?? '') === 'full_time' ? 1 : null,
                'part_time' => ($input['contract_type'] ?? '') === 'part_time' ? 1 : null,
                'contract' => ($input['contract_type'] ?? '') === 'contract' ? 1 : null,
                'permanent' => ($input['contract_type'] ?? '') === 'permanent' ? 1 : null,
                'sort_by' => $input['sort_by'], 'distance' => $input['distance'],
            ], fn ($value) => $value !== null && $value !== '');
            try {
                // Fixed authority; do not log URL, query parameters, response bodies or exception messages.
                $response = Http::acceptJson()->connectTimeout(5)->timeout(15)
                    ->get('https://api.adzuna.com/v1/api/jobs/'.$input['country_code'].'/search/'.$input['currentPage'], $parameters);
                if (! $response->successful()) {
                    Log::warning('External vacancy search failed.', ['provider' => 'adzuna', 'status' => $response->status()]);
                    throw new \RuntimeException('provider_failure');
                }
                $payload = $response->json();
                if (! is_array($payload) || ! is_array($payload['results'] ?? null) || ! is_numeric($payload['count'] ?? null)) {
                    throw new \RuntimeException('invalid_response');
                }
                $jobs = collect($payload['results'])->take(12)->filter(fn ($job) => is_array($job)
                    && is_scalar($job['id'] ?? null) && preg_match('/^[0-9]{1,30}$/D', (string) $job['id'])
                    && is_string($job['title'] ?? null) && is_string($job['description'] ?? null)
                    && $this->safeDestination($job['redirect_url'] ?? null))
                    ->unique('id')->map(fn ($job) => [
                        'id' => (string) $job['id'], 'title' => Str::limit(strip_tags($job['title']), 255, ''),
                        'description' => Str::limit(strip_tags($job['description']), 18000, ''),
                        'redirect_url' => $job['redirect_url'], 'created' => is_string($job['created'] ?? null) ? $job['created'] : null,
                        'company' => ['display_name' => Str::limit(strip_tags((string) data_get($job, 'company.display_name', 'Not supplied')), 255, '')],
                        'location' => ['display_name' => Str::limit(strip_tags((string) data_get($job, 'location.display_name', 'Not supplied')), 255, '')],
                        'category' => ['label' => Str::limit(strip_tags((string) data_get($job, 'category.label', 'Not supplied')), 100, '')],
                        'salary_min' => is_numeric($job['salary_min'] ?? null) ? max(0, (float) $job['salary_min']) : null,
                        'salary_max' => is_numeric($job['salary_max'] ?? null) ? max(0, (float) $job['salary_max']) : null,
                        'salary_is_predicted' => (bool) ($job['salary_is_predicted'] ?? false),
                        'contract_type' => is_string($job['contract_type'] ?? null) ? Str::limit($job['contract_type'], 50, '') : null,
                    ])->values()->all();

                return ['jobs' => $jobs, 'total' => max(0, (int) $payload['count'])];
            } catch (\Throwable $exception) {
                Log::warning('External vacancy search unavailable.', ['provider' => 'adzuna', 'exception_type' => $exception::class]);
                throw ValidationException::withMessages(['sector' => 'External vacancies could not be retrieved. Please try again later.']);
            }
        });
    }

    private function safeDestination(mixed $url): bool
    {
        if (! is_string($url) || ! filter_var($url, FILTER_VALIDATE_URL) || strlen($url) > 4096) {
            return false;
        }
        $parts = parse_url($url);

        // Preserve provider tracking links; never substitute a guessed employer destination.
        return in_array(strtolower($parts['scheme'] ?? ''), ['http', 'https'], true)
            && ! isset($parts['user']) && ! isset($parts['pass'])
            && preg_match('/(^|\.)adzuna\.(co\.uk|com|com\.au|co\.za|ca|de|fr|in|it|nl|pl|at|com\.br|co\.nz|sg|es|ch|be|com\.mx)$/D', strtolower($parts['host'] ?? '')) === 1;
    }
}
