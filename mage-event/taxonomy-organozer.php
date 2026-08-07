<?php
/**
 * Evently's skin for a single Organizer/Venue archive (`mep_org` taxonomy).
 * Filename intentionally matches the plugin's own (misspelled)
 * templates/taxonomy-organozer.php so MPWEM_Functions::template_path()
 * resolves this override — see mage-event/event-archive.php's file header.
 *
 * This doubles as the closest thing to an "Organizer Profile" page (brief
 * §22) the plugin's real taxonomy data supports: name, description, and
 * their events. Term meta (org_location/org_street/org_city/...) backs the
 * address shown here — see Evently_Booking_Adapter::get_address().
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

if ( have_posts() ) {
	the_post();
	rewind_posts();
}

$evently_term = get_queried_object();
$evently_sort = isset( $_GET['sort'] ) && 'DESC' === $_GET['sort'] ? 'DESC' : 'ASC';

$evently_results = Evently_Booking_Adapter::search_events(
	array(
		'org'      => $evently_term instanceof WP_Term ? $evently_term->slug : '',
		'sort'     => $evently_sort,
		'per_page' => (int) evently_get_setting( 'archive_columns_per_page', 12 ),
	)
);

$evently_address_parts = array();
if ( $evently_term instanceof WP_Term ) {
	foreach ( array( 'org_location', 'org_street', 'org_city', 'org_state', 'org_country' ) as $evently_meta_key ) {
		$evently_value = get_term_meta( $evently_term->term_id, $evently_meta_key, true );
		if ( $evently_value ) {
			$evently_address_parts[] = $evently_value;
		}
	}
}

do_action( 'evently_before_event_content' );
?>

<div class="evently-organizer-hero">
	<div class="evently-container">
		<p class="evently-breadcrumb">
			<a href="<?php echo esc_url( evently_get_events_page_url() ); ?>"><?php esc_html_e( 'All Events', 'evently' ); ?></a>
			<span aria-hidden="true">/</span>
			<span><?php esc_html_e( 'Organizers', 'evently' ); ?></span>
		</p>

		<div class="evently-organizer-hero__row">
			<div class="evently-avatar evently-organizer-hero__avatar" style="background-color:var(--evently-primary)">
				<?php echo esc_html( mb_substr( $evently_term->name, 0, 1 ) ); ?>
			</div>
			<div>
				<h1 class="evently-organizer-hero__title"><?php echo esc_html( $evently_term->name ); ?></h1>
				<?php if ( ! empty( $evently_address_parts ) ) : ?>
					<p class="evently-organizer-hero__location">
						<?php evently_icon( 'pin' ); ?> <?php echo esc_html( implode( ', ', $evently_address_parts ) ); ?>
					</p>
				<?php endif; ?>
				<p class="evently-organizer-hero__count">
					<?php
					printf(
						/* translators: %s: number of events by this organizer. */
						esc_html( _n( '%s event', '%s events', $evently_results['total'], 'evently' ) ),
						'<strong>' . esc_html( number_format_i18n( $evently_results['total'] ) ) . '</strong>'
					);
					?>
				</p>
			</div>
		</div>

		<?php if ( ! empty( $evently_term->description ) ) : ?>
			<p class="evently-organizer-hero__desc"><?php echo esc_html( $evently_term->description ); ?></p>
		<?php endif; ?>
	</div>
</div>

<div class="evently-container evently-section--tight">
	<h2 class="evently-organizer-events-title"><?php esc_html_e( 'Upcoming events', 'evently' ); ?></h2>

	<?php if ( empty( $evently_results['cards'] ) ) : ?>
		<?php
		evently_template_part(
			'template-parts/cards/empty-state',
			'',
			array(
				'title'   => __( 'No upcoming events from this organizer', 'evently' ),
				'message' => __( 'Check back soon, or browse all events instead.', 'evently' ),
				'action'  => array(
					'text' => __( 'Browse all events', 'evently' ),
					'url'  => evently_get_events_page_url(),
				),
			)
		);
		?>
	<?php else : ?>
		<?php evently_event_grid( $evently_results['cards'], 'default', 'grid-3' ); ?>

		<?php if ( $evently_results['max_pages'] > 1 ) : ?>
			<nav class="evently-pagination" aria-label="<?php esc_attr_e( 'Events pagination', 'evently' ); ?>">
				<?php
				echo paginate_links( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- paginate_links() output is already escaped by WP core.
					array(
						'total'   => $evently_results['max_pages'],
						'current' => $evently_results['paged'],
					)
				);
				?>
			</nav>
		<?php endif; ?>
	<?php endif; ?>
</div>

<?php
do_action( 'evently_after_event_content' );

get_footer();
