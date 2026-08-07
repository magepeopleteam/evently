# Customization

## CSS

Every visual token lives in `assets/css/variables.css` as a plain CSS custom property (`--evently-primary`, `--evently-space-4`, `--evently-radius-lg`, …) — never hardcoded elsewhere. Change a token there (or override it — see below) and everything using it updates.

To override without editing core files (so updates don't clobber your changes), add a small stylesheet in a child theme, or use **Additional CSS** (Appearance → Customize), targeting the same custom properties:

```css
:root {
  --evently-primary: #ff3366;
}
```

Evently → Theme Settings → Colors does exactly this for the 3 most commonly-changed colors, printed as a small inline `<style>` only when you've actually changed them from the defaults.

## Templates and template-parts

Every template-part is a small, single-purpose file under `template-parts/`. To change one section's markup, copy the file into a child theme at the same relative path — `get_template_part()` (used everywhere via `evently_template_part()`) automatically prefers the child theme's copy.

## Homepage section order

The homepage's section list is filterable:

```php
add_filter( 'evently_home_sections', function ( $sections ) {
    // Move testimonials right after the hero.
    $sections = array_diff( $sections, array( 'testimonials' ) );
    array_splice( $sections, 1, 0, array( 'testimonials' ) );
    return $sections;
} );
```

## Event card

`evently_event_card( $event, $variant )` (`inc/template-functions.php`) is the single event-card renderer used everywhere (homepage, archive, related events, Elementor widget). `$variant` is one of `default | featured | horizontal | compact | list` — see `template-parts/cards/event-card.php` and `assets/css/events.css`.

See also: [Child Theme](child-theme.md), [Hooks](hooks.md).
