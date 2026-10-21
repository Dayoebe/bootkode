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
