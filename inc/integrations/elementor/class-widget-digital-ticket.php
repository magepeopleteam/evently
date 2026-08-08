<?php
/**
 * Evently Digital Ticket Elementor widget — renders the exact same template-part as the
 * homepage and its "evently/digital-ticket" Gutenberg block (brief §26: no
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
 * Class Evently_Elementor_Widget_Digital_Ticket
 */
class Evently_Elementor_Widget_Digital_Ticket extends Evently_Elementor_Widget_Base {

	/**
	 * @return string
	 */
	public function get_name() {
		return 'evently-digital-ticket';
	}

	/**
	 * @return string
	 */
	public function get_title() {
		return __( 'Evently Digital Ticket', 'evently' );
	}

	/**
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-ticket';
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
			'heading_line_1',
			array(
				'label'   => __( 'Heading — line 1', 'evently' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Your ticket.', 'evently' ),
			)
		);
		$this->add_control(
			'heading_line_2',
			array(
				'label'   => __( 'Heading — line 2', 'evently' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Your experience.', 'evently' ),
			)
		);
		$this->add_control(
			'subhead',
			array(
				'label'   => __( 'Subheading', 'evently' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => __( 'Everything you need for your next event, right in one beautiful digital ticket.', 'evently' ),
			)
		);
		$this->add_control(
			'button_text',
			array(
				'label'   => __( 'Button text', 'evently' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'View My Tickets', 'evently' ),
			)
		);

		$this->add_control(
			'ticket_heading',
			array(
				'label'     => __( 'Sample ticket', 'evently' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);
		$evently_ticket_event = evently_demo_events()[0];
		$this->add_control(
			'event_title',
			array(
				'label'   => __( 'Event title', 'evently' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => evently_get_setting( 'ticket_event_title', $evently_ticket_event['title'] ),
			)
		);
		$this->add_control(
			'event_date',
			array(
				'label'   => __( 'Event date', 'evently' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => evently_get_setting( 'ticket_event_date', $evently_ticket_event['date_label'] ),
			)
		);
		$this->add_control(
			'event_city',
			array(
				'label'   => __( 'City', 'evently' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => evently_get_setting( 'ticket_event_city', $evently_ticket_event['city'] ),
			)
		);
		$this->add_control(
			'ticket_type',
			array(
				'label'   => __( 'Ticket type', 'evently' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => evently_get_setting( 'ticket_type', __( 'VIP PASS', 'evently' ) ),
			)
		);
		$this->add_control(
			'entry_time',
			array(
				'label'   => __( 'Entry time', 'evently' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => evently_get_setting( 'ticket_entry_time', __( 'ENTRY 06:30 PM', 'evently' ) ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function render() {
		evently_template_part( 'template-parts/home/digital-ticket', '', $this->get_settings_for_display() );
	}
}
