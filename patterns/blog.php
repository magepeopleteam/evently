<?php
/**
 * Title: Blog Grid
 * Slug: evently/blog-grid
 * Categories: evently
 * Description: The editorial Event Journal cards (brief §25) — not a
 * standard WordPress blog list (brief §24).
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'evently_pattern_content_blog' ) ) {
	/**
	 * @return string
	 */
	function evently_pattern_content_blog() {
		return evently_html_block( evently_capture_template_part( 'template-parts/home/event-journal' ) );
	}
}

