<?php

namespace App\Services;

use App\Models\Career\JobAlert;
use App\Models\Core\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class JobAlertService
{
    public const CURRENCIES = ['NGN', 'USD', 'EUR', 'GBP', 'CAD', 'AUD'];

    public function ready(): bool
    {
        return Schema::hasTable('job_alerts') && Schema::hasTable('job_alert_deliveries') && Schema::hasColumns('job_alerts', ['filter_hash', 'salary_currency']);
    }

    public function account(User $user): User
    {
        $user->refresh();
        abort_unless($user->is_active && $user->email_verified_at, 403);

        return $user;
    }

    public function create(User $user, array $input): JobAlert
    {
        abort_unless($this->ready(), 503, 'Saved alerts are being set up. Job search remains available.');
        $allowed = ['title', 'keywords', 'location', 'category', 'employment_type', 'work_type', 'experience_level', 'salary_min', 'salary_max', 'salary_currency', 'frequency'];
        $input = \Illuminate\Support\Arr::only($input, $allowed);
        foreach ($allowed as $key) {
            if (($input[$key] ?? null) === '') {
                $input[$key] = null;
            }
        }
        $data = validator($input, [
            'title' => 'required|string|max:100', 'keywords' => 'nullable|array|max:10', 'keywords.*' => 'required|string|max:100',
            'location' => 'nullable|string|max:255', 'category' => 'nullable|string|max:100',
            'employment_type' => ['nullable', Rule::in(['full-time', 'part-time', 'contract', 'temporary', 'internship', 'freelance'])],
            'work_type' => 'nullable|in:on-site,remote,hybrid', 'experience_level' => 'nullable|in:entry,junior,mid,senior,executive,director',
            'salary_min' => 'nullable|numeric|decimal:0,2|between:0,999999999999.99', 'salary_max' => 'nullable|numeric|decimal:0,2|between:0,999999999999.99',
            'salary_currency' => ['nullable', Rule::in(self::CURRENCIES)], 'frequency' => 'required|in:daily,weekly',
        ])->validate();
        if (isset($data['salary_min']) || isset($data['salary_max'])) {
            validator($data, ['salary_currency' => ['required', Rule::in(self::CURRENCIES)]])->validate();
            if (isset($data['salary_min'],$data['salary_max']) && $data['salary_max'] < $data['salary_min']) {
                throw \Illuminate\Validation\ValidationException::withMessages(['salary_max' => 'Maximum salary must be at least the minimum.']);
            }
        } else {
            $data['salary_currency'] = null;
        }
        $data['keywords'] = array_values(array_unique(array_filter(array_map('trim', $data['keywords'] ?? []), fn ($word) => $word !== '')));
        foreach (['location', 'category'] as $key) {
            $data[$key] = isset($data[$key]) ? trim($data[$key]) : null;
        }
        foreach (['salary_min', 'salary_max'] as $key) {
            $data[$key] = isset($data[$key]) ? number_format((float) $data[$key], 2, '.', '') : null;
        }
        $filter = [];
        foreach (['keywords', 'location', 'category', 'employment_type', 'work_type', 'experience_level', 'salary_min', 'salary_max', 'salary_currency'] as $key) {
            $filter[$key] = $data[$key] ?? null;
        }
        $data['filter_hash'] = hash('sha256', json_encode($filter, JSON_THROW_ON_ERROR));

        return DB::transaction(function () use ($user, $data) {
            User::whereKey($user->id)->lockForUpdate()->firstOrFail();
            $this->account($user);
            $existing = JobAlert::where('user_id', $user->id)->where('filter_hash', $data['filter_hash'])->first();
            if ($existing) {
                return $existing;
            }
            abort_if(JobAlert::where('user_id', $user->id)->count() >= 10, 422, 'You can save up to ten job alerts. Remove an old alert first.');

            return JobAlert::create(array_merge($data, ['user_id' => $user->id, 'is_active' => true]));
        });
    }

    public function owned(User $user, int $id): JobAlert
    {
        $this->account($user);

        return JobAlert::where('user_id', $user->id)->findOrFail($id);
    }

    public function pause(User $user, int $id, bool $active): void
    {
        $this->owned($user, $id)->update(['is_active' => $active]);
    }

    public function remove(User $user, int $id): void
    {
        $this->owned($user, $id)->delete();
    }

    public function frequency(User $user, int $id, string $frequency): void
    {
        validator(compact('frequency'), ['frequency' => 'required|in:daily,weekly'])->validate();
        $this->owned($user, $id)->update(['frequency' => $frequency]);
    }

    public function checkOwn(User $user, int $id): int
    {
        $this->owned($user, $id);

        return $this->deliver($id, true);
    }

    public function deliver(int $id, bool $manual = false): int
    {
        return DB::transaction(function () use ($id, $manual) {
            $candidate = JobAlert::findOrFail($id);
            $user = User::whereKey($candidate->user_id)->lockForUpdate()->firstOrFail();
            $alert = JobAlert::whereKey($id)->lockForUpdate()->firstOrFail();
            if (! $user->is_active || ! $user->email_verified_at || ! $alert->is_active) {
                return 0;
            }
            if (! $manual && $alert->last_sent_at && $alert->last_sent_at->gt($alert->frequency === 'weekly' ? now()->subWeek() : now()->subDay())) {
                return 0;
            }
            $query = $alert->matchingJobs();
            $count = (clone $query)->count();
            if (! $count) {
                return 0;
            }
            $jobs = (clone $query)->orderBy('created_at')->orderBy('id')->limit(50)->pluck('id')->all();
            $cursor = hash('sha256', implode(',', $jobs));
            app(PortalNotificationService::class)->record($user, 'job-alert:'.$alert->id.':'.$cursor,
                ['type' => 'job_alert', 'title' => 'New job matches', 'message' => count($jobs).' public job(s) matched your saved alert.',
                    'action_url' => route('user.job', ['alert' => $alert->id]), 'icon' => 'fas fa-briefcase', 'job_alert_id' => $alert->id, 'job_ids' => $jobs, 'match_count' => count($jobs), 'remaining_count' => $count - count($jobs)], 'job_alert');
            foreach ($jobs as $jobId) {
                DB::table('job_alert_deliveries')->insert(['job_alert_id' => $alert->id, 'job_id' => $jobId, 'created_at' => now()]);
            }
            $alert->update(['last_sent_at' => now()]);

            return count($jobs);
        });
    }
}
