<?php
/**
 * Default single-post template. See docs/blog.md for how the Event Journal
 * homepage cards relate to this (same posts, different card component).
 *
 * Elementor Pro Theme Builder `single` location can replace this entirely.
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
		? 'evently-single-post evently-single-post--elementor'
		: 'evently-single-post evently-container evently-section';
	?>
	<article <?php post_class( $evently_classes ); ?>>
		<?php while ( have_posts() ) : ?>
			<?php the_post(); ?>

			<?php evently_render_singular_title( 'post' ); ?>

			<?php if ( ! $evently_is_builder && has_post_thumbnail() ) : ?>
				<div class="evently-single-post__thumb">
					<?php the_post_thumbnail( 'evently-featured', array( 'loading' => 'lazy' ) ); ?>
				</div>
			<?php endif; ?>

			<div class="evently-single-post__content">
				<?php the_content(); ?>
			</div>

			<?php
			if ( ! $evently_is_builder ) {
				wp_link_pages(
					array(
						'before' => '<nav class="evently-page-links">' . esc_html__( 'Pages:', 'evently' ),
						'after'  => '</nav>',
					)
				);

				evently_template_part( 'template-parts/content/related-posts' );

				if ( comments_open() || get_comments_number() ) {
					comments_template();
				}
			}
			?>
		<?php endwhile; ?>
	</article>
	<?php
endif;

get_footer();
