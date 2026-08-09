<?php
/**
 * Small, dependency-free helper functions used across the theme.
 *
 * Nothing in this file talks to a database table the theme doesn't own, and
 * nothing here makes booking/pricing decisions — it only formats/escapes.
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether the (free or pro) event-booking plugin this theme integrates with is active.
 *
 * Centralised so every template checks the same thing the same way. See
 * inc/integrations/booking-plugin.php for the adapter that actually reads plugin data.
 *
 * @return bool
 */
function evently_has_booking_plugin() {
	return post_type_exists( 'mep_events' );
}

/**
 * Whether WooCommerce is active. WooCommerce is optional for Evently (brief §27) —
 * always gate WooCommerce-specific markup/CSS behind this instead of class_exists()
 * scattered through templates.
 *
 * @return bool
 */
function evently_has_woocommerce() {
	return class_exists( 'WooCommerce' );
}

/**
 * URL of the "browse all events" page.
 *
 * `mep_events` is registered with `has_archive => false` (confirmed in
 * admin/MPWEM_CPT.php), so `get_post_type_archive_link('mep_events')`
 * always returns false on a default install — this plugin expects the
 * "browse all events" experience to live on a regular WP Page using the
 * `[event-list]`/`[events_list]` shortcode (or, here, Evently's own
 * "Event Archive" page template) rather than a native CPT archive route.
 * Every template must call this helper instead of
 * get_post_type_archive_link() directly, so there's one place that knows
 * how to find that page.
 *
 * Resolution order: the page explicitly chosen in Evently → Theme Settings,
 * then the first published page using page-templates/event-archive.php,
 * then (in case a future plugin version enables has_archive) the native
 * archive link, then the site home as a last resort.
 *
 * @return string
 */
function evently_get_events_page_url() {
	static $url = null;

	if ( null !== $url ) {
		return $url;
	}

	if ( ! evently_has_booking_plugin() ) {
		$url = home_url( '/' );
		return $url;
	}

	$configured_id = (int) evently_get_setting( 'events_page_id', 0 );
	if ( $configured_id && 'publish' === get_post_status( $configured_id ) ) {
		$url = get_permalink( $configured_id );
		return $url;
	}

	$pages = get_posts(
		array(
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'meta_key'       => '_wp_page_template', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- one-time lookup, result cached in-request via the static above.
			'meta_value'     => 'page-templates/event-archive.php', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
		)
	);
	if ( ! empty( $pages ) ) {
		$url = get_permalink( $pages[0] );
		return $url;
	}

	$archive_link = get_post_type_archive_link( 'mep_events' );
	$url          = $archive_link ? $archive_link : home_url( '/' );

	return $url;
}

/**
 * Whether Elementor is active.
 *
 * @return bool
 */
function evently_has_elementor() {
	return did_action( 'elementor/loaded' );
}

/**
 * Fetch a single Evently theme setting with a safe fallback.
 *
 * All Evently → Theme Settings values live in one option (`evently_settings`)
 * so activating/deactivating the theme never scatters dozens of orphaned
 * wp_options rows. See inc/admin/theme-settings.php for the settings screen.
 *
 * @param string $key     Setting key.
 * @param mixed  $default Fallback if unset.
 * @return mixed
 */
function evently_get_setting( $key, $default = '' ) {
	static $settings = null;

	// The Theme Settings screen's live-preview iframe (inc/admin/theme-settings.php's
	// evently_ajax_preview_section()) overlays unsaved, sanitized values here for the
	// span of a single preview request — never persisted, never touches the DB option.
	if ( isset( $GLOBALS['evently_preview_overrides'] ) && array_key_exists( $key, $GLOBALS['evently_preview_overrides'] ) ) {
		$value = $GLOBALS['evently_preview_overrides'][ $key ];
		return ( '' === $value || null === $value ) ? $default : $value;
	}

	if ( null === $settings ) {
		$settings = get_option( 'evently_settings', array() );
		if ( ! is_array( $settings ) ) {
			$settings = array();
		}
	}

	if ( ! array_key_exists( $key, $settings ) ) {
		return $default;
	}

	// The Theme Settings form (inc/admin/theme-settings.php) always writes every
	// registered field on save, including ones left blank, as ''. Without this,
	// saving the settings screen even once — with e.g. the image fields left
	// empty — permanently blanks the homepage hero/featured images and copy
	// instead of falling back to the theme's bundled demo content.
	$value = $settings[ $key ];
	return ( '' === $value || null === $value ) ? $default : $value;
}

/**
 * Whether single `mep_events` pages use the plugin's own details design
 * (no Evently CSS/JS skin on plugin markup).
 *
 * Evently retired its `mage-event/single-events.php` override — without that
 * file, "Theme" mode can only mean restyling the plugin's HTML with
 * single-event.css, which changes the plugin layout. Until a real
 * mage-event override exists again, always keep the plugin design intact.
 *
 * When a child/theme ships `mage-event/single-events.php`, the Theme Settings
 * → Event details page control applies again.
 *
 * @return bool
 */
function evently_use_plugin_event_details() {
	$theme_override = trailingslashit( get_stylesheet_directory() ) . 'mage-event/single-events.php';
	if ( ! is_readable( $theme_override ) ) {
		return true;
	}

	return 'plugin' === evently_get_setting( 'single_event_template', 'plugin' );
}

/**
 * Render an inline SVG icon from assets/icons/ with output escaped via wp_kses.
 *
 * Icons are trusted theme assets (not user input), but we still run them
 * through a strict SVG allow-list rather than echoing raw file contents,
 * so a compromised/edited icon file can't inject arbitrary script.
 *
 * @param string $name  Icon file name without extension, e.g. "search".
 * @param array  $attrs Optional extra attributes merged onto the root <svg>, e.g. ['class' => 'icon-lg'].
 * @return void
 */
function evently_icon( $name, $attrs = array() ) {
	echo evently_get_icon( $name, $attrs ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside evently_get_icon() via wp_kses.
}

/**
 * Same as evently_icon() but returns the markup instead of echoing it.
 *
 * @param string $name  Icon file name without extension.
 * @param array  $attrs Optional extra attributes merged onto the root <svg>.
 * @return string Escaped SVG markup, or an empty string if the icon doesn't exist.
 */
function evently_get_icon( $name, $attrs = array() ) {
	$name = sanitize_file_name( $name );
	$path = EVENTLY_DIR . 'assets/icons/' . $name . '.svg';

	if ( ! is_readable( $path ) ) {
		return '';
	}

	$svg = file_get_contents( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- local theme asset, not a remote/user-supplied path.
	if ( false === $svg ) {
		return '';
	}

	if ( ! empty( $attrs ) ) {
		$attr_string = '';
		foreach ( $attrs as $attr_key => $attr_value ) {
			$attr_string .= ' ' . esc_attr( $attr_key ) . '="' . esc_attr( $attr_value ) . '"';
		}
		$svg = preg_replace( '/<svg/', '<svg' . $attr_string, $svg, 1 );
	}

	$allowed_svg = array(
		'svg'      => array(
			'class'           => true,
			'width'           => true,
			'height'          => true,
			'viewbox'         => true,
			'viewBox'         => true,
			'fill'            => true,
			'stroke'          => true,
			'stroke-width'    => true,
			'stroke-linecap'  => true,
			'stroke-linejoin' => true,
			'aria-hidden'     => true,
			'focusable'       => true,
			'role'            => true,
		),
		'path'     => array(
			'd'               => true,
			'fill'            => true,
			'stroke'          => true,
			'stroke-width'    => true,
			'stroke-linecap'  => true,
			'stroke-linejoin' => true,
		),
		'circle'   => array(
			'cx' => true,
			'cy' => true,
			'r'  => true,
		),
		'line'     => array(
			'x1' => true,
			'y1' => true,
			'x2' => true,
			'y2' => true,
		),
		'rect'     => array(
			'x'      => true,
			'y'      => true,
			'width'  => true,
			'height' => true,
			'rx'     => true,
		),
		'polyline' => array(
			'points' => true,
		),
		'g'        => array(
			'fill' => true,
		),
	);

	return wp_kses( $svg, $allowed_svg );
}

/**
 * Format a price for display using the site's WooCommerce currency settings when
 * available, otherwise a plain "$X" fallback. Never invents a price — callers
 * always pass a real numeric value sourced from the booking adapter/WooCommerce.
 *
 * @param float|int|string $amount
 * @return string Escaped HTML.
 */
function evently_format_price( $amount ) {
	if ( '' === $amount || null === $amount ) {
		return '';
	}

	if ( evently_has_woocommerce() && function_exists( 'wc_price' ) ) {
		return wc_price( $amount ); // wc_price() already escapes.
	}

	return esc_html( '$' . number_format_i18n( (float) $amount, 2 ) );
}

/**
 * Truncate text to a word count without cutting mid-word, appending an ellipsis.
 *
 * @param string $text
 * @param int    $words
 * @return string Plain text, not yet escaped by design (callers decide context: esc_html vs wp_kses_post).
 */
function evently_trim_words( $text, $words = 20 ) {
	return wp_trim_words( wp_strip_all_tags( $text ), $words, '…' );
}

/**
 * Whether the homepage should be handed over to an admin-built page (Gutenberg
 * blocks or Elementor) instead of the theme's built-in 14-section demo layout.
 *
 * Deliberately conservative: a fresh/default install (`show_on_front` still
 * "posts", or a static front page picked in Settings → Reading that has no
 * real content yet) always falls through to the built-in homepage — nothing
 * changes for anyone who hasn't gone through Evently Setup's "Homepage Editor"
 * step (inc/admin/setup-wizard.php) and actually built something.
 *
 * @return bool
 */
function evently_homepage_uses_custom_builder() {
	if ( 'page' !== get_option( 'show_on_front' ) ) {
		return false;
	}

	$front_id = (int) get_option( 'page_on_front' );
	if ( ! $front_id ) {
		return false;
	}

	$front_page = get_post( $front_id );
	if ( ! $front_page || 'publish' !== $front_page->post_status ) {
		return false;
	}

	$has_content   = '' !== trim( (string) $front_page->post_content );
	$has_elementor = 'builder' === get_post_meta( $front_id, '_elementor_edit_mode', true );

	return $has_content || $has_elementor;
}

/**
 * Safe wrapper around get_template_part() that lets template-parts receive
 * an associative array of local variables without polluting global scope.
 *
 * @param string $slug Template part slug, e.g. 'event/card'.
 * @param string $name Optional template part name.
 * @param array  $args Variables made available to the part as $args.
 * @return void
 */
function evently_template_part( $slug, $name = '', $args = array() ) {
	get_template_part( $slug, $name, $args );
}
