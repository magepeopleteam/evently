<?php
/**
 * Evently Categories Elementor widget — renders the same template-part as
 * the homepage and the "Evently Categories" Gutenberg pattern.
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Evently_Elementor_Widget_Categories
 */
class Evently_Elementor_Widget_Categories extends Evently_Elementor_Widget_Base {

	/**
	 * @return string
	 */
	public function get_name() {
		return 'evently-categories';
	}

	/**
	 * @return string
	 */
	public function get_title() {
		return __( 'Evently Categories', 'evently' );
	}

	/**
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-gallery-grid';
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
				'default' => __( 'Explore by experience', 'evently' ),
			)
		);
		$this->add_control(
			'subhead',
			array(
				'label'   => __( 'Subheading', 'evently' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( "Find something you'll love.", 'evently' ),
			)
		);

		$evently_repeater = new \Elementor\Repeater();
		$evently_repeater->add_control(
			'label',
			array(
				'label'   => __( 'Label', 'evently' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => '',
			)
		);
		$evently_repeater->add_control(
			'image',
			array(
				'label'   => __( 'Image', 'evently' ),
				'type'    => \Elementor\Controls_Manager::MEDIA,
				'default' => array( 'url' => '' ),
			)
		);
		$evently_repeater->add_control(
			'wide',
			array(
				'label'        => __( 'Wide tile', 'evently' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'evently' ),
				'label_off'    => __( 'No', 'evently' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$evently_defaults = array();
		foreach ( evently_demo_categories() as $evently_cat ) {
			$evently_defaults[] = array(
				'label' => $evently_cat['label'],
				'image' => array( 'url' => evently_demo_image_url( $evently_cat ) ),
				'wide'  => ! empty( $evently_cat['wide'] ) ? 'yes' : '',
			);
		}

		$this->add_control(
			'items',
			array(
				'label'       => __( 'Categories', 'evently' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $evently_repeater->get_controls(),
				'default'     => $evently_defaults,
				'title_field' => '{{{ label }}}',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function render() {
		evently_template_part( 'template-parts/home/categories', '', $this->get_settings_for_display() );
	}
}
