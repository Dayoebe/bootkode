<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        \Spatie\Permission\Models\Role::findOrCreate('student', 'web');
        \Illuminate\Support\Facades\Notification::fake();
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'terms' => '1',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertAuthenticated();
        $this->assertSame('student', auth()->user()->role);
        $this->assertTrue(auth()->user()->hasRole('student'));
        $this->assertFalse(auth()->user()->hasVerifiedEmail());
        $response->assertRedirect(route('student.dashboard', absolute: false));
    }
}
