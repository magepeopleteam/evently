<?php
/**
 * Evently Featured Event Elementor widget — renders the exact same template-part as the
 * homepage and its "evently/featured-event" Gutenberg block (brief §26: no
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
 * Class Evently_Elementor_Widget_Featured_Event
 */
class Evently_Elementor_Widget_Featured_Event extends Evently_Elementor_Widget_Base {

	/**
	 * @return string
	 */
	public function get_name() {
		return 'evently-featured-event';
	}

	/**
	 * @return string
	 */
	public function get_title() {
		return __( 'Evently Featured Event', 'evently' );
	}

	/**
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-star';
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
			'eyebrow',
			array(
				'label'   => __( 'Eyebrow', 'evently' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Featured Experience', 'evently' ),
			)
		);
		$this->add_control(
			'title',
			array(
				'label'   => __( 'Title', 'evently' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => evently_get_setting( 'featured_event_title', __( 'Future Music Festival', 'evently' ) ),
			)
		);
		$this->add_control(
			'image',
			array(
				'label'   => __( 'Image', 'evently' ),
				'type'    => \Elementor\Controls_Manager::MEDIA,
				'default' => array( 'url' => evently_get_setting( 'featured_event_image', evently_demo_image_url( array( 'image_file' => 'featured-music-festival.jpg' ) ) ) ),
			)
		);
		$this->add_control(
			'date',
			array(
				'label'   => __( 'Date', 'evently' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => evently_get_setting( 'featured_event_date', __( 'August 24–26, 2026', 'evently' ) ),
			)
		);
		$this->add_control(
			'location',
			array(
				'label'   => __( 'Location', 'evently' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => evently_get_setting( 'featured_event_location', __( 'Dhaka, Bangladesh', 'evently' ) ),
			)
		);
		$this->add_control(
			'note',
			array(
				'label'   => __( 'Attendee note', 'evently' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => evently_get_setting( 'featured_event_note', __( '20,000+ attendees expected', 'evently' ) ),
			)
		);
		$this->add_control(
			'button_text',
			array(
				'label'   => __( 'Button text', 'evently' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Explore Event', 'evently' ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function render() {
		evently_template_part( 'template-parts/home/featured-event', '', $this->get_settings_for_display() );
	}
}
