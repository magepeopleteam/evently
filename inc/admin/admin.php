<?php
/**
 * Admin-only bootstrap: Evently → Theme Settings + Evently Setup wizard
 * (brief §28, §31). Everything here is gated behind is_admin() so none of
 * it parses/executes on front-end requests.
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! is_admin() ) {
	return;
}

foreach ( array( 'theme-settings.php', 'setup-wizard.php' ) as $evently_admin_file ) {
	if ( is_readable( __DIR__ . '/' . $evently_admin_file ) ) {
		require_once __DIR__ . '/' . $evently_admin_file;
	}
}
unset( $evently_admin_file );

/**
 * Top-level "Evently" admin menu, with Theme Settings and Setup as
 * submenus (brief §28's "Evently Setup" + §31's "Evently → Theme Settings").
 *
 * @return void
 */
function evently_register_admin_menu() {
	$capability = 'manage_options';

	add_menu_page(
		__( 'Evently', 'evently' ),
		__( 'Evently', 'evently' ),
		$capability,
		'evently',
		'evently_render_setup_wizard_page',
		'dashicons-calendar-alt',
		59 // Just below Comments, above Plugins is 65 — keep it near other content menus.
	);

	add_submenu_page(
		'evently',
		__( 'Evently Setup', 'evently' ),
		__( 'Setup', 'evently' ),
		$capability,
		'evently',
		'evently_render_setup_wizard_page'
	);

	add_submenu_page(
		'evently',
		__( 'Evently Theme Settings', 'evently' ),
		__( 'Theme Settings', 'evently' ),
		$capability,
		'evently-settings',
		'evently_render_theme_settings_page'
	);
}
add_action( 'admin_menu', 'evently_register_admin_menu' );

/**
 * Admin-only CSS/JS. Full Evently admin CSS only on Evently's own screens,
 * but admin-setup.js (and its localized eventlyAdmin nonce/strings) also
 * has to load on *every* wp-admin screen whenever
 * evently_required_plugins_notice() (inc/admin/setup-wizard.php) is showing
 * there — otherwise its Install & Activate buttons render with no click
 * handler wired up anywhere outside Evently's own pages.
 *
 * @param string $hook
 * @return void
 */
function evently_admin_enqueue( $hook ) {
	$on_evently_screen = isset( $_GET['page'] ) && 0 === strpos( (string) $_GET['page'], 'evently' ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only screen check, not a form submission.
	$notice_showing    = ! evently_has_elementor() || ! evently_has_booking_plugin();

	if ( ! $on_evently_screen && ! $notice_showing ) {
		return;
	}

	$script_deps = array();
	if ( $on_evently_screen ) {
		$script_deps = array( 'jquery', 'wp-color-picker' );
		wp_enqueue_style(
			'evently-admin-font',
			'https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap',
			array(),
			null
		);
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style( 'evently-admin', EVENTLY_URI . 'assets/css/admin.css', array( 'wp-color-picker', 'evently-admin-font' ), EVENTLY_VERSION );
	}

	wp_enqueue_script( 'evently-admin', EVENTLY_URI . 'assets/js/admin-setup.js', $script_deps, EVENTLY_VERSION, true );
	wp_localize_script(
		'evently-admin',
		'eventlyAdmin',
		array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'evently_admin_nonce' ),
			'strings' => array(
				'importing' => __( 'Importing…', 'evently' ),
				'done'      => __( 'Done', 'evently' ),
				'error'     => __( 'Something went wrong. Please try again.', 'evently' ),
			),
		)
	);
}
add_action( 'admin_enqueue_scripts', 'evently_admin_enqueue' );
