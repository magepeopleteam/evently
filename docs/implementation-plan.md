# Evently — Implementation Plan

## 1. Inspection summary

**Design source (authoritative):** `Event Booking Homepage Design/` (a Figma Make export) sitting inside this theme folder.
- `src/imports/pasted_text/evently-website-design.md` — the original design brief (colors, type scale, spacing, section-by-section copy).
- `src/App.tsx` — a working React/Tailwind implementation of the homepage. Used for exact structure, copy, and interaction behavior (hover states, vibe filter, calendar selection, favorite toggle).
- `evently.html` — a static HTML/CSS export of the same homepage using semantic class names (`.hero`, `.event-card`, `.cal-widget`, `.dashboard`, …). This is the primary source for the theme's CSS architecture because its class naming already matches the "reusable component" requirement.
- `src/index.css` — confirms the Tailwind v4 theme tokens (colors, font) match the brief exactly.

Both React and static exports agree pixel-for-pixel (same copy, same spacing, same breakpoints: 1000px and 640px), so there is no ambiguity between them — the static export's class names are adopted verbatim as the theme's CSS component vocabulary (`.hero-*`, `.event-card`, `.cal-*`, `.dashboard`, `.ticket-*`, etc.) and the React file is used to confirm dynamic/interactive behavior.

**Plugin source (authoritative "Event Booking Plugin"):** `mage-eventpress` ("Event Booking Manager for WooCommerce" by MagePeople, CPT `mep_events`, prefixes `mep_`/`mpwem_`/`MPWEM_`/`MEP_`), plus optional `mage-eventpress-pro`. This is a real, mature WooCommerce-dependent plugin with its own CPTs, taxonomies, shortcodes, template-override mechanism, WooCommerce cart integration, and a My Account dashboard tab. The Evently theme treats this plugin as **the** event-booking backend and never re-implements booking/ticket/order logic — it only reads plugin data through an adapter and provides presentation.

No organizer-facing frontend dashboard or QR-ticket generator exists in the inspected plugin code — per brief §44/§23, the Organizer Dashboard section of Evently is built as a **UI-only demonstration** clearly marked as such, not wired to fake data claiming to be real.

## 2. Design tokens (locked)

| Token | Value |
|---|---|
| `--evently-color-bg` | `#FFFFFF` |
| `--evently-color-soft` | `#F6F6F3` |
| `--evently-color-dark` | `#0B0B0D` |
| `--evently-color-text` | `#111113` |
| `--evently-color-muted` | `#777773` |
| `--evently-color-border` | `#E7E7E3` |
| `--evently-color-accent` | `#6C5CE7` |
| `--evently-color-orange` | `#FF7657` |
| `--evently-color-success` | `#16A34A` (confirmed live-dot green `#22C55E` used at small scale in design; `#16A34A` per brief used for system-level success states e.g. availability) |
| `--evently-color-warning` | `#F59E0B` |
| `--evently-color-error` | `#DC2626` |

Font: Plus Jakarta Sans (400/500/600/700/800). Max content width 1240px (canvas 1440px). Section padding 100px (desktop) / 32px tablet / 20px mobile side padding. Radii: 10 / 16 / 24 / 28 (hero image / major cards, kept as an Evently-specific token alongside the brief's 10/16/24/32) / pill 999. 8px spacing scale per brief §9.

## 2.1 mage-eventpress integration facts (from direct code inspection — nothing below is inferred)

- CPT `mep_events`; taxonomies `mep_cat` (category), `mep_org` (organizer/venue — address lives in **term meta**: `org_location/org_street/org_city/org_state/org_postcode/org_country`), `mep_tag`.
- Query via `MPWEM_Query::event_query()` / `event_list_query()` — never hand-rolled `WP_Query` args, so upcoming/expired/stock logic stays centralized in the plugin.
- Per-event data via `MPWEM_Functions::get_all_info( $event_id )` (flattened meta + computed keys: `all_date`, `all_time`, `upcoming_date`, `full_address`, …) and companion statics: `get_all_dates()`, `get_all_times()`, `get_upcoming_date_time()`, `get_location()`, `get_total_available_seat()`, `get_available_ticket()`, `get_min_price()`. Ticket types are **one post-meta array**, `mep_event_ticket_type` (`option_name_t`, `option_price_t`, `option_qty_t`, `option_details_t`, …) — not separate posts/products.
- **Template override is a dedicated folder convention, not the WP template hierarchy.** `MPWEM_Functions::template_path( $file )` checks `get_stylesheet_directory() . '/mage-event/' . $file` first, else falls back to the plugin's own `templates/$file`. The plugin's own `single_template` / `archive_template` / `template_include` filter callbacks resolve unconditionally through this path for `mep_events`/`mep_org`/`mep_cat`/`mep_tag` — a classic-hierarchy file like theme-root `single-mep_events.php` is **never consulted**. Evently therefore ships its skin for these pages as `evently/mage-event/single-events.php`, `evently/mage-event/event-archive.php`, `evently/mage-event/taxonomy-category.php`, `evently/mage-event/taxonomy-organozer.php`, and `evently/mage-event/layout/*.php` / `evently/mage-event/list/*.php` — same relative paths as the plugin's `templates/` tree, restyled with Evently markup/classes but built from the same `$event_infos` data contract. This is the plugin's own sanctioned customization path (it even ships a "Template Override" admin screen that copies files into exactly this folder).
- WooCommerce model: **one hidden WC simple product per event** (not one per ticket type); ticket type/qty/date are cart-item-data, recalculated in `woocommerce_before_calculate_totals`. Booking submits a normal WC add-to-cart form when WC payment is enabled. When WooCommerce is inactive or its payment is switched off, the plugin's own native/offline checkout (`MEP_Pro_Native_Checkout.php`, AJAX `mep_native_checkout` — ships in the FREE plugin despite the class name) handles it instead — so the plugin (and Evently on top of it) already degrades gracefully without WooCommerce.
- My Account gets two endpoints, `event-bookings` / `event-booking-details` ("Event Booking Dashboard"), added via `woocommerce_account_menu_items` — Evently's "My Tickets" experience styles these endpoints rather than re-implementing them.
- **Confirmed absent, so Evently must not fake them:** no organizer-facing frontend dashboard anywhere in the plugin (it's a listed paid upsell — "Marketplace / Event Frontend Submit Addon" — in the plugin's own upgrade screen), and no QR-code/digital-ticket feature (also a listed separate paid add-on). Evently's Organizer Dashboard (§23) ships as a clearly-labeled UI demonstration; the Digital Ticket's QR block renders a decorative placeholder with an honest "requires a QR ticket add-on" note rather than a fake scannable code.
- Useful hooks for the adapter/theme to attach to: action `mep_event_list_shortcode( $event_id, $columnNumber, $style, $width, $unq_id )` (fires per card inside the plugin's own shortcode loops — lets a theme swap in its own card renderer), `mpwem_registration`, `mep_ticket_type_loop_list_row_start`; filters `mep_ticket_type_price`, `mpwem_group_ticket_price`.
- Admin capability: `MPWEM_Global_Function::get_admin_capability()` → `manage_woocommerce` when WooCommerce is active, else `edit_posts`.

## 3. Architecture decisions

1. **Hybrid theme, deliberately classic-routed.** Ships a full `theme.json` (v3 — WP 7.0.3 is present in this environment) so the Block Editor gets Evently's color palette, typography, spacing and radius presets, plus block patterns/style variations — but the theme does **not** ship `templates/index.html` and is therefore not flagged a full block/FSE theme (`wp_is_block_theme()` stays `false`). All routing goes through the classic PHP template hierarchy (`index.php`, `front-page.php`, `page.php`, `single.php`, `archive.php`, `single-mep_events.php`, `archive-mep_events.php`, `taxonomy-*.php`, `search.php`, `404.php`, `header.php`/`footer.php`). This is the standard "hybrid" pattern (theme.json + classic hierarchy) and is the safer choice here because: (a) `mage-eventpress` ships its own classic PHP templates it expects a theme to override via the classic hierarchy/`locate_template()`, not block HTML templates; (b) Elementor's Theme Builder and location system integrates far more predictably with classic template hierarchies than with full FSE themes; (c) WooCommerce's own template override system (`woocommerce.php`, `woocommerce/` template overrides) is classic-hierarchy-based. Patterns are still registered as real Gutenberg block patterns (brief §25) usable inside the block editor and are also what `front-page.php` assembles server-side for the homepage.
2. **CSS architecture** mirrors `evently.html`'s class system, split into the files the brief's §5 file structure specifies (`variables.css`, `base.css`, `typography.css`, `layout.css`, `components.css`, then one file per homepage-adjacent section group, `responsive.css` last). No Tailwind at runtime — Tailwind was only the Figma Make authoring tool; shipping a themeforest product on Tailwind's JIT engine is impractical, so the utility output is transcribed to hand-rolled BEM-style classes matching `evently.html`.
3. **Booking integration adapter.** `inc/integrations/booking-plugin.php` defines `Evently_Booking_Adapter`, a static-method facade over `mage-eventpress`'s real functions/CPT (mapped from the plugin-inspection agent's findings). Every theme template calls the adapter, never the plugin's globals directly. If the plugin is inactive, adapter methods return `null`/empty and templates render an honest "Event booking requires the Evently Booking plugin" notice (never fake data).
4. **WooCommerce is optional.** Detected via `class_exists('WooCommerce')`; theme WC styling loads conditionally (`inc/integrations/woocommerce.php`), consistent with `[[project_wc_optional_native_checkout]]`-style patterns already used on sibling projects.
5. **Elementor is optional.** `inc/integrations/elementor.php` registers an Evently widget category + a handful of widgets that call the *same* rendering functions as the Gutenberg blocks (`inc/template-functions.php` render helpers), so there is exactly one implementation of "render an event card" / "render a search bar" etc.
6. **No business logic in the theme.** Ticket price, availability, and order creation always come from the adapter (which defers to the plugin/WooCommerce). The theme only ever formats/displays.

## 4. File plan (delta from brief §5, adjusted to what's actually needed)

Kept close to the brief's proposed tree. One structural deviation, explained in §3.1 above: `templates/*.html` and `parts/*.html` (full-site-editing block templates) are **not** shipped — the theme is classic-hierarchy-routed for Elementor/WooCommerce/plugin compatibility, using `template-parts/` (PHP, `get_template_part()`) instead of `parts/*.html`. `theme.json` is still shipped in full for editor styling/presets/patterns. Demo importer lives under `inc/demo-import/`. Theme Settings under `inc/admin/`.

## 5. Phased delivery order

1. Design system + theme foundation (tokens, `theme.json`, `functions.php`, setup/enqueue)
2. Header/Footer template parts + component library (buttons, badges, the 6 event-card variants)
3. Homepage (16 sections from brief §11, as patterns assembled into `front-page`)
4. Event Archive (search + filters + grid/list + mobile drawer)
5. Single Event + sticky booking card + `Evently_Booking_Adapter`
6. Checkout styling / booking confirmation / digital ticket / My Account (“My Tickets”)
7. Organizer profile + organizer dashboard UI (clearly-labeled demo)
8. Blog (archive/single/category/related)
9. Gutenberg blocks/patterns + Elementor widgets + WooCommerce polish
10. Demo importer + "All Events" dummy content
11. Theme Settings admin
12. Documentation, i18n (`.pot`), accessibility/security/QA pass

Each phase is reported to the user with a short status summary; work continues without pausing for approval between phases unless a genuine ambiguity requires a decision only the user can make.
