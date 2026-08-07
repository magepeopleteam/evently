<?php
/**
 * Related events — same category first, excluding the current event, padded
 * with other upcoming events if the category has fewer than 3 other members
 * so this section reliably shows 3 events rather than sometimes 0–2 (brief §16).
 *
 * @param array $args { @type int $event_id }
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$evently_event_id = isset( $args['event_id'] ) ? (int) $args['event_id'] : 0;

if ( ! $evently_event_id || ! evently_has_booking_plugin() ) {
	return;
}

$evently_category = '';
$evently_terms     = get_the_terms( $evently_event_id, 'mep_cat' );
if ( ! empty( $evently_terms ) && ! is_wp_error( $evently_terms ) ) {
	$evently_category = $evently_terms[0]->slug;
}

$evently_related = array();
$evently_seen    = array( $evently_event_id );

if ( $evently_category ) {
	$evently_related = Evently_Booking_Adapter::get_events_for_cards( 6, 'related', array( 'cat' => $evently_category ) );
	$evently_related = array_values(
		array_filter(
			$evently_related,
			static function ( $card ) use ( $evently_event_id ) {
				return (int) $card['id'] !== $evently_event_id;
			}
		)
	);
	foreach ( $evently_related as $evently_card ) {
		$evently_seen[] = (int) $evently_card['id'];
	}
}

// A thin category (or no category at all) shouldn't mean "no related events" —
// pad with other upcoming events so every event page can show a full 3.
if ( count( $evently_related ) < 3 ) {
	$evently_pool = Evently_Booking_Adapter::get_events_for_cards( 12, 'related' );
	foreach ( $evently_pool as $evently_card ) {
		if ( count( $evently_related ) >= 3 ) {
			break;
		}
		if ( in_array( (int) $evently_card['id'], $evently_seen, true ) ) {
			continue;
		}
		$evently_related[] = $evently_card;
		$evently_seen[]    = (int) $evently_card['id'];
	}
}

if ( empty( $evently_related ) ) {
	return;
}
?>
<section class="evently-event-section evently-related-events">
	<h2><?php esc_html_e( 'Related events', 'evently' ); ?></h2>
	<?php evently_event_grid( array_slice( $evently_related, 0, 3 ), 'compact', 'grid-3' ); ?>
</section>
