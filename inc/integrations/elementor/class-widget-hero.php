<?php
/**
 * Evently Hero Elementor widget — renders the exact same template-part as
 * the homepage and the "Evently Hero" Gutenberg pattern (brief §26: no
 * duplicated business logic between Gutenberg and Elementor).
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Evently_Elementor_Widget_Hero
 */
class Evently_Elementor_Widget_Hero extends Evently_Elementor_Widget_Base {

	/**
	 * @return string
	 */
	public function get_name() {
		return 'evently-hero';
	}

	/**
	 * @return string
	 */
	public function get_title() {
		return __( 'Evently Hero', 'evently' );
	}

	/**
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-slider-push';
	}

	/**
	 * @return void
	 */
	protected function render() {
		evently_template_part( 'template-parts/home/hero' );
	}
}
