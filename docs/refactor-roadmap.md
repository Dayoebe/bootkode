# Coherence implementation roadmap

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


## Private project/assignment delivery — 22 October 2026

Implemented actual private project-deliverable and assignment attachments through existing attempts/answers, safe public instructions, frozen file/word requirements, owned draft persistence/removal, authorized immutable submitted downloads and manual instructor review. Project/assignment authoring now has scoped native forms and validated object metadata. Binary-response corruption from configuration output is fixed with a configuration-wide regression check. This supersedes the earlier private project-file backlog for supported configured lesson assessments.

Remaining assessment work includes real Q&A/peer-review participation beyond configuration metadata, exhaustive acceptance of other criteria editors, external review notifications and backed-up reconciliation of legacy attempts/definitions without supported snapshots. Historical public-file migration, provider/OAuth/live-AI/mail, dependency/legal and broad accessibility/theme gates remain. This continuation requires no migration, historical regrade or working-database record change; the whole-product overhaul is not declared production-ready.
