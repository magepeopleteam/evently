<?php
/**
 * Evently developer hooks (brief §40).
 *
 * This file intentionally hooks nothing by default for most of these —
 * they exist so child themes and add-ons have a stable place to attach
 * markup. Where Evently itself needs a default behavior on one of its own
 * hooks, that's added here explicitly and documented as such.
 *
 * Hook reference (see docs/hooks.md for full documentation + examples):
 *
 *   do_action( 'evently_before_header' )
 *   do_action( 'evently_after_header' )
 *   do_action( 'evently_before_event_content' )
 *   do_action( 'evently_after_event_content' )
 *   do_action( 'evently_before_event_card', array $event, string $variant )
 *   do_action( 'evently_after_event_card', array $event, string $variant )
 *   do_action( 'evently_before_footer' )
 *   do_action( 'evently_after_footer' )
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Print the skip-to-content link as early in <body> as possible.
 * Hooked to evently_before_header rather than hardcoded in header.php so a
 * child theme can move/replace it without copying the whole header.
 *
 * @return void
 */
function evently_output_skip_link() {
	printf(
		'<a class="skip-link screen-reader-text" href="#evently-main">%s</a>',
		esc_html__( 'Skip to content', 'evently' )
	);
}
add_action( 'evently_before_header', 'evently_output_skip_link' );

/**
 * When Theme Settings → Event details page is "Plugin", force the Event
 * Booking Manager's bundled single-events.php template specifically.
 *
 * Evently no longer ships a `mage-event/single-events.php` override (retired
 * — see docs/booking-integration.md), so MPWEM_Frontend::load_events_templates()
 * already falls through to whatever bundled template the plugin's own
 * settings resolve to before this filter ever runs. Priority 20 runs after
 * that and, only when "Plugin" is explicitly selected here, pins the choice
 * to `templates/single-events.php` regardless of what the plugin's own
 * settings would otherwise have picked.
 *
 * @param string $template Absolute path to the single template.
 * @return string
 */
function evently_maybe_use_plugin_single_event_template( $template ) {
	if ( ! evently_use_plugin_event_details() || ! evently_has_booking_plugin() ) {
		return $template;
	}

	$post = get_queried_object();
	if ( ! $post instanceof WP_Post || 'mep_events' !== $post->post_type ) {
		return $template;
	}

	if ( ! defined( 'MPWEM_PLUGIN_DIR' ) ) {
		return $template;
	}

	$plugin_template = MPWEM_PLUGIN_DIR . '/templates/single-events.php';
	if ( is_readable( $plugin_template ) ) {
		return $plugin_template;
	}

	return $template;
}
add_filter( 'single_template', 'evently_maybe_use_plugin_single_event_template', 20 );

/**
 * AJAX: autocomplete suggestions for the Smart Search / quick-search fields.
 * Public (nopriv) — search is a front-end discovery feature.
 *
 * @return void
 */
function evently_ajax_search_suggest() {
	check_ajax_referer( 'evently_search_suggest', 'nonce' );

	$term  = isset( $_REQUEST['term'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['term'] ) ) : '';
	$limit = isset( $_REQUEST['limit'] ) ? absint( $_REQUEST['limit'] ) : 8;

	if ( ! class_exists( 'Evently_Booking_Adapter' ) ) {
		wp_send_json_success( array( 'suggestions' => array() ) );
	}

	$suggestions = Evently_Booking_Adapter::suggest_events( $term, $limit );
	wp_send_json_success( array( 'suggestions' => $suggestions ) );
}
add_action( 'wp_ajax_evently_search_suggest', 'evently_ajax_search_suggest' );
add_action( 'wp_ajax_nopriv_evently_search_suggest', 'evently_ajax_search_suggest' );
