# Personalised learning ecosystem — implementation ledger

## Scope and audit (2 November 2026)

This is an incremental extension of the existing platform, not a replacement LMS. Phase 0 has inspected routes, application domains, migrations, authorization, generation, assessments, certification, frontend entry points and the five engineering documents. Installed versions: Laravel 11.46.1, Livewire 3.8.11 (its bundled Alpine), Tailwind 3.4.19, Vite 7.3.7; CLI PHP 8.4.27, Composer PHP constraint ^8.2. No dependency migration is proposed.

| Existing capability | Reuse / limitation |
| --- | --- |
| Authentication | AuthController, social login, email verification and Spatie roles; preserve account fields and referral handling. Learning-profile setup is a separate verified journey. |
| Learner intelligence | AiLearningProfile and LearningAiService already store goals, diagnosis, recommendations and adaptive paths. Current diagnosis uses technology-specific heuristics, activity-derived scores and mutable answer definitions: it is not a verified diagnostic qualification. |
| AI | AiManager, OpenAiCompatibleProvider, CodingMentorService, AiUsageRecord and private conversations already exist. Groq provider/model/key come from server configuration. The current configured model is reported only as configuration, never proof of a successful request. |
| Course generation | CourseGenerationService / BlueprintBuilder / Payload, AiCourseGenerationService, AiCourseExpansionService, CourseAiRequestService, CourseContentRun and queued jobs already provide validated draft curricula, module stages, retry and quota accounting. Learners cannot invoke instructor generation directly. |
| Discovery and purchase | Course::publiclyAvailable(), PublicCatalogService and WalletService::purchaseCourse() remain authoritative. Existing coach recommendations use weaker publication filters and can return unrelated courses: fix in Phase 2. |
| Delivery / assessment | CourseView, CbtAttemptService, AssessmentSubmissionService, AssessmentReviewService, ProgressService and structured/Q&A/project workflows retain enrollment, original attempt snapshots, private grading and manual-review boundaries. |
| Certification | Existing request/approval, eligibility, stable verification codes, PDF and QR support remain. Do not replace historical issued/revoked credentials. |
| Notifications | PortalNotificationService, LearningNotificationService, delivery receipts, preferences and queued email already exist. Reuse for future demand digests. |
| Dashboards / rewards / institutions | Existing role dashboards, shared shell/tokens/ui primitives, GamificationService and institutional workflows remain. No new currency or fabricated usage. |

### Current infrastructure boundary

The working environment references `bootkode_restored_20261028` at port 3308. No MariaDB listener was present during this audit. The October 28 content-audit ledger documents physical preservation and failed isolated InnoDB salvage. No claim is made that its pending editorial payload was applied or that working records are now recoverable. This phase will not start, replace, migrate or seed that working database. Validation uses a newly initialized independent temporary MariaDB instance and explicit `_test` databases. Application of the additive migration to real data requires restoration verification and a private affected-record backup first.

### Architecture decisions

Extend `ai_learning_profiles`; do not duplicate it with learner/profile/competency tables. Keep self-reported interests, knowledge and preferences separate from `signals`, `skill_diagnosis`, `assessment_feedback`, recommendations and paths. A form submission cannot award competence, enroll, spend funds, publish a course or issue a certificate. Lock the existing user row before profile creation/update to serialize concurrent first saves. Resolve ownership and current active role from authenticated server records on each request and mutation.

## Ordered implementation checklist

- [x] Phase 0: repository audit and implementation ledger.
- [ ] Phase 1a: additive structured learning profile, guided optional onboarding, later editing, dashboard entry, ownership/validation tests and responsive acceptance.
- [ ] Phase 1b: optional short diagnostics using the existing assessment engine; evidence projections must honor saved finalized attempt snapshots, manual review and scope. Replace heuristic competence claims before treating the coach as a diagnostic system.
- [ ] Phase 2: catalogue-backed exact/related/no-match ranking across disciplines; prerequisite metadata validation, explanations and accept/reject/save; persistent, adaptable roadmap using real course IDs.
- [ ] Phase 3: missing-course demand/strict deduplication and topic groups, grouped admin notices, learner-safe generation orchestration, resumable staged learning and authorized preview/enrollment. Reuse CourseContentRun and existing generation jobs, preserving draft/approval boundaries.
- [ ] Phase 4: contextual tutor with graded-assessment integrity, delivery improvements and configurable evidence-based completion; preserve existing certification IDs and history.
- [ ] Phase 5: role-protected demand analytics/review/usage surfaces and resolution history using existing authoring, approval and notification services.
- [ ] Phase 6: evidence-based rewards, cross-product responsive/accessibility polish and performance checks.
- [ ] Phase 7: full regression suite, current dependency audit, real-provider staging check, backed-up forward migration and representative end-to-end browser acceptance.

No generated course may bypass catalogue/payment/approval checks merely to complete this checklist. No empty catalogue result may be replaced with an unrelated recommendation. No AI text may determine user identity, payment status, marks or credential eligibility.

## Resume point

Start with Phase 1a. Update this ledger and docs/verification.md with actual evidence, commit IDs, migration application boundary and remaining work before handing off. Later sessions should inspect Git state and this ledger rather than creating parallel services.
