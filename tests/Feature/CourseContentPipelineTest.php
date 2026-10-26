<?php

namespace Tests\Feature;

use App\Models\Assessment\Assessment;
use App\Models\Core\User;
use App\Models\Learning\Course;
use App\Models\Learning\CourseCategory;
use App\Services\CourseGeneration\AiCourseGenerationService;
use App\Services\CourseImport\CourseContentSourceRegistry;
use App\Services\CourseImport\CourseImportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CourseContentPipelineTest extends TestCase
{
    use RefreshDatabase;

    private const REVISION = 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa';

    protected function setUp(): void
    {
        parent::setUp();
        $this->partialMock(\App\Services\CourseImport\GitHubContentClient::class, fn ($mock) => $mock->shouldReceive('addresses')->andReturn(['140.82.112.6']));
        Role::findOrCreate('instructor', 'web');
        Role::findOrCreate('academy_admin', 'web');
    }

    public function test_source_is_registered_and_rejects_unapproved_paths_without_network_requests(): void
    {
        $registry = app(CourseContentSourceRegistry::class);
        $this->assertSame('freecodecamp', $registry->get('freecodecamp')->key());
        $this->assertSame(['freecodecamp'], array_keys($registry->all()));

        Http::fake();
        try {
            $registry->get('freecodecamp')->inspect('https://169.254.169.254/latest/meta-data');
            $this->fail('An arbitrary URL was accepted as a repository path.');
        } catch (\RuntimeException $exception) {
            $this->assertStringContainsString('single freeCodeCamp curriculum block', $exception->getMessage());
        }
        Http::assertNothingSent();
    }

    public function test_freecodecamp_preview_requires_the_verified_bsd_license_and_excludes_solution_sections(): void
    {
        $this->fakeFreeCodeCampSource();
        $preview = app(CourseImportService::class)->preview('freecodecamp', 'curriculum/challenges/english/blocks/algorithms', 2);

        $this->assertSame('BSD-3-Clause', $preview['license_id']);
        $this->assertSame(self::REVISION, $preview['revision']);
        $this->assertCount(2, $preview['lessons']);
        $this->assertStringContainsString('selection sort', strtolower($preview['lessons'][0]['content']));
        $this->assertStringNotContainsString('secret solution', strtolower($preview['lessons'][0]['content']));
        $this->assertStringContainsString('Copyright (c) 2014, freeCodeCamp.', $preview['license_text']);
    }

    public function test_preview_limit_does_not_count_unselected_files_as_skipped(): void
    {
        $this->fakeFreeCodeCampSource();
        $preview = app(CourseImportService::class)->preview('freecodecamp', 'curriculum/challenges/english/blocks/algorithms', 1);

        $this->assertSame(2, $preview['files_available']);
        $this->assertSame(1, $preview['files_selected']);
        $this->assertSame(0, $preview['skipped_files']);
    }

    public function test_changed_source_revision_requires_explicit_new_draft(): void
    {
        $editor = User::factory()->create(['role' => 'instructor']);
        $category = CourseCategory::query()->create(['name' => 'Algorithms', 'description' => 'Learning systems']);
        $service = app(CourseImportService::class);

        $this->fakeFreeCodeCampSource('', self::REVISION);
        $first = $service->import($editor, 'freecodecamp', 'curriculum/challenges/english/blocks/algorithms', $category->id, 1);

        $changedRevision = str_repeat('b', 40);
        $this->fakeFreeCodeCampSource('', $changedRevision);
        $conflict = $service->import($editor, 'freecodecamp', 'curriculum/challenges/english/blocks/algorithms', $category->id, 1);

        $this->assertTrue($first['created']);
        $this->assertFalse($conflict['created']);
        $this->assertSame('conflict', $conflict['run']->status);
        $this->assertSame(1, Course::query()->count());

        $this->fakeFreeCodeCampSource('', $changedRevision);
        $newDraft = $service->import($editor, 'freecodecamp', 'curriculum/challenges/english/blocks/algorithms', $category->id, 1, true);

        $this->assertTrue($newDraft['created']);
        $this->assertSame(2, Course::query()->count());
        $this->assertFalse($newDraft['run']->course->is_published);
        $this->assertFalse($newDraft['run']->course->is_approved);
    }

    public function test_import_creates_a_draft_with_source_metadata_and_rerun_does_not_duplicate_course_content(): void
    {
        $this->fakeFreeCodeCampSource();
        $editor = User::factory()->create(['role' => 'instructor']);
        $category = CourseCategory::query()->create(['name' => 'Algorithms', 'description' => 'Learning systems']);

        $service = app(CourseImportService::class);
        $first = $service->import($editor, 'freecodecamp', 'curriculum/challenges/english/blocks/algorithms', $category->id, 2);
        $second = $service->import($editor, 'freecodecamp', 'curriculum/challenges/english/blocks/algorithms', $category->id, 2);

        $this->assertTrue($first['created']);
        $this->assertFalse($second['created']);
        $this->assertSame('completed', $first['run']->status);
        $this->assertSame('skipped', $second['run']->status);
        $this->assertSame($first['run']->course_id, $second['run']->course_id);
        $this->assertSame(1, Course::query()->count());
        $this->assertDatabaseHas('course_content_runs', [
            'id' => $first['run']->id,
            'source_type' => 'repository',
            'revision' => self::REVISION,
        ]);
        $course = $first['run']->course->fresh(['sections.lessons']);
        $this->assertFalse($course->is_published);
        $this->assertFalse($course->is_approved);
        $this->assertSame('BSD-3-Clause', $first['run']->metadata['license_id']);
        $this->assertSame(2, $course->sections->first()->lessons->count());
        foreach ($course->sections->first()->lessons as $lesson) {
            $this->assertStringContainsString('Copyright (c) 2014, freeCodeCamp.', $lesson->content);
        }
        $this->assertStringNotContainsString('secret solution', strtolower($course->sections->first()->lessons->last()->content));
    }

    public function test_unsupported_license_is_refused_without_creating_course_records(): void
    {
        $this->fakeFreeCodeCampSource('All rights reserved.');
        $editor = User::factory()->create(['role' => 'instructor']);
        $category = CourseCategory::query()->create(['name' => 'Algorithms', 'description' => 'Learning systems']);

        $this->expectException(\RuntimeException::class);
        try {
            app(CourseImportService::class)->import($editor, 'freecodecamp', 'curriculum/challenges/english/blocks/algorithms', $category->id, 1);
        } finally {
            $this->assertDatabaseCount('courses', 0);
            $this->assertDatabaseHas('course_content_runs', ['user_id' => $editor->id, 'status' => 'failed']);
        }
    }

    public function test_course_studio_is_for_course_authors_and_ai_disabled_state_is_explicit(): void
    {
        $learner = User::factory()->create();
        Livewire::actingAs($learner)
            ->test(\App\Livewire\CourseManagement\CourseContentStudio::class)
            ->assertForbidden();

        $instructor = User::factory()->create(['role' => 'instructor']);
        Livewire::actingAs($instructor)
            ->test(\App\Livewire\CourseManagement\CourseContentStudio::class)
            ->set('generationTopic', 'Digital Marketing')
            ->call('startGeneration')
            ->assertHasErrors('generation');

        $this->assertDatabaseCount('course_content_runs', 0);
    }

    public function test_ai_generation_persists_validated_lessons_and_existing_quiz_and_project_assessments_as_a_draft(): void
    {
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
        $editor = User::factory()->create(['role' => 'instructor']);
        $category = CourseCategory::query()->create(['name' => 'Business', 'description' => 'Business learning']);
        Http::fake(function (Request $request) {
            $messages = $request['messages'];
            $userContent = $messages[1]['content'];

            if (str_contains($messages[0]['content'], 'course blueprint')) {
                $content = json_encode([
                    'title' => 'Digital Marketing Fundamentals',
                    'subtitle' => 'A practical introduction',
                    'description' => str_repeat('A structured introduction to practical digital marketing work. ', 3),
                    'learning_outcomes' => ['Plan a campaign', 'Measure a campaign'],
                    'prerequisites' => [],
                    'modules' => [
                        ['title' => 'Module One', 'description' => 'Build the first skill set.', 'lessons' => [
                            ['title' => 'Lesson One A', 'outcome' => 'Explain one core idea.'],
                            ['title' => 'Lesson One B', 'outcome' => 'Apply one core idea.'],
                        ]],
                        ['title' => 'Module Two', 'description' => 'Apply the skill in a project.', 'lessons' => [
                            ['title' => 'Lesson Two A', 'outcome' => 'Compare campaign options.'],
                            ['title' => 'Lesson Two B', 'outcome' => 'Review project evidence.'],
                        ]],
                    ],
                ]);
            } else {
                $moduleTwo = str_contains($userContent, '"title":"Module Two"');
                $titles = $moduleTwo ? ['Lesson Two A', 'Lesson Two B'] : ['Lesson One A', 'Lesson One B'];
                $content = json_encode([
                    'lessons' => array_map(fn ($title) => [
                        'title' => $title,
                        'description' => 'A bounded lesson with a worked explanation and task.',
                        'content' => str_repeat('Explain the campaign decision with an example, then ask the learner to apply the method and record evidence. ', 5),
                    ], $titles),
                    'assessment' => [
                        'title' => $moduleTwo ? 'Module Two Quiz' : 'Module One Quiz',
                        'description' => 'Check the learner can apply the module outcomes.',
                        'questions' => [
                            [
                                'question_text' => 'Which action supports a measurable campaign?',
                                'options' => ['Set a goal', 'Ignore results', 'Avoid a target'],
                                'correct_answers' => [0],
                                'explanation' => 'A stated goal makes later measurement possible.',
                            ],
                            [
                                'question_text' => 'What should follow a campaign test?',
                                'options' => ['Review evidence', 'Delete the record', 'Assume success'],
                                'correct_answers' => [0],
                                'explanation' => 'Evidence supports an informed next step.',
                            ],
                        ],
                    ],
                    'project' => $moduleTwo ? [
                        'title' => 'Campaign measurement project',
                        'description' => 'Prepare a small campaign measurement plan.',
                        'instructions' => 'Choose one campaign goal, define a measurable indicator, and explain how you will review results.',
                        'deliverables' => ['A written campaign goal', 'A metric and review plan'],
                        'evaluation_criteria' => 'Assess alignment between goal and metric, clarity of the review plan, and evidence-based reasoning.',
                        'project_type' => 'marketing_plan',
                        'required_skills' => ['Campaign planning', 'Measurement'],
                        'max_score' => 100,
                    ] : null,
                ]);
            }

            return Http::response([
                'choices' => [['message' => ['content' => $content]]],
                'usage' => ['prompt_tokens' => 20, 'completion_tokens' => 30],
            ]);
        });

        $result = app(AiCourseGenerationService::class)->generate($editor, [
            'topic' => 'Digital Marketing',
            'category_id' => $category->id,
            'target_audience' => 'New small-business owners',
            'skill_level' => 'beginner',
            'learning_objectives' => ['Plan a campaign', 'Measure a campaign'],
            'duration_minutes' => 180,
            'module_count' => 2,
            'lessons_per_module' => 2,
            'teaching_style' => 'Clear explanations and practice',
            'practical_requirements' => 'Create a small campaign measurement plan.',
            'assessment_requirements' => 'Short multiple-choice quiz.',
            'include_assessments' => true,
            'language' => 'English',
            'additional_instructions' => '',
        ], (string) Str::uuid());

        $course = $result['run']->course->fresh(['sections.lessons', 'sections.assessments.questions']);
        $this->assertSame('completed', $result['run']->status);
        $this->assertFalse($course->is_published);
        $this->assertFalse($course->is_approved);
        $this->assertSame(2, $course->sections->count());
        $this->assertSame(4, $course->sections->sum(fn ($section) => $section->lessons->count()));
        $this->assertSame(3, Assessment::query()->count());
        $this->assertSame(1, Assessment::query()->where('type', 'project')->count());
        $this->assertSame('project_criteria', Assessment::where('type', 'project')->firstOrFail()->questions()->firstOrFail()->question_type);
        $this->assertSame(3, $result['summary']['assessment_count']);
        $this->assertSame(3, \App\Models\Ai\AiUsageRecord::query()->where('feature', 'course_generation')->count());
    }

    public function test_ai_generation_resumes_from_persisted_blueprint_and_completed_module(): void
    {
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
        $editor = User::factory()->create(['role' => 'instructor']);
        $category = CourseCategory::query()->create(['name' => 'Business', 'description' => 'Business learning']);
        $input = [
            'topic' => 'Digital Marketing',
            'category_id' => $category->id,
            'target_audience' => 'New small-business owners',
            'skill_level' => 'beginner',
            'learning_objectives' => ['Plan a campaign', 'Measure a campaign'],
            'duration_minutes' => 180,
            'module_count' => 2,
            'lessons_per_module' => 2,
            'teaching_style' => 'Clear explanations and practice',
            'practical_requirements' => '',
            'assessment_requirements' => '',
            'include_assessments' => false,
            'language' => 'English',
            'additional_instructions' => '',
        ];
        $generation = app(AiCourseGenerationService::class);
        $run = $generation->queue($editor, $input, 'resume-module-test');
        $blueprint = [
            'title' => 'Digital Marketing Foundations',
            'subtitle' => 'Plan and measure a first campaign',
            'description' => str_repeat('A structured introduction to practical digital marketing work. ', 3),
            'learning_outcomes' => $input['learning_objectives'],
            'prerequisites' => [],
            'modules' => [
                ['title' => 'Module One', 'description' => 'Build the first skill set.', 'lessons' => [
                    ['title' => 'Lesson One A', 'outcome' => 'Explain one core idea.'],
                    ['title' => 'Lesson One B', 'outcome' => 'Apply one core idea.'],
                ]],
                ['title' => 'Module Two', 'description' => 'Apply the skill in a project.', 'lessons' => [
                    ['title' => 'Lesson Two A', 'outcome' => 'Compare campaign options.'],
                    ['title' => 'Lesson Two B', 'outcome' => 'Review project evidence.'],
                ]],
            ],
        ];
        $savedLessonContent = str_repeat('This completed stage explains the first lesson with a practical example and learner activity. ', 5);
        $run->forceFill([
            'stage_data' => [
                'input' => $input,
                'blueprint' => $blueprint,
                'modules' => [
                    0 => ['lessons' => array_map(fn ($lesson) => [
                        'title' => $lesson['title'],
                        'description' => 'Explain this concept with a clear example and activity.',
                        'content' => $savedLessonContent,
                    ], $blueprint['modules'][0]['lessons'])],
                ],
            ],
        ])->save();

        Http::fake(fn () => Http::response([
            'choices' => [['message' => ['content' => json_encode([
                'lessons' => array_map(fn ($lesson) => [
                    'title' => $lesson['title'],
                    'description' => 'Explain this concept with a clear example and activity.',
                    'content' => str_repeat('This lesson explains the campaign decision with an example and asks the learner to apply the method. ', 5),
                ], $blueprint['modules'][1]['lessons']),
            ])]]],
            'usage' => ['prompt_tokens' => 20, 'completion_tokens' => 30],
        ]));

        $result = $generation->process($run->id);

        $this->assertSame('completed', $result['run']->status);
        $this->assertSame(1, \App\Models\Ai\AiUsageRecord::query()->where('feature', 'course_generation')->count());
        $this->assertSame(4, $result['run']->course->sections->sum(fn ($section) => $section->lessons->count()));
        $this->assertStringContainsString(trim($savedLessonContent), strip_tags(\App\Models\Learning\Lesson::where('title', 'Lesson One A')->firstOrFail()->content));
    }

    public function test_import_cli_lists_configured_sources(): void
    {
        $this->artisan('bootkode:courses-import --list-sources')
            ->expectsOutputToContain('freecodecamp')
            ->assertExitCode(0);
    }

    public function test_unknown_source_and_dry_run_cli_never_create_course_records(): void
    {
        $this->artisan('bootkode:courses-import --source=unknown --path=anything')->assertExitCode(1);
        $this->fakeFreeCodeCampSource();
        $this->artisan('bootkode:courses-import --path=curriculum/challenges/english/blocks/algorithms --limit=1 --dry-run')
            ->expectsOutputToContain('Dry run only')->assertExitCode(0);
        $this->assertDatabaseCount('courses', 0);
        $this->assertDatabaseCount('course_content_runs', 0);
    }

    public function test_import_identity_includes_owner_and_selected_subset(): void
    {
        $this->fakeFreeCodeCampSource();
        $editor = User::factory()->create(['role' => 'instructor']);
        $other = User::factory()->create(['role' => 'instructor']);
        $category = CourseCategory::create(['name' => 'Algorithms']);
        $service = app(CourseImportService::class);
        $one = $service->import($editor, 'freecodecamp', 'curriculum/challenges/english/blocks/algorithms', $category->id, 1);
        $two = $service->import($editor, 'freecodecamp', 'curriculum/challenges/english/blocks/algorithms', $category->id, 2);
        $otherResult = $service->import($other, 'freecodecamp', 'curriculum/challenges/english/blocks/algorithms', $category->id, 1);
        $this->assertTrue($one['created'] && $two['created'] && $otherResult['created']);
        $this->assertSame($other->id, $otherResult['run']->course->instructor_id);
        $this->assertSame(2, $two['run']->course->allLessons()->count());
        $this->assertDatabaseCount('courses', 3);
    }

    public function test_legacy_duplicate_run_cannot_reuse_another_editors_course_identity(): void
    {
        $this->fakeFreeCodeCampSource();
        $owner = User::factory()->create(['role' => 'instructor']);
        $other = User::factory()->create(['role' => 'instructor']);
        $category = CourseCategory::create(['name' => 'Algorithms']);
        $imports = app(CourseImportService::class);
        $first = $imports->import($owner, 'freecodecamp', 'curriculum/challenges/english/blocks/algorithms', $category->id, 1);
        $legacyKey = hash('sha256', 'freecodecamp:curriculum/challenges/english/blocks/algorithms');
        $first['run']->update(['source_key' => $legacyKey]);
        \App\Models\Learning\CourseContentRun::create([
            'source_key' => $legacyKey, 'source_type' => 'repository', 'user_id' => $other->id,
            'course_id' => $first['run']->course_id, 'status' => 'skipped', 'metadata' => $first['run']->metadata,
        ]);
        $result = $imports->import($other, 'freecodecamp', 'curriculum/challenges/english/blocks/algorithms', $category->id, 1);
        $this->assertTrue($result['created']);
        $this->assertNotSame($first['run']->course_id, $result['run']->course_id);
        $this->assertSame($other->id, $result['run']->course->instructor_id);
    }

    public function test_partial_import_rerun_preserves_manual_lesson_edits(): void
    {
        $this->fakeFreeCodeCampSource('', self::REVISION, '', true);
        $editor = User::factory()->create(['role' => 'instructor']);
        $category = CourseCategory::create(['name' => 'Algorithms']);
        $service = app(CourseImportService::class);
        $first = $service->import($editor, 'freecodecamp', 'curriculum/challenges/english/blocks/algorithms', $category->id, 2);
        $this->assertSame('partial', $first['run']->status);
        $lesson = $first['run']->course->allLessons()->firstOrFail();
        $lesson->update(['content' => 'Manually reviewed lesson content.']);
        $second = $service->import($editor, 'freecodecamp', 'curriculum/challenges/english/blocks/algorithms', $category->id, 2);
        $this->assertFalse($second['created']);
        $this->assertSame('skipped', $second['run']->status);
        $this->assertSame('Manually reviewed lesson content.', $lesson->fresh()->content);
        $this->assertDatabaseCount('courses', 1);
        Http::fake();
        $this->assertFalse($service->process($first['run']->id)['created']);
        Http::assertNothingSent();
    }

    public function test_failed_import_job_can_resume_after_source_recovers(): void
    {
        $editor = User::factory()->create(['role' => 'instructor']);
        $category = CourseCategory::create(['name' => 'Algorithms']);
        $imports = app(CourseImportService::class);
        $run = $imports->queue($editor, 'freecodecamp', 'curriculum/challenges/english/blocks/algorithms', $category->id, 1);
        Http::fake(fn () => Http::response([], 503));
        try {
            (new \App\Jobs\ImportCourseContent($run->id))->handle($imports);
            $this->fail('A failed download was accepted.');
        } catch (\App\Exceptions\CourseContentException) {
            $this->assertSame('failed', $run->fresh()->status);
        }
        $this->fakeFreeCodeCampSource();
        $imports->retry($run->fresh(), $editor);
        (new \App\Jobs\ImportCourseContent($run->id))->handle($imports);
        $this->assertSame('completed', $run->fresh()->status);
        $this->assertDatabaseCount('courses', 1);
    }

    public function test_import_sanitizes_scripts_links_and_excludes_assets(): void
    {
        $this->fakeFreeCodeCampSource('', self::REVISION, "---\ntitle: Unsafe markup\n---\n# --description--\nA lesson <script>alert(1)</script><a href=\"javascript:alert(1)\">link</a><img src=\"http://127.0.0.1/image\">\n\n```js\nconst example = 1;\n```\n");
        $editor = User::factory()->create(['role' => 'instructor']);
        $category = CourseCategory::create(['name' => 'Algorithms']);
        $result = app(CourseImportService::class)->import($editor, 'freecodecamp', 'curriculum/challenges/english/blocks/algorithms', $category->id, 1);
        $content = $result['run']->course->allLessons()->firstOrFail()->content;
        $this->assertStringNotContainsString('<script', $content);
        $this->assertStringNotContainsString('javascript:', $content);
        $this->assertStringNotContainsString('<img', $content);
        $this->assertStringContainsString('<code>', $content);
        $this->assertStringContainsString('const example = 1;', $content);
    }

    public function test_per_file_incompatible_license_is_refused(): void
    {
        $this->fakeFreeCodeCampSource('', self::REVISION, "---\ntitle: Restricted\nlicense: BSD-3-Clause OR CC-BY-NC-4.0\n---\n# --description--\nRestricted lesson.\n");
        $this->expectException(\App\Exceptions\CourseContentException::class);
        app(CourseImportService::class)->preview('freecodecamp', 'curriculum/challenges/english/blocks/algorithms', 1);
    }

    public function test_bsd_label_does_not_override_an_additional_source_notice(): void
    {
        $this->fakeFreeCodeCampSource('', self::REVISION, "---\ntitle: Third-party text\nlicense: BSD-3-Clause\nsource: https://unreviewed.example.test\n---\n# --description--\nThis separate source needs its own rights review.\n");
        $this->expectException(\App\Exceptions\CourseContentException::class);
        app(CourseImportService::class)->preview('freecodecamp', 'curriculum/challenges/english/blocks/algorithms', 1);
    }

    public function test_ancestor_notices_and_truncated_trees_fail_closed(): void
    {
        $client = $this->partialMock(\App\Services\CourseImport\GitHubContentClient::class);
        $client->shouldReceive('get')->andReturn(
            json_encode(['sha' => self::REVISION]),
            file_get_contents(base_path('tests/Fixtures/course-content/freecodecamp-LICENSE.md')),
            json_encode(['tree' => [['type' => 'blob', 'path' => 'NOTICE.md']], 'truncated' => false])
        );
        $this->expectException(\App\Exceptions\CourseContentException::class);
        app(CourseImportService::class)->preview('freecodecamp', 'curriculum/challenges/english/blocks/algorithms', 1);
    }

    public function test_role_revocation_and_cross_owner_retry_are_denied(): void
    {
        $editor = User::factory()->create(['role' => 'instructor']);
        $other = User::factory()->create(['role' => 'instructor']);
        $category = CourseCategory::create(['name' => 'Algorithms']);
        $imports = app(CourseImportService::class);
        $run = $imports->queue($editor, 'freecodecamp', 'curriculum/challenges/english/blocks/algorithms', $category->id, 1);
        $run->update(['status' => 'failed']);
        Livewire::actingAs($other)->test(\App\Livewire\CourseManagement\CourseContentStudio::class)
            ->call('retryRun', $run->id)->assertForbidden();
        $editor->syncRoles([]);
        $editor->update(['role' => 'student']);
        Http::fake();
        try {
            $imports->process($run->id);
            $this->fail('A revoked editor imported course content.');
        } catch (\App\Exceptions\CourseContentException) {
            Http::assertNothingSent();
            $this->assertDatabaseCount('courses', 0);
        }
    }

    public function test_same_source_processing_lock_blocks_a_concurrent_import(): void
    {
        $editor = User::factory()->create(['role' => 'instructor']);
        $category = CourseCategory::create(['name' => 'Algorithms']);
        $imports = app(CourseImportService::class);
        $run = $imports->queue($editor, 'freecodecamp', 'curriculum/challenges/english/blocks/algorithms', $category->id, 1);
        $lock = \Illuminate\Support\Facades\Cache::lock('course-import:'.$run->source_key, 900);
        $this->assertTrue($lock->get());
        Http::fake();
        try {
            $imports->process($run->id);
            $this->fail('A concurrent import was accepted.');
        } catch (\App\Exceptions\CourseContentException) {
            Http::assertNothingSent();
            $this->assertDatabaseCount('courses', 0);
        } finally {
            $lock->release();
        }
    }

    public function test_ai_failed_stage_preserves_successful_modules_and_retry_is_idempotent(): void
    {
        [$editor, $input] = $this->generationInput();
        $calls = 0;
        $this->fakeGeneration(function ($stage) use (&$calls) {
            $calls++;
            if ($stage === 'Module Two') {
                return ['invalid' => true];
            }

            return null;
        });
        $service = app(AiCourseGenerationService::class);
        $run = $service->queue($editor, $input, 'recovery-test');
        try {
            $service->process($run->id);
            $this->fail('A malformed module was accepted.');
        } catch (\Illuminate\Validation\ValidationException) {
            $this->assertSame('failed', $run->fresh()->status);
            $this->assertCount(1, $run->fresh()->stage_data['modules']);
            $this->assertDatabaseCount('courses', 0);
        }
        $this->assertSame(3, $calls);
        $this->fakeGeneration();
        $result = $service->process($run->id);
        $this->assertSame('completed', $result['run']->status);
        $this->assertFalse($service->process($run->id)['created']);
        $this->assertDatabaseCount('courses', 1);
        $this->assertSame(4, \App\Models\Ai\AiUsageRecord::where('feature', 'course_generation')->count());
    }

    public function test_ai_daily_quota_is_separate_and_retains_successful_stages(): void
    {
        [$editor, $input] = $this->generationInput();
        config(['bootkode_ai.course_generation_daily_requests' => 1]);
        $this->fakeGeneration();
        $service = app(AiCourseGenerationService::class);
        $run = $service->queue($editor, $input, 'quota-test');
        try {
            $service->process($run->id);
            $this->fail('Quota was bypassed.');
        } catch (\App\Exceptions\AiProviderException $exception) {
            $this->assertSame('daily_limit', $exception->reason);
            $this->assertArrayHasKey('blueprint', $run->fresh()->stage_data);
            $this->assertDatabaseCount('courses', 0);
            $this->assertSame(1, \App\Models\Ai\AiUsageRecord::where('feature', 'course_generation')->count());
            $this->assertSame(0, \App\Models\Ai\AiUsageRecord::where('feature', 'coding_mentor')->count());
        }
    }

    public function test_ai_cannot_control_grading_types_prices_ownership_or_publication(): void
    {
        [$editor, $input] = $this->generationInput();
        $input['include_assessments'] = true;
        $this->fakeGeneration(function ($stage, $data) {
            if ($stage === 'blueprint') {
                $data['instructor_id'] = 999;
                $data['price'] = 50000;
                $data['is_published'] = true;
                $data['modules'][0]['lessons'][0]['video_url'] = 'https://youtube.com/example';

                return $data;
            }
            $data['assessment']['type'] = 'project';
            $data['assessment']['pass_percentage'] = -100;
            $data['assessment']['questions'][0]['points'] = -999;
            $data['assessment']['questions'][0]['question_text'] .= '<script>alert(1)</script>';
            $data['assessment']['questions'][0]['explanation'] .= '<img src=x onerror=alert(1)>';
            $data['assessment']['questions'][0]['question_type'] = 'essay';

            return $data;
        });
        $result = app(AiCourseGenerationService::class)->generate($editor, $input, 'untrusted-ai');
        $course = $result['run']->course;
        $this->assertSame($editor->id, $course->instructor_id);
        $this->assertEquals(0, $course->price);
        $this->assertFalse($course->is_published || $course->is_approved);
        $question = \App\Models\Assessment\Question::firstOrFail();
        $this->assertSame('multiple_choice', $question->question_type);
        $this->assertStringNotContainsString('<script', $question->question_text);
        $this->assertStringNotContainsString('<img', $question->explanation);
        $this->assertEquals(5, $question->points);
        $this->assertSame('quiz', $question->assessment->type);
        $this->assertEquals(70, $question->assessment->pass_percentage);
        $this->assertNull($course->allLessons()->firstOrFail()->video_url);
        $this->assertSame(0, Course::publiclyAvailable()->whereKey($course->id)->count());
    }

    public function test_ai_rejects_an_out_of_range_quiz_answer(): void
    {
        [$editor, $input] = $this->generationInput();
        $input['include_assessments'] = true;
        $this->fakeGeneration(function ($stage, $data) {
            if ($stage !== 'blueprint') {
                $data['assessment']['questions'][0]['correct_answers'] = [99];
            }

            return $data;
        });
        try {
            app(AiCourseGenerationService::class)->generate($editor, $input, 'bad-answer');
            $this->fail('Invalid answer key was accepted.');
        } catch (\Illuminate\Validation\ValidationException) {
            $this->assertDatabaseCount('courses', 0);
        }
    }

    public function test_ai_request_id_cannot_be_reused_with_different_inputs(): void
    {
        [$editor, $input] = $this->generationInput();
        $service = app(AiCourseGenerationService::class);
        $service->queue($editor, $input, 'fixed-input');
        $input['topic'] = 'An unrelated course';
        $this->expectException(\Illuminate\Validation\ValidationException::class);
        $service->queue($editor, $input, 'fixed-input');
    }

    public function test_ai_cli_generates_and_reruns_without_a_duplicate(): void
    {
        [$editor, $input] = $this->generationInput();
        $this->fakeGeneration();
        $arguments = ['--topic' => $input['topic'], '--category-id' => $input['category_id'], '--instructor-id' => $editor->id,
            '--audience' => $input['target_audience'], '--objectives' => implode(';', $input['learning_objectives']), '--modules' => 2, '--lessons-per-module' => 2];
        $this->artisan('bootkode:courses-generate', $arguments)->expectsOutputToContain('Draft course:')->assertExitCode(0);
        $this->artisan('bootkode:courses-generate', $arguments)->expectsOutputToContain('Draft course:')->assertExitCode(0);
        $this->assertDatabaseCount('courses', 1);
    }

    public function test_missing_model_or_key_fails_without_network_or_course_writes(): void
    {
        [$editor, $input] = $this->generationInput();
        config(['bootkode_ai.providers.groq.model' => null]);
        Http::fake();
        try {
            app(AiCourseGenerationService::class)->generate($editor, $input, 'missing-model');
            $this->fail('A missing model was accepted.');
        } catch (\App\Exceptions\AiProviderException $exception) {
            $this->assertSame('not_configured', $exception->reason);
            Http::assertNothingSent();
            $this->assertDatabaseCount('courses', 0);
        }
    }

    public function test_ai_processing_lock_prevents_duplicate_provider_spending(): void
    {
        [$editor, $input] = $this->generationInput();
        $service = app(AiCourseGenerationService::class);
        $run = $service->queue($editor, $input, 'locked-generation');
        $lock = \Illuminate\Support\Facades\Cache::lock('course-generation:'.$run->id, 900);
        $this->assertTrue($lock->get());
        Http::fake();
        try {
            $service->process($run->id);
            $this->fail('An active generation lock was bypassed.');
        } catch (\App\Exceptions\AiProviderException $exception) {
            $this->assertSame('processing', $exception->reason);
            Http::assertNothingSent();
            $this->assertDatabaseCount('ai_usage_records', 0);
        } finally {
            $lock->release();
        }
    }

    public function test_generation_rejects_malformed_json_with_a_safe_error(): void
    {
        [$editor, $input] = $this->generationInput();
        Http::fake(fn () => Http::response(['choices' => [['message' => ['content' => 'not JSON private-provider-data']]]]));
        try {
            app(AiCourseGenerationService::class)->generate($editor, $input, 'malformed-json');
            $this->fail('Malformed JSON was accepted.');
        } catch (\App\Exceptions\AiProviderException $exception) {
            $this->assertSame('invalid_response', $exception->reason);
            $this->assertStringNotContainsString('private-provider-data', $exception->getMessage());
            $this->assertDatabaseCount('courses', 0);
        }
    }

    public function test_queued_import_keeps_the_reviewed_revision_and_uses_a_safe_reservation_window(): void
    {
        $this->fakeFreeCodeCampSource();
        $editor = User::factory()->create(['role' => 'instructor']);
        $category = CourseCategory::create(['name' => 'Algorithms']);
        \Illuminate\Support\Facades\Queue::fake();
        Livewire::actingAs($editor)->test(\App\Livewire\CourseManagement\CourseContentStudio::class)
            ->set('importCategoryId', (string) $category->id)->set('importLimit', 1)
            ->call('previewImport')->call('startImport')->assertHasNoErrors();
        $run = \App\Models\Learning\CourseContentRun::firstOrFail();
        $this->assertSame(self::REVISION, $run->metadata['expected_revision']);
        \Illuminate\Support\Facades\Queue::assertPushed(\App\Jobs\ImportCourseContent::class, fn ($job) => $job->connection === 'course-content' && $job->timeout < config('queue.connections.course-content.retry_after'));
        $this->assertDatabaseCount('courses', 0);
    }

    public function test_ui_requires_another_preview_when_the_selected_lesson_limit_changes(): void
    {
        $this->fakeFreeCodeCampSource();
        $editor = User::factory()->create(['role' => 'instructor']);
        $category = CourseCategory::create(['name' => 'Algorithms']);
        Livewire::actingAs($editor)->test(\App\Livewire\CourseManagement\CourseContentStudio::class)
            ->set('importCategoryId', (string) $category->id)->set('importLimit', 1)
            ->call('previewImport')->set('importLimit', 2)->call('startImport')->assertHasErrors('import');
        $this->assertDatabaseCount('course_content_runs', 0);
    }

    public function test_truncated_source_tree_is_not_treated_as_a_complete_listing(): void
    {
        $client = $this->partialMock(\App\Services\CourseImport\GitHubContentClient::class);
        $client->shouldReceive('get')->andReturn(json_encode(['sha' => self::REVISION]),
            file_get_contents(base_path('tests/Fixtures/course-content/freecodecamp-LICENSE.md')),
            json_encode(['tree' => [], 'truncated' => true]));
        $this->expectException(\App\Exceptions\CourseContentException::class);
        app(CourseImportService::class)->preview('freecodecamp', 'curriculum/challenges/english/blocks/algorithms', 1);
    }

    public function test_studio_repeated_generation_submissions_share_the_same_run(): void
    {
        [$editor, $input] = $this->generationInput();
        \Illuminate\Support\Facades\Queue::fake();
        $component = Livewire::actingAs($editor)->test(\App\Livewire\CourseManagement\CourseContentStudio::class)
            ->set('generationTopic', $input['topic'])->set('generationCategoryId', (string) $input['category_id'])
            ->set('targetAudience', $input['target_audience'])->set('learningObjectives', $input['learning_objectives'])
            ->set('moduleCount', 2)->set('lessonsPerModule', 2)->set('includeAssessments', false)
            ->set('teachingStyle', $input['teaching_style']);
        $component->call('startGeneration')->assertHasNoErrors()->call('startGeneration')->assertHasNoErrors();
        $this->assertDatabaseCount('course_content_runs', 1);
        $run = \App\Models\Learning\CourseContentRun::firstOrFail();
        $this->assertSame($run->id, app(AiCourseGenerationService::class)->queue($editor, $input)->id);
        \Illuminate\Support\Facades\Queue::assertPushed(\App\Jobs\GenerateCourseWithAi::class, fn ($job) => $job->runId === $run->id);
        $this->assertDatabaseCount('courses', 0);
    }

    private function generationInput(): array
    {
        config(['bootkode_ai.enabled' => true, 'bootkode_ai.provider' => 'groq', 'bootkode_ai.course_generation_daily_requests' => 12,
            'bootkode_ai.providers.groq' => ['key' => 'test-key', 'model' => 'test-model', 'base_url' => 'https://api.groq.test/v1']]);
        $editor = User::factory()->create(['role' => 'instructor']);
        $category = CourseCategory::create(['name' => 'Business']);

        return [$editor, ['topic' => 'Campaign Planning', 'category_id' => $category->id, 'target_audience' => 'Small business owners',
            'skill_level' => 'beginner', 'learning_objectives' => ['Plan a campaign', 'Measure campaign results'], 'duration_minutes' => 180,
            'module_count' => 2, 'lessons_per_module' => 2, 'teaching_style' => 'Examples and practice', 'practical_requirements' => '',
            'assessment_requirements' => '', 'include_assessments' => false, 'language' => 'English', 'additional_instructions' => '']];
    }

    private function fakeGeneration(?callable $change = null): void
    {
        Http::swap(new Factory);
        Http::preventStrayRequests();
        Http::fake(function (Request $request) use ($change) {
            $input = json_decode($request['messages'][1]['content'], true);
            $stage = isset($input['module']) ? $input['module']['title'] : 'blueprint';
            $data = $stage === 'blueprint' ? [
                'title' => 'Campaign Planning', 'subtitle' => 'Practical campaign decisions',
                'description' => str_repeat('Plan a campaign around a specific audience and choose useful measurement evidence. ', 2),
                'learning_outcomes' => ['Plan a campaign', 'Measure campaign results'], 'prerequisites' => [],
                'modules' => array_map(fn ($title) => ['title' => $title, 'description' => 'Develop a campaign decision with evidence.',
                    'lessons' => [['title' => $title.' A', 'outcome' => 'Explain a campaign decision'], ['title' => $title.' B', 'outcome' => 'Apply a measurement method']]], ['Module One', 'Module Two']),
            ] : [
                'lessons' => array_map(fn ($lesson) => ['title' => $lesson['title'], 'description' => 'Explain and apply a practical campaign decision.',
                    'content' => str_repeat('Identify the target audience. For example, a neighbourhood store can compare two messages with the same budget. Activity: record your goal and evidence, then justify your next decision. ', 3)], $input['module']['lessons']),
                'assessment' => ['title' => $stage.' Quiz', 'description' => 'Check the stated outcomes.', 'questions' => [
                    ['question_text' => 'What supports a measurable campaign?', 'options' => ['A goal', 'A guess', 'No record'], 'correct_answers' => [0], 'explanation' => 'A goal defines what to measure.'],
                    ['question_text' => 'How should you review a test?', 'options' => ['Use evidence', 'Guess', 'Ignore it'], 'correct_answers' => [0], 'explanation' => 'Evidence supports a decision.'],
                ]],
            ];
            if ($change) {
                $data = $change($stage, $data) ?? $data;
            }

            return Http::response(['choices' => [['message' => ['content' => json_encode($data)]]], 'usage' => ['prompt_tokens' => 10, 'completion_tokens' => 20]]);
        });
    }

    private function fakeFreeCodeCampSource(string $license = '', string $revision = self::REVISION, string $markdownOverride = '', bool $skipSecond = false): void
    {
        // Replacing a revision must replace the previous fake, whose callbacks otherwise accumulate.
        Http::swap(new Factory);
        Http::preventStrayRequests();

        $markdown = <<<'MARKDOWN'
---
id: 0123456789abcdef01234567
title: Implement Selection Sort
---
# --description--
Selection sort repeatedly selects a minimum value and places it in the next position. Explain the sequence and its quadratic time complexity.

**Instructions:** Write a function that returns an array in ascending order.

# --hints--
This section should not be imported.
secret solution content
MARKDOWN;
        if ($markdownOverride !== '') {
            $markdown = $markdownOverride;
        }
        $bsd = $license !== '' ? $license : file_get_contents(base_path('tests/Fixtures/course-content/freecodecamp-LICENSE.md'));

        Http::fake(function (Request $request) use ($markdown, $bsd, $revision, $skipSecond) {
            $url = $request->url();
            if (str_contains($url, '/commits/main')) {
                return Http::response(['sha' => $revision]);
            }
            if (str_contains($url, '/LICENSE.md')) {
                return Http::response($bsd);
            }
            if (str_contains($url, '/git/trees/')) {
                $treeId = basename(parse_url($url, PHP_URL_PATH));
                $segments = ['curriculum', 'challenges', 'english', 'blocks', 'algorithms'];
                $ids = [$revision, str_repeat('1', 40), str_repeat('2', 40), str_repeat('3', 40), str_repeat('4', 40), str_repeat('5', 40)];
                $position = array_search($treeId, $ids, true);
                $tree = $position < 5 ? [['path' => $segments[$position], 'type' => 'tree', 'sha' => $ids[$position + 1]]] : [
                    ['path' => '0123456789abcdef01234567.md', 'mode' => '100644', 'type' => 'blob', 'size' => strlen($markdown)],
                    ['path' => '1123456789abcdef01234567.md', 'mode' => '100644', 'type' => 'blob', 'size' => strlen($markdown)],
                ];

                return Http::response(['tree' => $tree, 'truncated' => false]);
            }
            if ($skipSecond && str_contains($url, '1123456789abcdef01234567.md')) {
                return Http::response('No supported front matter.');
            }
            if (str_contains($url, '.md')) {
                return Http::response($markdown);
            }

            return Http::response([], 404);
        });
    }
}
