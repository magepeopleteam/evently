<?php
/**
 * Evently theme bootstrap.
 *
 * This file only defines constants and loads the theme's modules from inc/.
 * No presentation logic or booking/business logic lives here — see
 * docs/architecture.md for where each concern belongs.
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Disallow direct access.
}

/** Theme version — bump on every release; also used as a cache-busting asset suffix. */
define( 'EVENTLY_VERSION', '1.0.0' );

/** Absolute filesystem path to the theme, no trailing slash. */
define( 'EVENTLY_DIR', trailingslashit( get_template_directory() ) );

/** Base URI of the theme, no trailing slash. */
define( 'EVENTLY_URI', trailingslashit( get_template_directory_uri() ) );

/** Minimum WordPress/PHP the theme targets — kept in sync with style.css header. */
define( 'EVENTLY_MIN_PHP', '7.4' );

/**
 * Core modules. Order matters: setup before enqueue, helpers before anything
 * that calls them, integrations last so they can rely on template functions.
 */
$evently_includes = array(
	'inc/helpers.php',
	'inc/setup.php',
	'inc/enqueue.php',
	'inc/template-functions.php',
	'inc/template-hooks.php',
	'inc/blocks/blocks.php',
	'inc/patterns/patterns.php',
	'inc/integrations/booking-plugin.php',
	'inc/integrations/woocommerce.php',
	'inc/integrations/elementor.php',
	'inc/demo-import/sample-data.php',
	'inc/demo-import/importer.php',
	'inc/admin/admin.php', // Loads inc/admin/theme-settings.php + inc/admin/setup-wizard.php itself — admin-only, so it's the last thing bootstrapped.
);

foreach ( $evently_includes as $evently_include ) {
	$evently_include_path = EVENTLY_DIR . $evently_include;
	if ( is_readable( $evently_include_path ) ) {
		require_once $evently_include_path;
	}
}
unset( $evently_includes, $evently_include, $evently_include_path );
