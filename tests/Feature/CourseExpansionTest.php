<?php

namespace Tests\Feature;

use App\Models\Assessment\Assessment;
use App\Models\Core\User;
use App\Models\Learning\Course;
use App\Models\Learning\CourseCategory;
use App\Models\Learning\CourseContentRun;
use App\Models\Learning\Lesson;
use App\Models\Learning\Section;
use App\Services\CourseGeneration\AiCourseExpansionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CourseExpansionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::findOrCreate('super_admin', 'web');
        Role::findOrCreate('instructor', 'web');
        config([
            'bootkode_ai.enabled' => true,
            'bootkode_ai.provider' => 'groq',
            'bootkode_ai.course_generation_daily_requests' => 12,
            'bootkode_ai.providers.groq' => [
                'key' => 'test-key',
                'model' => 'test-model',
                'base_url' => 'https://api.groq.test/v1',
            ],
        ]);
    }

    public function test_imported_draft_expansion_preserves_lessons_attribution_and_adds_section_quizzes(): void
    {
        [$editor, $course, $lessons] = $this->fixture(5);

        Http::fake(function (Request $request) {
            $input = json_decode($request['messages'][1]['content'], true);
            $titles = array_column($input['lessons'], 'title');
            $content = json_encode([
                'title' => 'Section: '.$titles[0],
                'description' => 'A structured section that builds practical understanding and prepares learners to apply these concepts.',
                'learning_outcomes' => [
                    'Explain the core ideas using relevant JavaScript examples.',
                    'Apply the techniques and check their output against expected behavior.',
                ],
                'lessons' => array_map(fn ($title) => [
                    'title' => $title,
                    'description' => 'A detailed explanation with a worked example and an exercise.',
                    'content' => str_repeat("## Concept\n\nWork through this JavaScript example step by step. Identify the input, trace the state, check the output, and then complete the practice exercise. Review the common mistake and summarize the key takeaway.\n\n", 9),
                ], $titles),
                'assessment' => [
                    'title' => 'Section knowledge check',
                    'description' => 'Check understanding of the section concepts and examples.',
                    'questions' => array_map(fn ($index) => [
                        'question_text' => 'Which approach is most appropriate for this section concept '.$index.'?',
                        'options' => ['Trace the input and output', 'Skip validation', 'Assume every input is identical', 'Ignore the returned value'],
                        'correct_answers' => [0],
                        'explanation' => 'Tracing the input and output makes the behavior explicit and lets you verify the result.',
                    ], range(1, 4)),
                ],
            ]);

            return Http::response([
                'choices' => [['message' => ['content' => "Here is the JSON response:\n```json\n".$content."\n```\nDone."]]],
                'usage' => ['prompt_tokens' => 80, 'completion_tokens' => 900],
            ]);
        });

        $result = app(AiCourseExpansionService::class)->expand($editor, $course->id);
        $expanded = $course->fresh(['sections.lessons', 'sections.assessments.questions']);

        $this->assertSame('completed', $result['run']->status);
        $this->assertSame(2, $result['summary']['sections']);
        $this->assertSame(2, $expanded->sections->count());
        $this->assertSame(5, $expanded->sections->sum(fn ($section) => $section->lessons->count()));
        $this->assertSame(8, $expanded->sections->sum(fn ($section) => $section->assessments->first()->questions->count()));
        $expandedLessons = $expanded->allLessons()->get();
        $this->assertSame($lessons->pluck('id')->sort()->values()->all(), $expandedLessons->pluck('id')->sort()->values()->all());
        $this->assertStringContainsString('Original freeCodeCamp challenge', $expandedLessons->first()->content);
        $this->assertStringContainsString('Copyright (c) 2014, freeCodeCamp.', $expandedLessons->last()->content);
        $this->assertGreaterThan(1200, strlen(strip_tags($expandedLessons->first()->content)));
        $this->assertSame(['Explain the core ideas using relevant JavaScript examples.', 'Apply the techniques and check their output against expected behavior.'], $expanded->learning_outcomes);
        $this->assertFalse($expanded->is_published);
        $this->assertFalse($expanded->is_approved);
        $this->assertSame(2, Assessment::query()->where('course_id', $course->id)->count());
        $this->assertSame(2, \App\Models\Ai\AiUsageRecord::query()->where('feature', 'course_generation')->count());

        $again = app(AiCourseExpansionService::class)->expand($editor, $course->id);
        $this->assertFalse($again['created']);
        $this->assertSame(2, Assessment::query()->where('course_id', $course->id)->count());
    }

    public function test_expansion_refuses_courses_with_enrollment_history_without_mutation(): void
    {
        [$editor, $course] = $this->fixture(4);
        $learner = User::factory()->create();
        $learner->courses()->attach($course->id, ['progress' => 0]);
        Http::fake();

        try {
            app(AiCourseExpansionService::class)->expand($editor, $course->id);
            $this->fail('A course with learner history was expanded.');
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $exception) {
            $this->assertSame(422, $exception->getStatusCode());
        }

        $this->assertSame(1, $course->fresh()->sections()->count());
        $this->assertSame(0, Assessment::query()->where('course_id', $course->id)->count());
        $this->assertSame(0, CourseContentRun::query()->where('source_type', 'ai_expansion')->count());
        Http::assertNothingSent();
    }

    public function test_cli_replay_prints_the_existing_summary_without_duplicate_assessments(): void
    {
        [$editor, $course] = $this->fixture(4);
        $this->fakeExpansion();
        $this->artisan('bootkode:courses-expand', ['--course-ids' => $course->id, '--editor-id' => $editor->id])->expectsOutputToContain('Expanded draft:')->assertExitCode(0);
        $this->artisan('bootkode:courses-expand', ['--course-ids' => $course->id, '--editor-id' => $editor->id])->expectsOutputToContain('Sections: 1')->assertExitCode(0);
        $this->assertSame(1, Assessment::where('course_id', $course->id)->count());
    }

    public function test_failed_section_retry_reuses_saved_successful_sections(): void
    {
        [$editor, $course] = $this->fixture(5);
        $service = app(AiCourseExpansionService::class);
        $run = $service->queue($editor, $course->id);
        $this->fakeExpansion(function ($request) {
            $data = json_decode($request['messages'][1]['content'], true);
            if (count($data['lessons']) === 1) {
                return Http::response(['error' => 'not leaked'], 429);
            }

            return null;
        });
        try {
            $service->process($run->id);
            $this->fail('The failed section was accepted.');
        } catch (\App\Exceptions\AiProviderException) {
            $this->assertSame('failed', $run->fresh()->status);
            $this->assertCount(1, $run->fresh()->stage_data['generated']);
            $this->assertSame(1, $course->fresh()->sections()->count());
            $this->assertSame(0, Assessment::count());
        }
        $this->fakeExpansion();
        $done = $service->process($run->id);
        $this->assertSame('completed', $done['run']->status);
        $this->assertSame(3, \App\Models\Ai\AiUsageRecord::where('feature', 'course_generation')->count());
        $this->assertFalse($service->process($run->id)['created']);
    }

    public function test_edits_during_generation_are_preserved_and_reported_as_a_conflict(): void
    {
        [$editor, $course, $lessons] = $this->fixture(4);
        $this->fakeExpansion(function () use ($lessons) {
            $lessons->first()->update(['content' => '<p>Human change made while the provider was responding.</p>']);

            return null;
        });
        try {
            app(AiCourseExpansionService::class)->expand($editor, $course->id);
            $this->fail('Concurrent edits were overwritten.');
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $exception) {
            $this->assertSame(409, $exception->getStatusCode());
            $this->assertStringContainsString('Human change', $lessons->first()->fresh()->content);
            $this->assertSame(0, Assessment::count());
            $this->assertDatabaseHas('course_content_runs', ['source_type' => 'ai_expansion', 'status' => 'conflict']);
        }
    }

    public function test_legacy_saved_snapshot_cannot_overwrite_an_edited_lesson(): void
    {
        [$editor, $course, $lessons] = $this->fixture(4);
        $service = app(AiCourseExpansionService::class);
        $run = $service->queue($editor, $course->id);
        $stages = $run->stage_data;
        unset($stages['fingerprint']);
        $run->update(['stage_data' => $stages, 'status' => 'failed']);
        $lessons->first()->update(['content' => '<p>Later human work.</p>']);
        Http::fake();
        try {
            $service->process($run->id);
            $this->fail('Legacy snapshot silently replaced an edit.');
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $exception) {
            $this->assertSame(409, $exception->getStatusCode());
            Http::assertNothingSent();
            $this->assertStringContainsString('Later human work', $lessons->first()->fresh()->content);
        }
    }

    public function test_expansion_rechecks_ownership_and_processing_lock(): void
    {
        [$editor, $course] = $this->fixture(4);
        $service = app(AiCourseExpansionService::class);
        $run = $service->queue($editor, $course->id);
        $lock = \Illuminate\Support\Facades\Cache::lock('course-expansion:'.$course->id, 900);
        $this->assertTrue($lock->get());
        Http::fake();
        try {
            $service->process($run->id);
            $this->fail('Active processing lock was ignored.');
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $exception) {
            $this->assertSame(409, $exception->getStatusCode());
            Http::assertNothingSent();
        } finally {
            $lock->release();
        }
        $editor->syncRoles(['instructor']);
        $course->update(['instructor_id' => User::factory()->create(['role' => 'instructor'])->id]);
        try {
            $service->process($run->id);
            $this->fail('Ownership change was ignored.');
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $exception) {
            $this->assertSame(403, $exception->getStatusCode());
            Http::assertNothingSent();
        }
    }

    public function test_studio_shows_category_quality_details_and_retry_for_failed_expansion(): void
    {
        [$editor, $course] = $this->fixture(4);
        $run = app(AiCourseExpansionService::class)->queue($editor, $course->id);
        $run->update(['status' => 'failed', 'failure_reason' => 'The provider could not be reached.']);
        \Illuminate\Support\Facades\Queue::fake();
        \Livewire\Livewire::actingAs($editor)->test(\App\Livewire\CourseManagement\CourseContentStudio::class)
            ->assertSee('Category: Algorithms')->assertSee('AI draft expansion')->assertSee('Retry saved stages')
            ->call('reviewRun', $run->id)->assertSee('Course thumbnail is missing.')
            ->call('retryRun', $run->id)->assertHasNoErrors();
        \Illuminate\Support\Facades\Queue::assertPushed(\App\Jobs\ExpandImportedCourse::class, fn ($job) => $job->runId === $run->id);
        $this->assertFalse($course->fresh()->is_approved);
        $this->assertFalse($course->fresh()->is_published);
    }

    public function test_studio_does_not_allow_another_instructor_to_review_or_expand_a_course(): void
    {
        [$editor, $course] = $this->fixture(4);
        $other = User::factory()->create(['role' => 'instructor']);
        $run = app(AiCourseExpansionService::class)->queue($editor, $course->id);
        \Livewire\Livewire::actingAs($other)->test(\App\Livewire\CourseManagement\CourseContentStudio::class)->call('reviewRun', $run->id)->assertForbidden();
        \Livewire\Livewire::actingAs($other)->test(\App\Livewire\CourseManagement\CourseContentStudio::class)->call('startExpansion', $course->id)->assertForbidden();
    }

    public function test_attribution_repair_is_additive_and_idempotent_for_legacy_drafts(): void
    {
        [$editor, $course, $lessons] = $this->fixture(4);
        $original = $lessons->keyBy('id')->map->content;
        $service = app(AiCourseExpansionService::class);
        $this->assertSame(4, $service->restoreAttribution($editor, $course->id));
        foreach ($lessons as $lesson) {
            $this->assertStringStartsWith($original[$lesson->id], $lesson->fresh()->content);
            $this->assertTrue(\App\Services\CourseImport\FreeCodeCampSource::includesLicense($lesson->fresh()->content, file_get_contents(base_path('tests/Fixtures/course-content/freecodecamp-LICENSE.md'))));
        }
        $this->assertSame(0, $service->restoreAttribution($editor, $course->id));
    }

    private function fakeExpansion(?callable $before = null): void
    {
        Http::swap(new \Illuminate\Http\Client\Factory);
        Http::preventStrayRequests();
        Http::fake(function (Request $request) use ($before) {
            if ($before && ($replacement = $before($request))) {
                return $replacement;
            }
            $data = json_decode($request['messages'][1]['content'], true);

            return Http::response(['choices' => [['message' => ['content' => json_encode([
                'title' => 'Applying the algorithm concepts', 'description' => 'Work through the algorithm steps and explain how the inputs lead to each result.',
                'learning_outcomes' => ['Explain the algorithm with a concrete trace.', 'Check the algorithm against expected results.'],
                'lessons' => array_map(fn ($lesson) => ['title' => $lesson['title'], 'description' => 'A worked explanation followed by a practical learner exercise.',
                    'content' => str_repeat("## Explain and apply\nTrace each input, record the state after a step, compare the result with the expected output, and explain why the invariant holds. Then complete a new example and justify your decision.\n\n", 8)], $data['lessons']),
                'assessment' => ['title' => 'Trace and check quiz', 'description' => 'Apply the section concepts to a concrete trace.', 'questions' => array_map(fn ($n) => [
                    'question_text' => 'How should the learner verify the result for example '.$n.'?', 'options' => ['Trace the steps', 'Assume it works', 'Skip the input', 'Ignore the output'], 'correct_answers' => [0],
                    'explanation' => 'Tracing the state and comparing the expected result provides specific evidence.'], range(1, 4))],
            ])]]], 'usage' => ['prompt_tokens' => 30, 'completion_tokens' => 500]]);
        });
    }

    private function fixture(int $lessonCount): array
    {
        $editor = User::factory()->create(['role' => 'super_admin']);
        $category = CourseCategory::query()->create(['name' => 'Algorithms', 'description' => 'Programming courses']);
        $course = Course::withoutEvents(fn () => Course::create([
            'instructor_id' => $editor->id,
            'category_id' => $category->id,
            'title' => 'Algorithms — freeCodeCamp',
            'slug' => 'algorithms-'.Str::uuid(),
            'description' => 'An imported course draft that needs lesson explanations and assessments.',
            'is_published' => false,
            'is_approved' => false,
        ]));
        $section = Section::create(['course_id' => $course->id, 'title' => 'Imported curriculum challenges', 'order' => 1]);
        $lessons = collect();

        for ($index = 1; $index <= $lessonCount; $index++) {
            $lessons->push(Lesson::withoutEvents(fn () => Lesson::create([
                'section_id' => $section->id,
                'title' => 'Lesson '.$index,
                'slug' => 'algorithms-lesson-'.$index.'-'.Str::uuid(),
                'content' => '<p>Original challenge excerpt and source link.</p>'.($index === $lessonCount ? '<pre>Copyright (c) 2014, freeCodeCamp.</pre>' : ''),
                'text_content' => 'Original challenge excerpt.',
                'duration_minutes' => 1,
                'order' => $index,
            ])));
        }

        CourseContentRun::query()->create([
            'source_key' => hash('sha256', 'freecodecamp:curriculum/challenges/english/blocks/algorithms:'.$course->id),
            'revision' => 'a'.str_repeat('a', 39),
            'source_type' => 'repository',
            'user_id' => $editor->id,
            'course_id' => $course->id,
            'status' => 'completed',
            'metadata' => ['source' => 'freecodecamp', 'path' => 'curriculum/challenges/english/blocks/algorithms', 'license_id' => 'BSD-3-Clause', 'license_text' => file_get_contents(base_path('tests/Fixtures/course-content/freecodecamp-LICENSE.md'))],
        ]);

        return [$editor, $course, $lessons];
    }
}
