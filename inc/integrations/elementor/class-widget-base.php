<?php
/**
 * Shared base class for Evently's Elementor widgets. This file is only
 * ever require'd from inc/integrations/elementor.php, which already
 * confirmed Elementor is loaded — so referencing \Elementor\Widget_Base
 * here is always safe.
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Evently_Elementor_Widget_Base
 */
abstract class Evently_Elementor_Widget_Base extends \Elementor\Widget_Base {

	/**
	 * @return string
	 */
	public function get_categories() {
		return array( 'evently' );
	}

	/**
	 * @return string[]
	 */
	public function get_keywords() {
		return array( 'evently', 'event' );
	}
}
