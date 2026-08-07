<?php
/**
 * Evently Organizer CTA Elementor widget — renders the exact same template-part as the
 * homepage and its "evently/organizer-cta" Gutenberg block (brief §26: no
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
 * Class Evently_Elementor_Widget_Organizer_Cta
 */
class Evently_Elementor_Widget_Organizer_Cta extends Evently_Elementor_Widget_Base {

	/**
	 * @return string
	 */
	public function get_name() {
		return 'evently-organizer-cta';
	}

	/**
	 * @return string
	 */
	public function get_title() {
		return __( 'Evently Organizer CTA', 'evently' );
	}

	/**
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-dashboard';
	}

	/**
	 * @return void
	 */
	protected function render() {
		evently_template_part( 'template-parts/home/organizer-cta' );
	}
}
