<?php

namespace Tests\Feature;

use App\Livewire\Dashboard\AffiliateAmbassadorDashboard;
use App\Livewire\Dashboard\ContentEditorDashboard;
use App\Livewire\Dashboard\InstructorDashboard;
use App\Livewire\Dashboard\MentorDashboard;
use App\Livewire\Dashboard\SuperAdminDashboard;
use App\Models\Core\User;
use App\Models\Learning\Course;
use App\Models\Mentorship\Mentorship;
use App\Models\Mentorship\MentorshipSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RoleDashboardHomepageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['student', 'instructor', 'mentor', 'content_editor', 'affiliate_ambassador', 'academy_admin', 'super_admin'] as $role) {
            Role::findOrCreate($role, 'web');
        }
    }

    public function test_each_role_homepage_uses_the_shared_home_hero_and_recorded_chart_surface(): void
    {
        $dashboards = [
            'student' => ['student.dashboard', 'Keep building momentum'],
            'instructor' => ['instructor.dashboard', 'Teach with clarity'],
            'mentor' => ['mentor.dashboard', 'Guide progress that matters'],
            'content_editor' => ['content_editor.dashboard', 'Make every page useful'],
            'affiliate_ambassador' => ['affiliate_ambassador.dashboard', 'Grow access with every referral'],
            'academy_admin' => ['academy_admin.dashboard', 'Run the academy from one clear view'],
            'super_admin' => ['super_admin.dashboard', 'See the platform. Prioritise what matters'],
        ];

        foreach ($dashboards as $role => [$route, $headline]) {
            $user = User::factory()->create(['role' => $role]);
            $user->assignRole($role);

            $response = $this->actingAs($user)->get(route($route))->assertOk();
            $response->assertSee('class="bk-dashboard-hero"', false)
                ->assertSee($headline)
                ->assertSee('<canvas', false);
            $this->assertSame(1, substr_count($response->getContent(), '<h1'), $role.' dashboard must own one primary heading.');
        }
    }

    public function test_dashboard_sidebar_and_hero_share_the_brand_and_saved_theme_palette(): void
    {
        $tokens = file_get_contents(resource_path('css/tokens.css'));
        $themes = file_get_contents(resource_path('css/app.css'));
        $dashboard = file_get_contents(resource_path('css/dashboard.css'));

        $this->assertStringContainsString('--bk-brand-navy: 7 21 45;', $tokens);
        $this->assertStringContainsString('--bk-brand-mint: 143 227 136;', $tokens);
        $this->assertStringContainsString('--bk-sidebar-bg: var(--bk-brand-navy);', $tokens);
        $this->assertStringContainsString('--bk-sidebar-accent: var(--bk-brand-mint);', $tokens);

        foreach (['.dark', '.sepia', '.ocean', '.forest'] as $theme) {
            $this->assertMatchesRegularExpression(
                '/'.preg_quote($theme, '/').'\s*\{[^}]*--bk-sidebar-bg:[^;]+;[^}]*--bk-sidebar-accent:[^;]+;/s',
                $themes,
                $theme.' must define coordinated dashboard chrome colours.'
            );
        }

        $this->assertGreaterThanOrEqual(2, substr_count($dashboard, 'background: rgb(var(--bk-sidebar-bg));'));
        $this->assertStringContainsString('.bk-dashboard-hero-primary { background: rgb(var(--bk-sidebar-accent));', $dashboard);
    }

    public function test_mentor_dashboard_only_uses_the_authenticated_mentors_relationships(): void
    {
        $mentor = $this->roleUser('mentor', 'Assigned Mentor');
        $otherMentor = $this->roleUser('mentor', 'Other Mentor');
        $assigned = $this->roleUser('student', 'Assigned Learner');
        $unrelated = $this->roleUser('student', 'Unrelated Learner');

        $relationship = Mentorship::create([
            'mentor_id' => $mentor->id,
            'mentee_id' => $assigned->id,
            'status' => Mentorship::STATUS_ACTIVE,
            'accepted_at' => now()->subDays(10),
            'started_at' => now()->subDays(10),
            'goals' => ['Ship a project'],
        ]);
        Mentorship::create([
            'mentor_id' => $otherMentor->id,
            'mentee_id' => $unrelated->id,
            'status' => Mentorship::STATUS_ACTIVE,
            'accepted_at' => now()->subDays(5),
        ]);
        MentorshipSession::create([
            'mentorship_id' => $relationship->id,
            'title' => 'Project review',
            'type' => MentorshipSession::TYPE_PROJECT_GUIDANCE,
            'status' => MentorshipSession::STATUS_COMPLETED,
            'scheduled_at' => now()->subDay(),
            'started_at' => now()->subDay()->subHour(),
            'ended_at' => now()->subDay(),
            'duration_minutes' => 60,
        ]);

        Livewire::actingAs($mentor)->test(MentorDashboard::class)
            ->call('updateMenteeFilter', 'all')
            ->assertSee('Assigned Learner')
            ->assertDontSee('Unrelated Learner')
            ->assertSee('Project review')
            ->assertSet('selectedMenteeFilter', 'all');
    }

    public function test_dashboard_filters_reject_unrecognised_values_and_foreign_course_ids(): void
    {
        $mentor = $this->roleUser('mentor');
        $mentorComponent = Livewire::actingAs($mentor)->test(MentorDashboard::class);
        $mentorComponent
            ->call('updateTimeframe', 'all-time')
            ->assertHasErrors('timeframe');

        $mentor->forceFill(['role' => 'student'])->save();
        $mentor->syncRoles(['student']);
        $mentorComponent->call('updateTimeframe', '7days')->assertForbidden();

        $instructor = $this->roleUser('instructor');
        $otherInstructor = $this->roleUser('instructor');
        $foreignCourse = Course::withoutEvents(fn () => Course::create([
            'instructor_id' => $otherInstructor->id,
            'title' => 'Foreign dashboard course',
            'slug' => 'foreign-dashboard-course',
        ]));
        Livewire::actingAs($instructor)->test(InstructorDashboard::class)
            ->call('updateCourseFilter', (string) $foreignCourse->id)
            ->assertStatus(422);
    }

    public function test_unrecorded_dashboard_signals_are_labelled_instead_of_fabricated(): void
    {
        $editor = $this->roleUser('content_editor');
        Livewire::actingAs($editor)->test(ContentEditorDashboard::class)
            ->assertSee('Audience timing is not recorded yet.')
            ->assertDontSee('Tuesday 10 AM')
            ->assertDontSee('65%')
            ->assertDontSee('3x more engagement');

        $affiliate = $this->roleUser('affiliate_ambassador');
        Livewire::actingAs($affiliate)->test(AffiliateAmbassadorDashboard::class)
            ->assertSee('Click analytics')
            ->assertSee('Not connected')
            ->assertSee('No click or conversion estimate shown');

        $admin = $this->roleUser('super_admin');
        Livewire::actingAs($admin)->test(SuperAdminDashboard::class)
            ->assertSee('No published status')
            ->assertDontSee('2.3 GB')
            ->assertDontSee('All systems operational');
    }

    private function roleUser(string $role, ?string $name = null): User
    {
        $user = User::factory()->create(array_filter(['role' => $role, 'name' => $name]));
        $user->assignRole($role);

        return $user;
    }
}
