<?php

namespace Tests\Unit\Ai;

use App\Contracts\AiProvider;
use App\Data\AiResponse;
use App\Models\Ai\AiConversation;
use App\Models\Core\User;
use App\Services\Ai\AiManager;
use App\Services\Ai\CodingMentorService;
use App\Services\LearningAiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class CodingMentorServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_one_user_cannot_delete_another_users_conversation(): void
    {
        [$owner, $other] = $this->users();
        $conversation = AiConversation::create(['user_id' => $owner->id, 'title' => 'Private']);

        $this->expectException(HttpException::class);
        $this->service()->delete($other, $conversation);
    }

    public function test_successful_reply_persists_both_messages_and_usage(): void
    {
        [$user] = $this->users();
        $conversation = AiConversation::create(['user_id' => $user->id, 'title' => 'New conversation']);
        $provider = Mockery::mock(AiProvider::class);
        $provider->shouldReceive('chat')->once()->andReturn(new AiResponse('Work from the smallest failing example.', 20, 10));
        $manager = Mockery::mock(AiManager::class);
        $manager->shouldReceive('provider')->once()->andReturn($provider);
        $learning = Mockery::mock(LearningAiService::class);
        $learning->shouldReceive('tutorContext')->once()->andReturn([]);

        (new CodingMentorService($manager, $learning))->send($user, $conversation, 'Why does this function fail?');

        $this->assertSame(2, $conversation->messages()->count());
        $this->assertDatabaseHas('ai_usage_records', ['user_id' => $user->id, 'feature' => 'coding_mentor', 'status' => 'succeeded']);
    }

    private function users(): array
    {
        return User::factory()->count(2)->create()->all();
    }

    private function service(): CodingMentorService
    {
        return new CodingMentorService(Mockery::mock(AiManager::class), Mockery::mock(LearningAiService::class));
    }
}
