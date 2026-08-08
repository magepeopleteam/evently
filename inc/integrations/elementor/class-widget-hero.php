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
				'default' => __( 'Discover Your Next Experience', 'evently' ),
			)
		);
		$this->add_control(
			'heading_line_1',
			array(
				'label'   => __( 'Heading — line 1', 'evently' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Events worth', 'evently' ),
			)
		);
		$this->add_control(
			'heading_line_2',
			array(
				'label'   => __( 'Heading — line 2', 'evently' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'remembering.', 'evently' ),
			)
		);
		$this->add_control(
			'subhead',
			array(
				'label'   => __( 'Subheading', 'evently' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => __( 'Find concerts, conferences, festivals and experiences happening around you.', 'evently' ),
			)
		);
		$this->add_control(
			'hero_image',
			array(
				'label'   => __( 'Hero image', 'evently' ),
				'type'    => \Elementor\Controls_Manager::MEDIA,
				'default' => array( 'url' => evently_get_setting( 'hero_image', evently_demo_image_url( array( 'image_file' => 'hero-concert-crowd.jpg' ) ) ) ),
			)
		);
		$this->add_control(
			'live_note',
			array(
				'label'   => __( 'Live note', 'evently' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => evently_get_setting( 'hero_live_note', __( '2,840 tickets sold today', 'evently' ) ),
			)
		);

		$this->add_control(
			'primary_button_text',
			array(
				'label'   => __( 'Primary button — text', 'evently' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Explore Events', 'evently' ),
			)
		);
		$this->add_control(
			'primary_button_url',
			array(
				'label'       => __( 'Primary button — link', 'evently' ),
				'type'        => \Elementor\Controls_Manager::URL,
				'default'     => array( 'url' => '' ),
				'placeholder' => __( 'Leave empty to link to the Events page', 'evently' ),
			)
		);
		$this->add_control(
			'secondary_button_text',
			array(
				'label'   => __( 'Secondary button — text', 'evently' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Browse Categories', 'evently' ),
			)
		);
		$this->add_control(
			'secondary_button_url',
			array(
				'label'       => __( 'Secondary button — link', 'evently' ),
				'type'        => \Elementor\Controls_Manager::URL,
				'default'     => array( 'url' => '#evently-categories' ),
				'placeholder' => '#evently-categories',
			)
		);

		$this->add_control(
			'spotlight_heading',
			array(
				'label'     => __( 'Spotlight card', 'evently' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);
		$evently_spotlight = evently_demo_events()[0];
		$this->add_control(
			'spotlight_title',
			array(
				'label'   => __( 'Spotlight — event title', 'evently' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => $evently_spotlight['title'],
			)
		);
		$this->add_control(
			'spotlight_date',
			array(
				'label'   => __( 'Spotlight — date', 'evently' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => $evently_spotlight['date_full'],
			)
		);
		$this->add_control(
			'spotlight_location',
			array(
				'label'   => __( 'Spotlight — location', 'evently' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => $evently_spotlight['location'],
			)
		);
		$this->add_control(
			'spotlight_price',
			array(
				'label'   => __( 'Spotlight — price label', 'evently' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => $evently_spotlight['price_label'],
			)
		);
		$this->add_control(
			'spotlight_button_text',
			array(
				'label'   => __( 'Spotlight — button text', 'evently' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Book Now', 'evently' ),
			)
		);
		$this->add_control(
			'spotlight_button_url',
			array(
				'label'       => __( 'Spotlight — button link', 'evently' ),
				'type'        => \Elementor\Controls_Manager::URL,
				'default'     => array( 'url' => '' ),
				'placeholder' => __( 'Leave empty to link to the Events page', 'evently' ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function render() {
		evently_template_part( 'template-parts/home/hero', '', $this->get_settings_for_display() );
	}
}
