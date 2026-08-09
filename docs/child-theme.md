# Child Theme Support

Evently is a classic-hierarchy theme (see `docs/implementation-plan.md` §3 for why it deliberately isn't a full block/FSE theme), so standard WordPress child-theme rules apply throughout.

## What you can override from a child theme

- **Any template file** — `header.php`, `footer.php`, `single.php`, `archive.php`, `page.php`, `404.php`, `search.php`, `comments.php`, `front-page.php` — by creating a file at the same path in your child theme.
- **Any template-part** — everything under `template-parts/`, `page-templates/` — same rule: same relative path, child theme wins, because every call goes through `get_template_part()` / `locate_template()` (via `evently_template_part()`). (Evently no longer ships a `mage-event/` override folder — see `docs/booking-integration.md` — but if you add one, the booking plugin's own `get_stylesheet_directory()`-first resolver already checks the child theme first too.)
- **Any CSS/JS file** — enqueue your own with a later priority, or use `wp_dequeue_style()` / `wp_dequeue_script()` in a `wp_enqueue_scripts` callback hooked after Evently's (priority > 10) and enqueue a replacement.
- **Any function** — every theme function is declared with `if ( ! function_exists( '...' ) )` guards where a child theme might reasonably want to fully replace it (e.g. `evently_get_icon()`), and hook-registered functions can always be `remove_action()`/`remove_filter()`'d and replaced.

## style.css

A child theme's `style.css` needs the standard header with `Template: evently`:

```css
/*
Theme Name: Evently Child
Template: evently
*/
```

## Recommended extension points

Prefer the documented hooks (`docs/hooks.md`) and filters over copying whole template files when you only need to add/remove something small — `evently_before_event_card` / `evently_after_event_card`, `evently_home_sections`, etc. exist specifically so a child theme doesn't have to fork a template just to add one element.
