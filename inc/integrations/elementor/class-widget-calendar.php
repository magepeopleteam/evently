<?php
/**
 * Evently Event Calendar Elementor widget — renders the exact same template-part as the
 * homepage and its "evently/calendar" Gutenberg block (brief §26: no
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
 * Class Evently_Elementor_Widget_Calendar
 */
class Evently_Elementor_Widget_Calendar extends Evently_Elementor_Widget_Base {

	/**
	 * @return string
	 */
	public function get_name() {
		return 'evently-calendar';
	}

	/**
	 * @return string
	 */
	public function get_title() {
		return __( 'Evently Event Calendar', 'evently' );
	}

	/**
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-calendar';
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
				'default' => __( "What's happening this month", 'evently' ),
			)
		);
		$this->add_control(
			'month_label',
			array(
				'label'       => __( 'Month label', 'evently' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => evently_get_setting( 'calendar_month_label', __( 'August 2026', 'evently' ) ),
				'description' => __( 'Only the month label is editable here — the day-by-day event list shown is curated demo data.', 'evently' ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function render() {
		evently_template_part( 'template-parts/home/calendar', '', $this->get_settings_for_display() );
	}
}
