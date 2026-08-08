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

$evently_heading       = $args['heading'] ?? __( 'Events happening near you', 'evently' );
$evently_default_city  = $args['default_city'] ?? evently_get_setting( 'default_city', __( 'Dhaka', 'evently' ) );
$evently_view_all_text = $args['view_all_text'] ?? __( 'View all events', 'evently' );
$evently_count         = isset( $args['count'] ) ? (int) $args['count'] : 4;
$evently_events        = evently_get_home_events( $evently_count, 'near-you' );
?>
<section class="evently-section">
	<div class="evently-container">
		<div class="evently-section-head evently-section-head--row">
			<div>
				<h2><?php echo esc_html( $evently_heading ); ?></h2>
				<div class="near-loc">
					<span data-evently-location-label><?php evently_icon( 'pin' ); ?> <?php echo esc_html( $evently_default_city ); ?></span>
					<button type="button" class="btn--pill-outline" data-evently-use-location>
						<?php esc_html_e( 'Use my location', 'evently' ); ?>
					</button>
				</div>
			</div>
			<a class="evently-section-head__link" href="<?php echo esc_url( evently_get_events_page_url() ); ?>">
				<?php echo esc_html( $evently_view_all_text ); ?> <span class="arrow" aria-hidden="true">→</span>
			</a>
		</div>

		<?php evently_event_grid( $evently_events, 'default', 'grid-4' ); ?>
	</div>
</section>
