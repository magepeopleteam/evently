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

// The Evently Hero Elementor widget's own "Buttons" repeater
// (class-widget-hero.php) takes priority when present — any number of
// buttons, in whatever order they were added/reordered in the panel;
// otherwise fall back to the original fixed Explore Events + Browse
// Categories pair, exactly as this file behaved before the repeater existed.
if ( ! empty( $args['buttons'] ) && is_array( $args['buttons'] ) ) {
	$evently_hero_buttons = array();
	foreach ( $args['buttons'] as $evently_btn ) {
		$evently_hero_buttons[] = array(
			'text'    => $evently_btn['text'],
			'url'     => ! empty( $evently_btn['url']['url'] ) ? $evently_btn['url']['url'] : evently_get_events_page_url(),
			'variant' => ! empty( $evently_btn['variant'] ) ? $evently_btn['variant'] : 'primary',
		);
	}
} else {
	$evently_hero_buttons = array(
		array(
			'text'    => __( 'Explore Events', 'evently' ),
			'url'     => evently_get_events_page_url(),
			'variant' => 'primary',
		),
		array(
			'text'    => __( 'Browse Categories', 'evently' ),
			'url'     => '#evently-categories',
			'variant' => 'secondary',
		),
	);
}

$evently_spotlight_title    = $args['spotlight_title'] ?? $evently_spotlight['title'];
$evently_spotlight_date     = $args['spotlight_date'] ?? $evently_spotlight['date_full'];
$evently_spotlight_location = $args['spotlight_location'] ?? $evently_spotlight['location'];
$evently_spotlight_price    = $args['spotlight_price'] ?? $evently_spotlight['price_label'];
$evently_spotlight_btn_text = $args['spotlight_button_text'] ?? __( 'Book Now', 'evently' );
$evently_spotlight_btn_url  = ! empty( $args['spotlight_button_url']['url'] ) ? $args['spotlight_button_url']['url'] : evently_get_events_page_url();
$evently_show_search        = ! isset( $args['show_search'] ) || 'yes' === $args['show_search'];
?>
<section class="hero">
	<div class="evently-container hero-inner">
		<div class="hero-left">
			<span class="evently-eyebrow evently-eyebrow--pill"><?php echo esc_html( $evently_eyebrow ); ?></span>
			<h1 class="hero-title"><?php echo esc_html( $evently_heading_line_1 ); ?><br /><?php echo esc_html( $evently_heading_line_2 ); ?></h1>
			<p class="hero-sub"><?php echo esc_html( $evently_subhead ); ?></p>

			<div class="hero-btns">
				<?php foreach ( $evently_hero_buttons as $evently_hero_btn ) : ?>
					<?php evently_button( $evently_hero_btn ); ?>
				<?php endforeach; ?>
			</div>

			<div class="hero-stats">
				<?php
				// The Evently Hero Elementor widget's own "Stat tiles" repeater
				// (class-widget-hero.php) takes priority when present; otherwise
				// fall back to evently_home_stats() minus its 3rd entry (reserved
				// for the full Stats section further down the page), exactly as
				// this file behaved before the repeater existed.
				if ( ! empty( $args['stats'] ) && is_array( $args['stats'] ) ) {
					$evently_hero_stats = $args['stats'];
				} else {
					$evently_hero_stats = array();
					foreach ( evently_home_stats() as $evently_stat_index => $evently_stat ) {
						if ( 2 === $evently_stat_index ) {
							continue;
						}
						$evently_hero_stats[] = $evently_stat;
					}
				}
				foreach ( $evently_hero_stats as $evently_stat ) :
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

	<?php if ( $evently_show_search ) : ?>
		<?php evently_template_part( 'template-parts/home/search-bar' ); ?>
	<?php endif; ?>
</section>
