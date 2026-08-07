<?php
/**
 * Homepage Hero (brief §12). Falls back to demo content but reads real
 * settings (headline/image/spotlight event) once wired to Theme Settings.
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$evently_spotlight = evently_demo_events()[0];
$evently_hero_image = evently_get_setting( 'hero_image', evently_demo_image_url( array( 'image_file' => 'hero-concert-crowd.jpg' ) ) );
$evently_tickets_today = evently_get_setting( 'hero_live_note', __( '2,840 tickets sold today', 'evently' ) );
?>
<section class="hero">
	<div class="evently-container hero-inner">
		<div class="hero-left">
			<span class="evently-eyebrow evently-eyebrow--pill"><?php esc_html_e( 'Discover Your Next Experience', 'evently' ); ?></span>
			<h1 class="hero-title"><?php esc_html_e( 'Events worth', 'evently' ); ?><br /><?php esc_html_e( 'remembering.', 'evently' ); ?></h1>
			<p class="hero-sub"><?php esc_html_e( 'Find concerts, conferences, festivals and experiences happening around you.', 'evently' ); ?></p>

			<div class="hero-btns">
				<?php
				evently_button(
					array(
						'text'    => __( 'Explore Events', 'evently' ),
						'url'     => evently_get_events_page_url(),
						'variant' => 'primary',
					)
				);
				evently_button(
					array(
						'text'    => __( 'Browse Categories', 'evently' ),
						'url'     => '#evently-categories',
						'variant' => 'secondary',
					)
				);
				?>
			</div>

			<div class="hero-stats">
				<?php foreach ( evently_demo_stats() as $evently_stat ) : ?>
					<?php if ( __( 'Customer Satisfaction', 'evently' ) === $evently_stat['label'] ) { continue; } ?>
					<div>
						<div class="hero-stat-num"><?php echo esc_html( $evently_stat['value'] ); ?></div>
						<div class="hero-stat-lbl"><?php echo esc_html( $evently_stat['label'] ); ?></div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>

		<div class="hero-img-wrap">
			<div class="hero-img">
				<img src="<?php echo esc_url( $evently_hero_image ); ?>" alt="<?php esc_attr_e( 'Crowd at a live summer music festival', 'evently' ); ?>" />
			</div>

			<div class="hero-live">
				<span class="hero-live-dot" aria-hidden="true"></span>
				<span><?php echo esc_html( $evently_tickets_today ); ?></span>
			</div>

			<div class="hero-card">
				<div class="hero-card-cat"><?php echo esc_html( mb_strtoupper( $evently_spotlight['title'] ) ); ?></div>
				<div class="hero-card-date"><?php echo esc_html( $evently_spotlight['date_full'] ); ?></div>
				<div class="hero-card-loc"><?php evently_icon( 'pin' ); ?><span><?php echo esc_html( $evently_spotlight['location'] ); ?></span></div>
				<div class="hero-card-foot">
					<span class="hero-card-price"><?php echo esc_html( $evently_spotlight['price_label'] ); ?></span>
					<?php
					evently_button(
						array(
							'text'    => __( 'Book Now', 'evently' ),
							'url'     => evently_get_events_page_url(),
							'variant' => 'primary',
							'size'    => 'sm',
						)
					);
					?>
				</div>
			</div>
		</div>
	</div>

	<?php evently_template_part( 'template-parts/home/search-bar' ); ?>
</section>
