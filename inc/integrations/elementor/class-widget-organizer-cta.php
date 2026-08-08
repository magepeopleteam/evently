<?php
/**
 * Evently Organizer CTA Elementor widget — renders the exact same template-part as the
 * homepage and its "evently/organizer-cta" Gutenberg block (brief §26: no
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
 * Class Evently_Elementor_Widget_Organizer_Cta
 */
class Evently_Elementor_Widget_Organizer_Cta extends Evently_Elementor_Widget_Base {

	/**
	 * @return string
	 */
	public function get_name() {
		return 'evently-organizer-cta';
	}

	/**
	 * @return string
	 */
	public function get_title() {
		return __( 'Evently Organizer CTA', 'evently' );
	}

	/**
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-dashboard';
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
				'default' => __( 'For Organizers', 'evently' ),
			)
		);
		$this->add_control(
			'heading',
			array(
				'label'   => __( 'Heading', 'evently' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Turn your event into an experience.', 'evently' ),
			)
		);
		$this->add_control(
			'subhead',
			array(
				'label'   => __( 'Subheading', 'evently' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => __( 'Create events, sell tickets, manage attendees and track your performance from one powerful dashboard.', 'evently' ),
			)
		);
		$this->add_control(
			'button_text',
			array(
				'label'   => __( 'Button text', 'evently' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Start Selling', 'evently' ),
			)
		);
		$evently_default_url = evently_get_setting( 'create_event_url', '' );
		if ( empty( $evently_default_url ) ) {
			$evently_default_url = evently_has_booking_plugin() ? admin_url( 'post-new.php?post_type=mep_events' ) : '#organizer';
		}
		$this->add_control(
			'button_url',
			array(
				'label'   => __( 'Button link', 'evently' ),
				'type'    => \Elementor\Controls_Manager::URL,
				'default' => array( 'url' => $evently_default_url ),
			)
		);
		$this->add_control(
			'secondary_button_text',
			array(
				'label'   => __( 'Secondary link text', 'evently' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Explore Organizer Tools', 'evently' ),
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
			'value',
			array(
				'label'   => __( 'Value', 'evently' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => '',
			)
		);
		$evently_repeater->add_control(
			'change',
			array(
				'label'   => __( 'Change', 'evently' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => '',
			)
		);

		$evently_defaults = array();
		foreach ( evently_demo_dashboard_stats() as $evently_stat ) {
			$evently_defaults[] = $evently_stat;
		}

		$this->add_control(
			'dash_stats',
			array(
				'label'       => __( 'Dashboard stats (preview)', 'evently' ),
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
		evently_template_part( 'template-parts/home/organizer-cta', '', $this->get_settings_for_display() );
	}
}
