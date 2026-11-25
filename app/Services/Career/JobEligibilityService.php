<?php

namespace App\Services\Career;

use App\Models\Career\CompetencyEvidence;
use App\Models\Career\JobPortal;
use App\Models\Core\User;
use App\Models\Learning\Course;

class JobEligibilityService
{
    public function evaluate(User $user, JobPortal $job): array
    {
        app(CareerAccess::class)->owner($user);
        $job = JobPortal::where('is_public', true)->whereIn('status', ['active', 'expired'])->findOrFail($job->id);
        $result = ['status' => 'assessment_required', 'label' => 'Requirements not reviewed', 'can_apply' => true, 'reviewed' => false,
            'explanation' => 'BootKode has not reviewed structured requirements for this vacancy. Its existing application process remains available; no eligibility is claimed.',
            'requirements' => [], 'mandatory_met' => 0, 'mandatory_total' => 0];
        if (! $job->isActive() || ($job->application_deadline && $job->application_deadline->lte(now()))) {
            return array_replace($result, ['status' => 'vacancy_expired', 'label' => 'Vacancy closed', 'can_apply' => false,
                'explanation' => 'This vacancy has expired or its application deadline has passed. Learning options do not guarantee it will reopen.']);
        }
        if (! $job->requirements_reviewed_at) {
            return $result;
        }
        $requirements = app(JobRequirementService::class);
        if (! hash_equals($requirements->hash($job), (string) $job->requirements_source_hash)) {
            return array_replace($result, ['can_apply' => false, 'label' => 'Requirements need another review',
                'explanation' => 'The vacancy source changed after review. Applications pause until an administrator checks the current requirements.']);
        }
        $result['reviewed'] = true;
        $evidence = CompetencyEvidence::where('user_id', $user->id)->where('review_status', 'verified')->get()
            ->filter(fn ($item) => app(CompetencyEvidenceService::class)->usable($item));
        foreach ($job->competency_requirements ?? [] as $requirement) {
            $matched = $evidence->filter(fn ($item) => $item->kind === $requirement['kind']
                && $requirements->normalize($item->label) === $requirements->normalize($requirement['label'])
                && ($requirement['kind'] !== 'experience' || ($item->years_experience !== null && (float) $item->years_experience >= (float) ($requirement['minimum_years'] ?? 0))));
            $result['requirements'][] = [...$requirement, 'met' => $matched->isNotEmpty(),
                'reason' => $matched->isNotEmpty() ? 'Supported by your current reviewed evidence.' : 'No current reviewed evidence satisfies this requirement.'];
        }
        $mandatory = collect($result['requirements'])->where('mandatory', true);
        $missing = $mandatory->where('met', false);
        $result['mandatory_total'] = $mandatory->count();
        $result['mandatory_met'] = $mandatory->where('met', true)->count();
        $result['can_apply'] = $missing->isEmpty();
        if ($missing->isEmpty()) {
            $preferredMissing = collect($result['requirements'])->where('mandatory', false)->where('met', false)->isNotEmpty();

            return array_replace($result, ['status' => $preferredMissing ? 'partially_qualified' : 'eligible', 'label' => $preferredMissing ? 'Preferred skills to develop' : 'Eligible against reviewed requirements',
                'explanation' => $preferredMissing ? 'All mandatory requirements are supported. Missing preferred requirements do not block your application.' : 'Your current evidence satisfies the reviewed mandatory requirements. The employer still makes the hiring decision.']);
        }
        $status = $missing->contains('kind', 'experience') ? 'experience_requirement_not_met'
            : ($missing->contains(fn ($item) => in_array($item['kind'], ['qualification', 'certification'])) ? 'qualification_requirement_not_met'
                : ($result['mandatory_met'] > 0 ? 'skills_gap_identified' : 'assessment_required'));
        $labels = ['experience_requirement_not_met' => 'Experience requirement not verified', 'qualification_requirement_not_met' => 'Qualification requirement not verified',
            'skills_gap_identified' => 'Skills gap identified', 'assessment_required' => 'Assessment or evidence review required'];

        return array_replace($result, ['status' => $status, 'label' => $labels[$status],
            'explanation' => 'Some mandatory requirements do not have sufficient current reviewed evidence. You can browse the vacancy and prepare, but cannot submit a new BootKode application yet.']);
    }

    public function learningOptions(array $eligibility): array
    {
        $options = [];
        foreach (collect($eligibility['requirements'])->where('met', false)->where('kind', 'skill')->take(10) as $requirement) {
            $term = trim($requirement['label']);
            $like = '%'.addcslashes($term, '\\%_').'%';
            $courses = Course::publiclyAvailable()->where(function ($query) use ($like) {
                $query->where('title', 'like', $like)->orWhere('tags', 'like', $like)->orWhere('learning_outcomes', 'like', $like);
            })->orderBy('title')->limit(40)->get(['id', 'title', 'slug', 'difficulty_level', 'prerequisites', 'tags', 'learning_outcomes'])
                ->filter(fn ($course) => preg_match('/(?<![\pL\pN])'.preg_quote(mb_strtolower($term), '/').'(?![\pL\pN])/u',
                    mb_strtolower(strip_tags($course->title).' '.json_encode([$course->tags, $course->learning_outcomes], JSON_UNESCAPED_UNICODE))) === 1)->take(3);
            $options[] = ['skill' => $term, 'mandatory' => $requirement['mandatory'], 'courses' => $courses->map(fn ($course) => [
                'id' => $course->id, 'title' => $course->title, 'difficulty' => $course->difficulty_level,
                'prerequisites' => $course->prerequisites ?? [], 'url' => route('courses.show', $course->slug),
            ])->all()];
        }

        return $options;
    }
}
