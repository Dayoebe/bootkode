<?php

namespace Tests\Feature;

use App\Livewire\UserManagement\Profile;
use App\Models\Core\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $this->actingAs(User::factory()->create())->get(route('profile.view'))
            ->assertOk()
            ->assertSee('data-profile-surface="modern"', false)
            ->assertSee('Account centre')
            ->assertSee('Profile readiness')
            ->assertSee('id="profile-panel-overview"', false)
            ->assertSee('id="profile-panel-account"', false);
    }

    public function test_edit_profile_route_opens_the_rebuilt_editing_workspace(): void
    {
        $this->actingAs(User::factory()->create())->followingRedirects()->get(route('profile.edit'))
            ->assertOk()
            ->assertSee('Edit profile')
            ->assertSee('id="profile-panel-identity"', false)
            ->assertSee('id="profile-panel-details"', false)
            ->assertSee('id="profile-panel-links"', false)
            ->assertDontSee('id="profile-panel-overview"', false);
    }

    public function test_profile_information_can_be_updated_and_changed_email_requires_verification(): void
    {
        Notification::fake();
        $user = User::factory()->create();
        Livewire::actingAs($user)->test(Profile::class)->call('toggleEditMode')
            ->set('name', 'Test User')->set('email', 'updated@example.com')
            ->call('updateProfile')->assertHasNoErrors();
        $this->assertSame('Test User', $user->fresh()->name);
        $this->assertSame('updated@example.com', $user->fresh()->email);
        $this->assertNull($user->fresh()->email_verified_at);
        Notification::assertSentTo($user, \App\Notifications\CustomVerifyEmail::class);
    }

    public function test_email_verification_is_unchanged_when_email_is_unchanged(): void
    {
        $user = User::factory()->create();
        Livewire::actingAs($user)->test(Profile::class)->call('toggleEditMode')
            ->set('name', 'Changed name')->call('updateProfile')->assertHasNoErrors();
        $this->assertSame('Changed name', $user->fresh()->name);
        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    public function test_user_can_delete_their_account_with_password_and_explicit_confirmation(): void
    {
        $user = User::factory()->create();
        Livewire::actingAs($user)->test(Profile::class)
            ->set('delete_password', 'password')->set('delete_confirmation', 'DELETE')
            ->call('deleteAccount')->assertHasNoErrors()->assertRedirect('/');
        $this->assertGuest();
        $this->assertNull($user->fresh());
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = User::factory()->create();
        Livewire::actingAs($user)->test(Profile::class)
            ->set('delete_password', 'wrong-password')->set('delete_confirmation', 'DELETE')
            ->call('deleteAccount')->assertHasErrors('delete_password');
        $this->assertNotNull($user->fresh());
    }

    public function test_blocked_account_deletion_preserves_authentication_avatar_and_learning_history(): void
    {
        $user = User::factory()->create(['profile_picture' => 'https://example.test/avatar.png']);
        $course = \App\Models\Learning\Course::withoutEvents(fn () => \App\Models\Learning\Course::create(['instructor_id' => User::factory()->create()->id, 'title' => 'History fixture', 'slug' => 'history-fixture']));
        $user->courses()->attach($course->id, ['progress' => 10]);
        Livewire::actingAs($user)->test(Profile::class)
            ->set('delete_password', 'password')->set('delete_confirmation', 'DELETE')
            ->call('deleteAccount')->assertHasErrors('delete_confirmation');
        $this->assertAuthenticatedAs($user);
        $this->assertSame('https://example.test/avatar.png', $user->fresh()->profile_picture);
        $this->assertDatabaseHas('course_user', ['user_id' => $user->id, 'course_id' => $course->id]);
    }

    public function test_profile_update_rejects_another_users_email(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        Livewire::actingAs($user)->test(Profile::class)->call('toggleEditMode')
            ->set('email', $other->email)->call('updateProfile')->assertHasErrors('email');
        $this->assertNotSame($other->email, $user->fresh()->email);
    }

    public function test_profile_visibility_rejects_unrecognised_values(): void
    {
        $user = User::factory()->create(['profile_visibility' => 'private']);

        Livewire::actingAs($user)->test(Profile::class)
            ->set('profileVisibility', 'everyone')
            ->call('updateProfileVisibility')
            ->assertHasErrors('profileVisibility');

        $this->assertSame('private', $user->fresh()->profile_visibility);
    }

    public function test_signing_out_other_sessions_requires_the_current_password(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)->test(Profile::class)
            ->set('current_password', 'incorrect-password')
            ->call('logoutOtherSessions')
            ->assertHasErrors('current_password');
    }

    public function test_profile_mutations_rebind_to_the_authenticated_user(): void
    {
        $user = User::factory()->create(['receive_course_updates' => true]);
        $other = User::factory()->create(['receive_course_updates' => true]);

        Livewire::actingAs($user)->test(Profile::class)
            ->set('user', $other)
            ->set('emailNotifications', false)
            ->call('updateEmailNotifications')
            ->assertHasNoErrors();

        $this->assertFalse($user->fresh()->receive_course_updates);
        $this->assertTrue($other->fresh()->receive_course_updates);
    }
}
