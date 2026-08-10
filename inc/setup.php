<?php
/**
 * Core theme setup: supports, menus, image sizes, content width.
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register theme support flags and navigation menus.
 */
function evently_setup() {
	// Translations.
	load_theme_textdomain( 'evently', EVENTLY_DIR . 'languages' );

	// Core supports.
	add_theme_support( 'title-tag' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'script', 'style', 'navigation-widgets' ) );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'customize-selective-refresh-widgets' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support(
		'editor-color-palette',
		array(
			array( 'name' => __( 'White', 'evently' ), 'slug' => 'white', 'color' => '#FFFFFF' ),
			array( 'name' => __( 'Surface', 'evently' ), 'slug' => 'surface', 'color' => '#F6F6F3' ),
			array( 'name' => __( 'Border', 'evently' ), 'slug' => 'border', 'color' => '#E7E7E3' ),
			array( 'name' => __( 'Muted', 'evently' ), 'slug' => 'muted', 'color' => '#777773' ),
			array( 'name' => __( 'Text', 'evently' ), 'slug' => 'text', 'color' => '#111113' ),
			array( 'name' => __( 'Dark', 'evently' ), 'slug' => 'dark', 'color' => '#0B0B0D' ),
			array( 'name' => __( 'Primary', 'evently' ), 'slug' => 'primary', 'color' => '#6C5CE7' ),
			array( 'name' => __( 'Orange Accent', 'evently' ), 'slug' => 'orange', 'color' => '#FF7657' ),
		)
	);

	add_theme_support(
		'custom-logo',
		array(
			'height'      => 40,
			'width'       => 160,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	add_theme_support(
		'custom-background',
		array(
			'default-color' => 'FFFFFF',
		)
	);

	// Content width used by wp_img_tag_add_width_and_height_attr() etc.
	if ( ! isset( $GLOBALS['content_width'] ) ) {
		$GLOBALS['content_width'] = 1240;
	}

	// Navigation menus.
	register_nav_menus(
		array(
			'primary'         => __( 'Primary Navigation', 'evently' ),
			'footer-explore'  => __( 'Footer — Explore', 'evently' ),
			'footer-company'  => __( 'Footer — Company', 'evently' ),
			'footer-support'  => __( 'Footer — Support', 'evently' ),
			'mobile'          => __( 'Mobile Navigation (optional override)', 'evently' ),
		)
	);

	// Image sizes used by the event-card system (brief §14) and editorial content.
	add_image_size( 'evently-card', 500, 380, true );        // Default/Trending/Vibe event card.
	add_image_size( 'evently-card-wide', 700, 460, true );    // Event Journal / featured lists.
	add_image_size( 'evently-hero', 900, 720, true );         // Hero / single-event gallery.
	add_image_size( 'evently-featured', 1440, 620, true );    // Featured Event full-bleed section.
	add_image_size( 'evently-square', 400, 400, true );       // Organizer/venue avatars, category tiles fallback.
	add_image_size( 'evently-category', 600, 400, true );     // Category bento cards.
}
add_action( 'after_setup_theme', 'evently_setup' );

/**
 * Register the block patterns' category so they group together in the inserter,
 * and disable the default core pattern remote fetch on the homepage template
 * screen if a site owner prefers a fully offline editor experience.
 */
function evently_register_pattern_categories() {
	register_block_pattern_category(
		'evently',
		array( 'label' => __( 'Evently', 'evently' ) )
	);
}
add_action( 'init', 'evently_register_pattern_categories' );

/**
 * Add a skip-to-content target's matching id via body_class + a viewport meta
 * tweak isn't necessary (WP core already prints viewport via other means on
 * most hosts) — this hook is reserved for later per-template body classes
 * (e.g. flagging archive layout / booking-plugin presence for CSS targeting).
 *
 * @param string[] $classes Existing body classes.
 * @return string[]
 */
function evently_body_classes( $classes ) {
	if ( ! evently_has_booking_plugin() ) {
		$classes[] = 'evently-no-booking-plugin';
	}
	if ( evently_has_woocommerce() ) {
		$classes[] = 'evently-has-woocommerce';
	}
	if ( is_front_page() ) {
		$classes[] = 'evently-front-page';
	}
	// Theme (Evently design) single event sits the fixed header over a dark
	// media hero — flag it so header.css can switch to light logo/nav until scroll.
	if ( evently_has_booking_plugin() && is_singular( 'mep_events' ) && ! evently_use_plugin_event_details() ) {
		$classes[] = 'evently-header-on-media';
	}
	return $classes;
}
add_filter( 'body_class', 'evently_body_classes' );

/**
 * Widen the default excerpt length slightly for the Event Journal / blog cards,
 * matching the editorial tone of the design (brief §24).
 *
 * @return int
 */
function evently_excerpt_length() {
	return 24;
}
add_filter( 'excerpt_length', 'evently_excerpt_length', 999 );

/**
 * Use an em dash + ellipsis instead of the default "[&hellip;]" to match the
 * editorial voice used throughout the Figma design ("Read article →").
 *
 * @return string
 */
function evently_excerpt_more() {
	return '…';
}
add_filter( 'excerpt_more', 'evently_excerpt_more' );

/**
 * Max items per dropdown column before a new column is added.
 * 9–16 children → 2 columns, 17–24 → 3, etc.
 */
define( 'EVENTLY_MENU_ITEMS_PER_COLUMN', 8 );

/**
 * Mark primary-nav parents with more than 8 direct children for multi-column
 * dropdowns. Adds `has-multi-column` and `menu-columns-{N}` on the parent <li>
 * so header.css can lay the submenu out as a mega-menu grid (desktop only).
 *
 * @param WP_Post[] $items Menu items.
 * @param stdClass  $args  wp_nav_menu() args.
 * @return WP_Post[]
 */
function evently_mark_multi_column_menu_items( $items, $args ) {
	if ( empty( $args->theme_location ) || 'primary' !== $args->theme_location ) {
		return $items;
	}

	$per_column   = (int) EVENTLY_MENU_ITEMS_PER_COLUMN;
	$child_counts = array();

	foreach ( $items as $item ) {
		$parent_id = (int) $item->menu_item_parent;
		if ( $parent_id > 0 ) {
			if ( ! isset( $child_counts[ $parent_id ] ) ) {
				$child_counts[ $parent_id ] = 0;
			}
			++$child_counts[ $parent_id ];
		}
	}

	foreach ( $items as $item ) {
		// Top-level items only (parent 0) — nested flyouts stay single-column.
		if ( 0 !== (int) $item->menu_item_parent ) {
			continue;
		}

		$count = isset( $child_counts[ (int) $item->ID ] ) ? (int) $child_counts[ (int) $item->ID ] : 0;
		if ( $count <= $per_column ) {
			continue;
		}

		$columns           = (int) ceil( $count / $per_column );
		$item->classes[]   = 'has-multi-column';
		$item->classes[]   = 'menu-columns-' . $columns;
	}

	return $items;
}
add_filter( 'wp_nav_menu_objects', 'evently_mark_multi_column_menu_items', 10, 2 );
