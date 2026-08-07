# Demo Content & Assets

## Photography

All 19 demo photos (8 events, 3 blog articles, 6 category tiles, the hero image, the featured-event banner) are bundled as real files under `assets/images/demo/` — sourced from Unsplash (whose [license](https://unsplash.com/license) permits commercial use with no attribution required) but shipped locally with the theme, not hotlinked. This means:

1. The demo import doesn't depend on the server being able to reach a third-party host — `Evently_Demo_Importer::attach_featured_image()` copies the bundled file straight into the Media Library (`wp_upload_bits()`), no outbound network request involved.
2. The homepage's pre-import fallback display (before you've imported anything) also renders these local files, so nothing on a fresh install ever hotlinks an external image.

Each demo item's `image_file` key (in `inc/demo-import/sample-data.php`) names the bundled file; `evently_demo_image_url()` resolves it to a URL, falling back to the original Unsplash URL only if the bundled file is ever missing.

To use your own photography instead: replace the files in `assets/images/demo/` (keep the same filenames, or update the `image_file` values to match), then re-run the import — `attach_featured_image()` skips any post that already has a featured image, so on an existing site you'd remove the old featured image first (or just add your own directly in the editor, which is simpler for a one-off change).

## Event content

Each of the 8 demo events (`inc/demo-import/sample-data.php`) carries real, per-event content rather than a single generic placeholder repeated 8 times:

- **Description** — a genuine 300–400 word write-up (`description`), not the 1-sentence card excerpt padded out.
- **FAQ** — 3 event-specific questions (`faq`), not the same 2 generic ones on every event.
- **Timeline** — a 5–6 entry agenda (`timeline`, time + title + description), rendered via the booking plugin's real `mpwem_timeline` hook (postmeta `mep_event_day`).
- **Gallery** — 4 real bundled photos per event (`gallery_files`), rendered via the plugin's real `mpwem_custom_slider` hook (postmeta `mep_gallery_images`, plain attachment-ID strings). Several events deliberately reuse another demo item's bundled photo rather than requiring 32 unique files — `Evently_Demo_Importer::get_or_create_demo_attachment()` uploads each unique file once and reuses the same Media Library attachment everywhere it's referenced (tracked via `_evently_demo_source_file` postmeta), so re-running the import never creates duplicates.
- **Date type** — a deliberate mix across the 8 events rather than all-identical: 5 use a fixed date (`date_type: 'fixed'`, some single-day, some a continuous multi-day span via `event_end_date_offset`), 1 is a "Particular Event" with several explicit one-off dates (`date_type: 'particular'` + `extra_dates`, mapped to the plugin's own `mep_enable_recurring = 'yes'` + `mep_event_more_date`), and 2 are genuinely recurring (`date_type: 'recurring'` + `recurrence`, mapped to the plugin's `mep_enable_recurring = 'everyday'` + `mep_repeated_periods` day-interval).
- **Related events** — `template-parts/event/related-events.php` always shows 3, even for a category with fewer members: it queries same-category events first, then pads with other upcoming events (excluding itself and anything already picked) until it has 3.

Re-running the import (Evently → Setup) refreshes an existing demo event's description/FAQ/timeline/gallery/dates to match the current dataset — deliberately not frozen at whichever version first created it, since this is the theme's own generated demo content, not something a site owner hand-edits in place. The one thing it never touches is a featured image that's already set (`attach_featured_image()`'s `has_post_thumbnail()` check), so replacing a demo event's photo in the Media Library sticks across re-imports.

## Fonts

Plus Jakarta Sans loads from Google Fonts by default (`inc/enqueue.php`). To self-host it (recommended for performance and GDPR-conscious deployments): download the woff2 files, place them at `assets/fonts/plus-jakarta-sans.css` (a small `@font-face` stylesheet) plus the font files it references, and the theme automatically prefers the local copy — no code changes needed (see the `evently_enqueue_fonts()` function).

## Icons

All icons in `assets/icons/` are original, hand-drawn SVGs made for this theme — no icon font/library dependency.
