# Learning-profile validation and release boundary

The working database is not a test target. All database tests for this slice used a newly initialized independent MariaDB 10.11.19 datadir at `/tmp/bootkode-ecosystem-mariadb`, loopback port 3312. Its three schemas are `bootkode_ecosystem_test` (automated suite), `bootkode_browser_test` (synthetic browser fixtures) and `bootkode_profile_migration_test` (migration preservation rehearsal). The environment files were not edited. No original MariaDB data directory was started, replaced or migrated.

## Automated and schema evidence

- Baseline: `php artisan test --compact` — 433 tests passed, 2,087 assertions, 91.95 seconds.
- Implementation suite: same command — 444 tests passed, 2,180 assertions, 115.38 seconds.
- Final focused check after readable validation-label changes: `php artisan test --compact --filter='LearnerProfileTest|RegistrationTest|EmailVerificationTest'` — 16 passed, 107 assertions.
- Explicit command environment: `DB_HOST=127.0.0.1 DB_PORT=3312 DB_DATABASE=bootkode_ecosystem_test DB_USERNAME=root DB_PASSWORD=`; `Tests\TestCase` remains unchanged and refuses unsafe MySQL database names.
- New tests cover multi-step persistence, optional fields, existing knowledge, topic normalization, updates/replays, actual-category validation, invalid formats/levels/time bounds, hidden-step validation, private-text escaping, ownership/evidence forgery, skip/resume, initial redirects, and fresh inactive/role-revocation access checks. No AI requests are needed or made.
- The new forward migration was applied to each disposable schema. A separate legacy-profile rehearsal confirmed every original field/value survives, all six new fields initially remain null, rollback preserves the legacy row, and reapplication succeeds. Synthetic fixtures never entered the working database.
- `npm run build` passed (12.98 seconds), with the existing PostCSS missing-`from` warning. Blade compilation, the new named route, changed PHP syntax, browser-script syntax, focused Pint checks and `git diff --check` passed.

## Browser reproduction

`tests/Browser/learning-profile.mjs` is a repeatable authenticated Chrome check. It requires a local isolated server, synthetic learner/category fixtures and environment variables for URL, credentials, output directory and a temporary Playwright Core installation. It adds no application dependency. External fonts, analytics and MathJax requests are stubbed and service workers are blocked; these are outside this flow's acceptance scope. The private test router selected built Vite assets without changing the concurrently active developer Vite runtime.

Example variables (supply actual synthetic credentials locally):

```bash
PLAYWRIGHT_CORE_PATH=/tmp/bootkode-ecosystem-browser/node_modules/playwright-core \
BROWSER_BASE_URL=http://127.0.0.1:8094 \
BROWSER_LEARNER_EMAIL=... BROWSER_LEARNER_PASSWORD=... \
BROWSER_OUTPUT_DIR=/tmp/bootkode-ecosystem-browser/evidence \
node tests/Browser/learning-profile.mjs
```

See `../verification.md` for the final browser result. Screenshots and machine-readable records are local artifacts, not proof of production, restored-data or full assistive-technology acceptance.

## Open release gates

- The working configuration still points to `bootkode_restored_20261028` at port 3308; a read-only connection attempt fails. Restore/verify its real records and take an affected-profile/schema backup before applying `2026_11_02_000000_extend_ai_learning_profiles_for_onboarding.php` normally. Do not substitute these empty test schemas for recovered records. The prior course editorial payload remains outside this slice.
- Current `composer audit --locked --format=json`: 7 advisory reports across `firebase/php-jwt`, `laravel/framework` and `symfony/mime`; the report includes a critical Laravel advisory and two reports of the email-rule issue. Current `npm audit --json`: 4 vulnerable dependency entries (1 high, 3 moderate: follow-redirects, glob, minimatch, sucrase). Reports are `/tmp/bootkode-ecosystem-{composer,npm}-audit.json`. Nothing was suppressed, upgraded across majors or declared green.
- No real Groq request was made for this form-only slice. Existing server configuration still uses `AiManager` / `OpenAiCompatibleProvider`, Groq and the configured `GROQ_MODEL`; `.env.example` / docs/bootkode-ai.md remain the setup guide. This slice introduces no new AI model, provider setting or queue job.
- The entire requested ecosystem is unfinished. Optional diagnostics, corrected evidence projections, cross-discipline matching/prerequisites/roadmaps, demand intelligence, learner-safe generation and remaining product polish are in the implementation ledger.
