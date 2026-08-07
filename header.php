<?php
/**
 * The header for the theme.
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<?php
/**
 * Fires right after <body> opens, before the site header markup.
 * Evently hooks its skip-link here by default — see inc/template-hooks.php.
 */
do_action( 'evently_before_header' );

evently_template_part( 'template-parts/header/site-header' );

/**
 * Fires right after the site header markup, before <main>.
 */
do_action( 'evently_after_header' );
?>

<main id="evently-main" class="evently-main">
