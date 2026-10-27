# BootKode product audit

Audit started 20 October 2026. This is a large existing product: 273 baseline routes, 86,450 lines of application PHP, seven dashboard roles, and multiple commercial and education domains. Static inspection establishes risks; only the verification report establishes tested behavior. Do not interpret this audit as production certification.

## Baseline and safety

- Branch started at `d034cad3` on `main`; work uses `refactor/bootkode-product-coherence`.
- Pre-existing changes: sidebar collapse markup, local profile tabs, `ProfileTabMarkupTest`, compiled CSS and manifest. Those changes were preserved. During the work, commit `9d4892fa` appeared on the feature branch and included the original source/test edits along with accumulated implementation; it was retained without rewriting history.
- Runtime PHP 8.4.26; locked Laravel 11.46.1, Livewire 3.6.4, Tailwind 3.4.17, Alpine 3.14.9, Vite 7.0.6. Tailwind 4 plugins are listed but the active PostCSS pipeline uses Tailwind 3. No major upgrade planned.
- Baseline `npm run build`: passes; stale Browserslist, PostCSS `from`, and 1.13 MB JavaScript chunk warnings.
- Baseline `/` HTTP: 200. Local MariaDB recovered database has all 37 migrations applied. Automated tests use `bootkode_test`, never the recovered database.
- Baseline test results and final results are recorded in `verification.md` when execution completes.

## Prioritized findings

| Priority | Location | Finding and impact | Resolution |
|---|---|---|---|
| Critical | `Auth/AuthController::getRoleValidationRules` | Guest can submit `super_admin` or other staff roles; the visible select is no security boundary. | Restrict registration to learner accounts; role assignment stays administrative. |
| Critical | `Financial/Admin/*`, financial routes | Several components have no authorization and expose financial information; PaystackSettings serializes the secret key. | Persistent role middleware, request-time component authorization, configuration-only credentials. |
| Critical | `StudentManagement/CourseCatalog::enroll` | Direct Livewire action enrolls in any course without checking publication or price. | Resolve published/approved course on server; route paid courses through checkout; transactional free enrollment. |
| High | `PaystackService::processWalletFunding`, `Wallet` | Replay check and ledger mutations occur without transaction/row lock; verified amount/currency not matched to local intent. | Atomic verified funding and locked, positive-value ledger writes with rollback tests. |
| High | `routes/web.php`, `bootstrap/app.php` | Signed gateway webhook requires a browser login and CSRF session. | Public endpoint with only that path exempted from CSRF; mandatory configured-key HMAC. |
| High | `WalletService` | Early returns leak manually opened transactions; purchase uses wrong enrollment relation metadata. | Transaction closures, correct enrollment records and idempotency. |
| High | CBT routes/components | Management and results URLs are public; management mutations lack role checks. | Require authentication and role enforcement on every management request. |
| High | `ProgressService` | References nonexistent Core UserProgress and unlocks sections globally for all learners. | Use actual Assessment model; learner-scoped access evaluation. |
| High | `public/service-worker.js` | Authenticated course HTML and arbitrary storage requests enter shared browser caches. | Keep explicit offline packs; stop implicit caching of private course responses. |
| Medium | `Pages/Home`, homepage Blade | Database queries in Blade; hardcoded 52%/Module 4 workspace resembles real progress; guest course links lead to signup. | Query in component, factual editorial hero, public discovery of actual published courses. |
| Medium | `app.css`, shared components | Five theme palettes, primitive Breeze buttons, random bright legacy colors, gradient substring overrides with `!important`. | Semantic tokens, compatible primitives, explicit shared classes, remove substring color remapping. |
| Medium | dashboard layout/student view | Duplicate welcome headings, eight metric tiles before learning, decorative animated backgrounds. | Prioritize continue-learning and restrained page headers. |
| Medium | JavaScript entry | Imports separate Alpine alongside Livewire Alpine; eager editors/scanner/chart; unguarded Livewire access on offline events; alert feedback. | Use Livewire-owned Alpine and consistent status feedback; isolate dependencies where contracts permit. |
| High | certificate model, migration and learner search | Existing workflow fields were absent from the schema; ungrouped certificate-number search could escape learner scoping. | Forward compatibility migration, historical token/file preservation and ownership regression tests. |
| High | `ManualPages/Statistics` | Unused public Livewire properties carried private names/revenue and simulated activity despite aggregate-only rendering. | Only compute the factual aggregates actually displayed; scope course data to public records. |
| High | dependencies | Baseline audits identify vulnerable PHP and JavaScript dependency trees. | Compatible npm updates and unused package removal; retain unresolved advisories as release blockers. |
| Medium | `CoursePolicy`, auth provider | Policy methods are mostly scaffold denials; policy registration is incomplete. | Preserve existing feature-level checks; document ownership boundaries before broad policy changes. |
| Medium | `tests`, `database/factories` | Missing namespaced UserFactory; starter tests reference removed profile contracts and old redirects. | Restore real factory and align tests to supported routes without weakening behavioral checks. |

## Feature inventory

See `route-inventory.md` for exact URLs, names, handlers, and middleware. Status here describes implementation evidence, not integration certification.

| Course content acquisition | Role-guarded Course Content Studio, bounded freeCodeCamp Markdown import, staged AI generation and run history | Implemented as a draft workflow; source adapter/licence scope is deliberately narrow. Live Algorithms import/queue acceptance is recorded; broader legal/source review and live AI provider acceptance remain release gates; see `course-content-system.md` and `verification.md`. |

| Journey/domain | Existing implementation | Access and status |
|---|---|---|
| Public discovery | Home, pricing, CMS PageController, blog, marketplace, certificate verification | Implemented; homepage course evaluation forced signup. Public content is database backed. |
| Account | AuthController, PasswordController, SocialAuthController, UserManagement/Profile | Implemented; seven roles use Spatie plus legacy role column. OAuth needs provider credentials. Registration escalation requires immediate repair. |
| Learn | CourseCatalog, AvailableCourses, EnrolledCourses, CourseView, LessonProgress, lesson_user/course_user, CourseEnrollment | Implemented; two enrollment surfaces and historical tables require compatibility. CourseView contains substantial lesson/progress/offline workflows. Paid enrollment bypass identified. |
| Instructor | Course builder, quality controls, reviews, instructor dashboard, earnings | Implemented; preserve course ownership and approval gates; no wholesale replacement. |
| Administration | Seven dashboards, users/roles, course approvals, CMS, support, newsletter, observability | Implemented; menu permissions are not sufficient authorization. Large components need staged action-level review. |
| Money | WalletService, PaystackService, financial components, withdrawals, refunds, commercial documents | Implemented but unsafe funding/purchase boundaries found. Gateway sandbox and concurrency checks required before launch. |
| Credentials | CertificateRequest, StudentCertificates, CertificateManagement, CertificateService, public verification controller | Implemented; completion uses historical lesson_user and course_user. Preserve existing certificate records and public verification codes. |
| Careers | Jobs, applications, portfolio, resume, mock interviews | Implemented; templates/services present; external opportunity accuracy is not verified by code inspection. |
| Support and community | Mentorship sessions/reviews, direct messages, community center, tickets | Implemented; many independent interactive components. Preserve relationships and private data boundaries. |
| AI | LearningAiService, CodingMentor, providers, usage/conversations | Implemented; missing credentials must remain explicit; existing privacy/provider unit tests. |
| Offline/PWA | service worker, offline packs/notes, manifest and standalone offline pages | Implemented; explicit user-owned packs must be retained while removing implicit private HTML caching. |
| Institutions/affiliates/marketplace | InstitutionService, bulk jobs, licenses, AffiliateService, marketplace services/policies | Implemented; distinct commercial workflows retained. Not certified without fixture-backed integration tests. |

## User journeys and recovery

1. Visitor → published catalogue → course summary → account → enrollment/checkout. Empty search offers reset; unpublished courses return 404; paid learning requires successful wallet purchase.
2. New learner → registration → email verification → learner dashboard → first course. Invalid fields retain safe input; privileged roles cannot be self-selected.
3. Returning learner → enrolled courses/continue lesson → completed lesson → progress → eligible certificate request. Existing lesson and certificate records remain authoritative.
4. Account holder → wallet → verified funding → transaction history/receipt. Failed verification leaves ledger unchanged; retry returns consistent success without duplicate credit.
5. Instructor → own course management → quality review → publication; admin → approvals and operations. Unauthorized direct URLs/actions return 403 rather than relying on hidden navigation.

## Inspection limitations

Route/template inspection spans the product; not every one of hundreds of operational screens has been manually exercised. Browser checks, covered workflows, integration limitations and unresolved issues must be named in the final verification report. No customer data or paid provider transactions should be created to manufacture evidence.

## Implementation outcome

The shared design system, public discovery, account flow, learner shell, finance surfaces and critical access/payment/credential/privacy boundaries were implemented. See `verification.md` for current evidence and outstanding release gates. This is not certification of every legacy domain screen or external integration.

## Operational continuation, 21 October 2026

A follow-up against the original scope found feasible implementation gaps beyond the first core-journey delivery:

| Location | Verified defect | Implemented repair |
|---|---|---|
| `CertificateTemplates` and its view | Sample cards; save/update/delete/default only announced success, preview ignored selected settings | Persisted designs, service-side administration checks, validated styles/fonts, actual isolated preview, default uniqueness and issuance snapshots |
| Career migrations | `job_applications.job_id` and `job_saves.job_id` reference the queue `jobs` table | Forward migration preflights unmatched rows before correcting both references to `job_portal`; no records removed |
| `Career/JobManagement` | Any authenticated account could open the route/component and mutate jobs or inspect applicants | Persistent role middleware and request-time component guard; action and sorting validation |
| `Career/JobPortal` | Detail model eagerly carried applications; direct actions accepted unavailable jobs; submission referenced component constants instead of model constants; creation/counter were separate | Published job resolution, restricted poster fields, no applicant relation in discovery, shared atomic application service, retry protection and stale availability checks |
| Job résumés | New applications stored uploaded résumés on the public disk | Private local uploads and authenticated applicant/admin downloads; old paths retain compatibility. Existing public files require a separately backed-up asset migration before assuming historical-file privacy |
| `CommunityCenter` | Moderation methods/data relied on hidden UI; learners could request other feedback, block accounts or remove discussions; submissions invented a score of 100 | Server-side administration guards, protected administrator/self account actions, explicit pending submissions with no grade and preservation of reviewed submissions |
| Community code challenge modal | Opening an existing challenge displayed the creation form | Existing challenges open a submission form; creation remains separate |
| Career/community views | Oversized/colored headers, unbounded tabs, untrapped dialogs, dynamic color classes and duplicate feedback patterns | Shared page headers/feedback, semantic tab states, bounded navigation, labelled forms and focus/Escape handling |

Job alerts and moderator replies still lack backing delivery workflows. Their actions now state that availability truthfully rather than announcing a nonexistent successful operation; they are not new completed integrations. Verification evidence is recorded in `verification.md`.

## Institutional, marketplace and mentorship continuation, 21 October 2026

| Location | Confirmed defect | Resulting behavior |
|---|---|---|
| Institution components/invitations | Cross-institution operations were mount/menu guarded; school administrator creation granted a global academy role; invitation GET authenticated recipients | Request-time platform guards, scoped membership portal, student global role for new members, signed-in recipient review and explicit locked POST acceptance |
| Institution enrollment | Public form selections could bypass canonical course availability and cohort membership scope | Services/jobs recheck available courses; assignments validate active membership in the actual institution |
| Marketplace payments/refunds | Wallet checkout/payout/refund paths duplicated writes or could credit without matching funding; independently rounded splits could create a kobo | Shared locked/atomic payment evidence, replay protection, exact minor-unit split, currency checks, recovered payout reversal and invoice rollback |
| Marketplace discovery/files | Ungrouped order search could escape ownership; public models carried paid file paths and unapproved reviews; download reported success without a file | Grouped ownership predicates, approved reviews only, hidden file payloads, actual paid-owner downloads and explicit missing-file feedback |
| Vendor listing uploads | Image/file controls accepted uploads but save discarded them; JSON categories shadowed the pivot relation | Validated persisted uploads, private paid files, cleanup on failed save, retained files on edit and explicit pivot reads |
| Mentorship | Legacy actions trusted unrelated IDs; non-student hub queries exposed others' data; payout could create money without a payer debit | Requeried participant/mentor authorization, scoped hub reads, paired locked wallet transfers and rollback/replay coverage |
| Mentor profile and existing records | Misnamed appended accessors/UTF-8 star count crashed real profile serialization; missing dates crashed populated pages; rates displayed USD beside NGN settlement | Valid accessors, clamped numeric star counts, explicit unrecorded dates, consistent NGN labels |
| Domain interfaces | Duplicate/missing primary headings, mobile business/settings overflow, untrapped dialogs and dead resource links | Shared root headings/feedback, bounded navigation, focus/Escape handling and explicit unpublished resource state |

No schema migration or working-database mutation is required for this continuation. Operational acceptance is representative, not a claim that all legacy actions, white-label delivery, institution billing or provider integrations are complete. Current checks and precise limits are recorded in `verification.md`.

## Support, authoring and affiliate continuation, 22 October 2026

| Area | Verified problem | Implemented change |
|---|---|---|
| Support and feedback | Unguarded staff components, leaking OR searches, public new attachments and disconnected response controls | Request-time staff/owner guards, grouped predicates, private uploads/authorized downloads and locked real response actions |
| Direct messages | A forged active conversation ID could affect another thread's read state through polling | Participation is rechecked before read markers, incoming notices and last-seen updates |
| Course authoring | Nested editors accepted unrelated lesson/assessment IDs; generic uploads accepted client-supplied validation arguments | Shared fresh course ownership guards, locked IDs/metadata, protected upload helper, scoped mutation queries and preserved array casts |
| Historical learning | Course/account deletion could cascade through real enrollments, progress and issued verification records | Course-row locks and transactional history guards, model-aware bulk deletion, recoverable account refusal before logout/avatar cleanup |
| Affiliates | Incorrect relations and commission writes without matching original purchase allocation | Locked funded debit matching, snapshot-based exact settlement, replay-safe counters and corrected net course analytics |
| Affiliate tools | Nonexistent banner destinations, invented usage copy, simulated clipboard success and nonpersisting global settings | Real escaped SVG banners, factual templates, browser-confirmed clipboard feedback and explicit read-only server configuration |
| Public marketplace routes | Detail/vendor/category closures did not mount the actual full-page component correctly | Named Livewire routes resolve real published items/vendors, with authenticated detail resolution and missing-item 404 |

The browser also exposed a course share dialog outside its Alpine scope, tablet builder overflow and narrow affiliate controls. These received targeted shared-modal/layout fixes. See the verification report for final accepted samples and limits; legacy interior theme coverage is still incomplete. No working-database record or schema was changed.

## Learning rewards, analytics and CBT continuation — 22 October 2026

| Finding | Implemented correction |
|---|---|
| Reward pages rendered raw partials/called nonexistent methods; games manufactured random successful scores | Real Livewire routes, actual ledger-backed rewards and explicit unpublished game states |
| Stale/negative virtual currency operations, replayed daily/quest awards and incomplete store delivery | Positive locked mutations, persisted daily/quest evidence, atomic supported redemption and replay/rollback tests |
| Duplicate analytics implementations accepted another user ID, referenced absent fields or estimated missing study time | One owner-scoped service/component, real enrollment/progress/session/grade sources, no invented duration or forecast |
| Shared leaderboards cached full account models and private users | Explicit opt-in plus public-profile gating; current four-field projections and withdrawal checks |
| Goal controls did not persist targets | Validated owner-only goals saved through the existing table, with a real activity CSV download |
| CBT payloads contained answer keys and trusted client attempt state | Safe question projection, locked properties, persisted server attempts, owner/eligibility/deadline validation and saved answers |
| Replayed or edited-question grading could change recorded outcomes | Atomic answer/result writes, replay-safe completion and private question snapshots; analytics prefer recorded result snapshots |

This removes further feasible defects beyond the previous handoff. No existing data/schema was migrated or deleted in the working database. Games, unsupported reward delivery, achievement broadcasts and reviewed path generation remain explicitly unavailable rather than claiming fabricated success.


## Lesson assessment and manual review continuation — 22 October 2026

The lesson assessment component had a separate, unsafe attempt path: public question models included answer keys, assessment IDs were not tied to the current lesson, written responses could be treated as graded, and a clear action deleted historical answers. CBT instructor management listed other instructors' courses/participants and exposed global-ID mutation paths. Personal result dialogs could load unrelated assessment definitions, recalculated old marks from edited questions, and the named summary route referenced a nonexistent method.

Lesson assessments now reuse `CbtAttemptService` and its private grading snapshots, persisted drafts, server deadlines, locked identifiers, maximum attempts and atomic submission. Written responses stay pending with no published percentage/pass/reward until every written answer is reviewed. Course owners/platform reviewers use the new Assessment Reviews queue; marks are bounded by the original points, carry the real reviewer/time/feedback and publish the final result transactionally. A replay returns the recorded mark; silent corrections are refused. Multiple selection uses exact-set grading. Personal result screens expose owned result summaries, original submitted responses and instructor feedback without grading keys. Missing pass-mark configuration cannot start an attempt or pass legacy zero scores. Attempt history is retained, including standalone CBT deletion guards. Instructor CBT CRUD, ordering, participant views and course choices are owner-scoped.

Parent/nested lesson requests recheck enrollment and actual lesson/course membership. Published lesson access follows the existing section completion threshold using recorded progress. Progress writes, new certificate eligibility and new lesson rewards cannot bypass pending assessments. Existing certificates and historical completion records are not revoked or rewritten. Legacy written answers without a persisted attempt snapshot require a separate backed-up records review; they are not automatically marked or inserted into the new queue.

Database verification also found an implicit MariaDB `ON UPDATE CURRENT_TIMESTAMP` rule on `learning_sessions.started_at`, which reset the server clock on draft/grade updates. Forward migration `2026_10_22_020000_preserve_learning_session_start_time` gives that column an explicit default without automatic updates. It was exercised in dedicated databases, backed up before application, and preserves every existing row. The working table contained zero rows. This prevents future clock changes; it cannot reconstruct historical timestamps that had already changed.

See verification.md for the exact test, browser, migration and preservation evidence. Matching/ordering/drag-drop players, formal mark corrections, legacy manual-record reconciliation, provider/dependency/legal gates and exhaustive accessibility/theme acceptance remain outstanding. This is additional completed implementation, not whole-product production certification.

## Structured questions and assessment authoring — 22 October 2026

The next inspection found matching accepted without usable pairs, multiple-choice writes that double-encoded JSON and silently shifted correct indices when blank options were filtered, attempt-limit controls that did not persist, and assessment duplication/reordering that could reach another lesson. Question statistics recursively read their own accessor. Legacy grading helpers treated ordered answers as unordered sets and could attempt automatic marking of written work.

Matching, ordering and drag-and-drop now have complete authoring and learner controls using the existing question, attempt, answer and result records. Versioned structured definitions are validated before starting; choice permutations remain private, saved answers preserve their target positions, exact grading uses immutable original mappings, and original submitted labels remain available in owned result history. Untouched controls do not invent answered questions. Legacy malformed definitions require instructor setup rather than automatic conversion or invented grading.

Authoring now preserves option positions and the literal option `0`, validates complete marking keys, stores arrays through the existing model casts, persists real attempt limits, and scopes copies/reorders to the actual lesson/assessment. Copying all questions is atomic and never copies learner history. Shared authoring screens and a contextual full workspace replace decorative format cards and nonfunctional switches. Three unreferenced assessment partials were removed after a repository reference search. Question statistics and legacy exact-key/manual-review helpers now match the supported rules.

Native pointer dragging, labelled selection alternatives, sequence buttons and physical Enter-key activation were verified. A discovered focus loss after Livewire row moves is fixed through the existing dashboard entry, including instructor reordering. See verification.md for precise evidence. Formal mark correction, legacy reconciliation, richer project/file/peer-review delivery, remaining legacy editor acceptance and the prior provider/dependency/legal/accessibility gates remain separate work; the three new interaction formats are no longer unimplemented.

## Recorded mark review continuation — 22 October 2026

| Area | Confirmed gap | Implemented behavior |
|---|---|---|
| Review queue | Finalized submissions disappeared; changed marks were refused without a usable correction workflow | Scoped pending/requested/finalized filters; explicit bounded written-mark corrections retaining prior marks and result snapshots |
| Correction integrity | No stale-decision, reason or correction replay boundary existed | Server-issued locked revision/operation context; user/attempt/answer locks; atomic current mark/result/history writes and exact replay handling |
| Learner result review | Learners could read feedback but could not request reconsideration or inspect a correction | Owned requests from both existing result surfaces; one request per recorded mark version; persisted upheld/corrected decisions and safe correction history |
| Mixed review display | Structured automatic responses could appear alongside manually reviewable answers | Automatic question types are consistently excluded from manual marking/correction controls |
| Learning history | Changing a grade must not silently rewrite issued credentials or historical rewards/completions | Snapshot-based future eligibility changes; existing ledger, completion and credential records retained |
| Focus after mutations | Publishing a correction or sending a request removes the submitting form | Dashboard-owned focus restoration to the updated response or correction control; shared dialog behavior retained |

This extends existing attempt/answer records without a migration, historical regrade or working-database record write. It does not implement peer review, project-file delivery, external review notifications or reconciliation of legacy attempts without complete snapshots. Verification evidence and actual limits are recorded in verification.md.


## Private submission continuation — 22 October 2026

| Area | Confirmed gap | Implemented behavior |
|---|---|---|
| Project/assignment delivery | Upload requirements existed only as authoring metadata | Private saved draft attachments, frozen constraints, required-work validation, immutable submitted files and actual manual marking |
| Private grading guidance | Manual-question option metadata could expose sample answers, rubrics and grading notes, including resumed attempts | Explicit safe instructions/limit projection for every manual format and sanitation of retained public snapshots |
| File ownership/integrity | No saved learner-file boundary existed | Active-attempt locks, fresh ownership/deadline checks, real MIME/extension/size checks, private storage and owner/reviewer-only hash-verified attachment downloads |
| Authoring | Legacy forms used inconsistent palettes and stored metadata without complete scoped rules | Shared native forms, locked scoped actions, normalized object casts, validated counts/weights and atomic copy/move |
| Binary response integrity | A leading newline in affiliate configuration prefixed and truncated downloads | Remove only the leading byte; all 24 configuration files must produce no output |

This supersedes the prior project-file backlog for configured lesson projects/assignments. It adds no schema migration, dependency, automatic file execution/grading or working-record mutation. Q&A/peer-review participation, unsupported legacy reconciliation, external review notifications and broader editor/theme/provider/dependency/legal acceptance remain separate work. Evidence is in verification.md.

## Dashboard redesign audit — 23 October 2026

Inventory: 286 total routes; 223 authenticated, including 208 GET routes. Seven role dashboards and learning, authoring, assessment, certificate, finance, rewards, careers, AI, community, mentorship, institutions, marketplace, publishing, support and account workspaces share one shell. Route aliases and nested workspaces are listed explicitly rather than counted as independent implementations.

Addressed: competing hardcoded surface palettes and forced CSS remapping; oversized/decorative headers; inconsistent shapes/shadows; inaccessible wide tables and unnamed filters; duplicated mobile More navigation; learner-only shortcuts for staff; missing current-page semantics; missing dialog focus/Escape handling; a misleading always-live navbar badge; and the exposed user directory found by access testing. No fabricated records or commercial outcomes were added.

The remaining integration limits, dependency advisories, import revision failure and exact browser coverage are recorded in verification.md. The in-progress Course Content Studio and its backend/routes remain the user's uncommitted work; the shared shell integrates without claiming that feature as delivered in this redesign.


## Release continuation — 23 October 2026

Expanded browser acceptance repaired blog/résumé/interview overflow, invalid affiliate support links, system-status tab scope, interview model namespaces and the learner dashboard crash after résumé creation. Random/hardcoded interview metrics were replaced with recorded aggregates and explicit unavailable states. Final working-tree suite: 275 passed / 1,426 assertions; isolated branch: 262 passed / 1,347 assertions. Browser evidence now records 306 checks across 112 routes with no unresolved issues in those states. Composer advisories fell to 5 across 2 packages; 8 npm vulnerabilities remain. Integration/assistive-technology acceptance and authenticated Git delivery remain release work. Details: dashboard-redesign/continuation-report.md.

## Course-content continuation — 26 October 2026

The licensed-source pipeline has live browser/queue/persistence evidence for the reviewed freeCodeCamp Algorithms text subset and duplicate-safe rerun. It remains draft-only, with per-lesson licence notices and owner/subset/revision identity. AI generation has mocked end-to-end structured lesson/quiz/project-criterion, quota, authorization and stage-recovery evidence; no paid/live provider success is claimed. The existing run migration was already applied locally and no working records were seeded or migrated by this continuation. Arbitrary repositories, unreviewed curriculum blocks, media/assets/packages and automatic publication remain unsupported. See course-content-system.md and verification.md.

## Runtime log follow-up — 26 October 2026

Later log inspection confirmed MySQL stopped and local database requests refused, including failed-job inspection. At that checkpoint, the application was blocked on starting the existing service. MySQL has since been restored; see Main course-content acceptance below. The 500 fallback now renders without its database-backed footer; that improvement does not restore course/import/generation availability. See verification.md for current outage evidence and the separate earlier passing feature checks.


## Main course-content acceptance — 26 October 2026

The earlier MySQL outage has cleared (current public HTTP 200). Live configured AI generation now passes both CLI and authenticated Studio → queue → worker → existing builder, using a dedicated acceptance database. Each new draft contains two modules, four lessons, two quizzes and a capstone project. History exposes actual category/validation/quality findings and safe expansion/retry controls.

The older failed working expansion was backed up and recovered in place: three sections, ten preserved lesson IDs/original texts, three quizzes and twelve questions, still unpublished/unapproved. Every lesson retains the full verified source notice. The dedicated worker is enabled and active with user lingering. Current full suite: 332 tests / 1,602 assertions. Strict provider schemas, shared quota, ownership/history/edit preservation and exact verification limits are documented in course-content-system.md and verification.md. This supersedes the earlier live-AI and runtime blockers; human review, additional sources, dependency advisories and authenticated Git delivery remain separate gates.


## Direct course authoring — 26 October 2026

At the user's request, three original courses were created directly in `bootkode_recovered` for the authenticated super-admin: Build Your First Responsive Website (ID 4), Spreadsheet Skills for Better Business Decisions (ID 5), and Digital Marketing: Plan, Create and Measure Your First Campaign (ID 6). Each contains three modules, nine substantive lessons, three four-question quizzes and one 100-point capstone with a real manual-review criterion. Original course covers, a tested reference website ZIP, a fictional CSV and a campaign worksheet exist in public course-material storage. These are working drafts, not acceptance fixtures or published courses.

The two pre-existing courses and learning/wallet/certificate records were backed up/fingerprinted and verified unchanged. Cases and figures are explicitly fictional teaching examples. Mechanical quality labels are disabled on the new drafts pending editorial review. The quality scanner now parses real markup and excludes code examples/plain-text copies; the course outline follows curriculum order rather than creation time. See verification.md for exact evidence.


## Assessment authoring in the lesson — 26 October 2026

Assessment authoring now has one visible context inside the course builder's Assessment tab. Removed the separate-workspace link and standalone authoring mode; existing named lesson-authoring URLs authorize the editor and redirect to the correct course/lesson/tab. Creating/editing assessments and managing questions or project criteria stay in the lesson. Keyed view transitions repair a browser-only stale-list defect when opening forms. Assessment records, grading, learner attempts and the learner assessment route are unchanged.
## Original imported-course editorial revision — 26 October 2026

Courses 1 and 2 were revised directly in the working application after inspecting their real curricula and confirming no learning/financial history. Practical Algorithms with JavaScript now has 3 modules / 11 lessons / 3 five-question quizzes / one marked capstone. JavaScript Data Structures: Arrays and Objects has 3 modules / 13 lessons / 3 five-question quizzes / one marked capstone. Both include original worked explanations, practice feedback and actual downloadable reference/verification kits. Corrected binary-search return format, duplicate-index pairing, quantity/name inventory rows, repeated-letter counting and copying/complexity claims; replaced ambiguous old quiz answers.

Existing course, section, lesson, assessment and question identities were retained; old slugs and source-run revisions/notices were preserved. Two foundation lessons, two data-structure sections, three data-structure quizzes and two capstones were added. Other courses and learning/payment/certificate records are unchanged by fingerprints. These remain unpublished/unapproved drafts with public mechanical endorsements disabled; human editorial acceptance and external-link checks remain release gates. See verification.md for executable and browser evidence.

## Groq integration and log review — 27 October 2026

The existing Groq provider is enabled and passes live plain/structured requests. Added an explicit administrator connection test and operator command, provider-specific safe failure guidance and a current-role guard for mentor Livewire requests. Existing mentor/course-generation/expansion services retain server-side keys, authorisation, quotas and draft review. No working course was generated or replaced by verification.

The 137 extra storage/logs files were historical verification captures and a worker console capture, not daily Laravel rotation. They are SHA-256-verified in private storage/app/diagnostics/legacy-logs-20261027.tar.gz. Only laravel.log remains; active worker console output uses journald. Application logging explicitly selects single. The latest local application failures were queue-worker MySQL connection refusals during startup; a current SQL query, login HTTP response and running worker verify recovery. Earlier testing warnings exercise expected refusal paths. No log history was erased.
