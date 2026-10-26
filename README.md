# BootKode

**Learn. Build. Grow.** Practical technology education for learners in Nigeria and beyond.

BootKode is an existing Laravel / Livewire application with public courses, learning progress, instructor and administration tools, wallet payments, certificates, mentorship, community, careers and institution workflows. Feature presence does not mean every provider integration is production-certified; read the [verification report](docs/verification.md).

## Stack

PHP 8.2+, Laravel 11.46.1, Livewire 3.6.4, Blade, Livewire-owned Alpine, Tailwind 3.4.19 and Vite 7.3.7. The full migration suite uses MySQL/MariaDB features. No frontend framework migration is required.

## Local setup

1. Install PHP dependencies with `composer install` and JavaScript dependencies with `npm ci`.
2. Copy `.env.example` to `.env` only for a new checkout. Set the local URL, application name, MySQL connection, mail and required provider configuration. Never overwrite an existing environment or commit credentials.
3. Run `php artisan key:generate` only for a new environment. Create a dedicated local database, then run `php artisan migrate`. Existing databases need a backup and reviewed forward migrations; never use fresh/reset commands on real records.
4. Run `npm run build`, then `php artisan serve`. For active frontend development use `npm run dev` in another terminal. No `public/hot` file should remain when testing a production build.
5. Configure the queue worker and scheduler for the actual mail/newsletter/job workflows: `php artisan queue:work` and the normal Laravel scheduler. Test external services independently before live use.

The recovered local database contains existing data. Preserve it. The new certificate compatibility migration adds fields required by the existing workflow and retains historical tokens, file paths and statuses.

## Verification

Create a dedicated test database and configure `.env.testing` with `APP_ENV=testing`, a test application key, test MySQL credentials and `DB_DATABASE=bootkode_test` (or another name ending `_test`/`_testing`). Use array mail/cache/session and the synchronous queue for automated tests. `Tests\TestCase` rejects other MySQL database names before database refresh. Do not point testing at the recovered database.

```sh
php artisan test
npm run build
php artisan view:cache
php artisan route:cache
php artisan route:clear
composer validate --no-check-publish
composer check-platform-reqs
git diff --check
```

Browser QA needs a persistent session driver such as `file`; the test suite's `array` session driver deliberately does not persist between browser requests. Create temporary fixtures only in a test database.

## Lesson assessments and review

Learners can open the assessment workspace from a lesson. Written submissions remain pending until the course owner or platform reviewer marks every written answer in **Course Management → Assessment Reviews**. Personal **Assessment results** shows retained attempts, published grades and feedback. Draft answers and deadlines persist across reloads; grading keys stay server-side. Instructors can open the contextual assessment authoring workspace from a course lesson. Lesson quizzes support matching, ordering and drag-and-drop with labelled keyboard alternatives; malformed legacy definitions require instructor setup. Legacy written records without attempt snapshots require a separate records review.

Finalized written marks support explicit corrections with retained before/after history. Learners can request reconsideration from either existing result screen; course owners/platform reviewers can correct or uphold the mark with a reason. **Assessment Reviews** filters pending, requested-review and finalized submissions. Stale decisions and changed replays are refused. Corrections update future eligibility while retaining existing rewards, completions and issued credentials. This workflow uses existing private attempt metadata and needs no new migration; unsupported historical attempts require a separate records review. Decisions are visible in results; no external notification delivery is claimed.

Project deliverables and file-enabled assignments support private saved draft attachments and instructor marking. Files keep the original requirements and become immutable on submission; authorized learners/reviewers download through verified private routes. Upload limits follow the strictest original instructor, Livewire and PHP configuration (currently 2 MiB locally). Sample answers, rubrics and grading notes remain private, including resumed attempts. Legacy attempts without upload rules keep their original text-only contract.

The session-clock compatibility migration removes an implicit MariaDB timestamp update rule. Back up the affected table before applying forward changes to an existing database; see the verification report for the local application and preservation evidence.

## Architecture and design

- [Product audit and real feature inventory](docs/product-audit.md)
- [Architecture and data compatibility](docs/architecture.md)
- [Design system and component contracts](docs/design-system.md)
- [Implementation roadmap](docs/refactor-roadmap.md)
- [Route inventory](docs/route-inventory.md)
- [Verification, known limits and release evidence](docs/verification.md)
- [AI setup and privacy](docs/bootkode-ai.md)
- [Course content import and generation](docs/course-content-system.md)
- [Contributor and coding-agent standard](AGENTS.md)

Public course discovery is `/courses`; public course summaries are `/courses/{slug}`. Authenticated lesson routes remain protected by enrollment. Shared controls live in `resources/views/components`; tokens live in `resources/css/tokens.css` with existing theme variables in `app.css`. Keep static UI in Blade, interaction in Livewire, and financial/enrollment rules in existing services.

`resources/js/app.js` contains public enhancements. `dashboard.js` loads charts and the existing Trix editors only for authenticated layouts. Livewire supplies Alpine and its plugins. Do not add another runtime.

## Payment configuration

Configure Paystack keys through server environment variables. The administration screen exposes setup state, never secret values. `/webhook/paystack` is a public HMAC-authenticated endpoint; browser callback URLs alone never authorize credit. Funding checks gateway reference, amount, currency and status before an atomic ledger update. Test with sandbox credentials and signed delivery. See Paystack's [webhook guidance](https://paystack.com/docs/payments/webhooks/) and [verification guidance](https://paystack.com/docs/payments/verify-payments/).

Marketplace wallet settlement requires an active NGN customer/vendor/platform wallet. Set server-side `MARKETPLACE_PLATFORM_USER_ID` to the designated super-administrator account; otherwise the first super administrator owns the platform wallet. Wallet checkout refuses missing ownership/currency/funding evidence and rolls back failed invoice or payout writes. Scout defaults to local collection search when Algolia credentials are absent; an explicitly configured driver still needs its real setup.

Financial limits are in `config/financial.php`; existing course-specific splits remain authoritative. The former Financial Settings form did not persist settings; its replacement shows the current applied rules.

## Release limits

The dependency audit still reports advisories that cannot all be resolved within the current major-version constraints. Composer's security policy blocks the attempted Laravel 11-compatible update. Do not disable the advisory policy or use forced major updates as a workaround. See the verification report for exact counts and the required follow-up.

There is no deployment claim. Merge/deployment, provider sandbox verification, legal documents and broader operational acceptance remain separate release requirements.

The 22 October continuation adds guarded support/feedback responses and private new attachments, participant-scoped message updates, secured nested course authoring, historical record deletion protection and funded affiliate commission settlement. Affiliate defaults remain server configuration (`config/affiliate.php`); the UI does not claim to save environment settings. Real banner routes and clipboard confirmation replace broken/promotional placeholders. See `docs/verification.md` for current automated/browser evidence and release limitations.

The further learning continuation consolidates both personal analytics routes around actual owned records and adds persisted goals and CSV export. Learning rewards now use positive atomic ledger operations and replay-safe daily/completion evidence. Leaderboard publication requires explicit opt-in and a public profile. CBT attempts persist their server start time, answers and private grading snapshots; replay and question edits cannot duplicate or restyle completed grades. No schema migration is needed for these changes. Unsupported game/reward/broadcast/path integrations show unavailable states.
