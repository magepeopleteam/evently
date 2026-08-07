<?php
/**
 * Elementor compatibility (brief §26). Elementor is entirely optional —
 * every hook/class below is registered only when Elementor has actually
 * loaded, so this file is a complete no-op on a site without it (the
 * current state of this environment). Every widget renders through the
 * exact same functions the Gutenberg patterns and the live homepage use
 * (evently_capture_template_part(), evently_event_grid(), etc.) — there is
 * exactly one implementation of "render an event grid", never a
 * duplicated Elementor-specific one.
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Remove Elementor's own content-width constraints on Evently's full-bleed
 * sections (hero, featured event, organizer CTA, final CTA all intentionally
 * run edge-to-edge) — CSS only, safe even if Elementor never loads.
 *
 * @return void
 */
function evently_elementor_editor_styles() {
	if ( ! evently_has_elementor() ) {
		return;
	}
	wp_enqueue_style( 'evently-variables' );
	wp_enqueue_style( 'evently-typography' );
	wp_enqueue_style( 'evently-components' );
}
add_action( 'elementor/editor/after_enqueue_styles', 'evently_elementor_editor_styles' );

/**
 * Register the "Evently" widget category in Elementor's panel.
 *
 * @param \Elementor\Elements_Manager $elements_manager
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

	\Elementor\Plugin::instance()->widgets_manager->register( new \Evently_Elementor_Widget_Hero() );
	\Elementor\Plugin::instance()->widgets_manager->register( new \Evently_Elementor_Widget_Event_Grid() );
	\Elementor\Plugin::instance()->widgets_manager->register( new \Evently_Elementor_Widget_Categories() );
	\Elementor\Plugin::instance()->widgets_manager->register( new \Evently_Elementor_Widget_Event_Search() );
	\Elementor\Plugin::instance()->widgets_manager->register( new \Evently_Elementor_Widget_Cta() );
}

/**
 * Wire everything up, but only once Elementor itself has confirmed it loaded.
 *
 * @return void
 */
function evently_elementor_init() {
	if ( ! did_action( 'elementor/loaded' ) ) {
		return;
	}
	add_action( 'elementor/elements/categories_registered', 'evently_elementor_register_category' );
	add_action( 'elementor/widgets/register', 'evently_elementor_register_widgets' );
}
add_action( 'plugins_loaded', 'evently_elementor_init' );
