<?php
/**
 * Hello Movie Engine functions and definitions
 *
 * @package Hello_Movie Engine
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'HELLO_MOVIEENGINE_VERSION', '1.2.0' );
define( 'HELLO_MOVIEENGINE_DIR', get_template_directory() );
define( 'HELLO_MOVIEENGINE_URI', get_template_directory_uri() );

/**
 * Theme setup: supports, menus, content width.
 */
require HELLO_MOVIEENGINE_DIR . '/inc/theme-setup.php';

/**
 * Widget areas.
 */
require HELLO_MOVIEENGINE_DIR . '/inc/widgets.php';

/**
 * Site style registry (Style One / Style Two).
 */
require HELLO_MOVIEENGINE_DIR . '/inc/styles/class-hello-movieengine-style-registry.php';
Hello_Movie_Engine_Style_Registry::init();

/**
 * OTT Feed (Style Two) helpers.
 */
require HELLO_MOVIEENGINE_DIR . '/inc/ott-feed/functions.php';

/**
 * Animation / motion (Customizer-controlled).
 */
require HELLO_MOVIEENGINE_DIR . '/inc/motion.php';

/**
 * Enqueue styles and scripts.
 */
require HELLO_MOVIEENGINE_DIR . '/inc/enqueue.php';

/**
 * Template helper functions.
 */
require HELLO_MOVIEENGINE_DIR . '/inc/template-functions.php';

/**
 * Page title: per-page show/hide (block editor).
 */
require HELLO_MOVIEENGINE_DIR . '/inc/page-title-meta.php';

/**
 * Mega Menu (Appearance → Menus fields).
 */
require HELLO_MOVIEENGINE_DIR . '/inc/mega-menu.php';

/**
 * Side rail menu icons (Appearance → Menus).
 */
require HELLO_MOVIEENGINE_DIR . '/inc/menu-icons.php';

/**
 * Side rail WP menu walker.
 */
require HELLO_MOVIEENGINE_DIR . '/inc/class-hello-movieengine-side-rail-walker.php';

/**
 * Header mega menu walker (banner + movie rows).
 */
require HELLO_MOVIEENGINE_DIR . '/inc/class-hello-movieengine-header-mega-walker.php';

/**
 * Template tags.
 */
require HELLO_MOVIEENGINE_DIR . '/inc/template-tags.php';

/**
 * Customizer options.
 */
require HELLO_MOVIEENGINE_DIR . '/inc/customizer/class-hello-movieengine-customizer.php';

/**
 * Style Two global left navigation.
 */
require HELLO_MOVIEENGINE_DIR . '/inc/class-hello-movieengine-side-rail.php';

/**
 * Mobile Bottom Navigation.
 */
require HELLO_MOVIEENGINE_DIR . '/inc/class-hello-movieengine-bottom-nav.php';

/**
 * Movie Engine plugin compatibility.
 */
require HELLO_MOVIEENGINE_DIR . '/inc/movie-engine-compat.php';

/**
 * GitHub theme updates (REMOVE this require + delete the file before WordPress.org submit).
 */
require HELLO_MOVIEENGINE_DIR . '/inc/github-theme-updater.php';