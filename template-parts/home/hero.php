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

// $args is populated by the Evently Hero Elementor widget's own settings
// (inc/integrations/elementor/class-widget-hero.php); empty/absent when
// rendered via the Gutenberg block or the static "builtin" homepage
// fallback, in which case every field below falls through to exactly what
// this file did before widget controls existed.
$evently_spotlight = evently_demo_events()[0];

$evently_eyebrow        = $args['eyebrow'] ?? __( 'Discover Your Next Experience', 'evently' );
$evently_heading_line_1 = $args['heading_line_1'] ?? __( 'Events worth', 'evently' );
$evently_heading_line_2 = $args['heading_line_2'] ?? __( 'remembering.', 'evently' );
$evently_subhead        = $args['subhead'] ?? __( 'Find concerts, conferences, festivals and experiences happening around you.', 'evently' );
$evently_hero_image     = ! empty( $args['hero_image']['url'] ) ? $args['hero_image']['url'] : evently_get_setting( 'hero_image', evently_demo_image_url( array( 'image_file' => 'hero-concert-crowd.jpg' ) ) );
$evently_tickets_today  = $args['live_note'] ?? evently_get_setting( 'hero_live_note', __( '2,840 tickets sold today', 'evently' ) );

$evently_primary_text    = $args['primary_button_text'] ?? __( 'Explore Events', 'evently' );
$evently_primary_url     = ! empty( $args['primary_button_url']['url'] ) ? $args['primary_button_url']['url'] : evently_get_events_page_url();
$evently_secondary_text  = $args['secondary_button_text'] ?? __( 'Browse Categories', 'evently' );
$evently_secondary_url   = ! empty( $args['secondary_button_url']['url'] ) ? $args['secondary_button_url']['url'] : '#evently-categories';

$evently_spotlight_title    = $args['spotlight_title'] ?? $evently_spotlight['title'];
$evently_spotlight_date     = $args['spotlight_date'] ?? $evently_spotlight['date_full'];
$evently_spotlight_location = $args['spotlight_location'] ?? $evently_spotlight['location'];
$evently_spotlight_price    = $args['spotlight_price'] ?? $evently_spotlight['price_label'];
$evently_spotlight_btn_text = $args['spotlight_button_text'] ?? __( 'Book Now', 'evently' );
$evently_spotlight_btn_url  = ! empty( $args['spotlight_button_url']['url'] ) ? $args['spotlight_button_url']['url'] : evently_get_events_page_url();
?>
<section class="hero">
	<div class="evently-container hero-inner">
		<div class="hero-left">
			<span class="evently-eyebrow evently-eyebrow--pill"><?php echo esc_html( $evently_eyebrow ); ?></span>
			<h1 class="hero-title"><?php echo esc_html( $evently_heading_line_1 ); ?><br /><?php echo esc_html( $evently_heading_line_2 ); ?></h1>
			<p class="hero-sub"><?php echo esc_html( $evently_subhead ); ?></p>

			<div class="hero-btns">
				<?php
				evently_button(
					array(
						'text'    => $evently_primary_text,
						'url'     => $evently_primary_url,
						'variant' => 'primary',
					)
				);
				evently_button(
					array(
						'text'    => $evently_secondary_text,
						'url'     => $evently_secondary_url,
						'variant' => 'secondary',
					)
				);
				?>
			</div>

			<div class="hero-stats">
				<?php
				// Hero shows only 3 of the 4 stats; the 3rd (index 2) is reserved
				// for the full Stats section further down the page.
				foreach ( evently_home_stats() as $evently_stat_index => $evently_stat ) :
					if ( 2 === $evently_stat_index ) {
						continue;
					}
					?>
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
				<div class="hero-card-cat"><?php echo esc_html( mb_strtoupper( $evently_spotlight_title ) ); ?></div>
				<div class="hero-card-date"><?php echo esc_html( $evently_spotlight_date ); ?></div>
				<div class="hero-card-loc"><?php evently_icon( 'pin' ); ?><span><?php echo esc_html( $evently_spotlight_location ); ?></span></div>
				<div class="hero-card-foot">
					<span class="hero-card-price"><?php echo esc_html( $evently_spotlight_price ); ?></span>
					<?php
					evently_button(
						array(
							'text'    => $evently_spotlight_btn_text,
							'url'     => $evently_spotlight_btn_url,
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
