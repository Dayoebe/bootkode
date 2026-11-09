# Coherence implementation roadmap

## Current implementation and release backlog — 27 October 2026

This summary supersedes remaining-work statements in the dated history below.

| Status | Work |
|---|---|
| Implemented | Course Content Studio import/generation, migration, managed queue worker, existing live-provider acceptance, Groq connection checks and logging consolidation; see their dated verification evidence |
| Implemented | Recorded lesson/structured assessments, private project/assignment files, formal manual-mark corrections/reconsideration and versioned Q&A/peer participation |
| Implemented | Private community replies, real saved job alerts/portal match receipts, optional queued email with current preferences, quiet hours and retry states; scheduled delivery stays off until deployment acceptance |
| Implemented | Private assessment-result/reconsideration/correction notices, eligible CBT and course-review reminders, recorded-achievement delivery/history and durable learning receipts |
| Implemented | Clean-checkout CI tests/builds, unsuppressed dependency release gates and the compatible npm parser security fix |
| Integration acceptance | Deployment-specific live mail, asynchronous default-queue worker and scheduled delivery; both delivery schedule flags remain off |
| Inventory verified locally | Checked historical attachment references/directories, unsupported submitted attempts and duplicate keyed lesson/quiz point rewards: zero affected rows/files in this working snapshot; repeat for other environments |
| Product/data decisions | Account archival/anonymization and any future historical reconciliation still need retention rules, backups and explicit forward operations |
| Unsupported product promises | Institution billing/white-label delivery, verified games/unsupported reward fulfillment and reviewed path generation; keep unavailable states truthful until backed by real implementations |
| Remaining acceptance/release | Other legacy editor/theme/assistive-technology flows, dependency findings, deployment-specific provider/payment/OAuth/mail/worker/backup checks, and human editorial/legal/publication decisions |

Q&A requires no migration. The two notification phases add backed-up forward migrations, now applied locally; neither phase publishes courses. Current evidence is in verification.md; historical counts and earlier environment outages are not current-state claims.

Work in independently verified commits. Preserve unrelated sidebar/profile edits and all existing commercial/learning records.

| Stage | Deliverable | Evidence required |
|---|---|---|
| 1 | Audit, route inventory, architecture | Locked dependency versions, baseline build/tests, safe branch |
| 2 | Registration and administrative access | Privileged registration rejected, learner forbidden from financial/CBT controls, Livewire checks |
| 3 | Funding, wallet and enrollment invariants | Replay, mismatch, rollback, insufficient balance and paid-course regression tests |
| 4 | Semantic tokens and shared controls | Build, compiled views, light/dark contrast, keyboard focus |
| 5 | Public discovery and factual home | Published-only listings, empty/search states, responsive browser checks |
| 6 | Learner and operational shell | Learning first, no duplicated welcome, accessible navigation/status/modal controls |
| 7 | Remaining reliability and privacy | Progress scoping and private response cache regression checks |
| 8 | Durable standard and release | Documentation, final checks, granular commits, remote verification |

Large domain components (CourseView, ResumeBuilder, LearningAnalytics, GamificationService, marketplace admin) should be decomposed only alongside behavior tests. A line-count-only refactor would risk existing workflows. Stage-specific completion and remaining acceptance work are tracked in `verification.md`; this roadmap does not assert that all existing workflows are already certified.

## Delivery state, 21 October 2026

Stages 1–7 have concrete implementations and core-journey regression coverage. Stage 8 includes the root engineering standard, setup documentation and granular feature-branch history; final verification and remote state are recorded in `verification.md`.

Release gates remain: dependency advisories under the retained major versions, real provider sandbox acceptance, organisation-approved legal content and broader acceptance of legacy career/community/institution/marketplace operations. Shared styling applies to those routes, but they are not all individually certified. The large components listed above were retained to preserve functionality rather than split without domain coverage.

## Further operational work, 21 October 2026

The first delivery did not exhaust feasible work. The continuation implements certificate-template persistence/rendering, career foreign-key repair/application integrity/private new résumés, and community authorization/ungraded solution submission. Shared operational headers, feedback, navigation and dialogs extend into those routes. See the continuation evidence in `verification.md`; these changes do not certify every legacy module.

Still distinguish implementation work from integration gates: job-alert delivery and moderator-response delivery are unavailable and no longer report fake success. Historical public résumé files require a backed-up private-storage migration; new uploads already use private storage. Institution, marketplace, mentorship and other legacy workflows require their own action/ownership and interaction acceptance before the whole-product request can be called complete.

## Further domain work, 21 October 2026

Institution membership/invitation scope, marketplace wallet order integrity/private delivery, and mentorship participant/privacy/payment boundaries now have implemented changes and dedicated regression coverage. Root interface consistency and representative role/tab/dialog acceptance extend into those domains; see `verification.md` for exact evidence. No real records were changed for this code-only continuation.

Remaining whole-product acceptance includes affiliates, messaging/support, instructor authoring/approval details, legacy analytics/gamification and other operational actions beyond these samples. Institution billing, white-label delivery, live provider settlement, unpublished mentoring resources, job-alert/moderator delivery and historical public-file migration are separate incomplete integrations. Existing dependency/legal/accessibility release gates remain. Do not mark the original full-product request complete based on representative domain coverage.

## Further implementation, 22 October 2026

Implemented additional support/feedback request guards and private delivery, participant-scoped message polling, nested authoring access/upload safeguards, historical learning/account deletion protection, funded replay-safe affiliate settlement, real banner/clipboard workflows and public marketplace detail routing. This supersedes the prior statement that these domains had not received action-level checks; exact coverage is recorded in verification.md.

Remaining work includes exhaustive editor/question-type and analytics/gamification acceptance, legacy theme/accessibility consistency, backed-up migration of historical public attachments, account archival/anonymization, full provider acceptance and the previously listed dependency/integration/legal release gates. No migration, deployment, data cleanup or global-role correction was applied to existing working records.

## Additional feasible work completed — 22 October 2026

Implemented recorded learning analytics consolidation, persisted goals/exports, private consented leaderboards, positive/replay-safe reward accounting and the real supported energy-redemption boundary. Removed speculative gamification helper implementations that referenced missing fields/methods; retained public service contracts with recorded-data implementations or explicit unavailable results. CBT now has persisted authorized attempts, server deadlines, saved answers, private grading snapshots, atomic grading and reward replay protection. See verification.md for exact checks.

Remaining scope includes complete manual/question-type grading workflows, published verified games and unsupported reward delivery, achievement broadcast integration, reviewed path generation, historical gamification duplicate reconciliation and the prior provider/dependency/legal/accessibility/historical-file gates. No claim of exhaustive acceptance or whole-product production readiness follows from these additional samples.


## Further assessment work — 22 October 2026

Implemented the secured lesson assessment/reviewer vertical slice using existing attempt and answer records, owner-scoped CBT management, private owned results, recorded-grade progress/certificate boundaries, exact-set multiple selection and a forward fix for MariaDB's mutable session start timestamp. This supersedes the earlier statement that all manual grading was unimplemented. The supported written formats now have a real review queue; the exact acceptance evidence and remaining limits are in verification.md.

Remaining assessment work includes matching/ordering/drag-drop interaction, formal mark correction/appeal history, backed-up reconciliation of legacy written attempts without snapshots and broader instructor-editor accessibility/format acceptance. Historical clock values are preserved, not reconstructed. Provider/dependency/legal/historical-file gates and exhaustive product acceptance remain open; branch delivery is not a production release.

## Structured assessment delivery — 22 October 2026

Implemented matching, ordering and drag-and-drop authoring, accessible learner interaction, saved positions, immutable private grading maps and owned response history. Also fixed assessment-copy/reorder scope, atomic copy behavior, real attempt-setting persistence, option-index/JSON integrity, recursive statistics and inconsistent legacy grading helpers. This supersedes the earlier remaining-work entry for these three interaction formats. Physical pointer and keyboard flows, including focus after Livewire moves, have concrete verification evidence.

Remaining assessment work includes formal correction/appeal history, backed-up reconciliation of legacy definitions/attempts without supported snapshots, richer project-file/peer-review delivery and exhaustive acceptance of the other criteria editors. Do not infer those integrations from their configuration metadata. Provider, dependency, legal, historical public-file migration and broad accessibility/theme gates remain; this continuation adds no migration or working-database record.

## Recorded mark correction and reconsideration — 22 October 2026

Implemented formal manual-mark corrections and learner-requested reconsideration through the existing review service and attempt records. Original marks/results, correction reasons and reviewer decisions remain recorded; current eligibility changes atomically with the corrected result. Scoped finalized/requested-review queues and owned history extend both learner result screens. This supersedes the earlier correction/appeal-history backlog for finalized written attempts with complete grading snapshots.

Remaining assessment work includes backed-up reconciliation of legacy attempts/definitions without supported snapshots, richer private project-file and peer-review delivery, complete criteria-editor acceptance and external review-notification integration. Existing credential remediation stays a separate authorized process. Provider, dependency, legal, historical public-file migration and broad accessibility/theme release gates remain open. This phase adds no schema migration or automatic historical regrade.

## Course content acquisition — 22 October 2026

Implemented one licensed-source vertical slice and structured AI draft generation through the existing course/assessment model, with run metadata, revision protection, queue jobs, role checks, retry state, CLI commands and documentation. Current support is limited to selected freeCodeCamp curriculum Markdown challenge descriptions under the repository BSD-3-Clause notice and AI-generated lesson batches/quizzes/project rubric. See `course-content-system.md` and `verification.md`; do not infer support for arbitrary repositories, source assets, or publication readiness.


## Private project/assignment delivery — 22 October 2026

Implemented actual private project-deliverable and assignment attachments through existing attempts/answers, safe public instructions, frozen file/word requirements, owned draft persistence/removal, authorized immutable submitted downloads and manual instructor review. Project/assignment authoring now has scoped native forms and validated object metadata. Binary-response corruption from configuration output is fixed with a configuration-wide regression check. This supersedes the earlier private project-file backlog for supported configured lesson assessments.

Remaining assessment work includes real Q&A/peer-review participation beyond configuration metadata, exhaustive acceptance of other criteria editors, external review notifications and backed-up reconciliation of legacy attempts/definitions without supported snapshots. Historical public-file migration, provider/OAuth/live-AI/mail, dependency/legal and broad accessibility/theme gates remain. This continuation requires no migration, historical regrade or working-database record change; the whole-product overhaul is not declared production-ready.

## Authenticated redesign continuation — 23 October 2026

- [x] Inventory authenticated routes, shared layouts, installed stack and real roles.
- [x] Consolidate shell, desktop collapse, mobile drawer/shortcuts and navigation semantics.
- [x] Migrate authenticated feature surfaces to existing semantic styling; remove forced legacy CSS remapping.
- [x] Add bounded accessible tables, field/control names, and existing-dialog focus/Escape behavior.
- [x] Protect the user directory on every request after a regression test exposed direct learner access.
- [x] Record final full-suite, role/browser and dependency-audit results in verification.md.
- [ ] Push feature commits when HTTPS Git credentials are available.

Detailed exceptions and state coverage live in dashboard-redesign/page-checklist.md. External payment/AI delivery still requires configured providers; visual changes do not certify an external transaction or generated response.


## Release continuation — 23 October 2026

Completed: compatible dependency security maintenance; 45 additional workspace routes at 390/768/1024/1440px; authoring/résumé/interview/system-status/eligibility repairs; recorded interview analytics; isolated release suite/build/classmaps. The import revision fixture now resets its HTTP factory while retaining conflict/draft assertions; that correction remains with the user's untracked feature. Remaining: Laravel/JWT major-version security decisions, Tailwind 3 dependency advisories, configured provider acceptance, conditional/assistive-technology coverage and Git authentication for ordinary push. See dashboard-redesign/continuation-report.md; no forced upgrades or suppressed advisories.

## Course-content continuation — 26 October 2026

Completed the existing Studio's idempotency, partial-run handling, fixed-host/DNS/download security, reviewed-revision queueing, server-owned AI fields and real project criteria. Verified a live licensed-source import and rerun through the existing editor in an isolated database. CLI/UI share validated-input generation identity and existing AI limits; queue reservation/timeout settings are explicit. Final automated/build/browser evidence is in verification.md. Remaining: configured live-AI/provider acceptance, additional separately reviewed source adapters, editorial/factual approval of resulting drafts, and the previously recorded dependency/release gates.

## Dependency-outage follow-up — 26 October 2026

Completed: database-independent 500 response and exception-renderer regression. The earlier MySQL service blocker is now resolved. Database connectivity, public HTTP 200, an empty failed-job list and real course-content processing were verified in Main course-content completion below. No recovery/reset/migration is authorized or implied by a refused connection.


## Main course-content completion — 26 October 2026

- [x] Exercise the actual configured provider through CLI and authenticated UI/queue/editor in an isolated test database.
- [x] Resolve real malformed/truncated output with strict schemas and bounded configurable generation tokens; share locked generation/expansion quota.
- [x] Expose actual category, validation and quality findings with authorized quality/expansion/retry actions.
- [x] Integrate/harden existing expansion and recover the backed-up failed working draft without replacing original lesson IDs/text or publishing it.
- [x] Restore verified full notices to older draft lessons through an explicit additive, idempotent command.
- [x] Install an enabled managed course-content worker with lingering; verify completed-job replay and current public/database availability.
- [x] Record 332 passing tests / 1,602 assertions, build and targeted authenticated four-width browser evidence.

The earlier MySQL and configured live-generation blockers are resolved for this checkout. Remaining release work: authenticated ordinary Git push, existing dependency advisories, human editorial/assessment approval and deployment-specific setup. Additional sources and broader theme/assistive-technology coverage remain separately scoped; see verification.md.


## Direct course creation — 26 October 2026

Completed: three original working course drafts owned by the authorised super-admin, totaling 27 substantial lessons, 36 explained multiple-choice questions and three marked capstones. Real covers/practice files are saved. Existing course and learning/financial/certificate records remain unchanged. Corrected code-example media false positives and displayed curriculum order discovered while reviewing the new content. Human editorial review and the normal publication workflow remain; these courses were not automatically published. See verification.md.


## Inline assessment authoring — 26 October 2026

Completed: remove the duplicate standalone authoring entry point, retain authorised legacy URL redirects into the selected lesson's Assessment tab, preserve tab/lesson location state, label mobile tab controls and fix stale list/form transitions. Authenticated checks cover all four tabs, create/cancel, assessment edit/cancel, quiz question edit/cancel, project criterion edit/cancel, returning to the list, switching lessons and four viewport widths. No working assessment or learner record was edited by verification.
## Original course-content revision — 26 October 2026

- Completed: directly revise existing courses 1 and 2 with explicit prerequisites, corrected contracts/examples, 24 ordered lessons, six quizzes (30 MCQs), two marked capstones and tested downloadable reference kits. Preserve original IDs/slugs/source attribution and unrelated records; validate the update in a dedicated test database before applying it to working drafts.
- Publication gates: human pedagogical/editorial review, external source/reference availability checks and deliberate publication/approval. Mechanical code checks and a local quality scan do not establish those decisions. No additional software refactor is required merely to inspect these revised drafts.

## Groq and logging follow-through — 27 October 2026

Completed: real Groq connectivity checks in CLI/administrator Studio; safe configuration/verification distinction; clear authentication/model/format failures; current-role mentor guard; single-file application logging and journal-backed worker console output; archive historical verification captures. Existing AI authoring and learner workflows remain in place. Full suite and actual responsive/browser evidence are recorded in verification.md. Generated-content factual review/publication remains a separate deliberate action.


## Q&A implementation — 27 October 2026

Completed: scoped Q&A authoring with normalized objects and full criteria validation; bounded saved original responses and immutable requirements; explicit private-course sharing; same-topic peer participation; approval/rejection/hiding; stale/replay handling; and withdrawal from retained personal results. Peer feedback stays separate from official grading and rewards. Existing records and legacy text-only behavior are preserved; no migration is needed.

The current-state summary near the top supersedes older dated backlog statements. Remaining implementation includes notification delivery, inspection-driven historical-data/account-lifecycle work and unsupported product promises. Broader theme/assistive-technology acceptance, existing dependency findings, deployment-specific provider/queue/backup acceptance and human editorial/legal/publication decisions remain release work. Do not automatically publish working course drafts or invent historical outcomes.


## Community replies and saved job alerts — 27 October 2026

Implemented owned native saved-alert forms, daily/weekly criteria, bounded public-job matching, durable replay receipts, real private community replies and atomic native portal notifications. Optional email defaults off, checks current source/preferences/account, respects quiet hours/weekends and records transport acceptance separately from delivery. Notification Preferences shares the same opt-ins, preserves concurrent unrelated values and saves valid quiet hours. Missing test-notification classes no longer produce false sent claims. See notification-delivery.md and verification.md for migration, browser and recovery evidence.

Remaining delivery scope is review/reconsideration, CBT reminders and achievement stubs, plus actual deployment mail/scheduler/worker acceptance. Push/SMS remain unconnected. The scheduler flag remains false; no external message was sent. This supersedes older job-alert/community-reply unavailable entries without closing the other historical-data, dependency, provider, editorial, legal or broad-accessibility gates.


## Learning notifications, CI and inventory — 27 October 2026

Completed: transactional finalized-result/reconsideration/correction notices, enrolled eligible CBT reminders, real course-review reminders and confirmed unsubscribe, recorded-achievement notices/history and current-preference queued email checks. Durable receipts survive notice deletion. Native course-review writes now resolve actual owner/course, retain the real comment and stop the actual reminder. The local receipt migration is applied with original records/migration rows preserved. No schedule or external mail was activated.

CI now installs dependencies from a clean checkout, tests/builds the application and separately enforces unsuppressed dependency audits. Untracking generated vendor/bootstrap manifests fixes the incomplete committed installation; local copies are retained. The compatible parser backport resolves the two npm findings. Six Composer advisories remain a release gate and require a separately reviewed dependency/framework migration.

A read-only inventory found no known historical public attachment references/files, unsupported submitted assessment snapshots, unmatched submitted written answers or duplicate keyed lesson/quiz point-reward groups in the current working database. This closes inspection for this snapshot, not migration tooling or other environments. Account archival, unsupported product promises, remaining legacy notification families/theme/editor/assistive-technology acceptance, real provider/worker/backup checks and human editorial/legal/publication decisions remain.

## Nontechnical curriculum expansion — 28 October 2026

Completed in working records: five new courses across communication, time management, teamwork, customer service and critical thinking; 30 substantive lessons, 60 MCQs, five practical capstones and printable/editable workbooks. Existing authoring, grading and category services are reused. Publication and editorial approval remain deliberate review steps; no new software refactor is needed to inspect the drafts. Evidence and scope are recorded in verification.md.


## Course editorial audit — 28 October 2026

Course editorial audit: 10 courses / 81 lessons inspected, corrections and materials prepared. Remaining: recover database access while preserving original records, pass the guarded rehearsal, apply the payload, verify saved content/history and complete authenticated responsive checks. The offline required-notice behavior has focused test coverage. See [the course audit](course-content-audit-20261028.md); do not mark live cleanup complete yet.

Database follow-up: all 6,143 regular files were preserved in a separate physical copy. Recovery modes 1–6 on an isolated copy failed at system-space page 0. Course edits remain prepared and unapplied; see the linked audit for preservation paths and recovery limits.

## Personalised learning programme — 2 November 2026

The [implementation ledger](learning-ecosystem/implementation.md) is the ordered continuation plan for the new brief. Phase 0 audit and the Phase 1a guided-profile implementation are in this branch; completion acceptance is recorded in verification.md. The wider transformation remains open.

Continue with optional diagnostics using the current assessment engine and finalized original result snapshots. Replace the existing heuristic competence/recommendation claims before adding catalogue-backed exact/related/no-match ranking, prerequisite validation and persistent roadmaps. Then introduce demand records/groups and grouped notices, wiring into the existing staged generation/authoring pipeline rather than duplicating it. Learner draft access must explicitly preserve review, payment and publication boundaries. Restore/verify the working database, back up affected records, and clear dependency release gates before a real-data release. No working-data migration is authorized by a test-only rehearsal result.

## Career & Opportunity Intelligence continuation — 2 November 2026

The [career ledger](learning-ecosystem/career-intelligence.md) extends the original learning transformation. The first slice connects reviewed requirements, private evidence claims/review, current eligibility, actual internal application enforcement, provider search repairs, catalogue learning options and bounded configured-provider advice/extraction. It reuses existing resume, portfolio, interview and mentorship workflows.

Next, finish the learning architecture's general matching/prerequisite and persistent-roadmap boundaries, then connect career gaps to shared course-demand/generation records with retries and notifications. Keep semantic/fuzzy matches out of mandatory evidence decisions unless explicitly reviewed. Add designated counselling credentials/access and private records before presenting counsellors as available. Employer discovery needs verification, explicit learner sharing consent and access audit records; it must not reuse private candidate queries from the Career Centre. Preserve real applications and credentials while adding verified outcomes and partnership analytics. Restore/verify the existing database service and back up affected records before applying the career migration to working data.

## Production and app experience milestone — 2 November 2026

- [x] Restore current local database records non-destructively, independently verify SQL restores, rehearse the two existing additive migrations and retain business-table hashes.
- [x] Apply compatible dependency security fixes without suppressing remaining advisories or forcing major upgrades.
- [x] Provide production example, read-only preflight, public-root/PHP examples, cron/supervised queue alternatives, safe migration and backup/GitHub guidance.
- [x] Bound heavy/background IO, retain generation/row checkpoints, prevent duplicate course dispatch, and protect logs/credentials and uncertain mail acknowledgements.
- [x] Split editor assets, bound the outline controls and paginate offline library summaries.
- [x] Deliver account-bound IndexedDB packs/queues, explicit event acknowledgements, replay-safe dates/time/rewards, authoritative lesson/assessment rules and safe navigation/resource caching.
- [x] Test targeted auth, ownership, payments, upload/privacy, queue/recovery, browser offline/history/editor/error flows and current build/cache commands.
- [ ] Resolve supported Laravel/JWT dependency set; obtain green security CI before first production release.
- [ ] Confirm provider PHP/cron/pcntl/runtime limits and perform encrypted off-host database/files/key recovery plus live provider staging acceptance.
- [ ] Profile larger dashboard/course graphs and conditional chart loading; complete broader enrollment/assessment/notes/bookmarks/certificate journeys under throttled networks.
- [ ] Complete real Android/iOS/PWA installation/lifecycle, eviction/media variants, all-theme keyboard/screen-reader and representative field performance acceptance. No global LCP/INP/CLS target is certified.

This is a completed coherent milestone within the wider premium-app brief, not completion of all nine phases. Earlier learning-ecosystem and career phases retain their existing unfinished status. See [milestone detail](app-experience-milestone.md).

## Premium app milestone 2 — 3 November 2026

- [x] Move Chart.js/helpers out of the shared authenticated entry and request them only from the 19 existing chart views.
- [x] Bound the six visible learner course/progress cards to a fixed six-query graph and reuse it for upcoming lesson tasks.
- [x] Remove GET-time gamification initialization and collapse consecutive-day streak reads to one bounded query.
- [x] Isolate secondary learner-dashboard failures with reported exceptions, typed fallback data, visible preservation copy and retry.
- [x] Add regression coverage for query count, route asset presence, read-only dashboard behavior, failure isolation and streak query count.
- [ ] Run the complete discover/enroll/lesson/assessment/notes/bookmarks/certificate journey under throttled/offline/reconnect browser conditions.
- [ ] Profile and reduce the enrolled course workspace's full assessment/question graph with representative isolated data.
- [ ] Evaluate route-specific MathJax and third-party analytics loading using measured pages; do not remove required learning content or consent controls speculatively.
- [ ] Complete field Core Web Vitals, physical Android/iOS PWA lifecycle, all-theme screen-reader/keyboard/contrast and live provider/payment acceptance.
- [ ] Resolve the supported Laravel/JWT dependency set and obtain a green unsuppressed security gate.

This milestone does not complete the nine-phase brief or change the existing deployment score. Detail and exact limits: [app-experience-milestone-2.md](app-experience-milestone-2.md).

## Premium app completion — 3 November 2026

- [x] Upgrade to Laravel 12.70.2 with compatible Socialite/JWT releases and clear Composer/npm audits without suppressions.
- [x] Apply theme/sidebar state before first paint, cloak conditional editor/sidebar content and enforce CLS <= 0.1 across the authenticated browser matrix.
- [x] Scope MathJax to CBT and authenticated marketing analytics to explicit saved consent.
- [x] Bound dense assessment summaries independently of assessment/question count and retain authored/passing semantics.
- [x] Add owned independent lesson notes/bookmarks and prove assessment resume, lesson progression and certificate visibility in a complete `_test` browser journey.
- [x] Lock and atomically persist enrollment completion plus replay-safe certificate issuance; keep soft-deleted certificate numbers unique.
- [x] Add one shared PWA install/update lifecycle while preserving explicit private-pack caching and never implicitly caching authenticated course HTML.
- [x] Verify five themes, focus/skip-link behavior, labels, duplicate IDs and touch targets at 390px and 1440px; verify the wider responsive/offline/editor matrix from 360px to 1920px.
- [ ] Complete physical Android/iOS install/update/eviction acceptance, field LCP/INP/CLS monitoring and representative production-load profiling.
- [ ] Complete host/cron/pcntl, encrypted off-host restore, HTTPS/SMTP/Paystack/OAuth/AI/media-provider staging and current remote CI acceptance.

The application-controlled premium experience work is complete. The open items require deployment infrastructure, real devices, field traffic or provider accounts and therefore remain explicit release gates rather than fabricated local success. Earlier learning-ecosystem/career roadmap items are not implicitly completed. Detail: [app-experience-completion.md](app-experience-completion.md).

## Learner project portfolio and showcase — 4 November 2026

- [x] Retain the existing portfolio domain and add private-by-default visibility plus optional source-code links through an additive migration.
- [x] Restrict the manager to verified learners and enforce active-account ownership through a registered policy on each Livewire mutation.
- [x] Support create, update, reorder and deletion with explicit publish/private controls and HTTP(S)-only links.
- [x] Add the smallest slug-based showcase because no reusable public profile route exists; require Public account visibility and include only explicitly published projects.
- [x] Keep public output escaped, omit private details, and add no portfolio directory or public download path.
- [x] Cover CRUD, ordering, cross-owner IDs, URL schemes, escaped output and public/private account boundaries on an explicit `_test` database.
- [x] Complete isolated authenticated/public browser interaction and responsive inspection at 390/768/1024/1440px; retain keyboard-only and screen-reader acceptance as explicit broader release work.

## Dashboard interface refresh — 4 November 2026

- [x] Refresh the shared sidebar, header and mobile navigation as one consistent professional shell.
- [x] Preserve role-filtered menu generation, named routes, persistent collapse and all authorization boundaries.
- [x] Add mobile drawer focus entry, Escape/backdrop dismissal and focus return.
- [x] Redesign the seven primary role dashboard homepages with a shared hierarchy, role-relevant CTA content and recorded-data charts.
- [x] Keep low-activity homepages informative with route-backed role work areas and factual suggested workflows.
- [x] Explain dashboard data sources and add role-specific operating checklists and support resources without fabricated signals.
- [x] Add semantic colour depth, a truthful live client-status strip and reduced-motion-safe micro-interactions across all seven role homepages.
- [x] Remove fabricated homepage insights and constrain mentor, instructor, content and affiliate dashboard filters/data projections.
- [x] Rebuild the dashboard profile as a theme-consistent account centre; reduce competing local navigation, restore the edit-route contract, remove nonfunctional/security-misleading controls and rebind every mutation to the authenticated account.
- [ ] Continue the shared design through secondary dashboard page families without changing their domain or authorization boundaries.
- [ ] Complete broader theme, screen-reader and authenticated workflow acceptance as each page family is redesigned.
