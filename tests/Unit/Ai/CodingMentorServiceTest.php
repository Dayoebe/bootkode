<?php

namespace Tests\Unit\Ai;

use App\Contracts\AiProvider;
use App\Data\AiResponse;
use App\Models\Ai\AiConversation;
use App\Models\Core\User;
use App\Services\Ai\AiManager;
use App\Services\Ai\CodingMentorService;
use App\Services\LearningAiService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Mockery;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class CodingMentorServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (! extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('The isolated privacy tests require pdo_sqlite; the recovered MariaDB database is intentionally not used.');
        }

        config(['database.default' => 'mentor_testing', 'database.connections.mentor_testing' => ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '']]);
        DB::purge('mentor_testing');

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('role')->default('student');
            $table->timestamps();
        });
        Schema::create('ai_conversations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('course_id')->nullable();
            $table->string('title');
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();
        });
        Schema::create('ai_conversation_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('conversation_id');
            $table->string('role');
            $table->text('content');
            $table->json('context')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
        Schema::create('ai_usage_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('feature');
            $table->string('provider');
            $table->string('status');
            $table->unsignedInteger('prompt_tokens')->nullable();
            $table->unsignedInteger('completion_tokens')->nullable();
            $table->unsignedInteger('duration_ms')->nullable();
            $table->string('error_reason')->nullable();
            $table->timestamps();
        });
    }

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
        DB::table('users')->insert([
            ['name' => 'Owner', 'email' => 'owner@example.test', 'role' => 'student', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Other', 'email' => 'other@example.test', 'role' => 'student', 'created_at' => now(), 'updated_at' => now()],
        ]);

        return User::query()->orderBy('id')->get()->all();
    }

    private function service(): CodingMentorService
    {
        return new CodingMentorService(Mockery::mock(AiManager::class), Mockery::mock(LearningAiService::class));
    }
}
