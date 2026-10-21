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
