# BootKode overhaul verification

Verification updated 21 October 2026. This report distinguishes implemented changes, current local checks and outstanding release gates. It does not declare the whole application production-ready.

## Original problems and resulting behavior

- Public signup accepted staff/admin roles; guest registration now creates learner accounts and privileged role submissions fail validation.
- Several finance/CBT/certificate management surfaces relied on navigation visibility. Persistent middleware and component boot guards now protect initial requests and Livewire actions; certificate operations retain course ownership checks.
- Catalogue enrollment could bypass payment. Published/approved availability and the existing WalletService now govern enrollment, purchase, both enrollment records and replay behavior.
- Funding and wallet writes were vulnerable to stale balances and duplicate processing. Verified reference/amount/currency/status, locks and transactions now bind intent, ledger, payment state and receipt. Signed webhook delivery works without a browser session; another user's callback reference is rejected.
- Certificate workflow columns were absent. A forward-only migration preserves historical statuses, tokens and files while adding missing fields. Requests recalculate completion server-side, search remains learner-scoped, and missing historical dates are displayed as unrecorded. PDF rendering keeps PHP execution and remote loading disabled.
- Public statistics computed simulated and private data beyond its rendered aggregates. It now queries only real displayed aggregates and publicly available courses.
- Duplicate welcome areas, competing controls and fixed light finance surfaces were consolidated. Learners see continue-learning before secondary metrics. Shared Blade controls, status feedback, dialogs, focus treatment and semantic tokens serve the existing shells.
- Public discovery now supports `/courses` and course summaries before signup; actual prices, categories and content replace the hardcoded sample progress.
- Implicit caching of private course HTML was removed while explicit offline packs remain supported.

## Design and preservation

The palette uses navy `#0f172a`, teal `#0f766e`, mint emphasis `#8fe388` and slate/white surfaces, with Sora headings and Manrope body text. Tokens are in `resources/css/tokens.css`; historical theme variables and five themes remain in `app.css`. Existing logo assets, roles, enrollment/progress history, course-specific splits, commercial workflows and public credential codes are retained.

The initial sidebar/profile edits and ProfileTabMarkupTest were preserved. Commit `9d4892fa` appeared during the work and included those edits alongside accumulated changes; it was retained without rewriting history. A later independent footer tagline edit is intentionally excluded from the final overhaul commits.

## Automated and build evidence

| Check | Actual result |
|---|---|
| Baseline `php artisan test` | 20 failed, 10 passed, 2 skipped; missing factory, outdated account expectations and registration validation |
| Full `php artisan test` | 61 passed, 227 assertions, no skips, 174.52 seconds |
| Final `php artisan test --filter=LearningIntegrityTest` | 8 passed, 30 assertions after the final legacy-date adjustment |
| `npm run build` | Pass; Vite 7.3.7, Tailwind 3.4.19; existing PostCSS `from` warning remains |
| `php artisan view:cache` | Pass |
| `php artisan route:cache`, then `php artisan route:clear` | Pass; no deployment cache left enabled |
| `composer validate --no-check-publish` | Pass |
| `composer check-platform-reqs` | Pass on PHP 8.4.26 |
| Changed PHP `php -l` | 56 files passed at the comprehensive check; subsequent changed PHP checked again before commit |
| `git diff --check` | Pass at final review |

Critical coverage includes auth/verification/password flows, profile updates/deletion/unique email, guest/staff boundaries, public course filtering/search/empty recovery, protected lessons, free and paid enrollment, insufficient balance, replay/mismatch/receipt rollback, callback ownership, stale payout refund models, learner-scoped progress, certificate completion tampering, request idempotency, legacy token/file compatibility, real PNG/PDF output and AI conversation ownership/provider behavior.

The first two AI privacy tests previously skipped because SQLite was absent; they now use the guarded dedicated MySQL test database with the original assertions retained. The full suite never uses the recovered working database.

## Database evidence

The certificate compatibility migration was verified in the dedicated test database before local application on 20 October. The affected working table was backed up first; its record count stayed at 0 and `CHECK TABLE bootkode_recovered.certificates` returned OK. No destructive command was run against working records. On 21 October, all 38 migrations are applied. The migration also avoids redundant enum alteration when rerun.

## Frontend performance

The baseline public JavaScript entry was 1,132.10 KB before gzip. The public entry is now 0.92 KB, notifications 1.31 KB, and the dashboard-only chart/Trix entry 412.39 KB. These are generated asset sizes, not total page weight: Livewire, fonts and applicable external assets are separate. Proven-unused editor/scanner, duplicate Alpine and Tailwind 4/email packages were removed; no frontend framework or major dependency upgrade was introduced.

## Browser evidence

Browser review uses the installed Chrome and its DevTools protocol at 390, 768, 1024 and 1440px, with synthetic learner/admin fixtures only in `bootkode_test`. Persistent file sessions are required for browser login; the automated suite's array session driver does not persist across HTTP requests. Analytics, Flatpickr and MathJax requests are blocked for this representative layout review; datepicker/math functionality is not certified by it.

The completed run checked **52 page/viewport combinations** and four interactions. Public home, catalogue, login, registration, pricing and About pages were checked at all four widths. Learner dashboard, profile, wallet, enrolled courses, lesson view and certificates were also checked at all four widths. Admin payment configuration, financial rules, certificate management and finance dashboard were checked at desktop width.

Every sampled page reached the requested URL with one primary heading, no broken images, no recorded JavaScript exceptions and no document-width overflow. The mobile public menu opened and closed on Escape; desktop collapse reached an 80px rail; dark mode applied its dark surface; an HTML-like notification remained text. These are representative browser checks, not an exhaustive accessibility audit. Screenshots were visually reviewed. The follow-up confirmed a 44px continue-learning target with no 390px overflow. After transitions settled, dark headings were `rgb(248,250,252)` on `rgb(17,24,39)` surfaces; no JavaScript errors were recorded.

Local review artifacts are ignored under `storage/app/coherence-qa`; fixtures are confined to the test database and removed after review.

## Dependency and integration release gates

`npm audit --json` initially reported 27 vulnerabilities, including 2 critical. Compatible updates and unused-package removal reduced this to **8: 5 high and 3 moderate**, in the retained Tailwind 3 build dependency tree. No forced major upgrade was used.

`composer audit --locked --format=json` reports **62 advisories across 19 packages**. `composer update --with-all-dependencies --dry-run` fails because Composer's security policy excludes the available Laravel 11 versions under the current constraint. No policy suppression or advisory ignore was added. A separately reviewed dependency migration is required for release.

Actual Paystack sandbox settlement, transfers/refunds, OAuth, outbound mail, external uploads and live AI calls were not performed. Provider calls in financial tests are faked; no real payment was simulated as successful in production code. Multi-process financial load/concurrency testing remains a staging requirement; row-lock/stale-model/replay tests do not replace it.

Legal terms/privacy documents were not present as published content; fake links were removed from registration and no legal policy was invented. Obtain and publish organisation-approved policies before launch. Screen-reader testing, an exhaustive WCAG audit and individual operational acceptance of all legacy domains remain outstanding. Certificate-template administration still contains legacy preview/configuration behavior and requires its own persistence review.

## Git delivery

Work uses `refactor/bootkode-product-coherence`, not `main`. The baseline is `d034cad3`. Logical changes are committed separately; no force-push, reset, merge or deployment is performed. The implementation commit inventory is below. The final documentation commit follows it; the final handoff reports the exact pushed HEAD. Remote push evidence is appended after verification.

| Commit | Message |
|---|---|
| b62345e9 | docs: audit BootKode product architecture and critical risks |
| 45c2fa4e | fix(security): restrict registration and protect administrative actions |
| 238ba114 | fix(finance): make funding and course enrollment atomic and replay safe |
| 73edf590 | refactor(ui): establish semantic tokens and shared accessible controls |
| c20b85c2 | feat(courses): add factual public discovery and unify publication rules |
| e656869a | refactor(auth): simplify onboarding and verify account changes |
| 0762c6ba | refactor(layout): focus learner workspace and unify accessible feedback |
| dab1ed2d | refactor(finance): unify operational surfaces and show unavailable states |
| 0b746f46 | fix(finance): enforce payout rules and prevent repeated refunds |
| f47b4d0c | fix(learning): enforce credential eligibility and isolate private progress |
| 9d4892fa | feat: add global notification system and dashboard chart integration |
| 969086a4 | fix(certificates): preserve legacy records and secure asset rendering |
| 2ecaefef | fix(ui): finish theme tokens and accessible learning actions |


A draft PR could not be opened through the connected GitHub app because it returned `UNAUTHORIZED` and requires reauthentication. Git transport is independent; no credential was extracted to work around that boundary. `docs/pull-request.md` contains the prepared review description. Reconnect GitHub or open the branch comparison to create the PR; no merge is authorized.

## Verified GitHub delivery

On 21 October 2026, `637eb3c27db90da6fc135529bee09bc02bf5b94b` was pushed to `Dayoebe/bootkode`, branch `refactor/bootkode-product-coherence`, and `git ls-remote --heads origin refactor/bootkode-product-coherence` returned that exact SHA. HTTPS push lacked credentials; the ordinary SSH push succeeded without changing the configured remote or force-pushing. This final delivery note is a subsequent documentation-only commit, whose pushed SHA is reported in the handoff.

The working tree retains only the independently edited footer tagline. Synthetic browser fixtures were removed (remaining fixture users: 0). The original application server remains available; the owned QA server and browser are stopped. No environment file, payment credential, user record dump or test artifact was committed.

Additional documentation commits: `637eb3c2 docs: record verification evidence and release gates`, followed by `docs: record GitHub delivery verification`.

## Further implementation and verification, 21 October 2026

The first handoff did not finish all feasible work in the original request. This continuation closes additional concrete defects rather than treating the first core-journey delivery as the whole product.

### Resulting behavior

- Certificate templates now persist, edit, preview, delete and switch defaults. Administrator checks run at the service/component boundaries, styles/fonts are validated, and only super administrators assign defaults. An issuance snapshot in existing certificate metadata preserves each approved design after later edits, default changes or template deletion. Historical certificates retain the old rendering and codes/files. Preview content is explicitly unissued and isolated in a sandboxed iframe.
- Both career foreign keys incorrectly referenced the queue `jobs` table. A forward compatibility migration checks all affected records before changing either relation to `job_portal`. The test suite verifies real career applications/saves and refusal to discard unmatched records.
- Career administration now checks roles on HTTP and Livewire requests. Discovery carries no other applicants or private poster email. Applications re-resolve eligibility, reject closed/private/external-method submissions, and create the record/counter atomically with retry protection. Failed persistence rolls back the record and removes a new upload.
- New résumés are stored privately with applicant/admin download checks. Historical public paths retain compatibility; moving any historical public assets remains an explicit backup/migration task. Both affected working career tables were empty at inspection.
- Job-detail save/apply buttons call actual supported actions. Email/phone/external applications use validated destinations; unsupported URL schemes are rejected. Quick apply opens the real form rather than fabricating a cover letter or attachment.
- Community moderation reads/mutations check administrator access. Forging a tab cannot reveal private feedback/users; account actions protect self and higher-privilege accounts. Named community routes open their corresponding tabs.
- Code solutions remain pending with no invented grade. Existing reviewed submissions cannot have their scores cleared by resubmission. The real solve button opens a submission form; a missing difficulty/points metadata record now renders safely without fabricated values.
- Shared headings, feedback, bounded tab navigation, labelled community forms, confirmations and dialog focus/Escape behavior extend to these operational routes. Job alerts and moderator replies truthfully state their unavailable delivery workflows.

### Final checks

| Check | Actual result |
|---|---|
| `php artisan test` on the final continuation source | **88 passed, 374 assertions**, no skips, 46.23 seconds |
| Focused operational tests before the last browser-discovered fix | 26 passed, 136 assertions |
| Final community/link focused checks | 7 passed, 53 assertions; includes nullable challenge metadata and the real submission form |
| `npm run build` | Pass, 5.94 seconds; CSS 280.86 KB, public JS 0.92 KB, dashboard JS 412.39 KB; existing PostCSS `from` warning remains |
| `php artisan view:cache` | Pass |
| `php artisan route:cache`, then `php artisan route:clear` | Pass |
| Changed/new PHP syntax | 18 files passed |
| `git diff --check` | Pass |

Browser evidence adds **20 page/viewport samples**: learner careers/community and administrator templates/jobs/moderation at 390, 768, 1024 and 1440px. Every sample reached its requested path, had one primary heading, and had no document-width overflow, recorded JavaScript exception or error page. Sixteen additional tab transitions covered community, learner careers, job administration and moderation; code submission and Escape were also exercised. A separate **seven-check follow-up** verified template focus trapping, Escape/focus return to Create template, actual browser save/edit/preview, and the job-detail save/apply controls at 390px. Preview and editor remain mutually exclusive. Screenshots of the sampled mobile/desktop pages were visually reviewed.

The initial template-editor timing probes ran before the asynchronous dialog opened; those probes are not accepted as focus evidence. The explicit wait-for-open follow-up confirmed trapping and focus return. Earlier harness interruptions (Node WebSocket flag, snapshot selection and resource timeouts) are also excluded from pass counts. Browser interaction exposed the nullable challenge metadata crash; it was repaired and covered by regression tests before the final full run.

The owned browser was reduced to a single tab after the workstation exhausted memory/swap. External analytics, avatar, icon/font, datepicker and MathJax requests were blocked for the representative layout review; this run does not certify those services/assets. Existing earlier browser evidence remains historical evidence, not a new certification of all legacy routes.

Browser data used a separate `bootkode_browser_test` database; automated tests used `bootkode_test`. Both satisfy the test-database guard. No fixture was added to the working database. The two existing career tables were backed up under ignored `storage/app/db-restore` before migration; their counts stayed 0. `CHECK TABLE` returned OK for both and `certificate_templates`, and both foreign-key targets now read `job_portal`. No historical certificate data was rewritten by the new template-table migration. Both migrations have been applied locally. Synthetic browser users/designs were removed; remaining review users: 0. The owned QA server/browser were stopped and the original port-8000 server was preserved.

During the continuation, independent user commits `85b6c338` (password visibility) and `fb5df279` (footer tagline) appeared on the feature branch. They were preserved without history rewriting. The earlier statement about an uncommitted footer described the first handoff; it is superseded by these subsequent commits.

### Scope still outstanding

This is further implementation, not certification of the complete original request. Dependency release gates, real external-provider acceptance and approved legal content remain. Institutions, marketplace, mentorship and other legacy operations still need their own action/ownership and interaction acceptance. Job-alert delivery and moderator-response delivery have not been built. Historical public résumé files, if imported from another environment, must be backed up and moved before treating them as private. No merge/deployment, production financial transaction, exhaustive screen-reader/WCAG certification or complete legacy-domain acceptance is claimed.

### Continuation commit inventory

- `b85b3b3b fix(certificates): persist designs and preserve issued snapshots`
- `8fd112cf fix(career): secure applications and repair job relationships`
- `df95c344 fix(community): enforce moderation access and real solution submission`
- `build(ui): compile shared operational interface styles`
- `docs: distinguish operational fixes from whole-product acceptance`

Final remote SHA verification is reported in the handoff after the ordinary feature-branch push. No merge or deployment is performed.
