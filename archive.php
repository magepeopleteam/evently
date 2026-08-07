<?php
/**
 * Default archive template (blog category/tag/date/author archives).
 * The mep_events post type archive is served by the booking plugin via
 * mage-event/event-archive.php instead — see docs/architecture.md.
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
		<h1 class="evently-archive-header__title"><?php the_archive_title(); ?></h1>
		<?php the_archive_description( '<div class="evently-archive-header__desc">', '</div>' ); ?>
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
