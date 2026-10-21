# BootKode architecture

BootKode is a Laravel 11 / Livewire 3 modular monolith. Blade and Alpine support server-rendered journeys; no new frontend framework is required.

## Responsibilities

- `routes/web.php` and `routes/auth.php`: stable named URL contracts, authentication, verification and role middleware. Laravel 11 middleware registration belongs in `bootstrap/app.php`; the old `app/Http/Kernel.php` is not the active registration mechanism.
- `app/Livewire/<domain>`: interactive form state, validation, authorization, pagination and orchestration. Check sensitive authorization on every request, including Livewire hydration.
- `app/Http/Controllers`: HTTP-only callbacks, auth, downloads and public verification. Gateway callbacks delegate verified financial mutations to services.
- `app/Services`: multi-record operations and reusable business rules. Transactions must not escape through early returns; financial operations lock current records before checking balances or statuses.
- `app/Models/<domain>`: persistence, relationships, casts and domain invariants. Core User has both a legacy `role` column and Spatie roles; use role APIs for authorization.
- `resources/views/layouts`: public, dashboard, blog, pages and exam shells. Exam has deliberate distraction-free behavior; do not turn administration into marketing.
- `resources/views/components`: static shared UI; Livewire is reserved for server interactions.
- `resources/css`: theme and semantic design tokens; `resources/js`: browser enhancement. Livewire owns Alpine.

## Data compatibility

`course_enrollments` stores enrollment/payment/completion metadata. Historical `course_user` stores learning pivot/progress. Existing CourseView reconciles historical enrollments. Maintain both when enrolling; do not delete historical tables as a cosmetic cleanup. `lesson_user`, LessonProgress and assessment UserProgress have distinct existing consumers; consolidate only with migration and behavioral coverage.

Wallet writes must atomically persist balance and ledger, reject nonpositive amounts and use row locks. Funding must verify gateway reference/status/amount/currency against a stored wallet-funding intent; transaction status, credit and receipt belong in one database transaction. Gateway webhook is public and HMAC-authenticated. Browser return URLs alone never establish payment success.

Public discovery only reads published, approved courses. Learning content still requires enrollment. Certificate verification codes remain stable. Server-side completion and role/ownership rules remain authoritative.

## Operational boundaries

MariaDB `bootkode_recovered` contains existing data and is not disposable. Tests use an explicitly dedicated database. Never run fresh/reset migrations against the working database. Schema changes require forward migrations. The certificate compatibility migration widens status storage, adds missing workflow fields and copies historical verification tokens/file paths only when the new fields are empty. The working database was backed up and the migration was validated on the dedicated test database before local application.

Provider credentials remain in environment/configuration, never Livewire public properties. Receipt, payout, newsletter and AI integrations need independent staging verification.

See `product-audit.md`, `design-system.md`, `refactor-roadmap.md` and `verification.md` for decisions and evidence.

## Saved certificate designs and career applications

`CertificateTemplate` stores validated design settings, not arbitrary HTML or Blade. `CertificateService` owns writes/default changes/deletion; only academy/super administrators manage designs and only super administrators assign a default. A nullable unique `default_slot` enforces one default in storage. The selected design is copied into existing certificate metadata at approval. Rendering uses that snapshot; editing or deleting the source design never restyles an issued record. Records predating snapshots retain the historical design and tokens/files. Preview HTML is isolated in a sandboxed iframe with explicit non-issued placeholders; fonts are restricted to those available to the PDF renderer.

`JobApplicationService` resolves currently public/internal/open applications and locks the career job before creating an application and updating its count in one transaction. A retry returns the original application. Failed persistence cleans up a newly stored résumé. New résumés use the private local disk; the download controller checks applicant ownership or administrator roles. Legacy public paths remain readable through the guarded controller, but already-public historical assets need an independent backup/move procedure before claiming they are private.

The career compatibility migration validates both tables for unmatched records before any constraint DDL. Both application/save foreign keys target `job_portal`, never the unrelated Laravel queue table. Community moderation checks authorization inside every read/mutation boundary as well as rendering. Code solutions are pending and ungraded until a real review supplies a result; submission alone does not create a score.

## Marketplace, institutional membership, and mentorship boundaries

Marketplace wallet checkout has one boundary in `MarketplaceService::checkoutWallet`, shared by Livewire and the legacy HTTP controller. It locks the customer and current wallet, resolves published items/prices/commission on the server, and binds each paid order to its matching debit. Vendor/platform credits, order state and invoice share the transaction. A session checkout UUID returns existing customer orders on replay. Commission uses integer minor units with the vendor receiving the remainder. Settlement wallets must match the order currency. Full wallet refunds reverse actual credited wallets and restore the customer in the same transaction; insufficient recovery funds roll everything back. Card settlement and partial/external refunds are not implemented by this wallet boundary. Historical paid orders without funding evidence are retained, but cannot mint another payout through it.

Paid marketplace files now use private local storage and `marketplace.order.download` checks the paid customer's ownership. Public item serialization hides file paths and public reviews load only approved records. Legacy public paths retain guarded-download compatibility; already-public assets still require a separately backed-up move. Listing uploads persist with category assignment, preserve existing attachments on edit, and clean up new uploads on failed persistence. The historical JSON `categories` column remains untouched; `itemCategories` is the unambiguous pivot relation for reads.

Institutional `admin` and `instructor` are membership roles, not global platform roles. New institution accounts remain global students. Invitation GET requires sign-in and only reviews; CSRF-protected POST rechecks the target account/email, pending state, expiry and capacity under locks. Accepting does not log in the recipient, verify email, or promote a global role. Existing global roles are preserved and require a separate administrator review if previously over-granted. Institutional administrators have a membership-scoped portal; platform staff operate cross-institution licensing/cohorts/imports. Enrollment rechecks `Course::publiclyAvailable()` in services and queued CSV processing. Cohort assignments validate active members of the actual institution before writes.

Mentorship reads/actions re-resolve actual participant ownership. Mentor-only parent and nested components check roles on every request. Completion locks the persisted session; paid completion debits the mentee's existing NGN wallet and credits the mentor atomically, with transaction IDs in metadata. Repeated/stale completion cannot repeat payment or session counters. Insufficient funds roll back completion. Historical paid flags are preserved. New legacy-session attachments use private storage; historical public files are not relocated by this code change. Unpublished mentor resources have explicit unavailable states.

Search defaults to Scout's collection engine when Algolia credentials are absent. An explicitly configured driver remains authoritative; configured Algolia still needs working credentials and independent acceptance.
