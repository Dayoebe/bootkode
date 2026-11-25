# Career & Opportunity Intelligence

This extends the learning-ecosystem ledger and the existing application. It does not replace the job portal, resume, portfolio, assessment, certificate, mentorship or notification systems.

## Audit — 2 November 2026

- `Career\JobPortal` / `JobApplicationService` own internal listings and atomic private applications. External/email/phone destinations already have a separate action component; opening them must not create a submitted application.
- `Career\JobSearch` uses Adzuna but currently generates random match percentages and fabricated popularity counts, caches learner-specific flags across users, initializes credentials only on mount and can log credential-bearing query parameters. Fix these before extending provider intelligence.
- Existing listing requirements/skills are free text and JSON, with no reviewed mandatory/preferred contract. Existing `calculateMatchScore` uses self-reported resume information; it cannot establish eligibility.
- Resume skills, external certificates, portfolio entries, learning-profile skills and work history are self-reported. No existing verified competency assertion store was found. Existing assessment sessions retain original finalized grading snapshots; approved/revoked BootKode certificates have stable identities. Neither enrollment nor a certificate automatically proves every skill.
- Existing mentor profiles, mentorships, career-advice session types and private booking/payment workflows should be reused. No designated career-counsellor credential/access workflow was found; do not label ordinary mentors as qualified counsellors.
- The original learning transformation has structured onboarding, but general matching, persistent roadmaps and course-demand orchestration remain unfinished. Career links must not pretend those workflows already exist.
- The recovered user-level `bootkode-database.service` is failing to start; port 3308 is unavailable. Preserve its datadir and validate this work only against an independent `_test` database.

## Delivery order

- [x] C1a: truthful and bounded Adzuna search, safe failure logging, source attribution, valid original provider destinations and honest external-opening state.
- [x] C1b: reviewed structured requirements, private evidence assertions/review, real-time eligibility and internal application enforcement; preserve legacy unreviewed application contracts with explicit unknown eligibility.
- [x] C1c: integrated Career Centre, existing-course learning options, AI extraction/advice with bounded usage, and existing resume/portfolio/interview/mentorship entry points.
- [ ] C2: persistent job-to-skill roadmaps and shared missing-course demand/generation integration after the underlying learning modules are implemented; automatic notification/reassessment workflows.
- [ ] C3: employer verification and consented candidate discovery, designated counsellors/private consultation records, richer verified CV export and outcome/partnership analytics.
- [ ] C4: complete cross-product/provider/staging QA and restored-data migration acceptance.

## Source and verification boundaries

Reviewed requirements and verified assertions belong to PHP services, not AI or public Livewire properties. AI extraction is a draft with literal supporting excerpts; an administrator confirms it. Preferred requirements never block application. Changing the source listing invalidates a prior requirement review. Review history, source identities, expiry/revocation and assessment corrections must remain inspectable. Candidate evidence stays private; employers receive no automatic access.

Adzuna search remains a provider-backed display integration, not a permanent content import or an AI training dataset. Preserve redirect URLs and per-advert attribution. Provider descriptions can be snippets, so they cannot support a full mandatory eligibility decision. Source references checked during this audit: [API overview](https://developer.adzuna.com/overview), [search response documentation](https://developer.adzuna.com/docs/search), [terms](https://developer.adzuna.com/docs/terms_of_service). Commercial licensing/account limits require operator verification; no new third-party content retention or AI processing is implied by these changes.

## Implemented components

| Boundary | Components |
| --- | --- |
| Existing provider repair | `AdzunaSearchService`, existing `JobSearch` / provider Blade, `AdzunaSearchIntegrityTest` |
| Reviewed vacancy requirements | Existing `JobPortal` gains a bounded contract, review/source hash and private review history; `JobRequirementService` validates literal supporting excerpts and stale revisions |
| Private evidence | `CompetencyEvidence` / `CompetencyEvidenceService`; pending claims, non-owner admin decisions/reasons, expiry/revocation, reassessment, retained source summaries and private decision history |
| Eligibility and applications | `JobEligibilityService`; existing `JobApplicationService`, portal modal and apply-action component check current reviewed mandatory requirements; historical applications, private files and replay/counter integrity remain |
| AI | `CareerAiService` uses existing `AiManager` / `OpenAiCompatibleProvider`, server-configured Groq/model and `AiUsageRecord`; drafts require human review; advice requires explicit context-sharing consent |
| Product surfaces | `CareerCentre`, `CareerReview`, corresponding Blade views, named routes, shared menu and learner dashboard link; paginated admin queues/status filter; existing CV/portfolio/interview/mentorship entry points |
| Schema | `2026_11_02_010000_add_career_intelligence_records.php`: five nullable job review fields plus the uniquely owned/deduplicated evidence ledger. No course, enrollment, wallet, credential or existing application records are rewritten |
| Tests | `CareerIntelligenceTest`, `AdzunaSearchIntegrityTest`, existing `CareerIntegrityTest` and `tests/Browser/career-intelligence.mjs` |

The application submission service now requires the actual authenticated active verified owner. Two existing service-level regression fixtures were given that authenticated identity; their private-file and forced-rollback assertions were retained. There are no fabricated verified credentials or automatic competency awards in production data.

## Validation — 2 November 2026

- Initial branch suite: **444 passed / 2,180 assertions**. Core suite: **474 passed / 2,295 assertions**. Final complete suite after review pagination, source privacy and legacy certificate compatibility: **476 passed / 2,304 assertions**, 143.97 seconds. Latest intermediate career-focused run: **39 passed / 155 assertions**; the final suite includes source-summary privacy coverage and approved/legacy-issued certificate cases.
- `npm run build` passed twice, including the final 6.41-second build. The existing PostCSS missing-`from` warning remains. Blade cache, the two named career routes, changed-PHP syntax, focused Pint, browser-runner syntax and `git diff --check` passed. No application dependency was added.
- The career migration passed forward preservation of every synthetic legacy vacancy/application field, empty rollback and reapply. A rollback with recorded evidence refused before any schema/data removal. This is intentional: deployed evidence/review history requires a forward compatibility migration, not destructive rollback.
- Authenticated Chrome passed **29 responsive states** at **390/768/1024/1440px**: gaps, pending evidence, reviewer decision, requirement review, eligible/preferred gaps, consented advice/history and external provider links at every width; the 390px journey also exercised the real internal application form, Escape dismissal and an actual persisted submission. No uncaught JavaScript/local HTTP 5xx errors or document horizontal overflow were recorded; each state retained one visible primary heading and applied shared styles.
- Browser authentication used the actual login/CSRF endpoint with separate synthetic learner/reviewer sessions. Provider responses were controlled temporary-router stubs; no live Groq or Adzuna network call is claimed. External fonts/analytics/MathJax and service workers were excluded. Viewport screenshots and records are under `/tmp/bootkode-career-browser-evidence`; mobile gap and desktop requirement-review screens were visually inspected. Earlier browser attempts stalled under severe host memory pressure; the complete final run passed after temporarily stopping this checkout's Vite process. Vite was restarted afterward.
- Databases were explicit `_test` schemas on an independent temporary MariaDB at loopback port **3312**: `bootkode_ecosystem_test`, `bootkode_browser_test`, `bootkode_career_migration_test`. Tests did not alter the working environment files or real records. Logs: `/tmp/bootkode-career-final-suite.log`, `-focused-final.log`, `-migration.log`, `-final-build.log` and `-browser.log`.

## Setup and remaining release gates

1. Restore normal operation of the existing recovered database service on port 3308, verify its records and back up affected schema/vacancy/application records before a normal forward migration. This slice did **not** apply its migration to the working database. Do not replace recovered data with the temporary fixture schemas.
2. Existing provider setup remains in `config/bootkode_ai.php` / `docs/bootkode-ai.md`. Optional `BOOTKODE_AI_CAREER_DAILY_REQUESTS=20` controls a combined per-account advisor/extraction budget; provider attempts reserve usage before network work. Failed/invalid extraction consumes that budget and records a safe failure reason. Token counts are recorded when supplied; no cost is invented when pricing/usage is unavailable.
3. Configure Adzuna with existing server-only `ADZUNA_APP_ID` / `ADZUNA_APP_KEY`. Account-specific access, available countries, licence/attribution terms and live application destinations need operator/staging verification. Search results remain short-lived display projections, with no permanent vacancy-content import, full-description promise or external application-submission claim.
4. Current Composer audit reports **7 advisory reports / 3 packages**, including a critical Laravel report; npm reports **4 vulnerable dependency entries (1 high / 3 moderate)**. Reports: `/tmp/bootkode-career-composer-audit.json` and `/tmp/bootkode-career-npm-audit.json`. Nothing was suppressed or upgraded across majors. Release is not cleared.
5. Matching is literal and evidence-based, not semantic equivalency, a professional accreditation decision or a hiring guarantee. Admins must name the required competency/proficiency accurately and check evidence relevance/authenticity. Existing-course options are bounded, published whole-skill matches; they do not enroll learners or establish competence.
6. C2–C4 above remain open: persistent pathways, shared demand/generation, reassessment notices, richer verified CVs, employer verification/consented discovery, designated counsellors/private appointments and verified outcome analytics. Four-width targeted acceptance is not full-theme, screen-reader, restored-data, all-source-type or live-provider acceptance.

Legacy compatibility: career certificate selectors, advisor context and evidence validation use the existing `Certificate::approved()` scope, preserving both `approved` and historical `issued` credentials. A non-null revocation timestamp or revoked status makes a credential unusable for current eligibility. Tests cover both original status representations without rewriting historical identifiers/files.

## Git delivery

Branch: `feat/career-opportunity-intelligence`, based on the preserved onboarding work.

- `6037e195` — truthful, bounded and credential-safe external vacancy search.
- `2987634f` — reviewed requirements/evidence, eligibility/application integration, Career Centre/review UI, configured-provider AI, migration and regression/browser coverage.
- `342754d1` — historical issued-certificate compatibility and revocation regression coverage.

Ordinary `git push --set-upstream origin feat/career-opportunity-intelligence` failed because Git could not read a GitHub HTTPS username (`No such device or address`). The implementation and this documentation remain local; no remote delivery/deployment is claimed. No force push or shared-history rewrite was attempted. Once transport credentials are available, use an ordinary push of this branch.
