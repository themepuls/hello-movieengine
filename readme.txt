=== Hello Movie Engine ===

Contributors: themepul
Tags: dark, entertainment, one-column, two-columns, custom-background, custom-logo, custom-menu, featured-images, footer-widgets, threaded-comments, translation-ready, wide-blocks

Requires at least: 4.5
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 1.0.7
License: GNU General Public License v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A lightweight, cinematic dark theme built as the official companion for the Movie Engine plugin.

== Description ==

Hello Movie Engine delivers a modern OTT streaming experience with responsive layouts, dual header styles, and seamless Movie Engine plugin integration.

Features:
* Dual header styles: Transparent (over hero) or solid, configurable via Customizer
* Responsive header: Transparent header scrolls with content on mobile; fixed only on desktop
* Mega Menu: multi-column desktop panels via Appearance → Menus (Enable Mega Menu + Columns)
* Movie Engine integration: fixed header on the locations and specific pages chosen in the Customizer
* Page title hidden on playlist page
* Dark cinema-style interface optimized for streaming
* Customizer options for header, layout, colors, and page title
* Translation ready

== Installation ==

1. In your admin panel, go to Appearance > Themes and click the Add New button.
2. Click Upload Theme and Choose File, then select the theme's .zip file. Click Install Now.
3. Click Activate to use your new theme.
4. Install and activate the Movie Engine plugin for full movie/series/episode support.
5. Customize via Appearance > Customize.

== Frequently Asked Questions ==

= Does this theme require the Movie Engine plugin? =

No. The theme works standalone, but the Movie Engine plugin enables movie, series, episode, and playlist pages with automatic header styling.

= Which pages use the transparent header? =

Set Header Style to Transparent, then choose locations under Fixed Header On. Select Pages adds extra pages. Unselected pages use the solid header. The homepage title banner appears only when Page Title → Front Page is selected. On mobile, the transparent header scrolls with content (position relative).

= How do I enable a mega menu? =

1. Go to Appearance → Menus.
2. Expand a top-level Primary Menu item.
3. Check Enable Mega Menu.
4. Choose Mega Menu Columns (2–8).
5. Nest column groups under that item. On a nested item, check Mega column heading to style it as a column title.
6. Save the menu.

Desktop shows a multi-column panel. Mobile keeps the accordion submenu.

== Changelog ==

= 1.0.7 =
* Fix: Form fields use one focus ring (border + soft shadow) instead of a stacked outline on keyboard focus
* Fix: Header live search input no longer adds an extra outline on top of the search box ring

= 1.0.6 =
* Header: Select Pages picker for the fixed header, in addition to the location buttons
* Page title: Front Page is its own option. The homepage banner stays off unless Front Page is selected
* A page no longer prints a second title when the page title banner is already showing

= 1.0.5 =
* Footer: copyright stays full width on its own row, and the menu sits full width on the next row
* Footer stacks and centers on small screens so the copyright text and menu are no longer cut off

= 1.0.4 =
* GitHub updater: re-check for newer releases sooner when the site is already on the cached version (avoids missing update notices for up to 12 hours)

= 1.0.3 =
* Mega Menu: Enable Mega Menu + Columns (2–8) controls on Appearance → Menus
* Mega Menu: desktop multi-column panels with responsive column caps; hover bridge fix; parent/child typography
* Mega Menu: mobile/tablet accordion unchanged; touch open support on no-hover desktops

= 1.0.2 =
* Theme updates and GitHub updater support

= 1.0.0 =
* Initial release
* Dual header styles (transparent/solid)
* Mobile-responsive header (relative on mobile, fixed on desktop)
* Movie Engine integration: transparent only on front page, movie, series, episode; solid elsewhere
* Page title hidden on playlist page

== Credits ==

* Based on Underscores https://underscores.me/, (C) 2012-2020 Automattic, Inc., [GPLv2 or later](https://www.gnu.org/licenses/gpl-2.0.html)
* normalize.css https://necolas.github.io/normalize.css/, (C) 2012-2018 Nicolas Gallagher and Jonathan Neal, [MIT](https://opensource.org/licenses/MIT)
