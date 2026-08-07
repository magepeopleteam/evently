<?php
/**
 * Events Near You (brief §11). "Use my location" is a real (not simulated)
 * browser geolocation request handled by assets/js/search.js — it updates
 * the visible label, it does not pretend to re-run a distance query the
 * booking plugin has no API for (brief §44).
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$evently_default_city = evently_get_setting( 'default_city', __( 'Dhaka', 'evently' ) );
$evently_events        = evently_get_home_events( 4, 'near-you' );
?>
<section class="evently-section">
	<div class="evently-container">
		<div class="evently-section-head evently-section-head--row">
			<div>
				<h2><?php esc_html_e( 'Events happening near you', 'evently' ); ?></h2>
				<div class="near-loc">
					<span data-evently-location-label><?php evently_icon( 'pin' ); ?> <?php echo esc_html( $evently_default_city ); ?></span>
					<button type="button" class="btn--pill-outline" data-evently-use-location>
						<?php esc_html_e( 'Use my location', 'evently' ); ?>
					</button>
				</div>
			</div>
			<a class="evently-section-head__link" href="<?php echo esc_url( evently_get_events_page_url() ); ?>">
				<?php esc_html_e( 'View all events', 'evently' ); ?> <span class="arrow" aria-hidden="true">→</span>
			</a>
		</div>

		<?php evently_event_grid( $evently_events, 'default', 'grid-4' ); ?>
	</div>
</section>
