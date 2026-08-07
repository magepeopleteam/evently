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

$evently_events = evently_get_home_events( 8, 'trending' );
?>
<section class="evently-section">
	<div class="evently-container">
		<div class="evently-section-head evently-section-head--row">
			<h2 class="evently-mb-0"><?php esc_html_e( 'Trending right now', 'evently' ); ?></h2>
			<a class="evently-section-head__link" href="<?php echo esc_url( evently_get_events_page_url() ); ?>">
				<?php esc_html_e( 'View all', 'evently' ); ?> <span class="arrow" aria-hidden="true">→</span>
			</a>
		</div>

		<?php evently_event_grid( $evently_events, 'default', 'grid-4' ); ?>
	</div>
</section>
