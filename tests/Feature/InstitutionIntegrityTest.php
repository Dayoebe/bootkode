<?php

namespace Tests\Feature;

use App\Livewire\Institution\InstitutionPortal;
use App\Models\Admin\InstitutionInvitation;
use App\Models\Admin\InstitutionUser;
use App\Models\Core\Institution;
use App\Models\Core\User;
use App\Services\InstitutionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class InstitutionIntegrityTest extends TestCase
{
    use RefreshDatabase;

    private function institution(string $name = 'Test institution'): Institution
    {
        $owner = auth()->id() ?? User::factory()->create()->id;
        return Institution::create(['created_by' => $owner, 'admin_user_id' => $owner, 'name' => $name, 'email' => str_replace(' ', '', strtolower($name)).'@example.test',
            'status' => 'active', 'license_type' => 'basic', 'max_users' => 10,
            'license_start_date' => now()->subDay(), 'license_end_date' => now()->addYear()]);
    }

    private function admin(): User
    {
        Role::findOrCreate('academy_admin', 'web');
        return User::factory()->create(['role' => 'academy_admin']);
    }

    public function test_learner_cannot_mount_any_institution_operation_component(): void
    {
        $this->actingAs(User::factory()->create());
        foreach (['InstitutionPortal','BulkEnrollment','CohortManagement','InstitutionAnalytics','LicenseManagement','PartnerInstitutions','WhitelabelSettings'] as $name) {
            Livewire::test('App\\Livewire\\Institution\\'.$name)->assertForbidden();
        }
        $this->get(route('institution.cohorts'))->assertForbidden();
    }

    public function test_school_admin_invitation_does_not_grant_a_global_role_or_verify_email(): void
    {
        Notification::fake();
        $this->actingAs($this->admin());
        $invitation = app(InstitutionService::class)->inviteInstitutionAdmin($this->institution(), [
            'email' => 'invited-school-admin@example.test', 'name' => 'Invited fixture', 'role' => 'admin',
        ]);
        $user = $invitation->invitee;
        $this->assertTrue($user->hasRole('student'));
        $this->assertFalse($user->hasAnyRole(['super_admin', 'academy_admin', 'instructor']));
        $this->assertNull($user->email_verified_at);
        $this->assertSame('admin', $invitation->role);
        $this->assertDatabaseHas('institution_users', ['user_id' => $user->id, 'status' => 'pending']);
    }

    public function test_new_institution_administration_is_membership_scoped(): void
    {
        Notification::fake();
        $this->actingAs($this->admin());
        $institution = app(InstitutionService::class)->createInstitution([
            'name' => 'Created institution', 'email' => 'created-institution@example.test', 'admin_email' => 'new-admin@example.test',
            'status' => 'active', 'license_type' => 'basic', 'max_users' => 10,
        ]);
        $this->assertFalse($institution->adminUser->hasRole('academy_admin'));
        $this->assertDatabaseHas('institution_users', ['institution_id' => $institution->id, 'user_id' => $institution->admin_user_id, 'role' => 'admin']);
    }

    public function test_guest_link_does_not_authenticate_or_accept_and_get_only_reviews_the_invitation(): void
    {
        $user = User::factory()->create();
        $invitation = InstitutionInvitation::create(['invited_by' => User::factory()->create()->id, 'institution_id' => $this->institution()->id, 'user_id' => $user->id,
            'email' => $user->email, 'name' => $user->name, 'role' => 'admin']);
        $this->get(route('institution.invitations.accept', $invitation->token))->assertRedirect(route('login'));
        $this->assertGuest();
        $this->assertSame('pending', $invitation->fresh()->status);
        $this->actingAs($user)->get(route('institution.invitations.accept', $invitation->token))->assertOk()->assertSee('Accept invitation');
        $this->assertSame('pending', $invitation->fresh()->status);
        $this->post(route('institution.invitations.confirm', $invitation->token))->assertRedirect(route('dashboard'));
        $this->assertSame('accepted', $invitation->fresh()->status);
        $this->assertFalse($user->fresh()->hasRole('academy_admin'));
        $this->post(route('institution.invitations.confirm', $invitation->token))->assertStatus(410);
    }

    public function test_another_account_cannot_accept_or_read_the_invitation(): void
    {
        $owner = User::factory()->create();
        $invitation = InstitutionInvitation::create(['invited_by' => User::factory()->create()->id, 'institution_id' => $this->institution()->id, 'user_id' => $owner->id, 'email' => $owner->email, 'role' => 'admin']);
        $this->actingAs(User::factory()->create())->get(route('institution.invitations.accept', $invitation->token))->assertForbidden();
        $this->post(route('institution.invitations.confirm', $invitation->token))->assertForbidden();
        $this->assertSame('pending', $invitation->fresh()->status);
        $this->assertDatabaseCount('institution_users', 0);
    }

    public function test_revoked_or_expired_invitations_cannot_create_membership(): void
    {
        $user = User::factory()->create();
        foreach (['revoked', 'pending'] as $status) {
            $invitation = InstitutionInvitation::create(['invited_by' => User::factory()->create()->id, 'institution_id' => $this->institution('Test '.$status)->id,
                'user_id' => $user->id, 'email' => $user->email, 'role' => 'admin', 'status' => $status,
                'expires_at' => $status === 'pending' ? now()->subMinute() : now()->addDay()]);
            $this->actingAs($user)->post(route('institution.invitations.confirm', $invitation->token))->assertStatus(410);
        }
        $this->assertDatabaseCount('institution_users', 0);
    }

    public function test_institution_membership_portal_is_scoped_and_cannot_enter_platform_operations(): void
    {
        $user = User::factory()->create();
        $mine = $this->institution('Owned institution');
        $this->institution('PRIVATE other institution');
        InstitutionUser::create(['institution_id' => $mine->id, 'user_id' => $user->id, 'role' => 'admin', 'status' => 'active']);
        Livewire::actingAs($user)->test(InstitutionPortal::class)->assertSee('Owned institution')->assertDontSee('PRIVATE other institution');
        Livewire::test(InstitutionPortal::class)->call('getRecentActivities')->assertForbidden();
        $this->get(route('institution.licenses'))->assertForbidden();
    }
    public function test_institution_enrollment_rejects_a_private_course_before_writes(): void
    {
        $this->actingAs($this->admin());
        $institution = $this->institution();
        $course = \App\Models\Learning\Course::withoutEvents(fn () => \App\Models\Learning\Course::create([
            'title' => 'Private draft', 'slug' => 'private-draft', 'description' => 'Private', 'instructor_id' => auth()->id(),
            'is_published' => false, 'is_approved' => false, 'price' => 0,
        ]));
        try {
            app(InstitutionService::class)->enrollUsersInCourses($institution, [$course->id]);
            $this->fail('Private course was accepted');
        } catch (\Illuminate\Validation\ValidationException $exception) {
            $this->assertArrayHasKey('courses', $exception->errors());
        }
        $this->assertDatabaseCount('course_enrollments', 0);
    }

}
