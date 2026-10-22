<?php

namespace App\Services;

use App\Models\Assessment\Question;

class StructuredQuestionService
{
    public const TYPES = ['matching', 'ordering', 'drag_drop'];

    public function array($value): array
    {
        for ($depth = 0; $depth < 2 && is_string($value); $depth++) $value = json_decode($value, true);
        return is_array($value) ? $value : [];
    }

    /** Only the mapping belongs in private grading metadata. */
    public function project(Question $question): array
    {
        $options = $this->array($question->options);
        $items = $options[$question->question_type === 'ordering' ? 'items' : 'choices'] ?? [];
        $prompts = $question->question_type === 'ordering' ? [] : ($options['prompts'] ?? []);
        $correct = $this->array($question->correct_answers);
        abort_unless(($options['version'] ?? null) === 1 && is_array($items) && array_is_list($items) && count($items) >= 2 && count($items) <= 12, 422, 'Your instructor must configure this structured question before it can start.');
        abort_unless($question->question_type === 'ordering' || (is_array($prompts) && array_is_list($prompts) && count($prompts) === count($items)), 422, 'The matching targets need instructor setup.');
        foreach (array_merge($items, $prompts) as $text) abort_unless(is_string($text) && trim($text) !== '' && mb_strlen($text) <= 1000, 422, 'The question items need instructor setup.');
        $expected = range(0, count($items) - 1);
        $keys = $correct;
        foreach ($keys as $key) abort_unless(is_int($key) || (is_string($key) && ctype_digit($key)), 422, 'The marking key needs instructor setup.');
        $keys = array_map('intval', $keys); sort($keys);
        abort_unless(array_is_list($correct) && $keys === $expected, 422, 'The marking key needs instructor setup.');
        $mapping = collect($expected)->shuffle()->values()->all();
        return ['options' => array_map(fn ($index) => $items[$index], $mapping), 'prompts' => $prompts, 'mapping' => $mapping, 'format_version' => 1];
    }

    public function validate(array $question, $value): void
    {
        if ($value === null || $value === '' || $value === []) return;
        $size = count($question['options']);
        abort_unless(is_array($value) && count($value) <= $size, 422, 'Choose valid question items.');
        foreach ($value as $key => $index) {
            abort_unless(filter_var($key, FILTER_VALIDATE_INT) !== false && (int) $key >= 0 && (int) $key < $size, 422, 'Choose a valid target.');
            if ($index === null || $index === '') continue;
            abort_unless((is_int($index) || is_string($index)) && filter_var($index, FILTER_VALIDATE_INT) !== false && (int) $index >= 0 && (int) $index < $size, 422, 'Choose a valid item.');
        }
        if ($question['question_type'] === 'ordering') {
            $indices = array_map('intval', $value); sort($indices);
            abort_unless(array_is_list($value) && $indices === range(0, $size - 1) && !in_array(null, $value, true) && !in_array('', $value, true), 422, 'Arrange every item exactly once.');
        }
    }

    public function mapAnswer(array $question, array $grading, array $value): array
    {
        $this->validate($question, $value);
        $result = [];
        foreach (range(0, count($question['options']) - 1) as $position) {
            $index = $value[$position] ?? null;
            $result[] = $index === null || $index === '' ? null : $grading['option_map'][(int) $index];
        }
        return $result;
    }

    public function grade($answer, array $correct): bool
    {
        $values = $this->array($answer);
        if (count($values) !== count($correct) || !array_is_list($values)) return false;
        foreach ($values as $value) if (!is_int($value)) return false;
        return $values === array_map('intval', $correct);
    }

    public function response(array $question, array $grading, $answer): string
    {
        $values = $this->array($answer);
        $mapping = array_map('intval', $grading['option_map'] ?? []);
        $lines = [];
        foreach ($values as $position => $index) {
            $display = $index === null ? false : array_search((int) $index, $mapping, true);
            $text = $display !== false ? strip_tags((string) ($question['options'][$display] ?? 'Recorded item unavailable')) : 'No selection';
            $label = $question['question_type'] === 'ordering' ? ($position + 1).'. ' : strip_tags((string) ($question['prompts'][$position] ?? 'Target')).': ';
            $lines[] = $label.$text;
        }
        return implode("\n", $lines);
    }
}
