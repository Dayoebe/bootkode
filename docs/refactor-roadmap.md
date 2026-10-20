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
