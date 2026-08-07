<?php
/**
 * "Evently CTA" pattern (brief §25).
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'evently_pattern_content_final_cta' ) ) {
	/**
	 * @return string
	 */
	function evently_pattern_content_final_cta() {
		return evently_html_block( evently_capture_template_part( 'template-parts/home/final-cta' ) );
	}
}
