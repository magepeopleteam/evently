# Elementor Compatibility

Elementor is entirely optional. `inc/integrations/elementor.php` only registers anything after confirming Elementor actually loaded (`did_action( 'elementor/loaded' )`) — on a site without Elementor, that file is a complete no-op, and nothing else in the theme assumes Elementor exists.

## Widgets

Registered under the **Evently** category in Elementor's panel (`inc/integrations/elementor/`):

- **Evently Hero** — no controls, renders the homepage hero verbatim.
- **Evently Events Grid** — heading, event count, card style (default/featured/compact/list), column count.
- **Evently Categories** — renders the homepage categories bento grid verbatim.
- **Evently Event Search** — the real, functional 4-field search bar.
- **Evently CTA** — title/description/button text/button link controls.

## One rendering layer, not two

Every widget calls the exact same functions the Gutenberg patterns and the live homepage use — `evently_template_part()`, `evently_event_grid()`, `evently_get_home_events()`, `evently_button()`. There is no Elementor-specific reimplementation of "how an event card looks" anywhere; a design change to `template-parts/cards/event-card.php` or `assets/css/events.css` updates the homepage, the block patterns, and the Elementor widgets simultaneously.

## Adding a widget

1. Create a new class in `inc/integrations/elementor/` extending `Evently_Elementor_Widget_Base`.
2. Require it and register an instance inside `evently_elementor_register_widgets()` in `inc/integrations/elementor.php`.
3. In `render()`, call an existing `evently_*` render function or `evently_template_part()` — don't duplicate markup that already exists in a template-part.
