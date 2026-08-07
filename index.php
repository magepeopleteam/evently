<?php
/**
 * The default template — classic WordPress template hierarchy fallback.
 * front-page.php / page.php / single.php / archive.php all take priority
 * over this file for their respective contexts; this only renders when
 * nothing more specific matches.
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<div class="evently-container evently-section">
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
