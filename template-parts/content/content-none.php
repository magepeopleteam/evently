<?php
/**
 * Fallback markup when no posts are found (search, archive, blog).
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="evently-container evently-section">
	<?php
	evently_template_part(
		'template-parts/cards/empty-state',
		'',
		array(
			'title'   => is_search() ? __( 'No results found', 'evently' ) : __( 'Nothing published yet', 'evently' ),
			'message' => is_search()
				? __( 'Try a different search term, or browse events and articles from the homepage.', 'evently' )
				: __( 'Check back soon.', 'evently' ),
			'action'  => array(
				'text' => __( 'Back to homepage', 'evently' ),
				'url'  => home_url( '/' ),
			),
		)
	);
	?>
</div>
