# BootKode design system

Learn. Build. Grow. Education comes first; use factual course content, clear next actions and restrained editorial hierarchy.

## Identity and tokens

Keep the established navy/teal identity and existing Sora headings / Manrope body fonts (system fallbacks, Google Fonts `display=swap`). Navy `#0f172a` anchors navigation and primary emphasis; teal `#0f766e` is the light-theme action color; mint `#8fe388` is reserved for emphasis on navy. White/slate surfaces separate content. Success, warning and danger convey meaning with accompanying text/icons. The logo remains unchanged.

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
