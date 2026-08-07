<?php
/**
 * Evently Events Grid Elementor widget — same data source
 * (evently_get_home_events(), backed by Evently_Booking_Adapter when the
 * booking plugin is active) and same card renderer (evently_event_grid())
 * as the homepage and the "Evently Event Grid" Gutenberg pattern.
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Evently_Elementor_Widget_Event_Grid
 */
class Evently_Elementor_Widget_Event_Grid extends Evently_Elementor_Widget_Base {

	/**
	 * @return string
	 */
	public function get_name() {
		return 'evently-event-grid';
	}

	/**
	 * @return string
	 */
	public function get_title() {
		return __( 'Evently Events Grid', 'evently' );
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
			'count',
			array(
				'label'   => __( 'Number of events', 'evently' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 12,
				'default' => 4,
			)
		);

		$this->add_control(
			'variant',
			array(
				'label'   => __( 'Card style', 'evently' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'default',
				'options' => array(
					'default'  => __( 'Default', 'evently' ),
					'featured' => __( 'Featured', 'evently' ),
					'compact'  => __( 'Compact', 'evently' ),
					'list'     => __( 'List', 'evently' ),
				),
			)
		);

		$this->add_control(
			'columns',
			array(
				'label'   => __( 'Columns', 'evently' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'grid-4',
				'options' => array(
					'grid-2' => '2',
					'grid-3' => '3',
					'grid-4' => '4',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$events   = evently_get_home_events( (int) $settings['count'], 'trending' );
		?>
		<section class="evently-section">
			<div class="evently-container">
				<?php if ( ! empty( $settings['heading'] ) ) : ?>
					<div class="evently-section-head">
						<h2><?php echo esc_html( $settings['heading'] ); ?></h2>
					</div>
				<?php endif; ?>
				<?php evently_event_grid( $events, $settings['variant'], $settings['columns'] ); ?>
			</div>
		</section>
		<?php
	}
}
