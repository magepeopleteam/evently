<?php
/**
 * Title: Nearby Events
 * Slug: evently/nearby-events
 * Categories: evently
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'evently_pattern_content_nearby_events' ) ) {
	/**
	 * @return string
	 */
	function evently_pattern_content_nearby_events() {
		return evently_html_block( evently_capture_template_part( 'template-parts/home/near-you' ) );
	}
}

