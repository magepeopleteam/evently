<?php
/**
 * Default single-post template. See docs/blog.md for how the Event Journal
 * homepage cards relate to this (same posts, different card component).
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<article <?php post_class( 'evently-single-post evently-container evently-section' ); ?>>
	<?php while ( have_posts() ) : ?>
		<?php the_post(); ?>

		<header class="evently-single-post__header">
			<?php
			$evently_categories = get_the_category();
			if ( ! empty( $evently_categories ) ) :
				?>
				<div class="evently-eyebrow evently-eyebrow--pill"><?php echo esc_html( $evently_categories[0]->name ); ?></div>
			<?php endif; ?>

			<h1 class="evently-single-post__title"><?php the_title(); ?></h1>

			<div class="evently-single-post__meta">
				<span><?php echo esc_html( get_the_date() ); ?></span>
				<span aria-hidden="true">·</span>
				<span><?php echo esc_html( get_the_author() ); ?></span>
			</div>
		</header>

		<?php if ( has_post_thumbnail() ) : ?>
			<div class="evently-single-post__thumb">
				<?php the_post_thumbnail( 'evently-featured', array( 'loading' => 'lazy' ) ); ?>
			</div>
		<?php endif; ?>

		<div class="evently-single-post__content">
			<?php the_content(); ?>
		</div>

		<?php
		wp_link_pages(
			array(
				'before' => '<nav class="evently-page-links">' . esc_html__( 'Pages:', 'evently' ),
				'after'  => '</nav>',
			)
		);
		?>

		<?php evently_template_part( 'template-parts/content/related-posts' ); ?>

		<?php
		if ( comments_open() || get_comments_number() ) {
			comments_template();
		}
		?>
	<?php endwhile; ?>
</article>

<?php
get_footer();
