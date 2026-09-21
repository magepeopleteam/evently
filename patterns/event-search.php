<?php
/**
 * Title: Event Search
 * Slug: evently/event-search
 * Categories: evently
 * Description: The real, functional 4-field search bar (brief §13/§25),
 * standalone (no hero).
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'evently_pattern_content_event_search' ) ) {
	/**
	 * @return string
	 */
	function evently_pattern_content_event_search() {
		return evently_html_block( evently_capture_template_part( 'template-parts/home/search-bar' ) );
	}
}

