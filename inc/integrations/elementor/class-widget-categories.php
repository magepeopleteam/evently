<?php
/**
 * Evently Categories Elementor widget — renders the same template-part as
 * the homepage and the "Evently Categories" Gutenberg pattern.
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Evently_Elementor_Widget_Categories
 */
class Evently_Elementor_Widget_Categories extends Evently_Elementor_Widget_Base {

	/**
	 * @return string
	 */
	public function get_name() {
		return 'evently-categories';
	}

	/**
	 * @return string
	 */
	public function get_title() {
		return __( 'Evently Categories', 'evently' );
	}

	/**
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-gallery-grid';
	}

	/**
	 * @return void
	 */
	protected function render() {
		evently_template_part( 'template-parts/home/categories' );
	}
}
