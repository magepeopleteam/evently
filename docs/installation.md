# Installation

## Requirements

- WordPress 6.4+
- PHP 7.4+
- Recommended: WooCommerce (for ticket checkout/payments) and a compatible event-booking plugin built against mage-eventpress ("Event Booking Manager for WooCommerce")

Evently works without either plugin — see [Event Booking Integration](booking-integration.md) for exactly what degrades gracefully and how.

## Install the theme

1. **Appearance → Themes → Add New → Upload Theme**, choose the Evently `.zip`, click **Install Now**, then **Activate**.
2. Or unzip to `wp-content/themes/evently` via FTP/SFTP if you prefer manual installation.

## Install the recommended plugins

1. **Plugins → Add New**, search "WooCommerce", install and activate. (Evently's **Setup** screen also offers a one-click install for WooCommerce specifically, since it's hosted on wordpress.org.)
2. Install and activate your Event Booking plugin. This is not a wordpress.org plugin, so it must be uploaded manually (Plugins → Add New → Upload Plugin) or installed from your license account.

## Run the Setup wizard

Go to **Evently → Setup** in wp-admin. It will:

- Show live status for both required plugins (with a real install button for WooCommerce, and a link out for the booking plugin, since it can't be installed via the wordpress.org API).
- Let you import the "All Events" demo (categories, organizers, 8 real events with real ticket types, 3 blog posts, the Events + Organizer Dashboard pages, and a starter navigation menu). Re-running the import is safe — it detects and skips content it already created.

See [Demo Import](demo-import.md) for exactly what gets created and how to undo it.

## Configure Theme Settings

**Evently → Theme Settings** covers branding, colors, header/footer content, event-card display toggles, archive defaults, and social links. See [Theme Settings](theme-settings.md).
