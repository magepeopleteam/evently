<?php
/**
 * Homepage Categories — asymmetric bento grid (brief §11 Category section).
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$evently_categories = evently_demo_categories();
$evently_archive_url = evently_get_events_page_url();
?>
<section class="evently-section" id="evently-categories">
	<div class="evently-container">
		<div class="evently-section-head">
			<h2><?php esc_html_e( 'Explore by experience', 'evently' ); ?></h2>
			<p><?php esc_html_e( "Find something you'll love.", 'evently' ); ?></p>
		</div>

		<div class="cat-grid">
			<?php foreach ( $evently_categories as $evently_index => $evently_cat ) : ?>
				<div class="cat-card<?php echo $evently_cat['wide'] ? ' cat-card--wide' : ''; ?>">
					<img src="<?php echo esc_url( evently_demo_image_url( $evently_cat ) ); ?>" alt="<?php echo esc_attr( $evently_cat['label'] ); ?>" loading="<?php echo 0 === $evently_index ? 'eager' : 'lazy'; ?>" />
					<div class="cat-overlay" aria-hidden="true"></div>
					<a class="cat-label" href="<?php echo esc_url( add_query_arg( 'mep_cat', sanitize_title( $evently_cat['label'] ), $evently_archive_url ) ); ?>">
						<span><?php echo esc_html( $evently_cat['label'] ); ?></span>
						<span class="cat-arrow" aria-hidden="true">→</span>
					</a>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
