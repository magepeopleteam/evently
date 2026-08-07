<?php
/**
 * Evently Stats Elementor widget — renders the exact same template-part as the
 * homepage and its "evently/stats" Gutenberg block (brief §26: no
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
 * Class Evently_Elementor_Widget_Stats
 */
class Evently_Elementor_Widget_Stats extends Evently_Elementor_Widget_Base {

	/**
	 * @return string
	 */
	public function get_name() {
		return 'evently-stats';
	}

	/**
	 * @return string
	 */
	public function get_title() {
		return __( 'Evently Stats', 'evently' );
	}

	/**
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-counter';
	}

	/**
	 * @return void
	 */
	protected function render() {
		evently_template_part( 'template-parts/home/stats' );
	}
}
