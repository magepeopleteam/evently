<?php
/**
 * Evently override of mage-eventpress templates/layout/organizer.php.
 *
 * Same hide/only logic and mage_event_single_org_name filter as the plugin;
 * markup matches Evently’s organizer identity card (avatar + name + follow).
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$event_id                  = $event_id ?? 0;
$event_infos               = $event_infos ?? array();
$event_infos               = ( is_array( $event_infos ) && ! empty( $event_infos ) ) ? $event_infos : MPWEM_Functions::get_all_info( $event_id );
$only                      = $only ?? '';
$_single_event_setting_sec = is_array( $event_infos ) && array_key_exists( 'single_event_setting_sec', $event_infos ) ? $event_infos['single_event_setting_sec'] : array();
$single_event_setting_sec  = is_array( $_single_event_setting_sec ) && ! empty( $_single_event_setting_sec ) ? $_single_event_setting_sec : array();
$hide_organizer            = is_array( $single_event_setting_sec ) && array_key_exists( 'mep_event_hide_org_from_details', $single_event_setting_sec ) ? $single_event_setting_sec['mep_event_hide_org_from_details'] : 'no';

if ( ! ( $event_id > 0 && 'no' === $hide_organizer ) ) {
	return;
}

$names = MPWEM_Global_Function::all_taxonomy_data( $event_id, 'mep_org' );
if ( ! ( is_array( $names ) && ! empty( $names ) ) ) {
	return;
}

ob_start();

if ( $only ) {
	echo esc_html( implode( ', ', $names ) );
} else {
	$orgs = get_the_terms( $event_id, 'mep_org' );
	if ( ! is_array( $orgs ) || empty( $orgs ) ) {
		$content = ob_get_clean();
		echo apply_filters( 'mage_event_single_org_name', $content, $event_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- filtered markup.
		return;
	}
	?>
	<div class="mpwem_organizer">
		<?php foreach ( $orgs as $org ) : ?>
			<?php
			if ( ! $org instanceof WP_Term ) {
				continue;
			}
			$org_link = get_term_link( $org );
			$org_link = is_wp_error( $org_link ) ? '' : $org_link;
			$initial  = function_exists( 'mb_substr' )
				? mb_strtoupper( mb_substr( $org->name, 0, 1 ) )
				: strtoupper( substr( $org->name, 0, 1 ) );
			?>
			<div class="mpwem_organizer_item evently-plugin-organizer">
				<div class="evently-plugin-organizer__identity">
					<div class="evently-plugin-organizer__avatar" aria-hidden="true"><?php echo esc_html( $initial ); ?></div>
					<div class="evently-plugin-organizer__meta">
						<span class="evently-plugin-organizer__label"><?php esc_html_e( 'Organized by', 'evently' ); ?></span>
						<?php if ( $org_link ) : ?>
							<a class="evently-plugin-organizer__name" href="<?php echo esc_url( $org_link ); ?>">
								<?php echo esc_html( $org->name ); ?>
							</a>
						<?php else : ?>
							<span class="evently-plugin-organizer__name"><?php echo esc_html( $org->name ); ?></span>
						<?php endif; ?>
					</div>
				</div>
				<?php if ( $org_link ) : ?>
					<a class="evently-plugin-organizer__follow" href="<?php echo esc_url( $org_link ); ?>">
						<?php esc_html_e( 'View', 'evently' ); ?>
					</a>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
	</div>
	<?php
}

$content = ob_get_clean();
echo apply_filters( 'mage_event_single_org_name', $content, $event_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- filtered markup.
