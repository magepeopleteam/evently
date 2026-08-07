<?php
/**
 * Gutenberg block registrations.
 *
 * Two different mechanisms live in this file:
 * 1. `register_block_style()` variations so core blocks (Button, Group,
 *    Separator) can pick up Evently's design language directly from the
 *    block editor's Styles panel — no custom block type needed for these.
 * 2. The theme's 14 homepage sections (Hero, Categories, Testimonials, …)
 *    as real dynamic block types under blocks/{slug}/ — each one's
 *    render.php delegates straight to the same template-part the built-in
 *    demo homepage uses, so a section still reflects Theme Settings after
 *    being placed on an admin-built homepage (see
 *    evently_homepage_uses_custom_builder(), inc/helpers.php). This is
 *    deliberately hand-written against WordPress's own editor globals
 *    (assets/js/blocks-editor.js) rather than an npm/webpack build — same
 *    "no framework, no build step" rule the rest of the theme's JS follows.
 *    (The 14 block PATTERNS in inc/patterns/patterns.php are a separate,
 *    older feature — static content snapshots for reuse on OTHER pages —
 *    and are unrelated to these live blocks.)
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Canonical registry for the 14 homepage-section blocks: slug (also the
 * template-parts/home/{slug}.php filename and the `evently/{slug}` block
 * name) => {title, icon, description}. The single source of truth for both
 * the PHP registration below and the editor's JS registration (localized
 * into assets/js/blocks-editor.js as `eventlyBlockSections`) — one place to
 * add/rename/re-describe a block instead of two.
 *
 * @return array<string,array{title:string,icon:string,description:string}>
 */
function evently_get_block_sections() {
	return array(
		'hero'            => array(
			'title'       => __( 'Evently Hero', 'evently' ),
			'icon'        => 'cover-image',
			'description' => __( 'Homepage hero banner: headline, live stat, hero photo, spotlight event card.', 'evently' ),
		),
		'categories'      => array(
			'title'       => __( 'Evently Categories', 'evently' ),
			'icon'        => 'category',
			'description' => __( 'The category bento grid (Concerts, Conferences, Sports…).', 'evently' ),
		),
		'trending-events' => array(
			'title'       => __( 'Evently Trending Events', 'evently' ),
			'icon'        => 'chart-line',
			'description' => __( 'Grid of trending events — real events once added, demo cards otherwise.', 'evently' ),
		),
		'featured-event'  => array(
			'title'       => __( 'Evently Featured Event', 'evently' ),
			'icon'        => 'megaphone',
			'description' => __( 'Full-bleed featured-event banner.', 'evently' ),
		),
		'choose-vibe'     => array(
			'title'       => __( 'Evently Choose Your Vibe', 'evently' ),
			'icon'        => 'filter',
			'description' => __( 'Mood-based pill filter (Music, Learn, Business…) with matching events.', 'evently' ),
		),
		'near-you'        => array(
			'title'       => __( 'Evently Near You', 'evently' ),
			'icon'        => 'location',
			'description' => __( 'Events near the configured default city.', 'evently' ),
		),
		'calendar'        => array(
			'title'       => __( 'Evently Event Calendar', 'evently' ),
			'icon'        => 'calendar-alt',
			'description' => __( 'Editorial month calendar with per-day events.', 'evently' ),
		),
		'how-it-works'    => array(
			'title'       => __( 'Evently How It Works', 'evently' ),
			'icon'        => 'list-view',
			'description' => __( 'The 3-step Discover → Book → Enjoy process.', 'evently' ),
		),
		'digital-ticket'  => array(
			'title'       => __( 'Evently Digital Ticket', 'evently' ),
			'icon'        => 'tickets-alt',
			'description' => __( 'Digital ticket showcase mockup.', 'evently' ),
		),
		'organizer-cta'   => array(
			'title'       => __( 'Evently Organizer CTA', 'evently' ),
			'icon'        => 'businessperson',
			'description' => __( "Dark \"for organizers\" section with a dashboard mockup.", 'evently' ),
		),
		'stats'           => array(
			'title'       => __( 'Evently Stats', 'evently' ),
			'icon'        => 'chart-bar',
			'description' => __( 'The 4-stat strip (Events, Tickets Sold, etc.).', 'evently' ),
		),
		'testimonials'    => array(
			'title'       => __( 'Evently Testimonials', 'evently' ),
			'icon'        => 'testimonial',
			'description' => __( '3-card testimonials carousel.', 'evently' ),
		),
		'event-journal'   => array(
			'title'       => __( 'Evently Event Journal', 'evently' ),
			'icon'        => 'admin-post',
			'description' => __( 'Blog/editorial teaser grid — links to real posts once published.', 'evently' ),
		),
		'final-cta'       => array(
			'title'       => __( 'Evently Final CTA', 'evently' ),
			'icon'        => 'flag',
			'description' => __( 'The closing full-width call-to-action.', 'evently' ),
		),
	);
}

/**
 * Register the `evently` block category so all 14 section blocks group
 * together in the inserter instead of scattering under "Theme".
 *
 * @param array[] $categories
 * @return array[]
 */
function evently_register_block_category( $categories ) {
	return array_merge(
		array(
			array(
				'slug'  => 'evently',
				'title' => __( 'Evently', 'evently' ),
				'icon'  => 'calendar-alt',
			),
		),
		$categories
	);
}
add_filter( 'block_categories_all', 'evently_register_block_category' );

/**
 * Register each homepage-section block from its blocks/{slug}/block.json —
 * `render` in that file points back at render.php, which just calls the
 * theme's normal evently_template_part() (same as front-page.php's default
 * homepage), so there is exactly one rendering codepath, not two.
 *
 * @return void
 */
function evently_register_section_blocks() {
	foreach ( array_keys( evently_get_block_sections() ) as $evently_slug ) {
		$evently_block_json = EVENTLY_DIR . 'blocks/' . $evently_slug . '/block.json';
		if ( is_readable( $evently_block_json ) ) {
			register_block_type( $evently_block_json );
		}
	}
}
add_action( 'init', 'evently_register_section_blocks' );

/**
 * Enqueue the hand-written (no build step) editor JS that registers all 14
 * blocks client-side — server-side registration alone (above) makes render.php
 * work but doesn't teach the block editor's inserter/UI about the block; that
 * always has to happen in JS.
 *
 * @return void
 */
function evently_enqueue_block_editor_js() {
	wp_enqueue_script(
		'evently-blocks-editor',
		EVENTLY_URI . 'assets/js/blocks-editor.js',
		array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-server-side-render', 'wp-i18n' ),
		EVENTLY_VERSION,
		true
	);

	wp_localize_script( 'evently-blocks-editor', 'eventlyBlockSections', evently_get_block_sections() );
}
add_action( 'enqueue_block_editor_assets', 'evently_enqueue_block_editor_js' );

/**
 * Register block style variations.
 *
 * @return void
 */
function evently_register_block_styles() {
	register_block_style(
		'core/button',
		array(
			'name'  => 'evently-accent',
			'label' => __( 'Evently Accent', 'evently' ),
		)
	);

	register_block_style(
		'core/button',
		array(
			'name'  => 'evently-ghost',
			'label' => __( 'Evently Ghost', 'evently' ),
		)
	);

	register_block_style(
		'core/group',
		array(
			'name'  => 'evently-surface',
			'label' => __( 'Evently Surface', 'evently' ),
		)
	);

	register_block_style(
		'core/group',
		array(
			'name'  => 'evently-dark',
			'label' => __( 'Evently Dark', 'evently' ),
		)
	);

	register_block_style(
		'core/separator',
		array(
			'name'  => 'evently-hairline',
			'label' => __( 'Evently Hairline', 'evently' ),
		)
	);
}
add_action( 'init', 'evently_register_block_styles' );

/**
 * CSS backing the style variations above (loaded with the editor + front
 * end styles registered in inc/enqueue.php's editor block, plus its own
 * small front-end stylesheet since these can appear in any post/page).
 *
 * @return void
 */
function evently_enqueue_block_style_css() {
	wp_enqueue_style(
		'evently-block-styles',
		EVENTLY_URI . 'assets/css/block-styles.css',
		array( 'evently-variables' ),
		EVENTLY_VERSION
	);
}
add_action( 'wp_enqueue_scripts', 'evently_enqueue_block_style_css' );
add_action( 'enqueue_block_editor_assets', 'evently_enqueue_block_style_css' );
