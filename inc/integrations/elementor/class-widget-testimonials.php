<?php
/**
 * Evently Testimonials Elementor widget — renders the exact same template-part as the
 * homepage and its "evently/testimonials" Gutenberg block (brief §26: no
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
 * Class Evently_Elementor_Widget_Testimonials
 */
class Evently_Elementor_Widget_Testimonials extends Evently_Elementor_Widget_Base {

	/**
	 * @return string
	 */
	public function get_name() {
		return 'evently-testimonials';
	}

	/**
	 * @return string
	 */
	public function get_title() {
		return __( 'Evently Testimonials', 'evently' );
	}

	/**
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-testimonial';
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
				'default' => __( 'Loved by people who love great experiences.', 'evently' ),
			)
		);

		$evently_repeater = new \Elementor\Repeater();
		$evently_repeater->add_control(
			'name',
			array(
				'label'   => __( 'Name', 'evently' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => '',
			)
		);
		$evently_repeater->add_control(
			'role',
			array(
				'label'   => __( 'Role', 'evently' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => '',
			)
		);
		$evently_repeater->add_control(
			'text',
			array(
				'label'   => __( 'Quote', 'evently' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => '',
			)
		);
		$evently_repeater->add_control(
			'stars',
			array(
				'label'   => __( 'Stars (1–5)', 'evently' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 5,
				'default' => 5,
			)
		);
		$evently_repeater->add_control(
			'initials',
			array(
				'label'   => __( 'Avatar initials', 'evently' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => '',
			)
		);
		$evently_repeater->add_control(
			'color',
			array(
				'label'   => __( 'Avatar color', 'evently' ),
				'type'    => \Elementor\Controls_Manager::COLOR,
				'default' => '#6C5CE7',
			)
		);

		$this->add_control(
			'items',
			array(
				'label'       => __( 'Testimonials', 'evently' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $evently_repeater->get_controls(),
				'default'     => evently_demo_testimonials(),
				'title_field' => '{{{ name }}}',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function render() {
		evently_template_part( 'template-parts/home/testimonials', '', $this->get_settings_for_display() );
	}
}
