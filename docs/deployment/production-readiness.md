# BootKode first production release assessment

2 November 2026. Target: one specialised Laravel hosting account with approximately 2 GiB RAM. No hosting purchase, DNS change or production deployment has been performed.

## Current assessment

**71/100, release blocked.** This is an engineering checklist assessment, not a security certification, load benchmark or guarantee of user capacity. Critical gates override the score.

| Area | Earned / available | Current evidence and limit |
| --- | --- | --- |
| Runtime and dependencies | 15 / 15 | Supported Laravel 12.70.2; reproducible locks; Composer and npm audits clear |
| Security and access | 12 / 20 | Existing authentication, ownership, wallet and admin coverage; new production logging/debug safeguards; live hosting security unverified |
| Data and recovery | 16 / 20 | Current records recovered and independently restored; off-host encrypted retention and recovery SLA not yet established |
| Background operations | 12 / 15 | Queued generation, checkpoints, bounded workers and retry windows; actual provider CLI allowance unverified |
| Hosting and resource fit | 5 / 10 | Conservative 2 GiB single-node configuration; no target host or representative load measurements |
| Release reproducibility | 7 / 10 | Locked installs, CI, build, preflight and runbooks; repository push credentials and a current remote CI run remain required |
| External integrations | 4 / 10 | Controlled API failure/replay tests; real mail, payment, AI, vacancy licence and provider-account acceptance remain open |

### Critical blockers

1. Confirm a host supports the documented cron CLI runtime (at least 660 seconds), `pcntl`, MySQL/MariaDB, writable persistent storage and minute-level cron. A provider that kills every process after 30–60 seconds cannot run the existing 600-second generation/import jobs through this profile. Arrange a permitted external runner or a separate smaller-stage implementation before enabling those workflows.
2. Establish private encrypted off-host backups, test a host-level restore including uploads and APP_KEY, and monitor backup age. Local restore evidence alone does not establish disaster recovery.
3. Complete HTTPS/domain/proxy, transactional email, Paystack sandbox/webhook, AI account limits, Adzuna licence/quota and media-provider acceptance in staging. Controlled fixtures are not live-provider proof.
4. Run the locked clean-install workflow and dependency gate on the delivered remote commit. Local Composer/npm audits are clear, but unavailable GitHub credentials prevent current remote CI evidence.

Official references: [Laravel support policy](https://laravel.com/docs/12.x/releases), [security advisories](https://github.com/laravel/framework/security/advisories), [deployment](https://laravel.com/docs/12.x/deployment), [queue timeout/reservation rules](https://laravel.com/docs/12.x/queues). Recheck these at release time.

## Inspected architecture and preserved contracts

The application uses Laravel 12.70.2 / PHP `^8.2`, Livewire 3.8.11 with its own Alpine runtime, Tailwind 3.4.19, Vite 7.3.7, MySQL-specific migrations, Spatie roles, private assessments and uploads, wallet/course enrollment, historical certificates, mentorship, careers, institutions and explicit offline packs. PHP 8.4 is the tested CLI baseline. Node 20.20.2 builds locally; CI uses Node 22. Keep the lockfiles; do not run updates during deployment.

Direct dependency review found existing PDF engines (Dompdf/mPDF), QR libraries, image processing, Cloudinary, Socialite/Google OAuth, Scout/Algolia, purification and permissions/activity packages. No dependency was added. Removing apparently overlapping libraries without verifying their callers would risk existing résumé/certificate/export workflows. `laravel/pail` is currently a production dependency but is a CLI tool: do not run it as a production background process. `composer install --no-dev --prefer-dist --optimize-autoloader` excludes development-only tooling; further package cleanup requires a separate caller audit.

`Course::publiclyAvailable()` remains discovery/enrollment scope, `WalletService::purchaseCourse()` remains the purchase boundary, and Paystack still verifies reference/status/amount/currency with atomic ledger/receipt writes and replay handling. Only its webhook is CSRF-exempt and must pass the configured-key HMAC. Payment POSTs are not automatically retried: an ambiguous gateway acknowledgement requires reconciliation. Issued certificate statuses, tokens, files and codes are preserved. No financial algorithm was replaced.

Database sessions/cache/queues require their migrated tables. The new read-only `php artisan bootkode:production-check` reports setup failures without printing connection credentials. It does not certify public-root configuration, provider licences, backup age, queue latency or a dependency audit. `--skip-database` deliberately fails readiness.

Production debug responses are disabled even if a stale `APP_DEBUG=true` remains. Production exception reporting records class/code location instead of raw SQL bindings or arbitrary exception messages. The rotating logger also redacts configured credentials, nested payload/query/body keys, email addresses, URL queries and exception objects. Host logs, PHP-FPM logs, database logs, SMTP dashboards, failed-job payloads and historical files still require private permissions and retention. This change does not retroactively erase old logs or claim a complete forensic secret-history scan.

## Hosting compatibility and suggested resource budget

Start with one application node, one worker at a time, database queue/cache/session, SMTP and existing Cloudinary/external video URLs. Redis, Horizon, Octane, Reverb and a persistent Node/Vite process are **not required**. Broadcasting currently uses the log driver. Do not run `composer dev`, `artisan serve`, `queue:listen`, Pail or Vite as production services.

| Resource | Initial configuration / estimate |
| --- | --- |
| CPU | 1–2 vCPU; measured staging workload determines adequacy |
| RAM | 2 GiB total; use at most two FPM children and one 256 MiB CLI worker initially |
| PHP | Supported patched PHP 8.4 for this tested branch, OPcache 128 MiB, `memory_limit=256M`, `max_execution_time=60` for web |
| Database | MySQL 8 or supported MariaDB 10.11+; InnoDB buffer pool initially 128–256 MiB when DB shares the 2 GiB host |
| Rough RAM reservation | Web/FPM 384–512 MiB, one worker 192–256 MiB, database 256–384 MiB, OPcache 128 MiB, OS/web overhead 240–320 MiB; retain the remaining headroom |
| Disk | Allow 15–20 GiB initially for releases, uploads, database and logs; backups/media stay off-host; set alerts and revise from measurements |
| Build | Build on GitHub CI or a development/build machine; do not overlap Composer/Vite builds with live workers on the 2 GiB account |

These are estimates, not measured RSS or concurrent-user claims. No representative production load/Lighthouse/RUM result exists yet. Local test time is strongly affected by workstation memory pressure. Monitor FPM RSS/queue, MySQL slow queries, response p95, worker RSS, queue age, failed jobs, disk and API usage before increasing concurrency.

Required PHP extensions include the framework defaults plus `pdo_mysql`, fileinfo, mbstring, curl, dom/xml, GD, intl, zip, bcmath and CLI pcntl. `composer check-platform-reqs --no-dev` is authoritative for the lock. Image optimizers may need optional host binaries; confirm the actual upload route. Redis requires phpredis or an installed supported client; only configuration was tested locally, not a live Redis server. The S3 adapter is not installed merely by setting `FILESYSTEM_DISK=s3`; provision and test its compatible adapter first. Current private local uploads remain supported.

## Workload inventory and limits

| Workflow | Execution and controls | Operational limit |
| --- | --- | --- |
| AI course generation / imported expansion | Existing database-backed run ledger, validated saved blueprint/module/section stages, input idempotency, processing locks, after-commit jobs, two attempts, 600-second job timeout, 960-second reservation | Failed stages resume; provider requests already accepted before a crash can still be charged again. Daily course quota includes failed requests. Human review remains mandatory. |
| Repository import | Fixed-host/DNS-pinned size-bounded client, staged run records and replay guards, 600/960-second queue window | Source licences/revisions and GitHub quotas remain required. |
| Full media QA | Queued with current reviewer authorization; public HTTPS HEAD-only checks, TLS verification, DNS pinning, no redirects/private IPs, ten-minute metadata cache, 45-second soft network budget | Unconfirmed/redirected/restricted links remain unchecked; no video download. Retry can reuse cached results. DNS/kernel timing and real provider behavior need staging checks. |
| Bulk enrollment | Dedicated database/Redis alias; 600-second limit; each committed row and resume position saved atomically; bounded 100-row/40-second slices; CSV hash guard; current admin check; duplicate/cancelled completion guards | Original CSV must remain in private persistent storage; uncertain welcome-email acknowledgement still cannot promise exactly-once SMTP delivery. |
| Newsletters | Unique campaign dispatch, bounded recipient queries, one SMTP recipient per job, locked delivery claims, one counter increment, current unsubscribe/cancellation checks | Lost SMTP acknowledgement is `delivery_unknown`; operator reconciliation precedes another send. Stale `processing` is reported by preflight. High-volume mail should use an appropriate dedicated service. |
| Portal notices | Existing source/owner/opt-in recheck at delivery, receipt state, retries and quiet-hour rescheduling | Provider transport acceptance is not proof of inbox delivery. |
| Interactive AI | Existing mentor/career request budgets/usage records; résumé requests now use configured model, private per-user cache and shared usage records | Bounded HTTP/connect timeouts; production example uses one attempt and 15-second timeout. Per-user quotas are not an account-wide monetary cap: set provider spending limits and monitor totals. |
| Payments | Necessary synchronous verification with 5-second connection / 15-second request bounds and safe failure logs | Never retry transfers/refunds blindly. Reconcile ambiguous provider state. |
| PDF/QR/export, image uploads, offline pack building | Existing request workflows retained | Large reports, course graphs, external Cloudinary uploads and PDF rendering need representative memory/load checks. Large video must use external media hosting; a remote URL alone does not prove offline availability. |

Public catalog statistics already use an appropriate cache; Adzuna caches provider results without user saved/open flags. Assessment/course/application lists use existing pagination and the AI ledger has `(user_id, feature, created_at)` indexes. The new bulk cursor uses existing JSON settings, requiring no migration. Database Scout avoids loading a whole catalog into PHP on the suggested production profile; validate its SQL results in staging. No speculative indexes or blanket authenticated-response cache were added.

## Verification and remaining evidence

See [verification](../verification.md) for current test/build/browser counts and limits. Current recovery evidence is private under `/home/olamilekan/.local/share/bootkode/recovery-20261102/`: all 138 source tables / 605 rows matched an independent MariaDB restore, 237 foreign keys passed, and 133 business-table hashes/old fields survived the two rehearsed additive migrations. Working service is `bootkode-database.service` on loopback 3308. Original corrupted directories were retained. The old XAMPP MySQL at 3306 is still corrupt; do not repoint BootKode to it or delete its system tablespace.

Next gates: host capability confirmation, encrypted off-host recovery rehearsal, sandbox/live-provider acceptance, representative load, physical-device PWA acceptance and remote CI. The supported dependency set, complete synthetic learner journey and responsive/browser gate are verified locally. GitHub push/deployment requires actual credentials and green release gates; no deployment success is claimed.
