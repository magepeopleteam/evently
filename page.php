<?php
/**
 * Default template for static Pages.
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<article <?php post_class( 'evently-page evently-container evently-section' ); ?>>
	<?php while ( have_posts() ) : ?>
		<?php the_post(); ?>

		<?php evently_render_singular_title( 'page' ); ?>

		<?php if ( has_post_thumbnail() ) : ?>
			<div class="evently-page__thumb">
				<?php the_post_thumbnail( 'evently-featured', array( 'loading' => 'lazy' ) ); ?>
			</div>
		<?php endif; ?>

		<div class="evently-page__content">
			<?php the_content(); ?>
		</div>

		<?php
		if ( comments_open() || get_comments_number() ) {
			comments_template();
		}
		?>
	<?php endwhile; ?>
</article>

<?php
get_footer();
