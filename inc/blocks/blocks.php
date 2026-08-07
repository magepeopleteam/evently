<?php
/**
 * Gutenberg block registrations that don't need a JS build step:
 * `register_block_style()` variations so core blocks (Button, Group,
 * Separator) can pick up Evently's design language directly from the
 * block editor's Styles panel — no custom block type needed for these.
 *
 * The theme's 14 full sections (Hero, Event Grid, Categories, …) are
 * delivered as block PATTERNS instead of custom block types — see
 * inc/patterns/patterns.php for why (real, adapter-backed content with
 * zero JS build tooling required, since patterns are just registered
 * block markup).
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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
