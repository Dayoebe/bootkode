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
        $this->actingAs(User::factory()->create())->get(route('profile.view'))->assertOk();
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

    public function test_profile_update_rejects_another_users_email(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        Livewire::actingAs($user)->test(Profile::class)->call('toggleEditMode')
            ->set('email', $other->email)->call('updateProfile')->assertHasErrors('email');
        $this->assertNotSame($other->email, $user->fresh()->email);
    }
}
