<?php

namespace Tests\Feature;

use App\Models\Content\Page;
use App\Models\Core\User;
use App\Models\Mentorship\MockInterview;
use App\Services\Career\MockInterviewCompletionService;
use App\Services\Content\PageAnalyticsProjection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class EvidenceBasedReportingTest extends TestCase
{
    use RefreshDatabase;

    public function test_page_analytics_projection_exposes_only_persisted_values(): void
    {
        $author = User::factory()->create();
        $this->actingAs($author);
        $page = Page::create([
            'title' => 'Recorded analytics fixture',
            'slug' => 'recorded-analytics-fixture',
            'content' => 'Evidence-based content.',
            'status' => Page::STATUS_PUBLISHED,
        ]);
        $page->forceFill(['view_count' => 17])->saveQuietly();

        $projection = app(PageAnalyticsProjection::class);
        $summary = $projection->summary();
        $topPage = $projection->topPages()->first();
        $activity = $projection->recentActivity(30)->first();

        $this->assertSame(17, $summary['total_views']);
        $this->assertFalse($summary['detailed_analytics_available']);
        $this->assertSame(17, $topPage['views']);
        $this->assertSame($author->name, $activity['user_name']);
        $this->assertArrayNotHasKey('unique_views', $topPage);
        $this->assertArrayNotHasKey('revenue', $summary);
        $this->assertStringNotContainsString('rand(', file_get_contents(app_path('Livewire/Pages/Partials/Analytics.php')));
    }

    public function test_interview_completion_records_participation_without_inventing_scores(): void
    {
        $user = User::factory()->create();
        $interview = MockInterview::create([
            'user_id' => $user->id,
            'title' => 'Evidence-based interview',
            'slug' => 'evidence-based-interview-'.Str::lower(Str::random(6)),
            'type' => MockInterview::TYPE_TECHNICAL,
            'format' => MockInterview::FORMAT_TEXT,
            'status' => MockInterview::STATUS_IN_PROGRESS,
            'difficulty_level' => MockInterview::DIFFICULTY_BEGINNER,
            'questions' => [['id' => 1], ['id' => 2]],
        ]);

        $completed = app(MockInterviewCompletionService::class)->completeForUser($interview, $user->id, [
            ['question_id' => 1, 'answer' => 'A real answer', 'response_time' => 30],
            ['question_id' => 2, 'answer' => '', 'response_time' => 60],
        ]);

        $this->assertSame(MockInterview::STATUS_COMPLETED, $completed->status);
        $this->assertSame('50.00', $completed->completion_rate);
        $this->assertSame('45.00', $completed->avg_response_time);
        $this->assertNull($completed->overall_score);
        $this->assertNull($completed->technical_score);
        $this->assertNull($completed->communication_score);
        $this->assertNull($completed->confidence_score);
        $this->assertNull($completed->problem_solving_score);
        $this->assertNull($completed->cultural_fit_score);
        $this->assertStringNotContainsString('rand(', file_get_contents(app_path('Livewire/Career/StudentInterviewTaker.php')));
        $this->assertStringNotContainsString('rand(', file_get_contents(app_path('Livewire/Career/UserMockInterview.php')));
    }

    public function test_user_cannot_complete_another_users_interview(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $interview = MockInterview::create([
            'user_id' => $owner->id,
            'title' => 'Private interview',
            'slug' => 'private-interview-'.Str::lower(Str::random(6)),
            'questions' => [['id' => 1]],
        ]);

        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        app(MockInterviewCompletionService::class)->completeForUser($interview, $otherUser->id, []);
    }
}
