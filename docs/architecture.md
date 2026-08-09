# Architecture

## The one rule everything else follows

**Evently owns presentation. The Event Booking plugin owns events/tickets/bookings/attendees. WooCommerce owns orders/payments/checkout.** Deactivating or switching the theme never deletes or corrupts booking data, because the theme never stores any.

```
Evently Theme                     Event Booking Plugin        WooCommerce
──────────────                    ─────────────────────       ───────────
Layout, styling, templates   ──►  Events, tickets, bookings    Orders, payments,
Gutenberg patterns                 attendees, availability      cart, checkout
Elementor widgets                  booking rules
Theme Settings              ◄──── read-only, via
                                   Evently_Booking_Adapter
```

## Classic-hierarchy, not full-site-editing

Evently ships a complete `theme.json` (colors/typography/spacing/radius tokens, block patterns) but is **not** a full block/FSE theme — there's no `templates/index.html`. Routing goes through the classic PHP template hierarchy (`header.php`, `front-page.php`, `single.php`, …). This is deliberate: the booking plugin ships its own classic-hierarchy-style template-override convention (see below) and Elementor/WooCommerce both integrate more predictably with classic-hierarchy themes than with FSE-only ones. Full reasoning: `docs/implementation-plan.md` §3.

## Where a page's markup actually comes from

| Request | Resolved by |
|---|---|
| `/` | `front-page.php` — always wins for the site root, regardless of Settings → Reading |
| Any Page using the "Evently — Event Archive" template | `page-templates/event-archive.php` |
| Any Page using the "Evently — Organizer Dashboard" template | `page-templates/organizer-dashboard.php` |
| A single `mep_events` post | The booking plugin's own bundled single-event template — Evently retired its `mage-event/` override (see `docs/booking-integration.md`) |
| A `mep_cat` / `mep_org` term archive | The booking plugin's own bundled taxonomy template — same reason |
| Everything else (posts, pages, search, 404) | Standard classic hierarchy (`single.php`, `page.php`, `search.php`, `404.php`, `index.php`) |

Full detail on the plugin's own template-override mechanism and why `mep_events` has no native archive URL: `docs/booking-integration.md`.

## The three places business logic is allowed to live

1. **The booking plugin's own hooks**, called through `Evently_Booking_Adapter` (data reads) or captured directly (`render_booking_form()`, the real transactional ticket form).
2. **WooCommerce's own templates/hooks**, restyled but never reimplemented (`docs/woocommerce.md`).
3. Nowhere else. If you find yourself computing a price, checking availability, or deciding whether a booking succeeded inside a theme template — that's a bug; it belongs in the adapter calling the plugin, not in the template.

See `docs/implementation-plan.md` for the full phase-by-phase build record and every architectural decision's rationale, and `docs/hooks.md` / `docs/customization.md` for extension points.
