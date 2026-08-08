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
	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			array( 'label' => __( 'Content', 'evently' ) )
		);

		$this->add_control(
			'title',
			array(
				'label'   => __( 'Title', 'evently' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => evently_get_setting( 'final_cta_title', __( 'Your next great experience is waiting.', 'evently' ) ),
			)
		);
		$this->add_control(
			'subtitle',
			array(
				'label'   => __( 'Subtitle', 'evently' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => evently_get_setting( 'final_cta_subtitle', __( 'Discover thousands of events, experiences and unforgettable moments.', 'evently' ) ),
			)
		);
		$this->add_control(
			'button_text',
			array(
				'label'   => __( 'Button text', 'evently' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => evently_get_setting( 'final_cta_button_text', __( 'Explore Events', 'evently' ) ),
			)
		);
		$this->add_control(
			'button_url',
			array(
				'label'       => __( 'Button link', 'evently' ),
				'type'        => \Elementor\Controls_Manager::URL,
				'default'     => array( 'url' => evently_get_setting( 'final_cta_button_url', '' ) ),
				'placeholder' => __( 'Leave empty to link to the Events page', 'evently' ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function render() {
		evently_template_part( 'template-parts/home/final-cta', '', $this->get_settings_for_display() );
	}
}
