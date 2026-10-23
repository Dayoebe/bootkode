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

## Institution, marketplace and mentorship continuation, 21 October 2026

This continuation implements additional domain boundaries and interfaces; the complete original product request remains open for the domains and integrations listed below.

### Resulting behavior

- Institution operations enforce platform administration on each HTTP/Livewire request. A school administrator receives an active institution membership, not a global academy role. New accounts remain unverified students until the real verification flow completes. Invitation GET requires sign-in and reviews only; CSRF-protected POST checks recipient identity, pending/expiry state and capacity under locks. Wrong-account, revoked, expired and replayed invitations are rejected. Existing global roles are not silently demoted. The school administrator portal reads only their own active administrative memberships; cross-institution operations remain with platform staff.
- Cohort assignments validate active membership in the selected institution. Enrollment services and CSV jobs recheck canonical course availability; CSV role values cannot grant global staff privileges. Working institution, invitation, enrollment and certificate records are preserved.
- Marketplace wallet checkout resolves the session cart, published items, prices and commission on the server. Customer/wallet/order locks, matching debit evidence, minor-unit splits, matching settlement currency, payout replay protection and invoice persistence bind the operation. Full wallet refunds reverse actual credited wallets before restoring customer funds and invoice state; failed recovery rolls everything back. Card payment and partial/external refund settlement are not manufactured by this boundary.
- Seller tools follow existing seller eligibility on every request. Nested platform/content operations retain their respective role gates. Named marketplace destinations now open the corresponding cart, listings, drafts, business orders/withdrawals, administration, reviews or settings section. Ownership searches group their OR clauses beneath the customer/vendor predicate.
- Paid files use private storage and actual paid-customer downloads. Listing images/files now persist, category reads use the pivot relation despite the retained JSON column, failed saves clean up new uploads, and editing metadata preserves existing files. Soft-deleted purchased items retain download compatibility. Public models hide file paths; public review queries include approved reviews only, and selected products recheck publication after Livewire hydration. Cached featured products also recheck publication. Internal administration notes are excluded from customer purchases.
- Mentorship actions requery actual participants and mentor permissions. Paid completion transfers existing NGN funds from the mentee to mentor atomically, records paired ledger IDs and prevents repeated payments/session counters. Insufficient funds roll back completion. Verification remains inside grouped mentor search conditions. Mentor-only parent/nested components enforce roles on each request.
- Real mentor profiles serialize valid accessors/star counts, and historical missing dates render as unrecorded. Rates display NGN consistently. Root headers/feedback and bounded mobile navigation extend to the three domains; nested mentoring panels keep secondary headings. Profile controls open the actual editor. Unpublished mentoring resources have no dead download links. Action feedback in shopping/sessions remains visible through subsequent Livewire refreshes instead of depending on a transient session flash.

### Automated evidence

| Check | Actual result |
|---|---|
| Full `php artisan test` | **123 passed, 514 assertions**, no skips, 47.46 seconds |
| `npm run build` | Pass, 6.21 seconds; CSS 279.11 KB, public JS 0.92 KB, dashboard JS 412.39 KB; existing PostCSS `from` warning remains |
| `php artisan view:cache` | Pass |
| `php artisan route:cache`, then `php artisan route:clear` | Pass |
| Changed/new PHP syntax | **53 files passed** |
| `git diff --check` | Pass |

New regression cases cover invitation ownership/expiry/replay/no automatic login or global promotion, institution membership scope/course eligibility, nested administrator/seller/mentor access, canonical checkout prices, atomic invoice rollback, replay-safe payment and refund reversal, insufficient refund recovery, exact small-amount splits, currency mismatch rollback, customer search isolation, internal-note privacy, paid-owner private downloads after soft deletion, upload persistence/edit retention, public review/publication scope, mentorship privacy, unfunded rollback, stale completion replay, real profile/editor rendering, missing dates, verified search and persistent action feedback.

Automated tests use guarded `bootkode_test`; browser fixtures use separate `bootkode_browser_test`. No schema migration or working-database mutation was needed for this continuation. Previous migration/backups remain historical evidence, not newly performed operations.

### Browser and ledger evidence

Completed Chrome records cover **100 distinct role/page/viewport combinations** across learner discovery/purchases/mentorship, platform institution/marketplace/mentor administration, the scoped institution-member portal, and mentor profile/resources/sessions/selling. Widths are 390, 768, 1024 and 1440px. Affected samples were repeated after fixes; learner seller pages sampled before the eligibility guard are excluded and replaced with authorized mentor samples. All accepted samples reached the requested path with one primary heading, no recorded JavaScript exception/error page and no document overflow.

The final action run passed **23 checks**: five dialog keyboard checks, six marketplace-system tabs, six mentor-dashboard tabs, three business views, cart add/checkout and mentor completion. Together with the 14 institution/marketplace root-tab transitions, this supplies **37 interaction checks**. Each sampled dialog moved/trapped focus, dismissed on Escape and returned to the originating action. The first focus-return probe incorrectly depended on a temporary DOM data attribute removed by Livewire; the final probe uses the actual action identity. Earlier failed/partial probes are excluded from pass counts.

Two isolated fixture runs independently persisted one paid ₦100 marketplace order and a paid mentorship session with paired ₦100 debit/credit. Each learner balance moved from ₦1,000 to ₦800. The final run also visibly retained both success confirmations. Populated cart, customer purchases and vendor orders were repeated at 390px after fixing layouts that empty-state sampling had missed. Six further populated mobile/desktop screenshots were captured and reviewed. Screenshot review then identified undefined mentoring color utilities that left controls/metrics unreadable; these were replaced with shared button/surface styles and received a fresh build and visual check.

The final mentoring style check repeated **12 mobile/desktop page samples** with no overflow, error page, duplicate heading or recorded JavaScript exception. The representative primary control measured 45.6px tall with sampled foreground/background contrast of 5.47:1 in light mode and 6.21:1 in dark mode. These limited measurements do not certify all controls or themes.

One larger browser process stopped when workstation resources were exhausted; completed records were preserved and missing checks resumed in fresh, smaller runs. External analytics, fonts/icons, avatars, datepicker and MathJax requests were blocked for this review. Missing CDN icon glyphs in screenshots are excluded from visual acceptance; external assets, datepicker/math behavior, screen-reader use and an exhaustive WCAG audit are not certified. Local artifacts remain ignored under `storage/app/coherence-qa`.

### Remaining scope and release gates

The earlier dependency audits were reviewed: **8 npm findings (5 high, 3 moderate)** and **62 Composer advisories across 19 packages** remain under the retained major constraints. Dependencies were unchanged in this continuation; these are recorded audit results, not a new advisory feed scan. No suppression, forced upgrade, deployment or real provider charge was used.

Actual gateway settlement/transfers/refunds, OAuth, outbound mail and live AI still need independent acceptance. Institution billing/white-label delivery, unpublished mentor resources, job alerts/moderator replies and other legacy integrations remain incomplete. Historical public paid files, résumés and mentorship attachments require a backed-up private-storage migration before claiming historical-file privacy. New legacy mentorship attachments use private storage, but a complete attachment delivery workflow is not certified here. Previously over-granted institution global roles require an administrator review; no existing role was automatically removed.

Affiliates, messaging/support, instructor authoring and other legacy domains still require action-level and interaction acceptance. This continuation is substantial additional implementation, not whole-product completion. The updated route inventory records current contracts. No working-database record was created or modified for review.

Known synthetic domain users were removed from the browser test database (remaining: 0). Only the owned port-8011 QA server and headless browser were stopped; the original port-8000 application remains running and returned HTTP 200. No environment file, provider credential, private fixture or browser artifact is included in the commits.

### Domain continuation commits

- `a3cd24a2 fix(institutions): scope membership and secure invitation acceptance`
- `cf18fc97 fix(marketplace): bind orders to atomic wallet settlement and private delivery`
- `657dec87 fix(mentorship): enforce participant scope and funded session completion`
- `dcf2cdec build(ui): compile institutional and mentoring interface updates`

The documentation commit records this evidence and remaining scope. The exact pushed HEAD and remote comparison are reported in the handoff after ordinary feature-branch transport; no merge or deployment is performed.

## Support, authoring and affiliate continuation — 22 October 2026

This section supersedes the earlier lack of action-level acceptance for these domains. It adds implementation and evidence; it does not certify the whole product.

### Automated and build checks

- Final `php artisan test`: **154 passed, 651 assertions, 99.74s** on guarded `bootkode_test`.
- Final `npm run build`: passed in **6.42s**; CSS **274.47 KB**, public JavaScript **0.92 KB**, dashboard JavaScript **416.67 KB**. The existing nonfatal PostCSS `from` warning remains.
- `php artisan view:cache`, `php artisan route:cache` followed by `php artisan route:clear`, changed-PHP syntax checks (**65 files**) and `git diff --check`: passed. `node --check resources/js/dashboard.js` also passed.
- Fresh `npm audit --json`: **8 findings (5 high, 3 moderate)**. Fresh `composer audit --format=json`: **62 advisories across 19 packages**. Neither dependencies nor advisory policy were changed. These remain release gates under the retained major constraints.

New regression coverage checks private support/FAQ/feedback search, staff mount/action denials, real private upload and owner download, actual response persistence/status validation, participant-scoped message polling/sending, and forged private-course feedback selection. Authoring cases cover nested component role/ownership checks, cross-course mutations, publication eligibility, protected upload arguments, locked metadata, real document persistence, retained physical files, history-preserving course/lesson/account deletion, role retention and issued verification codes. Affiliate cases cover funded matching debit/split snapshots, exact small amounts, replay-safe credits/counters, configuration authority, banner publication, restricted API payloads, corrected relations/net course analytics and real overview report export. Marketplace regression checks named published product/vendor routes and unpublished 404 behavior.

An intermediate account-deletion check caught normal logout recreating a deleted model by saving its remember token. The final code uses logout without token persistence after successful deletion; blocked removal retains authentication, avatar and learning records. Earlier failures and browser probes are excluded from final pass counts.

### Browser and persistence checks

Final accepted layout records cover **88 distinct role/page/viewport combinations**: learner (support, feedback, messages, course preview, marketplace detail), platform administrator (support/feedback/FAQ, affiliates, course listing/QA), instructor (create/edit/builder/listing/reviews/analytics), and affiliate (dashboard/tools/history/reports/analytics), at **390/768/1024/1440px**. Affected preview, builder, tools and analytics samples were repeated after fixes. Accepted samples reached their intended page with one primary heading, no recorded JavaScript exception/error page, and no document overflow.

The final interaction records add **30 checks**: six learner support/message checks; four staff response/dialog checks; eight authoring/editor checks; eight affiliate tool/banner/clipboard/export checks; and four share-dialog/response-visibility/clipboard follow-ups. The final three sampled dialogs moved/trapped focus, dismissed on Escape and returned focus. Clipboard checks require visible feedback from the actual browser promise, including a truthful denied-access recovery state; no copied email was sent. A real SVG banner returned HTTP 200, and the downloaded overview CSV contains the actual synthetic course, learner, paid status and **₦6.00** commission.

Browser-driven draft creation persisted a new draft and redirected into its builder. Creating a lesson initially exposed a dynamically mounted editor whose inline function never ran. Moving its behavior to `dashboard.js`, using standard Livewire property updates and cleaning up listeners fixed the observed failure. The final run created sections/lessons, switched content/media/settings/assessment tabs and saved real Trix content. Empty and populated fixture editor states were sampled; exhaustive question-type/editor acceptance remains outstanding.

A separate guarded database read verified the actual submitted ticket, resolved support response, responded feedback, participant message and saved lesson content. The real test course purchase debited the learner from **₦1,000 to ₦900** and recorded **₦6** affiliate earnings from the original platform allocation. Replay/rollback/split boundaries have automated coverage; this is internal-wallet evidence, not external settlement.

Screenshot review covered representative support, affiliate tools and course share-dialog states. Visual inspection found narrow controls missed by document-width measurements, and prompted the wrapping/input-width fixes. Analytics/fonts/icons/avatars/datepicker/MathJax/third-party QR requests were blocked for isolated review. Missing external icon glyphs, the QR service, outbound mail, all themes, screen readers and exhaustive WCAG acceptance are not certified. Browser artifacts and private fixtures remain ignored under `storage/app/coherence-qa`.

### Data preservation and remaining work

No working-database schema or records were changed; no migration was required. Browser records were created only in `bootkode_browser_test`. Known synthetic users, their dependent records, category and FAQ were removed afterward (**0 known fixture users remaining**). Only the owned 8011 QA server and 9222 headless browser were stopped. The existing 8000 application and 5173 development server were preserved; the application returned HTTP 200.

New support/feedback attachments are private; historical public files still require a backed-up migration. Removing a lesson file reference deliberately preserves the physical asset. History-linked account deletion now requests support handling; a complete anonymization/archival workflow is separate work. Affiliate settings accurately show server configuration instead of claiming environment writes. Failed/legacy affiliate settlements require administrator review and are never represented as paid. Provider/OAuth/live-AI/mail acceptance, legal content, historical attachment migration, broader analytics/gamification/editor acceptance and the recorded dependency gates remain outstanding. No deployment or whole-product production-readiness claim is made.

Continuation commits:

- `c0d98c0f fix(support): protect private requests and connect response workflows`
- `822ef5b8 fix(courses): secure authoring and preserve learning history`
- `b7d8be31 fix(affiliates): settle verified commissions and restore real reporting`
- `27b4540a fix(marketplace): mount published detail routes and align review access`
- `b90f3277 build(ui): compile course editor and operational interface fixes`

The documentation commit records the evidence and outstanding scope. Ordinary feature-branch push and exact remote HEAD comparison are reported in the handoff; no merge or deployment is performed.

## Recorded learning, rewards and CBT — 22 October 2026

Further inspection found actionable defects beyond the preceding handoff. This continuation implements and verifies those paths rather than treating all remaining work as external setup.

### Final checks

- `php artisan test`: **178 passed, 767 assertions, 95.88s**, using guarded `bootkode_test`.
- `npm run build`: passed in **11.90s**. CSS is **270.27 KB**, public JavaScript **0.92 KB**, dashboard charts/editors **417.46 KB**. The existing nonfatal PostCSS `from` warning remains.
- `php artisan view:cache`, `php artisan route:cache` followed by `php artisan route:clear`, `node --check resources/js/dashboard.js`, changed-PHP syntax checks (**20 files**) and `git diff --check`: passed.
- Dependency files are unchanged. The fresh audit from the preceding same-day continuation was reviewed: **8 npm findings (5 high, 3 moderate)** and **62 Composer advisories across 19 packages**. These remain release gates; no suppression or major upgrade was applied.

New automated cases cover daily reward replay across stale models/requests; negative/stale virtual balances; persisted quest completion and once-only rewards; consecutive streak/gem replay; lesson evidence; reward-store rollback/replay; opt-in/private leaderboard scope and withdrawal; deduplicated owned analytics; real goals/CSV; named reward routes; and truthful legacy service export/unavailable behavior. CBT cases cover course enrollment/access, answer-key exclusion, locked client metadata, persisted attempt reuse, duplicate submission, unstarted/cross-user/forged-answer denial, server expiry, saved answers surviving reload, maximum attempts, true/false grading, rollback, private grading snapshots and question edits after an attempt starts.

### Browser and database evidence

Accepted layouts cover **32 distinct learner/page/viewport combinations**: five reward pages, both historical personal analytics URLs and the CBT page at **390/768/1024/1440px**. The four CBT samples were repeated after removing a duplicate hidden warning heading; completed results also retained one primary heading. Accepted records had no document overflow, error page or recorded JavaScript exception. Pre-exam, active exam and result states received representative interaction/screenshot review; this does not certify every exam state at every width.

The continuation supplies **22 accepted interaction checks**: 20 passing checks for daily claim/replay, all reward tabs, explicit leaderboard participation, supported redemption/replay, four chart periods, goal persistence, a real activity CSV, persisted exam start/answer, confirmation focus/Escape/return and analytics reading the completed grade; plus two passing follow-ups for completed result reload and exclusion of grading keys from the browser snapshot. One initial result probe expected the literal word “Passed” while the interface displayed “Congratulations!”; that failed probe is excluded. The later check verified the displayed 100% result and persisted completion. Tab trapping is supplied by the existing shared modal; the new exam probe explicitly verified focus, Escape and return.

A separate guarded database read confirmed **60 points, 52 coins, 4 gems, one daily reward, one redemption, two saved goals (45 daily minutes / 8 weekly lessons), one answer row and a 100% recorded exam result**. Personal analytics independently read the same 100% result and one enrolled course. A private fixture with higher points remained excluded from the public leaderboard. The CSV contains recorded daily lesson counts and session minutes. No estimated study duration, random game score, fabricated badge or external payment was used.

Both analytics URLs now use the same authorized implementation. Recorded CBT result snapshots take precedence over changed question definitions. Charts come from the existing dashboard bundle and were exercised through filter changes; a bounded data table supplies the same values. Screenshots of rewards, analytics and the exam were reviewed. Third-party analytics/fonts/icons/avatars/datepicker/MathJax/QR resources remained blocked in isolated review; external glyphs/services, all themes, screen readers and exhaustive WCAG acceptance are not certified.

### Preservation and remaining limits

No schema migration or verification record was written to the working database. Fixtures used only `bootkode_browser_test`; the three known synthetic users and dependent records, reward item and category were removed afterward (**0 known fixture users remaining**). Only the owned port-8011 QA server and port-9222 headless browser were stopped. Existing port-8000 and port-5173 processes remained; the application returned HTTP 200.

Stored badges, scores, reward purchases, enrollments, attempts and financial history are preserved. Possible historical duplicate gamification rows are not automatically merged or deleted. The oldest row is canonical for these flows, and future writes serialize on the account. Course/account deletion also protects learning sessions and assessment answers. Standalone CBT retains its authenticated global contract; course-linked exams require enrollment, ownership or platform authority. The current CBT player supports multiple-choice and true/false auto-grading; other question types require a separate grading workflow and are refused before recording an attempt.

No verified game implementation is published. Unsupported reward delivery, special-event awards, achievement broadcasting, reviewed learning-path generation and PDF analytics exports do not report fabricated success. Broader manual grading, historical attachment migration, provider/OAuth/live-AI/mail acceptance, legal content, dependency updates and exhaustive accessibility/theme acceptance remain outstanding. This continuation closes additional real defects; it is not a whole-product production-readiness claim.

Commits:

- `84e52d0a fix(learning): secure rewards and unify personal analytics`
- `83f2d416 fix(cbt): persist authorized attempts and protect grading snapshots`
- `7a243391 build(ui): compile recorded learning and exam interface updates`

The documentation commit records these results. Ordinary branch push and exact remote HEAD verification are reported in the handoff; no merge or deployment was performed.

## Written assessment, owned results and immutable exam-clock continuation — 22 October 2026

### Findings and implementation

The lesson player used a second grading path that exposed answer keys, accepted unrelated assessment IDs, ignored attempt limits and deleted historical answers. Written work could appear scored without a reviewer. CBT management exposed other instructors' resources and participant records, and its clear actions removed real grades. Personal result views loaded unrelated definitions, recalculated historical scores from edited questions and had a broken named summary handler.

The embedded lesson panel and new `lesson.assessments` workspace now use `CbtAttemptService`. Drafts survive reload, the server owns the attempt/deadline, answer shapes/ownership/length are validated and completed submissions replay their recorded result. Multiple selection requires the exact correct set. Written submissions stay pending without a published percentage/pass until all written answers are marked. Missing pass-mark configuration cannot start an attempt or turn a legacy zero score into a pass. Unsupported interaction formats are refused before recording a new attempt.

The new Assessment Reviews queue resolves actual course ownership/platform authority, rejects self-grading and unrelated answers, and bounds marks by the private original rubric. Marks, reviewer/time/feedback, private audit log, final result and eligible rewards commit together. Partial review stays pending; an identical replay returns the recorded result and a changed mark requires formal correction. New lesson progress, lesson rewards and certificate eligibility read actual reviewed results. Parent and nested lesson requests recheck enrollment, publication, course membership and recorded section progress. Historical issued credentials and completion records are not revoked.

Owned result pages retain attempt history, original question/option snapshots, the learner's submitted responses, recorded marks and instructor feedback while excluding grading keys. The shared response projection prevents later question edits from changing what the learner sees. Instructor CBT CRUD, ordering, course choices and participant data are scoped; standalone attempts also prevent deletion of their assessments/questions. The historical summary URL now mounts the owned result component.

### Final automated and build evidence

- `php artisan test`: **197 passed, 865 assertions, 166.46 seconds**; the full guarded MySQL suite includes **19 new assessment regression cases**.
- `npm run build`: passed in **14.20 seconds**; application CSS **256.83 KB**, dashboard CSS **17.91 KB**.
- `php artisan view:cache`, `php artisan route:cache` followed by `php artisan route:clear`: passed.
- `php -l` on **19 changed/new PHP files** and `git diff --check`: passed.
- `php artisan route:list --json`: **283 registered contracts** recorded in the updated inventory.

Production build, Blade cache, route cache/clear, changed-PHP syntax and `git diff --check` passed. Public JavaScript remains 0.92 KB and dashboard JavaScript 417.46 KB. The shared semantic navy/teal tokens, Sora/Manrope typography, existing Blade controls and trapped dialogs are reused; no dependency or framework migration was added. Build warnings about Browserslist data and a PostCSS `from` option remain pre-existing limitations, not suppressed findings.

The new assessment cases cover pending results/rewards/progress/certificate denial; cross-course and revoked enrollment requests; unpublished/locked lesson access; malformed and oversized responses; private question payloads/locked metadata; authorized bounded partial/final review; self/other-instructor/unrelated-answer denial; immutable marks and idempotent review; full rollback on reward failure; saved drafts/maximum attempts/multiple selection; owned summary routes; instructor CRUD scope/history retention; original response snapshots and keys; missing pass configuration; and immutable clocks across saving, expiry and grading. Existing CBT, financial, ownership and learning cases remain enabled.

Fresh dependency audits were reviewed: `npm audit --json` reports **8 findings (5 high, 3 moderate)**; `composer audit --format=json` reports **62 advisories across 19 packages**. Dependency manifests/locks are unchanged. No advisory suppression or forced major upgrade was used.

### Browser and database evidence

Accepted checks cover **32 distinct page/state/viewport samples at 390/768/1024/1440px**, plus **23 workflow/privacy/dialog checks**. The layouts include the lesson assessment list, active written workspace, instructor queue, CBT management, personal result list, embedded course lesson, written response result and selected-choice result dialog. Four repeated queue samples are excluded from the distinct count. Accepted samples have one primary heading, bounded document width, no error page and no recorded JavaScript exception. Screenshots of the learner/reviewer/embedded/result states were inspected.

Native text input saved both written responses; a reload resumed them. Native checkboxes recorded an exact-set passing result. The owning instructor marked real responses through the form; partial review remained pending and final review published 70% with feedback. Learner results and personal analytics read the same records. Submission and instructor-review dialogs were checked for focus, Escape and return. Other instructors could not discover the managed CBT fixture or queue data; direct forbidden-action behavior is additionally covered by automated tests. Final response-card review verified the original selected options and written responses at all four widths without private rubric data.

A separate guarded database read confirmed three actual graded attempts (70%, 100%, 70%), five answer rows, real reviewer/feedback audit entries and two quiz-point transactions totaling **100 points**. Passing the same assessment twice did not duplicate its reward. Analytics counted three complete graded attempts with an 80% average. This was the interactive cohort; the later response-layout cohort used the actual services to prepare its fixtures and is not counted as another native marking-flow exercise.

Initial harness selector/regular-expression probes and an incomplete fixture seed were corrected and excluded from accepted evidence. The full suite also exposed an initial aggregation variable error and a route-parameter boot guard error; both were fixed before the final passing run. Third-party analytics/fonts/icons/avatars/datepicker/MathJax/QR assets were blocked in isolated browser review. External glyph/services, every editor/theme, screen readers and exhaustive WCAG acceptance are not certified.

### Migration and preservation evidence

Database inspection found `learning_sessions.started_at` had an implicit MariaDB `ON UPDATE CURRENT_TIMESTAMP` rule in the working and dedicated databases. Saving answers or grading could overwrite the start with database current time, resetting the deadline and producing misleading duration values. Forward migration `2026_10_22_020000_preserve_learning_session_start_time` changes only the column definition to an explicit `DEFAULT CURRENT_TIMESTAMP` without automatic updates.

The generated SQL was inspected with `migrate --pretend`; the migration ran in `bootkode_test` and `bootkode_browser_test` first. Private schema/full-row backups were then retained under `storage/app/private/backups` with mode 0600. The browser table's two existing rows matched their pre-migration backup field-for-field. The working `bootkode_recovered` table contained **0 rows**, was backed up before the isolated forward migration, and still contained 0 afterward. The affected records were unchanged; no fresh/reset command or fixture insertion ran on that database.

A subsequent real browser attempt kept its original **11:28:54 UTC** start through autosave, submission at **11:28:59 UTC** and final grading at **11:29:07 UTC**. Its recorded elapsed time was five seconds and its configured deadline survived reload. The automated time-advance case also verifies expiry uses the original clock and late answers cannot replace saved responses. The migration preserves historical values; it cannot reconstruct timestamps already overwritten before the fix, and rollback deliberately does not restore the unsafe rule.

### Cleanup and remaining scope

All known synthetic accounts, courses, lessons, sections, assessments, answers, sessions and polymorphic role links from these review cohorts were removed from the dedicated browser database. Cleanup explicitly handles the lesson foreign key's `SET NULL` behavior rather than assuming a course delete removes lessons. The private backups remain retained. Only owned QA processes were stopped; normal application/development listeners were preserved and the application returned HTTP 200.

New attempts support multiple choice, true/false and reviewed text responses for short-answer/essay/fill-blank/assignment/project/Q&A questions. Matching/ordering/drag-drop interaction, formal correction/appeal history and backed-up reconciliation of legacy written answers without attempt snapshots remain outstanding. The queue does not invent attempt sessions for legacy data, regrade issued credentials, reconstruct historical clocks or claim outbound mail delivery. Prior provider/OAuth/live-AI/mail, historical public-file migration, legal, dependency and exhaustive accessibility/theme gates remain open. This continuation is not a whole-product production-readiness claim.

Commits for this continuation:

- `53709a2d feat(assessment): add authorized reviews and retain attempt history`
- `48b0b212 build(ui): compile assessment workspace and result surfaces`

The documentation commit records the accepted evidence and remaining gates. The handoff reports ordinary branch push and exact remote HEAD verification; no merge or deployment is performed.

## Structured questions, scoped authoring and keyboard focus — 22 October 2026

### Findings and delivered behavior

The next inspection found matching questions could be saved with no usable pairs; multiple-choice writes double-encoded JSON and filtered blank options without preserving marking indices; maximum-attempt controls were not saved; and assessment copying/reordering used unscoped IDs. Question statistics recursed through their own accessor. Legacy correctness helpers sorted sequence answers and attempted automatic written-answer marking. The new keyboard check also found focus falling back to the page when Livewire moved a row.

The lesson authoring workspace now provides complete matching, ordering and drag-and-drop forms. Choices retain their indices, including the literal option `0`. Complete unique structured items and valid private keys are required, arrays serialize through model casts once, and maximum attempts persist through the existing field. The contextual named authoring route reuses the same lesson manager. Copies and exact-list reorders resolve actual owned lesson/assessment records; copying all questions is atomic and copies no learner history. Loaded identifiers/definitions are locked in Livewire. Three unused assessment partials were removed after a repository reference search.

Learners use labelled matching lists, native draggable choices backed by those same lists, and sequence buttons with explicit save. Public structured projections omit the original-index permutation; it remains in private attempt grading metadata. Draft positions survive reload and deadline handling. Submission maps to original positions and awards exact credit; incomplete/repeated/wrong matches receive zero, invalid sequences are rejected, and untouched controls do not invent answered questions. Mixed written work still requires manual review. Original question/target/choice labels and marks remain stable after edits. Authorized reviewers can also read the private written reference answer.

The legacy model helpers now enforce exact sets/sequences and refuse automatic marking of written work rather than reporting unconfigured partial credit. No existing answer or credential is automatically regraded. Statistics read actual counts without recursive accessor calls. A small handler in the existing dashboard JavaScript restores focus to the moved learner/author item after Livewire changes the DOM; no new Alpine runtime, sorting dependency or component system is introduced.

### Final automated/build evidence

- `php artisan test`: **215 passed, 979 assertions, 100.31 seconds**. The full guarded MySQL suite includes **18 new structured-question/authoring regression cases**.
- `npm run build`: passed in **7.84 seconds**. Application CSS **256.59 KB**, dashboard CSS **17.91 KB**, public JavaScript **0.92 KB**, dashboard JavaScript **417.87 KB**.
- `php artisan view:cache`, `php artisan route:cache` followed by `php artisan route:clear`: passed.
- `php -l` on **16 changed/new PHP files**, `node --check resources/js/dashboard.js`, and `git diff --check`: passed.
- `php artisan route:list --json`: **284 contracts** recorded in the updated inventory.
- Fresh `npm audit --json`: **8 findings (5 high, 3 moderate)**. Fresh `composer audit --format=json`: **62 advisories across 19 packages**. No dependency manifest/lock changes, suppression or forced major upgrade was applied. Existing Browserslist/PostCSS build warnings remain.

New cases cover every structured format, private permutation exclusion, exact grading and reward replay; incomplete/repeated/invalid/nested answers; saved positions and server expiry; immutable edited definitions/results; malformed legacy setup; untouched controls; sequence movement; atomic submission/copy rollback; author create/edit/copy and array persistence; option removal/zero/index integrity; attempt-setting persistence; cross-lesson copy/reorder denial; locked client definitions; exact ordering/statistics; custom boolean labels/private reference scope; and legacy helper consistency. Existing authorization, financial, learning and manual-grading tests remain enabled.

### Browser and database evidence

Accepted browser evidence covers **28 distinct state/viewport samples** and **21 workflow checks**, with no accepted error page, document overflow or recorded JavaScript exception. The seven states at 390/768/1024/1440px are the author list, assessment form, matching form, ordering form, drag/drop form, active learner workspace and learner result. Screenshots of mobile/desktop authoring, matching, sequence/drop controls and original response results were inspected.

Native assessment submission saved a one-attempt quiz. Native question forms saved all three structured definitions, edits survived a full remount, and copies/reorders persisted. A different instructor's actual authoring request returned 403. Learner controls saved matching selections and arranged the sequence; Chrome's pointer drag interception and native drop dispatch moved the selected choice onto its target. The drop alternative remained labelled and usable after reload. Submission recorded a real **100%, 9/9** result with original submitted-response text; repeated submission did not duplicate rows/reward. The shared confirmation dialog retained Escape/focus-return behavior.

Physical Enter-key activation was verified for learner sequence moves, question moves and assessment moves. Each final check confirmed both changed order and focus remaining on the moved item's enabled control after the DOM update. Initial probes that assumed every partial array serialized as a JavaScript array, or sent an incomplete keyboard event, were corrected and excluded. Semantic position comparison now verifies partial selections and a sequence resume with the same attempt ID. The actual focus regression found during that investigation was fixed before final acceptance.

A separate guarded database read confirmed **three graded answer rows, one 100% completed attempt, one saved active attempt, 50 quiz points and one quiz-point transaction**. Two browser-authored assessments retained a 12-minute duration and one-attempt limit, with four questions each and **zero learner answer rows**. The second is a real atomic copy of the first; copied history is not fabricated. The active attempt was used only to verify saved partial positions/keyboard behavior and received no grade or reward.

Third-party analytics/fonts/icons/avatars/datepicker/MathJax/QR assets were blocked in isolated review. These samples do not certify external glyph/services, all legacy criteria/editor/theme states, screen readers or exhaustive WCAG conformance. The separate focused standalone CBT player retains its existing supported-format contract; structured questions run in the lesson workspace.

### Preservation, cleanup and remaining work

No migration or compatibility conversion ran in this continuation. The working `bootkode_recovered` database remained at **41 migration records**, with **0 known structured fixture accounts/courses**. All temporary accounts, both lessons, owned copied assessments/questions, answer/session/reward data and polymorphic role links were removed from `bootkode_browser_test`; the guarded cleanup reported **0 known fixture accounts/dependencies remaining**. Only owned port-8011/9222 QA processes were stopped. Normal port-8000/5173 listeners were preserved and the application returned HTTP 200.

Malformed legacy structured definitions require explicit instructor setup; no undocumented old shape is guessed or bulk rewritten. Formal mark correction/appeal history, backed-up legacy reconciliation, richer project-file/peer-review delivery and exhaustive acceptance of other criteria editors remain open. Existing provider/OAuth/live-AI/mail, historical public-file migration, dependency, legal and broad accessibility/theme gates remain. Matching/ordering/drag-drop are now implemented for lesson quizzes and are not part of that unsupported-format backlog. No whole-product production-readiness, merge or deployment claim follows from this continuation.

Commits for this continuation:

- `32de09c9 feat(assessment): deliver structured questions and scoped authoring`
- `830e4199 build(ui): compile structured assessment controls and focus handling`

The documentation commit records the accepted evidence and remaining gates. The handoff reports ordinary feature-branch push and exact remote HEAD verification; no merge or deployment is performed.

## Recorded mark correction and learner reconsideration — 22 October 2026

### Findings and implemented boundary

Finalized written submissions disappeared from the review queue and the initial marking service correctly refused overwrites without providing a correction workflow. Learners could read feedback but had no owned request/history path. Structured automatic answers also appeared among manual-review controls. Inspection reused the existing assessment, attempt, answer and role/ownership boundaries; the unrelated project controller is only a legacy stub and was not used to create a parallel submission system.

The existing review service now handles explicit corrections and owner-requested reconsideration for finalized written answers with complete original snapshots. It retains before/after marks, feedback, grader/time and full result snapshots; requires a reason; checks current course/platform authorization and non-self review; locks learner/attempt/answers; refuses stale decisions; and makes exact operation retries safe. Marks accept at most two decimal places, matching their persisted precision. A correction, published result, audit and any resolved request persist together. Automatic/pending/cross-attempt/unsupported historical records cannot use this path.

Learners can request reconsideration from either existing result surface. One request is retained per answer correction version; exact retries do not reopen a decision. Reviewers can correct a mark or uphold it with a reason. Pending/requested/finalized queue filters use actual scoped records. Safe learner history omits keys, rubric guidance, reviewer identifiers, operation/revision data and full private result audits. Result headings refresh from the recorded snapshot when the component renders. Retained personal result requests remain owner-scoped after enrollment ends; this does not reopen lesson access.

Corrections change future pass/progress/new-credential eligibility using original question marks and the original pass threshold. They do not award/claw back rewards, delete completion history, or change issued/revoked credentials, tokens or files. No schema migration, dependency, bulk reconciliation or working-record mutation is introduced. Decisions are visible in results; no external notification delivery is claimed.

### Final automated and build evidence

- Final `php artisan test`: **231 passed, 1,087 assertions, 273.25 seconds** on guarded `bootkode_test`. There are **16 new correction/review cases**, including snapshot bounds, precision, ownership/self-review, exact replay, stale decisions, owner requests, upheld/corrected history, atomic rollback, safe projection and issued-credential/completion/reward preservation.
- Final `npm run build`: **passed in 5.86 seconds**. Public JavaScript remains **0.92 KB**; the dashboard entry is **418.00 KB**, app CSS **256.59 KB** and dashboard CSS **17.91 KB**. No additional runtime or dependency was added. The existing PostCSS `from` warning remains; the final build did not emit the earlier Browserslist warning.
- **7 changed application/test PHP files** passed syntax checks; dashboard JavaScript passed `node --check`. Blade compilation, route cache/clear and `git diff --check` passed. Route inventory remains **284 routes**.
- Fresh dependency audits remain **8 npm advisories (5 high, 3 moderate)** and **62 Composer advisories across 19 packages**. Advisories were not suppressed and no forced major upgrade was used.
- Runtime verification used PHP **8.4.27**, Laravel **11.46.1**, Livewire **3.6.4**, Tailwind **3.4.19** and Vite **7.3.7**. The working application returned **HTTP 200** after final checks.

### Authenticated browser acceptance

Accepted Chrome evidence contains **44 distinct responsive state samples** at **390/768/1024/1440px** and **21 workflow checks**, with no unhandled browser exception, internal-error page or horizontal page overflow in those accepted samples. Samples cover owned recorded results, request disclosures, personal-result dialogs, pending/requested queues, review dialogs, correction forms, reasoned decisions, stale errors, corrected history and scrolled correction controls. Actual 390/1440 screenshots were inspected, including native field errors and publication controls.

Native forms created two owner review requests without changing the original 70% result. The reviewer corrected one answer from 3 to 2 points (60%) and upheld the other request with an explanation. A second browser tab then changed the first mark from 2 to 1 point (50%); the stale first tab's attempted replacement was refused and Reload cleared its old correction context. Both learner result surfaces displayed 50%, both correction reasons and the recorded request decisions. Other instructors could not list these results; learners received 403 from the staff route. Invalid score errors remained associated with the score field and did not change the saved mark.

Physical Enter opened the correction and focused its bounded score field. Cancel/publication returned focus to the correction control; request/decision replacement focused the corresponding response. Shift+Tab stayed inside the review dialog. Browser testing found that disabling the dialog opener during loading made Escape return focus to the body; the opener now retains focus with a loading opacity state, and the repeated physical Escape check returned to the correct row trigger.

Early harness attempts read a slow server response before it settled and compared the decimal HTML bound `4.00` to the literal `4`; these are excluded from accepted evidence. Final checks await request completion and compare numeric bounds. External analytics, fonts, icons and avatar/CDN assets were blocked during browser QA. Interface screenshots therefore use fallbacks and contain unavailable external icons. This is targeted light-theme/Chrome interaction acceptance, not complete assistive-technology, every-theme or every-browser certification.

### Persistence, cleanup and delivery

Private browser-database evidence records two correction events, unchanged initial review history, final **50% / 5 of 10 points / not passed**, request decisions **corrected/upheld**, and **zero open requests**. The original passing-result reward remains **50 points with one quiz point transaction**. The separate submitted written exercise remains pending, and all three submitted answer records retained their original response text.

Known fixture accounts **30–32** from the initial timing run were removed before the accepted run. Final fixture accounts **33–35**, their known course/lesson/assessment/question records and dependent session/answer/enrollment/reward/role links were removed from `bootkode_browser_test`. Explicit dependent-row checks returned zero. Private evidence stays in ignored QA storage. Only owned QA Chrome/server processes **23136/23131** were stopped after checking their command markers.

Read-only counts and SHA-256 row fingerprints matched before/after for working users, courses, lessons, assessments, questions, attempts, answers, credentials, reward ledger and all **41 migration records**. No fixture account was present in `bootkode_recovered`. On resuming after the environment restart, neither development listener was running; Laravel **8000** and Vite **5173** were restored after checking port state. Final listeners were **10907/10935** and working HTTP was 200; QA ports 8011/9222 were closed.

Code/build commits:

- `54e8ef74 feat(assessment): retain mark corrections and review decisions`
- `89dd7f5a build(ui): compile recorded-mark focus handling`

The documentation commit records this evidence and the remaining limits. Delivery uses an ordinary feature-branch push followed by exact remote HEAD verification; it does not merge or deploy the application.

Remaining feasible assessment work includes richer private project-file/peer-review delivery, exhaustive criteria-editor acceptance and backed-up reconciliation of legacy attempts/definitions without complete snapshots. Provider/OAuth/live-AI/mail acceptance, historical public-file migration, dependency/legal gates and broad accessibility/theme acceptance also remain open. Formal owner reconsideration and explicit correction history for supported finalized written attempts are now implemented; the whole-product request is not declared production-ready.


## Private project/assignment submissions — 22 October 2026

### Implemented boundary

Inspection found file requirements without a real saved learner-file workflow, private manual grading options in public/resumed question projections and project/assignment criteria editors with incomplete validation and scope. The implementation reuses existing attempts, answers, authoring and review services. Configured deliverables and assignments accept private draft files with original instructions/limits; required work is validated before normal submission. Expiry retains only actual saved work. Original file/word requirements and private grading guidance are available to authorized reviewers. Submitted work remains pending until genuinely marked.

Fresh authorization, learner/attempt locks, deadlines, real-byte MIME validation, positive size, extension/count/word bounds, duplicate replay handling, failed-write cleanup, private filesystem visibility, hash checks and owner/reviewer-only downloads cover the boundary. Learner snapshots, including old resumed attempts, omit grading notes/sample answers/rubrics. Submitted files cannot be replaced or removed. No schema migration, dependency, automated execution/file grading or historical regrade is introduced. Legacy attempts without upload rules preserve their original text-only contract.

### Automated, build and route evidence

- Final `php artisan test`: **251 passed / 1,244 assertions / 74.01 seconds**, using guarded `bootkode_test`. This includes **19 submission-integrity cases** and **one configuration-output case covering all 24 config files**. Coverage includes access/ownership, original constraints, replays/rollback, deadlines, required files, byte/type/size/hash validation, safe projections, private visibility, retained downloads and file-only pending/reviewed results.
- Isolated `npm run build`: **passed in 9.04 seconds**. App CSS is **256.86 KB** (`app-y6N7-v5i.css`), dashboard CSS **17.91 KB**, public JS **0.92 KB** and dashboard JS **418.00 KB**. This build excludes concurrent Course Content Studio changes and reproduces the browser-accepted asset hash. The existing PostCSS `from` warning remains.
- **14 owned PHP files** passed syntax checks; existing dashboard JavaScript passed syntax validation. Blade compilation, route cache/clear and diff checks passed. The delivered route inventory contains **285 routes**, including the new verified `assessment.submission-file` download. A separate concurrent studio route in the shared checkout is outside this delivery.
- Fresh audits retain **8 npm advisories (5 high, 3 moderate)** and **62 Composer advisories across 19 packages**. No advisory suppression or forced major upgrade was used.

The first isolated PHP command encountered an incomplete temporary vendor copy. Replacing that disposable copy with a link to the existing installed dependencies allowed compilation checks to run; the full suite above ran in the shared checkout before concurrent import tests were added. No dependency installation/update was performed for this phase. Composer's linked application autoload resolves shared source, so these compilation checks are not represented as a fully independent PHP source environment. Owned application sources were compared byte-for-byte between checkouts.

### Actual browser and storage acceptance

Chrome accepted **82 checks: 52 distinct responsive state samples at 390/768/1024/1440px and 30 workflow checks**. Native author forms created/copy/moved project criteria and created an assignment; learner forms uploaded real TXT, ZIP and PDF files. Checks covered private drafts, another reviewer's draft denial, duplicate attachment, removal/revoked URL, resume, required-file blocking, submission, unrelated instructor denial, private grading guidance, manual **8/10 and 7/10** marks and learner **80%/70%** results. A mobile retake saved/left/resumed with its file and remained ungraded while the earlier result stayed 80%. Initial-review completion focused the queue filter. Native TXT/PDF downloads and an authenticated ZIP response matched original bytes, attachment headers and private/no-store policy. Small files display their actual bytes.

Byte comparison exposed a real defect: a newline before `<?php` in `config/affiliate.php` prefixed binary responses while Content-Length truncated their final byte. Removing that byte fixed all three downloads; no affiliate values changed. A subsequent real-storage probe verified **private visibility, mode 0600 and matching SHA-256** after explicitly setting private visibility. The browser run preceded that final storage option and a later read-only original-requirements paragraph in the review dialog; the probe and final full suite cover those final changes respectively.

390/1440 screenshots were inspected; intermediate widths received layout/action sampling. Accepted samples had no page overflow, internal-error page or unhandled exception. External analytics/fonts/icons/avatars were blocked, producing fallback fonts and unavailable CDN icons. This is targeted Chrome/light-theme interaction acceptance, not complete screen-reader/every-theme/every-browser certification. Local PHP upload limits remain **2 MiB**, even where instructor settings permit more; actual displayed/enforced limits use the strictest configured bound.

### Persistence, cleanup and scope

Isolated browser records contained **three attempts**, **four saved files**, **two finalized answers**, final results **80%/70%**, and an active retake with no result. Only the two real finalized results awarded rewards: **100 points across two quiz transactions**. Author-created questions stored JSON objects rather than encoded JSON strings and had no fabricated answers. Known fixture accounts 36–38 and their course/assessment/session/answer/enrollment/reward links were removed from `bootkode_browser_test`; dependent counts and known private-file directories returned zero. The later filesystem probe used a rolled-back test transaction and deleted its own file. Private evidence stays in ignored QA storage; temporary Livewire uploads retain normal cleanup without deleting another user's uploads.

Read-only row counts and SHA-256 fingerprints of working users, courses, lessons, assessments, questions, attempts, answers, credentials, reward ledger and **41 migration records** matched before/after. No fixture was inserted into the working database. Only owned browser/server processes were stopped; existing development listeners and the concurrent Vite hot file were preserved. Concurrent AI/content-import source, tests, documentation and generated working assets remain outside these commits.

Q&A/peer-review settings are still configuration, not a completed participation workflow. Backed-up legacy reconciliation, other criteria-editor acceptance, review notification delivery and existing provider/dependency/legal/historical-file/accessibility gates remain open. Delivery is an ordinary feature-branch push with exact remote HEAD verification; no merge, deployment or whole-product readiness claim follows.


### Resumed runtime check — 23 October 2026

After the interrupted delivery resumed, the four submission commits were intact and the index contained no staged work. Concurrent content-import source/docs/styles remained as uncommitted work. The existing Laravel listener on 8000 and Vite on 5173 were retained; the current hot file was not changed. MySQL/MariaDB was inactive with no 3306 listener, and the homepage returned HTTP 500 with MySQL connection refused. `sudo -n /opt/lampp/lampp startmysql` was refused because this session requires an interactive administrator password. Database directories belong to mysql, so no unprivileged alternate server, permission change or database reconstruction was attempted.

An administrator must run `sudo /opt/lampp/lampp startmysql` before current HTTP/database acceptance can be repeated. The earlier 251-test, browser and record-preservation evidence above was obtained on 22 October with the database running; it is not presented as a fresh 23 October test run. This runtime blocker does not change the committed source/build acceptance or authorize any destructive database operation.
