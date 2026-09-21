<?php
/**
 * Title: Event Calendar
 * Slug: evently/event-calendar
 * Categories: evently
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'evently_pattern_content_calendar' ) ) {
	/**
	 * @return string
	 */
	function evently_pattern_content_calendar() {
		return evently_html_block( evently_capture_template_part( 'template-parts/home/calendar' ) );
	}
}

