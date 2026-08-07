<?php
/**
 * Evently Featured Event Elementor widget — renders the exact same template-part as the
 * homepage and its "evently/featured-event" Gutenberg block (brief §26: no
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
 * Class Evently_Elementor_Widget_Featured_Event
 */
class Evently_Elementor_Widget_Featured_Event extends Evently_Elementor_Widget_Base {

	/**
	 * @return string
	 */
	public function get_name() {
		return 'evently-featured-event';
	}

	/**
	 * @return string
	 */
	public function get_title() {
		return __( 'Evently Featured Event', 'evently' );
	}

	/**
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-star';
	}

	/**
	 * @return void
	 */
	protected function render() {
		evently_template_part( 'template-parts/home/featured-event' );
	}
}
