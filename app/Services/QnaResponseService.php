<?php

namespace App\Services;

use App\Models\Assessment\Question;
use Illuminate\Support\Arr;

class QnaResponseService
{
    public const TOPICS = ['discussion', 'debate', 'peer_review', 'reflection', 'case_study'];

    /** Old attempts keep their written-response contract; sharing is never inferred. */
    public function definition(Question $question): ?array
    {
        $criteria = app(StructuredQuestionService::class)->array($question->options);
        if (($criteria['version'] ?? null) !== 1) return null;
        validator($criteria, [
            'topic_type' => 'required|in:'.implode(',', self::TOPICS),
            'min_responses' => 'required|integer|between:1,10',
            'min_response_length' => 'required|integer|between:10,1000',
            'allow_peer_review' => 'required|boolean', 'moderator_approval' => 'required|boolean',
            'discussion_prompts' => 'present|array|max:10', 'discussion_prompts.*' => 'required|string|max:1000',
            'evaluation_criteria' => 'required|array|min:1|max:20',
            'evaluation_criteria.*' => 'array:name,weight,description',
            'evaluation_criteria.*.name' => 'required|string|max:100',
            'evaluation_criteria.*.weight' => 'required|integer|between:1,100',
            'evaluation_criteria.*.description' => 'nullable|string|max:1000',
        ])->validate();
        if (array_sum(array_column($criteria['evaluation_criteria'], 'weight')) !== 100) {
            throw \Illuminate\Validation\ValidationException::withMessages(['evaluationCriteria' => 'Evaluation weights must total 100%.']);
        }
        return array_merge(Arr::only($criteria, ['version', 'topic_type', 'min_responses', 'min_response_length', 'allow_peer_review', 'moderator_approval', 'discussion_prompts']),
            ['topic_title' => strip_tags($question->question_text), 'required' => (bool) $question->is_required, 'max_responses' => 10, 'max_response_length' => 4000, 'max_total_length' => 10000]);
    }

    public function validate(array $rules, mixed $value, bool $final = false, bool $expired = false): void
    {
        if ($value === null || $value === '' || $value === []) {
            abort_if($final && !$expired && $rules['required'], 422, 'Complete the required Q&A responses before submitting.');
            return;
        }
        abort_unless(is_array($value) && array_is_list($value) && count($value) <= $rules['max_responses'], 422, 'Use the recorded Q&A response fields.');
        foreach ($value as $text) abort_unless(is_string($text) && mb_strlen($text) <= $rules['max_response_length'], 422, 'Each Q&A response may contain up to '.$rules['max_response_length'].' characters.');
        abort_unless(array_sum(array_map('mb_strlen', $value)) <= $rules['max_total_length'], 422, 'Q&A responses may contain up to 10,000 characters in total.');
        if (!$final || $expired) return;
        $provided = array_values(array_filter(array_map('trim', $value), fn ($text) => $text !== ''));
        if (!$provided && !$rules['required']) return;
        abort_unless(count($provided) >= $rules['min_responses'], 422, 'Write at least '.$rules['min_responses'].' Q&A responses.');
        foreach ($provided as $text) abort_unless(mb_strlen($text) >= $rules['min_response_length'], 422, 'Each completed Q&A response needs at least '.$rules['min_response_length'].' characters.');
        abort_unless(count(array_unique($provided)) === count($provided), 422, 'Write distinct Q&A responses.');
    }

    public function display(?string $value, ?array $rules): string
    {
        if (!$rules) return $value ?? 'No answer submitted.';
        $responses = json_decode($value ?? '', true);
        if (!is_array($responses)) return 'No answer submitted.';
        return collect($responses)->filter(fn ($text) => is_string($text) && trim($text) !== '')
            ->values()->map(fn ($text, $index) => 'Response '.($index + 1).":\n".$text)->implode("\n\n") ?: 'No answer submitted.';
    }
}
