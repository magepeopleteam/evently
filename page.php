<?php
/**
 * Default template for static Pages.
 *
 * Elementor Pro Theme Builder `single` location can replace this entirely.
 * Elementor-built pages drop the boxed `.evently-container` cage so the
 * builder canvas can go full-bleed (kit Content Width still applies inside).
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

if ( ! function_exists( 'evently_elementor_location' ) || ! evently_elementor_location( 'single' ) ) :
	$evently_is_builder = function_exists( 'evently_is_elementor_built' ) && evently_is_elementor_built();
	$evently_classes    = $evently_is_builder
		? 'evently-page evently-page--elementor'
		: 'evently-page evently-container evently-section';
	?>
	<article <?php post_class( $evently_classes ); ?>>
		<?php while ( have_posts() ) : ?>
			<?php the_post(); ?>

			<?php evently_render_singular_title( 'page' ); ?>

			<?php if ( ! $evently_is_builder && has_post_thumbnail() ) : ?>
				<div class="evently-page__thumb">
					<?php the_post_thumbnail( 'evently-featured', array( 'loading' => 'lazy' ) ); ?>
				</div>
			<?php endif; ?>

			<div class="evently-page__content">
				<?php the_content(); ?>
			</div>

			<?php
			if ( ! $evently_is_builder && ( comments_open() || get_comments_number() ) ) {
				comments_template();
			}
			?>
		<?php endwhile; ?>
	</article>
	<?php
endif;

get_footer();
