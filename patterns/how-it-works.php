<?php
/**
 * "Evently How It Works" pattern (brief §25).
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
