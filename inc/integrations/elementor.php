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

	// The remaining 12 homepage sections — simple delegates, same shape as
	// Hero/Categories above (zero controls, content from Theme Settings),
	// giving full 1:1 Elementor parity with all 14 evently/{slug} Gutenberg
	// blocks. Event Grid/Event Search/CTA above stay as independently
	// configurable, general-purpose widgets — this isn't replacing them.
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

	\Elementor\Plugin::instance()->widgets_manager->register( new \Evently_Elementor_Widget_Hero() );
	\Elementor\Plugin::instance()->widgets_manager->register( new \Evently_Elementor_Widget_Event_Grid() );
	\Elementor\Plugin::instance()->widgets_manager->register( new \Evently_Elementor_Widget_Categories() );
	\Elementor\Plugin::instance()->widgets_manager->register( new \Evently_Elementor_Widget_Event_Search() );
	\Elementor\Plugin::instance()->widgets_manager->register( new \Evently_Elementor_Widget_Cta() );

	\Elementor\Plugin::instance()->widgets_manager->register( new \Evently_Elementor_Widget_Trending_Events() );
	\Elementor\Plugin::instance()->widgets_manager->register( new \Evently_Elementor_Widget_Featured_Event() );
	\Elementor\Plugin::instance()->widgets_manager->register( new \Evently_Elementor_Widget_Choose_Vibe() );
	\Elementor\Plugin::instance()->widgets_manager->register( new \Evently_Elementor_Widget_Near_You() );
	\Elementor\Plugin::instance()->widgets_manager->register( new \Evently_Elementor_Widget_Calendar() );
	\Elementor\Plugin::instance()->widgets_manager->register( new \Evently_Elementor_Widget_How_It_Works() );
	\Elementor\Plugin::instance()->widgets_manager->register( new \Evently_Elementor_Widget_Digital_Ticket() );
	\Elementor\Plugin::instance()->widgets_manager->register( new \Evently_Elementor_Widget_Organizer_Cta() );
	\Elementor\Plugin::instance()->widgets_manager->register( new \Evently_Elementor_Widget_Stats() );
	\Elementor\Plugin::instance()->widgets_manager->register( new \Evently_Elementor_Widget_Testimonials() );
	\Elementor\Plugin::instance()->widgets_manager->register( new \Evently_Elementor_Widget_Event_Journal() );
	\Elementor\Plugin::instance()->widgets_manager->register( new \Evently_Elementor_Widget_Final_Cta() );
}

/**
 * Wire everything up directly on Elementor's own action hooks — no
 * `did_action( 'elementor/loaded' )` gate needed (and one previously here
 * was actually a bug: Elementor fires `elementor/loaded` AFTER the
 * `plugins_loaded` callback chain has already reached the theme's own
 * `plugins_loaded` callbacks, so that check was always false and these two
 * hooks never attached — meaning every Evently Elementor widget silently
 * failed to register). Registering straight on Elementor's hooks is already
 * "entirely optional" by construction: if Elementor never loads, these
 * actions simply never fire, so nothing here ever runs.
 */
add_action( 'elementor/elements/categories_registered', 'evently_elementor_register_category' );
add_action( 'elementor/widgets/register', 'evently_elementor_register_widgets' );
