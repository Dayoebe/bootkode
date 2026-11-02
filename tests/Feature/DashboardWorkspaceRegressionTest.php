<?php

namespace Tests\Feature;

use App\Livewire\Career\AdminMockInterview;
use App\Livewire\Career\StudentInterviewTaker;
use App\Livewire\Career\UserMockInterview;
use App\Models\Content\ResumeProfile;
use App\Models\Core\User;
use App\Models\Mentorship\InterviewQuestion;
use App\Models\Mentorship\MockInterview;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DashboardWorkspaceRegressionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        foreach (['student', 'instructor', 'super_admin'] as $role) {
            Role::findOrCreate($role, 'web');
        }
    }

    public function test_ineligible_affiliate_pages_link_to_existing_support_without_a_server_error(): void
    {
        $student = User::factory()->create(['role' => 'student']);

        $this->actingAs($student)->get(route('affiliate.apply'))
            ->assertRedirect(route('affiliate.not-eligible'));
        $this->get(route('affiliate.not-eligible'))->assertOk()
            ->assertSee(route('help.support'), false)
            ->assertSee('Contact Support');
    }

    public function test_dashboard_uses_the_saved_resume_completion_accessor(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $resume = ResumeProfile::create([
            'user_id' => $student->id,
            'full_name' => $student->name,
            'email' => $student->email,
        ]);

        $this->assertSame($resume->completion_percentage, $student->getResumeCompletionPercentage());
        $this->assertGreaterThan(0, $student->getResumeCompletionPercentage());
        $this->actingAs($student)->get(route('student.dashboard'))->assertOk();
    }

    public function test_interview_workspaces_use_existing_models_and_only_show_the_learners_records(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $other = User::factory()->create(['role' => 'student']);
        $admin = User::factory()->create(['role' => 'super_admin']);
        MockInterview::create(['user_id' => $student->id, 'title' => 'My practice interview', 'status' => 'completed', 'overall_score' => 80]);
        MockInterview::create(['user_id' => $other->id, 'title' => 'Another private interview', 'status' => 'scheduled']);
        InterviewQuestion::create([
            'created_by' => $admin->id, 'question' => 'Explain how you would review an unfamiliar task.',
            'type' => 'technical', 'difficulty_level' => 'beginner', 'answer_type' => 'text',
            'is_active' => true, 'is_approved' => true,
        ]);

        Livewire::actingAs($student)->test(UserMockInterview::class)
            ->assertSet('statistics.totalInterviews', 1)
            ->assertSet('statistics.completedInterviews', 1)
            ->assertSee('My practice interview')
            ->assertDontSee('Another private interview');
        Livewire::actingAs($admin)->test(AdminMockInterview::class)
            ->assertSet('statistics.totalInterviews', 2)
            ->assertSet('statistics.totalQuestions', 1);
    }

    public function test_interview_administration_rechecks_role_on_livewire_requests(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $this->actingAs($student)->get(route('admin.interview'))->assertForbidden();
        $admin = User::factory()->create(['role' => 'super_admin']);
        $component = Livewire::actingAs($admin)->test(AdminMockInterview::class);
        $admin->syncRoles(['student']);
        $admin->forceFill(['role' => 'student'])->save();

        $component->call('loadAvailableQuestions')->assertForbidden();
    }

    public function test_staff_interview_analytics_report_recorded_outcomes_instead_of_random_values(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);
        $student = User::factory()->create(['role' => 'student']);
        foreach ([[80, 60], [60, 30]] as [$score, $minutes]) {
            MockInterview::create(['user_id' => $student->id, 'title' => 'Completed practice', 'type' => 'technical',
                'status' => 'completed', 'overall_score' => $score, 'estimated_duration_minutes' => $minutes]);
        }
        MockInterview::create(['user_id' => $student->id, 'title' => 'Unfinished practice', 'type' => 'behavioral',
            'status' => 'scheduled', 'overall_score' => 99, 'estimated_duration_minutes' => 120]);

        $component = Livewire::actingAs($admin)->test(AdminMockInterview::class)
            ->assertSet('statistics.repeatUsers', 1)
            ->assertSet('statistics.plannedHours', 3.5)
            ->set('activeTab', 'analytics')->assertSee('Not collected')
            ->assertSee('70.0%')->assertSee('Not scored');
        $user = $component->get('topUsers')->firstWhere('id', $student->id);
        $this->assertSame(2, (int) $user->completed_interviews_count);
        $this->assertEquals(70, $user->recorded_interview_score);
        $component->set('activeTab', 'users')->assertSee('66.7%')->assertSee('70.0%');
    }

    public function test_interview_taker_refuses_another_learners_record_before_starting_it(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $owner = User::factory()->create(['role' => 'student']);
        $interview = MockInterview::create(['user_id' => $owner->id, 'title' => 'Private practice', 'status' => 'scheduled']);

        Livewire::actingAs($student)->test(StudentInterviewTaker::class, ['interview' => $interview->id])
            ->assertForbidden();
        $this->assertSame('scheduled', $interview->fresh()->status);
    }
}
