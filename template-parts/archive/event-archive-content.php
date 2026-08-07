<?php
/**
 * Shared Event Archive body — search hero, filter sidebar/drawer, toolbar,
 * grid/list, pagination. Used by BOTH:
 *  - page-templates/event-archive.php (the real, primary path — the
 *    booking plugin registers `mep_events` with `has_archive => false`,
 *    so this "browse all events" experience is meant to live on a regular
 *    WP Page, the same way the plugin's own [event-list]/[events_list]
 *    shortcode is normally embedded — see docs/architecture.md), and
 *  - mage-event/event-archive.php (kept for correctness in case a future
 *    plugin version, or the PRO add-on, ever enables has_archive).
 *
 * Reads its own filter state from $_GET so it doesn't need any $args.
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$evently_search = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';
$evently_cat    = isset( $_GET['mep_cat'] ) ? sanitize_text_field( wp_unslash( $_GET['mep_cat'] ) ) : '';
$evently_org    = isset( $_GET['mep_org'] ) ? sanitize_text_field( wp_unslash( $_GET['mep_org'] ) ) : '';
$evently_city   = isset( $_GET['city'] ) ? sanitize_text_field( wp_unslash( $_GET['city'] ) ) : '';
$evently_status = isset( $_GET['status'] ) && in_array( $_GET['status'], array( 'upcoming', 'today', 'expired' ), true ) ? $_GET['status'] : 'upcoming';
$evently_sort   = isset( $_GET['sort'] ) && 'DESC' === $_GET['sort'] ? 'DESC' : 'ASC';
$evently_view   = isset( $_GET['view'] ) && in_array( $_GET['view'], array( 'grid', 'list' ), true ) ? $_GET['view'] : evently_get_setting( 'archive_default_view', 'grid' );
$evently_year   = '';

if ( ! empty( $_GET['evently_date'] ) ) {
	$evently_date_ts = strtotime( sanitize_text_field( wp_unslash( $_GET['evently_date'] ) ) );
	if ( $evently_date_ts ) {
		$evently_year = gmdate( 'Y', $evently_date_ts );
	}
}

$evently_per_page = (int) evently_get_setting( 'archive_columns_per_page', 12 );

$evently_results = Evently_Booking_Adapter::search_events(
	array(
		'search'   => $evently_search,
		'cat'      => $evently_cat,
		'org'      => $evently_org,
		'city'     => $evently_city,
		'year'     => $evently_year,
		'status'   => $evently_status,
		'sort'     => $evently_sort,
		'per_page' => $evently_per_page,
	)
);

$evently_categories = Evently_Booking_Adapter::get_filter_terms( 'mep_cat' );
$evently_organizers  = Evently_Booking_Adapter::get_filter_terms( 'mep_org' );
$evently_cities      = Evently_Booking_Adapter::get_filter_cities();

$evently_filter_args = array(
	'search'     => $evently_search,
	'cat'        => $evently_cat,
	'org'        => $evently_org,
	'city'       => $evently_city,
	'status'     => $evently_status,
	'categories' => $evently_categories,
	'organizers' => $evently_organizers,
	'cities'     => $evently_cities,
);

/**
 * Fires before the Event Archive's main content (search results header,
 * filters, and grid).
 */
do_action( 'evently_before_event_content' );
?>

<div class="evently-archive-hero">
	<div class="evently-container">
		<h1 class="evently-archive-hero__title"><?php esc_html_e( 'All Events', 'evently' ); ?></h1>
		<p class="evently-archive-hero__sub">
			<?php
			printf(
				/* translators: %s: number of events found. */
				esc_html( _n( '%s event found', '%s events found', $evently_results['total'], 'evently' ) ),
				'<strong>' . esc_html( number_format_i18n( $evently_results['total'] ) ) . '</strong>'
			);
			?>
		</p>
		<?php evently_template_part( 'template-parts/home/search-bar' ); ?>
	</div>
</div>

<div class="evently-container evently-archive-layout">
	<button type="button" class="evently-archive-filters-toggle btn btn--secondary" data-evently-modal-trigger="evently-filters-drawer">
		<?php evently_icon( 'chevron-right' ); ?> <?php esc_html_e( 'Filters', 'evently' ); ?>
	</button>

	<aside class="evently-archive-sidebar" id="evently-archive-sidebar" aria-label="<?php esc_attr_e( 'Filter events', 'evently' ); ?>">
		<?php evently_template_part( 'template-parts/archive/filters', '', $evently_filter_args ); ?>
	</aside>

	<div class="evently-archive-main">
		<div class="evently-archive-toolbar">
			<div class="evently-archive-toolbar__status" role="tablist" aria-label="<?php esc_attr_e( 'Event status', 'evently' ); ?>">
				<?php
				$evently_status_tabs = array(
					'upcoming' => __( 'Upcoming', 'evently' ),
					'today'    => __( 'Today', 'evently' ),
					'expired'  => __( 'Past', 'evently' ),
				);
				foreach ( $evently_status_tabs as $evently_tab_key => $evently_tab_label ) :
					$evently_tab_url = add_query_arg( array_merge( $_GET, array( 'status' => $evently_tab_key, 'paged' => false ) ) );
					?>
					<a
						href="<?php echo esc_url( $evently_tab_url ); ?>"
						class="evently-pill-filter<?php echo $evently_tab_key === $evently_status ? ' is-active' : ''; ?>"
						aria-current="<?php echo $evently_tab_key === $evently_status ? 'true' : 'false'; ?>"
					><?php echo esc_html( $evently_tab_label ); ?></a>
				<?php endforeach; ?>
			</div>

			<div class="evently-archive-toolbar__right">
				<label class="screen-reader-text" for="evently-sort"><?php esc_html_e( 'Sort by', 'evently' ); ?></label>
				<select id="evently-sort" class="evently-select evently-archive-sort" data-evently-query-param="sort">
					<option value="ASC" <?php selected( $evently_sort, 'ASC' ); ?>><?php esc_html_e( 'Date: Soonest first', 'evently' ); ?></option>
					<option value="DESC" <?php selected( $evently_sort, 'DESC' ); ?>><?php esc_html_e( 'Date: Latest first', 'evently' ); ?></option>
				</select>

				<div class="evently-view-toggle" role="group" aria-label="<?php esc_attr_e( 'Grid or list view', 'evently' ); ?>">
					<a href="<?php echo esc_url( add_query_arg( 'view', 'grid' ) ); ?>" class="evently-view-toggle__btn<?php echo 'grid' === $evently_view ? ' is-active' : ''; ?>" aria-current="<?php echo 'grid' === $evently_view ? 'true' : 'false'; ?>" aria-label="<?php esc_attr_e( 'Grid view', 'evently' ); ?>">
						<?php evently_icon( 'menu' ); ?>
					</a>
					<a href="<?php echo esc_url( add_query_arg( 'view', 'list' ) ); ?>" class="evently-view-toggle__btn<?php echo 'list' === $evently_view ? ' is-active' : ''; ?>" aria-current="<?php echo 'list' === $evently_view ? 'true' : 'false'; ?>" aria-label="<?php esc_attr_e( 'List view', 'evently' ); ?>">
						<?php evently_icon( 'ticket' ); ?>
					</a>
				</div>
			</div>
		</div>

		<?php if ( ! evently_has_booking_plugin() ) : ?>
			<div class="evently-notice evently-notice--warning">
				<?php
				printf(
					/* translators: %s: link text. */
					esc_html__( 'Event booking requires the Evently Booking plugin. %s', 'evently' ),
					'<a href="https://mage-people.com/product/mage-woo-event-booking-manager-pro/" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Learn more', 'evently' ) . '</a>'
				);
				?>
			</div>
		<?php elseif ( empty( $evently_results['cards'] ) ) : ?>
			<?php
			evently_template_part(
				'template-parts/cards/empty-state',
				'',
				array(
					'title'   => __( 'No events match your filters', 'evently' ),
					'message' => __( 'Try a different category, city, or clear your search.', 'evently' ),
					'action'  => array(
						'text' => __( 'Clear filters', 'evently' ),
						'url'  => evently_get_events_page_url(),
					),
				)
			);
			?>
		<?php else : ?>
			<?php evently_event_grid( $evently_results['cards'], $evently_view, 'list' === $evently_view ? 'list' : 'grid-3' ); ?>

			<?php if ( $evently_results['max_pages'] > 1 ) : ?>
				<nav class="evently-pagination" aria-label="<?php esc_attr_e( 'Events pagination', 'evently' ); ?>">
					<?php
					echo paginate_links( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- paginate_links() output is already escaped by WP core.
						array(
							'total'     => $evently_results['max_pages'],
							'current'   => $evently_results['paged'],
							'prev_text' => evently_get_icon( 'chevron-left' ) . '<span class="screen-reader-text">' . esc_html__( 'Previous', 'evently' ) . '</span>',
							'next_text' => '<span class="screen-reader-text">' . esc_html__( 'Next', 'evently' ) . '</span>' . evently_get_icon( 'chevron-right' ),
						)
					);
					?>
				</nav>
			<?php endif; ?>
		<?php endif; ?>
	</div>
</div>

<?php
/**
 * Fires after the Event Archive's main content.
 */
do_action( 'evently_after_event_content' );
?>

<div id="evently-filters-drawer" class="evently-modal evently-modal--drawer" data-evently-modal role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Filter events', 'evently' ); ?>" hidden>
	<div class="evently-modal__panel evently-modal__panel--drawer">
		<div class="evently-modal__header">
			<h2 class="evently-modal__title"><?php esc_html_e( 'Filters', 'evently' ); ?></h2>
			<button type="button" class="evently-icon-btn" data-evently-modal-close aria-label="<?php esc_attr_e( 'Close filters', 'evently' ); ?>">
				<?php evently_icon( 'close' ); ?>
			</button>
		</div>
		<?php evently_template_part( 'template-parts/archive/filters', '', $evently_filter_args ); ?>
	</div>
</div>
