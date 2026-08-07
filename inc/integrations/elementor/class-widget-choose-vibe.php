<?php
/**
 * Evently Choose Your Vibe Elementor widget — renders the exact same template-part as the
 * homepage and its "evently/choose-vibe" Gutenberg block (brief §26: no
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
 * Class Evently_Elementor_Widget_Choose_Vibe
 */
class Evently_Elementor_Widget_Choose_Vibe extends Evently_Elementor_Widget_Base {

	/**
	 * @return string
	 */
	public function get_name() {
		return 'evently-choose-vibe';
	}

	/**
	 * @return string
	 */
	public function get_title() {
		return __( 'Evently Choose Your Vibe', 'evently' );
	}

	/**
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-filter';
	}

	/**
	 * @return void
	 */
	protected function render() {
		evently_template_part( 'template-parts/home/choose-vibe' );
	}
}
