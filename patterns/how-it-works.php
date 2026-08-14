<?php
/**
 * Title: How It Works
 * Slug: evently/how-it-works
 * Categories: evently
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'evently_pattern_content_how_it_works' ) ) {
	/**
	 * @return string
	 */
	function evently_pattern_content_how_it_works() {
		return evently_html_block( evently_capture_template_part( 'template-parts/home/how-it-works' ) );
	}
}

