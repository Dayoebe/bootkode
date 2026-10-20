# BootKode AI

## Current implementation

The first production-minded vertical slice provides a private AI Coding Mentor. It reuses BootKode's authenticated dashboard, course enrollments, published/approved lesson content, and existing learning-profile calculations.

The mentor stores user-owned conversations and messages, limits history and course context sent to the provider, records operational usage, and never executes submitted code. Learners can only use content from enrolled courses; instructors and academy administrators retain the broader course access already established by the application.

The existing AI Learning Coach remains the evidence-based progress overview. Its tutor entry now leads to the persistent mentor rather than producing a simulated local AI answer.

## Provider setup

BootKode AI is disabled by default. Configure these values on the server, then clear the Laravel configuration cache:

```dotenv
BOOTKODE_AI_ENABLED=true
BOOTKODE_AI_PROVIDER=groq
GROQ_API_KEY=your-server-side-key
GROQ_MODEL=a-current-model-from-your-groq-account
GROQ_BASE_URL=https://api.groq.com/openai/v1
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
- AI output is rendered as escaped text. Course content and learner prompts are explicitly treated as untrusted input.
- Usage records contain operational metadata and provider-reported token counts when available, not prompts or responses.

## Next vertical slices

Code-review submissions, project mentoring, career coaching, instructor draft approval, and administrator usage summaries are intentionally not represented by placeholder screens. Each should be implemented as a tested vertical slice on top of the provider and usage foundation.
