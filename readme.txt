=== Evently ===

Theme Name: Evently
Theme URI: https://evently.example.com
Author: Evently Team
Author URI: https://evently.example.com
Tags: event, booking, events, tickets, conference, festival, block-patterns, custom-logo, custom-menu, editor-style, featured-images, full-width-template, rtl-language-support, threaded-comments, translation-ready, wide-blocks
Requires at least: 6.4
Tested up to: 6.7
Requires PHP: 7.4
License: GNU General Public License v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Evently is a premium event discovery and ticket-booking WordPress theme for concerts, conferences, festivals, workshops and every experience worth remembering.

== Description ==

Evently is a presentation-layer theme: it never stores, prices, or processes bookings itself. Event and booking data comes from a compatible event-booking plugin (built against mage-eventpress — "Event Booking Manager for WooCommerce"); payments, cart and checkout come from WooCommerce when it's installed. Deactivating or switching away from Evently never deletes event, booking, or order data, because the theme never owns that data in the first place.

See docs/ for full documentation: installation, the demo importer, theme settings, the booking-plugin integration contract, Elementor and Gutenberg support, WooCommerce styling, child theming, and the full hook/filter reference.

== Installation ==

1. Install and activate Evently like any WordPress theme (Appearance → Themes → Add New → Upload, or extract to wp-content/themes/evently).
2. Install and activate WooCommerce (optional, but required for ticket checkout) and your event-booking plugin.
3. Go to Evently → Setup to check plugin requirements and import the "All Events" demo content.
4. Visit Evently → Theme Settings to configure branding, colors, and defaults.

Full details: docs/installation.md and docs/getting-started.md.

== Frequently Asked Questions ==

See docs/faq.md and docs/troubleshooting.md.

== Changelog ==

= 1.0.0 =
* Initial release.

== Credits ==

* Plus Jakarta Sans typeface — SIL Open Font License 1.1 (https://fonts.google.com/specimen/Plus+Jakarta+Sans).
* Demo content photography — Unsplash License (https://unsplash.com/license), commercial use permitted, no attribution required. Replace with your own licensed photography before commercial redistribution — see docs/demo-content.md.
* Icons — custom-drawn SVG, original to this theme (assets/icons/).

== Notes for the Envato/ThemeForest reviewer ==

* No third-party code is bundled beyond what's credited above.
* No hardcoded external domains, development URLs, or debug output ship in the theme.
* All user-facing strings are translatable (text domain: evently); see languages/evently.pot.
