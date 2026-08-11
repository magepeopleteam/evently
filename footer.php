<?php
/**
 * The footer for the theme.
 *
 * When Elementor Pro Theme Builder provides a Footer location, that template
 * replaces Evently's site-footer; otherwise the theme footer prints as usual.
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
</main>

<?php
/**
 * Fires right after </main>, before the site footer markup.
 */
do_action( 'evently_before_footer' );

if ( ! function_exists( 'evently_elementor_location' ) || ! evently_elementor_location( 'footer' ) ) {
	evently_template_part( 'template-parts/footer/site-footer' );
}

/**
 * Fires right after the site footer markup, before </body>.
 */
do_action( 'evently_after_footer' );

// Global modals — present once per page, triggered from anywhere (the
// header search icon, mobile filter buttons, etc.).
evently_template_part( 'template-parts/modals/search-modal' );

wp_footer();
?>
</body>
</html>
