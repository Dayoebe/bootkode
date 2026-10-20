<?php

namespace Tests\Feature;

use App\Livewire\Cbt\CbtManagement;
use App\Livewire\Financial\Admin\PaystackSettings;
use App\Models\Core\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AccessBoundariesTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_register_a_privileged_role(): void
    {
        foreach (array_diff(User::getRoles(), [User::ROLE_STUDENT]) as $role) {
            $this->post('/register', [
                'name' => 'Learner', 'email' => 'learner@example.com',
                'password' => 'password', 'password_confirmation' => 'password', 'role' => $role,
            ])->assertSessionHasErrors('role');
        }
        $this->assertDatabaseCount('users', 0);
        $this->assertGuest();
    }

    public function test_learner_cannot_open_financial_admin_routes(): void
    {
        $this->actingAs(User::factory()->create());
        foreach (['/admin/financial', '/admin/paystack/settings', '/admin/payments', '/admin/revenue/reports'] as $uri) {
            $this->get($uri)->assertForbidden();
        }
    }

    public function test_sensitive_livewire_components_recheck_authorization(): void
    {
        $this->actingAs(User::factory()->create());
        Livewire::test(PaystackSettings::class)->assertForbidden();
        Livewire::test(CbtManagement::class)->assertForbidden();
    }

    public function test_cbt_routes_require_a_login(): void
    {
        $this->get('/cbt/management')->assertRedirect(route('login'));
        $this->get('/cbt/results')->assertRedirect(route('login'));
    }

    public function test_admin_configuration_never_serializes_payment_secrets(): void
    {
        $user = User::factory()->create();
        \Spatie\Permission\Models\Role::findOrCreate(User::ROLE_SUPER_ADMIN, 'web');
        $user->assignRole(User::ROLE_SUPER_ADMIN);
        config(['services.paystack.secret_key' => 'sk_test_private_fixture']);
        Livewire::actingAs($user)->test(PaystackSettings::class)
            ->assertSee('Payment configuration')->assertDontSee('sk_test_private_fixture');
    }
}
