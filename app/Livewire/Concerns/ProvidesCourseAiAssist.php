<?php

namespace App\Livewire\Concerns;

trait ProvidesCourseAiAssist
{
    protected function aiAssistFormName(): string
    {
        return 'course details';
    }

    protected function aiAssistFields(): array
    {
        return [
            'title' => ['label' => 'Course title', 'purpose' => 'A clear compelling course title', 'max' => 255, 'actions' => ['generate', 'suggest', 'improve', 'seo'], 'form' => true],
            'subtitle' => ['label' => 'Course subtitle', 'purpose' => 'A concise supporting promise', 'max' => 255, 'actions' => ['generate', 'improve', 'shorten'], 'form' => true],
            'slug' => ['label' => 'Course URL slug', 'purpose' => 'A short URL-safe phrase', 'max' => 255, 'actions' => ['generate', 'seo'], 'format' => 'slug'],
            'description' => ['label' => 'Course description', 'purpose' => 'A clear overview without unverified claims', 'max' => 5000, 'actions' => ['generate', 'improve', 'rewrite', 'expand', 'shorten', 'grammar', 'professional'], 'form' => true],
            'target_audience' => ['label' => 'Target audience', 'purpose' => 'The intended learners and relevant starting point', 'max' => 500, 'actions' => ['generate', 'improve', 'shorten'], 'form' => true],
            'learning_outcomes' => ['label' => 'Learning outcomes', 'purpose' => 'Observable outcomes beginning with action verbs', 'type' => 'list', 'max_items' => 10, 'item_max' => 500, 'actions' => ['generate', 'improve', 'rewrite'], 'form' => true],
            'prerequisites' => ['label' => 'Prerequisites', 'purpose' => 'Honest learner prerequisites; say none when appropriate', 'type' => 'list', 'max_items' => 10, 'item_max' => 1000, 'actions' => ['generate', 'improve', 'rewrite'], 'form' => true],
            'syllabus_overview' => ['label' => 'Syllabus overview', 'purpose' => 'A high-level proposed module structure for human review', 'max' => 1000, 'actions' => ['generate', 'improve', 'rewrite', 'expand', 'shorten'], 'form' => true],
            'materials_included' => ['label' => 'Materials included', 'purpose' => 'Only materials supported by the supplied brief; otherwise mark as proposed', 'type' => 'list', 'max_items' => 10, 'item_max' => 255, 'actions' => ['generate', 'improve'], 'form' => true],
        ];
    }

    protected function aiAssistContext(): array
    {
        $category = collect($this->categories ?? [])->firstWhere('id', (int) $this->category_id);

        return [
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'category' => data_get($category, 'name'),
            'difficulty level' => $this->difficulty_level,
            'target audience' => $this->target_audience,
            'description' => $this->description,
            'learning outcomes' => $this->learning_outcomes,
            'prerequisites' => $this->prerequisites,
            'syllabus overview' => $this->syllabus_overview,
        ];
    }
}
