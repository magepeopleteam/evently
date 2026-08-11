<?php
/**
 * Elementor compatibility (brief §26).
 *
 * Fully optional: if Elementor never loads, none of these hooks fire.
 * Covers:
 *  - Evently widget category + homepage/section widgets
 *  - Theme Builder locations (header/footer/single/archive) when Pro is present
 *  - Page Settings → Hide Title
 *  - Full Width / Canvas page templates
 *  - Editor preview styles
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether a post is built with the Elementor editor.
 *
 * @param int $post_id Post ID. 0 = current queried object.
 * @return bool
 */
function evently_is_elementor_built( $post_id = 0 ) {
	if ( ! $post_id ) {
		$post_id = get_queried_object_id();
	}
	if ( ! $post_id ) {
		return false;
	}
	return 'builder' === get_post_meta( $post_id, '_elementor_edit_mode', true );
}

/**
 * Try an Elementor Theme Builder location (Pro). Returns true when a
 * location template was rendered so the theme should skip its fallback.
 *
 * Safe no-op when Elementor Pro is inactive (`elementor_theme_do_location`
 * is undefined).
 *
 * @param string $location header|footer|single|archive.
 * @return bool
 */
function evently_elementor_location( $location ) {
	return function_exists( 'elementor_theme_do_location' ) && elementor_theme_do_location( $location );
}

/**
 * Register all core Theme Builder locations so Evently can be fully
 * overridden by Elementor Pro Header/Footer/Single/Archive templates.
 *
 * @param \ElementorPro\Modules\ThemeBuilder\Classes\Locations_Manager $manager Locations manager.
 * @return void
 */
function evently_elementor_register_locations( $manager ) {
	if ( method_exists( $manager, 'register_all_core_location' ) ) {
		$manager->register_all_core_location();
	}
}
add_action( 'elementor/theme/register_locations', 'evently_elementor_register_locations' );

/**
 * Body classes that help CSS adapt when Elementor owns the page chrome.
 *
 * @param string[] $classes Body classes.
 * @return string[]
 */
function evently_elementor_body_classes( $classes ) {
	if ( ! evently_has_elementor() ) {
		return $classes;
	}

	if ( is_singular() && evently_is_elementor_built() ) {
		$classes[] = 'evently-elementor-built';
	}

	if ( function_exists( 'evently_elementor_hides_title' ) && evently_elementor_hides_title() ) {
		$classes[] = 'evently-elementor-hide-title';
	}

	return $classes;
}
add_filter( 'body_class', 'evently_elementor_body_classes' );

/**
 * Enqueue theme design tokens inside the Elementor editor preview.
 *
 * @return void
 */
function evently_elementor_editor_styles() {
	wp_enqueue_style( 'evently-variables' );
	wp_enqueue_style( 'evently-typography' );
	wp_enqueue_style( 'evently-components' );
}
add_action( 'elementor/editor/after_enqueue_styles', 'evently_elementor_editor_styles' );

/**
 * Frontend CSS tweaks that only matter when Elementor is active.
 *
 * @return void
 */
function evently_elementor_frontend_styles() {
	if ( ! evently_has_elementor() ) {
		return;
	}

	$css = '
		/* Theme Builder header replaces .site-header — drop fixed-header clearance. */
		body:not(:has(#evently-site-header)) .evently-main {
			padding-top: 0;
		}
		/* Let Elementor stretched sections escape theme wrappers. */
		body.elementor-page .evently-main,
		body.elementor-page .evently-page,
		body.elementor-page .evently-page__content,
		body.evently-elementor-built .evently-main {
			overflow: visible;
		}
		/* Elementor-built pages: no theme max-width cage around the canvas. */
		body.evently-elementor-built .evently-page.evently-container,
		body.evently-elementor-built .evently-single-post.evently-container {
			max-width: none;
			padding-left: 0;
			padding-right: 0;
		}
		body.evently-elementor-built .evently-page.evently-section,
		body.evently-elementor-built .evently-single-post.evently-section {
			padding-left: 0;
			padding-right: 0;
		}
		/* Canvas / full-bleed builder pages keep content edge-to-edge. */
		body.elementor-template-canvas .evently-main,
		body.elementor-template-full-width .evently-page__content {
			padding-left: 0;
			padding-right: 0;
		}
	';

	wp_register_style( 'evently-elementor', false, array( 'evently-layout' ), EVENTLY_VERSION );
	wp_enqueue_style( 'evently-elementor' );
	wp_add_inline_style( 'evently-elementor', preg_replace( '/\s+/', ' ', trim( $css ) ) );
}
add_action( 'wp_enqueue_scripts', 'evently_elementor_frontend_styles', 30 );

/**
 * Register the "Evently" widget category in Elementor's panel.
 *
 * @param \Elementor\Elements_Manager $elements_manager Elements manager.
 * @return void
 */
function evently_elementor_register_category( $elements_manager ) {
	$elements_manager->add_category(
		'evently',
		array(
			'title' => __( 'Evently', 'evently' ),
			'icon'  => 'eicon-calendar',
		)
	);
}

/**
 * Register Evently's Elementor widgets once Elementor's widget manager is ready.
 *
 * @return void
 */
function evently_elementor_register_widgets() {
	require_once EVENTLY_DIR . 'inc/integrations/elementor/class-widget-base.php';
	require_once EVENTLY_DIR . 'inc/integrations/elementor/class-widget-hero.php';
	require_once EVENTLY_DIR . 'inc/integrations/elementor/class-widget-event-grid.php';
	require_once EVENTLY_DIR . 'inc/integrations/elementor/class-widget-categories.php';
	require_once EVENTLY_DIR . 'inc/integrations/elementor/class-widget-event-search.php';
	require_once EVENTLY_DIR . 'inc/integrations/elementor/class-widget-cta.php';
	require_once EVENTLY_DIR . 'inc/integrations/elementor/class-widget-trending-events.php';
	require_once EVENTLY_DIR . 'inc/integrations/elementor/class-widget-featured-event.php';
	require_once EVENTLY_DIR . 'inc/integrations/elementor/class-widget-choose-vibe.php';
	require_once EVENTLY_DIR . 'inc/integrations/elementor/class-widget-near-you.php';
	require_once EVENTLY_DIR . 'inc/integrations/elementor/class-widget-calendar.php';
	require_once EVENTLY_DIR . 'inc/integrations/elementor/class-widget-how-it-works.php';
	require_once EVENTLY_DIR . 'inc/integrations/elementor/class-widget-digital-ticket.php';
	require_once EVENTLY_DIR . 'inc/integrations/elementor/class-widget-organizer-cta.php';
	require_once EVENTLY_DIR . 'inc/integrations/elementor/class-widget-stats.php';
	require_once EVENTLY_DIR . 'inc/integrations/elementor/class-widget-testimonials.php';
	require_once EVENTLY_DIR . 'inc/integrations/elementor/class-widget-event-journal.php';
	require_once EVENTLY_DIR . 'inc/integrations/elementor/class-widget-final-cta.php';

	$widgets = array(
		'Evently_Elementor_Widget_Hero',
		'Evently_Elementor_Widget_Event_Grid',
		'Evently_Elementor_Widget_Categories',
		'Evently_Elementor_Widget_Event_Search',
		'Evently_Elementor_Widget_Cta',
		'Evently_Elementor_Widget_Trending_Events',
		'Evently_Elementor_Widget_Featured_Event',
		'Evently_Elementor_Widget_Choose_Vibe',
		'Evently_Elementor_Widget_Near_You',
		'Evently_Elementor_Widget_Calendar',
		'Evently_Elementor_Widget_How_It_Works',
		'Evently_Elementor_Widget_Digital_Ticket',
		'Evently_Elementor_Widget_Organizer_Cta',
		'Evently_Elementor_Widget_Stats',
		'Evently_Elementor_Widget_Testimonials',
		'Evently_Elementor_Widget_Event_Journal',
		'Evently_Elementor_Widget_Final_Cta',
	);

	foreach ( $widgets as $widget_class ) {
		\Elementor\Plugin::instance()->widgets_manager->register( new $widget_class() );
	}
}

add_action( 'elementor/elements/categories_registered', 'evently_elementor_register_category' );
add_action( 'elementor/widgets/register', 'evently_elementor_register_widgets' );

/**
 * Elementor Full Width template skips page.php — print the theme title
 * (unless Page Settings → Hide Title is on).
 *
 * @return void
 */
function evently_elementor_print_page_title() {
	if ( ! function_exists( 'evently_render_singular_title' ) || ! evently_should_render_singular_title() ) {
		return;
	}

	if ( ! is_singular( array( 'page', 'post' ) ) ) {
		return;
	}

	$context = is_singular( 'post' ) ? 'post' : 'page';

	echo '<div class="evently-container evently-elementor-page-title">';
	evently_render_singular_title( $context );
	echo '</div>';
}
add_action( 'elementor/page_templates/header-footer/before_content', 'evently_elementor_print_page_title' );
