<?php
/**
 * Trending Events (brief §11) — 4-column grid, real data via
 * evently_get_home_events(), falling back to the demo dataset.
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$evently_heading       = $args['heading'] ?? __( 'Trending right now', 'evently' );
$evently_view_all_text = $args['view_all_text'] ?? __( 'View all', 'evently' );
$evently_view_all_url  = ! empty( $args['view_all_url']['url'] ) ? $args['view_all_url']['url'] : evently_get_events_page_url();
$evently_count         = isset( $args['count'] ) ? (int) $args['count'] : 8;
$evently_events        = evently_get_home_events( $evently_count, 'trending' );
?>
<section class="evently-section">
	<div class="evently-container">
		<div class="evently-section-head evently-section-head--row">
			<h2 class="evently-mb-0"><?php echo esc_html( $evently_heading ); ?></h2>
			<a class="evently-section-head__link" href="<?php echo esc_url( $evently_view_all_url ); ?>">
				<?php echo esc_html( $evently_view_all_text ); ?> <span class="arrow" aria-hidden="true">→</span>
			</a>
		</div>

		<?php evently_event_grid( $evently_events, 'default', 'grid-4' ); ?>
	</div>
</section>
