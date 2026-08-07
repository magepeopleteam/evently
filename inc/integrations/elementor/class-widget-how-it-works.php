<?php
/**
 * Evently How It Works Elementor widget — renders the exact same template-part as the
 * homepage and its "evently/how-it-works" Gutenberg block (brief §26: no
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
 * Class Evently_Elementor_Widget_How_It_Works
 */
class Evently_Elementor_Widget_How_It_Works extends Evently_Elementor_Widget_Base {

	/**
	 * @return string
	 */
	public function get_name() {
		return 'evently-how-it-works';
	}

	/**
	 * @return string
	 */
	public function get_title() {
		return __( 'Evently How It Works', 'evently' );
	}

	/**
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-time-line';
	}

	/**
	 * @return void
	 */
	protected function render() {
		evently_template_part( 'template-parts/home/how-it-works' );
	}
}
