<?php

namespace App\Services\CourseGeneration;

use App\Contracts\AiProvider;
use App\Data\AiResponse;
use App\Exceptions\AiProviderException;
use App\Models\Ai\AiUsageRecord;
use App\Models\Core\User;
use Illuminate\Support\Facades\DB;
use Throwable;

/** One request budget for original generation and imported-draft expansion. */
class CourseAiRequestService
{
    public function chat(User $editor, AiProvider $provider, array $messages, int $maxTokens, ?array $schema = null): AiResponse
    {
        $limit = max(1, (int) config('bootkode_ai.course_generation_daily_requests', 12));
        $startedAt = hrtime(true);
        $usage = DB::transaction(function () use ($editor, $limit) {
            DB::table('users')->where('id', $editor->id)->lockForUpdate()->firstOrFail();
            $used = AiUsageRecord::query()
                ->where('user_id', $editor->id)
                ->where('feature', 'course_generation')
                ->where('created_at', '>=', now()->startOfDay())
                ->count();

            if ($used >= $limit) {
                throw new AiProviderException('The daily AI course-generation limit has been reached. Try again tomorrow.', 'daily_limit');
            }

            return AiUsageRecord::create([
                'user_id' => $editor->id,
                'feature' => 'course_generation',
                'provider' => (string) config('bootkode_ai.provider', 'unknown'),
                'status' => 'processing',
            ]);
        });

        try {
            $format = $schema === null ? ['type' => 'json_object'] : ['type' => 'json_schema', 'json_schema' => ['name' => 'course_stage', 'strict' => true, 'schema' => $schema]];
            $response = $provider->chat($messages, ['max_tokens' => min($maxTokens, 8192), 'temperature' => 0.3, 'response_format' => $format]);
            $usage->forceFill([
                'status' => 'succeeded',
                'prompt_tokens' => $response->promptTokens,
                'completion_tokens' => $response->completionTokens,
                'duration_ms' => (int) round((hrtime(true) - $startedAt) / 1_000_000),
            ])->save();

            return $response;
        } catch (AiProviderException $exception) {
            $usage->forceFill([
                'status' => 'failed',
                'duration_ms' => (int) round((hrtime(true) - $startedAt) / 1_000_000),
                'error_reason' => $exception->reason,
            ])->save();
            throw $exception;
        } catch (Throwable $exception) {
            $usage->forceFill([
                'status' => 'failed',
                'duration_ms' => (int) round((hrtime(true) - $startedAt) / 1_000_000),
                'error_reason' => 'unexpected_error',
            ])->save();
            \Illuminate\Support\Facades\Log::warning('Course generation failed.', ['editor_id' => $editor->id, 'exception_type' => $exception::class]);
            throw new AiProviderException('BootKode AI could not complete this request. Please try again later.', 'unexpected_error');
        }
    }
}
