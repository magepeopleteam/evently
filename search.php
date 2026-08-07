<?php
/**
 * Search results template.
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<div class="evently-container evently-section">
	<header class="evently-archive-header">
		<h1 class="evently-archive-header__title">
			<?php
			printf(
				/* translators: %s: search query. */
				esc_html__( 'Search results for: %s', 'evently' ),
				'<span>' . esc_html( get_search_query() ) . '</span>'
			);
			?>
		</h1>
	</header>

	<?php if ( have_posts() ) : ?>
		<div class="journal-grid">
			<?php
			while ( have_posts() ) :
				the_post();
				evently_template_part( 'template-parts/content/content' );
			endwhile;
			?>
		</div>
		<?php the_posts_pagination( array( 'prev_text' => __( '← Newer', 'evently' ), 'next_text' => __( 'Older →', 'evently' ) ) ); ?>
	<?php else : ?>
		<?php evently_template_part( 'template-parts/content/content-none' ); ?>
	<?php endif; ?>
</div>

<?php
get_footer();
