<?php
/**
 * Evently Near You Elementor widget — renders the exact same template-part as the
 * homepage and its "evently/near-you" Gutenberg block (brief §26: no
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
 * Class Evently_Elementor_Widget_Near_You
 */
class Evently_Elementor_Widget_Near_You extends Evently_Elementor_Widget_Base {

	/**
	 * @return string
	 */
	public function get_name() {
		return 'evently-near-you';
	}

	/**
	 * @return string
	 */
	public function get_title() {
		return __( 'Evently Near You', 'evently' );
	}

	/**
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-map-pin';
	}

	/**
	 * @return void
	 */
	protected function render() {
		evently_template_part( 'template-parts/home/near-you' );
	}
}
