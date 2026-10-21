<?php

namespace Tests\Feature;

use App\Livewire\Career\JobManagement;
use App\Livewire\Career\JobPortal as JobPortalComponent;
use App\Models\Career\JobApplication;
use App\Models\Career\JobPortal;
use App\Models\Core\User;
use App\Services\JobApplicationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CareerIntegrityTest extends TestCase
{
    use RefreshDatabase;

    private function job(array $attributes = []): JobPortal
    {
        return JobPortal::create(array_merge([
            'posted_by' => User::factory()->create()->id, 'title' => 'Fixture developer opening',
            'description' => 'Temporary opening for career regression tests only.', 'company_name' => 'Test fixture',
            'category' => 'engineering', 'location' => 'Lagos', 'status' => 'active', 'is_public' => true,
            'application_method' => 'internal',
        ], $attributes));
    }

    public function test_job_administration_requires_an_admin_on_http_and_livewire(): void
    {
        $this->actingAs(User::factory()->create());
        $this->get(route('admin.job'))->assertForbidden();
        Livewire::test(JobManagement::class)->assertForbidden();
    }

    public function test_job_detail_does_not_serialize_other_applicants_or_private_posters_email(): void
    {
        $job = $this->job();
        $applicant = User::factory()->create();
        JobApplication::create(['job_id' => $job->id, 'user_id' => $applicant->id, 'cover_letter' => 'PRIVATE APPLICATION TEXT']);
        $component = Livewire::actingAs(User::factory()->create())->test(JobPortalComponent::class)->call('viewJob', $job->id);
        $serialized = json_encode($component->get('selectedJob')->toArray());
        $this->assertStringNotContainsString('PRIVATE APPLICATION TEXT', $serialized);
        $this->assertStringNotContainsString($applicant->email, $serialized);
        $this->assertStringNotContainsString($job->postedBy->email, $serialized);
        $this->assertFalse($component->get('selectedJob')->relationLoaded('applications'));
    }

    public function test_unpublished_expired_and_closed_jobs_cannot_receive_applications(): void
    {
        $user = User::factory()->create();
        foreach ([['status' => 'draft'], ['is_public' => false], ['expires_at' => now()->subDay()],
            ['application_deadline' => now()->subMinute()], ['application_method' => 'email'],
        ] as $attributes) {
            $job = $this->job($attributes);
            try {
                Livewire::actingAs($user)->test(JobPortalComponent::class)->call('openApplicationModal', $job->id);
                $this->fail('Unavailable job accepted an application modal');
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) {
                $this->assertSame([$job->id], $exception->getIds());
            }
        }
        $this->assertDatabaseCount('job_applications', 0);
    }

    public function test_application_and_counter_are_atomic_and_replays_do_not_duplicate_them(): void
    {
        $user = User::factory()->create();
        $job = $this->job();
        $this->actingAs($user);
        $service = app(JobApplicationService::class);
        $first = $service->submit($user, $job->id, str_repeat('Cover letter ', 6));
        $retry = $service->submit($user, $job->id, 'Retry');
        $this->assertSame($first->id, $retry->id);
        $this->assertDatabaseCount('job_applications', 1);
        $this->assertSame(1, $job->fresh()->applications_count);
        $this->assertSame('pending', $first->status);
    }

    public function test_application_is_rechecked_after_the_job_closes_with_modal_open(): void
    {
        $user = User::factory()->create();
        $job = $this->job();
        $component = Livewire::actingAs($user)->test(JobPortalComponent::class)->call('openApplicationModal', $job->id);
        $job->update(['status' => 'paused']);
        try {
            $component->set('coverLetter', str_repeat('Cover letter ', 6))->call('submitApplication');
            $this->fail('A stale modal accepted a closed job');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) {
            $this->assertSame([$job->id], $exception->getIds());
        }
        $this->assertDatabaseCount('job_applications', 0);
    }

    public function test_resume_download_requires_applicant_ownership_or_admin_and_new_upload_is_private(): void
    {
        Storage::fake('local');
        Storage::fake('public');
        $user = User::factory()->create();
        $job = $this->job();
        $file = UploadedFile::fake()->create('resume.pdf', 20, 'application/pdf');
        $application = app(JobApplicationService::class)->submit($user, $job->id, 'Cover letter', [], $file);
        Storage::disk('local')->assertExists($application->resume_path);
        Storage::disk('public')->assertMissing($application->resume_path);
        $this->actingAs(User::factory()->create())->get($application->resume_url)->assertForbidden();
        $this->actingAs($user)->get($application->resume_url)->assertOk()->assertHeader('X-Content-Type-Options', 'nosniff');
        Role::findOrCreate('academy_admin', 'web');
        $admin = User::factory()->create();
        $admin->assignRole('academy_admin');
        $this->actingAs($admin)->get($application->resume_url)->assertOk();
    }

    public function test_failed_application_counter_write_rolls_back_record_and_uploaded_file(): void
    {
        Storage::fake('local');
        $job = $this->job();
        $user = User::factory()->create();
        JobPortal::updating(function () { throw new \RuntimeException('Forced counter failure'); });
        try {
            app(JobApplicationService::class)->submit($user, $job->id, 'Cover letter', [], UploadedFile::fake()->create('resume.pdf', 20));
            $this->fail('Expected the counter failure');
        } catch (\RuntimeException $exception) {
            $this->assertSame('Forced counter failure', $exception->getMessage());
        } finally {
            JobPortal::flushEventListeners();
            JobPortal::clearBootedModels();
        }
        $this->assertDatabaseCount('job_applications', 0);
        $this->assertSame(0, $job->fresh()->applications_count);
        $this->assertSame([], Storage::disk('local')->allFiles('private/job-resumes'));
    }

    public function test_foreign_key_migration_refuses_unmatched_records_without_deleting_them(): void
    {
        $user = User::factory()->create();
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        try {
            DB::table('job_saves')->insert(['job_id' => 999999, 'user_id' => $user->id]);
        } finally {
            \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();
        }
        try {
            (require database_path('migrations/2026_10_21_010000_correct_career_job_foreign_keys.php'))->up();
            $this->fail('Migration accepted an unmatched career record');
        } catch (\RuntimeException $exception) {
            $this->assertStringContainsString('Unmatched career records in job_saves', $exception->getMessage());
        }
        $this->assertDatabaseHas('job_saves', ['job_id' => 999999, 'user_id' => $user->id]);
    }

    public function test_saved_jobs_reference_career_records_and_unavailable_alerts_never_report_success(): void
    {
        $job = $this->job();
        $user = User::factory()->create();
        Livewire::actingAs($user)->test(JobPortalComponent::class)->call('toggleSaveJob', $job->id)->assertHasNoErrors();
        $this->assertDatabaseHas('job_saves', ['job_id' => $job->id, 'user_id' => $user->id]);
        Livewire::test(JobPortalComponent::class)->call('createJobAlert')->assertSee('Job alerts are not available yet');
        $this->assertSame('job_portal', collect(\Illuminate\Support\Facades\Schema::getForeignKeys('job_saves'))->firstWhere('columns', ['job_id'])['foreign_table']);
    }
}
