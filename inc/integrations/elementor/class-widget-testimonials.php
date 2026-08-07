<?php
/**
 * Evently Testimonials Elementor widget — renders the exact same template-part as the
 * homepage and its "evently/testimonials" Gutenberg block (brief §26: no
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
 * Class Evently_Elementor_Widget_Testimonials
 */
class Evently_Elementor_Widget_Testimonials extends Evently_Elementor_Widget_Base {

	/**
	 * @return string
	 */
	public function get_name() {
		return 'evently-testimonials';
	}

	/**
	 * @return string
	 */
	public function get_title() {
		return __( 'Evently Testimonials', 'evently' );
	}

	/**
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-testimonial';
	}

	/**
	 * @return void
	 */
	protected function render() {
		evently_template_part( 'template-parts/home/testimonials' );
	}
}
