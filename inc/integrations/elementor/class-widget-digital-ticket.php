<?php
/**
 * Evently Digital Ticket Elementor widget — renders the exact same template-part as the
 * homepage and its "evently/digital-ticket" Gutenberg block (brief §26: no
 * duplicated business logic between Gutenberg and Elementor). Content comes
 * entirely from Evently → Theme Settings, same as the homepage — this widget
 * takes no controls of its own.
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Evently_Elementor_Widget_Digital_Ticket
 */
class Evently_Elementor_Widget_Digital_Ticket extends Evently_Elementor_Widget_Base {

	/**
	 * @return string
	 */
	public function get_name() {
		return 'evently-digital-ticket';
	}

	/**
	 * @return string
	 */
	public function get_title() {
		return __( 'Evently Digital Ticket', 'evently' );
	}

	/**
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-ticket';
	}

	/**
	 * @return void
	 */
	protected function render() {
		evently_template_part( 'template-parts/home/digital-ticket' );
	}
}
