<?php
/**
 * Generic empty state — used by the event grid, favorites list, My Tickets,
 * and the Event Archive's "no results" state (brief §15, §21).
 *
 * @param array $args {
 *     @type string $title
 *     @type string $message
 *     @type string $icon    Icon name in assets/icons/. Default 'empty-events'.
 *     @type array  $action  Optional ['text' => ..., 'url' => ...] rendered as a button.
 * }
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title   = isset( $args['title'] ) ? $args['title'] : __( 'Nothing here yet', 'evently' );
$message = isset( $args['message'] ) ? $args['message'] : '';
$icon    = isset( $args['icon'] ) ? $args['icon'] : 'empty-events';
$action  = isset( $args['action'] ) ? $args['action'] : null;
?>
<div class="evently-empty-state">
	<div class="evently-empty-state__icon"><?php evently_icon( $icon ); ?></div>
	<h3><?php echo esc_html( $title ); ?></h3>
	<?php if ( $message ) : ?>
		<p><?php echo esc_html( $message ); ?></p>
	<?php endif; ?>
	<?php if ( ! empty( $action['text'] ) && ! empty( $action['url'] ) ) : ?>
		<div class="evently-empty-state__action">
			<?php
			evently_button(
				array(
					'text'    => $action['text'],
					'url'     => $action['url'],
					'variant' => 'secondary',
					'arrow'   => false,
					'size'    => 'sm',
				)
			);
			?>
		</div>
	<?php endif; ?>
</div>
