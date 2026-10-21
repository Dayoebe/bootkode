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
