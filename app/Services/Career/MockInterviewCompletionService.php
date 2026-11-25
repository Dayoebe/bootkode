<?php

namespace App\Services\Career;

use App\Models\Mentorship\MockInterview;
use Illuminate\Support\Facades\DB;

final class MockInterviewCompletionService
{
    /**
     * Complete an owned interview using factual participation measures only.
     * Scores remain null until a reviewer or a validated rubric supplies them.
     *
     * @param array<int|string, array<string, mixed>> $responses
     */
    public function completeForUser(MockInterview $interview, int $userId, array $responses): MockInterview
    {
        return DB::transaction(function () use ($interview, $userId, $responses): MockInterview {
            $ownedInterview = MockInterview::query()
                ->whereKey($interview->getKey())
                ->where('user_id', $userId)
                ->lockForUpdate()
                ->firstOrFail();

            $questionCount = count($ownedInterview->questions ?? []);
            $answeredCount = collect($responses)
                ->filter(fn ($response): bool => is_array($response) && filled($response['answer'] ?? null))
                ->count();
            $responseTimes = collect($responses)
                ->pluck('response_time')
                ->filter(fn ($seconds): bool => is_numeric($seconds) && (float) $seconds >= 0);

            $ownedInterview->complete($responses, [
                'overall_score' => null,
                'technical_score' => null,
                'communication_score' => null,
                'confidence_score' => null,
                'problem_solving_score' => null,
                'cultural_fit_score' => null,
                'eye_contact_score' => null,
                'body_language_score' => null,
                'completion_rate' => $questionCount > 0
                    ? min(100, round(($answeredCount / $questionCount) * 100, 2))
                    : 0,
                'avg_response_time' => $responseTimes->isNotEmpty()
                    ? round((float) $responseTimes->average(), 2)
                    : null,
            ]);

            return $ownedInterview->refresh();
        });
    }
}
