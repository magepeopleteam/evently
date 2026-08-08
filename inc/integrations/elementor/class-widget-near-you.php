<?php
/**
 * Evently Near You Elementor widget — renders the exact same template-part as the
 * homepage and its "evently/near-you" Gutenberg block (brief §26: no
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
 * Class Evently_Elementor_Widget_Near_You
 */
class Evently_Elementor_Widget_Near_You extends Evently_Elementor_Widget_Base {

	/**
	 * @return string
	 */
	public function get_name() {
		return 'evently-near-you';
	}

	/**
	 * @return string
	 */
	public function get_title() {
		return __( 'Evently Near You', 'evently' );
	}

	/**
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-map-pin';
	}

	/**
	 * @return void
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			array( 'label' => __( 'Content', 'evently' ) )
		);

		$this->add_control(
			'heading',
			array(
				'label'   => __( 'Heading', 'evently' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Events happening near you', 'evently' ),
			)
		);
		$this->add_control(
			'default_city',
			array(
				'label'   => __( 'Default city', 'evently' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => evently_get_setting( 'default_city', __( 'Dhaka', 'evently' ) ),
			)
		);
		$this->add_control(
			'view_all_text',
			array(
				'label'   => __( '"View all" link text', 'evently' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'View all events', 'evently' ),
			)
		);
		$this->add_control(
			'count',
			array(
				'label'   => __( 'Number of events', 'evently' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 12,
				'default' => 4,
			)
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function render() {
		evently_template_part( 'template-parts/home/near-you', '', $this->get_settings_for_display() );
	}
}
