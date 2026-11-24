# Harden publishing authorization, reporting integrity and Laravel 12 operations

## Summary

This change closes the enterprise-readiness gaps identified in BootKode's publishing, CMS reporting, mock-interview and scheduler workflows. It preserves the existing Laravel/Livewire modular-monolith architecture and operational data boundaries.

## What changed

- Added an explicit publishing access matrix for blog, page, content and newsletter administration.
- Added persistent Livewire authorization guards so sensitive components recheck the current role on every request.
- Restricted blog image upload to authenticated, verified and authorized staff, with request throttling and controller-level authorization.
- Replaced generated CMS visitors, revenue, conversion and Core Web Vitals with persisted page counters and honest unavailable states.
- Replaced generated interview scores with recorded participation metrics; unsupported assessment dimensions remain unassessed until a real review process supplies evidence.
- Bound interview completion and results to the authenticated owner and recorded server-side question timing.
- Consolidated all Laravel 12 schedules in `routes/console.php` and removed the unused legacy console kernel.
- Kept newsletter, job-alert, reminder and withdrawal automation behind explicit environment gates. Withdrawal processing remains disabled by default.
- Updated Laravel to 12.73.0 and resolved Symfony 7.4 packages against the deployed PHP 8.2.12 floor without suppressing advisories.
- Extracted publishing authorization, page analytics projection and interview completion services from large components.
- Updated architecture, design, audit, refactor, verification and deployment documentation.

## Verification

- Complete suite: **547 tests / 2,887 assertions** in **115.17 seconds** on the dedicated `_test` database.
- Focused publishing/reporting/scheduler suite: **15 tests / 133 assertions**.
- `composer validate --strict --no-check-publish`: passed.
- PHP 8.2 `composer check-platform-reqs`: passed.
- Unsuppressed locked Composer audit: zero advisories.
- `npm audit --audit-level=moderate`: zero vulnerabilities.
- Production Vite build, Blade compilation, changed-PHP syntax checks and `git diff --check`: passed.
- Targeted Chrome checks at 390, 768, 1024 and 1440px found no document overflow or local page/JavaScript error on the changed analytics/results surfaces; learner blog-administration access returned 403.

## Release gates

Keep this PR out of production until both GitHub Actions jobs pass for the exact reviewed SHA and a real staging environment completes the deployment checklist.

- Configure host cron to run `php artisan schedule:run` every minute and confirm scheduler logs.
- Keep `WITHDRAWAL_PROCESSING_SCHEDULE_ENABLED=false` until Paystack sandbox replay, reconciliation and operator acceptance pass.
- Keep external delivery schedules disabled until queue workers, SMTP/provider transport, dry runs and unsubscribe/cancellation behavior are accepted in staging.
- Rehearse pending migrations against a restored isolated database and preserve a validated database/upload/APP_KEY backup before any production maintenance window.
- Complete HTTPS/proxy/session, provider, queue, backup/restore, representative load and accessibility acceptance.

No production deployment, live provider delivery, host scheduler execution or payment settlement is claimed by this PR.
