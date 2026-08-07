<?php
/**
 * Event Archive filter form — shared between the desktop sidebar and the
 * mobile drawer (brief §15). A plain GET form so it works with JS off;
 * assets/js/filters.js only adds auto-submit-on-change as an enhancement.
 *
 * @param array $args {
 *     @type string   $search, $cat, $org, $city, $status Current values.
 *     @type WP_Term[] $categories, $organizers
 *     @type string[]  $cities
 * }
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$evently_search     = isset( $args['search'] ) ? $args['search'] : '';
$evently_cat        = isset( $args['cat'] ) ? $args['cat'] : '';
$evently_org        = isset( $args['org'] ) ? $args['org'] : '';
$evently_city       = isset( $args['city'] ) ? $args['city'] : '';
$evently_categories = isset( $args['categories'] ) ? $args['categories'] : array();
$evently_organizers  = isset( $args['organizers'] ) ? $args['organizers'] : array();
$evently_cities      = isset( $args['cities'] ) ? $args['cities'] : array();
?>
<form method="get" class="evently-filters-form" data-evently-query-form>
	<div class="evently-filters-form__field">
		<label class="evently-field-label" for="evently-filter-search"><?php esc_html_e( 'Search', 'evently' ); ?></label>
		<input type="text" id="evently-filter-search" name="s" class="evently-input" value="<?php echo esc_attr( $evently_search ); ?>" placeholder="<?php esc_attr_e( 'Event name…', 'evently' ); ?>" />
	</div>

	<?php if ( ! empty( $evently_categories ) ) : ?>
		<div class="evently-filters-form__field">
			<label class="evently-field-label" for="evently-filter-cat"><?php esc_html_e( 'Category', 'evently' ); ?></label>
			<select id="evently-filter-cat" name="mep_cat" class="evently-select">
				<option value=""><?php esc_html_e( 'All categories', 'evently' ); ?></option>
				<?php foreach ( $evently_categories as $evently_term ) : ?>
					<option value="<?php echo esc_attr( $evently_term->slug ); ?>" <?php selected( $evently_cat, $evently_term->slug ); ?>>
						<?php echo esc_html( $evently_term->name ); ?> (<?php echo esc_html( $evently_term->count ); ?>)
					</option>
				<?php endforeach; ?>
			</select>
		</div>
	<?php endif; ?>

	<?php if ( ! empty( $evently_organizers ) ) : ?>
		<div class="evently-filters-form__field">
			<label class="evently-field-label" for="evently-filter-org"><?php esc_html_e( 'Organizer / Venue', 'evently' ); ?></label>
			<select id="evently-filter-org" name="mep_org" class="evently-select">
				<option value=""><?php esc_html_e( 'All organizers', 'evently' ); ?></option>
				<?php foreach ( $evently_organizers as $evently_term ) : ?>
					<option value="<?php echo esc_attr( $evently_term->slug ); ?>" <?php selected( $evently_org, $evently_term->slug ); ?>>
						<?php echo esc_html( $evently_term->name ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</div>
	<?php endif; ?>

	<?php if ( ! empty( $evently_cities ) ) : ?>
		<div class="evently-filters-form__field">
			<label class="evently-field-label" for="evently-filter-city"><?php esc_html_e( 'City', 'evently' ); ?></label>
			<select id="evently-filter-city" name="city" class="evently-select">
				<option value=""><?php esc_html_e( 'All cities', 'evently' ); ?></option>
				<?php foreach ( $evently_cities as $evently_city_option ) : ?>
					<option value="<?php echo esc_attr( $evently_city_option ); ?>" <?php selected( $evently_city, $evently_city_option ); ?>>
						<?php echo esc_html( $evently_city_option ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</div>
	<?php endif; ?>

	<div class="evently-filters-form__actions">
		<button type="submit" class="btn btn--primary btn--block"><?php esc_html_e( 'Apply Filters', 'evently' ); ?></button>
		<?php if ( $evently_search || $evently_cat || $evently_org || $evently_city ) : ?>
			<a href="<?php echo esc_url( evently_get_events_page_url() ); ?>" class="evently-filters-form__clear">
				<?php esc_html_e( 'Clear all', 'evently' ); ?>
			</a>
		<?php endif; ?>
	</div>
</form>
