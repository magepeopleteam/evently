<?php
/**
 * "Evently Ticket Preview" pattern (brief §25). The QR block inside is
 * decorative marketing artwork, not a real scannable ticket — see
 * template-parts/home/digital-ticket.php's docblock.
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'evently_pattern_content_ticket_preview' ) ) {
	/**
	 * @return string
	 */
	function evently_pattern_content_ticket_preview() {
		return evently_html_block( evently_capture_template_part( 'template-parts/home/digital-ticket' ) );
	}
}
