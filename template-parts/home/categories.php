<?php
/**
 * Homepage Categories — asymmetric bento grid (brief §11 Category section).
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$evently_archive_url = evently_get_events_page_url();
$evently_heading     = $args['heading'] ?? __( 'Explore by experience', 'evently' );
$evently_subhead     = $args['subhead'] ?? __( "Find something you'll love.", 'evently' );

// The Evently Categories Elementor widget's own repeater (class-widget-categories.php)
// takes priority when present; otherwise fall back to the bundled demo items
// overlaid with the legacy per-index evently_get_setting() overrides, exactly
// as this file behaved before the widget had any controls.
if ( ! empty( $args['items'] ) && is_array( $args['items'] ) ) {
	$evently_categories = array();
	foreach ( $args['items'] as $evently_item ) {
		$evently_categories[] = array(
			'label'          => $evently_item['label'],
			'wide'           => ! empty( $evently_item['wide'] ),
			'image_override' => ! empty( $evently_item['image']['url'] ) ? $evently_item['image']['url'] : '',
		);
	}
} else {
	$evently_categories = evently_demo_categories();
	foreach ( $evently_categories as $evently_index => &$evently_cat ) {
		$evently_n            = $evently_index + 1;
		$evently_cat['label'] = evently_get_setting( "category_{$evently_n}_label", $evently_cat['label'] );
		$evently_cat['image_override'] = evently_get_setting( "category_{$evently_n}_image", '' );
	}
	unset( $evently_cat );
}
?>
<section class="evently-section" id="evently-categories">
	<div class="evently-container">
		<div class="evently-section-head">
			<h2><?php echo esc_html( $evently_heading ); ?></h2>
			<p><?php echo esc_html( $evently_subhead ); ?></p>
		</div>

		<div class="cat-grid">
			<?php foreach ( $evently_categories as $evently_index => $evently_cat ) : ?>
				<div class="cat-card<?php echo $evently_cat['wide'] ? ' cat-card--wide' : ''; ?>">
					<img src="<?php echo esc_url( ! empty( $evently_cat['image_override'] ) ? $evently_cat['image_override'] : evently_demo_image_url( $evently_cat ) ); ?>" alt="<?php echo esc_attr( $evently_cat['label'] ); ?>" loading="<?php echo 0 === $evently_index ? 'eager' : 'lazy'; ?>" />
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
