<?php

namespace Tests\Feature;

use App\Exceptions\AiProviderException;
use App\Livewire\Career\CareerCentre;
use App\Livewire\Career\CareerReview;
use App\Models\Ai\AiLearningProfile;
use App\Models\Assessment\LearningSession;
use App\Models\Career\CompetencyEvidence;
use App\Models\Career\JobPortal;
use App\Models\Core\User;
use App\Models\Credentials\Certificate;
use App\Models\Learning\Course;
use App\Services\Career\CareerAiService;
use App\Services\Career\CompetencyEvidenceService;
use App\Services\Career\JobEligibilityService;
use App\Services\Career\JobRequirementService;
use App\Services\JobApplicationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class CareerIntelligenceTest extends TestCase
{
    use RefreshDatabase;

    private function fixture(bool $review = true): array
    {
        Http::preventStrayRequests();
        Role::findOrCreate('academy_admin', 'web');
        $admin = User::factory()->create(['role' => 'academy_admin']);
        $learner = User::factory()->create();
        $job = JobPortal::create(['posted_by' => $admin->id, 'title' => 'Data Analyst', 'description' => 'SQL is essential. Python is preferred. Accounting degree required. Three years in accounting required.',
            'company_name' => 'Synthetic employer', 'category' => 'analytics', 'location' => 'Lagos', 'status' => 'active', 'is_public' => true, 'application_method' => 'internal']);
        $this->actingAs($admin);
        $job = $job->fresh();
        if ($review) {
            $this->publish($job, [$this->requirement('SQL', true), $this->requirement('Python', false)]);
        }
        $this->actingAs($learner);

        return compact('admin', 'learner', 'job');
    }

    private function requirement(string $label, bool $mandatory = true, string $kind = 'skill', ?float $years = null): array
    {
        $excerpt = match ($label) {
            'SQL' => 'SQL is essential.', 'Python' => 'Python is preferred.', 'Accounting degree' => 'Accounting degree required.', default => 'Three years in accounting required.'
        };

        return ['kind' => $kind, 'label' => $label, 'mandatory' => $mandatory, 'minimum_years' => $years, 'excerpt' => $excerpt];
    }

    private function publish(JobPortal $job, array $items): void
    {
        $service = app(JobRequirementService::class);
        $service->publish($job->id, $items, $service->hash($job->fresh()), 'Checked the original requirement text and mandatory versus preferred status.');
    }

    private function evidence(array $f, string $label = 'SQL', array $attributes = [], bool $verify = true): CompetencyEvidence
    {
        $this->actingAs($f['learner']);
        $evidence = app(CompetencyEvidenceService::class)->claim(array_merge(['kind' => 'skill', 'label' => $label, 'source_type' => 'external', 'reference' => 'Synthetic source checked independently by the reviewer.'], $attributes));
        if ($verify) {
            $this->actingAs($f['admin']);
            app(CompetencyEvidenceService::class)->review($evidence->id, 'verified', 'Independently checked the source and its relevance to this competency.');
        }
        $this->actingAs($f['learner']);

        return $evidence->fresh();
    }

    private function eligibility(array $f): array
    {
        return app(JobEligibilityService::class)->evaluate($f['learner'], $f['job']);
    }

    private function ai(string $content, int $status = 200): void
    {
        config(['bootkode_ai.enabled' => true, 'bootkode_ai.provider' => 'groq', 'bootkode_ai.retries' => 0,
            'bootkode_ai.providers.groq' => ['key' => 'private-test-key', 'model' => 'configured-test-model', 'base_url' => 'https://api.groq.test/v1']]);
        Http::fake(['https://api.groq.test/*' => Http::response(['choices' => [['message' => ['content' => $content]]], 'usage' => ['prompt_tokens' => 25, 'completion_tokens' => 15]], $status)]);
    }

    public function test_unreviewed_legacy_vacancies_remain_applicable_without_a_false_eligibility_claim(): void
    {
        $f = $this->fixture(false);
        $status = $this->eligibility($f);
        $this->assertTrue($status['can_apply']);
        $this->assertFalse($status['reviewed']);
        $this->assertSame('assessment_required', $status['status']);
        $application = app(JobApplicationService::class)->submit($f['learner'], $f['job']->id, 'A legitimate cover letter.');
        $this->assertSame($f['learner']->id, $application->user_id);
    }

    public function test_self_reports_and_pending_claims_do_not_establish_mandatory_competence(): void
    {
        $f = $this->fixture();
        AiLearningProfile::create(['user_id' => $f['learner']->id, 'existing_knowledge' => ['skills' => ['SQL']], 'starting_level' => 'expert']);
        $this->evidence($f, verify: false);
        $status = $this->eligibility($f);
        $this->assertFalse($status['can_apply']);
        $this->assertSame('assessment_required', $status['status']);
        $this->assertSame(0, $status['mandatory_met']);
        $this->expectException(\Illuminate\Validation\ValidationException::class);
        try {
            app(JobApplicationService::class)->submit($f['learner'], $f['job']->id, 'Cover letter');
        } finally {
            $this->assertDatabaseCount('job_applications', 0);
            $this->assertSame(0, $f['job']->fresh()->applications_count);
        }
    }

    public function test_missing_preferred_skill_does_not_block_an_application_and_replay_is_safe(): void
    {
        $f = $this->fixture();
        $this->evidence($f);
        $status = $this->eligibility($f);
        $this->assertTrue($status['can_apply']);
        $this->assertSame('partially_qualified', $status['status']);
        $service = app(JobApplicationService::class);
        $first = $service->submit($f['learner'], $f['job']->id, 'A cover letter');
        $second = $service->submit($f['learner'], $f['job']->id, 'A retry');
        $this->assertSame($first->id, $second->id);
        $this->assertDatabaseCount('job_applications', 1);
        $this->assertSame(1, $f['job']->fresh()->applications_count);
    }

    public function test_exact_reviewed_evidence_matches_without_requiring_a_bootkode_certificate(): void
    {
        $f = $this->fixture();
        $this->evidence($f);
        $this->evidence($f, 'Python');
        $this->assertSame('eligible', $this->eligibility($f)['status']);
        $this->assertDatabaseCount('certificates', 0);
    }

    public function test_partial_mandatory_coverage_reports_a_skill_gap(): void
    {
        $f = $this->fixture();
        $this->actingAs($f['admin']);
        $this->publish($f['job'], [$this->requirement('SQL'), $this->requirement('Python')]);
        $this->evidence($f);
        $status = $this->eligibility($f);
        $this->assertSame('skills_gap_identified', $status['status']);
        $this->assertSame(1, $status['mandatory_met']);
        $this->assertFalse($status['can_apply']);
    }

    public function test_qualification_and_relevant_experience_are_distinct_from_skills(): void
    {
        $f = $this->fixture();
        $this->actingAs($f['admin']);
        $this->publish($f['job'], [$this->requirement('Accounting degree', kind: 'qualification'), $this->requirement('Accounting', kind: 'experience', years: 3)]);
        $this->evidence($f, 'Accounting degree', ['kind' => 'qualification']);
        $this->evidence($f, 'Accounting', ['kind' => 'experience', 'years_experience' => 2]);
        $this->assertSame('experience_requirement_not_met', $this->eligibility($f)['status']);
        $this->evidence($f, 'Accounting', ['kind' => 'experience', 'years_experience' => 3]);
        $this->assertTrue($this->eligibility($f)['can_apply']);
    }

    public function test_expired_vacancies_are_browsable_but_not_applicable(): void
    {
        $f = $this->fixture();
        $f['job']->update(['expires_at' => now()->subMinute()]);
        $status = $this->eligibility($f);
        $this->assertSame('vacancy_expired', $status['status']);
        $this->assertFalse($status['can_apply']);
        Livewire::test(CareerCentre::class)->call('selectJob', $f['job']->id)->assertSee('Vacancy closed');
    }

    public function test_reviewed_source_changes_pause_applications_and_stale_reviewers_cannot_overwrite(): void
    {
        $f = $this->fixture();
        $old = app(JobRequirementService::class)->hash($f['job']);
        $f['job']->update(['requirements' => 'An additional requirement arrived.']);
        $this->assertFalse($this->eligibility($f)['can_apply']);
        $this->actingAs($f['admin']);
        try {
            app(JobRequirementService::class)->publish($f['job']->id, [], $old, 'A source review that is now stale.');
            $this->fail('Stale review was accepted');
        } catch (HttpException $e) {
            $this->assertSame(409, $e->getStatusCode());
        }
        $this->assertCount(1, $f['job']->fresh()->requirements_review_history);
    }

    public function test_fabricated_excerpts_and_unsupported_experience_shapes_are_rejected(): void
    {
        $f = $this->fixture(false);
        $this->actingAs($f['admin']);
        $this->expectException(\Illuminate\Validation\ValidationException::class);
        $this->publish($f['job'], [['kind' => 'skill', 'label' => 'Kubernetes', 'mandatory' => true, 'minimum_years' => 10, 'excerpt' => 'Invented requirement']]);
    }

    public function test_revocation_expiry_and_reassessment_change_current_eligibility_without_deleting_history(): void
    {
        $f = $this->fixture();
        $evidence = $this->evidence($f);
        $this->assertTrue($this->eligibility($f)['can_apply']);
        $this->actingAs($f['admin']);
        app(CompetencyEvidenceService::class)->review($evidence->id, 'revoked', 'The source was withdrawn and is no longer reliable.');
        $this->actingAs($f['learner']);
        $this->assertFalse($this->eligibility($f)['can_apply']);
        app(CompetencyEvidenceService::class)->reassess($evidence->id);
        $this->assertSame('pending', $evidence->fresh()->review_status);
        $this->assertCount(3, $evidence->fresh()->review_history);
    }

    public function test_verified_evidence_expiry_is_checked_on_every_evaluation(): void
    {
        $f = $this->fixture();
        $evidence = $this->evidence($f);
        $evidence->update(['expires_at' => now()->addMinute()]);
        $this->assertTrue($this->eligibility($f)['can_apply']);
        $this->travel(2)->minutes();
        $this->assertFalse($this->eligibility($f)['can_apply']);
        $this->travelBack();
    }

    public function test_original_finalized_attempt_results_are_rechecked_after_a_mark_correction(): void
    {
        $f = $this->fixture();
        $assessment = \App\Models\Assessment\Assessment::create(['title' => 'SQL practical assessment', 'type' => 'quiz', 'pass_percentage' => 70]);
        $session = LearningSession::create(['user_id' => $f['learner']->id, 'activity_type' => 'assessment', 'started_at' => now()->subMinutes(5), 'ended_at' => now(),
            'metadata' => ['assessment_id' => $assessment->id, 'pass_percentage' => 70, 'results' => ['passed' => true, 'pending_review' => false, 'percentage' => 85]]]);
        $evidence = $this->evidence($f, attributes: ['source_type' => 'assessment', 'source_id' => $session->id]);
        $this->assertTrue($this->eligibility($f)['can_apply']);
        $session->update(['metadata' => ['assessment_id' => $assessment->id, 'pass_percentage' => 70, 'results' => ['passed' => false, 'pending_review' => false, 'percentage' => 50]]]);
        $this->assertFalse($this->eligibility($f)['can_apply']);
        $this->assertSame('verified', $evidence->fresh()->review_status);
    }

    public function test_another_learners_source_and_self_verification_are_refused(): void
    {
        $f = $this->fixture();
        $other = User::factory()->create();
        $session = LearningSession::create(['user_id' => $other->id, 'activity_type' => 'assessment', 'started_at' => now(), 'ended_at' => now(), 'metadata' => ['assessment_id' => 5, 'pass_percentage' => 50, 'results' => ['passed' => true, 'pending_review' => false, 'percentage' => 100]]]);
        try {
            $this->evidence($f, attributes: ['source_type' => 'assessment', 'source_id' => $session->id]);
            $this->fail('Unowned source accepted');
        } catch (HttpException $e) {
            $this->assertSame(422, $e->getStatusCode());
        }
        $f['learner']->assignRole('academy_admin');
        $pending = $this->evidence($f, verify: false);
        try {
            app(CompetencyEvidenceService::class)->review($pending->id, 'verified', 'Attempt to verify my own skills.');
            $this->fail('Self verification accepted');
        } catch (HttpException $e) {
            $this->assertSame(403, $e->getStatusCode());
        }
        $this->assertSame('pending', $pending->fresh()->review_status);
    }

    public function test_repeated_claims_are_idempotent_and_cannot_set_their_review_state(): void
    {
        $f = $this->fixture();
        $one = $this->evidence($f, attributes: ['review_status' => 'verified', 'reviewed_by' => $f['admin']->id], verify: false);
        $two = $this->evidence($f, verify: false);
        $this->assertSame($one->id, $two->id);
        $this->assertSame('pending', $two->review_status);
        $this->assertNull($two->reviewed_by);
    }

    public function test_private_evidence_and_opportunities_cannot_be_accessed_through_forged_ids(): void
    {
        $f = $this->fixture();
        $evidence = $this->evidence($f, verify: false);
        $other = User::factory()->create();
        $this->actingAs($other);
        $this->get(route('career.review'))->assertForbidden();
        Livewire::test(CareerReview::class)->assertForbidden();
        $this->get(route('career.centre'))->assertOk()->assertDontSee('Synthetic source checked independently');
        try {
            app(CompetencyEvidenceService::class)->reassess($evidence->id);
            $this->fail('Unowned reassessment accepted');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            $this->assertTrue(true);
        }
        $private = JobPortal::withoutEvents(fn () => JobPortal::create(['posted_by' => $f['admin']->id, 'slug' => Str::uuid(), 'title' => 'PRIVATE VACANCY', 'description' => 'Private', 'company_name' => 'Private', 'category' => 'private', 'location' => 'Private', 'status' => 'active', 'is_public' => false]));
        try {
            Livewire::test(CareerCentre::class)->call('selectJob', $private->id);
            $this->fail('Private opportunity was disclosed');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $this->assertSame([$private->id], $e->getIds());
        }
    }

    public function test_account_deactivation_and_admin_role_revocation_apply_to_livewire_requests(): void
    {
        $f = $this->fixture();
        $component = Livewire::test(CareerCentre::class);
        User::whereKey($f['learner']->id)->update(['is_active' => false]);
        $component->call('selectJob', $f['job']->id)->assertForbidden();
        $this->actingAs($f['admin']);
        $adminComponent = Livewire::test(CareerReview::class);
        $f['admin']->syncRoles([]);
        $adminComponent->call('loadJob', $f['job']->id)->assertForbidden();
    }

    public function test_course_options_use_real_published_whole_skill_matches_and_do_not_enroll(): void
    {
        $f = $this->fixture();
        foreach ([['SQL foundations', true, true, null], ['NoSQL foundations', true, true, null], ['SQL hidden draft', false, true, null], ['SQL scheduled', true, true, now()->addDay()]] as [$title, $published, $approved, $scheduled]) {
            Course::withoutEvents(fn () => Course::create(['instructor_id' => $f['admin']->id, 'slug' => Str::uuid(), 'title' => $title, 'is_published' => $published, 'is_approved' => $approved, 'scheduled_publish_at' => $scheduled]));
        }
        $options = app(JobEligibilityService::class)->learningOptions($this->eligibility($f));
        $this->assertSame(['SQL foundations'], collect($options[0]['courses'])->pluck('title')->all());
        $this->assertSame([], $options[1]['courses']);
        $this->assertDatabaseCount('course_enrollments', 0);
        $this->assertNotSame(app(JobRequirementService::class)->normalize('C#'), app(JobRequirementService::class)->normalize('C++'));
    }

    public function test_ai_extraction_is_validated_but_never_publishes_its_own_requirements(): void
    {
        $f = $this->fixture(false);
        $this->actingAs($f['admin']);
        $this->ai(json_encode(['items' => [$this->requirement('SQL'), $this->requirement('Python', false)]]));
        $items = app(CareerAiService::class)->extract($f['job']->id, app(JobRequirementService::class)->hash($f['job']));
        $this->assertTrue($items[0]['mandatory']);
        $this->assertFalse($items[1]['mandatory']);
        $this->assertNull($f['job']->fresh()->requirements_reviewed_at);
        $this->assertDatabaseHas('ai_usage_records', ['feature' => 'career_requirements', 'status' => 'succeeded', 'prompt_tokens' => 25]);
        Http::assertSent(fn ($request) => $request['model'] === 'configured-test-model' && $request['response_format']['type'] === 'json_object');
    }

    public function test_invalid_ai_payload_is_not_persisted_and_usage_records_the_failure(): void
    {
        $f = $this->fixture(false);
        $this->actingAs($f['admin']);
        $this->ai('{malformed');
        try {
            app(CareerAiService::class)->extract($f['job']->id, app(JobRequirementService::class)->hash($f['job']));
            $this->fail('Invalid extraction accepted');
        } catch (AiProviderException $e) {
            $this->assertSame('invalid_response', $e->reason);
        }
        $this->assertNull($f['job']->fresh()->competency_requirements);
        $this->assertDatabaseHas('ai_usage_records', ['status' => 'failed', 'error_reason' => 'invalid_response']);
    }

    public function test_provider_failures_consume_the_shared_daily_budget_without_leaking_provider_data(): void
    {
        $this->fixture();
        $this->ai('private provider body', 429);
        config(['bootkode_ai.career_daily_requests' => 1]);
        try {
            app(CareerAiService::class)->advise('How should I prepare?', true);
            $this->fail('Provider failure ignored');
        } catch (AiProviderException $e) {
            $this->assertSame('rate_limited', $e->reason);
            $this->assertStringNotContainsString('private provider', $e->getMessage());
        }
        try {
            app(CareerAiService::class)->advise('Try again', true);
            $this->fail('Quota bypassed');
        } catch (AiProviderException $e) {
            $this->assertSame('daily_limit', $e->reason);
        }
        Http::assertSentCount(1);
        $this->assertDatabaseCount('ai_usage_records', 1);
    }

    public function test_career_advice_requires_consent_and_projects_only_owned_safe_context(): void
    {
        $f = $this->fixture();
        $this->evidence($f, attributes: ['reference' => 'PRIVATE PROOF REFERENCE MUST NOT LEAVE THE APP']);
        AiLearningProfile::create(['user_id' => $f['learner']->id, 'goal' => 'My actual goal', 'existing_knowledge' => ['skills' => ['Self-reported SQL']]]);
        $other = User::factory()->create();
        AiLearningProfile::create(['user_id' => $other->id, 'goal' => 'OTHER PRIVATE GOAL']);
        $this->ai('<script>inventedExperience()</script>');
        try {
            app(CareerAiService::class)->advise('Question');
            $this->fail('Missing consent accepted');
        } catch (HttpException $e) {
            $this->assertSame(422, $e->getStatusCode());
        }
        Http::assertNothingSent();
        Livewire::test(CareerCentre::class)->set('question', 'How do I prepare?')->set('shareContext', true)->call('askAdvisor')
            ->assertSee('&lt;script&gt;inventedExperience()&lt;/script&gt;', false)->assertDontSee('<script>inventedExperience()</script>', false);
        Http::assertSent(function ($request) {
            $text = json_encode($request->data());

            return str_contains($text, 'My actual goal') && str_contains($text, 'self_reported_skills')
                && ! str_contains($text, 'OTHER PRIVATE GOAL') && ! str_contains($text, 'PRIVATE PROOF REFERENCE');
        });
    }

    public function test_approved_certificate_supports_only_its_named_credential_and_revocation_is_live(): void
    {
        $f = $this->fixture();
        $course = Course::withoutEvents(fn () => Course::create(['instructor_id' => $f['admin']->id, 'slug' => Str::uuid(), 'title' => 'SQL']));
        $certificate = Certificate::create(['course_id' => $course->id, 'user_id' => $f['learner']->id, 'status' => Certificate::STATUS_APPROVED]);
        $this->assertFalse($this->eligibility($f)['can_apply']);
        $f['job']->update(['requirements' => 'SQL certificate required.']);
        $this->actingAs($f['admin']);
        $this->publish($f['job'], [['kind' => 'certification', 'label' => 'SQL', 'mandatory' => true, 'minimum_years' => null, 'excerpt' => 'SQL certificate required.']]);
        $this->evidence($f, 'SQL', ['kind' => 'certification', 'source_type' => 'certificate', 'source_id' => $certificate->id]);
        $this->assertTrue($this->eligibility($f)['can_apply']);
        $certificate->update(['status' => Certificate::STATUS_REVOKED]);
        $this->assertFalse($this->eligibility($f)['can_apply']);
        $this->assertNotNull($certificate->fresh()->verification_code);
    }

    public function test_admin_can_review_requirements_in_place_without_an_automatic_ai_approval(): void
    {
        $f = $this->fixture(false);
        $this->actingAs($f['admin']);
        Livewire::test(CareerReview::class)->call('loadJob', $f['job']->id)
            ->set('items', [$this->requirement('SQL')])->set('reason', 'Reviewed the original source for the exact required competency.')
            ->call('publish')->assertHasNoErrors()->assertSee('Reviewed requirements saved');
        $this->assertCount(1, $f['job']->fresh()->competency_requirements);
        $this->actingAs($f['learner']);
        Livewire::test(CareerCentre::class)->call('selectJob', $f['job']->id)->assertSee('Assessment or evidence review required');
    }

    public function test_source_review_summaries_are_admin_only_and_exclude_private_grading_material(): void
    {
        $f = $this->fixture();
        $assessment = \App\Models\Assessment\Assessment::create(['title' => 'SQL retained assessment', 'type' => 'quiz', 'pass_percentage' => 90]);
        $session = LearningSession::create(['user_id' => $f['learner']->id, 'activity_type' => 'assessment', 'started_at' => now()->subMinutes(5), 'ended_at' => now(),
            'metadata' => ['assessment_id' => $assessment->id, 'pass_percentage' => 70, 'results' => ['passed' => true, 'pending_review' => false, 'percentage' => 85], 'grading' => ['answer' => 'PRIVATE GRADING KEY']]]);
        $evidence = $this->evidence($f, attributes: ['source_type' => 'assessment', 'source_id' => $session->id], verify: false);
        $this->actingAs($f['admin']);
        $summary = app(CompetencyEvidenceService::class)->sourceSummary($evidence);
        $this->assertSame('70%', $summary['Original pass threshold']);
        $this->assertStringNotContainsString('PRIVATE GRADING KEY', json_encode($summary));
        $this->actingAs($f['learner']);
        $this->expectException(HttpException::class);
        app(CompetencyEvidenceService::class)->sourceSummary($evidence);
    }
}
