# App experience, performance and reliability — milestone 1

2 November 2026. Implemented in the existing application, following the production-readiness work. This is a coherent verified milestone, not acceptance of the entire nine-phase transformation or every existing interface.

## Audit and preserved architecture

At delivery, the installed stack was Laravel 11.46.1, Livewire 3.6.4, Tailwind 3.4.19 and Vite 7.3.7, with PHP `^8.2` and tested PHP 8.4. Livewire owns Alpine. The current installed Livewire version is recorded in [milestone 2](app-experience-milestone-2.md). Existing shared tokens, Sora/Manrope declarations, role-aware sidebar/mobile destinations, 1024px rail, theme support, focus-trapped menus/dialogs and `wire:navigate` are retained. Public identity, real course content, historical enrollments/certificates, assessments, financial workflows, career services and gamification were not replaced. The preceding production milestone passed 500 tests / 2,407 assertions; the expanded suite and current evidence are in verification.md.

Reviewed sources include application/guest/dashboard layouts, role navigation, Vite entries, public JS, both Trix authoring views, online course access/completion, offline manifest/sync service/controller, PWA manifest/worker/reader, relevant models/migrations, auth/logout, existing tests, current Git state and the existing architecture/product/design audits. This was a targeted source and browser audit; it is not a claim to have manually read every legacy file or accepted every route/theme.

Findings addressed:

- All authenticated pages loaded the 419,482-byte dashboard JS including Trix and its 17.91 kB CSS, even when no editor existed.
- The offline library loaded all owned pack manifests and full course lesson graphs into PHP at once.
- Offline structured course/queue data used unscoped localStorage; synchronization retained a stale CSRF token and cleared the entire queue after one response, including events added during the request.
- The offline sync path bypassed lesson assessment/section access checks and overwrote completion timestamps/time counters on replay.
- The service worker was conservative about implicit navigation caching, but explicit pack installation could still fetch/cache authenticated course HTML; downloaded document URLs were not reliably read from the explicit pack cache offline.
- The manifest locked orientation and its course shortcut used an older destination.
- Visual inspection found the outline's lesson-title input overlapping the editor at desktop width, and a login welcome message duplicated inside the lesson editor.

Production risks remain in [the readiness assessment](deployment/production-readiness.md): unsupported framework and six Composer advisories, host capability/queue/runtime confirmation, private off-host recovery and real provider acceptance. Do not confuse this milestone with deployment approval.

## Implemented behavior

### Assets, navigation and layout

Trix and its CSS now live in `resources/js/editors.js`. Both existing authoring components request that entry through Livewire `@assets`, including when opened through navigation. The dashboard retains charts/workspace behavior; no second Alpine runtime or new application dependency was introduced. Authenticated browser tests verify both editors load, and learner dashboards do not fetch the editor bundle.

The sidebar/bottom navigation and browser history remain native to Livewire. A shared offline announcement links directly to downloaded materials without promising that server actions work offline. The standalone reader offers a course chooser, collapsible touch-sized lesson navigation on phones, focusable lesson headings, a skip link, visible focus, readable headings and safe-area spacing. The outline's form controls are bounded and labelled, its active lesson uses semantic action colours, and its save feedback has its own scope so the shell's login welcome is not repeated.

The offline library returns ten owned summaries per page, extracting only the manifest lesson count and a course/count summary. Lesson bodies are not hydrated for the list. Its update listener is removed when Alpine destroys the component. No speculative database index or broad authenticated-response cache was added.

### Explicit offline storage and sync

The existing reader and pack UI remain. Full pack content and event queues now use IndexedDB (`bootkode-offline-learning`); small per-account indexes/metadata stay in localStorage for immediate library state. Packs carry the server's user ID. Previously unscoped browser packs cannot safely be assigned to the current account and require a new explicit download. Original server records are retained.

A fresh authenticated session endpoint returns the current owner and CSRF token with `private, no-store`. Tokens are not saved in IndexedDB. The sync endpoint requires the pack owner to match the authenticated account. A sync request sends at most 50 pending events from a pack. A single in-page promise and browser lock coordinate sync; IndexedDB queue edits are atomic. The server acknowledges event IDs, and the client removes only acknowledged IDs from its submitted snapshot. Events added while a reply is in flight remain pending. Lost replies, 401/419, offline requests and denied storage report recoverable states without claiming success or clearing pending progress.

Server acceptance rechecks enrollment, active/verified account, published lesson membership, the existing section-access service and actual passing reviewed assessment results. A learner cannot complete an assessment by claiming an offline lesson event. Replays preserve the original lesson/course completion dates, retain the largest recorded cumulative time, and reuse the existing gamification replay guard. Server completion time is authoritative; a device timestamp cannot invent historical/future achievement evidence. Existing historical completion dates remain retained if course content later expands. Unconfirmed/blocked events remain pending for online resolution.

The service worker never implicitly caches authenticated HTML. The manifest no longer lists course HTML for download; pack installation permits only same-origin static/public resource paths and rejects HTML outside the two fixed offline shells. Explicit same-origin public files can be served from the pack cache offline. External, failed or disallowed resources produce a partial-pack state, and linked video still needs internet unless genuinely stored and tested. Hashed build assets use cache-first reads; background static refreshes retain the worker lifetime. Cache versions change when offline behavior changes. The PWA supports adaptive orientation and its course shortcut uses `/courses`.

Confirmed logout requests browser cache/storage clearing and sends a server-confirmed sign-out marker. Public JS provides targeted IndexedDB/pack-cache/local metadata cleanup when that header is unavailable; other open readers close their database connection after the storage event. A session timeout does **not** trigger cleanup: pending work survives sign-in. Cleanup failures have visible alerts. Downloads remain unencrypted browser-local data intended for the learner's own device; device storage can be blocked or evicted. The UI explains that sign-out removes device downloads, while confirmed server progress remains.

## Measured results and evidence limits

| Measurement | Baseline | Current |
| --- | --- | --- |
| Dashboard JS raw bytes | 419,482 | 213,564 (**49.09% less**) |
| Same files compressed by the same gzip procedure | 124,513 | 72,450 |
| Trix JS on a non-authoring learner dashboard | Included in the dashboard entry | Not requested in the browser |
| Editor JS/CSS | Loaded on every authenticated route | 205.36 kB JS / 17.91 kB CSS requested by the two authoring views |
| Offline library list | All packs and full lesson graphs | Ten owned summary records per page, without full manifests |

The byte comparison uses the tracked production bundle at commit `8556793b` and the current build; it is not a claim that all network traffic fell 49%. No LCP/INP/CLS field target has been certified. Browser evidence records local synthetic loopback LCP/CLS samples and request query/time headers; external analytics/fonts/MathJax were excluded, fixture datasets were small and host memory pressure varied. These observations are unsuitable for a production capacity or Core Web Vitals claim. No representative field INP, throttled-network distribution, large-dataset benchmark or physical-device result exists yet.

Authenticated browser evidence is private at `/home/olamilekan/.local/share/bootkode/verification-20261102/evidence/`. Checks cover 360, 390, 430, 768, 1024, 1440 and 1920 px; dashboard/history, online course, service-worker offline reader, lost/in-flight acknowledgements, session expiry, account switching, both editors, queued QA and the paginated library. The logout test removes Clear-Site-Data from a real response to verify the JS fallback independently. Storage-denial injection checks that an optional offline failure does not crash the online course. No live AI, payment, vacancy API or SMTP call is used in these browser fixtures. See verification.md for final counts and any unresolved limits.

## Phase status and next milestone

- Phase 1: targeted architecture/baseline audit and concrete risk inventory recorded; broad route/content/device inventory still applies from existing reports.
- Phase 2: existing responsive design system and shared shell retained; no replacement component system.
- Phases 3–5: targeted outline/reader interaction, editor asset loading and offline-list query work delivered. Remaining account/admin interiors and larger query graphs are not all accepted.
- Phase 6: explicit account-bound IndexedDB packs, safe caches and acknowledged progress delivered for the checked browser. Real install prompts, media variants, eviction, Safari/Android lifecycle and cross-device constraints need further acceptance.
- Phases 7–9: targeted access/failure/accessibility tests and code/visual review completed for this milestone; broad accessibility, all themes, slow-network and production field testing remain open.

Next implementation milestone: profile the larger learner dashboard/course query graphs with realistic restored data, finish safe route-specific chart loading and secondary-widget isolation, then run complete enrollment → lesson/quiz → notes/bookmarks → certificate journeys under throttled/offline conditions. Address the supported dependency set as a separate release-critical change. Do not mark the entire premium transformation complete from these targeted results.
