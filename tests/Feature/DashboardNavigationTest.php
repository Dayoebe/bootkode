<?php

namespace Tests\Feature;

use App\Models\Core\User;
use App\View\Components\MobileBottomNav;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DashboardNavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_every_role_receives_existing_shortcuts_for_its_workspace(): void
    {
        $expected = [
            'student' => 'student.enrolled-courses',
            'instructor' => 'my-course',
            'mentor' => 'mentorship.dashboard',
            'content_editor' => 'my-course',
            'affiliate_ambassador' => 'affiliate.dashboard',
            'academy_admin' => 'all-users',
            'super_admin' => 'all-users',
        ];
        foreach ($expected as $role => $destination) {
            Role::findOrCreate($role, 'web');
            $user = User::factory()->create(['role' => $role]);
            $user->assignRole($role);
            $items = app(MobileBottomNav::class)->items($user);
            $this->assertSame($destination, $items[1]['route']);
            foreach ($items as $item) {
                $this->assertTrue(Route::has($item['route']), $role.': '.$item['route']);
                $this->assertNotEmpty(route($item['route']));
            }
        }
    }

    public function test_primary_administrative_role_takes_precedence_over_secondary_learner_role(): void
    {
        foreach (['student', 'academy_admin'] as $role) {
            Role::findOrCreate($role, 'web');
        }
        $user = User::factory()->create(['role' => 'academy_admin']);
        $user->syncRoles(['student', 'academy_admin']);
        $this->assertSame('all-users', app(MobileBottomNav::class)->items($user)[1]['route']);
    }

    public function test_shell_renders_without_duplicate_primary_headings_and_keeps_learner_boundaries(): void
    {
        Role::findOrCreate('student', 'web');
        $user = User::factory()->create(['role' => 'student']);
        $user->assignRole('student');
        $response = $this->actingAs($user)->get(route('profile.view'))->assertOk();
        $response->assertSee('Workspace navigation')->assertSee('Mobile dashboard navigation');
        $this->assertSame(1, substr_count($response->getContent(), '<h1'));
        $this->get(route('all-users'))->assertForbidden();
        \Livewire\Livewire::actingAs($user)->test(\App\Livewire\UserManagement\AllUser::class)->assertForbidden();
    }

    public function test_user_directory_rechecks_access_after_an_administrators_role_is_revoked(): void
    {
        Role::findOrCreate('academy_admin', 'web');
        Role::findOrCreate('student', 'web');
        $user = User::factory()->create(['role' => 'academy_admin']);
        $user->assignRole('academy_admin');
        $component = \Livewire\Livewire::actingAs($user)->test(\App\Livewire\UserManagement\AllUser::class)->assertOk();
        $user->update(['role' => 'student']);
        $user->syncRoles(['student']);
        $component->call('getRoleStats')->assertForbidden();
    }
}
