# Theme assets layout

## Shared (`assets/core/`)

Used by every design pack:

- `css/base.css` — variables, reset, typography
- `js/navigation.js` — header menu / search behavior

## Admin only (`assets/admin/`)

Customizer controls (not loaded on the public site):

- `css/customizer-controls.css`
- `js/customizer-controls.js`

## Design packs

Only **one** pack loads at a time (Customizer → Site Style):

| Folder | Role |
|--------|------|
| `assets/style-one/css/` | Classic layout styles |
| `assets/style-one/js/` | Classic-only scripts (e.g. bottom nav) |
| `assets/style-two/css/` | OTT feed layout (+ `home-feed.css`) |
| `assets/style-two/js/` | Style Two scripts |

## PHP templates

Layout views and Movie Engine overrides live in `inc/templates/` (see `inc/templates/README.md`).

`template-parts/` is reserved for blog/content fragments only (`content.php`, etc.).

## Add Style Three

1. `assets/style-three/css/` + `js/`
2. `inc/templates/header/style-three/`, `footer/style-three/`, etc.
3. Register in `inc/styles/class-hello-movieengine-style-registry.php`
4. Optional plugin overrides under `inc/templates/movie-engine/style-three/`