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
