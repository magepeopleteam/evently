<?php
/**
 * The homepage (brief §11). Assembles the 14 content sections as
 * independent, individually-reusable template-parts — never a monolithic
 * file — so any section can be reordered, removed, or swapped for its
 * block-pattern equivalent without touching the others.
 *
 * This is the theme's built-in default. Once a site owner runs Evently
 * Setup's "Homepage Editor" step and picks Gutenberg or Elementor,
 * evently_homepage_uses_custom_builder() starts returning true and this file
 * hands off to that admin-built page instead — see the branch below. Nothing
 * here changes for a fresh/default install.
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

if ( evently_homepage_uses_custom_builder() ) {
	global $post;
	$evently_custom_front_page = get_post( (int) get_option( 'page_on_front' ) );
	if ( $evently_custom_front_page ) {
		$post = $evently_custom_front_page; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- standard "borrow the loop" pattern for rendering one specific page's content outside The Loop.
		setup_postdata( $post );
		?>
		<main id="primary" class="evently-custom-homepage">
			<?php the_content(); ?>
		</main>
		<?php
		wp_reset_postdata();
	}
} else {
	$evently_home_sections = array(
		'hero',
		'categories',
		'trending-events',
		'featured-event',
		'choose-vibe',
		'near-you',
		'calendar',
		'how-it-works',
		'digital-ticket',
		'organizer-cta',
		'stats',
		'testimonials',
		'event-journal',
		'final-cta',
	);

	/**
	 * Filters the ordered list of homepage section slugs rendered by
	 * front-page.php. Each slug maps to template-parts/home/{slug}.php.
	 * Only applies to the built-in demo homepage, not an admin-built one.
	 *
	 * @param string[] $evently_home_sections Section slugs in render order.
	 */
	$evently_home_sections = apply_filters( 'evently_home_sections', $evently_home_sections );

	foreach ( $evently_home_sections as $evently_section ) {
		evently_template_part( 'template-parts/home/' . sanitize_file_name( $evently_section ) );
	}
}

get_footer();
