<?php
/**
 * Conditional asset loading.
 *
 * Every stylesheet/script here is registered first (cheap) and only
 * enqueued on the templates that actually need it (brief §33 — "Do not
 * load booking/dashboard scripts globally").
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Font loading: use a locally bundled Plus Jakarta Sans if the site owner has
 * dropped woff2 files into assets/fonts/ (recommended before ThemeForest
 * submission — avoids a third-party request to Google Fonts for GDPR/perf
 * reasons), otherwise fall back to the Google Fonts CDN so the theme still
 * looks correct out of the box.
 *
 * @return void
 */
function evently_enqueue_fonts() {
	$local_font = EVENTLY_DIR . 'assets/fonts/plus-jakarta-sans.css';

	if ( is_readable( $local_font ) ) {
		wp_enqueue_style( 'evently-fonts', EVENTLY_URI . 'assets/fonts/plus-jakarta-sans.css', array(), EVENTLY_VERSION );
		return;
	}

	wp_enqueue_style(
		'evently-fonts',
		'https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap',
		array(),
		null // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion -- third-party URL; versioning it would break Google Fonts' own cache-busting.
	);
}

/**
 * Preconnect to Google Fonts only when we're actually using the CDN fallback.
 *
 * @return void
 */
function evently_resource_hints() {
	if ( is_readable( EVENTLY_DIR . 'assets/fonts/plus-jakarta-sans.css' ) ) {
		return;
	}
	echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
	echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
}
add_action( 'wp_head', 'evently_resource_hints', 1 );

/**
 * Evently → Theme Settings → Colors (brief §32) overrides the locked design
 * tokens only when a site owner explicitly changes them — printed as a
 * tiny inline override so assets/css/variables.css never needs a build
 * step to reflect a color change.
 *
 * @return void
 */
function evently_print_color_overrides() {
	$overrides = array(
		'--evently-primary' => evently_get_setting( 'color_primary', '#6C5CE7' ),
		'--evently-orange'  => evently_get_setting( 'color_orange', '#FF7657' ),
		'--evently-dark'    => evently_get_setting( 'color_dark', '#0B0B0D' ),
	);

	$defaults = array( '--evently-primary' => '#6C5CE7', '--evently-orange' => '#FF7657', '--evently-dark' => '#0B0B0D' );
	$changed  = array_diff_assoc( $overrides, $defaults );

	if ( empty( $changed ) ) {
		return;
	}

	echo '<style id="evently-color-overrides">:root{';
	foreach ( $changed as $property => $value ) {
		printf( '%s:%s;', esc_attr( $property ), esc_attr( $value ) );
	}
	echo '}</style>' . "\n";
}
add_action( 'wp_head', 'evently_print_color_overrides', 5 );

/**
 * Register every theme stylesheet/script up front (cheap — no I/O beyond the
 * function definitions below), then enqueue only what the current template needs.
 *
 * @return void
 */
function evently_register_assets() {
	$css_dir = EVENTLY_URI . 'assets/css/';
	$js_dir  = EVENTLY_URI . 'assets/js/';
	$ver     = EVENTLY_VERSION;

	// Design-system core — needed on every page, deliberately small & split so
	// a reviewer (or a child theme) can see exactly what governs what.
	$core_styles = array(
		'evently-variables'   => 'variables.css',
		'evently-base'        => 'base.css',
		'evently-typography'  => 'typography.css',
		'evently-layout'      => 'layout.css',
		'evently-components'  => 'components.css',
	);
	$deps = array();
	foreach ( $core_styles as $handle => $file ) {
		wp_register_style( $handle, $css_dir . $file, $deps, $ver );
		$deps[] = $handle;
	}
	// $deps now holds every core handle in load order — reused as the
	// dependency chain for section-specific stylesheets below.
	$core_deps = $deps;

	// Section-specific styles, only enqueued on the templates that render that markup.
	wp_register_style( 'evently-header', $css_dir . 'header.css', $core_deps, $ver );
	wp_register_style( 'evently-hero', $css_dir . 'hero.css', $core_deps, $ver );
	wp_register_style( 'evently-events', $css_dir . 'events.css', $core_deps, $ver );
	wp_register_style( 'evently-home', $css_dir . 'home.css', array_merge( $core_deps, array( 'evently-events' ) ), $ver );
	wp_register_style( 'evently-archive', $css_dir . 'archive.css', $core_deps, $ver );
	wp_register_style( 'evently-single-event', $css_dir . 'single-event.css', $core_deps, $ver );
	wp_register_style( 'evently-plugin-event-details', $css_dir . 'plugin-event-details.css', $core_deps, $ver );
	wp_register_style( 'evently-booking', $css_dir . 'booking.css', $core_deps, $ver );
	wp_register_style( 'evently-dashboard', $css_dir . 'dashboard.css', $core_deps, $ver );
	wp_register_style( 'evently-blog', $css_dir . 'blog.css', $core_deps, $ver );
	wp_register_style( 'evently-responsive', $css_dir . 'responsive.css', array_merge( $core_deps, array( 'evently-header', 'evently-hero', 'evently-events', 'evently-home', 'evently-blog' ) ), $ver );

	// Scripts — vanilla JS, in_footer, no framework (brief §38).
	wp_register_script( 'evently-navigation', $js_dir . 'navigation.js', array(), $ver, array( 'in_footer' => true, 'strategy' => 'defer' ) );
	wp_register_script( 'evently-favorites', $js_dir . 'favorites.js', array(), $ver, array( 'in_footer' => true, 'strategy' => 'defer' ) );
	wp_register_script( 'evently-search', $js_dir . 'search.js', array(), $ver, array( 'in_footer' => true, 'strategy' => 'defer' ) );
	wp_register_script( 'evently-filters', $js_dir . 'filters.js', array(), $ver, array( 'in_footer' => true, 'strategy' => 'defer' ) );
	wp_register_script( 'evently-calendar', $js_dir . 'calendar.js', array(), $ver, array( 'in_footer' => true, 'strategy' => 'defer' ) );
	wp_register_script( 'evently-carousel', $js_dir . 'carousel.js', array(), $ver, array( 'in_footer' => true, 'strategy' => 'defer' ) );
	wp_register_script( 'evently-modal', $js_dir . 'modal.js', array(), $ver, array( 'in_footer' => true, 'strategy' => 'defer' ) );
	wp_register_script( 'evently-gallery-lightbox', $js_dir . 'gallery-lightbox.js', array( 'evently-modal', 'jquery' ), $ver, array( 'in_footer' => true, 'strategy' => 'defer' ) );
	wp_register_script( 'evently-booking-form', $js_dir . 'booking-form.js', array(), $ver, array( 'in_footer' => true, 'strategy' => 'defer' ) );
	wp_register_script( 'evently-single-event', $js_dir . 'single-event.js', array(), $ver, array( 'in_footer' => true, 'strategy' => 'defer' ) );
	// Attendee drawer needs jQuery + plugin qty/clone scripts (mpwem_script).
	wp_register_script(
		'evently-attendee-drawer',
		$js_dir . 'attendee-drawer.js',
		array( 'jquery' ),
		$ver,
		array( 'in_footer' => true )
	);
}
add_action( 'init', 'evently_register_assets' );

/**
 * Decide what the current request actually needs and enqueue it.
 *
 * @return void
 */
function evently_enqueue_assets() {
	evently_enqueue_fonts();

	wp_enqueue_style( 'evently-variables' );
	wp_enqueue_style( 'evently-base' );
	wp_enqueue_style( 'evently-typography' );
	wp_enqueue_style( 'evently-layout' );
	wp_enqueue_style( 'evently-components' );
	wp_enqueue_style( 'evently-header' );
	wp_enqueue_script( 'evently-navigation' );
	wp_enqueue_script( 'evently-modal' ); // Backs the header's quick-search modal on every page.

	$is_event_context = evently_has_booking_plugin() && ( is_singular( 'mep_events' ) || is_post_type_archive( 'mep_events' ) || is_tax( array( 'mep_cat', 'mep_org', 'mep_tag' ) ) );

	if ( is_front_page() ) {
		wp_enqueue_style( 'evently-hero' );
		wp_enqueue_style( 'evently-events' );
		wp_enqueue_style( 'evently-home' );
		wp_enqueue_style( 'evently-blog' ); // Event Journal teaser section reuses the real blog's editorial card styles.
		wp_enqueue_script( 'evently-search' );
		wp_enqueue_script( 'evently-favorites' );
		wp_enqueue_script( 'evently-calendar' );
		wp_enqueue_script( 'evently-carousel' );
		wp_enqueue_script( 'evently-filters' ); // Backs Choose Your Vibe's tab switching (template-parts/home/choose-vibe.php) — homepage-only section, so it needs its own enqueue here too, not just the Event Archive's.
	}

	if ( $is_event_context || is_page_template( 'page-templates/event-archive.php' ) ) {
		wp_enqueue_style( 'evently-events' );
		wp_enqueue_style( 'evently-archive' );
		wp_enqueue_script( 'evently-search' );
		wp_enqueue_script( 'evently-filters' );
		wp_enqueue_script( 'evently-favorites' );
	}

	// Theme single-event skin assets — skip when Theme Settings loads the
	// plugin's own details template (plugin enqueues its own CSS/JS).
	if ( evently_has_booking_plugin() && is_singular( 'mep_events' ) && ! evently_use_plugin_event_details() ) {
		wp_enqueue_style( 'evently-events' );
		// Load after the plugin's ticket CSS so our booking-card overrides win.
		$evently_single_deps = array( 'evently-components' );
		foreach ( array( 'mpwem_style', 'mpwem_global' ) as $evently_plugin_style ) {
			if ( wp_style_is( $evently_plugin_style, 'registered' ) || wp_style_is( $evently_plugin_style, 'enqueued' ) ) {
				$evently_single_deps[] = $evently_plugin_style;
			}
		}
		$evently_styles = wp_styles();
		if ( isset( $evently_styles->registered['evently-single-event'] ) ) {
			$evently_styles->registered['evently-single-event']->deps = array_values(
				array_unique(
					array_merge(
						$evently_styles->registered['evently-single-event']->deps,
						$evently_single_deps
					)
				)
			);
		}
		wp_enqueue_style( 'evently-single-event' );
		wp_enqueue_style( 'evently-booking' );
		wp_enqueue_script( 'evently-modal' );

		// Gallery strip uses the booking plugin's Owl Carousel (already
		// registered as mp_owl_carousel). Re-register as a fallback if the
		// site opted out of the plugin's own enqueue.
		if ( defined( 'MPWEM_PLUGIN_URL' ) && ! wp_script_is( 'mp_owl_carousel', 'registered' ) ) {
			wp_register_style( 'mp_owl_carousel', MPWEM_PLUGIN_URL . '/assets/helper/owl_carousel/owl.carousel.min.css', array(), '2.3.4' );
			wp_register_script( 'mp_owl_carousel', MPWEM_PLUGIN_URL . '/assets/helper/owl_carousel/owl.carousel.min.js', array( 'jquery' ), '2.3.4', true );
		}
		if ( wp_script_is( 'mp_owl_carousel', 'registered' ) ) {
			wp_enqueue_style( 'mp_owl_carousel' );
			wp_enqueue_script( 'mp_owl_carousel' );
			$evently_scripts = wp_scripts();
			if ( isset( $evently_scripts->registered['evently-gallery-lightbox'] ) ) {
				$evently_scripts->registered['evently-gallery-lightbox']->deps[] = 'mp_owl_carousel';
			}
		}

		wp_enqueue_script( 'evently-gallery-lightbox' );
		wp_enqueue_script( 'evently-booking-form' );
		wp_enqueue_script( 'evently-single-event' );
		wp_localize_script(
			'evently-single-event',
			'eventlySingleI18n',
			array(
				'rateEvent'     => __( 'How would you rate this event?', 'evently' ),
				'writeReview'   => __( 'Write a review', 'evently' ),
				'submitReview'  => __( 'Submit review', 'evently' ),
				'eventReview'   => __( 'Event review', 'evently' ),
				'close'         => __( 'Close', 'evently' ),
				'noReviews'     => __( 'No reviews yet', 'evently' ),
				'beFirst'       => __( 'Be the first to share your experience.', 'evently' ),
				'starSingular'  => __( '1 star', 'evently' ),
				/* translators: %d: star rating 2–5 */
				'starPlural'    => __( '%d stars', 'evently' ),
				'zoomGallery'   => __( 'View gallery', 'evently' ),
				/* translators: %d: number of gallery photos */
				'galleryCount'  => __( '%d photos', 'evently' ),
				'prevImage'     => __( 'Previous image', 'evently' ),
				'nextImage'     => __( 'Next image', 'evently' ),
			)
		);

		// Per-ticket attendee form drawer (Horizon-parity UX for Evently template).
		$evently_attendee_deps = array( 'jquery' );
		if ( wp_script_is( 'mpwem_script', 'registered' ) || wp_script_is( 'mpwem_script', 'enqueued' ) ) {
			$evently_attendee_deps[] = 'mpwem_script';
		}
		$evently_scripts = wp_scripts();
		if ( isset( $evently_scripts->registered['evently-attendee-drawer'] ) ) {
			$evently_scripts->registered['evently-attendee-drawer']->deps = array_values(
				array_unique(
					array_merge(
						$evently_scripts->registered['evently-attendee-drawer']->deps,
						$evently_attendee_deps
					)
				)
			);
		}
		wp_enqueue_script( 'evently-attendee-drawer' );
		$evently_same_attendee = 'no';
		if ( class_exists( 'MPWEM_Global_Function' ) && method_exists( 'MPWEM_Global_Function', 'get_settings' ) ) {
			$evently_same_attendee = (string) MPWEM_Global_Function::get_settings( 'general_setting_sec', 'mep_enable_same_attendee', 'no' );
		}
		wp_localize_script(
			'evently-attendee-drawer',
			'eventlyAttendeeI18n',
			array(
				'attendeeDetails'     => __( 'Enter attendee details', 'evently' ),
				'attendeeEdit'        => __( 'Edit', 'evently' ),
				'attendeeDrawerTitle' => __( 'Attendee details', 'evently' ),
				'attendeeDrawerHelp'  => __( 'Complete the required fields for this ticket, then save.', 'evently' ),
				'attendeeContinue'    => __( 'Save attendee details', 'evently' ),
				'attendeeIncomplete'  => __( 'Required', 'evently' ),
				'attendeeAdded'       => __( 'Attendee details added', 'evently' ),
				'attendeeForTicket'   => __( 'Attendees for %s', 'evently' ),
				'attendeeCardLabel'   => __( 'Attendee', 'evently' ),
				'close'               => __( 'Close', 'evently' ),
				'sameAttendee'        => $evently_same_attendee,
			)
		);
	}

	// Plugin details templates need Evently's fixed-header clearance + the
	// same max-width/pad track as .site-header / .evently-container.
	if ( evently_has_booking_plugin() && is_singular( 'mep_events' ) && evently_use_plugin_event_details() ) {
		// Load after Horizon CSS when that template is active so Evently font /
		// spacing overrides win over Outfit + Playfair and the extra hero top margin.
		if ( wp_style_is( 'mpwem_horizon_theme', 'registered' ) ) {
			$evently_styles = wp_styles();
			if ( isset( $evently_styles->registered['evently-plugin-event-details'] ) ) {
				$evently_styles->registered['evently-plugin-event-details']->deps = array_values(
					array_unique(
						array_merge(
							$evently_styles->registered['evently-plugin-event-details']->deps,
							array( 'mpwem_horizon_theme' )
						)
					)
				);
			}
		}
		wp_enqueue_style( 'evently-plugin-event-details' );
		// Read more on description + Pro review modal polish (shared with theme skin).
		wp_enqueue_script( 'evently-single-event' );
		wp_localize_script(
			'evently-single-event',
			'eventlySingleI18n',
			array(
				'rateEvent'    => __( 'How would you rate this event?', 'evently' ),
				'writeReview'  => __( 'Write a review', 'evently' ),
				'submitReview' => __( 'Submit review', 'evently' ),
				'eventReview'  => __( 'Event review', 'evently' ),
				'close'        => __( 'Close', 'evently' ),
				'noReviews'    => __( 'No reviews yet', 'evently' ),
				'beFirst'      => __( 'Be the first to share your experience.', 'evently' ),
				'starSingular' => __( '1 star', 'evently' ),
				/* translators: %d: star rating 2–5 */
				'starPlural'   => __( '%d stars', 'evently' ),
				'zoomGallery'  => __( 'View gallery', 'evently' ),
				/* translators: %d: number of gallery photos */
				'galleryCount' => __( '%d photos', 'evently' ),
				'prevImage'    => __( 'Previous image', 'evently' ),
				'nextImage'    => __( 'Next image', 'evently' ),
			)
		);
	}

	if ( ( evently_has_woocommerce() && is_account_page() ) || evently_is_organizer_dashboard() ) {
		wp_enqueue_style( 'evently-dashboard' );
		wp_enqueue_script( 'evently-filters' ); // Organizer Dashboard tabs + My Account's own JS is enqueued by WooCommerce/the plugin.
		wp_enqueue_style( 'evently-events' );
	}

	if ( is_singular( 'post' ) || is_home() || ( is_archive() && ! $is_event_context ) || is_search() ) {
		wp_enqueue_style( 'evently-blog' );
	}

	if ( evently_has_woocommerce() && ( is_cart() || is_checkout() || is_account_page() || is_shop() || is_product() ) ) {
		wp_enqueue_style( 'evently-booking' );
	}

	wp_enqueue_style( 'evently-responsive' );

	// Comment reply script only where it's actually usable.
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'evently_enqueue_assets' );

/**
 * Whether the current request is the (theme-provided) Organizer Dashboard
 * page template. Declared here as a light stub; the real implementation
 * lives in inc/template-functions.php once that file loads (function_exists
 * guard keeps this file safe to load in isolation/tests).
 *
 * @return bool
 */
if ( ! function_exists( 'evently_is_organizer_dashboard' ) ) {
	function evently_is_organizer_dashboard() {
		return is_page_template( 'page-templates/organizer-dashboard.php' );
	}
}

/**
 * Editor styles so the block editor roughly matches the front end typography
 * and color palette (brief §25/§4 — Gutenberg support).
 *
 * @return void
 */
function evently_editor_assets() {
	add_theme_support( 'editor-styles' );
	add_editor_style(
		array(
			'assets/css/variables.css',
			'assets/css/base.css',
			'assets/css/typography.css',
			'assets/css/editor.css',
		)
	);
}
add_action( 'after_setup_theme', 'evently_editor_assets' );
