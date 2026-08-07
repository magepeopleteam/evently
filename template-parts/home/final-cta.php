<?php
/**
 * Final CTA (brief §11) — the strongest visual close of the homepage.
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section class="cta-section">
	<div class="cta-glow" aria-hidden="true"></div>
	<div class="cta-inner">
		<h2 class="cta-title"><?php esc_html_e( 'Your next great experience is waiting.', 'evently' ); ?></h2>
		<p class="cta-sub"><?php esc_html_e( 'Discover thousands of events, experiences and unforgettable moments.', 'evently' ); ?></p>
		<?php
		evently_button(
			array(
				'text'    => __( 'Explore Events', 'evently' ),
				'url'     => evently_get_events_page_url(),
				'variant' => 'white',
			)
		);
		?>
	</div>
</section>
