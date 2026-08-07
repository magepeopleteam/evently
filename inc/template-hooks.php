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
 *   do_action( 'evently_before_booking', int $event_id )
 *   do_action( 'evently_after_booking', int $event_id )
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
 * Booking Manager single template instead of Evently's mage-event override.
 *
 * The plugin's MPWEM_Frontend::load_events_templates() prefers
 * theme/mage-event/single-events.php whenever that file exists. Priority 20
 * runs after that filter and swaps back to the plugin file when requested.
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
