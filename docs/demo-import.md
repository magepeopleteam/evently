# Demo Import

**Evently → Setup** imports the "Evently — All Events" demo. Other demos listed in the wizard (Concert, Conference, Wedding, Sports, Workshop) are placeholders for future releases — the importer's registry (`Evently_Demo_Importer`) is built so adding one is a matter of adding another dataset + calling the same `run()` machinery, not rewriting the wizard.

## What gets created

| Content | Count | Detail |
|---|---|---|
| Event categories (`mep_cat`) | 6 | Music, Conference, Workshop, Food & Dining, Sports, Business |
| Organizers (`mep_org`) | 8 | One per demo event |
| Events (`mep_events`) | 8 | Real dates (a mix of fixed/particular-multi-date/recurring — see docs/demo-content.md), venue/city/country, two real ticket types (General + VIP) with live quantity/availability, a 300–400 word description, a 3-question FAQ, a 5–6 entry Timeline, a 4-photo Gallery, and a real featured image — all copied from the theme's bundled `assets/images/demo/` files |
| Blog posts | 3 | The Event Journal articles named in the design brief |
| Pages | 2 | "Events" (Event Archive template) and "Organizer Dashboard" template, reusing existing pages with those templates already assigned instead of duplicating |
| Navigation menu | 1 | Only created if the "Primary Navigation" location has no menu assigned yet — never replaces an existing menu |

## Safety

- **Idempotent.** Every category/organizer is looked up by name before creating (`term_exists()`); every event/post is looked up by slug/title before creating; every page is looked up by its `_wp_page_template` meta value first. Running the import twice does not duplicate anything.
- **Never touches existing content it didn't create.** Every post the importer creates is tagged with `_evently_demo_content` postmeta so it's always identifiable — the importer never scans for "anything that looks like an event" to delete or modify.
- **No silent deletion.** There is currently no "remove demo content" action exposed in the UI, specifically so nothing can be deleted without a confirmation step; if you need to remove the demo data, delete the tagged posts from **Events → All Events** (filter by the `_evently_demo_content` meta, or simply by the recognizable demo titles) the normal WordPress way.
- **Featured images are copied from bundled local files, not fetched over the network.** `attach_featured_image()` reads the theme's own `assets/images/demo/*.jpg` and uploads them to the Media Library directly — no outbound request, so it works even on hosts with a broken/incomplete CA certificate bundle (a real issue found and fixed on one local dev stack during development; see `docs/demo-content.md`). It only falls back to fetching the original hotlinked URL if the bundled file is somehow missing, and reports how many images failed in the import log if even that fallback fails, rather than failing silently.
- **Self-healing.** If an earlier import run created an event/post but its image failed to attach (e.g. from an older version of this importer, or a genuinely unreachable fallback URL), simply re-running the import attaches the missing image without duplicating or otherwise touching the existing post — `attach_featured_image()` is a no-op for anything that already has a featured image.
- **Content refreshes on re-import, images don't.** An existing demo event's description, dates, FAQ, Timeline and Gallery are re-synced to the current dataset on every run (`Evently_Demo_Importer::sync_event_content()`) — this is the theme's own generated content, so "re-import" is expected to bring it up to date with the theme version installed, the same way most themes' demo importers work. The one exception is the featured image, which is left alone once set, so replacing a demo event's photo in the Media Library sticks across re-imports.

## Requirements it checks

- **WooCommerce** — offers a real one-click install (it's on wordpress.org) if missing.
- **Event Booking plugin** — detected via `post_type_exists('mep_events')`; since it isn't a wordpress.org plugin, Setup links out to get it rather than pretending to install it.

Import can only run once the booking plugin is active — events cannot exist without it.
