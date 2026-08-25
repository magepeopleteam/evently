<?php
/**
 * Title: Organizer CTA
 * Slug: evently/organizer-cta
 * Categories: evently
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'evently_pattern_content_organizer' ) ) {
	/**
	 * @return string
	 */
	function evently_pattern_content_organizer() {
		return evently_html_block( evently_capture_template_part( 'template-parts/home/organizer-cta' ) );
	}
}

