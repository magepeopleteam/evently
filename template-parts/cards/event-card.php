<?php
/**
 * The Evently event card — one markup structure, six variants via a single
 * modifier class (brief §14: "Use the same underlying component throughout
 * Evently. Do not duplicate markup unnecessarily.").
 *
 * Variants: default | featured | horizontal | compact | list | mobile
 * ("mobile" intentionally aliases "default" — responsive.css already
 * reflows the default card for small screens, so a separate mobile markup
 * would just be the same DOM with different CSS, which responsive.css
 * already provides).
 *
 * Expects $args['event'] (see evently_normalize_event()) and $args['variant'].
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$event   = isset( $args['event'] ) ? $args['event'] : evently_normalize_event( array() );
$variant = isset( $args['variant'] ) ? $args['variant'] : 'default';

if ( 'mobile' === $variant ) {
	$variant = 'default';
}

$allowed_variants = array( 'default', 'featured', 'horizontal', 'compact', 'list' );
if ( ! in_array( $variant, $allowed_variants, true ) ) {
	$variant = 'default';
}

$card_classes = array( 'event-card', 'event-card--' . $variant );

// Evently → Theme Settings → Events (brief §32) — real display toggles.
$evently_show_price    = (bool) evently_get_setting( 'show_price', 1 );
$evently_show_location = (bool) evently_get_setting( 'show_location', 1 );
$evently_show_favorite = (bool) evently_get_setting( 'show_favorite', 1 );
$evently_show_rating   = (bool) evently_get_setting( 'show_rating', 1 );
?>
<article class="<?php echo esc_attr( implode( ' ', $card_classes ) ); ?>" data-evently-event-id="<?php echo esc_attr( $event['id'] ); ?>">
	<div class="event-img">
		<?php if ( ! empty( $event['image_id'] ) ) : ?>
			<?php echo wp_get_attachment_image( $event['image_id'], 'evently-card', false, array( 'alt' => $event['image_alt'] ? $event['image_alt'] : $event['title'], 'loading' => 'lazy' ) ); ?>
		<?php elseif ( ! empty( $event['image_url'] ) ) : ?>
			<img src="<?php echo esc_url( $event['image_url'] ); ?>" alt="<?php echo esc_attr( $event['image_alt'] ? $event['image_alt'] : $event['title'] ); ?>" loading="lazy" />
		<?php else : ?>
			<div class="event-img__placeholder" aria-hidden="true"></div>
		<?php endif; ?>

		<?php if ( ! empty( $event['category'] ) ) : ?>
			<span class="event-cat"><?php echo esc_html( $event['category'] ); ?></span>
		<?php endif; ?>

		<?php if ( 'list' !== $variant && $evently_show_favorite ) : ?>
			<button
				type="button"
				class="event-fav evently-icon-btn"
				data-evently-favorite
				data-event-id="<?php echo esc_attr( $event['id'] ); ?>"
				aria-pressed="<?php echo $event['is_favorite'] ? 'true' : 'false'; ?>"
				aria-label="<?php esc_attr_e( 'Save to favorites', 'evently' ); ?>"
			>
				<span class="evently-icon-outline"><?php evently_icon( 'heart' ); ?></span>
				<span class="evently-icon-filled"><?php evently_icon( 'heart-filled' ); ?></span>
			</button>
		<?php endif; ?>

		<?php if ( 'sold-out' === $event['availability'] ) : ?>
			<span class="event-availability event-availability--sold-out"><?php esc_html_e( 'Sold out', 'evently' ); ?></span>
		<?php elseif ( 'low-stock' === $event['availability'] ) : ?>
			<span class="event-availability event-availability--low-stock"><?php esc_html_e( 'Almost full', 'evently' ); ?></span>
		<?php endif; ?>
	</div>

	<div class="event-body">
		<?php if ( ! empty( $event['date_label'] ) ) : ?>
			<div class="event-date"><?php echo esc_html( $event['date_label'] ); ?></div>
		<?php endif; ?>

		<h3 class="event-title"><a href="<?php echo esc_url( $event['url'] ); ?>" class="event-title__link"><?php echo esc_html( $event['title'] ); ?></a></h3>

		<?php if ( $evently_show_location && ! empty( $event['location'] ) ) : ?>
			<div class="event-loc">
				<?php evently_icon( 'pin' ); ?>
				<span><?php echo esc_html( $event['location'] ); ?></span>
			</div>
		<?php endif; ?>

		<?php if ( 'featured' === $variant && $evently_show_rating && ! empty( $event['rating'] ) ) : ?>
			<?php evently_star_rating( $event['rating'] ); ?>
		<?php endif; ?>

		<div class="event-foot">
			<?php if ( $evently_show_price && ! empty( $event['price_label'] ) ) : ?>
				<span class="event-price"><?php echo esc_html( $event['price_label'] ); ?></span>
			<?php endif; ?>
			<a href="<?php echo esc_url( $event['url'] ); ?>" class="event-book">
				<span class="screen-reader-text"><?php echo esc_html( $event['title'] ); ?> —</span>
				<?php esc_html_e( 'Book', 'evently' ); ?> <span class="evently-arrow" aria-hidden="true">→</span>
			</a>
		</div>
	</div>
</article>
