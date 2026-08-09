# Hooks & Filters

All Evently hooks use the `evently_` prefix. Every hook below actually fires somewhere real in the theme (file:line noted) — this isn't an aspirational list.

## Actions

### `evently_before_header` / `evently_after_header`
Fire in `header.php`, immediately around the site header template-part.
```php
add_action( 'evently_before_header', function () {
    echo '<div class="announcement-bar">Free shipping on all tickets this week!</div>';
} );
```
Evently itself hooks its skip-to-content link on `evently_before_header` (`inc/template-hooks.php`).

### `evently_before_footer` / `evently_after_footer`
Fire in `footer.php`, around the site footer template-part.

### `evently_before_event_content` / `evently_after_event_content`
Fire around the main content of the Event Archive (`template-parts/archive/event-archive-content.php`, used by `page-templates/event-archive.php`). They previously also fired on the single-event and taxonomy-archive pages via the theme's `mage-event/` override templates; those files have been retired (see `docs/booking-integration.md`), so single-event and taxonomy-archive pages now render through the booking plugin's own bundled templates and no longer fire these hooks.

### `evently_before_event_card` / `evently_after_event_card`
Fire in `evently_event_card()` (`inc/template-functions.php`), around every single card render — homepage, archive, related events, Elementor widget, all of them, since they all funnel through this one function.
```php
add_action( 'evently_before_event_card', function ( $event, $variant ) {
    if ( 'sold-out' === $event['availability'] ) {
        echo '<span class="my-badge">Last chance</span>';
    }
}, 10, 2 );
```

## Filters

### `evently_home_sections`
Filters the ordered array of homepage section slugs `front-page.php` renders (each maps to `template-parts/home/{slug}.php`). See `docs/customization.md` for a reorder example.

## Adding your own

Every `evently_*` render function documented in `docs/customization.md` and `docs/booking-integration.md` is a normal PHP function — nothing stops you from wrapping any of them in your own `do_action()`/`apply_filters()` calls in a child theme. Keep the `evently_` prefix for anything you intend other developers/child themes to rely on.
