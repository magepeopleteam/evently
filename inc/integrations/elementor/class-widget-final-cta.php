<?php
/**
 * Evently Final CTA Elementor widget — renders the exact same template-part as the
 * homepage and its "evently/final-cta" Gutenberg block (brief §26: no
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
 * Class Evently_Elementor_Widget_Final_Cta
 */
class Evently_Elementor_Widget_Final_Cta extends Evently_Elementor_Widget_Base {

	/**
	 * @return string
	 */
	public function get_name() {
		return 'evently-final-cta';
	}

	/**
	 * @return string
	 */
	public function get_title() {
		return __( 'Evently Final CTA', 'evently' );
	}

	/**
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-call-to-action';
	}

	/**
	 * @return void
	 */
	protected function render() {
		evently_template_part( 'template-parts/home/final-cta' );
	}
}
