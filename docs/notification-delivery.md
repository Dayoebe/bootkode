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

The learning-event implementation below extends this delivery boundary. Generic legacy category/language/blocked-sender settings are not newly certified by these event-specific opt-ins and quiet-hour checks. Historical file/account lifecycle decisions, dependency findings and other release gates remain in the consolidated roadmap.

## Learning events — 27 October 2026

AssessmentReviewService now records a learner notice when every written answer is finalized, an actual course-owner notice for a reconsideration request, and a learner notice for an upheld decision or explicit correction. Official marks, review history and the notice commit together. Generic notices exclude original answers, marks, private guidance, feedback and reasons; those remain in the existing authorized review/results screens. Exact retries do not announce the event again. Current active/verified reviewer roles are rechecked, and queued requests stop emailing an instructor after ownership changes or a request closes.

LearningNotificationService extends the native notification boundary with learning_notification_receipts. Its additive migration is applied locally after dedicated-database verification and a private backup. A user/event uniqueness constraint and locked source transactions preserve delivery identity even when an opened/deleted notice disappears. The down migration refuses to erase receipts automatically. Migration must precede use of the new event hooks.

CBT reminders target the active Assessment/recorded-attempt workflow, not the legacy CbtExam model. Only published/approved, enrolled course quizzes with supported single-answer automatic questions, nonempty choices, a configured pass mark, an upcoming recorded deadline, attempts remaining and no existing attempt qualify. Global quizzes without an assigned audience and lesson-linked assessments are excluded. A valid historical course_user enrollment is retained. The existing reminder_frequency choice selects one hour, six hours, one day or three days; never disables reminders. One notice is recorded per learner/assessment/deadline. These are reminders about stored dates; this phase does not change attempt deadline enforcement or access contracts.

Course review reminders require an actual completed CourseEnrollment with a completion timestamp and no existing review. Standard allows three portal reminders (initially seven days after completion, then 14 and 30 days after the preceding notice); frequent uses 3/7/14 days, minimal permits one after 14 days. The count/last_reminded_at fields now track actual portal notices, not mailbox delivery. A newer notice supersedes an older pending email, and current minimal preference cancels excess queued emails. Existing reviews, individual unsubscribe and account-wide opt-out suppress delivery; account-wide opt-out also covers future courses. Statistics use independent queries, and next-due reads do not mutate timestamps.

A signed unsubscribe GET displays confirmation and makes no write. POST retains normal CSRF protection. Links expire after 30 days and include a separate record/expiry-bound HMAC, in addition to Laravel's signature. This protects this specific operation while the framework signed-URL advisory remains unresolved. The existing GET names/URLs remain; two POST confirmation routes are additive. Responses prohibit caching and indexing. Signing a link is not proof of email delivery.

Existing UserAchievement and UserBadge records supply private achievement notices and owned rewards history. Existing milestone definitions are retained. Generation uses recorded completion timestamps, distinct completed lessons, ended recorded study sessions and actual finalized assessment percentages. Bare progress percentages and client scores are not completion evidence. Account locking prevents duplicate awards; source/notice failures roll back together. Awards add no extra coins, points, gems or credential. Existing lesson/quiz reward boundaries and historical marks remain authoritative. Historical awards are not automatically broadcast by this migration; the retained broadcastAchievement service accepts only a persisted award owned by the actual recipient.

Notification Preferences exposes assessment emails, real CBT deadline email choices, recorded-achievement emails and course-review email/frequency settings. These event families require explicit saved email opt-ins; absent settings default off. Current account/source/ownership/preferences and quiet hours are checked by the shared queued email job. CBT question-format eligibility is rechecked too. Notification destinations must match the configured application host; a supplied request Host header cannot authorize an external destination. Local/testing environments permit loopback aliases only when the configured host is itself loopback. Compatibility notification classes now load correctly and refuse foreign/ineligible direct email projections. No public achievement broadcast, push or SMS delivery is introduced.

Additional read-only commands:

```sh
php artisan bootkode:cbt-reminders --dry-run
php artisan reviews:send-reminders --dry-run
```

Without --dry-run these commands create eligible private portal notices and may queue opted-in email. LEARNING_REMINDERS_SCHEDULE_ENABLED defaults false; after deployment verification it adds CBT checks every 15 minutes and course-review checks daily at 11:00 Africa/Lagos under the current UTC application configuration (10:00 application time). The default email queue needs its own asynchronous worker; the dedicated course-content worker is unchanged. Existing newsletter scheduling remains intact. No working delivery command or external message was used during acceptance.

Other legacy notification families retain their own behavior. In particular, the existing new-course-review instructor notice is distinct from the new PortalEventNotification states; its isolated browser test used the array mailer. This phase is not blanket certification of every old notification class or provider.
