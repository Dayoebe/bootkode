# Community replies and saved job alerts

This delivery extends existing community feedback, job search, native database notifications and account preferences. It does not activate external messages or certify the other notification families.

## Member and staff behavior

Super/academy administrators can save one plain-text reply to an actual community request from Community → Moderation → User Feedback. The requester reads the retained reply under My Feedback; other members cannot read it. A reply records its actual author/time and creates a private portal notice atomically. An exact retry reuses the reply; a conflicting replacement is refused. Resolving a request alone does not fabricate a response timestamp. Historical replies are retained without inventing an author.

Signed-in active, verified members can save up to ten job alerts from actual search criteria, select daily/weekly checks, pause/resume, reload filters, check now or remove their own alert. Identical criteria reuse the existing alert. Alert matching uses active, public, unexpired jobs with an unexpired application deadline. Salary filters use explicit currency and exclude hidden salaries. Zero is a valid bound. Each check records at most 50 unseen matches, a portal notice and unique per-job receipts in one transaction; additional matches remain available for later checks. No matches produce no notice or fabricated send time. Receipts survive deletion of a navbar notice. Later publication of an older draft remains discoverable.

Email is optional and defaults off for these two event families. Local forms and Notification Preferences share `email_job_alerts` and `email_community_feedback_replies`. Updates merge locked current preferences and preserve unrelated keys. Email jobs recheck the active/verified account, current opt-in, actual source ownership/availability and actual matching jobs before sending. Messages carry generic text and a portal link; private feedback content is not emailed. Quiet hours and weekend choices defer these emails using an existing saved timezone when valid, otherwise the application timezone. Portal notices remain immediate.

## Storage and deployment

`2026_10_27_100000_add_notification_delivery_records` creates the previously missing `job_alerts` table, unique `job_alert_deliveries` receipts and nullable `community_feedback.responded_by`. Existing ad-hoc job-alert tables require separate review before migration; the migration does not silently adopt or erase them. Existing feedback rows and all original columns are preserved. Automatic down migration refuses to erase delivery history; use a reviewed backed-up forward change.

Test against a dedicated `_test`/`_testing` database and back up affected working records/schema before applying this additive migration. Current local application and preservation evidence are recorded in verification.md. Do not use fresh/reset against working records.

The default `JOB_ALERTS_SCHEDULE_ENABLED=false` leaves scheduled alert delivery inactive. Manual owner checks still work. After deployment-specific review, a normal Laravel scheduler can run the guarded hourly command and an asynchronous queue worker can process optional email. This is separate from the dedicated `course-content` queue. No new provider, queue runtime or dependency is introduced.

Read-only operator checks:

```sh
php artisan bootkode:job-alerts --dry-run
php artisan bootkode:notification-email
php artisan schedule:list
```

The non-dry-run `bootkode:job-alerts` command creates real portal notices and may queue opted-in email. `bootkode:notification-email --id=<uuid> --send` explicitly requeues an existing eligible email for fresh source/preference/transport checks. Review records first; neither operation was used to send external mail during acceptance.

## Delivery semantics and limits

Native portal notification creation is transactional and deterministic per source event. Email states distinguish disabled, unavailable, pending/deferred, processing, retry, queue failure, transport acceptance, cancellation and exhausted failure. Array/log mailers never count as external delivery. Queue failures preserve the portal notice and a recoverable state. Processing leases bound concurrent jobs; retries skip recorded transport acceptance. Failed transport exceptions and queue warnings exclude credentials/provider bodies.

`transport_accepted` means the mail transport returned successfully, not mailbox delivery. SMTP cannot guarantee exactly-once delivery across a crash between provider acceptance and the final database write. Quiet-hour deferral requires a real asynchronous worker; the sync driver is unsuitable for production delivery. No live SMTP/mailbox, scheduled production worker, push or SMS acceptance is claimed. Existing test-delivery controls now explain that no test was sent instead of referencing a nonexistent notification class.

Review/reconsideration, CBT reminder and achievement notification stubs remain separate implementation work. Generic legacy category/language/blocked-sender settings are not newly certified by these event-specific opt-ins and quiet-hour checks. Historical file/account cleanup, dependency findings and other release gates remain in the consolidated roadmap.
