<?php
/**
 * Evently Event Journal Elementor widget — renders the exact same template-part as the
 * homepage and its "evently/event-journal" Gutenberg block (brief §26: no
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
 * Class Evently_Elementor_Widget_Event_Journal
 */
class Evently_Elementor_Widget_Event_Journal extends Evently_Elementor_Widget_Base {

	/**
	 * @return string
	 */
	public function get_name() {
		return 'evently-event-journal';
	}

	/**
	 * @return string
	 */
	public function get_title() {
		return __( 'Evently Event Journal', 'evently' );
	}

	/**
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-post-list';
	}

	/**
	 * @return void
	 */
	protected function render() {
		evently_template_part( 'template-parts/home/event-journal' );
	}
}
