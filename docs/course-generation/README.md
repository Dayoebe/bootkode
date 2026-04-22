# Course Generation Workflow

This workflow adds a reusable, Tinker-friendly course generator for Bootkode.

## What It Uses

- `App\Services\CourseGeneration\CourseGenerationService`
- `App\Services\CourseGeneration\CourseGenerationPayload`
- `App\Services\CourseGeneration\CourseGenerationBlueprintBuilder`
- `php artisan bootkode:generate-course`

The generator writes through the existing `Course`, `Section`, `Lesson`, `Assessment`, and `Question` models. It does not add new course schema fields.

## What It Supports

- course basics: title, subtitle, description, slug, pricing, publish/approve flags
- category resolution by id, slug, or name
- instructor resolution by id or email, with a safe fallback if omitted
- generated or user-supplied sections and lessons
- learning outcomes, prerequisites, syllabus overview, FAQs, tags, and materials
- video lessons with verified YouTube URLs when supplied
- manual-review warnings when video lessons exist but URLs are missing
- optional section quizzes with generated questions
- duplicate handling via `return_existing`, `fail`, or `create_new`

## Current Schema Limits

- Courses do not currently support dedicated SEO/meta columns, so the generator does not create `meta_title` or `meta_description` for courses.
- The generator will not invent YouTube URLs. Supply verified URLs through `video_references` or `sections[*].lessons[*].video_url`.

## Tinker Usage

```php
$payload = json_decode(
    file_get_contents(base_path('docs/course-generation/examples/web-development.json')),
    true
);

$result = app(\App\Services\CourseGeneration\CourseGenerationService::class)->generate($payload);

$result['course']->only(['id', 'title', 'slug', 'is_published', 'is_approved']);
$result['warnings'];
```

Preview without inserting:

```php
$payload = [
    'topic' => 'Prompt Engineering for Product Teams',
    'category' => 'AI & Automation',
    'skill_level' => 'beginner',
    'course_goals' => [
        'Write clearer prompts for recurring workflows',
        'Review outputs critically before using them',
        'Build a repeatable prompting workflow for a small team',
    ],
    'section_count' => 4,
    'lessons_per_section' => 4,
];

$preview = app(\App\Services\CourseGeneration\CourseGenerationService::class)->preview($payload);
```

## Artisan Usage

Preview a JSON payload:

```bash
php artisan bootkode:generate-course \
  --payload=docs/course-generation/examples/web-development.json \
  --dry-run
```

Generate directly from CLI options:

```bash
php artisan bootkode:generate-course \
  --topic="Laravel API Development" \
  --category="Web Development" \
  --instructor=1 \
  --level=intermediate \
  --goal="Build clean API endpoints" \
  --goal="Handle authentication and validation" \
  --goal="Ship a maintainable feature workflow" \
  --sections=5 \
  --lessons=4 \
  --with-faqs \
  --with-resources \
  --with-quizzes
```

## Payload Shape

```json
{
  "topic": "Laravel API Development",
  "category": {
    "name": "Web Development",
    "description": "Build practical software and web delivery skills."
  },
  "instructor": {
    "id": 1
  },
  "skill_level": "intermediate",
  "target_audience": "Junior PHP developers who want stronger API delivery habits.",
  "course_goals": [
    "Design clean API workflows",
    "Build maintainable feature implementations",
    "Ship with stronger testing and review discipline"
  ],
  "section_count": 5,
  "lessons_per_section": 4,
  "video_lessons_per_section": 1,
  "include_faqs": true,
  "include_resources": true,
  "include_quizzes": true,
  "publish": false,
  "approve": false,
  "duplicate_strategy": "return_existing",
  "video_references": [
    {
      "title": "Verified walkthrough",
      "url": "https://www.youtube.com/watch?v=..."
    }
  ]
}
```

## Example Payloads

- `docs/course-generation/examples/web-development.json`
- `docs/course-generation/examples/graphics-design.json`
- `docs/course-generation/examples/data-analysis.json`

The example payloads intentionally avoid fake YouTube URLs. Add verified URLs before production publishing if you want the generated video lessons to embed immediately.
