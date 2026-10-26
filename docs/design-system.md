# BootKode design system

Learn. Build. Grow. Education comes first; use factual course content, clear next actions and restrained editorial hierarchy.

## Identity and tokens

Keep the established navy/teal identity and existing Sora headings / Manrope body fonts (system fallbacks, Google Fonts `display=swap`). Navy `#0f172a` anchors navigation and primary emphasis; teal `#0f766e` is the light-theme action color; mint `#8fe388` is reserved for emphasis on navy. White/slate surfaces separate content. Success, warning and danger convey meaning with accompanying text/icons. The logo remains unchanged. Its older violet/blue monogram is not a usable full-interface palette; the restrained navy/teal already established in the product provides accessible action and surface pairs without recoloring the asset.

`resources/css/tokens.css` is the authoritative shared token file. `app.css` retains existing theme RGB variables for light, dark, sepia, ocean and forest compatibility. Tailwind 3 config exposes semantic RGB colors; do not migrate to Tailwind 4 syntax. Semantic action foregrounds must be paired with their backgrounds, particularly in dark mode.

Use 4px-based spacing, 44px standard control height, 8px control/panel radius, restrained panel shadow, 1240px public content width and responsive 16/24/32px gutters. Body is 16px with 1.6 line-height; compact metadata remains readable at 12–14px. Page titles use 24–36px; only public display headings reach 48–64px. Tables use tabular numerals where amounts matter.

## Components and contracts

- Existing `primary-button`, `secondary-button`, `danger-button`, `text-input`, `input-label`, `input-error`: stable Blade APIs, semantic styles, overridable attributes. Primary/danger retain default submit type; secondary defaults to button. Label inputs explicitly; connect error/help IDs using `aria-describedby` and expose `aria-invalid` for invalid fields.
- `ui.page-header`: title, optional description/eyebrow and action slot, one primary page heading.
- `ui.panel`: reusable content surface, optional heading/action slots.
- `ui.empty-state`: title, explanatory copy and optional recovery action.
- `ui.flash`: escaped session feedback with live status announcements.
- `course-card`: public published course summary using actual price/category/lesson counts, no fabricated ratings or outcomes.
- Existing modal: dialog semantics, labelled title, Escape, focus trap and focus return; do not add another modal library.

Prefer props for variants and slots for content. Keep static UI in Blade and server interactions in Livewire. Never infer authorization from disabled/hidden controls. Add `wire:loading` and `wire:target` to expensive actions; destructive actions require explicit confirmation. Named routes are mandatory for internal destinations.

## Accessibility and responsive behavior

Provide skip links, visible focus, semantic landmarks, labelled icon controls and text alongside statuses. Respect reduced motion globally; remove ambient decorative animation. Mobile drawers must close on Escape and return focus. Tables scroll within an explicitly bounded container rather than causing page overflow. Grids collapse at Tailwind's established 640/768/1024/1280 breakpoints; verify 390/768/1024/1440 widths. Do not hide overflow merely to conceal a broken layout.

Avoid gradients/glass/oversized cards as the default pattern. Do not override Tailwind color names by substring selectors. Migrate repeated patterns through shared classes/components rather than adding another layer of `!important` rules. Context-specific exam/certificate print layouts remain deliberate exceptions.

## Operational forms and previews

Career/community/template management use the shared page heading and escaped `ui.flash` feedback (including the existing `message` session key). Tabs use explicit semantic accent utilities and bounded horizontal scrolling. The shared modal accepts an optional `model` property to entangle a Livewire boolean; focus trapping, Escape and return remain shared behavior. Existing community/job dialogs use the same Livewire-owned Alpine focus behavior. Community form labels connect to inputs and error descriptions.

Certificate print styling is an intentional document-specific exception to interface tokens. Saved designs accept literal hex colors and the renderer's DejaVu font families, never custom CSS, HTML or remote font URLs. Previews use an isolated sandboxed document and visibly state that no credential has been issued.

## Institutional, marketplace and mentorship surfaces

Root marketplace/institution/mentorship pages use the shared page header, factual definition-list metrics and escaped live feedback. Nested marketplace panels own secondary headings; standalone mentor profile/code-review pages provide their own primary heading. Business and management navigation scroll within their container on small screens. Existing institution and mentorship dialogs use the Livewire-owned Alpine focus plugin, modal semantics, Escape dismissal and focus return. No additional Alpine runtime or modal dependency is introduced. Rate labels use the actual NGN wallet currency. A resource without published content displays its unavailable state instead of a placeholder link.

Legacy interior cards and forms still contain older utilities; root consistency and representative keyboard/layout acceptance do not constitute complete visual or screen-reader acceptance of every legacy interior state.

Mentoring primary controls use the shared `bk-button` / `bk-button-primary` foreground/background pair. Undefined legacy accent utilities were replaced with semantic utilities, and mentoring overview/analytics metric cards use bounded surfaces rather than bright gradients. Shopping/session feedback can pass persistent locked component messages into `ui.flash`; session-backed calls retain the existing component API.

## Support and authoring continuation

Help/support and staff response screens use the existing page header, panel, labels/errors, flash feedback and shared modal. Ticket history renders actual status and responses; no invented response-time promise appears. New attachment copy explains the real owner/support access boundary. Course sharing uses the shared modal instead of an out-of-scope Alpine variable, and its navigation is horizontally bounded. The course builder owns a primary heading, wraps tablet actions and reuses shared primary controls. Affiliate tool navigation and narrow input/action groups stay inside their containers; analytics filters wrap with explicit accessible names. These are targeted improvements, not complete visual acceptance of all legacy interiors/themes.

## Learning analytics and rewards

The rewards and analytics surfaces use shared headers, panels, form primitives, bounded navigation and factual definition-list metrics. The reward balance copy distinguishes virtual coins/gems from the NGN wallet. Analytics displays recorded data and missing-result states, with labelled filters, persistent goal feedback, accessible chart descriptions and a bounded data-table alternative. Charts use existing theme variables and disable animation. The CBT submission dialog reuses the shared modal; answer radios remain keyboard-accessible with visible peer focus rings, and hidden warning content no longer owns a primary heading. Exam layouts remain a deliberate focused-workflow exception to the standard dashboard surface.

## Course Content Studio

The authoring studio uses the dashboard layout, shared page heading/panels, labelled forms, associated inline errors, visible asynchronous status, bounded history table and the existing course editor link. Import preview explicitly shows the selected path, source revision, attribution/licence and content limitations before import. Generation/unavailable/progress states use text and do not imply review or publication. Queued work is presented as real status, not a simulated completion animation.


## Lesson assessment and review surfaces

Lesson assessment workspaces and personal results use shared page headers, panels, buttons and status regions. The embedded component owns an h2; its full workspace owns the page h1. Choice labels are touch-accessible, multiple selection has explicit exact-set guidance, written responses have labelled limits and autosave feedback, and pending/final results use text status instead of an invented percentage. Only server-accepted results publish a pass. Instructor score fields expose original bounds and associated errors; feedback is plain text. Submission, instructor review and personal results reuse the shared trapped dialog with Escape/focus-return behavior. Legacy CBT authoring interiors retain their existing styling; these changes do not certify every editor/theme.

## Structured question authoring and interaction

The lesson assessment authoring workspace reuses the existing tokens, page header, panels, controls, feedback and labelled errors. Its embedded form owns a secondary heading; the full workspace owns the page heading. Format selection is a labelled native control; complete pairs and ordered items replace decorative unsupported-format cards. Author actions wrap at small widths and do not depend on a third-party sorting library.

Matching uses labelled selection lists. Drag-and-drop adds bounded draggable choices to the same lists, with explicit keyboard alternatives. Ordering uses touch-sized Move up/Move down buttons plus explicit save. Learner, question and assessment moves preserve focus on the moved item; when a direction becomes unavailable, focus moves to its other enabled control. Existing navy/teal and Sora/Manrope conventions remain unchanged. This is targeted acceptance of these states, not certification of every legacy criteria editor/theme or assistive technology.

## Recorded mark review and correction

The review queue uses a labelled native status filter, existing page header/panel, wrapped controls and the shared trapped dialog. A correction is an explicit form with original score bounds, separate learner feedback and required reason; the publication control explains its immediate result/eligibility effect. Validation errors stay associated with the corresponding fields. Learner review requests and correction history use native disclosure elements inside the existing submitted-response surface. Plain-text reasons and decisions are escaped. A dashboard event restores focus to the updated response section after a request/decision, to the score input when opening a correction, and to its original control after cancellation/publication. No additional palette, component framework or Alpine runtime is introduced. Actual responsive/keyboard acceptance and limits belong in verification.md.

Dialog-opening controls retain focus during their loading state. The review trigger uses loading opacity rather than temporary disabling, allowing the shared focus trap to capture its original control and return there on Escape. Mutation submits still disable while saving and explicitly restore focus after their forms are replaced.


## Project and assignment file workflows

Project/assignment criteria forms use native submit forms and existing page headers, panels, labels/errors and buttons. Original file/word limits are visible to learners and reviewers. File controls explain private draft/submitted access, actual extensions, counts and the effective server size cap. Temporary upload progress has a status announcement; choosing a file and attaching it to the saved draft are separate explicit actions. Saved-file links show the original filename, readable size and unavailable state; drafts expose a touch-sized remove control. Small files show exact bytes. No palette, component system, animation or Alpine runtime was added.

Author copy/move actions retain the existing dashboard focus behavior. Completing an initial review returns focus to the queue filter when the pending row disappears. This extends the recorded-mark dialog contracts; targeted browser acceptance and its limits are recorded in verification.md.

## Authenticated workspace consolidation — 23 October 2026

The shared shell now uses a flat theme-aware surface, a 256px sidebar and an 80px persistent desktop rail from 1024px upward. Tablet/mobile use one trapped full-navigation drawer and five bottom destinations, including Menu; four destinations follow actual user roles. The page owns its primary heading. Existing navy/teal, Sora/Manrope, semantic tokens and public identity are retained.

`resources/css/dashboard.css` contains scoped layout/component rules and extends the same tokens through the `app.css` entry. `--bk-workspace-width` caps content at 1440px. `ui.table` gives tables a labelled keyboard-scrollable region; semantic column headers retain all data. Legacy forced color-remapping rules were removed after migrating their callers. Action backgrounds pair with `action.foreground`; no new UI framework or Alpine runtime is installed. Existing Font Awesome 6.5.2 is locally hosted with its licence for authenticated layouts. The unused global Flatpickr request was removed.

The feature migration standardizes panel radius/shadow, headings, filter names, icon-control names and focus/Escape behavior on existing dialogs. Admin quick actions reuse the shared modal. Public pages, printed credentials/resumes, focused CBT exams and unrouted game prototypes retain their deliberate layouts. Refer to `dashboard-redesign/page-checklist.md` and `verification.md` for state-specific acceptance and limits.


## Release continuation — 23 October 2026

Blog and résumé forms now bound narrow columns, wrap actions and share semantic Trix surfaces/toolbar rules. Interview navigation scrolls within its labelled region; analytics controls wrap. System-status panels share their Alpine scope, and mentorship dialogs reference their visible headings. Résumé export closes on Escape and returns focus. Uncollected satisfaction and unscored results use explicit text; reporting never generates random outcomes. Chart animation is disabled and trend data has a keyboard-accessible table alternative. See dashboard-redesign/continuation-report.md for checked states and limits.

## Course Content Studio acceptance — 26 October 2026

The Studio uses the actual `x-text-input`, `x-input-label`, `x-primary-button` and `x-secondary-button` primitives, plus `bk-input` for native selects/textareas and `bk-field-error` for associated errors. Workflow panels align at their tops. A component-owned locked notice announces queued actions without duplicating the dashboard's flash banner. History has a labelled keyboard-scrollable region, real content counts/status, review flags and the existing editor route. Responsive/browser evidence covers 390/768/1024/1440px; wider theme/assistive-technology acceptance remains separate.

## Server-error fallback — 26 October 2026

The standalone 500 page uses existing semantic surfaces/text, `ui.panel`, the shared button class, a skip link and one primary heading. It has no database-dependent shell, ambient animation, invented contact details or new palette. A recovery link uses the existing named home route.


## Course-content review controls — 26 October 2026

History retains the shared bounded table and existing semantic buttons. Rows show the real category, draft counts, safe validation messages and quality issues in native keyboard-operable details. Editor, quality scan and failed-stage retry remain available together; imported-draft expansion uses a clear native confirmation before queueing. These controls reuse the existing Blade/Livewire shell with no new palette, Alpine runtime or dependency. Authenticated layout and workflow checks cover 390/768/1024/1440px; live quality findings and generated-editor navigation are recorded in verification.md.


## Original course materials — 26 October 2026

The three new working drafts use original vector covers in BootKode's existing navy/teal/mint identity and the existing builder/assessment interface. Downloadable reference code, the fictional order CSV and the plain-text campaign worksheet are real files linked from the lessons. No interface palette, component system, animation or frontend dependency was added. The outline respects stored curriculum order.

Long course titles now remain bounded in the existing toolbar at tablet widths, with status controls allowed to wrap. Twelve authenticated builder/width checks for the three new drafts pass at 390/768/1024/1440px.
