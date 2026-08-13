<?php
/**
 * Title: Hero
 * Slug: evently/hero
 * Categories: evently
 * Description: Captures the real homepage hero template-part (including
 * its floating search bar) so the pattern always matches the live design
 * (brief §25).
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'evently_pattern_content_hero' ) ) {
	/**
	 * @return string
	 */
	function evently_pattern_content_hero() {
		return evently_html_block( evently_capture_template_part( 'template-parts/home/hero' ) );
	}
}

