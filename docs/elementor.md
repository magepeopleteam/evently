# Elementor Compatibility

Evently is fully Elementor-compatible. `inc/integrations/elementor.php` only runs when Elementor is active — without Elementor the theme behaves exactly as before.

## What’s supported

| Feature | Behavior |
|---|---|
| **Page Layout → Theme** | Uses `page.php` / `single.php`. Elementor-built documents drop the boxed container so the canvas can go full-bleed. |
| **Page Layout → Elementor Full Width** | Plugin template + theme header/footer. Theme title prints above content unless **Hide Title** is on. |
| **Page Layout → Elementor Canvas** | Plugin owns the document (no theme chrome). |
| **Page Settings → Hide Title** | Honored in PHP (`evently_elementor_hides_title()`) and CSS (`--page-title-display` / `h1.entry-title`). |
| **Theme Builder (Elementor Pro)** | Core locations registered: `header`, `footer`, `single`, `archive`. Theme templates call `elementor_theme_do_location()` with Evently fallbacks. |
| **Content Width / stretched sections** | Kit width owns Elementor containers; theme wrappers use `overflow: visible` so stretched sections aren’t clipped. |
| **Evently widgets** | Full homepage-section parity under the **Evently** category (see below). |

## Theme Builder locations

Registered via `elementor/theme/register_locations` → `register_all_core_location()`.

| Location | Theme entry points |
|---|---|
| `header` | `header.php` |
| `footer` | `footer.php` |
| `single` | `page.php`, `single.php`, `front-page.php`, `404.php`, `index.php` (singular) |
| `archive` | `archive.php`, `search.php`, `index.php` (non-singular) |

When a Pro template is assigned to a location, Evently skips its fallback markup for that slot.

## Hide Title

1. Elementor Page Settings switcher sets `:root { --page-title-display: none }`.
2. Titles use `h1.entry-title.elementor-page-title` (matches Elementor’s default Page Title Selector).
3. `evently_should_render_singular_title()` also skips printing when `hide_title === 'yes'`.

## Widgets

Registered under the **Evently** category (`inc/integrations/elementor/`):

- **Evently Hero**, **Categories**, **Trending Events**, **Featured Event**, **Choose Your Vibe**, **Near You**, **Calendar**, **How It Works**, **Digital Ticket**, **Organizer CTA**, **Stats**, **Testimonials**, **Event Journal**, **Final CTA** — Theme Settings–driven homepage sections.
- **Evently Events Grid** — heading, count, card style, columns.
- **Evently Event Search** — functional 4-field search bar.
- **Evently CTA** — title / description / button controls.

Every widget calls the same `evently_*` render helpers as Gutenberg patterns and classic PHP templates — one markup layer, not two.

## Adding a widget

1. Create a class in `inc/integrations/elementor/` extending `Evently_Elementor_Widget_Base`.
2. Require + register it in `evently_elementor_register_widgets()`.
3. In `render()`, call an existing `evently_*` helper or `evently_template_part()` — don’t duplicate markup.
