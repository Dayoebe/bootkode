<?php

namespace App\Services;

use App\Models\Ai\AiLearningProfile;
use App\Models\Ai\AiTutorMessage;
use App\Models\Assessment\StudentAnswer;
use App\Models\Core\User;
use App\Models\Credentials\Certificate;
use App\Models\Learning\Course;
use App\Models\Learning\CourseEnrollment;
use App\Models\Mentorship\CodeReview;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Throwable;

class LearningAiService
{
    private const SKILLS = [
        'foundation' => [
            'label' => 'Programming Foundations',
            'terms' => ['foundation', 'fundamental', 'beginner', 'intro', 'logic', 'html', 'css', 'javascript basics'],
        ],
        'frontend' => [
            'label' => 'Frontend Engineering',
            'terms' => ['frontend', 'html', 'css', 'javascript', 'react', 'vue', 'ui', 'tailwind', 'component'],
        ],
        'backend' => [
            'label' => 'Backend Engineering',
            'terms' => ['backend', 'php', 'laravel', 'api', 'server', 'node', 'python', 'controller', 'service'],
        ],
        'database' => [
            'label' => 'Database Design',
            'terms' => ['database', 'mysql', 'sql', 'migration', 'eloquent', 'query', 'schema'],
        ],
        'testing' => [
            'label' => 'Testing & QA',
            'terms' => ['test', 'testing', 'phpunit', 'pest', 'qa', 'coverage', 'debug'],
        ],
        'security' => [
            'label' => 'Security & Reliability',
            'terms' => ['security', 'auth', 'authorization', 'validation', 'csrf', 'permission', 'webhook'],
        ],
        'project_delivery' => [
            'label' => 'Project Delivery',
            'terms' => ['project', 'portfolio', 'github', 'deployment', 'code review', 'shipping', 'evidence'],
        ],
        'career_readiness' => [
            'label' => 'Career Readiness',
            'terms' => ['career', 'resume', 'interview', 'job', 'certificate', 'mentor', 'portfolio'],
        ],
    ];

    public function profile(User $user, ?string $goal = null, bool $refresh = false): AiLearningProfile
    {
        $profile = AiLearningProfile::firstOrNew(['user_id' => $user->id]);
        $goal = $goal === null ? $profile->goal : trim($goal);
        $goal = $goal !== '' ? $goal : null;

        if (! $refresh && $profile->exists && $profile->diagnosed_at && $profile->diagnosed_at->gt(now()->subMinutes(30)) && $profile->goal === $goal) {
            return $profile;
        }

        $signals = $this->signals($user);
        $diagnosis = $this->diagnoseSkills($signals, $goal);
        $feedback = $this->assessmentFeedback($signals);
        $recommendations = $this->courseRecommendations($user, $diagnosis, $signals);
        $path = $this->adaptivePath($user, $signals, $diagnosis, $feedback, $recommendations, $goal);

        $profile->fill([
            'goal' => $goal,
            'signals' => $signals,
            'skill_diagnosis' => $diagnosis,
            'assessment_feedback' => $feedback,
            'course_recommendations' => $recommendations,
            'adaptive_path' => $path,
            'diagnosed_at' => now(),
        ])->save();

        return $profile;
    }

    public function askTutor(User $user, string $question, ?int $courseId = null): AiTutorMessage
    {
        $question = trim(Str::limit(strip_tags($question), 1200, ''));
        $context = $this->contextSnippets($user, $question, $courseId);
        $answer = $this->localTutorAnswer($user, $question, $context);
        $source = 'local_context';

        if (! empty($context) && config('services.openai.key')) {
            try {
                $remoteAnswer = $this->openAiTutorAnswer($question, $context);

                if ($remoteAnswer) {
                    $answer = $remoteAnswer;
                    $source = 'openai';
                }
            } catch (Throwable $exception) {
                report($exception);
            }
        }

        return AiTutorMessage::create([
            'user_id' => $user->id,
            'course_id' => $courseId,
            'question' => $question,
            'answer' => $answer,
            'source' => $source,
            'context' => $context,
            'metadata' => [
                'context_count' => count($context),
                'answered_at' => now()->toIso8601String(),
            ],
        ]);
    }

    public function recentTutorMessages(User $user, int $limit = 8)
    {
        return AiTutorMessage::with('course')
            ->where('user_id', $user->id)
            ->latest()
            ->limit($limit)
            ->get();
    }

    private function signals(User $user): array
    {
        $enrollments = CourseEnrollment::with([
            'course.category',
            'course.sections.lessons.assessments.questions',
            'course.directAssessments.questions',
        ])
            ->where('user_id', $user->id)
            ->latest('updated_at')
            ->limit(25)
            ->get();

        $completedLessonIds = $user->completedLessons()->pluck('lessons.id')->all();
        $attempts = $this->assessmentAttempts($user);
        $certificates = Certificate::with('course')
            ->where('user_id', $user->id)
            ->latest('updated_at')
            ->limit(20)
            ->get()
            ->map(fn (Certificate $certificate) => [
                'course_id' => $certificate->course_id,
                'course_title' => $certificate->course?->title,
                'status' => $certificate->status,
                'issued_date' => optional($certificate->issued_date)->toDateString(),
                'score' => $certificate->score,
            ])
            ->values()
            ->all();

        $codeReviews = CodeReview::with('mentorship')
            ->where(function (Builder $query) use ($user) {
                $query->where('requested_by', $user->id)
                    ->orWhereHas('mentorship', fn (Builder $mentorship) => $mentorship->where('mentee_id', $user->id));
            })
            ->latest('updated_at')
            ->limit(10)
            ->get()
            ->map(fn (CodeReview $review) => [
                'title' => $review->title,
                'status' => $review->status,
                'approval_status' => $review->approval_status,
                'score' => $review->rubric_total_score,
                'rubric_scores' => $review->rubric_scores ?? [],
                'technologies' => $review->technologies ?? [],
                'improvement_areas' => $review->improvement_areas ?? [],
            ])
            ->values()
            ->all();

        return [
            'user' => [
                'id' => $user->id,
                'role' => $user->role,
                'skills' => $user->skills ?? [],
                'occupation' => $user->occupation,
            ],
            'enrollments' => $enrollments->map(function (CourseEnrollment $enrollment) use ($completedLessonIds) {
                $course = $enrollment->course;
                $lessonIds = $course?->sections?->flatMap(fn ($section) => $section->lessons->pluck('id'))->values() ?? collect();

                return [
                    'course_id' => $course?->id,
                    'title' => $course?->title,
                    'slug' => $course?->slug,
                    'description' => $course?->description,
                    'difficulty' => $course?->difficulty_level,
                    'tags' => $course?->tags ?? [],
                    'learning_outcomes' => $course?->learning_outcomes ?? [],
                    'progress' => (int) $enrollment->progress_percentage,
                    'is_completed' => (bool) $enrollment->is_completed,
                    'completed_lessons' => $lessonIds->intersect($completedLessonIds)->count(),
                    'total_lessons' => $lessonIds->count(),
                    'last_accessed_at' => optional($enrollment->updated_at)->toIso8601String(),
                ];
            })->values()->all(),
            'assessment_attempts' => $attempts,
            'certificates' => $certificates,
            'code_reviews' => $codeReviews,
            'portfolio_count' => Schema::hasTable('portfolios')
                ? DB::table('portfolios')->where('user_id', $user->id)->count()
                : 0,
        ];
    }

    private function assessmentAttempts(User $user): array
    {
        if (! Schema::hasTable('student_answers')) {
            return [];
        }

        return StudentAnswer::with(['assessment.course', 'assessment.lesson', 'question'])
            ->where('user_id', $user->id)
            ->whereNotNull('submitted_at')
            ->latest('submitted_at')
            ->limit(200)
            ->get()
            ->groupBy(fn (StudentAnswer $answer) => $answer->assessment_id . ':' . $answer->attempt_number)
            ->map(function (Collection $answers) {
                $assessment = $answers->first()->assessment;
                $maxPoints = max(1, (float) $answers->sum(fn ($answer) => (float) ($answer->question?->points ?? 1)));
                $earned = (float) $answers->sum('points_earned');
                $percentage = round(($earned / $maxPoints) * 100, 1);

                return [
                    'assessment_id' => $assessment?->id,
                    'assessment_title' => $assessment?->title,
                    'course_id' => $assessment?->course_id,
                    'course_title' => $assessment?->course?->title,
                    'lesson_title' => $assessment?->lesson?->title,
                    'type' => $assessment?->type,
                    'attempt_number' => $answers->first()->attempt_number,
                    'percentage' => $percentage,
                    'passed' => $percentage >= (int) ($assessment?->pass_percentage ?? 70),
                    'submitted_at' => optional($answers->first()->submitted_at)->toIso8601String(),
                    'missed_questions' => $answers
                        ->where('is_correct', false)
                        ->take(5)
                        ->map(fn (StudentAnswer $answer) => [
                            'question' => Str::limit(strip_tags((string) $answer->question?->question_text), 160),
                            'explanation' => Str::limit(strip_tags((string) $answer->question?->explanation), 220),
                            'tags' => $answer->question?->tags ?? [],
                            'difficulty' => $answer->question?->difficulty_level,
                        ])
                        ->values()
                        ->all(),
                ];
            })
            ->values()
            ->all();
    }

    private function diagnoseSkills(array $signals, ?string $goal = null): array
    {
        $scores = collect(self::SKILLS)->mapWithKeys(fn ($skill, $key) => [$key => [
            'key' => $key,
            'label' => $skill['label'],
            'score' => 12,
            'level' => 'Starting',
            'evidence' => [],
            'gaps' => [],
            'next_action' => null,
        ]])->all();

        foreach ($signals['enrollments'] as $enrollment) {
            $text = $this->courseText($enrollment);
            $progress = (int) ($enrollment['progress'] ?? 0);
            $isCompleted = (bool) ($enrollment['is_completed'] ?? false);

            foreach ($this->matchingSkills($text) as $key) {
                $scores[$key]['score'] += min(36, max(8, $progress * 0.35));
                $scores[$key]['evidence'][] = "{$enrollment['title']} ({$progress}% complete)";

                if ($isCompleted) {
                    $scores[$key]['score'] += 12;
                }
            }
        }

        foreach ($signals['assessment_attempts'] as $attempt) {
            $text = trim(($attempt['assessment_title'] ?? '') . ' ' . collect($attempt['missed_questions'] ?? [])->pluck('question')->implode(' '));
            $matched = $this->matchingSkills($text);

            foreach ($matched as $key) {
                $scores[$key]['score'] += ((float) ($attempt['percentage'] ?? 0)) * 0.18;

                if (! ($attempt['passed'] ?? false)) {
                    $scores[$key]['gaps'][] = "Needs review from {$attempt['assessment_title']} ({$attempt['percentage']}%).";
                }
            }
        }

        foreach ($signals['code_reviews'] as $review) {
            foreach (($review['rubric_scores'] ?? []) as $rubric => $score) {
                $key = match ($rubric) {
                    'testing' => 'testing',
                    'security' => 'security',
                    'correctness', 'maintainability' => 'backend',
                    default => 'project_delivery',
                };

                $scores[$key]['score'] += ((int) $score) * 4;
                $scores[$key]['evidence'][] = "Code review: {$review['title']}";
            }

            if (($review['approval_status'] ?? null) === CodeReview::APPROVAL_APPROVED) {
                $scores['project_delivery']['score'] += 18;
            }

            foreach (($review['improvement_areas'] ?? []) as $area) {
                $matched = $this->matchingSkills($area);

                foreach ($matched ?: ['project_delivery'] as $key) {
                    $scores[$key]['gaps'][] = "Code review improvement: {$area}.";
                }
            }
        }

        if (count($signals['certificates'] ?? []) > 0) {
            $scores['career_readiness']['score'] += 18;
            $scores['project_delivery']['score'] += 10;
            $scores['career_readiness']['evidence'][] = count($signals['certificates']) . ' certificate signal(s).';
        }

        if (($signals['portfolio_count'] ?? 0) > 0) {
            $scores['career_readiness']['score'] += 20;
            $scores['project_delivery']['score'] += 15;
            $scores['career_readiness']['evidence'][] = 'Portfolio project available.';
        }

        foreach ($scores as $key => $skill) {
            $score = max(0, min(100, (int) round($skill['score'])));
            $scores[$key]['score'] = $score;
            $scores[$key]['level'] = $this->skillLevel($score);
            $scores[$key]['evidence'] = array_values(array_unique(array_slice($skill['evidence'], 0, 4)));
            $scores[$key]['gaps'] = array_values(array_unique(array_slice($skill['gaps'], 0, 4)));
            $scores[$key]['next_action'] = $this->skillAction($key, $score, $goal);
        }

        return collect($scores)
            ->sortBy('score')
            ->values()
            ->all();
    }

    private function assessmentFeedback(array $signals): array
    {
        return collect($signals['assessment_attempts'] ?? [])
            ->sortBy('percentage')
            ->take(6)
            ->map(function (array $attempt) {
                $missed = collect($attempt['missed_questions'] ?? []);

                return [
                    'assessment_title' => $attempt['assessment_title'],
                    'course_title' => $attempt['course_title'],
                    'percentage' => $attempt['percentage'],
                    'passed' => $attempt['passed'],
                    'summary' => $attempt['passed']
                        ? 'Passed. Strengthen recall by revisiting missed explanations before moving too far ahead.'
                        : 'Not yet secure. Revisit the lesson notes, then retake after correcting the missed concepts.',
                    'focus_points' => $missed->map(function ($missedQuestion) {
                        return [
                            'topic' => $missedQuestion['question'] ?: 'Missed concept',
                            'hint' => $missedQuestion['explanation'] ?: 'Review the lesson section connected to this question and explain the idea in your own words.',
                            'difficulty' => $missedQuestion['difficulty'] ?: 'medium',
                        ];
                    })->values()->all(),
                ];
            })
            ->values()
            ->all();
    }

    private function courseRecommendations(User $user, array $diagnosis, array $signals): array
    {
        $enrolledIds = collect($signals['enrollments'])->pluck('course_id')->filter()->all();
        $weakSkills = collect($diagnosis)->where('score', '<', 65)->pluck('key')->values();

        return Course::query()
            ->with(['category', 'instructor'])
            ->where('is_published', true)
            ->where('is_approved', true)
            ->whereNotIn('id', $enrolledIds)
            ->latest('updated_at')
            ->limit(40)
            ->get()
            ->map(function (Course $course) use ($weakSkills) {
                $text = $this->courseText([
                    'title' => $course->title,
                    'description' => $course->description,
                    'tags' => $course->tags ?? [],
                    'learning_outcomes' => $course->learning_outcomes ?? [],
                    'difficulty' => $course->difficulty_level,
                ]);
                $matched = collect($this->matchingSkills($text));
                $gapMatches = $weakSkills->intersect($matched);
                $score = 35 + ($gapMatches->count() * 18) + ($matched->count() * 4);

                if ($course->difficulty_level === 'beginner') {
                    $score += 6;
                }

                return [
                    'course_id' => $course->id,
                    'title' => $course->title,
                    'difficulty' => $course->difficulty_level,
                    'category' => $course->category?->name,
                    'instructor' => $course->instructor?->name,
                    'score' => min(100, $score),
                    'reason' => $gapMatches->isNotEmpty()
                        ? 'Targets ' . $gapMatches->map(fn ($key) => self::SKILLS[$key]['label'])->implode(', ')
                        : 'Broadens your current learning path.',
                    'url' => $this->routeOr('#', 'courses.preview', ['course' => $course->id]),
                ];
            })
            ->sortByDesc('score')
            ->take(6)
            ->values()
            ->all();
    }

    private function adaptivePath(User $user, array $signals, array $diagnosis, array $feedback, array $recommendations, ?string $goal): array
    {
        $steps = [];
        $current = collect($signals['enrollments'])
            ->where('is_completed', false)
            ->sortByDesc('progress')
            ->first();

        if ($current) {
            $steps[] = [
                'label' => 'Continue current course',
                'title' => $current['title'],
                'detail' => "You are {$current['progress']}% through this course. Finish the next lesson before adding another major goal.",
                'priority' => 'high',
                'url' => $this->routeOr('#', 'course.view', ['course' => $current['slug'], 'continue' => 1]),
            ];
        } elseif (! empty($recommendations)) {
            $first = $recommendations[0];
            $steps[] = [
                'label' => 'Start a track',
                'title' => $first['title'],
                'detail' => $first['reason'],
                'priority' => 'high',
                'url' => $first['url'],
            ];
        }

        if (! empty($feedback)) {
            $weak = $feedback[0];
            $steps[] = [
                'label' => 'Repair assessment gap',
                'title' => $weak['assessment_title'] ?? 'Assessment review',
                'detail' => "{$weak['percentage']}% score. {$weak['summary']}",
                'priority' => ($weak['passed'] ?? false) ? 'medium' : 'high',
                'url' => $this->routeOr('#', 'student.enrolled-courses'),
            ];
        }

        $weakSkill = collect($diagnosis)->sortBy('score')->first();
        if ($weakSkill) {
            $steps[] = [
                'label' => 'Skill focus',
                'title' => $weakSkill['label'],
                'detail' => $weakSkill['next_action'],
                'priority' => $weakSkill['score'] < 50 ? 'high' : 'medium',
                'url' => $this->routeOr('#', 'ai.learning.coach'),
            ];
        }

        $approvedReviews = collect($signals['code_reviews'])->where('approval_status', CodeReview::APPROVAL_APPROVED)->count();
        if ($approvedReviews === 0) {
            $steps[] = [
                'label' => 'Submit work for review',
                'title' => 'Get project/code evidence',
                'detail' => 'Submit a repository or snippet so a mentor can score it and attach evidence to your career profile.',
                'priority' => 'medium',
                'url' => $this->routeOr('#', 'mentorship.code-reviews'),
            ];
        }

        if (count($signals['certificates']) === 0) {
            $steps[] = [
                'label' => 'Certificate milestone',
                'title' => 'Complete one certificate-ready course',
                'detail' => 'A certificate gives the recommendation engine stronger evidence for your next path.',
                'priority' => 'medium',
                'url' => $this->routeOr('#', 'student.enrolled-courses'),
            ];
        }

        if (($signals['portfolio_count'] ?? 0) === 0) {
            $steps[] = [
                'label' => 'Career evidence',
                'title' => 'Publish one portfolio project',
                'detail' => 'Turn your strongest reviewed work into a portfolio item for career support.',
                'priority' => 'low',
                'url' => $this->routeOr('#', 'portfolio.show'),
            ];
        }

        if ($goal) {
            array_unshift($steps, [
                'label' => 'Chosen goal',
                'title' => Str::limit($goal, 80),
                'detail' => 'The plan is ordered around this learner goal.',
                'priority' => 'high',
                'url' => $this->routeOr('#', 'ai.learning.coach'),
            ]);
        }

        return array_values(array_slice($steps, 0, 8));
    }

    private function contextSnippets(User $user, string $question, ?int $courseId = null): array
    {
        if (! Schema::hasTable('lessons')) {
            return [];
        }

        $terms = collect(preg_split('/\s+/', Str::lower($question)))
            ->map(fn ($term) => trim($term, " \t\n\r\0\x0B.,?!;:'\"()[]{}"))
            ->filter(fn ($term) => strlen($term) >= 4)
            ->unique()
            ->take(8)
            ->values();

        $enrolledCourseIds = CourseEnrollment::where('user_id', $user->id)->pluck('course_id');

        $query = DB::table('lessons')
            ->join('sections', 'sections.id', '=', 'lessons.section_id')
            ->join('courses', 'courses.id', '=', 'sections.course_id')
            ->select([
                'lessons.id',
                'lessons.title',
                'lessons.content',
                'lessons.text_content',
                'lessons.description',
                'courses.id as course_id',
                'courses.title as course_title',
                'courses.slug as course_slug',
                'sections.title as section_title',
            ])
            ->where('courses.is_published', true)
            ->where('courses.is_approved', true);

        if ($courseId) {
            $query->where('courses.id', $courseId);
        } elseif (! $user->isSuperAdmin() && ! $user->isAcademyAdmin() && ! $user->isInstructor()) {
            $query->whereIn('courses.id', $enrolledCourseIds);
        }

        if ($terms->isNotEmpty()) {
            $query->where(function ($builder) use ($terms) {
                foreach ($terms as $term) {
                    $like = '%' . addcslashes($term, '\\%_') . '%';
                    $builder->orWhere('lessons.title', 'like', $like)
                        ->orWhere('lessons.description', 'like', $like)
                        ->orWhere('lessons.content', 'like', $like)
                        ->orWhere('lessons.text_content', 'like', $like)
                        ->orWhere('sections.title', 'like', $like)
                        ->orWhere('courses.title', 'like', $like);
                }
            });
        }

        return $query->limit(5)
            ->get()
            ->map(fn ($row) => [
                'course_id' => $row->course_id,
                'course_title' => $row->course_title,
                'section_title' => $row->section_title,
                'lesson_title' => $row->title,
                'excerpt' => Str::limit(trim(preg_replace('/\s+/', ' ', strip_tags((string) ($row->content ?: $row->text_content ?: $row->description)))), 550),
                'url' => $this->routeOr('#', 'course.view', ['course' => $row->course_slug, 'lesson' => $row->id]),
            ])
            ->values()
            ->all();
    }

    private function localTutorAnswer(User $user, string $question, array $context): string
    {
        if (empty($context)) {
            return "I could not find a matching lesson in your available course content yet. Rephrase the question with the course topic, or enroll in a related course so I can answer from BootKode lesson material.";
        }

        $primary = $context[0];
        $answer = "Based on {$primary['course_title']} / {$primary['lesson_title']}, focus on this idea first: {$primary['excerpt']}";

        if (count($context) > 1) {
            $answer .= "\n\nRelated places to check: " . collect($context)->skip(1)->take(3)
                ->map(fn ($item) => "{$item['course_title']} - {$item['lesson_title']}")
                ->implode('; ') . '.';
        }

        $answer .= "\n\nPractical next step: write a tiny example, run it, then compare it against the lesson explanation. If it still fails, ask again with the exact error or code snippet.";

        return $answer;
    }

    private function openAiTutorAnswer(string $question, array $context): ?string
    {
        $baseUrl = rtrim((string) config('services.openai.base_url', 'https://api.openai.com'), '/');
        $model = (string) config('services.openai.model', 'gpt-4o-mini');
        $contextText = collect($context)
            ->map(fn ($item, $index) => ($index + 1) . ". {$item['course_title']} / {$item['lesson_title']}: {$item['excerpt']}")
            ->implode("\n");

        $response = Http::withToken(config('services.openai.key'))
            ->timeout((int) config('services.openai.timeout', 15))
            ->post($baseUrl . '/v1/chat/completions', [
                'model' => $model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are BootKode AI Tutor. Answer from the provided course context. Be practical, concise, and do not invent unavailable course material.',
                    ],
                    [
                        'role' => 'user',
                        'content' => "Course context:\n{$contextText}\n\nLearner question: {$question}",
                    ],
                ],
                'temperature' => 0.35,
                'max_tokens' => 650,
            ]);

        if (! $response->successful()) {
            return null;
        }

        return trim((string) data_get($response->json(), 'choices.0.message.content')) ?: null;
    }

    private function matchingSkills(string $text): array
    {
        $text = Str::lower($text);
        $matches = [];

        foreach (self::SKILLS as $key => $skill) {
            foreach ($skill['terms'] as $term) {
                if (Str::contains($text, Str::lower($term))) {
                    $matches[] = $key;
                    break;
                }
            }
        }

        return array_values(array_unique($matches));
    }

    private function courseText(array $course): string
    {
        return trim(implode(' ', [
            $course['title'] ?? '',
            $course['description'] ?? '',
            $course['difficulty'] ?? '',
            $this->textValue($course['tags'] ?? []),
            $this->textValue($course['learning_outcomes'] ?? []),
        ]));
    }

    private function textValue(mixed $value): string
    {
        if ($value instanceof Collection) {
            $value = $value->all();
        }

        if (is_array($value)) {
            return collect($value)
                ->flatten()
                ->map(fn ($item) => is_scalar($item) ? (string) $item : '')
                ->filter()
                ->implode(' ');
        }

        return is_scalar($value) ? (string) $value : '';
    }

    private function skillLevel(int $score): string
    {
        return match (true) {
            $score >= 85 => 'Strong',
            $score >= 70 => 'Ready',
            $score >= 50 => 'Developing',
            $score >= 30 => 'Needs focus',
            default => 'Starting',
        };
    }

    private function skillAction(string $key, int $score, ?string $goal): string
    {
        if ($score >= 75) {
            return 'Use this strength in a reviewed project or mentor session.';
        }

        return match ($key) {
            'testing' => 'Add test practice to the next lesson or project before requesting approval.',
            'security' => 'Review validation, authorization, and data-handling examples before shipping work.',
            'project_delivery' => 'Submit a repository or snippet for rubric review so your progress has evidence.',
            'career_readiness' => 'Turn completed work into a portfolio item, resume evidence, or certificate milestone.',
            default => $goal
                ? "Choose one lesson that directly supports \"{$goal}\" and complete it before adding another course."
                : 'Complete the next unfinished lesson and retake weak assessments after reviewing explanations.',
        };
    }

    private function routeOr(string $fallback, string $name, array $params = []): string
    {
        try {
            if (Route::has($name)) {
                return route($name, $params);
            }
        } catch (Throwable) {
            return $fallback;
        }

        return $fallback;
    }
}
