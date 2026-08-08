<?php
/**
 * Evently How It Works Elementor widget — renders the exact same template-part as the
 * homepage and its "evently/how-it-works" Gutenberg block (brief §26: no
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
 * Class Evently_Elementor_Widget_How_It_Works
 */
class Evently_Elementor_Widget_How_It_Works extends Evently_Elementor_Widget_Base {

	/**
	 * @return string
	 */
	public function get_name() {
		return 'evently-how-it-works';
	}

	/**
	 * @return string
	 */
	public function get_title() {
		return __( 'Evently How It Works', 'evently' );
	}

	/**
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-time-line';
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
				'default' => __( 'Book experiences in three simple steps.', 'evently' ),
			)
		);

		$evently_repeater = new \Elementor\Repeater();
		$evently_repeater->add_control(
			'label',
			array(
				'label'   => __( 'Title', 'evently' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => '',
			)
		);
		$evently_repeater->add_control(
			'desc',
			array(
				'label'   => __( 'Description', 'evently' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => '',
			)
		);

		$this->add_control(
			'steps',
			array(
				'label'       => __( 'Steps', 'evently' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $evently_repeater->get_controls(),
				'default'     => array(
					array(
						'label' => evently_get_setting( 'step_1_label', __( 'Discover', 'evently' ) ),
						'desc'  => evently_get_setting( 'step_1_desc', __( 'Find an event you love from thousands of curated experiences worldwide.', 'evently' ) ),
					),
					array(
						'label' => evently_get_setting( 'step_2_label', __( 'Book', 'evently' ) ),
						'desc'  => evently_get_setting( 'step_2_desc', __( 'Choose your ticket and pay securely. Get instant confirmation.', 'evently' ) ),
					),
					array(
						'label' => evently_get_setting( 'step_3_label', __( 'Enjoy', 'evently' ) ),
						'desc'  => evently_get_setting( 'step_3_desc', __( 'Receive your digital ticket and enjoy the event worry-free.', 'evently' ) ),
					),
				),
				'title_field' => '{{{ label }}}',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function render() {
		evently_template_part( 'template-parts/home/how-it-works', '', $this->get_settings_for_display() );
	}
}
