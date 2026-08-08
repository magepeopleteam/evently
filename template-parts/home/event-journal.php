<?php
/**
 * Event Journal — editorial blog teaser (brief §11/§24). Links to real
 * published posts when they exist (matched loosely by title so the demo
 * importer's imported articles take over automatically); otherwise shows
 * the curated preview copy without pretending an article page exists yet.
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$evently_heading       = $args['heading'] ?? __( 'Event Journal', 'evently' );
$evently_subhead       = $args['subhead'] ?? __( 'Ideas, inspiration and stories from the world of events.', 'evently' );
$evently_view_all_text = $args['view_all_text'] ?? __( 'View all', 'evently' );
$evently_count         = isset( $args['count'] ) ? (int) $args['count'] : 3;

$evently_articles = array_slice( evently_demo_journal_articles(), 0, $evently_count );

foreach ( $evently_articles as &$evently_article ) {
	$evently_matching_posts = get_posts(
		array(
			'post_type'      => 'post',
			'title'          => $evently_article['title'],
			'posts_per_page' => 1,
			'post_status'    => 'publish',
		)
	);
	$evently_article['url'] = ! empty( $evently_matching_posts ) ? get_permalink( $evently_matching_posts[0] ) : '';
}
unset( $evently_article );
?>
<section class="evently-section">
	<div class="evently-container">
		<div class="evently-section-head evently-section-head--row">
			<div>
				<h2><?php echo esc_html( $evently_heading ); ?></h2>
				<p><?php echo esc_html( $evently_subhead ); ?></p>
			</div>
			<a class="evently-section-head__link" href="<?php echo esc_url( get_post_type_archive_link( 'post' ) ); ?>">
				<?php echo esc_html( $evently_view_all_text ); ?> <span class="arrow" aria-hidden="true">→</span>
			</a>
		</div>

		<div class="journal-grid">
			<?php foreach ( $evently_articles as $evently_article ) : ?>
				<?php $evently_url = $evently_article['url'] ? $evently_article['url'] : '#'; ?>
				<article class="journal-card">
					<a href="<?php echo esc_url( $evently_url ); ?>" class="journal-img" aria-hidden="<?php echo $evently_article['url'] ? 'false' : 'true'; ?>" tabindex="<?php echo $evently_article['url'] ? '0' : '-1'; ?>">
						<img src="<?php echo esc_url( evently_demo_image_url( $evently_article ) ); ?>" alt="<?php echo esc_attr( $evently_article['title'] ); ?>" loading="lazy" />
					</a>
					<div class="evently-eyebrow"><?php echo esc_html( mb_strtoupper( $evently_article['category'] ) ); ?></div>
					<h3 class="journal-title"><a href="<?php echo esc_url( $evently_url ); ?>"><?php echo esc_html( $evently_article['title'] ); ?></a></h3>
					<div class="journal-foot">
						<span class="journal-date"><?php echo esc_html( $evently_article['date'] ); ?></span>
						<a href="<?php echo esc_url( $evently_url ); ?>" class="journal-read"><?php esc_html_e( 'Read article', 'evently' ); ?> <span class="evently-arrow" aria-hidden="true">→</span></a>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>
