<?php

namespace Tests\Feature;

use App\Livewire\Pages\Courses;
use App\Models\Core\User;
use App\Models\Learning\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PublicLearningJourneyTest extends TestCase
{
    use RefreshDatabase;

    private function course(array $attributes = []): Course
    {
        return Course::withoutEvents(fn () => Course::create(array_merge([
            'title' => 'Practical Laravel', 'slug' => 'practical-laravel',
            'instructor_id' => User::factory()->create()->id,
            'is_published' => true, 'is_approved' => true, 'price' => 12000,
            'description' => 'A practical course description.',
        ], $attributes)));
    }

    public function test_public_discovery_works_without_signup_and_excludes_private_future_courses(): void
    {
        $visible = $this->course();
        $private = $this->course(['title' => 'Private Draft Fixture', 'slug' => 'private-draft', 'is_published' => false]);
        $future = $this->course(['title' => 'Future Course Fixture', 'slug' => 'future-course', 'scheduled_publish_at' => now()->addDay()]);
        $this->get('/courses')->assertOk()->assertSee($visible->title)->assertDontSee($private->title)->assertDontSee($future->title);
        $this->get(route('courses.show', $visible->slug))->assertOk()->assertSee('₦12,000.00')->assertSee('Create a learning account');
        $this->get(route('courses.show', $private->slug))->assertNotFound();
        $this->get(route('courses.show', $future->slug))->assertNotFound();
        $this->get('/')->assertOk()->assertSee(route('courses.index'))->assertDontSee('Module 4 of 8')->assertDontSee('52%');
    }

    public function test_catalogue_search_has_an_empty_state_and_recovery_action(): void
    {
        $course = $this->course();
        Livewire::test(Courses::class)->set('search', 'no-such-fixture')
            ->assertSee('No courses found')->assertDontSee($course->title)
            ->call('resetFilters')->assertSee($course->title);
    }

    public function test_free_enrollment_creates_both_records_without_debit(): void
    {
        $course = $this->course(['price' => 0, 'is_free' => true]);
        $learner = User::factory()->create();
        Livewire::actingAs($learner)->test(\App\Livewire\StudentManagement\CourseCatalog::class)
            ->call('enroll', $course->id)->assertRedirect(route('course.view', $course->slug));
        $this->assertDatabaseHas('course_enrollments', ['course_id' => $course->id, 'user_id' => $learner->id, 'amount_paid' => 0]);
        $this->assertDatabaseHas('course_user', ['course_id' => $course->id, 'user_id' => $learner->id]);
        $this->assertDatabaseCount('wallet_transactions', 0);
    }

    public function test_learning_content_remains_protected(): void
    {
        $course = $this->course();
        $this->get(route('course.view', $course->slug))->assertRedirect(route('login'));
        $this->actingAs(User::factory()->create())->get(route('course.view', $course->slug))->assertForbidden();
    }

    public function test_public_statistics_exclude_private_names_and_unpublished_titles(): void
    {
        $course = $this->course(['title' => 'Confidential draft fixture', 'is_published' => false]);
        $course->instructor->update(['name' => 'Private learner fixture']);
        $this->get(route('statistics'))->assertOk()->assertDontSee('Private learner fixture')
            ->assertDontSee('Confidential draft fixture')->assertDontSee('10 minutes ago');
    }
}
