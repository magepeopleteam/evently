<?php
/**
 * Evently Trending Events Elementor widget — renders the exact same template-part as the
 * homepage and its "evently/trending-events" Gutenberg block (brief §26: no
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
 * Class Evently_Elementor_Widget_Trending_Events
 */
class Evently_Elementor_Widget_Trending_Events extends Evently_Elementor_Widget_Base {

	/**
	 * @return string
	 */
	public function get_name() {
		return 'evently-trending-events';
	}

	/**
	 * @return string
	 */
	public function get_title() {
		return __( 'Evently Trending Events', 'evently' );
	}

	/**
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-posts-grid';
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
				'default' => __( 'Trending right now', 'evently' ),
			)
		);
		$this->add_control(
			'view_all_text',
			array(
				'label'   => __( '"View all" link text', 'evently' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'View all', 'evently' ),
			)
		);
		$this->add_control(
			'view_all_url',
			array(
				'label'       => __( '"View all" link URL', 'evently' ),
				'type'        => \Elementor\Controls_Manager::URL,
				'default'     => array( 'url' => '' ),
				'placeholder' => __( 'Leave empty to link to the Events page', 'evently' ),
			)
		);
		$this->add_control(
			'count',
			array(
				'label'   => __( 'Number of events', 'evently' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 12,
				'default' => 8,
			)
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function render() {
		evently_template_part( 'template-parts/home/trending-events', '', $this->get_settings_for_display() );
	}
}
