# Troubleshooting

**"Explore Events" / "Create Event" links go to the homepage instead of an events listing.**
`evently_get_events_page_url()` couldn't find a page using the "Evently — Event Archive" template. Run the demo import (Evently → Setup) to create one automatically, or create a Page yourself and set its Template (Page Attributes) to "Evently — Event Archive".

**The ticket-selection form doesn't appear on a single event page.**
It requires the Event Booking plugin to be active (`evently_has_booking_plugin()`); if it's active but the form is still missing, check the event's own **Registration Status** setting in the plugin's event editor — Evently renders whatever the plugin's `mpwem_registration` hook outputs, including its own "registration closed"/"members only" messages.

**Demo import says "Note: N demo image(s) could not be downloaded."**
Demo images are bundled with the theme (`assets/images/demo/`) and copied locally, so this should be rare — it only appears if one of those bundled files is missing (e.g. a stripped-down installer package) and its fallback (fetching the original hotlinked URL) also failed. Confirm `assets/images/demo/` shipped with your copy of the theme; everything else imports correctly either way, and simply re-running the import retries only the images that are still missing.

**WooCommerce shop/cart/checkout pages show a "coming soon" message instead of the styled templates.**
That's WooCommerce's own "Coming Soon" site-visibility mode, not a theme issue — turn it off under WooCommerce → Settings → Site visibility.

**A page looks unstyled / raw HTML.**
Check `inc/enqueue.php`'s conditions — Evently loads CSS conditionally per template to stay fast. If you've added a custom page template that needs one of the theme's stylesheets, enqueue it explicitly with `wp_enqueue_style( 'evently-<handle>' )` (the handles are all registered in `evently_register_assets()`, so you don't need the file path again).

**Colors/fonts don't match after changing Theme Settings.**
Color overrides only print when they differ from the design defaults (`evently_print_color_overrides()`) — check the saved value isn't identical to the default. For fonts, confirm `assets/fonts/plus-jakarta-sans.css` doesn't exist if you expect the Google Fonts fallback, or does exist (with valid font files) if you expect the local one.

Still stuck? Check `docs/faq.md`, or the plugin-integration specifics in `docs/booking-integration.md`.
