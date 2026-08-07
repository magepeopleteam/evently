<?php
/**
 * "Evently Event Grid" pattern — a real, adapter-backed grid of upcoming
 * events (brief §25). Reuses the homepage's Trending Events template-part,
 * which already sources live data via evently_get_home_events().
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'evently_pattern_content_event_grid' ) ) {
	/**
	 * @return string
	 */
	function evently_pattern_content_event_grid() {
		return evently_html_block( evently_capture_template_part( 'template-parts/home/trending-events' ) );
	}
}
