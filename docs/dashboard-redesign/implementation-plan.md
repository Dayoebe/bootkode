# Authenticated application redesign

23 October 2026. Branch: `feat/bootkode-dashboard-redesign`.

## Audit and architecture

Laravel 11.46.1, PHP CLI 8.4.27, Livewire 3.6.4, Spatie Permission 6.22.0; Tailwind 3.4.19, Vite 7.3.7, Chart.js 4.5.0, Trix 2.1.19. Livewire supplies Alpine and focus/collapse plugins. Blade layouts, PHP domain components and existing services remain authoritative. Manrope/Sora and Font Awesome 6.5.2 already serve the product. No framework, font or icon migration is needed.

`layouts.dashboard` owns the authenticated shell; `layouts.exam` is a focused assessment exception. Public `app`, `pages` and `blog` layouts remain separate. Route middleware and PHP ownership/role guards control access. `config/menu.php` is the existing navigation source. Seven role dashboards serve student, instructor, mentor, content editor, affiliate ambassador, academy admin and super admin. Several modules have root workspaces with nested partials, so route count is not page count.

The route export contains 286 routes, 237 authenticated routes, including 218 GET routes. See `route-inventory.md` for each handler and access boundary; `page-checklist.md` records view-level coverage and exceptions. Downloads, APIs, redirects and printable documents require different acceptance from interactive pages.

## Findings

- Existing primitives: page-header, panel, empty-state, flash, buttons, text-input, labels/errors, dropdown, modal, theme selectors, course card.
- Inconsistent utility palettes, heavy shadows, oversized profile/dashboard headers, repeated page padding, decoration and hover movement persist inside modules.
- Legacy CSS force-remaps hardcoded utility colors with `!important`; this competes with tokens and can corrupt meaning and contrast.
- The sidebar is tall and decorative, has conflicting responsive label classes, lacks current-page semantics, and its mobile More panel duplicates navigation and has no focus trap.
- Mobile navigation routes all non-students into course authoring and gives all roles learner journeys. It must follow actual roles.
- Public marketing and printable certificates/resumes must not be restyled by authenticated CSS.
- Existing uncommitted course content pipeline work, routes, documentation and build changes were recorded before edits and are excluded from redesign commits.

## Implementation sequence

1. Audit and baseline; record the inventory and test/environment limits.
2. Extend existing semantic tokens and primitives; use a scoped authenticated foundation, eliminate conflicting overrides after migrating callers.
3. Shared shell: compact white navigation, role-aware mobile destinations, one full drawer, persistent desktop collapse, keyboard/focus behavior, real search/notifications.
4. Migrate role dashboards and authenticated module surfaces, controls, tables, headings, feedback and responsive patterns. Preserve domain actions and parameters.
5. Exercise isolated fixture workflows in the dedicated test database, browser checks at 390/768/1024/1440, full suite/build/cache/PHP/diff checks; document exceptions precisely.

## Validation and delivery

Tests use `.env.testing` and `bootkode_test` only. Never migrate/reset the working database or insert browser fixtures there. Capture baseline failures before edits. Reuse the existing running local service where applicable; browser fixture server uses the isolated testing database. Security regressions include role guards, ownership, wallet/replay/rollback, enrolment, assessments and certificates. Check public pages separately. Dependency audits record findings without forced upgrades.

Commit validated, independently reviewable milestones with Conventional Commits, stage explicit files/hunks only, and push normally to the verified `https://github.com/Dayoebe/bootkode.git` remote. Build artefacts are already tracked; isolate the redesign build from the pre-existing authoring changes before committing assets.

## Progress

- [x] Git/stack/route/layout/component audit and baseline test started.
- [ ] Shared foundation and shell.
- [ ] Role dashboards and feature migration.
- [ ] Browser and regression acceptance.
- [ ] Final documentation and pushes.
