<?php
/**
 * Final CTA (brief §11) — the strongest visual close of the homepage.
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$evently_cta_title       = $args['title'] ?? evently_get_setting( 'final_cta_title', __( 'Your next great experience is waiting.', 'evently' ) );
$evently_cta_subtitle    = $args['subtitle'] ?? evently_get_setting( 'final_cta_subtitle', __( 'Discover thousands of events, experiences and unforgettable moments.', 'evently' ) );
$evently_cta_button_text = $args['button_text'] ?? evently_get_setting( 'final_cta_button_text', __( 'Explore Events', 'evently' ) );
$evently_cta_button_url  = ! empty( $args['button_url']['url'] ) ? $args['button_url']['url'] : evently_get_setting( 'final_cta_button_url', evently_get_events_page_url() );
?>
<section class="cta-section">
	<div class="cta-glow" aria-hidden="true"></div>
	<div class="cta-inner">
		<h2 class="cta-title"><?php echo esc_html( $evently_cta_title ); ?></h2>
		<p class="cta-sub"><?php echo esc_html( $evently_cta_subtitle ); ?></p>
		<?php
		evently_button(
			array(
				'text'    => $evently_cta_button_text,
				'url'     => $evently_cta_button_url,
				'variant' => 'white',
			)
		);
		?>
	</div>
</section>
