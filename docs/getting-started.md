# Getting Started

## The core user journey

Discover (homepage) → Search/Filter (Events page) → Explore (single event) → Select Ticket → Book → Receive Ticket (My Account → Event Booking Dashboard).

## What each area of the site is

| Page | Template file | Notes |
|---|---|---|
| Homepage | `front-page.php` | 14 independent sections, each a `template-parts/home/*.php` file. Reorder/remove via the `evently_home_sections` filter. |
| Events (browse all) | `page-templates/event-archive.php` | Assign this Page Template to any Page — the theme finds it automatically (`evently_get_events_page_url()`). Created automatically by Setup's demo import. |
| Category / Organizer archive | Booking plugin's own bundled taxonomy template | Evently retired its `mage-event/` override — see [Event Booking Integration](booking-integration.md). |
| Single event | Booking plugin's own bundled single-event template | Evently retired its `mage-event/` override. The ticket-selection form is still the plugin's real form, restyled via CSS — not reimplemented. |
| Organizer Dashboard | `page-templates/organizer-dashboard.php` | Real stats for the logged-in user's own events — no simulated data. |
| Blog | `index.php` / `archive.php` / `single.php` / `search.php` | Editorial "Event Journal" cards, not generic post loops. |
| WooCommerce (shop/cart/checkout/My Account) | Styled via CSS + a light wrapper hook, no template overrides | See [WooCommerce](woocommerce.md). |

## Where things live

```
assets/       Compiled-free CSS/JS/icons/fonts — one file per concern, loaded conditionally (inc/enqueue.php)
inc/          PHP: setup, enqueue, helpers, template functions/hooks, blocks, patterns, integrations, admin, demo-import
template-parts/  Reusable partials (header, footer, cards, home sections, archive filters, event content, modals)
page-templates/  Classic WordPress Page Templates (Event Archive, Organizer Dashboard)
patterns/     Block pattern content generators (one file per pattern, registered by inc/patterns/patterns.php)
docs/         You are here
```

## First things to customize

1. **Evently → Theme Settings** → General: set your "Create Event" URL and Events page.
2. **Appearance → Editor → Site Title/Logo** (or Customizer, on classic setups): upload your logo — falls back to the site title, styled as a wordmark, if you skip this.
3. **Menus**: assign a menu to "Primary Navigation" (Appearance → Menus) — the header/footer render sensible defaults until you do.
4. Replace the demo dataset's Unsplash-hotlinked imagery with your own licensed photography before going live — see [Demo Content](demo-content.md).
