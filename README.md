# Hello Movie Engine

A lightweight, cinematic dark theme built as the official companion for the [Movie Engine](https://movieengine.pro/) plugin. Delivers a modern OTT streaming experience with responsive layouts, dual header styles, and seamless plugin integration.

## Features

- **Dual header styles**: Transparent (over hero) or solid, configurable via Customizer
- **Responsive header**: Transparent header uses `position: relative` on mobile (≤1024px) so it scrolls with content; fixed only on desktop
- **Mega Menu**: Enable per menu item in Appearance → Menus (Enable Mega Menu + Columns); optional column headings on nested items; mobile keeps the accordion menu
- **Movie Engine integration**: Transparent header only on front page, single movie, series, and episode; solid everywhere else (playlist, archives, search, blog, etc.)
- **Customizer options**: Header style, width, colors, padding, page title locations
- **Dark theme**: Cinema-style dark interface optimized for streaming
- **Translation ready**: Full text domain support

## Requirements

- WordPress 4.5 or later
- PHP 7.4 or later
- [Movie Engine](https://movieengine.pro/) plugin (optional, for full functionality)

## Installation

1. Upload the theme folder to `/wp-content/themes/`
2. Activate the theme in **Appearance → Themes**
3. Install and activate the Movie Engine plugin for movie/series/episode support
4. Customize via **Appearance → Customize**

## Mega Menu

1. Go to **Appearance → Menus**
2. Expand a **top-level** Primary Menu item
3. Check **Enable Mega Menu**
4. Choose **Mega Menu Columns** (2–8)
5. Nest column groups under that item. On a nested item, check **Mega column heading** to style it as a column title
6. Save the menu

Example structure:

```
Movies                    ← Enable Mega Menu + 3 columns
├── Genres                ← Mega column heading
│   ├── Action
│   └── Drama
├── By Year
└── Lists
```

Desktop (≥1025px) shows the multi-column panel. Mobile keeps the existing accordion submenu.

## Customization

- **Header**: Style (transparent/solid), width (boxed/fullwidth), background color, padding
- **Page title**: Show/hide per location (blog, archives, search, single post/page, etc.)
- **Layout**: Content padding, sidebar width, blog columns
- **Colors**: Primary color, page title background

## File Structure

```
hello-movieengine/
├── assets/
│   ├── css/          # header, layout, components, responsive
│   └── js/           # navigation, customizer
├── inc/
│   ├── customizer/   # Customizer sections and controls
│   ├── mega-menu.php
│   ├── movie-engine-compat.php
│   └── template-functions.php
├── template-parts/
│   ├── header/       # header-transparent, header-solid
│   └── page-title.php
├── header.php
├── footer.php
└── style.css
```

## License

GPLv2 or later. See [LICENSE](LICENSE).
