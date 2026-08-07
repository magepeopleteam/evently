<?php
/**
 * Related posts — same-category articles, excluding the current post
 * (brief §24). Query is capped and cheap; skips entirely when nothing
 * qualifies rather than rendering an empty section.
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$evently_category_ids = wp_get_post_categories( get_the_ID() );

if ( empty( $evently_category_ids ) ) {
	return;
}

$evently_related = new WP_Query(
	array(
		'category__in'        => $evently_category_ids,
		'post__not_in'        => array( get_the_ID() ),
		'posts_per_page'      => 3,
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	)
);

if ( ! $evently_related->have_posts() ) {
	return;
}
?>
<section class="evently-related-posts">
	<h2 class="evently-related-posts__title"><?php esc_html_e( 'Related articles', 'evently' ); ?></h2>
	<div class="evently-grid evently-grid--3">
		<?php
		while ( $evently_related->have_posts() ) :
			$evently_related->the_post();
			?>
			<article class="evently-related-post-card">
				<?php if ( has_post_thumbnail() ) : ?>
					<a href="<?php the_permalink(); ?>" class="evently-related-post-card__thumb">
						<?php the_post_thumbnail( 'evently-card', array( 'loading' => 'lazy' ) ); ?>
					</a>
				<?php endif; ?>
				<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
				<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
			</article>
			<?php
		endwhile;
		wp_reset_postdata();
		?>
	</div>
</section>
