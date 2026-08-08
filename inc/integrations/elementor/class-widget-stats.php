<?php
/**
 * Evently Stats Elementor widget — renders the exact same template-part as the
 * homepage and its "evently/stats" Gutenberg block (brief §26: no
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
 * Class Evently_Elementor_Widget_Stats
 */
class Evently_Elementor_Widget_Stats extends Evently_Elementor_Widget_Base {

	/**
	 * @return string
	 */
	public function get_name() {
		return 'evently-stats';
	}

	/**
	 * @return string
	 */
	public function get_title() {
		return __( 'Evently Stats', 'evently' );
	}

	/**
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-counter';
	}

	/**
	 * @return void
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			array( 'label' => __( 'Content', 'evently' ) )
		);

		$evently_repeater = new \Elementor\Repeater();
		$evently_repeater->add_control(
			'value',
			array(
				'label'   => __( 'Value', 'evently' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => '',
			)
		);
		$evently_repeater->add_control(
			'label',
			array(
				'label'   => __( 'Label', 'evently' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => '',
			)
		);

		$this->add_control(
			'items',
			array(
				'label'       => __( 'Stats', 'evently' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $evently_repeater->get_controls(),
				'default'     => evently_demo_stats(),
				'title_field' => '{{{ label }}}',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function render() {
		evently_template_part( 'template-parts/home/stats', '', $this->get_settings_for_display() );
	}
}
