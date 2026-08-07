<?php
/**
 * Testimonials (brief §11) — 3 cards, horizontal carousel on mobile.
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section class="evently-section evently-section--soft">
	<div class="evently-container">
		<div class="evently-section-head evently-section-head--center">
			<h2><?php esc_html_e( 'Loved by people who love great experiences.', 'evently' ); ?></h2>
		</div>

		<div class="testi-grid" data-evently-carousel="mobile">
			<?php foreach ( evently_demo_testimonials() as $evently_testimonial ) : ?>
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
