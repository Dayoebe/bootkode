# App experience, performance and reliability — milestone 2

3 November 2026. This continues [milestone 1](app-experience-milestone.md) in the existing Laravel/Livewire application. It is a coherent implementation and verification milestone, not acceptance of the complete premium-app brief or production release approval.

## Current audit and preserved architecture

The current checkout uses Laravel 11.46.1, Livewire 3.8.11, Tailwind 3.4.19 and Vite 7.3.7 on PHP 8.4.27. Livewire remains the Alpine owner. The shared dashboard layout, role-filtered navigation, named routes, semantic tokens, public/private boundaries and existing course, assessment, wallet, certificate, career, mentorship, notification, gamification and offline systems remain authoritative. No framework, component system or application dependency was added.

The audit covered Git state and recent delivery, package manifests and audits, application/database runtime, 293 routes, the shared layouts and Vite entries, the learner dashboard component/view, chart-producing dashboard and feature views, course/section/lesson/enrollment relationships, learning sessions, relevant tests, PWA/offline milestone evidence and the required architecture/design/product/roadmap/verification documents. The working schema remains `bootkode_restored_20261028` on the user service at loopback port 3308; 139 tables and all 46 migrations were present. No working record was created, changed, migrated or removed.

Baseline evidence before edits:

- `php artisan test`: 509 passed / 2,455 assertions in 160.41 seconds.
- `npm run build`: passed; dashboard JavaScript was 213,564 raw bytes / 72,294 bytes under the same gzip-9 measurement.
- `npm audit` and `npm audit --omit=dev`: zero advisories.
- Composer metadata/platform checks: passed. `composer audit --locked` still reports six advisories across Laravel and `firebase/php-jwt`; release remains blocked.
- All 46 migrations were recorded and `queue:failed` reported no failed jobs.

The restored working data currently contains no learner enrollments or lesson-completion pivots. It is therefore not a representative learner-load dataset. Query scaling is verified with explicit synthetic fixtures only in `bootkode_deployment_test`; no learner or result was invented in the working database.

## Findings and implementation

### Route-specific chart delivery

Chart.js was imported by `resources/js/dashboard.js`, so every authenticated route downloaded and parsed it even when the page contained no chart. The chart runtime, empty-data helper and two Alpine chart factories now live in `resources/js/charts.js`. Nineteen existing chart-rendering Blade views request that entry through Livewire `@assets`; the shared dashboard layout no longer embeds chart helpers. The normal dashboard bundle retains workspace/editor coordination only. Livewire navigation still owns history and asset de-duplication.

The empty-chart helper now creates text nodes instead of assigning interpolated HTML. It continues to show an explicit unavailable/no-data state and does not turn a missing chart library into a successful chart claim.

Measured production build output:

| Asset | Previous | Current |
| --- | ---: | ---: |
| Always-loaded authenticated dashboard JS | 213,564 B | 5,312 B |
| Same files, gzip-9 | 72,294 B | 1,748 B |
| Route-specific chart JS | Included above | 210,009 B / 71,384 B gzip-9 |

The always-loaded entry is 208,252 bytes (97.51%) smaller. Chart pages still load the chart chunk and should be treated as approximately the same JavaScript class as before; this is not a claim that their LCP/INP improved by 97.51% or that total page traffic fell by that amount. Non-chart authenticated routes receive the material saving.

### Bounded learner progress projection

The learner dashboard previously repeated completed-lesson, course-lesson, section and completion-count queries inside the visible enrollment loop, then repeated next-lesson work for the tasks widget. It now loads one completed-lesson set plus the six visible enrollments, courses/instructors, ordered sections and published lessons as a fixed six-query graph. Totals, completed counts and the next published lesson are projected in memory. Upcoming lesson tasks reuse the same computed projection.

The regression fixture contains six courses, four published lessons per course and one actual completion per course. It asserts exactly six queries, six cards, the correct 1/4 counts and the second lesson as the next step. This is bounded-query evidence, not a production throughput or large-dataset latency benchmark.

Dashboard quick enrollment counts use one conditional aggregate instead of two queries. Unused gamification values were removed from that projection, and a GET no longer calls `getOrCreateGamificationData()` or creates daily-quest state. Existing gamification services and write boundaries remain unchanged.

`LearningSession::getStudyStreak()` now loads at most 365 distinct completed activity dates in one query and computes the consecutive current streak in memory. It no longer issues one query for every consecutive day. Existing streak semantics remain: a streak starts only with completed activity today and stops at the first missing date.

### Secondary-widget failure isolation

Learning progress and factual quick statistics remain the primary dashboard path. Analytics, tasks, achievements, announcements, career, mentorship, reviews, wishlist and system-status loaders now report exceptions through Laravel and return safe typed fallbacks. The view replaces only the failed panel with a shared, accessible “temporarily unavailable” status and a retry action. It does not report success, discard learning data or hide the failure in an empty catch.

A database-listener fault injection against the system-status query verifies that the learner heading and rest of the workspace remain rendered, the failure state is visible, and the copy states that no learning data changed.

## Verification and limits

Final verification passes:

- `StudentDashboardPerformanceTest`: 4 tests / 16 assertions covering fixed query count, chart-route asset presence/absence, no GET-time gamification write, secondary-widget isolation and one-query streak calculation.
- Existing dashboard résumé regression: 1 test / 3 assertions.
- Complete `php artisan test`: 513 tests / 2,471 assertions in 119.78 seconds against the explicit `bootkode_deployment_test` schema.
- Authenticated Chrome: 32 responsive states at 360/390/430/768/1024/1440/1920px. The required 390/768/1024/1440 dashboard, history-restoration and course states had one visible primary heading and no horizontal overflow. The run also passed real CSRF login, Livewire history, service-worker offline completion/reconnect, lost-acknowledgement retry, account isolation/logout cleanup, storage-denial recovery, both editors, queued media QA and the offline library. No uncaught JavaScript error or local HTTP 5xx was recorded. Representative 390px and 1440px dashboard captures were inspected.
- The local synthetic dashboard responses used 46 queries (one other-account state used 40) and 91.82–117.55 ms across the seven primary widths. These are loopback observations with one synthetic enrollment, not a production capacity claim; the targeted six-course projection test is the scaling guard.
- Production Vite build, Blade compilation, route cache build/clear, changed-PHP syntax, focused Pint and `git diff --check` pass. The existing PostCSS missing-`from` warning remains.
- Composer metadata/platform checks pass. Both npm audits remain at zero advisories. Composer still reports six advisories across two packages, including critical Laravel and low-severity JWT findings; the current supported major cannot clear them without a separately reviewed framework/JWT migration, so release remains blocked.

The browser harness records local LCP/CLS diagnostics but does not provide field Core Web Vitals or INP. Some 1024px and wider synthetic states exceeded CLS 0.1, including 1024px dashboard/course and 1440px/1920px course states. Those readings keep layout-stability acceptance open; they are not hidden by the otherwise passing functional run. External analytics/fonts/MathJax were excluded from the loopback harness.

The accidental multi-file focused invocation raced `RefreshDatabase` processes against the same dedicated test schema; it produced table-exists/table-missing setup errors and no application assertions. The lingering runner was stopped, only `bootkode_deployment_test` was rebuilt, and subsequent single-process tests and the complete suite passed. The working schema was not targeted.

Open acceptance remains substantial: representative production-data timing, physical Android/iOS installation and lifecycle, throttled enrollment-to-certificate journeys, upload/provider/payment acceptance, all-theme keyboard/screen-reader/contrast coverage, field LCP/INP/CLS and the supported dependency migration. Chart splitting and query bounds do not clear those gates.

## Next milestone

Exercise the complete discover/enroll/continue/assessment/notes/bookmarks/certificate journey in a dedicated browser database under throttled, offline and reconnect conditions. Profile the course workspace’s large assessment graph and split optional MathJax/analytics assets only where evidence supports it. Keep payment/provider and real-device acceptance separate, and resolve the supported Laravel/JWT dependency set before any release claim.

Local delivery commits are `28c2b7fe` (implementation, tests and built assets) and `9fab76a6` (documentation and evidence) on `feat/premium-app-experience-milestone-2`. The ordinary GitHub push failed because HTTPS credentials are unavailable (`could not read Username`); no remote delivery or deployment is claimed.
