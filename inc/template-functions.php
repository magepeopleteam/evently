<?php
/**
 * Reusable render helpers. These produce markup only — no querying,
 * pricing, or availability decisions happen here (that's the adapter's job,
 * see inc/integrations/booking-plugin.php). Keeping render functions pure
 * means the exact same function backs the Gutenberg block, the Elementor
 * widget, and every classic-PHP template that needs the same component
 * (brief §26 "do not duplicate business logic between Gutenberg and
 * Elementor").
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render (echo) an Evently button/link.
 *
 * @param array $args {
 *     @type string $text    Visible label. Required.
 *     @type string $url     Href. Defaults to '#'.
 *     @type string $variant primary|secondary|white|accent|ghost|outline-white|pill-outline. Default 'primary'.
 *     @type bool   $arrow   Append the animated "→" glyph. Default true.
 *     @type string $size    '' or 'sm'. Default ''.
 *     @type array  $attrs   Extra raw attributes, e.g. ['data-modal' => 'search'] — keys/values are escaped.
 * }
 * @return void
 */
function evently_button( $args ) {
	echo evently_get_button( $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside evently_get_button().
}

/**
 * Same as evently_button() but returns the markup.
 *
 * @param array $args See evently_button().
 * @return string
 */
function evently_get_button( $args ) {
	$args = wp_parse_args(
		$args,
		array(
			'text'    => '',
			'url'     => '#',
			'variant' => 'primary',
			'arrow'   => true,
			'size'    => '',
			'attrs'   => array(),
		)
	);

	if ( '' === trim( (string) $args['text'] ) ) {
		return '';
	}

	$classes = array( 'btn', 'btn--' . sanitize_html_class( $args['variant'] ) );
	if ( 'sm' === $args['size'] ) {
		$classes[] = 'btn--sm';
	}

	$attr_string = '';
	foreach ( (array) $args['attrs'] as $attr_key => $attr_value ) {
		$attr_string .= ' ' . esc_attr( $attr_key ) . '="' . esc_attr( $attr_value ) . '"';
	}

	$arrow = $args['arrow'] ? ' <span class="evently-arrow" aria-hidden="true">→</span>' : '';

	return sprintf(
		'<a href="%1$s" class="%2$s"%3$s>%4$s%5$s</a>',
		esc_url( $args['url'] ),
		esc_attr( implode( ' ', $classes ) ),
		$attr_string, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built entirely from esc_attr() pairs above.
		esc_html( $args['text'] ),
		$arrow // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static markup, no variable input.
	);
}

/**
 * Render an Evently badge/pill.
 *
 * @param string $text
 * @param string $variant soft|accent|dark|success|warning|error. Default 'soft'.
 * @return void
 */
function evently_badge( $text, $variant = 'soft' ) {
	if ( '' === trim( (string) $text ) ) {
		return;
	}
	printf(
		'<span class="evently-badge evently-badge--%1$s">%2$s</span>',
		esc_attr( sanitize_html_class( $variant ) ),
		esc_html( $text )
	);
}

/**
 * Render a star rating, e.g. ★★★★★ for 5, ★★★★☆ for 4.2 (rounded).
 *
 * @param float $rating 0–5.
 * @return void
 */
function evently_star_rating( $rating ) {
	$rating = max( 0, min( 5, round( (float) $rating ) ) );
	printf(
		'<span class="evently-stars" role="img" aria-label="%1$s">%2$s%3$s</span>',
		esc_attr(
			sprintf(
				/* translators: %s: numeric rating out of 5. */
				__( 'Rated %s out of 5', 'evently' ),
				$rating
			)
		),
		esc_html( str_repeat( '★', $rating ) ),
		esc_html( str_repeat( '☆', 5 - $rating ) )
	);
}

/**
 * Normalize a loosely-shaped event array so every template/adapter caller
 * can rely on the same keys existing (never undefined-index notices).
 *
 * @param array $event Partial event data.
 * @return array Fully-keyed event data.
 */
function evently_normalize_event( $event ) {
	return wp_parse_args(
		$event,
		array(
			'id'            => 0,
			'title'         => '',
			'url'           => '#',
			'image_id'      => 0,
			'image_url'     => '',
			'image_alt'     => '',
			'date_label'    => '',
			'date_full'     => '',
			'time'          => '',
			'location'      => '',
			'category'      => '',
			'price_label'   => '',
			'price'         => null,
			'rating'        => null,
			'organizer'     => '',
			'availability'  => '', // '', 'low-stock', 'sold-out'.
			'is_favorite'   => false,
			'attendee_note' => '', // e.g. "2,840 tickets sold today" — hero only.
		)
	);
}

/**
 * Render one Evently event card.
 *
 * @param array  $event   See evently_normalize_event() for the accepted shape.
 * @param string $variant default|featured|horizontal|compact|list. Default 'default'.
 * @return void
 */
function evently_event_card( $event, $variant = 'default' ) {
	$event = evently_normalize_event( $event );

	/**
	 * Fires immediately before an event card renders.
	 *
	 * @param array  $event   Normalized event data.
	 * @param string $variant Card variant being rendered.
	 */
	do_action( 'evently_before_event_card', $event, $variant );

	evently_template_part( 'template-parts/cards/event-card', '', array(
		'event'   => $event,
		'variant' => $variant,
	) );

	/**
	 * Fires immediately after an event card renders.
	 *
	 * @param array  $event   Normalized event data.
	 * @param string $variant Card variant rendered.
	 */
	do_action( 'evently_after_event_card', $event, $variant );
}

/**
 * Render a grid/row of event cards.
 *
 * @param array[] $events  Array of event arrays (see evently_normalize_event()).
 * @param string  $variant Card variant, applies to every card in the set.
 * @param string  $layout  'grid-4'|'grid-3'|'grid-2'|'scroll'. Default 'grid-4'.
 * @return void
 */
function evently_event_grid( $events, $variant = 'default', $layout = 'grid-4' ) {
	if ( empty( $events ) ) {
		evently_template_part( 'template-parts/cards/empty-state', '', array(
			'title'   => __( 'No events found', 'evently' ),
			'message' => __( 'Try adjusting your search or check back soon — new events are added regularly.', 'evently' ),
		) );
		return;
	}

	$layout_class = 'evently-scroll-row';
	if ( 'grid-4' === $layout ) {
		$layout_class = 'evently-grid evently-grid--4';
	} elseif ( 'grid-3' === $layout ) {
		$layout_class = 'evently-grid evently-grid--3';
	} elseif ( 'grid-2' === $layout ) {
		$layout_class = 'evently-grid evently-grid--2';
	} elseif ( 'list' === $layout ) {
		$layout_class = 'evently-list-rows';
	}

	printf( '<div class="%s events-grid">', esc_attr( $layout_class ) );
	foreach ( $events as $single_event ) {
		evently_event_card( $single_event, $variant );
	}
	echo '</div>';
}

/**
 * The theme's single "where do event cards come from" decision point.
 *
 * Tries the real booking-plugin adapter first (once Evently_Booking_Adapter
 * exists — see inc/integrations/booking-plugin.php); falls back to the
 * canonical demo dataset (inc/demo-import/sample-data.php) so the homepage
 * always renders something realistic instead of an empty grid on a fresh
 * install. No template should query mep_events or read the demo array
 * directly — everything goes through this function.
 *
 * @param int    $count Max events to return.
 * @param string $context Optional hint for the adapter, e.g. 'trending'|'near-you'|'vibe'.
 * @param array  $adapter_args Extra args forwarded to the adapter when it's available.
 * @return array[] Array of card-shaped event arrays (see evently_normalize_event()).
 */
function evently_get_home_events( $count = 8, $context = 'trending', $adapter_args = array() ) {
	if ( evently_has_booking_plugin() && class_exists( 'Evently_Booking_Adapter' ) ) {
		$live_events = Evently_Booking_Adapter::get_events_for_cards( $count, $context, $adapter_args );
		if ( ! empty( $live_events ) ) {
			return $live_events;
		}
	}

	$demo_events = array_slice( evently_demo_events(), 0, $count );

	return array_map( 'evently_demo_event_to_card', $demo_events );
}

/**
 * The 4 homepage stats (stats.php's full strip, plus the hero's 3-up teaser
 * which drops the 3rd one). Overlays any admin values from Evently → Theme
 * Settings → Homepage: Stats on top of the bundled demo numbers, keyed by
 * position rather than by label text so renaming a stat's label doesn't
 * break the hero's "skip the 3rd one" logic.
 *
 * @return array[] 4 entries, each {value, label}.
 */
function evently_home_stats() {
	$stats = evently_demo_stats();

	foreach ( $stats as $evently_index => &$evently_stat ) {
		$evently_n            = $evently_index + 1;
		$evently_stat['value'] = evently_get_setting( "stat_{$evently_n}_value", $evently_stat['value'] );
		$evently_stat['label'] = evently_get_setting( "stat_{$evently_n}_label", $evently_stat['label'] );
	}
	unset( $evently_stat );

	return $stats;
}

/**
 * Render a template-part to a string instead of echoing it. Used by the
 * block patterns (patterns/*.php) so a pattern's content is always exactly
 * what the live theme templates render — never a hand-duplicated copy that
 * can drift out of sync (brief §25/§26: one rendering layer, reused
 * everywhere, including by Elementor widgets).
 *
 * @param string $slug
 * @param array  $args
 * @return string
 */
function evently_capture_template_part( $slug, $args = array() ) {
	ob_start();
	evently_template_part( $slug, '', $args );
	return (string) ob_get_clean();
}

/**
 * Wrap raw HTML as a `core/html` block, ready to embed inside a block
 * pattern's content string.
 *
 * @param string $html
 * @return string
 */
function evently_html_block( $html ) {
	if ( '' === trim( $html ) ) {
		return '';
	}
	return "<!-- wp:html -->\n" . $html . "\n<!-- /wp:html -->\n";
}

/**
 * Fetch the social links configured in Evently → Theme Settings, filtered to
 * only the ones with a URL set, in the fixed brand order (brief §11 footer).
 *
 * @return array<string, string> Map of network slug => URL.
 */
function evently_get_social_links() {
	$networks = array( 'instagram', 'facebook', 'x', 'youtube' );
	$links    = array();

	foreach ( $networks as $network ) {
		$url = evently_get_setting( 'social_' . $network, '' );
		if ( ! empty( $url ) ) {
			$links[ $network ] = $url;
		}
	}

	return $links;
}

/**
 * Whether the current singular view should print Evently's page/post title.
 *
 * @return bool
 */
function evently_should_render_singular_title() {
	if ( is_front_page() || is_home() ) {
		return false;
	}

	if ( ! is_singular( array( 'page', 'post' ) ) ) {
		return false;
	}

	// Already printed this request (page.php / single.php / Elementor hook).
	if ( did_action( 'evently_rendered_singular_title' ) ) {
		return false;
	}

	return true;
}

/**
 * Print the singular page/post title block.
 *
 * @param string $context 'page' or 'post'. Empty = detect from queried object.
 * @return void
 */
function evently_render_singular_title( $context = '' ) {
	if ( ! evently_should_render_singular_title() ) {
		return;
	}

	if ( '' === $context ) {
		$context = is_singular( 'post' ) ? 'post' : 'page';
	}

	if ( 'post' === $context ) {
		$evently_categories = get_the_category();
		?>
		<header class="evently-single-post__header">
			<?php if ( ! empty( $evently_categories ) ) : ?>
				<div class="evently-eyebrow evently-eyebrow--pill"><?php echo esc_html( $evently_categories[0]->name ); ?></div>
			<?php endif; ?>
			<h1 class="evently-single-post__title"><?php the_title(); ?></h1>
			<div class="evently-single-post__meta">
				<span><?php echo esc_html( get_the_date() ); ?></span>
				<span aria-hidden="true">·</span>
				<span><?php echo esc_html( get_the_author() ); ?></span>
			</div>
		</header>
		<?php
	} else {
		?>
		<header class="evently-page__header">
			<h1 class="evently-page__title"><?php the_title(); ?></h1>
		</header>
		<?php
	}

	/**
	 * Fires after Evently prints the singular title (prevents duplicates).
	 */
	do_action( 'evently_rendered_singular_title' );
}
