<?php
/**
 * WooCommerce integration — styling only. WooCommerce is optional (brief
 * §27); every hook here checks class_exists('WooCommerce') implicitly by
 * living behind the evently_has_woocommerce() guard in inc/enqueue.php for
 * assets, and by WooCommerce itself never firing these hooks when it's
 * inactive. Evently never overrides WooCommerce's cart/checkout/payment
 * logic — only presentation (product gallery options, wrapper markup,
 * image sizes) and CSS (assets/css/booking.css).
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Declare WooCommerce support once WooCommerce itself has loaded.
 *
 * @return void
 */
function evently_woocommerce_setup() {
	add_theme_support(
		'woocommerce',
		array(
			'thumbnail_image_width' => 500,
			'gallery_thumbnail_image_width' => 120,
			'single_image_width'    => 700,
			'product_grid'           => array(
				'default_rows'    => 3,
				'min_rows'        => 1,
				'default_columns' => 3,
				'min_columns'     => 1,
				'max_columns'     => 4,
			),
		)
	);
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );
}
add_action( 'after_setup_theme', 'evently_woocommerce_setup' );

/**
 * Evently has no sidebar concept anywhere (no register_sidebar(), no
 * sidebar.php) — shop/product pages are meant to run full-width, same as
 * every other archive in the theme. Without this, WooCommerce's own
 * `woocommerce_sidebar` hook still calls get_sidebar( 'shop' ), and
 * since the theme has neither sidebar-shop.php nor sidebar.php,
 * WordPress core's locate_template() falls all the way through to its
 * own always-present (deprecated) wp-includes/theme-compat/sidebar.php
 * — a raw, unstyled dump of every page/archive/category on the site.
 * Removing the hook entirely (the standard approach for sidebar-less
 * WooCommerce themes) stops that fallback from ever being reached.
 *
 * @return void
 */
function evently_woocommerce_remove_sidebar() {
	remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );
}
add_action( 'init', 'evently_woocommerce_remove_sidebar' );

/**
 * Wrap WooCommerce's main content in Evently's container so shop/cart/
 * checkout/account pages get the theme's max-width + horizontal padding
 * without touching WooCommerce's own wrapper template files.
 *
 * @return void
 */
function evently_woocommerce_wrapper_start() {
	echo '<div class="evently-container evently-section--tight evently-woocommerce">';
}
add_action( 'woocommerce_before_main_content', 'evently_woocommerce_wrapper_start', 5 );

/**
 * @return void
 */
function evently_woocommerce_wrapper_end() {
	echo '</div>';
}
add_action( 'woocommerce_after_main_content', 'evently_woocommerce_wrapper_end', 15 );

/**
 * WooCommerce prints its own page title (h1) via woocommerce_page_title() —
 * keep it, but let evently's typography classes apply instead of relying on
 * unscoped `h1` rules colliding with the rest of the theme.
 *
 * @param string $title
 * @return string
 */
function evently_woocommerce_page_title_class( $title ) {
	return '<span class="evently-woocommerce-title">' . $title . '</span>';
}
add_filter( 'woocommerce_page_title', 'evently_woocommerce_page_title_class' );

/**
 * Reduce related/upsell product columns to 3 to match Evently's grid rhythm.
 *
 * @param int $columns
 * @return int
 */
function evently_woocommerce_related_products_columns() {
	return 3;
}
add_filter( 'woocommerce_related_products_columns', 'evently_woocommerce_related_products_columns' );

/**
 * @param array $args
 * @return array
 */
function evently_woocommerce_upsell_columns( $args ) {
	$args['columns'] = 3;
	return $args;
}
add_filter( 'woocommerce_upsell_display_args', 'evently_woocommerce_upsell_columns' );

/**
 * WooCommerce's default breadcrumb separator/wrapper — align markup with
 * Evently's .evently-breadcrumb component (CSS only, see booking.css).
 *
 * @param array $defaults
 * @return array
 */
function evently_woocommerce_breadcrumb_defaults( $defaults ) {
	$defaults['wrap_before'] = '<nav class="evently-breadcrumb evently-woocommerce-breadcrumb">';
	$defaults['wrap_after']  = '</nav>';
	$defaults['delimiter']   = ' <span aria-hidden="true">/</span> ';
	return $defaults;
}
add_filter( 'woocommerce_breadcrumb_defaults', 'evently_woocommerce_breadcrumb_defaults' );
