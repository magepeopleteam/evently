<?php
/**
 * Featured Event — full-bleed cinematic banner (brief §11).
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$evently_featured_eyebrow  = $args['eyebrow'] ?? __( 'Featured Experience', 'evently' );
$evently_featured_image    = ! empty( $args['image']['url'] ) ? $args['image']['url'] : evently_get_setting( 'featured_event_image', evently_demo_image_url( array( 'image_file' => 'featured-music-festival.jpg' ) ) );
$evently_featured_title    = $args['title'] ?? evently_get_setting( 'featured_event_title', __( 'Future Music Festival', 'evently' ) );
$evently_featured_date     = $args['date'] ?? evently_get_setting( 'featured_event_date', __( 'August 24–26, 2026', 'evently' ) );
$evently_featured_location = $args['location'] ?? evently_get_setting( 'featured_event_location', __( 'Dhaka, Bangladesh', 'evently' ) );
$evently_featured_note     = $args['note'] ?? evently_get_setting( 'featured_event_note', __( '20,000+ attendees expected', 'evently' ) );
$evently_featured_btn_text = $args['button_text'] ?? __( 'Explore Event', 'evently' );
$evently_featured_url      = evently_get_events_page_url();
?>
<a href="<?php echo esc_url( $evently_featured_url ); ?>" class="featured-section">
	<img src="<?php echo esc_url( $evently_featured_image ); ?>" alt="<?php echo esc_attr( $evently_featured_title ); ?>" loading="lazy" />
	<div class="featured-overlay" aria-hidden="true"></div>
	<div class="featured-content">
		<div class="featured-inner">
			<div class="evently-eyebrow evently-eyebrow--on-dark"><?php echo esc_html( $evently_featured_eyebrow ); ?></div>
			<h2 class="featured-title"><?php echo esc_html( $evently_featured_title ); ?></h2>
			<div class="featured-meta">
				<span><?php echo esc_html( $evently_featured_date ); ?></span>
				<span aria-hidden="true">·</span>
				<span><?php echo esc_html( $evently_featured_location ); ?></span>
			</div>
			<div class="featured-attendees"><?php echo esc_html( $evently_featured_note ); ?></div>
			<span class="btn btn--white"><?php echo esc_html( $evently_featured_btn_text ); ?> <span class="evently-arrow" aria-hidden="true">→</span></span>
		</div>
	</div>
</a>
