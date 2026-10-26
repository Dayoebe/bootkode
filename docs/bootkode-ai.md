# BootKode AI

## Current implementation

The AI foundation provides a private AI Coding Mentor and a role-guarded course-generation workflow. The mentor reuses BootKode's authenticated dashboard, course enrollments, published/approved lesson content, and existing learning-profile calculations. Course generation reuses the configured AI provider and existing course authoring/assessment models.

The mentor stores user-owned conversations and messages, limits history and course context sent to the provider, records operational usage, and never executes submitted code. Learners can only use content from enrolled courses; instructors and academy administrators retain the broader course access already established by the application.

The existing AI Learning Coach remains the evidence-based progress overview. Its tutor entry now leads to the persistent mentor rather than producing a simulated local AI answer.

The Course Content Studio creates validated blueprint and lesson stages, optional multiple-choice quizzes and a project rubric, then persists a reviewable draft through `CourseGenerationService`. It records module-level progress for retry and has a separate per-editor daily quota. Generated courses remain unpublished and unapproved; factual claims, references, outcomes and answer keys require human review. See [course-content-system.md](./course-content-system.md) for the source importer, complete configuration and commands.

## Provider setup

BootKode AI is disabled by default. Configure these values on the server, then clear the Laravel configuration cache:

```dotenv
BOOTKODE_AI_ENABLED=true
BOOTKODE_AI_PROVIDER=groq
GROQ_API_KEY=your-server-side-key
GROQ_MODEL=a-current-model-from-your-groq-account
GROQ_BASE_URL=https://api.groq.com/openai/v1
BOOTKODE_AI_COURSE_GENERATION_DAILY_REQUESTS=12
```

Do not expose the key through Vite variables or browser state. The model has no hardcoded default because provider availability changes; select a currently supported model in the environment.

Optional limits are documented in `.env.example`. Apply the migration during deployment with the normal reviewed release process:

```bash
php artisan migrate
php artisan config:clear
```

## Failure and privacy behavior

- Missing or disabled configuration leaves the academy functional and shows a clear unavailable state.
- Provider errors are normalized without returning credentials or raw provider responses.
- Provider calls use server-side authentication, timeout, bounded retry, input limits, history limits, context limits, and daily per-user request limits.
- Course generation uses its own request quota so an instructor's course work is not charged against the learner coding-mentor quota.
- AI output is rendered as escaped text. Course content and learner prompts are explicitly treated as untrusted input.
- Usage records contain operational metadata and provider-reported token counts when available, not prompts or responses. Course generation stage state is retained in the private course-content run record to support retry.

## Next vertical slices

Code-review submissions, career coaching, and administrator usage summaries are intentionally not represented by placeholder screens. Course generation feeds the existing instructor editor and approval process; it does not replace that workflow or auto-approve generated material.

## Course-generation verification — 26 October 2026

Course generation accepts the documented audience, objectives, language, teaching style and optional project/assessment guidance through the same CLI/UI service. Validated-input request identity prevents repeated submissions/reloads from creating duplicate drafts. Completed stages survive later failures; processing locks and separate generation usage reservations limit duplicate spending. Generated projects include actual written-response criteria for the existing review flow. Markdown is sanitized, grading weights/types and ownership/publication remain server-owned, and generated references require human verification. Live AI generation was not exercised in this continuation; mocked provider/persistence verification and live licensed-source import are reported separately in verification.md.
