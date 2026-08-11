<?php
/**
 * Template Name: Evently — Event Archive
 * Template Post Type: page
 *
 * The real, primary "browse all events" page (brief §15). Because
 * mage-eventpress registers `mep_events` with `has_archive => false`, this
 * plugin's own intended pattern for a full events-listing page is a normal
 * WP Page — this is that page, assigned via Page Attributes → Template in
 * the block/classic editor. evently_get_events_page_url() finds whichever
 * published page uses this template automatically, so every "Explore
 * Events" link across the theme already points here once a site owner
 * creates one (the demo importer creates it automatically — brief §28).
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

if ( ! function_exists( 'evently_elementor_location' ) || ! evently_elementor_location( 'single' ) ) {
	while ( have_posts() ) :
		the_post();
		// Any editor content added above the archive (e.g. an intro paragraph)
		// prints first, so this template stays useful even with custom copy.
		$evently_page_content = get_the_content();
		if ( ! empty( trim( wp_strip_all_tags( $evently_page_content ) ) ) ) :
			?>
			<div class="evently-container evently-archive-page-intro">
				<?php the_content(); ?>
			</div>
			<?php
		endif;
	endwhile;

	evently_template_part( 'template-parts/archive/event-archive-content' );
}

get_footer();
