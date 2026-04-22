<?php

namespace App\Services\CourseGeneration;

use Illuminate\Support\Str;

class CourseGenerationBlueprintBuilder
{
    protected const SECTION_TEMPLATES = [
        'generic' => [
            [
                'title' => 'Foundations of %s',
                'description' => 'Build the baseline understanding, vocabulary, and working rhythm required to approach %s with confidence and consistency.',
                'lessons' => [
                    'Understanding the %s Landscape',
                    'Tools, Terminology, and Setup for %s',
                    'Core Principles That Shape Great %s Work',
                    'Planning Your First %s Deliverable',
                    'Guided Practice: A Small %s Win',
                ],
            ],
            [
                'title' => 'Building a Repeatable %s Workflow',
                'description' => 'Turn the topic into a practical workflow so learners can move from intent to execution without guesswork.',
                'lessons' => [
                    'Mapping a Reliable %s Workflow',
                    'Breaking %s into Repeatable Steps',
                    'Choosing the Right Approach for Different Scenarios',
                    'Practice: Moving from Brief to Execution',
                    'Reviewing and Improving the Process',
                ],
            ],
            [
                'title' => 'Applied Techniques for %s',
                'description' => 'Focus on the most useful techniques, habits, and quality checks that make %s work production-ready.',
                'lessons' => [
                    'Essential Techniques for Better %s Results',
                    'Making Strong Decisions Under Real Constraints',
                    'Troubleshooting Common %s Problems',
                    'Practice: Refining a Realistic %s Task',
                    'Video Walkthrough: Technique Review',
                ],
            ],
            [
                'title' => 'Executing Real-World %s Projects',
                'description' => 'Shift from guided learning into realistic execution so learners can connect the pieces into finished work.',
                'lessons' => [
                    'Scoping a Real-World %s Task',
                    'Working Faster Without Sacrificing Quality',
                    'Handling Feedback and Revision Loops',
                    'Practice: Delivering a Polished Outcome',
                    'Video Walkthrough: Real-World Execution',
                ],
            ],
            [
                'title' => 'Quality, Delivery, and Growth in %s',
                'description' => 'Close the course by strengthening quality control, presentation, and next-step planning for continued growth.',
                'lessons' => [
                    'Quality Checks Before You Ship %s Work',
                    'Presenting and Explaining Your Decisions',
                    'Building a Personal Improvement System',
                    'Practice: Final Review and Refinement',
                    'Video Walkthrough: Professional Delivery',
                ],
            ],
            [
                'title' => 'Advanced Patterns in %s',
                'description' => 'Explore advanced patterns, edge cases, and stronger decision-making so learners can operate with greater independence.',
                'lessons' => [
                    'Recognizing Advanced %s Patterns',
                    'Balancing Speed, Quality, and Tradeoffs',
                    'Avoiding Expensive Mistakes in Complex Work',
                    'Practice: Solving a Harder %s Scenario',
                    'Video Walkthrough: Advanced Review',
                ],
            ],
            [
                'title' => '%s Capstone and Portfolio Readiness',
                'description' => 'Bring the course together in a capstone-oriented structure that reinforces execution, review, and communication.',
                'lessons' => [
                    'Defining a Capstone Scope for %s',
                    'Structuring the Work from Start to Finish',
                    'Review, Feedback, and Final Polish',
                    'Presenting the Outcome with Clarity',
                    'Video Walkthrough: Capstone Debrief',
                ],
            ],
        ],
        'web_development' => [
            [
                'title' => '%s Fundamentals, Stack, and Setup',
                'description' => 'Start with the product context, core tools, and technical setup required to build confidently inside a modern web workflow.',
                'lessons' => [
                    'Understanding How %s Fits Into Modern Web Work',
                    'Setting Up Your Local %s Workflow',
                    'Core Concepts Every %s Builder Should Know',
                    'Planning a Clean Starter Project',
                    'Guided Build: Your First Working Feature',
                ],
            ],
            [
                'title' => 'Designing the Core User Flow for %s',
                'description' => 'Translate requirements into usable flows, interfaces, and implementation decisions that support a strong user experience.',
                'lessons' => [
                    'Mapping the User Journey',
                    'Structuring Screens, Components, or Pages',
                    'Managing Inputs, States, and User Feedback',
                    'Practice: Building a Meaningful Flow',
                    'Video Walkthrough: UX-to-Implementation Review',
                ],
            ],
            [
                'title' => 'Data, State, and Integrations for %s',
                'description' => 'Work through the data layer, state management decisions, and external integrations that make the product useful.',
                'lessons' => [
                    'Designing the Data You Actually Need',
                    'Connecting State to User Actions',
                    'Integrating External Services Without Chaos',
                    'Practice: Building a Realistic Feature Loop',
                    'Video Walkthrough: Data Flow Review',
                ],
            ],
            [
                'title' => 'Quality, Security, and Shipping %s',
                'description' => 'Strengthen the build with quality checks, security thinking, and practical shipping discipline.',
                'lessons' => [
                    'Writing Safer and More Maintainable Features',
                    'Testing the Parts That Matter Most',
                    'Handling Edge Cases, Errors, and Empty States',
                    'Practice: Hardening the Feature Set',
                    'Video Walkthrough: Pre-Launch Review',
                ],
            ],
            [
                'title' => 'Performance, Polish, and Career-Ready %s Delivery',
                'description' => 'Finish by improving performance, polish, and presentation so the work feels credible in production and in a portfolio.',
                'lessons' => [
                    'Improving Performance Without Premature Complexity',
                    'Polishing Developer Experience and Maintainability',
                    'Documenting and Presenting Your Solution',
                    'Practice: Final Quality Pass',
                    'Video Walkthrough: Portfolio Debrief',
                ],
            ],
            [
                'title' => 'Advanced Architecture for %s',
                'description' => 'Move into stronger architecture, scale-minded tradeoffs, and more mature implementation patterns.',
                'lessons' => [
                    'Choosing the Right Architecture Boundary',
                    'Managing Complexity as the Product Grows',
                    'Improving Reliability and Team Readiness',
                    'Practice: Refactoring for Clarity',
                    'Video Walkthrough: Architecture Review',
                ],
            ],
        ],
        'graphics_design' => [
            [
                'title' => '%s Foundations and Creative Direction',
                'description' => 'Anchor the course in audience, message, visual hierarchy, and the design decisions that make output intentional.',
                'lessons' => [
                    'Understanding the Purpose Behind %s',
                    'Building a Strong Creative Brief',
                    'Visual Hierarchy, Balance, and Clarity',
                    'Planning a Cohesive Direction',
                    'Guided Design Exercise: First Draft Decisions',
                ],
            ],
            [
                'title' => 'Layout, Composition, and Systems in %s',
                'description' => 'Develop a repeatable visual system so layouts feel structured, readable, and professionally resolved.',
                'lessons' => [
                    'Building Better Layouts with Grids and Spacing',
                    'Composition Rules That Improve Readability',
                    'Working With Repetition, Rhythm, and Alignment',
                    'Practice: Structuring a Multi-Element Design',
                    'Video Walkthrough: Composition Breakdown',
                ],
            ],
            [
                'title' => 'Typography, Color, and Brand Consistency for %s',
                'description' => 'Refine communication through stronger type, color decisions, and visual consistency across deliverables.',
                'lessons' => [
                    'Choosing Type That Supports the Message',
                    'Using Color With More Intent',
                    'Creating a Consistent Visual Language',
                    'Practice: Refining a Brand-Aligned Piece',
                    'Video Walkthrough: Typography and Color Critique',
                ],
            ],
            [
                'title' => 'Production Workflow and Client-Ready %s Assets',
                'description' => 'Move from concept to production by organizing files, preparing assets, and communicating work clearly.',
                'lessons' => [
                    'Structuring Files and Artboards Efficiently',
                    'Preparing Assets for Different Use Cases',
                    'Presenting Choices to Stakeholders or Clients',
                    'Practice: Packaging a Finished Deliverable',
                    'Video Walkthrough: Production Review',
                ],
            ],
            [
                'title' => 'Portfolio-Level %s Refinement',
                'description' => 'Close with critique, refinement, and presentation skills that help the work look ready for public review.',
                'lessons' => [
                    'Running a Meaningful Self-Critique',
                    'Refining Details That Increase Perceived Quality',
                    'Building a Presentation Narrative Around the Work',
                    'Practice: Final Portfolio Pass',
                    'Video Walkthrough: Design Debrief',
                ],
            ],
        ],
        'data_analysis' => [
            [
                'title' => '%s Framing, Setup, and Success Metrics',
                'description' => 'Start by clarifying the business question, data context, and analysis setup that will shape the rest of the course.',
                'lessons' => [
                    'Understanding the Question Behind %s',
                    'Setting Up a Clean Analysis Workflow',
                    'Defining Useful Metrics and Dimensions',
                    'Planning the First Analysis Pass',
                    'Guided Exercise: Framing a Small Analysis',
                ],
            ],
            [
                'title' => 'Cleaning and Structuring Data for %s',
                'description' => 'Move into the practical work of preparing data so the analysis is trustworthy, consistent, and usable.',
                'lessons' => [
                    'Finding Data Quality Risks Early',
                    'Cleaning, Typing, and Structuring the Dataset',
                    'Creating Reusable Analysis Logic',
                    'Practice: Building a Reliable Working Table',
                    'Video Walkthrough: Data Cleaning Review',
                ],
            ],
            [
                'title' => 'Exploration and Insight Discovery in %s',
                'description' => 'Explore the data, surface patterns, and identify the kinds of insights that deserve deeper analysis.',
                'lessons' => [
                    'Exploring Distributions, Trends, and Outliers',
                    'Comparing Segments That Matter',
                    'Spotting Signals Worth Investigating',
                    'Practice: Turning Findings Into Insight',
                    'Video Walkthrough: Exploratory Analysis',
                ],
            ],
            [
                'title' => 'Storytelling, Dashboards, and Communication for %s',
                'description' => 'Turn raw findings into clear narratives, dashboards, and recommendations that decision-makers can use.',
                'lessons' => [
                    'Choosing the Right Chart for the Question',
                    'Designing a Cleaner Reporting Narrative',
                    'Building Dashboards That Support Decisions',
                    'Practice: Presenting a Recommendation',
                    'Video Walkthrough: Insight Storytelling Review',
                ],
            ],
            [
                'title' => 'Advanced Thinking and Delivery in %s',
                'description' => 'Finish with stronger analytical judgment, automation habits, and delivery standards for production-ready work.',
                'lessons' => [
                    'Avoiding Common Analytical Mistakes',
                    'Improving Repeatability and Automation',
                    'Reviewing the Quality of Recommendations',
                    'Practice: Final Analysis Case Review',
                    'Video Walkthrough: Final Analyst Debrief',
                ],
            ],
        ],
    ];

    public function build(CourseGenerationPayload $payload): array
    {
        $topic = $payload->get('topic');
        $archetype = $this->resolveArchetype($topic, $payload->get('category'));
        $goals = $this->buildCourseGoals($payload, $topic);

        $sections = $payload->hasCustomSections()
            ? $this->buildProvidedSections($payload, $goals)
            : $this->buildGeneratedSections($payload, $archetype, $goals);

        [$sections, $courseVideos, $warnings] = $this->hydrateVideoLessons($payload, $sections, $topic);
        $sections = $this->attachAssessments($payload, $sections, $topic);

        $estimatedDuration = $payload->get('estimated_duration_minutes')
            ?: collect($sections)->sum(fn (array $section) => collect($section['lessons'])->sum('duration_minutes'));

        return [
            'course' => [
                'title' => $payload->get('title') ?: $topic,
                'subtitle' => $payload->get('subtitle') ?: $this->buildSubtitle($payload, $archetype),
                'slug' => $payload->get('slug'),
                'description' => $this->buildCourseDescription($payload, $goals),
                'difficulty_level' => $payload->get('skill_level'),
                'estimated_duration_minutes' => $estimatedDuration,
                'target_audience' => $payload->get('target_audience') ?: $this->buildTargetAudience($payload, $archetype),
                'learning_outcomes' => $payload->get('learning_outcomes') ?: $this->buildLearningOutcomes($payload, $goals),
                'prerequisites' => $payload->get('prerequisites') ?: $this->buildPrerequisites($payload, $archetype),
                'syllabus_overview' => $payload->get('syllabus_overview') ?: $this->buildSyllabusOverview($sections),
                'faqs' => $payload->get('faqs') ?: ($payload->get('include_faqs') ? $this->buildFaqs($payload, $estimatedDuration) : []),
                'materials_included' => $payload->get('materials_included') ?: ($payload->get('include_resources') ? $this->buildMaterials($payload, $archetype) : []),
                'tags' => $payload->get('tags') ?: $this->buildTags($payload, $archetype),
                'videos' => $courseVideos,
                'external_links' => $payload->get('resource_links', []),
                'has_offline_content' => $payload->get('include_resources'),
            ],
            'sections' => $sections,
            'warnings' => $warnings,
        ];
    }

    protected function buildGeneratedSections(CourseGenerationPayload $payload, string $archetype, array $goals): array
    {
        $topic = $payload->get('topic');
        $sectionCount = min(max($payload->get('section_count'), 1), 12);
        $lessonsPerSection = min(max($payload->get('lessons_per_section'), 1), 12);
        $templates = self::SECTION_TEMPLATES[$archetype] ?? self::SECTION_TEMPLATES['generic'];
        $templateIndexes = $this->selectSectionTemplateIndexes(count($templates), $sectionCount);
        $sections = [];

        foreach ($templateIndexes as $position => $templateIndex) {
            $template = $templates[$templateIndex];
            $sectionGoal = $goals[$position % count($goals)];
            $lessonIndexes = $this->distributedIndexes(count($template['lessons']), $lessonsPerSection);
            $lessons = [];

            foreach ($lessonIndexes as $lessonPosition => $lessonIndex) {
                $isVideo = $this->isVideoLesson($lessonPosition, $lessonsPerSection, $payload->get('video_lessons_per_section'));
                $title = sprintf($template['lessons'][$lessonIndex], $topic);
                $lessons[] = $this->makeLessonBlueprint(
                    payload: $payload,
                    sectionTitle: sprintf($template['title'], $topic),
                    sectionGoal: $sectionGoal,
                    title: $title,
                    lessonPosition: $lessonPosition,
                    sectionPosition: $position,
                    contentType: $isVideo ? 'video' : 'text'
                );
            }

            $sections[] = [
                'title' => sprintf($template['title'], $topic),
                'description' => sprintf($template['description'], $topic) . ' This section is anchored by the goal: ' . $sectionGoal . '.',
                'lessons' => $lessons,
            ];
        }

        return $sections;
    }

    protected function buildProvidedSections(CourseGenerationPayload $payload, array $goals): array
    {
        $topic = $payload->get('topic');
        $sections = [];

        foreach ($payload->get('sections', []) as $sectionPosition => $section) {
            $sectionTitle = $section['title'] ?? 'Section ' . ($sectionPosition + 1) . ': ' . $topic;
            $sectionGoal = $goals[$sectionPosition % count($goals)];
            $lessons = [];
            $providedLessons = $section['lessons'] ?? [];

            foreach ($providedLessons as $lessonPosition => $lesson) {
                $contentType = $lesson['content_type'] ?? ($lesson['video_url'] ?? null ? 'video' : 'text');
                $lessons[] = $this->makeLessonBlueprint(
                    payload: $payload,
                    sectionTitle: $sectionTitle,
                    sectionGoal: $sectionGoal,
                    title: $lesson['title'] ?? sprintf('%s Practice %d', $topic, $lessonPosition + 1),
                    lessonPosition: $lessonPosition,
                    sectionPosition: $sectionPosition,
                    contentType: $contentType,
                    descriptionOverride: $lesson['description'] ?? null,
                    contentOverride: $lesson['content'] ?? null,
                    videoUrlOverride: $lesson['video_url'] ?? null,
                    durationOverride: $lesson['duration_minutes'] ?? null,
                    completionTimeType: $lesson['completion_time_type'] ?? null,
                    difficultyLevel: $lesson['difficulty_level'] ?? null,
                    externalLinks: $lesson['external_links'] ?? []
                );
            }

            $sections[] = [
                'title' => $sectionTitle,
                'description' => $section['description'] ?? 'This section moves the learner forward by focusing on ' . Str::lower($sectionGoal) . '.',
                'lessons' => $lessons,
                'assessment' => $section['assessment'] ?? [],
            ];
        }

        return $sections;
    }

    protected function makeLessonBlueprint(
        CourseGenerationPayload $payload,
        string $sectionTitle,
        string $sectionGoal,
        string $title,
        int $lessonPosition,
        int $sectionPosition,
        string $contentType,
        ?string $descriptionOverride = null,
        ?string $contentOverride = null,
        ?string $videoUrlOverride = null,
        ?int $durationOverride = null,
        ?string $completionTimeType = null,
        ?string $difficultyLevel = null,
        array $externalLinks = []
    ): array {
        $contentType = in_array($contentType, ['text', 'video', 'file'], true) ? $contentType : 'text';
        $duration = $durationOverride ?: $this->estimateLessonDuration($contentType, $lessonPosition, $sectionPosition);

        return [
            'title' => $title,
            'description' => $descriptionOverride ?: $this->buildLessonDescription($payload, $sectionTitle, $sectionGoal, $title, $contentType),
            'content' => $contentOverride,
            'content_type' => $contentType,
            'video_url' => $videoUrlOverride,
            'duration_minutes' => $duration,
            'completion_time_type' => $completionTimeType ?: ($contentType === 'video' ? 'watching' : 'reading'),
            'difficulty_level' => $difficultyLevel ?: $this->resolveLessonDifficulty($payload->get('skill_level'), $sectionPosition),
            'external_links' => $externalLinks,
        ];
    }

    protected function hydrateVideoLessons(CourseGenerationPayload $payload, array $sections, string $topic): array
    {
        $videoPool = $payload->get('video_references', []);
        $videoIndex = 0;
        $courseVideos = [];
        $warnings = [];

        foreach ($sections as $sectionIndex => $section) {
            foreach ($section['lessons'] as $lessonIndex => $lesson) {
                $manualReviewRequired = false;
                $videoUrl = $lesson['video_url'] ?? null;

                if (($lesson['content_type'] ?? 'text') === 'video' && !$videoUrl) {
                    $candidate = $videoPool[$videoIndex] ?? null;

                    if ($candidate && !empty($candidate['url'])) {
                        $videoUrl = $candidate['url'];
                        $videoIndex++;
                    } else {
                        $manualReviewRequired = true;
                        $warnings[] = sprintf(
                            'Lesson "%s" was marked as a video lesson but no verified YouTube URL was supplied. Manual review is required.',
                            $lesson['title']
                        );
                    }
                }

                if ($videoUrl) {
                    $courseVideos[] = [
                        'title' => $lesson['title'],
                        'url' => $videoUrl,
                    ];
                }

                $sections[$sectionIndex]['lessons'][$lessonIndex]['video_url'] = $videoUrl;
                $sections[$sectionIndex]['lessons'][$lessonIndex]['content'] = $this->buildLessonContent(
                    payload: $payload,
                    topic: $topic,
                    section: $section,
                    lesson: $sections[$sectionIndex]['lessons'][$lessonIndex],
                    manualVideoReviewRequired: $manualReviewRequired
                );
            }
        }

        return [$sections, $courseVideos, $warnings];
    }

    protected function attachAssessments(CourseGenerationPayload $payload, array $sections, string $topic): array
    {
        if (!$payload->get('include_quizzes')) {
            return $sections;
        }

        foreach ($sections as $index => $section) {
            $assessment = $section['assessment'] ?? [];

            if ($assessment === []) {
                $assessment = [
                    'title' => $section['title'] . ' Knowledge Check',
                    'description' => 'A short quiz to confirm the learner can explain the section flow, identify the most important lesson checkpoints, and carry the section goal forward.',
                    'type' => 'quiz',
                    'pass_percentage' => 70,
                    'estimated_duration_minutes' => 10,
                    'questions' => $this->buildGeneratedQuestions($topic, $section),
                ];
            } else {
                $assessment['title'] = $assessment['title'] ?? ($section['title'] . ' Knowledge Check');
                $assessment['description'] = $assessment['description'] ?? 'A short quiz attached to this section.';
                $assessment['type'] = $assessment['type'] ?? 'quiz';
                $assessment['pass_percentage'] = $assessment['pass_percentage'] ?? 70;
                $assessment['estimated_duration_minutes'] = $assessment['estimated_duration_minutes'] ?? 10;
                $assessment['questions'] = !empty($assessment['questions'])
                    ? $assessment['questions']
                    : $this->buildGeneratedQuestions($topic, $section);
            }

            $sections[$index]['assessment'] = $assessment;
        }

        return $sections;
    }

    protected function buildGeneratedQuestions(string $topic, array $section): array
    {
        $lessonTitles = collect($section['lessons'])->pluck('title')->values()->all();
        $firstLesson = $lessonTitles[0] ?? 'the opening lesson';
        $lastLesson = $lessonTitles[count($lessonTitles) - 1] ?? $firstLesson;
        $sectionGoal = $section['description'] ?? 'Build a repeatable and practical workflow.';

        return [
            [
                'question_text' => sprintf('Which lesson opens "%s" by setting the direction for the section?', $section['title']),
                'question_type' => 'multiple_choice',
                'options' => array_slice(array_pad($lessonTitles, 4, $firstLesson), 0, 4),
                'correct_answers' => [0],
                'points' => 5,
                'explanation' => 'The first lesson frames the section and gives the learner the context needed for the rest of the work.',
                'difficulty_level' => 'easy',
                'tags' => [$topic, $section['title']],
            ],
            [
                'question_text' => sprintf('Which lesson is designed as the closing guided application for "%s"?', $section['title']),
                'question_type' => 'multiple_choice',
                'options' => [
                    $lastLesson,
                    $firstLesson,
                    'A lesson from a different section',
                    'There is no applied lesson in this section',
                ],
                'correct_answers' => [0],
                'points' => 5,
                'explanation' => 'The final lesson is positioned as the most complete guided application or walkthrough in the section.',
                'difficulty_level' => 'medium',
                'tags' => [$topic, $section['title']],
            ],
            [
                'question_text' => sprintf('What is the main objective of the "%s" section?', $section['title']),
                'question_type' => 'multiple_choice',
                'options' => [
                    Str::limit(strip_tags($sectionGoal), 120),
                    'Memorize every tool without applying them in practice',
                    'Skip structured workflow and move straight to advanced shortcuts',
                    'Rely on guesswork instead of a repeatable process',
                ],
                'correct_answers' => [0],
                'points' => 5,
                'explanation' => 'Each section objective is defined so learners can connect the lessons to a practical outcome.',
                'difficulty_level' => 'medium',
                'tags' => [$topic, $section['title']],
            ],
        ];
    }

    protected function buildLessonContent(
        CourseGenerationPayload $payload,
        string $topic,
        array $section,
        array $lesson,
        bool $manualVideoReviewRequired = false
    ): string {
        if (!empty($lesson['content'])) {
            return $this->appendResourceAndVideoNotes(
                $payload,
                $lesson['content'],
                $lesson['title'],
                $manualVideoReviewRequired
            );
        }

        $description = e($lesson['description'] ?? '');
        $sectionTitle = e($section['title']);
        $lessonTitle = e($lesson['title']);
        $topicText = e($topic);
        $contentType = $lesson['content_type'] ?? 'text';

        $primaryFocus = $contentType === 'video'
            ? 'watch the walkthrough carefully, note the decision points, and replicate the workflow afterward'
            : 'read actively, highlight the major decisions, and translate the process into a small repeatable task';

        $practicePrompt = $contentType === 'video'
            ? 'Pause the walkthrough at key transitions, reproduce the demonstrated steps, and capture one improvement you would make in your own version.'
            : 'Summarize the lesson in your own words, then apply it to a small scoped task that can be finished in one sitting.';

        $resourceBlock = '';
        if ($payload->get('include_resources')) {
            $resourceBlock = '<h2>Suggested Resource Pack</h2><ul>'
                . '<li>One-page checklist for the lesson workflow</li>'
                . '<li>Short reflection notes to capture what changed in your understanding</li>'
                . '<li>Practice prompt or worksheet for independent repetition</li>'
                . '</ul>';
        }

        $videoReviewBlock = '';
        if ($manualVideoReviewRequired) {
            $videoReviewBlock = '<div><strong>Manual review required:</strong> add a verified YouTube URL for this lesson before publishing it publicly.</div>';
        }

        return <<<HTML
<p>{$description}</p>
<h2>What This Lesson Covers</h2>
<ul>
<li>How this lesson supports the wider flow of {$sectionTitle}</li>
<li>The decisions, tools, and checkpoints that matter most for {$topicText}</li>
<li>What to pay attention to so the learner can move into the next lesson with confidence</li>
</ul>
<h2>How to Work Through It</h2>
<p>Use this lesson to {$primaryFocus}. The goal is not passive consumption; it is to turn the lesson into a working habit.</p>
<h2>Practice Prompt</h2>
<p>{$practicePrompt}</p>
{$resourceBlock}
{$videoReviewBlock}
HTML;
    }

    protected function appendResourceAndVideoNotes(
        CourseGenerationPayload $payload,
        string $content,
        string $lessonTitle,
        bool $manualVideoReviewRequired
    ): string {
        $notes = [];

        if ($payload->get('include_resources')) {
            $notes[] = '<h2>Suggested Resource Pack</h2><ul>'
                . '<li>Lesson checklist</li><li>Quick recap notes</li><li>Independent practice prompt</li></ul>';
        }

        if ($manualVideoReviewRequired) {
            $notes[] = '<div><strong>Manual review required:</strong> add a verified YouTube URL for "' . e($lessonTitle) . '" before publishing.</div>';
        }

        return trim($content . "\n" . implode("\n", $notes));
    }

    protected function buildCourseGoals(CourseGenerationPayload $payload, string $topic): array
    {
        $goals = $payload->get('course_goals', []);

        if ($goals !== []) {
            return $goals;
        }

        return [
            'Build a clear and repeatable workflow for ' . Str::lower($topic),
            'Move from guided exercises into practical, real-world execution',
            'Improve quality, confidence, and delivery standards in finished work',
        ];
    }

    protected function buildSubtitle(CourseGenerationPayload $payload, string $archetype): string
    {
        $topic = $payload->get('topic');
        $audience = $payload->get('target_audience') ?: 'serious learners';

        return match ($archetype) {
            'web_development' => "Build production-ready {$topic} skills through guided implementation, workflow thinking, and portfolio-minded delivery.",
            'graphics_design' => "Develop sharper {$topic} decisions through structured critique, design systems, and client-ready execution.",
            'data_analysis' => "Turn {$topic} into a practical analysis workflow with cleaner data thinking, stronger insights, and better communication.",
            default => "A structured {$topic} course for {$audience} who want practical skill growth, not filler content.",
        };
    }

    protected function buildCourseDescription(CourseGenerationPayload $payload, array $goals): string
    {
        $topic = $payload->get('topic');
        $audience = $payload->get('target_audience') ?: 'learners who want a practical workflow';
        $level = $payload->get('skill_level');
        $goalText = implode('; ', array_slice($goals, 0, 3));

        return "This {$level}-level course helps {$audience} develop a clear, professional approach to {$topic}. The course is structured to move from foundations into applied execution, with each section building toward realistic delivery. By the end, learners will be able to {$goalText}.";
    }

    protected function buildTargetAudience(CourseGenerationPayload $payload, string $archetype): string
    {
        return match ($archetype) {
            'web_development' => 'Aspiring developers, junior engineers, and builders who want a more structured path from concepts to shipping useful features.',
            'graphics_design' => 'New and growing designers who want stronger creative judgment, cleaner execution, and more presentable portfolio work.',
            'data_analysis' => 'Analysts, operators, and curious professionals who want to turn raw data into decisions, reporting, and practical recommendations.',
            default => 'Learners who want a structured, practical pathway from fundamentals to credible real-world execution.',
        };
    }

    protected function buildLearningOutcomes(CourseGenerationPayload $payload, array $goals): array
    {
        $topic = $payload->get('topic');

        return array_values(array_unique(array_slice(array_merge(
            array_map(
                fn (string $goal) => Str::finish($goal, '.'),
                $goals
            ),
            [
                "Plan {$topic} work using a clearer workflow and stronger decision-making checkpoints.",
                "Execute scoped {$topic} tasks with more consistency, review discipline, and confidence.",
                "Communicate the reasoning behind finished {$topic} work in a more professional way.",
            ]
        ), 0, 6)));
    }

    protected function buildPrerequisites(CourseGenerationPayload $payload, string $archetype): array
    {
        $topic = $payload->get('topic');

        return match ($payload->get('skill_level')) {
            'beginner' => [
                'Comfort using a computer and following a structured digital workflow.',
                "No prior {$topic} experience is required, but consistent practice will help.",
                'A willingness to review mistakes, take notes, and repeat short practice tasks.',
            ],
            'intermediate' => [
                "Working familiarity with the fundamentals of {$topic} or a closely related skill.",
                'Comfort using the common tools or environments that support this workflow.',
                'Ability to complete small independent exercises without step-by-step supervision.',
            ],
            'advanced', 'expert' => [
                "Strong foundational experience in {$topic} or a closely related discipline.",
                'Comfort diagnosing quality issues and making tradeoff decisions independently.',
                'Readiness to tackle larger scenarios, critique your own work, and iterate quickly.',
            ],
            default => [
                'A practical mindset and willingness to work through structured lessons.',
            ],
        };
    }

    protected function buildFaqs(CourseGenerationPayload $payload, int $estimatedDuration): array
    {
        $topic = $payload->get('topic');
        $level = $payload->get('skill_level');
        $hours = max(1, (int) ceil($estimatedDuration / 60));

        return [
            [
                'question' => "Is this {$topic} course suitable for {$level} learners?",
                'answer' => "Yes. The course is structured to meet {$level} learners where they are, then move them into practical application at a sustainable pace.",
            ],
            [
                'question' => 'How much time should I set aside each week?',
                'answer' => "A steady 3 to 5 hours per week is enough for most learners to make progress through a {$hours}-hour course while still completing the exercises.",
            ],
            [
                'question' => 'Will I create work I can reuse after the course?',
                'answer' => 'Yes. The workflow is designed around practical deliverables, recap notes, and repeatable practice prompts so learners leave with reusable material.',
            ],
            [
                'question' => 'Does the course include video lessons?',
                'answer' => 'Yes, where verified references are available. If a video reference is still pending manual review, the lesson content will clearly call that out.',
            ],
        ];
    }

    protected function buildMaterials(CourseGenerationPayload $payload, string $archetype): array
    {
        $topic = $payload->get('topic');

        return match ($archetype) {
            'web_development' => [
                "{$topic} implementation checklist",
                'Feature planning worksheet',
                'Testing and quality review prompt',
                'Deployment readiness checklist',
            ],
            'graphics_design' => [
                "{$topic} creative brief template",
                'Design critique checklist',
                'Typography and hierarchy review sheet',
                'Final asset handoff checklist',
            ],
            'data_analysis' => [
                "{$topic} analysis brief template",
                'Data cleaning checklist',
                'Insight storytelling outline',
                'Final reporting review sheet',
            ],
            default => [
                "{$topic} course workbook",
                'Practice checklist',
                'Section recap notes',
                'Independent exercise prompt sheet',
            ],
        };
    }

    protected function buildTags(CourseGenerationPayload $payload, string $archetype): array
    {
        $topic = $payload->get('topic');
        $category = $payload->get('category');
        $categoryName = $category['name'] ?? $category['slug'] ?? 'Course';

        $tags = [
            $topic,
            $categoryName,
            Str::headline(str_replace('_', ' ', $archetype)),
            Str::headline($payload->get('skill_level')),
        ];

        foreach ($payload->get('course_goals', []) as $goal) {
            $tags[] = Str::limit($goal, 40, '');
        }

        return array_values(array_slice(array_unique(array_filter(array_map(
            fn (string $tag) => Str::of($tag)->squish()->toString(),
            $tags
        ))), 0, 10));
    }

    protected function buildSyllabusOverview(array $sections): string
    {
        $sectionTitles = collect($sections)->pluck('title')->all();

        return 'The course progresses through the following sections: ' . implode(' -> ', $sectionTitles) . '.';
    }

    protected function buildLessonDescription(
        CourseGenerationPayload $payload,
        string $sectionTitle,
        string $sectionGoal,
        string $lessonTitle,
        string $contentType
    ): string {
        $topic = $payload->get('topic');
        $mode = $contentType === 'video' ? 'watch and apply' : 'study and apply';

        return "{$lessonTitle} helps learners {$mode} a focused part of {$topic}. It supports {$sectionTitle} by driving toward the goal: {$sectionGoal}.";
    }

    protected function resolveArchetype(string $topic, ?array $category): string
    {
        $haystack = Str::lower(trim($topic . ' ' . ($category['name'] ?? $category['slug'] ?? '')));

        if (Str::contains($haystack, ['web', 'frontend', 'backend', 'laravel', 'react', 'javascript', 'php', 'api', 'software'])) {
            return 'web_development';
        }

        if (Str::contains($haystack, ['design', 'graphics', 'ui', 'ux', 'branding', 'visual'])) {
            return 'graphics_design';
        }

        if (Str::contains($haystack, ['data', 'analysis', 'analytics', 'excel', 'sql', 'power bi', 'tableau', 'python'])) {
            return 'data_analysis';
        }

        return 'generic';
    }

    protected function distributedIndexes(int $available, int $required): array
    {
        if ($required >= $available) {
            return range(0, $available - 1);
        }

        if ($required === 1) {
            return [0];
        }

        $indexes = [];

        for ($i = 0; $i < $required; $i++) {
            $index = (int) round(($i * ($available - 1)) / ($required - 1));
            $indexes[] = $index;
        }

        $indexes = array_values(array_unique($indexes));

        while (count($indexes) < $required) {
            foreach (range(0, $available - 1) as $candidate) {
                if (!in_array($candidate, $indexes, true)) {
                    $indexes[] = $candidate;
                }

                if (count($indexes) === $required) {
                    break;
                }
            }
        }

        sort($indexes);

        return $indexes;
    }

    protected function selectSectionTemplateIndexes(int $available, int $required): array
    {
        if ($required >= max(1, $available - 1)) {
            return range(0, min($required, $available) - 1);
        }

        return $this->distributedIndexes($available, $required);
    }

    protected function isVideoLesson(int $lessonPosition, int $lessonCount, int $videoLessonsPerSection): bool
    {
        if ($videoLessonsPerSection <= 0) {
            return false;
        }

        return $lessonPosition >= max(0, $lessonCount - $videoLessonsPerSection);
    }

    protected function estimateLessonDuration(string $contentType, int $lessonPosition, int $sectionPosition): int
    {
        $base = $contentType === 'video' ? 18 : 12;

        return $base + ($lessonPosition * 3) + min($sectionPosition, 3);
    }

    protected function resolveLessonDifficulty(string $courseLevel, int $sectionPosition): string
    {
        return match ($courseLevel) {
            'beginner' => $sectionPosition >= 3 ? 'intermediate' : 'beginner',
            'intermediate' => $sectionPosition >= 3 ? 'advanced' : 'intermediate',
            'advanced' => $sectionPosition >= 2 ? 'advanced' : 'intermediate',
            'expert' => 'expert',
            default => 'beginner',
        };
    }
}
