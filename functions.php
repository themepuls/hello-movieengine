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

define( 'HELLO_MOVIEENGINE_VERSION', '1.0.0' );
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
 * Enqueue styles and scripts.
 */
require HELLO_MOVIEENGINE_DIR . '/inc/enqueue.php';

/**
 * Template helper functions.
 */
require HELLO_MOVIEENGINE_DIR . '/inc/template-functions.php';

/**
 * Template tags.
 */
require HELLO_MOVIEENGINE_DIR . '/inc/template-tags.php';

/**
 * Customizer options.
 */
require HELLO_MOVIEENGINE_DIR . '/inc/customizer/class-hello-movieengine-customizer.php';

/**
 * Movie Engine plugin compatibility.
 */
require HELLO_MOVIEENGINE_DIR . '/inc/movie-engine-compat.php';