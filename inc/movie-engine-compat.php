<?php
/**
 * Movie Engine plugin detection and compatibility layer.
 *
 * @package Hello_Movie Engine
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Check if Movie Engine plugin is active.
 */
function hello_movieengine_is_movie_engine_active() {
	return defined( 'MOVIE_ENGINE_VERSION' ) || class_exists( 'MovieEngine\\Plugin' );
}

/**
 * Check if the current page is a Movie Engine archive.
 */
function hello_movieengine_is_movie_engine_archive() {
	if ( ! hello_movieengine_is_movie_engine_active() ) {
		return false;
	}

	$me_types = array(
		'movie_engine_movie',
		'movie_engine_series',
		'movie_engine_person',
	);

	return is_post_type_archive( $me_types );
}

/**
 * Whether the current page is the Movie Engine “Studios” page from Design settings.
 *
 * @return bool
 */
function hello_movieengine_is_studios_page() {
	if ( ! hello_movieengine_is_movie_engine_active() || ! is_page() ) {
		return false;
	}

	$options = get_option( 'movie_engine_design_settings', array() );
	if ( empty( $options['studios_page'] ) ) {
		return false;
	}

	return (int) $options['studios_page'] === (int) get_queried_object_id();
}

/**
 * Whether the request is a single Movie Engine studio taxonomy archive.
 *
 * @return bool
 */
function hello_movieengine_is_studio_taxonomy() {
	return hello_movieengine_is_movie_engine_active() && is_tax( 'movie_engine_studio' );
}

/**
 * Movie Engine post types that this theme supports.
 */
function hello_movieengine_get_movie_engine_post_types() {
	return array(
		'movie_engine_movie',
		'movie_engine_series',
		'movie_engine_episode',
		'movie_engine_person',
		'movie_engine_season',
	);
}

/**
 * Add Movie Engine post type support for thumbnails.
 */
function hello_movieengine_movie_engine_post_type_support() {
	if ( ! hello_movieengine_is_movie_engine_active() ) {
		return;
	}

	foreach ( hello_movieengine_get_movie_engine_post_types() as $pt ) {
		if ( post_type_exists( $pt ) ) {
			add_post_type_support( $pt, 'thumbnail' );
		}
	}
}
add_action( 'init', 'hello_movieengine_movie_engine_post_type_support', 20 );

/**
 * Adjust the document title for Movie Engine archives if needed.
 */
function hello_movieengine_movie_engine_archive_title( $title ) {
	if ( ! hello_movieengine_is_movie_engine_active() ) {
		return $title;
	}

	if ( is_post_type_archive( 'movie_engine_movie' ) ) {
		$title = esc_html__( 'Movies', 'hello-movieengine' );
	} elseif ( is_post_type_archive( 'movie_engine_series' ) ) {
		$title = esc_html__( 'TV Series', 'hello-movieengine' );
	} elseif ( is_post_type_archive( 'movie_engine_person' ) ) {
		$title = esc_html__( 'People', 'hello-movieengine' );
	}

	return $title;
}
add_filter( 'get_the_archive_title', 'hello_movieengine_movie_engine_archive_title' );

/**
 * Output live Movie Engine palette CSS from current design settings.
 *
 * The plugin caches tokens in uploads/custom-style.css; that file can lag behind
 * Customizer preview or unsaved changes. Inline CSS from get_option() (preview-aware)
 * overrides the cached file so preset/tab changes apply immediately.
 */
function hello_movieengine_enqueue_movie_engine_design_palette() {
	if ( ! hello_movieengine_is_movie_engine_active() || ! class_exists( 'MovieEngine\\Core\\Cache' ) ) {
		return;
	}

	$design = get_option( 'movie_engine_design_settings', array() );
	if ( ! is_array( $design ) ) {
		$design = array();
	}

	$css = \MovieEngine\Core\Cache::generate_design_css( $design );
	if ( empty( $css ) ) {
		return;
	}

	$handles = array( 'movie-engine-custom', 'movie-engine', 'hello-movieengine-base' );
	$handle  = '';
	foreach ( $handles as $candidate ) {
		if ( wp_style_is( $candidate, 'enqueued' ) ) {
			$handle = $candidate;
			break;
		}
	}
	if ( '' === $handle ) {
		return;
	}

	wp_add_inline_style( $handle, $css );
}
add_action( 'wp_enqueue_scripts', 'hello_movieengine_enqueue_movie_engine_design_palette', 100 );

/**
 * Style Two: watch-page hero Back (fullwidth plugin hero + inline cinema).
 * Plugin templates are not modified; Style Two wraps them via movie_engine_get_template_part.
 */

/**
 * Fallback URL for watch-page hero Back (archive / browse / home).
 *
 * @param string $context `movie` or `series`.
 * @return string
 */
function hello_movieengine_get_watch_back_href( $context = 'movie' ) {
	$design = get_option( 'movie_engine_design_settings', array() );
	$href   = '';

	if ( 'series' === $context ) {
		$href = get_post_type_archive_link( 'movie_engine_series' );
		if ( ! empty( $design['series_archive_page'] ) ) {
			$page_href = get_permalink( (int) $design['series_archive_page'] );
			if ( $page_href ) {
				$href = $page_href;
			}
		}
	} else {
		$href = get_post_type_archive_link( 'movie_engine_movie' );
		if ( ! empty( $design['movie_archive_page'] ) ) {
			$page_href = get_permalink( (int) $design['movie_archive_page'] );
			if ( $page_href ) {
				$href = $page_href;
			}
		}
	}

	if ( empty( $href ) && ! empty( $design['browse_page'] ) ) {
		$browse_href = get_permalink( (int) $design['browse_page'] );
		if ( $browse_href ) {
			$href = $browse_href;
		}
	}

	if ( empty( $href ) ) {
		$href = home_url( '/' );
	}

	return $href;
}

/**
 * Home URL for browse archive Back control.
 *
 * @return string
 */
function hello_movieengine_get_home_back_href() {
	$front_page_id = (int) get_option( 'page_on_front' );
	if ( $front_page_id > 0 ) {
		$permalink = get_permalink( $front_page_id );
		if ( $permalink ) {
			return $permalink;
		}
	}

	return home_url( '/' );
}

/**
 * Back link markup for single movie / series heroes.
 *
 * @param string $context `movie` or `series`.
 * @return string
 */
function hello_movieengine_get_watch_hero_back_html( $context = 'movie' ) {
	$href = hello_movieengine_get_watch_back_href( $context );

	ob_start();
	?>
	<a
		class="hme-watch-hero__back"
		href="<?php echo esc_url( $href ); ?>"
		data-fallback-href="<?php echo esc_url( $href ); ?>"
		data-back-mode="fallback">
		<span class="fa-solid fa-arrow-left" aria-hidden="true"></span>
		<?php esc_html_e( 'Back', 'hello-movieengine' ); ?>
	</a>
	<?php
	return ob_get_clean();
}

/**
 * Back link for Style Two movie / TV series browse archives (→ home).
 *
 * @return string
 */
function hello_movieengine_get_browse_archive_back_html() {
	$href = hello_movieengine_get_home_back_href();

	ob_start();
	?>
	<a
		class="hme-watch-hero__back hme-browse-archive__back"
		href="<?php echo esc_url( $href ); ?>"
		data-fallback-href="<?php echo esc_url( $href ); ?>"
		data-back-mode="fallback">
		<span class="fa-solid fa-arrow-left" aria-hidden="true"></span>
		<?php esc_html_e( 'Back', 'hello-movieengine' ); ?>
	</a>
	<?php
	return ob_get_clean();
}

/**
 * Inject Back link immediately inside the plugin hero root element.
 *
 * @param string $html    Rendered plugin hero HTML.
 * @param string $context `movie` or `series`.
 * @return string
 */
function hello_movieengine_inject_watch_hero_back( $html, $context = 'movie' ) {
	if ( false !== strpos( $html, 'hme-watch-hero__back' ) ) {
		return $html;
	}

	$markup = hello_movieengine_get_watch_hero_back_html( $context );
	if ( '' === $markup ) {
		return $html;
	}

	$count    = 0;
	$replaced = preg_replace(
		'#(<div class="movie-engine-movie-hero"[^>]*>)#',
		'$1' . $markup,
		$html,
		1,
		$count
	);

	return ( $count > 0 && null !== $replaced ) ? $replaced : $html;
}

/**
 * Enqueue hero Back behavior on single movie & TV series pages.
 */
function hello_movieengine_enqueue_watch_hero_back_script() {
	if ( 'style-two' !== hello_movieengine_get_active_style() ) {
		return;
	}

	if ( ! hello_movieengine_is_movie_engine_active() ) {
		return;
	}

	$is_player_endpoint = isset( $_SERVER['REQUEST_URI'] ) && preg_match( '#/player/?(\?|$)#', sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) );
	$is_watch_singular  = is_singular( array( 'movie_engine_movie', 'movie_engine_series', 'movie_engine_episode' ) );
	$is_me_archive      = is_post_type_archive( array( 'movie_engine_movie', 'movie_engine_series' ) );

	if ( ! $is_watch_singular && ! $is_player_endpoint && ! $is_me_archive ) {
		return;
	}

	$path = HELLO_MOVIEENGINE_DIR . '/assets/core/js/watch-hero-back.js';
	if ( ! file_exists( $path ) ) {
		return;
	}

	wp_enqueue_script(
		'hello-movieengine-watch-hero-back',
		get_template_directory_uri() . '/assets/core/js/watch-hero-back.js',
		array(),
		filemtime( $path ),
		true
	);
}
add_action( 'wp_enqueue_scripts', 'hello_movieengine_enqueue_watch_hero_back_script', 25 );

/**
 * Sanitize Movie Engine design settings to prevent layout breakdowns.
 *
 * Cine Movie Engine series episodes template only supports 'list' and 'grid'.
 * If 'tabs' or any legacy invalid value is stored, normalize it to 'list'.
 *
 * @param mixed $settings Options array.
 * @return mixed Normalized options array.
 */
function hello_movieengine_sanitize_design_settings( $settings ) {
	if ( is_array( $settings ) ) {
		if ( isset( $settings['series_seasons_layout'] ) && ! in_array( $settings['series_seasons_layout'], array( 'list', 'grid' ), true ) ) {
			$settings['series_seasons_layout'] = 'list';
		}
		if ( isset( $settings['movie_watch_hero_height_vh'] ) ) {
			$settings['movie_watch_hero_height_vh'] = hello_movieengine_sanitize_watch_hero_height_vh( $settings['movie_watch_hero_height_vh'] );
		}
		if ( isset( $settings['series_watch_hero_height_vh'] ) ) {
			$settings['series_watch_hero_height_vh'] = hello_movieengine_sanitize_watch_hero_height_vh( $settings['series_watch_hero_height_vh'] );
		}
	}
	return $settings;
}

/**
 * CSS custom properties for single movie / TV series watch heroes.
 *
 * @return string Declarations without selector wrapper.
 */
function hello_movieengine_watch_hero_root_vars() {
	if ( ! hello_movieengine_is_movie_engine_active() ) {
		return '';
	}

	$design = get_option( 'movie_engine_design_settings', array() );
	if ( ! is_array( $design ) ) {
		return '';
	}

	$vars = '';

	$movie_vh = isset( $design['movie_watch_hero_height_vh'] ) ? hello_movieengine_sanitize_watch_hero_height_vh( $design['movie_watch_hero_height_vh'] ) : 0;
	if ( $movie_vh >= 40 ) {
		$vars .= '--me-watch-movie-hero-height:' . $movie_vh . 'vh;';
	}

	$series_vh = isset( $design['series_watch_hero_height_vh'] ) ? hello_movieengine_sanitize_watch_hero_height_vh( $design['series_watch_hero_height_vh'] ) : 0;
	if ( $series_vh >= 40 ) {
		$vars .= '--me-watch-series-hero-height:' . $series_vh . 'vh;';
	}

	return $vars;
}
add_filter( 'option_movie_engine_design_settings', 'hello_movieengine_sanitize_design_settings' );
add_filter( 'default_option_movie_engine_design_settings', 'hello_movieengine_sanitize_design_settings' );

