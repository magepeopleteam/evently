<?php
/**
 * "Evently Event Carousel" pattern — a horizontal-scrolling row of real
 * events (brief §25), for pages that want a compact, swipeable teaser
 * rather than a full grid.
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'evently_pattern_content_event_carousel' ) ) {
	/**
	 * @return string
	 */
	function evently_pattern_content_event_carousel() {
		$events = evently_get_home_events( 8, 'trending' );

		ob_start();
		?>
		<section class="evently-section">
			<div class="evently-container">
				<div class="evently-section-head">
					<h2><?php esc_html_e( 'Discover events', 'evently' ); ?></h2>
				</div>
				<?php evently_event_grid( $events, 'compact', 'scroll' ); ?>
			</div>
		</section>
		<?php
		return evently_html_block( (string) ob_get_clean() );
	}
}
