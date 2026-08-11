<?php
/**
 * Default archive template (blog category/tag/date/author archives).
 * `mep_events` has `has_archive => false` (no native archive URL); its
 * mep_cat / mep_org taxonomy archives are served by the booking plugin's
 * own bundled templates instead — see docs/architecture.md.
 *
 * Elementor Pro Theme Builder `archive` location can replace this entirely.
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

if ( ! function_exists( 'evently_elementor_location' ) || ! evently_elementor_location( 'archive' ) ) :
	?>
	<div class="evently-container evently-section">
		<header class="evently-archive-header">
			<h1 class="evently-archive-header__title entry-title"><?php the_archive_title(); ?></h1>
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
endif;

get_footer();
