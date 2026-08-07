<?php
/**
 * Choose Your Vibe — mood-based pill filter (brief §11). This is an
 * editorial discovery feature, not a core booking concept, so it maps onto
 * the booking plugin's `mep_tag` taxonomy when a tag of the same name
 * exists (e.g. a real "Music" tag), and onto the curated demo dataset's
 * `vibe` field otherwise. Client-side switching is handled by
 * assets/js/filters.js as a proper ARIA tabs pattern — no page reload.
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$evently_vibes = array(
	__( 'Music', 'evently' ),
	__( 'Learn', 'evently' ),
	__( 'Business', 'evently' ),
	__( 'Creative', 'evently' ),
	__( 'Sports', 'evently' ),
	__( 'Family', 'evently' ),
	__( 'Food', 'evently' ),
	__( 'Travel', 'evently' ),
);

$evently_demo_pool = evently_demo_events();
$evently_panels     = array();

foreach ( $evently_vibes as $evently_vibe ) {
	$evently_events = array();

	if ( evently_has_booking_plugin() && class_exists( 'Evently_Booking_Adapter' ) && taxonomy_exists( 'mep_tag' ) ) {
		$evently_tag_term = get_term_by( 'slug', sanitize_title( $evently_vibe ), 'mep_tag' );
		if ( $evently_tag_term ) {
			$evently_events = Evently_Booking_Adapter::get_events_for_cards( 4, 'tag', array( 'tag' => $evently_tag_term->slug ) );
		}
	}

	if ( empty( $evently_events ) ) {
		$evently_matching_demo = array_values(
			array_filter(
				$evently_demo_pool,
				static function ( $evently_event ) use ( $evently_vibe ) {
					return in_array( $evently_vibe, $evently_event['vibe'], true );
				}
			)
		);
		$evently_events = array_map( 'evently_demo_event_to_card', array_slice( $evently_matching_demo, 0, 4 ) );
	}

	$evently_panels[ $evently_vibe ] = $evently_events;
}

$evently_active_vibe = $evently_vibes[0];
?>
<section class="evently-section evently-section--soft" data-evently-vibe-filter>
	<div class="evently-container">
		<div class="evently-section-head evently-section-head--center">
			<h2><?php esc_html_e( 'Choose your vibe', 'evently' ); ?></h2>
			<p><?php esc_html_e( 'Find an experience that matches your mood.', 'evently' ); ?></p>
		</div>

		<div class="vibe-filters" role="tablist" aria-label="<?php esc_attr_e( 'Filter events by vibe', 'evently' ); ?>">
			<?php foreach ( $evently_vibes as $evently_vibe ) : ?>
				<button
					type="button"
					role="tab"
					class="evently-pill-filter<?php echo $evently_vibe === $evently_active_vibe ? ' is-active' : ''; ?>"
					id="evently-vibe-tab-<?php echo esc_attr( sanitize_title( $evently_vibe ) ); ?>"
					aria-selected="<?php echo $evently_vibe === $evently_active_vibe ? 'true' : 'false'; ?>"
					aria-controls="evently-vibe-panel-<?php echo esc_attr( sanitize_title( $evently_vibe ) ); ?>"
					data-vibe="<?php echo esc_attr( sanitize_title( $evently_vibe ) ); ?>"
				>
					<?php echo esc_html( $evently_vibe ); ?>
				</button>
			<?php endforeach; ?>
		</div>

		<?php foreach ( $evently_panels as $evently_vibe => $evently_events ) : ?>
			<div
				class="vibe-events"
				id="evently-vibe-panel-<?php echo esc_attr( sanitize_title( $evently_vibe ) ); ?>"
				role="tabpanel"
				aria-labelledby="evently-vibe-tab-<?php echo esc_attr( sanitize_title( $evently_vibe ) ); ?>"
				<?php echo $evently_vibe === $evently_active_vibe ? '' : 'hidden'; ?>
			>
				<?php evently_event_grid( $evently_events, 'default', 'grid-4' ); ?>
			</div>
		<?php endforeach; ?>
	</div>
</section>
