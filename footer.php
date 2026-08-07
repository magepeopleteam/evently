<?php
/**
 * The footer for the theme.
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

evently_template_part( 'template-parts/footer/site-footer' );

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
