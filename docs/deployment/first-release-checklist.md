# Staged deployment runbook

Reference `.env.production.example`, `deploy/php.ini.example`, `deploy/nginx.conf.example`, `deploy/supervisor.conf.example`, and [readiness gates](production-readiness.md). These are reviewable examples, not instructions to deploy this currently blocked branch.

## 1. Select a compatible host and release

- Resolve the supported-framework/security dependency gates first. Require green application **and** dependency CI jobs for the exact reviewed Git SHA. A build artifact from a failed security workflow is not a deployable release.
- Confirm PHP 8.4/extensions/CLI pcntl, MySQL or supported MariaDB, persistent private uploads, public-only document root, minute cron, a compatible dump client, outbound HTTPS/SMTP, TLS, storage links and >=660-second CLI execution. Confirm actual provider limits; do not infer persistent workers from the words “Laravel hosting”.
- Prepare a restricted staging environment with separate database, APP_KEY and provider sandbox keys. Never copy production credentials into GitHub Actions or tests. All destructive test databases must end `_test` or `_testing`.
- Use SSH/deploy keys with least repository access or the host's GitHub integration. Pin host keys. Pull the exact reviewed commit using an ordinary fast-forward/checkout release process; never force-push, reset shared work, or deploy an unreviewed moving branch. This local work does not claim a successful GitHub push.

## 2. Build outside the 2 GiB application server

```sh
composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader
composer check-platform-reqs --no-dev
npm ci --ignore-scripts
npm run build
```

Build with development NPM dependencies installed; Vite/Tailwind are build tools. The host needs `vendor`, application sources and `public/build` plus its manifest; it does not need Node, `node_modules`, a Vite server or test fixtures. Retain the existing complete public assets and application views/routes/configuration. Do not upload `.git`, `.env*` secrets, `storage/app` from a developer checkout, private SQL/recovery/test/browser evidence, test accounts or `public/hot`. Save a checksum/release SHA for the artifact. Run full tests on CI before producing a `--no-dev` runtime artifact, rather than removing development dependencies from a working test checkout.

## 3. Provision private configuration and persistent paths

Set APP_ENV=production, APP_DEBUG=false, the real HTTPS APP_URL, a newly generated **first-install-only** APP_KEY, database-scoped credentials, secure encrypted database sessions, rotating warning logs and required provider credentials through the hosting panel. Keep that APP_KEY for all later releases and restores. Do not regenerate existing keys. Do not expose secrets through VITE variables or Livewire properties.

Serve `/srv/bootkode/current/public` only. Configure the actual TLS certificate and FPM socket; the Nginx example is incomplete without them. Apache must similarly use `public` and allow its existing rewrite rules. Validate `.env`, composer.lock, backups and arbitrary PHP upload paths return 403/404 without printing their body. Configure trusted proxy IPs only when the provider supplies them; do not trust every forwarded header by default.

Link shared `storage` into each release. Storage and `bootstrap/cache` need application-user ownership/group access (directories typically 2770/2775, files 660/664 as required), never recursive 777. Keep `.env`, client credentials and backups 600; private backup directories 700. Use targeted ACLs when deploy and web users differ. Public storage links expose only `storage/app/public`; never link all `storage/app`. Preserve all private submitted work, résumés and issued certificate files. Do not run recursive deletes or copy local private evidence to hosting.

Configure SMTP and validate reset/verification mail; do not use the log mailer in production. Keep large video in the existing external media provider, with working authorisation and URLs; do not copy video archives into the application release. Private local file routes remain in place. S3 requires its compatible adapter and private-download validation before switching disks.

## 4. Back up and rehearse migrations

Use [backup and restoration](backup-and-restoration.md). Restore the real snapshot into a separate isolated `_test` schema with application writers/mail/workers disabled. Verify counts, hashes, financial totals, enrollment/course relationships, historical certificate statuses/tokens/files and required foreign keys before and after `php artisan migrate --no-interaction` on that copy. Inspect every pending migration and large-table locking/backfill cost. MySQL DDL may commit independently; a migration is not guaranteed transactional.

Before the live maintenance window, make a fresh validated database + upload backup and preserve the previous release/key. Stop cron/worker admission and let active jobs drain: `artisan down` alone does not stop queue workers. With writers stopped, record the maintenance backup and only then run reviewed forward migrations on the intended database:

```sh
php artisan down --retry=60
php artisan migrate:status
php artisan migrate --force --no-interaction
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan bootkode:production-check
```

No fresh/reset commands, blanket seeding or unattended rollback. The career migration deliberately refuses rollback when reviewed requirements or evidence exist. Preserve old records; use a reviewed forward compatibility migration. `config:cache` includes secrets: protect bootstrap/cache and rebuild it on the target host after provisioning environment. Do not upload a developer's cached config. Do not use `cache:clear` during active locked/unique work: it can remove worker coordination. Broader `optimize:clear` is for a controlled window after runtime tables exist.

## 5. Configure the scheduler and queue runner

Scheduler cron (actual provider PHP path and application path):

```cron
* * * * * cd /srv/bootkode/current && /usr/bin/php artisan schedule:run >> /srv/bootkode/shared/storage/logs/scheduler.log 2>&1
```

Rotate that log through the host. `schedule:list` currently contains the newsletter dispatcher; job alerts and learning reminders are opt-in configuration. Test reminders with their `--dry-run` commands before enabling them. Reset quests, review analytics and withdrawal commands are **not** automatically scheduled by this work. Do not add automatic payouts without their own approval/reconciliation procedure.

### No persistent worker: bounded cron bursts

Set QUEUE_CONNECTION=database, COURSE_QUEUE_DRIVER=database, BULK_QUEUE_DRIVER=database, DB_QUEUE_RETRY_AFTER=120, PRODUCTION_QUEUE_MODE=cron, PRODUCTION_CRON_MAX_RUNTIME to the **verified** allowed duration (>=660). The dedicated heavy aliases reserve for 960 seconds; their jobs time out at 600. The wrapper uses one shared flock across all queue kinds, prevents overlap, stops when empty and uses a 192 MiB worker threshold / 256 MiB PHP cap.

```cron
*/3 * * * * /srv/bootkode/current/scripts/queue-burst.sh default >> /srv/bootkode/shared/storage/logs/queue-cron.log 2>&1
1-59/3 * * * * /srv/bootkode/current/scripts/queue-burst.sh course-content >> /srv/bootkode/shared/storage/logs/queue-cron.log 2>&1
2-59/3 * * * * /srv/bootkode/current/scripts/queue-burst.sh bulk-enrollment >> /srv/bootkode/shared/storage/logs/queue-cron.log 2>&1
```

A burst's `--max-time=50` is checked **between** jobs, not a hard process deadline. A current job may still run for 600 seconds; allow at least 660 seconds. Long course work can delay mail on this low-cost profile; monitor queue age and arrange prioritised capacity if necessary. Host cron syntax and flock availability must be tested. Do not run `schedule:work` or a hidden forever-loop as a shared-hosting workaround.

### Host explicitly supports supervised workers

Use the optional Supervisor example instead of cron queue bursts. It runs one worker over all queues through the long-window `course-content` alias. All three drivers/backing connections must match in that mode. Never pop 600-second jobs through the default 90/120-second reservation. Set PRODUCTION_QUEUE_MODE=supervisor. Restart workers on release with `php artisan queue:restart`, confirm they drain and restart against the new release, and retain shared cache/storage for the restart signal.

Optional Redis: set QUEUE_CONNECTION=redis, COURSE_QUEUE_DRIVER=redis and BULK_QUEUE_DRIVER=redis together with valid REDIS configuration/extension. Heavy aliases still retain 960-second reservations. Stop/drain the old backend before switching; pending jobs do not migrate themselves. Do not run consumers against inconsistent backends and assume all queues are drained.

## 6. Staging and controlled launch acceptance

- Native login/logout, inactive/unverified restrictions, admin denial, owner/private-file download, upload MIME/size/path checks, session expiration and error recovery.
- Paystack sandbox: mismatched reference/amount/currency/status, invalid HMAC, replayed callback/webhook and rollback; inspect ledger/receipt atomically. No live transaction or payment success is implied by HTTP stubs.
- Real transactional email, notification opt-ins/unsubscribe/quiet hours, newsletter acknowledgement review and actual provider delivery reporting.
- Explicit AI setup/probe, quota/failure/retry/resume without duplicate course records, account spending limits, human review before publication; Adzuna quota/licence/attribution and external redirect behavior.
- Browser checks at 390/768/1024/1440 and a larger desktop/phone size for the app-experience milestone; keyboard/Escape/focus, history, editor loading, slow/offline learning and reconnection. Build success does not prove these.
- Representative request/query/memory profiles and a load rehearsal. LCP <=2.5s, INP <=200ms and CLS <=0.1 remain measured acceptance targets, not current claims.

Only after all gates pass: switch the approved release, reload FPM/OPcache if required, `php artisan up`, resume the chosen runner, and verify public/authenticated health and a real end-to-end lesson journey. Observe errors, queue lag, API cost and backup age before increasing traffic. None of these production actions has been executed here.

## 7. Failure and restore procedure

For a code-only regression with compatible schema, stop admission, drain workers and switch to the preserved code release. Do not roll back a data-bearing migration blindly. For data loss, stop all writers and restore the complete consistent database/files/key set into a **new** schema/location; verify it before changing the application connection. Define the recovery point and account for transactions accepted after the snapshot. Restoring a stale payment database while gateways keep accepting events can corrupt financial history; reconcile gateway events first. Never delete the original corrupt data or clear failed jobs to make the status green.
