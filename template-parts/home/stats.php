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

// The Evently Stats Elementor widget's own repeater (class-widget-stats.php)
// takes priority when present; otherwise fall back to evently_home_stats()
// (the legacy per-index evently_get_setting() overrides layered on the demo
// dataset), exactly as this file behaved before the widget had any controls.
$evently_stats = ( ! empty( $args['items'] ) && is_array( $args['items'] ) ) ? $args['items'] : evently_home_stats();
?>
<section class="evently-section">
	<div class="evently-container stats-grid" data-evently-count-up>
		<?php foreach ( $evently_stats as $evently_stat ) : ?>
			<div>
				<div class="stat-val"><?php echo esc_html( $evently_stat['value'] ); ?></div>
				<div class="stat-lbl"><?php echo esc_html( $evently_stat['label'] ); ?></div>
			</div>
		<?php endforeach; ?>
	</div>
</section>
