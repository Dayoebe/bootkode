# Premium app experience, performance and reliability — completion

3 November 2026. This completes the repository-controlled continuation of [milestone 1](app-experience-milestone.md) and [milestone 2](app-experience-milestone-2.md). It does not claim a production deployment, live-provider success, physical-device certification or field performance.

## Supported stack and preserved boundaries

The current lock uses PHP 8.4.27, Laravel 12.70.2, Livewire 3.8.11, Socialite 5.31.0, `firebase/php-jwt` 7.2.1, Tailwind 3.4.19 and Vite 7.3.7. The reviewed supported-major migration clears the prior Laravel/JWT advisories without ignores or security-policy changes. Livewire remains the only Alpine owner; existing named routes, policies, role/ownership checks, `Course::publiclyAvailable()`, `WalletService::purchaseCourse()`, enrollment history and certificate codes remain authoritative.

No migration or working learner record is created, rewritten or deleted. Automated and browser fixtures use databases ending `_test`/`_testing`.

## Completed application work

- The document head applies saved theme and desktop-sidebar state before first paint. Conditional sidebar/editor/status content is cloaked until Alpine hydrates. The responsive browser gate enforces CLS <= 0.1 and records layout-shift sources.
- MathJax is removed from the global authenticated shell and requested by the CBT surface that needs it. Authenticated marketing tags render only when the account has saved both collection and marketing consent; the default is no marketing tag.
- `AssessmentResultService` loads questions once and uses one owned-answer query plus one completed-session query for an entire assessment collection. A small and a dense 8-section/40-lesson/12-assessment fixture keep query growth bounded.
- Lesson notes and bookmarks are separate account-owned saved resources. Notes are bounded to 5,000 characters; removing a bookmark cannot delete a note. Authorization and persistence stay server-side.
- The browser journey proves a saved note/bookmark, assessment answer reload/resume, passing submission, next-lesson progression, final completion and visible certificate. Course completion locks the enrollment and rechecks certificate existence inside one transaction. Certificate/enrollment state is committed together; asset generation and notices are isolated after persistence. Replays reuse the existing credential and soft-deleted numbers remain reserved.
- One shared PWA controller owns registration, install prompting and deliberate update activation. The versioned service worker retains explicit public/static and downloaded private-pack boundaries. It does not implicitly cache authenticated course HTML. Offline manifest/sync endpoints are throttled.
- Existing skip links, focus rings, headings, labels, live regions and touch controls are retained. Ocean/forest accents were adjusted through semantic theme variables, not one-off overrides.

## Controlled evidence

The complete synthetic learner journey passes at 390px with no recorded browser errors. Its artifact records two lessons, assessment resume, independent note/bookmark persistence and certificate visibility at `/home/olamilekan/.local/share/bootkode/verification-20261103-completion/`.

The final authenticated Chrome matrix passes 32 states at 360, 390, 430, 768, 1024, 1440 and 1920px: dashboard, history restoration and online course at every width; offline reader/completion/reconnect; lost-acknowledgement recovery; account switching/logout cleanup; storage denial; lesson and blog editors; queued media checks; and the offline library. No uncaught JavaScript error or local HTTP 5xx is recorded. Maximum observed CLS is 0.0544 and maximum diagnostic LCP is 1,152 ms on loopback. These are controlled local observations, not field Core Web Vitals or a capacity result.

At 390px and 1440px the harness finds no duplicate IDs, unnamed buttons, unlabeled fields or tested touch targets below 44px. Skip-link focus is visible, reduced-motion transition duration is zero, and sampled primary/secondary/accent/action text in light, dark, sepia, ocean and forest themes is at least 4.5:1. Evidence is retained at `/home/olamilekan/.local/share/bootkode/verification-20261103-completion-final-responsive/`.

Focused tests cover dependency-compatible certificate behavior, atomic completion/replay, private notes/bookmarks, PWA manifest/cache policy, consent-aware analytics and bounded course queries. The final full-suite/build/cache/audit counts are recorded in [verification](verification.md).

## Release boundaries

Physical Android/iOS install, update, storage eviction and assistive-technology sessions remain external acceptance. So do representative production data/load, field LCP/INP/CLS, target-host cron/pcntl/TLS/proxy/storage configuration, encrypted off-host database/files/APP_KEY restoration, and real SMTP, Paystack, OAuth, AI, vacancy and external-media accounts. Remote CI and ordinary push require repository credentials. None of those is inferred from loopback fixtures, and the application is not labelled production-ready until the operational gates pass.
