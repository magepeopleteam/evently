<?php
/**
 * Title: Testimonials
 * Slug: evently/testimonials
 * Categories: evently
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'evently_pattern_content_testimonials' ) ) {
	/**
	 * @return string
	 */
	function evently_pattern_content_testimonials() {
		return evently_html_block( evently_capture_template_part( 'template-parts/home/testimonials' ) );
	}
}

