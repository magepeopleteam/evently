# Event Booking Integration

Evently is a presentation-only theme. All booking business logic — pricing, availability, cart, checkout, attendee records, order status — belongs to the event-booking plugin and (optionally) WooCommerce. The theme never re-implements any of it. This document explains exactly how the two integration mechanisms work, so you can extend the theme safely or point it at a different (compatible) plugin.

## The plugin this theme was built against

mage-eventpress ("Event Booking Manager for WooCommerce" by MagePeople) — CPT `mep_events`, taxonomies `mep_cat` (category) / `mep_org` (organizer/venue) / `mep_tag`, prefixes `mep_` / `mpwem_` / `MPWEM_` / `MEP_`.

Two important, non-obvious facts about it that shaped this integration:

1. **`mep_events` is registered with `has_archive => false`.** There is no native "browse all events" URL. The plugin's own intended pattern is a WordPress Page with its `[event-list]`/`[events_list]` shortcode — Evently's equivalent is `page-templates/event-archive.php`, a real Page Template, found automatically by `evently_get_events_page_url()`. Never call `get_post_type_archive_link( 'mep_events' )` directly in a template — it returns `false` on a default install.
2. **The plugin has its own template-override folder convention**, unrelated to WordPress's normal template hierarchy: `MPWEM_Functions::template_path( $file )` checks `get_stylesheet_directory() . '/mage-event/' . $file` before falling back to its own bundled template. Evently **retired its `mage-event/` override files** (single-events.php, event-archive.php, taxonomy-category.php, taxonomy-organozer.php, and their layout partials) — the single-event page and the `mep_cat`/`mep_org` taxonomy archives now render through the plugin's own bundled templates unmodified. `mage-event/event-archive.php` was dead weight in practice anyway: `mep_events` has `has_archive => false`, so WordPress never routes to a post-type-archive template for it. If you need a themed single-event/taxonomy skin again, re-add the same `mage-event/{file}.php` paths — the plugin's resolver picks them up automatically, no registration needed.

## `Evently_Booking_Adapter` — the only door into plugin data

`inc/integrations/booking-plugin.php` defines every method a template needs: `get_event_card_data()`, `get_events_for_cards()`, `search_events()`, `get_ticket_types()`, `get_min_price()`, `get_availability_status()`, `get_address()`, `get_organizer_term()`, `get_faqs()`, `get_organizer_stats()`, `render_booking_form()`, and more. Every method:

- Starts with `if ( ! self::is_active() ) { return <safe empty value>; }` — so nothing fatals when the plugin is inactive.
- Reads plugin data only through the plugin's own public statics (`MPWEM_Functions`, `MPWEM_Query`, `MPWEM_Global_Function`) or public hooks — never raw SQL, never guessed meta keys.

**No template should call `MPWEM_*`/`mep_*` anything directly.** If you need a new piece of plugin data in a template, add a method to the adapter first.

**Currently unused pending a caller:** `render_booking_form()`, `render_hook_widget()`, and `render_event_reviews()` were written for `mage-event/single-events.php`'s markup and have no caller now that file is retired (see above). They're still correct and safe to call — wire them into a new single-event template if you reintroduce a themed skin.

## The one thing the adapter deliberately does NOT rebuild

`Evently_Booking_Adapter::render_booking_form( $event_id )` captures the output of `do_action( 'mpwem_registration', $event_id, $meta )` — the plugin's own real ticket-selection + add-to-cart form (early-bird windows, member-only gating, cart-state detection, WooCommerce-vs-native-checkout branching all live there). Evently does **not** restyle plugin event-details markup unless a real `mage-event/single-events.php` theme override exists (retired by default). On singular `mep_events`, only `plugin-event-details.css` loads (fixed-header clearance + isolation from theme base resets). If you need to change how tickets are selected, that's a plugin-side change, not a theme-side one.

## Swapping in a different booking plugin

1. Rewrite `Evently_Booking_Adapter`'s method bodies to call your plugin's real API (keep the same public method signatures so every template keeps working unmodified).
2. Update `evently_has_booking_plugin()` (`inc/helpers.php`) to detect your plugin instead of `mep_events`.
3. If your plugin has its own template-override convention, add `mage-event/` files for it (the path is retired but the plugin resolver still checks for it automatically — see above); if it uses the normal WordPress template hierarchy instead, add classic `single-{post_type}.php` / `archive-{post_type}.php` files instead.
4. `render_booking_form()` is the one method it's fine to actually reimplement, if your plugin's checkout form isn't hook-renderable the same way — everything else should stay a thin, faithful wrapper.

## Confirmed absent from the inspected plugin (so Evently never fakes them)

- **No organizer-facing frontend dashboard.** It's a listed paid upsell in the plugin's own admin screens. Evently's Organizer Dashboard (`page-templates/organizer-dashboard.php`) is real, but scoped to what the data model actually supports: real revenue/ticket stats (via `MPWEM_Functions::registration_stats()`) for events the logged-in user authored — not a simulated multi-tenant SaaS dashboard.
- **No QR-code / digital-ticket feature.** Also a separate paid add-on. The homepage's Digital Ticket showcase is explicitly decorative marketing artwork (`aria-hidden`, documented in its own file header) — never presented as a real, scannable ticket.
