# Theme Settings

**Evently → Theme Settings** (`inc/admin/theme-settings.php`) is one option (`evently_settings`) with 24 fields across 9 sections. Every field is read through `evently_get_setting( $key, $default )` — grep the theme for that function to see exactly which template reads which setting.

| Section | Fields | Actually affects |
|---|---|---|
| General | Create Event URL, Events Page | Header/footer "Create Event" links; `evently_get_events_page_url()`'s first lookup |
| Header | Hero Image URL, Hero Live Note | Homepage hero (`template-parts/home/hero.php`) |
| Colors | Primary Accent, Secondary Accent, Dark | Overrides the design-token CSS variables site-wide via a small inline `<style>` printed in `wp_head` (`evently_print_color_overrides()`) — only when changed from the defaults, so nothing prints on a fresh install |
| Events | Default City, Featured Event fields, card display toggles (price/location/favorite/rating) | Homepage "Near You" default city, Featured Event section content, and real per-card rendering in `template-parts/cards/event-card.php` |
| Archive | Events per page, Default view (grid/list) | `Evently_Booking_Adapter::search_events()`'s `per_page`, and the Event Archive's initial grid/list state |
| Single Event | Show related events | Whether `template-parts/event/related-events.php` renders |
| Footer | Footer tagline | Footer brand column |
| Social | Instagram/Facebook/X/YouTube URLs | Footer social icons — icons only render for networks with a URL set |
| Performance | Lazy-load images | Reserved for a future toggle; images already use `loading="lazy"` by default throughout |

## Extending it

Add a new field by adding one entry to `evently_get_settings_fields()` in `inc/admin/theme-settings.php` — the form field, sanitizer, and storage are all generated from that one array. Read it anywhere with `evently_get_setting( 'your_key', $default )`.
