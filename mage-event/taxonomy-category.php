<?php
/**
 * Evently's skin for a single Event Category archive (`mep_cat` taxonomy).
 * Overrides the plugin's templates/taxonomy-category.php — see
 * mage-event/event-archive.php's file header for how this override path
 * is resolved.
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
$evently_view = isset( $_GET['view'] ) && 'list' === $_GET['view'] ? 'list' : 'grid';

$evently_results = Evently_Booking_Adapter::search_events(
	array(
		'cat'      => $evently_term instanceof WP_Term ? $evently_term->slug : '',
		'sort'     => $evently_sort,
		'per_page' => (int) evently_get_setting( 'archive_columns_per_page', 12 ),
	)
);

do_action( 'evently_before_event_content' );
?>

<div class="evently-archive-hero">
	<div class="evently-container">
		<p class="evently-breadcrumb">
			<a href="<?php echo esc_url( evently_get_events_page_url() ); ?>"><?php esc_html_e( 'All Events', 'evently' ); ?></a>
			<span aria-hidden="true">/</span>
			<span><?php echo esc_html( $evently_term->name ); ?></span>
		</p>
		<h1 class="evently-archive-hero__title"><?php echo esc_html( $evently_term->name ); ?></h1>
		<?php if ( ! empty( $evently_term->description ) ) : ?>
			<p class="evently-archive-hero__sub"><?php echo esc_html( $evently_term->description ); ?></p>
		<?php else : ?>
			<p class="evently-archive-hero__sub">
				<?php
				printf(
					/* translators: %s: number of events found. */
					esc_html( _n( '%s event found', '%s events found', $evently_results['total'], 'evently' ) ),
					'<strong>' . esc_html( number_format_i18n( $evently_results['total'] ) ) . '</strong>'
				);
				?>
			</p>
		<?php endif; ?>
	</div>
</div>

<div class="evently-container evently-section--tight">
	<?php if ( empty( $evently_results['cards'] ) ) : ?>
		<?php
		evently_template_part(
			'template-parts/cards/empty-state',
			'',
			array(
				'title'   => __( 'No events in this category yet', 'evently' ),
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
