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

		// A repeater rather than fixed primary/secondary fields — so an admin
		// can add, remove or reorder however many buttons they want from the
		// panel itself ("+ Add Item"), not just edit the text of two fixed
		// slots. Defaults to today's exact 2 buttons (Explore Events primary,
		// Browse Categories secondary) so nothing changes until edited.
		$evently_buttons_repeater = new \Elementor\Repeater();
		$evently_buttons_repeater->add_control(
			'text',
			array(
				'label'   => __( 'Button text', 'evently' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => '',
			)
		);
		$evently_buttons_repeater->add_control(
			'url',
			array(
				'label'       => __( 'Button link', 'evently' ),
				'type'        => \Elementor\Controls_Manager::URL,
				'default'     => array( 'url' => '' ),
				'placeholder' => __( 'Leave empty to link to the Events page', 'evently' ),
			)
		);
		$evently_buttons_repeater->add_control(
			'variant',
			array(
				'label'   => __( 'Style', 'evently' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'primary',
				'options' => array(
					'primary'   => __( 'Primary (solid)', 'evently' ),
					'secondary' => __( 'Secondary (outline)', 'evently' ),
				),
			)
		);

		$this->add_control(
			'buttons',
			array(
				'label'       => __( 'Buttons', 'evently' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $evently_buttons_repeater->get_controls(),
				'default'     => array(
					array(
						'text'    => __( 'Explore Events', 'evently' ),
						'url'     => array( 'url' => '' ),
						'variant' => 'primary',
					),
					array(
						'text'    => __( 'Browse Categories', 'evently' ),
						'url'     => array( 'url' => '#evently-categories' ),
						'variant' => 'secondary',
					),
				),
				'title_field' => '{{{ text }}}',
			)
		);

		// Stat tiles (10K+ Events / 250K+ Tickets Sold / 50+ Cities) — a
		// repeater for the same reason as Buttons above: add/remove/reorder
		// freely instead of fixed slots. Defaults match the 3 of 4
		// evently_home_stats() entries the hero has always shown (it skips
		// index 2 — "Customer Satisfaction" — which is reserved for the
		// full Stats section further down the page).
		$evently_stats_repeater = new \Elementor\Repeater();
		$evently_stats_repeater->add_control(
			'value',
			array(
				'label'   => __( 'Value', 'evently' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => '',
			)
		);
		$evently_stats_repeater->add_control(
			'label',
			array(
				'label'   => __( 'Label', 'evently' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => '',
			)
		);
		$evently_demo_stats_all = evently_demo_stats();
		$this->add_control(
			'stats',
			array(
				'label'       => __( 'Stat tiles', 'evently' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $evently_stats_repeater->get_controls(),
				'default'     => array(
					$evently_demo_stats_all[0],
					$evently_demo_stats_all[1],
					$evently_demo_stats_all[3],
				),
				'title_field' => '{{{ label }}}',
			)
		);

		$this->add_control(
			'show_search',
			array(
				'label'        => __( 'Show search bar', 'evently' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'evently' ),
				'label_off'    => __( 'Hide', 'evently' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before',
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
