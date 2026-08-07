<?php
/**
 * Editorial "Event Journal" card — used by index.php/archive.php/search.php
 * for every post type this theme doesn't have a bespoke card for. Shares
 * markup with the homepage's Event Journal teaser (brief §24: "Do not make
 * these look like standard WordPress blog cards").
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$evently_categories = get_the_category();
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'journal-card' ); ?>>
	<a href="<?php the_permalink(); ?>" class="journal-img">
		<?php if ( has_post_thumbnail() ) : ?>
			<?php the_post_thumbnail( 'evently-card-wide', array( 'loading' => 'lazy' ) ); ?>
		<?php else : ?>
			<div class="event-img__placeholder" aria-hidden="true"></div>
		<?php endif; ?>
	</a>

	<?php if ( ! empty( $evently_categories ) ) : ?>
		<div class="evently-eyebrow"><?php echo esc_html( mb_strtoupper( $evently_categories[0]->name ) ); ?></div>
	<?php endif; ?>

	<h2 class="journal-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

	<div class="journal-foot">
		<time class="journal-date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
		<a href="<?php the_permalink(); ?>" class="journal-read">
			<?php esc_html_e( 'Read article', 'evently' ); ?> <span class="evently-arrow" aria-hidden="true">→</span>
		</a>
	</div>
</article>
