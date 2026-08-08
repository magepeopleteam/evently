<?php
/**
 * Evently Event Journal Elementor widget — renders the exact same template-part as the
 * homepage and its "evently/event-journal" Gutenberg block (brief §26: no
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
 * Class Evently_Elementor_Widget_Event_Journal
 */
class Evently_Elementor_Widget_Event_Journal extends Evently_Elementor_Widget_Base {

	/**
	 * @return string
	 */
	public function get_name() {
		return 'evently-event-journal';
	}

	/**
	 * @return string
	 */
	public function get_title() {
		return __( 'Evently Event Journal', 'evently' );
	}

	/**
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-post-list';
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
				'default' => __( 'Event Journal', 'evently' ),
			)
		);
		$this->add_control(
			'subhead',
			array(
				'label'   => __( 'Subheading', 'evently' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Ideas, inspiration and stories from the world of events.', 'evently' ),
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
			'count',
			array(
				'label'   => __( 'Number of articles', 'evently' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 3,
				'default' => 3,
			)
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function render() {
		evently_template_part( 'template-parts/home/event-journal', '', $this->get_settings_for_display() );
	}
}
