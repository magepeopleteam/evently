<?php
/**
 * Testimonials (brief §11) — 3 cards, horizontal carousel on mobile.
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$evently_heading = $args['heading'] ?? __( 'Loved by people who love great experiences.', 'evently' );

// The Evently Testimonials Elementor widget's own repeater
// (class-widget-testimonials.php) takes priority when present; otherwise
// fall back to the bundled demo quotes overlaid with the legacy per-index
// evently_get_setting() overrides, exactly as this file behaved before the
// widget had any controls.
if ( ! empty( $args['items'] ) && is_array( $args['items'] ) ) {
	$evently_testimonials = $args['items'];
	foreach ( $evently_testimonials as &$evently_t ) {
		$evently_t['stars'] = (int) $evently_t['stars'];
	}
	unset( $evently_t );
} else {
	$evently_testimonials = evently_demo_testimonials();
	foreach ( $evently_testimonials as $evently_index => &$evently_t ) {
		$evently_n         = $evently_index + 1;
		$evently_t['name']     = evently_get_setting( "testimonial_{$evently_n}_name", $evently_t['name'] );
		$evently_t['role']     = evently_get_setting( "testimonial_{$evently_n}_role", $evently_t['role'] );
		$evently_t['text']     = evently_get_setting( "testimonial_{$evently_n}_text", $evently_t['text'] );
		$evently_t['initials'] = evently_get_setting( "testimonial_{$evently_n}_initials", $evently_t['initials'] );
		$evently_t['color']    = evently_get_setting( "testimonial_{$evently_n}_color", $evently_t['color'] );
		$evently_t['stars']    = (int) evently_get_setting( "testimonial_{$evently_n}_stars", $evently_t['stars'] );
	}
	unset( $evently_t );
}
?>
<section class="evently-section evently-section--soft">
	<div class="evently-container">
		<div class="evently-section-head evently-section-head--center">
			<h2><?php echo esc_html( $evently_heading ); ?></h2>
		</div>

		<div class="testi-grid" data-evently-carousel="mobile">
			<?php foreach ( $evently_testimonials as $evently_testimonial ) : ?>
				<div class="testi-card">
					<?php evently_star_rating( $evently_testimonial['stars'] ); ?>
					<p class="testi-text">&ldquo;<?php echo esc_html( $evently_testimonial['text'] ); ?>&rdquo;</p>
					<div class="testi-author">
						<div class="evently-avatar" style="background-color:<?php echo esc_attr( $evently_testimonial['color'] ); ?>">
							<?php echo esc_html( $evently_testimonial['initials'] ); ?>
						</div>
						<div>
							<div class="testi-name"><?php echo esc_html( $evently_testimonial['name'] ); ?></div>
							<div class="testi-role"><?php echo esc_html( $evently_testimonial['role'] ); ?></div>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
