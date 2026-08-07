# WooCommerce Compatibility

WooCommerce is optional. Every WooCommerce-facing hook in `inc/integrations/woocommerce.php` and every WooCommerce-scoped stylesheet load in `inc/enqueue.php` is guarded by `evently_has_woocommerce()` (`class_exists( 'WooCommerce' )`) — nothing references WooCommerce classes/functions when it's inactive.

## What Evently changes

- **Nothing to WooCommerce's own logic.** No payment gateway code, no cart/checkout business logic, no order-status handling.
- **Presentation only:**
  - `add_theme_support( 'woocommerce' )` with Evently's image sizes and gallery zoom/lightbox/slider.
  - Wraps WooCommerce's main content in Evently's container (`.evently-container`) via `woocommerce_before_main_content` / `woocommerce_after_main_content`, without touching WooCommerce's own template files.
  - Restyles WooCommerce's real markup: shop grid, single product, cart, checkout (billing/shipping/payment/order-review), My Account (navigation tabs, orders table, addresses, downloads), order-received/thank-you page. See `assets/css/booking.css` — every selector targets WooCommerce's actual class names, verified against its real output, not guessed.

## My Account → Event Booking Dashboard ("My Tickets")

If your booking plugin adds My Account endpoints (mage-eventpress adds `event-bookings` / `event-booking-details`), Evently restyles that real markup too — `.mpwem-event-bookings-dashboard`, `.mpwem-stats`, `.mpwem-bookings-table`, the details modal, status badges — all in `assets/css/booking.css`. The stats, search, and PDF-download button (when a PDF add-on is active) are the plugin's own real AJAX-backed features; Evently doesn't reimplement any of it.

## Testing without going live

If your WooCommerce store is in "Coming Soon" mode, `/cart/`, `/checkout/`, and `/shop/` render WooCommerce's own coming-soon page instead of the normal templates — that's WooCommerce's own behavior, not a theme limitation. Turn Coming Soon mode off (WooCommerce → Settings → Site visibility) to preview the styled cart/checkout/shop pages.
