=== Evently ===

Theme Name: Evently
Theme URI: https://evently.example.com
Author: Evently Team
Author URI: https://evently.example.com
Tags: event, booking, events, tickets, conference, festival, block-patterns, custom-logo, custom-menu, editor-style, featured-images, full-width-template, rtl-language-support, threaded-comments, translation-ready, wide-blocks
Requires at least: 6.4
Tested up to: 6.7
Requires PHP: 7.4
Requires Plugins: elementor, mage-eventpress
License: GNU General Public License v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Evently is a premium event discovery and ticket-booking WordPress theme for concerts, conferences, festivals, workshops and every experience worth remembering.

== Description ==

Evently is a presentation-layer theme: it never stores, prices, or processes bookings itself. Event and booking data comes from mage-eventpress ("Event Booking Manager for WooCommerce", required); payments, cart and checkout come from WooCommerce when it's installed. Deactivating or switching away from Evently never deletes event, booking, or order data, because the theme never owns that data in the first place. Elementor is required — every homepage section ships as a real Elementor widget (and, for use on any other page, a matching Gutenberg block); the demo importer builds a fully pre-designed, directly editable homepage in Elementor rather than a blank canvas.

See docs/ for full documentation: installation, the demo importer, theme settings, the booking-plugin integration contract, Elementor and Gutenberg support, WooCommerce styling, child theming, and the full hook/filter reference.

== Installation ==

1. Install and activate Evently like any WordPress theme (Appearance → Themes → Add New → Upload, or extract to wp-content/themes/evently). You'll be taken straight to Evently → Setup.
2. From Evently → Setup, install and activate the two required plugins — Elementor and mage-eventpress — with one click each, plus WooCommerce if you need ticket checkout (optional).
3. Click "Import Demo Content" — this creates realistic categories, organizers, events with real ticket types, blog posts, the Events/Organizer Dashboard pages, and a fully pre-built, directly editable Elementor homepage.
4. Open the homepage with "Edit Homepage with Elementor" to customize the pre-built design, and visit Evently → Theme Settings for site-wide branding, colors, and defaults.

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
