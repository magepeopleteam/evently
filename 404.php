<?php
/**
 * 404 template.
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<div class="evently-container evently-section evently-404">
	<div class="evently-empty-state">
		<div class="evently-empty-state__icon"><?php evently_icon( 'empty-events' ); ?></div>
		<h1><?php esc_html_e( "This page took a wrong turn.", 'evently' ); ?></h1>
		<p><?php esc_html_e( "We couldn't find what you were looking for. It may have moved, sold out, or never existed.", 'evently' ); ?></p>
		<div class="evently-empty-state__action">
			<?php
			evently_button(
				array(
					'text'    => __( 'Explore Events', 'evently' ),
					'url'     => evently_get_events_page_url(),
					'variant' => 'primary',
				)
			);
			?>
		</div>
	</div>
</div>

<?php
get_footer();
