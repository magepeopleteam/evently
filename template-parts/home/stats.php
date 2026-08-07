<?php
/**
 * Stats strip (brief §11). Numbers animate a subtle count-up when the
 * section enters the viewport — see the IntersectionObserver-gated
 * handler for [data-evently-count-up] in assets/js/carousel.js.
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section class="evently-section">
	<div class="evently-container stats-grid" data-evently-count-up>
		<?php foreach ( evently_demo_stats() as $evently_stat ) : ?>
			<div>
				<div class="stat-val"><?php echo esc_html( $evently_stat['value'] ); ?></div>
				<div class="stat-lbl"><?php echo esc_html( $evently_stat['label'] ); ?></div>
			</div>
		<?php endforeach; ?>
	</div>
</section>
