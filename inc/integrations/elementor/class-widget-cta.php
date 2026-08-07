<?php
/**
 * Evently CTA Elementor widget — a configurable dark call-to-action band,
 * built from the same button renderer (evently_get_button()) the rest of
 * the theme uses.
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Evently_Elementor_Widget_Cta
 */
class Evently_Elementor_Widget_Cta extends Evently_Elementor_Widget_Base {

	/**
	 * @return string
	 */
	public function get_name() {
		return 'evently-cta';
	}

	/**
	 * @return string
	 */
	public function get_title() {
		return __( 'Evently CTA', 'evently' );
	}

	/**
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-call-to-action';
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
			'title',
			array(
				'label'   => __( 'Title', 'evently' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Your next great experience is waiting.', 'evently' ),
			)
		);

		$this->add_control(
			'description',
			array(
				'label'   => __( 'Description', 'evently' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => __( 'Discover thousands of events, experiences and unforgettable moments.', 'evently' ),
			)
		);

		$this->add_control(
			'button_text',
			array(
				'label'   => __( 'Button label', 'evently' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Explore Events', 'evently' ),
			)
		);

		$this->add_control(
			'button_url',
			array(
				'label'       => __( 'Button link', 'evently' ),
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
		$settings = $this->get_settings_for_display();
		$url      = ! empty( $settings['button_url']['url'] ) ? $settings['button_url']['url'] : evently_get_events_page_url();
		?>
		<section class="cta-section">
			<div class="cta-glow" aria-hidden="true"></div>
			<div class="cta-inner">
				<h2 class="cta-title"><?php echo esc_html( $settings['title'] ); ?></h2>
				<?php if ( ! empty( $settings['description'] ) ) : ?>
					<p class="cta-sub"><?php echo esc_html( $settings['description'] ); ?></p>
				<?php endif; ?>
				<?php
				evently_button(
					array(
						'text'    => $settings['button_text'],
						'url'     => $url,
						'variant' => 'white',
					)
				);
				?>
			</div>
		</section>
		<?php
	}
}
