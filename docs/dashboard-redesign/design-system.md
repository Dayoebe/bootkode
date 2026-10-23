# Dashboard design system guide

The source of truth remains [../design-system.md](../design-system.md), `resources/css/tokens.css`, theme RGB variables in `app.css`, and semantic utilities in `tailwind.config.js`.

Keep existing Sora headings, Manrope body, navy/teal actions, white surfaces and theme support. This guide describes their authenticated application use; it is not another token system.

- Page gutters: 16px mobile, 24px tablet, 32px desktop; maximum content width 1440px.
- Spacing: multiples of 4px, 24px between sections, 16/24px panel padding.
- Radius: existing 8px panel/control token; pills reserved for statuses and avatars.
- Type: 24–32px page title, 18–20px section title, 14–16px body, 12–14px metadata. Data uses tabular numerals.
- Controls: 44px standard minimum, visible focus, labels and associated help/errors. Use PHP validation and Livewire loading targets.
- Desktop shell at 1024px: 256px sidebar or 80px persistent rail. Tablet/mobile: trapped, scrollable full navigation drawer and a short role-aware bottom bar with safe area spacing.
- Tables: `ui.table` provides a labelled scroll region, bounded width and keyboard access. Keep essential columns available.
- Dialogs: existing `modal` API, focus trap/return, Escape, backdrop dismissal, bounded viewport.
- Navigation: native links/buttons, `aria-current`, expanded disclosure state and matching labelled regions; do not use ARIA menu roles for ordinary site navigation.
- Feedback: `ui.flash` live status, honest unavailable/empty/error text. A failing search must not claim there are no matches.
- Icons: existing Font Awesome, decorative icons hidden from assistive technology. No ambient movement; reduced motion respected.

```blade
<x-ui.page-header title="Transactions" description="Review your recorded wallet activity.">
    <x-slot:actions><a href="{{ route('wallet.index') }}" class="bk-button bk-button-secondary">Wallet</a></x-slot:actions>
</x-ui.page-header>
<x-ui.panel><x-ui.table label="Wallet transactions"><table>...</table></x-ui.table></x-ui.panel>
```

Use the actual named route for each feature. Public layouts and printable documents are deliberate exceptions. A structural migration does not prove every populated, empty, error or modal state has passed browser acceptance; consult verification and the checklist.
