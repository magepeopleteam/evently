<?php
/**
 * Evently Smart Event Search (brief §13) — reusable on the homepage hero
 * and (in compact form) at the top of the Event Archive. Submits a real GET
 * request to the events archive; Phase 4's mage-event/event-archive.php
 * override reads these same query vars to run the actual filtered query, so
 * this isn't a decorative form — it's the front door to real search.
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$evently_search_action = evently_get_events_page_url();

$evently_categories = array();
if ( evently_has_booking_plugin() && taxonomy_exists( 'mep_cat' ) ) {
	$evently_terms = get_terms( array( 'taxonomy' => 'mep_cat', 'hide_empty' => false ) );
	if ( ! is_wp_error( $evently_terms ) ) {
		foreach ( $evently_terms as $evently_term ) {
			$evently_categories[ $evently_term->slug ] = $evently_term->name;
		}
	}
}
if ( empty( $evently_categories ) ) {
	foreach ( evently_demo_categories() as $evently_cat ) {
		$evently_categories[ sanitize_title( $evently_cat['label'] ) ] = $evently_cat['label'];
	}
}
?>
<div class="search-wrap">
	<form class="search-bar evently-overflow-x" role="search" method="get" action="<?php echo esc_url( $evently_search_action ); ?>">
		<div class="search-field">
			<label class="evently-field-label" for="evently-search-what"><?php esc_html_e( 'What', 'evently' ); ?></label>
			<input type="text" id="evently-search-what" name="s" placeholder="<?php esc_attr_e( 'Search events, artists, venues…', 'evently' ); ?>" />
		</div>
		<div class="search-field">
			<label class="evently-field-label" for="evently-search-where"><?php esc_html_e( 'Where', 'evently' ); ?></label>
			<input type="text" id="evently-search-where" name="city" placeholder="<?php esc_attr_e( 'Dhaka', 'evently' ); ?>" />
		</div>
		<div class="search-field">
			<label class="evently-field-label" for="evently-search-when"><?php esc_html_e( 'When', 'evently' ); ?></label>
			<?php /* The booking plugin's query API only filters by year + upcoming/today/expired status, not an arbitrary date — mage-event/event-archive.php reads this as `evently_date` and narrows to the matching year, an honest best-effort rather than a fabricated exact-day filter. */ ?>
			<input type="date" id="evently-search-when" name="evently_date" placeholder="<?php esc_attr_e( 'Any date', 'evently' ); ?>" />
		</div>
		<div class="search-field">
			<label class="evently-field-label" for="evently-search-category"><?php esc_html_e( 'Category', 'evently' ); ?></label>
			<select id="evently-search-category" name="mep_cat">
				<option value=""><?php esc_html_e( 'All events', 'evently' ); ?></option>
				<?php foreach ( $evently_categories as $evently_slug => $evently_name ) : ?>
					<option value="<?php echo esc_attr( $evently_slug ); ?>"><?php echo esc_html( $evently_name ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="search-submit">
			<button type="submit" class="btn" aria-label="<?php esc_attr_e( 'Search events', 'evently' ); ?>">
				<?php esc_html_e( 'Search', 'evently' ); ?>
			</button>
		</div>
	</form>

	<form class="search-bar-mobile" role="search" method="get" action="<?php echo esc_url( $evently_search_action ); ?>">
		<label class="screen-reader-text" for="evently-search-mobile"><?php esc_html_e( 'Search events', 'evently' ); ?></label>
		<input type="text" id="evently-search-mobile" class="evently-input" name="s" placeholder="<?php esc_attr_e( 'Search events…', 'evently' ); ?>" />
		<button type="submit" class="btn btn--accent"><?php esc_html_e( 'Search', 'evently' ); ?></button>
	</form>
</div>
