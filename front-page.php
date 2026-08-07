<?php
/**
 * The homepage (brief §11). Assembles the 15 content sections as
 * independent, individually-reusable template-parts — never a monolithic
 * file — so any section can be reordered, removed, or swapped for its
 * block-pattern equivalent without touching the others.
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

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
 *
 * @param string[] $evently_home_sections Section slugs in render order.
 */
$evently_home_sections = apply_filters( 'evently_home_sections', $evently_home_sections );

foreach ( $evently_home_sections as $evently_section ) {
	evently_template_part( 'template-parts/home/' . sanitize_file_name( $evently_section ) );
}

get_footer();
