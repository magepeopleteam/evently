<?php
/**
 * Evently Event Search Elementor widget — the real, functional search bar,
 * identical to the homepage's and the "Evently Event Search" pattern's.
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Evently_Elementor_Widget_Event_Search
 */
class Evently_Elementor_Widget_Event_Search extends Evently_Elementor_Widget_Base {

	/**
	 * @return string
	 */
	public function get_name() {
		return 'evently-event-search';
	}

	/**
	 * @return string
	 */
	public function get_title() {
		return __( 'Evently Event Search', 'evently' );
	}

	/**
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-search';
	}

	/**
	 * @return void
	 */
	protected function render() {
		evently_template_part( 'template-parts/home/search-bar' );
	}
}
