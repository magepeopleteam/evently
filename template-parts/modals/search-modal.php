<?php
/**
 * Header quick-search modal — triggered by the search icon in the site
 * header on every page. A lighter-weight companion to the homepage's full
 * 4-field Smart Search (template-parts/home/search-bar.php); this one
 * submits a plain keyword search to the events archive.
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$evently_search_action = evently_get_events_page_url();
?>
<div id="evently-search-modal" class="evently-modal" data-evently-modal role="dialog" aria-modal="true" aria-labelledby="evently-search-modal-title" hidden>
	<div class="evently-modal__panel evently-modal__panel--search">
		<div class="evently-modal__header">
			<h2 id="evently-search-modal-title" class="evently-modal__title"><?php esc_html_e( 'Search events', 'evently' ); ?></h2>
			<button type="button" class="evently-icon-btn" data-evently-modal-close aria-label="<?php esc_attr_e( 'Close search', 'evently' ); ?>">
				<?php evently_icon( 'close' ); ?>
			</button>
		</div>
		<form role="search" method="get" action="<?php echo esc_url( $evently_search_action ); ?>" class="evently-modal__search-form">
			<label class="screen-reader-text" for="evently-quick-search"><?php esc_html_e( 'Search events, artists, venues…', 'evently' ); ?></label>
			<input
				type="text"
				id="evently-quick-search"
				name="s"
				class="evently-input"
				placeholder="<?php esc_attr_e( 'Search events, artists, venues…', 'evently' ); ?>"
				autocomplete="off"
				data-evently-autocomplete
			/>
			<button type="submit" class="btn btn--accent"><?php esc_html_e( 'Search', 'evently' ); ?></button>
		</form>
		<p class="evently-modal__hint">
			<?php
			printf(
				/* translators: %s: link to the full smart search on the homepage. */
				esc_html__( 'Looking for filters by date, location or category? Use the %s on the homepage.', 'evently' ),
				'<a href="' . esc_url( home_url( '/#evently-categories' ) ) . '">' . esc_html__( 'full search', 'evently' ) . '</a>'
			);
			?>
		</p>
	</div>
</div>
