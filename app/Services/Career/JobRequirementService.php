<?php

namespace App\Services\Career;

use App\Models\Career\JobPortal;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class JobRequirementService
{
    public const KINDS = ['skill' => 'Competency / skill', 'experience' => 'Professional experience', 'qualification' => 'Educational qualification', 'certification' => 'Certification', 'portfolio' => 'Portfolio / project'];

    public function source(JobPortal $job): string
    {
        return trim(strip_tags(implode("\n", [$job->title, $job->description, $job->requirements, implode(', ', $job->skills_required ?? []), $job->experience_level])));
    }

    public function hash(JobPortal $job): string
    {
        return hash('sha256', $this->source($job));
    }

    public function normalize(string $label): string
    {
        // Keep punctuation significant: C++, C# and C are different competencies.
        return mb_strtolower(preg_replace('/\s+/u', ' ', trim($label)));
    }

    public function validate(array $items, JobPortal $job): array
    {
        $data = validator(['items' => $items], [
            'items' => 'present|array|max:30', 'items.*' => 'required|array:kind,label,mandatory,minimum_years,excerpt',
            'items.*.kind' => ['required', Rule::in(array_keys(self::KINDS))],
            'items.*.label' => 'required|string|max:120', 'items.*.mandatory' => 'required|boolean',
            'items.*.minimum_years' => 'nullable|numeric|between:0,80', 'items.*.excerpt' => 'required|string|max:500',
        ])->validate()['items'];
        $seen = [];
        foreach ($data as $index => &$item) {
            $item['label'] = trim($item['label']);
            $item['excerpt'] = trim($item['excerpt']);
            $item['mandatory'] = (bool) $item['mandatory'];
            $item['minimum_years'] = filled($item['minimum_years'] ?? null) ? (float) $item['minimum_years'] : null;
            if ($item['excerpt'] === '' || ! str_contains($this->source($job), $item['excerpt'])) {
                throw ValidationException::withMessages(["items.$index.excerpt" => 'Use an exact excerpt from the saved vacancy source.']);
            }
            if ($item['minimum_years'] !== null && $item['kind'] !== 'experience') {
                throw ValidationException::withMessages(["items.$index.minimum_years" => 'Only experience requirements may specify years.']);
            }
            $key = $item['kind'].':'.$this->normalize($item['label']);
            if (isset($seen[$key])) {
                throw ValidationException::withMessages(["items.$index.label" => 'Combine this duplicate requirement before publishing.']);
            }
            $seen[$key] = true;
        }

        return array_values($data);
    }

    public function publish(int $jobId, array $items, string $expectedHash, string $reason): void
    {
        $admin = app(CareerAccess::class)->administrator();
        validator(compact('reason'), ['reason' => 'required|string|min:10|max:1000'])->validate();
        DB::transaction(function () use ($jobId, $items, $expectedHash, $reason, $admin) {
            $job = JobPortal::whereKey($jobId)->lockForUpdate()->firstOrFail();
            abort_unless(hash_equals($this->hash($job), $expectedHash), 409, 'The vacancy changed. Reload and review the current source.');
            $items = $this->validate($items, $job);
            $history = $job->requirements_review_history ?? [];
            $history[] = ['at' => now()->toIso8601String(), 'by' => $admin->id, 'reason' => trim($reason),
                'previous' => $job->competency_requirements, 'previous_source_hash' => $job->requirements_source_hash,
                'requirements' => $items, 'source_hash' => $expectedHash];
            $job->forceFill(['competency_requirements' => $items, 'requirements_source_hash' => $expectedHash,
                'requirements_reviewed_at' => now(), 'requirements_reviewed_by' => $admin->id, 'requirements_review_history' => $history])->save();
        });
    }
}
