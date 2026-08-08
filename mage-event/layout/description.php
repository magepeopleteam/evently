<?php
/**
 * Evently override of mage-eventpress layout/description.php.
 *
 * Adds justified body copy and a Read more / Read less toggle when the
 * description exceeds 200 words (same UX as Evently's theme single skin).
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

$event_id = $event_id ?? 0;
// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
$event_infos = $event_infos ?? array();
// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
$event_infos               = ( is_array( $event_infos ) && sizeof( $event_infos ) > 0 ) ? $event_infos : MPWEM_Functions::get_all_info( $event_id );
$_single_event_setting_sec = is_array( $event_infos ) && array_key_exists( 'single_event_setting_sec', $event_infos ) ? $event_infos['single_event_setting_sec'] : array();
$single_event_setting_sec  = is_array( $_single_event_setting_sec ) && ! empty( $_single_event_setting_sec ) ? $_single_event_setting_sec : array();
$description_title         = is_array( $single_event_setting_sec ) && array_key_exists( 'mep_event_hide_description_title', $single_event_setting_sec ) ? $single_event_setting_sec['mep_event_hide_description_title'] : 'no';

if ( ! get_post_field( 'post_content', $event_id ) ) {
	return;
}

$evently_desc_html  = apply_filters( 'the_content', get_post_field( 'post_content', $event_id ) );
$evently_desc_words = str_word_count( wp_strip_all_tags( $evently_desc_html ) );
$evently_desc_more  = $evently_desc_words > 200;
?>
<div class="mpwem_details">
	<?php if ( 'no' === $description_title ) : ?>
		<h2 class="_mb"><?php esc_html_e( 'Event  Description', 'mage-eventpress' ); ?></h2>
	<?php endif; ?>
	<div
		class="mpwem_details_content mp_wp_editor<?php echo $evently_desc_more ? ' mpwem_details_content--collapsible is-collapsed' : ''; ?>"
		<?php if ( $evently_desc_more ) : ?>
			data-evently-readmore
		<?php endif; ?>
	>
		<div class="mpwem_details_content__body">
			<?php echo $evently_desc_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core the_content filters. ?>
		</div>
		<?php if ( $evently_desc_more ) : ?>
			<button
				type="button"
				class="evently-read-more"
				data-evently-readmore-toggle
				aria-expanded="false"
			>
				<span data-label-more><?php esc_html_e( 'Read more', 'evently' ); ?></span>
				<span data-label-less hidden><?php esc_html_e( 'Read less', 'evently' ); ?></span>
			</button>
		<?php endif; ?>
	</div>
</div>
