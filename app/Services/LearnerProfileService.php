<?php

namespace App\Services;

use App\Models\Ai\AiLearningProfile;
use App\Models\Core\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class LearnerProfileService
{
    public const LEVELS = [
        'complete_beginner' => 'Complete beginner', 'beginner' => 'Beginner',
        'intermediate' => 'Intermediate', 'advanced' => 'Advanced', 'expert' => 'Expert',
    ];

    public const PACES = ['gentle' => 'A little at a time', 'steady' => 'A steady routine', 'intensive' => 'An intensive plan'];

    public const FORMATS = ['reading' => 'Reading', 'video' => 'Video', 'audio' => 'Audio', 'practice' => 'Practical activities'];

    public function authorize(): User
    {
        $user = User::find(auth()->id());
        abort_unless($user?->is_active && $user->hasVerifiedEmail() && $user->hasAnyRole(['student', 'instructor', 'academy_admin', 'super_admin']), 403);

        return $user;
    }

    public function profile(): ?AiLearningProfile
    {
        return AiLearningProfile::where('user_id', $this->authorize()->id)->first();
    }

    public function defaults(?AiLearningProfile $profile): array
    {
        return [
            'category_ids' => $profile?->interests['category_ids'] ?? [],
            'custom_topics' => implode("\n", $profile?->interests['topics'] ?? []),
            'skills' => implode("\n", $profile?->existing_knowledge['skills'] ?? []),
            'completed_courses' => implode("\n", $profile?->existing_knowledge['completed_courses'] ?? []),
            'experience' => $profile?->existing_knowledge['experience'] ?? '',
            'practical_experience' => $profile?->existing_knowledge['practical_experience'] ?? '',
            'starting_level' => $profile?->starting_level ?? '',
            'goal' => $profile?->goal ?? '',
            'pace' => $profile?->learning_preferences['pace'] ?? '',
            'minutes_per_week' => $profile?->learning_preferences['minutes_per_week'] ?? '',
            'formats' => $profile?->learning_preferences['formats'] ?? [],
            'target_weeks' => $profile?->learning_preferences['target_weeks'] ?? '',
            'language' => $profile?->learning_preferences['language'] ?? '',
        ];
    }

    public function rules(?int $step = null): array
    {
        $steps = [
            1 => [
                'category_ids' => ['present', 'array', 'max:20'],
                'category_ids.*' => ['required', 'integer', 'distinct', 'exists:course_categories,id'],
                'custom_topics' => ['nullable', 'string', 'max:1000', $this->linesRule(10, 80)],
            ],
            2 => [
                'skills' => ['nullable', 'string', 'max:2000', $this->linesRule(20, 100)],
                'completed_courses' => ['nullable', 'string', 'max:2000', $this->linesRule(20, 100)],
                'experience' => ['nullable', 'string', 'max:2000'],
                'practical_experience' => ['nullable', 'string', 'max:2000'],
                'starting_level' => ['nullable', Rule::in(array_keys(self::LEVELS))],
            ],
            3 => [
                'goal' => ['required', 'string', 'max:255'],
                'pace' => ['nullable', Rule::in(array_keys(self::PACES))],
                'minutes_per_week' => ['nullable', 'integer', 'min:15', 'max:10080'],
                'formats' => ['present', 'array', 'max:4'],
                'formats.*' => ['required', 'distinct', Rule::in(array_keys(self::FORMATS))],
                'target_weeks' => ['nullable', 'integer', 'min:1', 'max:260'],
                'language' => ['nullable', 'string', 'max:80'],
            ],
        ];

        return $step === null ? array_merge(...array_values($steps)) : ($steps[$step] ?? []);
    }

    public function save(array $input): AiLearningProfile
    {
        $user = $this->authorize();
        $input = array_replace($this->defaults(null), $input);
        $input = array_map(fn ($value) => is_string($value) ? trim($value) : $value, $input);
        $data = Validator::make($input, $this->rules(), [], $this->attributes())->validate();

        return DB::transaction(function () use ($user, $data) {
            // Serialize even concurrent first saves before a profile row exists.
            User::whereKey($user->id)->lockForUpdate()->firstOrFail();
            $profile = AiLearningProfile::firstOrNew(['user_id' => $user->id]);
            $profile->fill([
                'goal' => $data['goal'],
                'interests' => ['category_ids' => array_map('intval', $data['category_ids']), 'topics' => $this->lines($data['custom_topics'] ?? '')],
                'existing_knowledge' => [
                    'skills' => $this->lines($data['skills'] ?? ''),
                    'completed_courses' => $this->lines($data['completed_courses'] ?? ''),
                    'experience' => $data['experience'] ?: null,
                    'practical_experience' => $data['practical_experience'] ?: null,
                ],
                'starting_level' => $data['starting_level'] ?: null,
                'learning_preferences' => [
                    'pace' => $data['pace'] ?: null,
                    'minutes_per_week' => $data['minutes_per_week'] === '' || $data['minutes_per_week'] === null ? null : (int) $data['minutes_per_week'],
                    'formats' => array_values($data['formats']),
                    'target_weeks' => $data['target_weeks'] === '' || $data['target_weeks'] === null ? null : (int) $data['target_weeks'],
                    'language' => $data['language'] ?: null,
                ],
                'onboarding_completed_at' => $profile->onboarding_completed_at ?? now(),
                'onboarding_skipped_at' => null,
                'diagnosed_at' => null,
            ])->save();

            return $profile;
        });
    }

    public function skip(): void
    {
        $user = $this->authorize();
        DB::transaction(function () use ($user) {
            User::whereKey($user->id)->lockForUpdate()->firstOrFail();
            $profile = AiLearningProfile::firstOrNew(['user_id' => $user->id]);
            if (! $profile->onboarding_completed_at) {
                $profile->onboarding_skipped_at ??= now();
                $profile->save();
            }
        });
    }

    public function attributes(): array
    {
        return [
            'category_ids' => 'categories', 'category_ids.*' => 'category',
            'custom_topics' => 'other interests', 'skills' => 'existing skills',
            'completed_courses' => 'completed courses', 'experience' => 'experience',
            'practical_experience' => 'practical experience', 'starting_level' => 'starting level',
            'goal' => 'learning goal', 'pace' => 'learning pace', 'minutes_per_week' => 'weekly learning time',
            'formats' => 'preferred formats', 'formats.*' => 'preferred format',
            'target_weeks' => 'desired timeline', 'language' => 'preferred language',
        ];
    }

    private function lines(string $text): array
    {
        return collect(preg_split('/\R/u', $text))->map(fn ($line) => trim($line))->filter(fn ($line) => $line !== '')
            ->unique(fn ($line) => mb_strtolower($line))->values()->all();
    }

    private function linesRule(int $count, int $length): \Closure
    {
        return function (string $attribute, mixed $value, \Closure $fail) use ($count, $length) {
            if (! is_string($value)) {
                return;
            }
            $lines = $this->lines($value);
            if (count($lines) > $count || collect($lines)->contains(fn ($line) => mb_strlen($line) > $length)) {
                $fail("Use at most {$count} entries, one per line, with up to {$length} characters each.");
            }
        };
    }
}
