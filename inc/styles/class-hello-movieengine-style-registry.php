<?php
/**
 * Site style registry (Style One / Style Two assets and template paths).
 *
 * @package Hello_Movie Engine
 * @since 1.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Hello_Movie_Engine_Style_Registry {

	const DEFAULT_STYLE = 'style-one';

	const SETTING_KEY = 'hello_movieengine_site_style';

	/**
	 * Registered styles.
	 *
	 * @return array<string, array{label:string,description:string}>
	 */
	public static function get_styles() {
		$styles = array(
			'style-one' => array(
				'label'       => __( 'Classic (Style One)', 'hello-movieengine' ),
				'description' => __( 'Default Hello MovieEngine layout and spacing.', 'hello-movieengine' ),
			),
			'style-two' => array(
				'label'       => __( 'OTT Feed (Style Two)', 'hello-movieengine' ),
				'description' => __( 'Full-width home rows and streaming-style sections (themoviestime-style).', 'hello-movieengine' ),
			),
		);

		return apply_filters( 'hello_movieengine_registered_styles', $styles );
	}

	/**
	 * Active style slug.
	 *
	 * @return string
	 */
	public static function get_active_style() {
		$style  = get_theme_mod( self::SETTING_KEY, self::DEFAULT_STYLE );
		$styles = self::get_styles();

		return array_key_exists( $style, $styles ) ? $style : self::DEFAULT_STYLE;
	}

	/**
	 * Boot hooks.
	 */
	public static function init() {
		add_filter( 'body_class', array( __CLASS__, 'filter_body_class' ) );
		add_filter( 'movie_engine_get_template_part', array( __CLASS__, 'filter_movie_engine_template' ), 10, 3 );
	}

	/**
	 * Body classes for the active style pack.
	 *
	 * @param string[] $classes Body classes.
	 * @return string[]
	 */
	public static function filter_body_class( $classes ) {
		$style     = self::get_active_style();
		$classes[] = 'hmestyle-' . $style;
		$classes[] = 'hello-movieengine-' . $style;

		return $classes;
	}

	/**
	 * Absolute path to inc/templates (layout + plugin overrides).
	 *
	 * @param string $relative Path under inc/templates/.
	 * @return string
	 */
	public static function templates_path( $relative = '' ) {
		$base = trailingslashit( HELLO_MOVIEENGINE_DIR ) . 'inc/templates/';
		if ( '' === $relative ) {
			return $base;
		}

		return $base . ltrim( $relative, '/' );
	}

	/**
	 * Layout view: inc/templates/{area}/{style}/{file}.php
	 *
	 * @param string $area  header, footer, navigation.
	 * @param string $style style-one, style-two.
	 * @param string $file  transparent, default, bottom-nav, etc.
	 * @return string Empty if missing.
	 */
	public static function layout_template_file( $area, $style, $file ) {
		if ( strpos( $area, '..' ) !== false || strpos( $style, '..' ) !== false || strpos( $file, '..' ) !== false ) {
			return '';
		}

		$path = self::templates_path( "{$area}/{$style}/{$file}.php" );

		return is_readable( $path ) ? $path : '';
	}

	/**
	 * Plugin overrides: inc/templates/movie-engine/{style}/ (mirror plugin slug paths).
	 *
	 * @param string $template Resolved plugin template path.
	 * @param string $slug     Template slug.
	 * @param string $name     Template name.
	 * @return string
	 */
	public static function filter_movie_engine_template( $template, $slug, $name ) {
		if ( '' === $slug || strpos( $slug, '..' ) !== false ) {
			return $template;
		}

		$style    = self::get_active_style();
		$relative = $slug . ( $name ? '-' . $name : '' ) . '.php';

		if ( strpos( $relative, '..' ) !== false ) {
			return $template;
		}

		$candidate = self::templates_path( 'movie-engine/' . $style . '/' . $relative );
		if ( is_readable( $candidate ) ) {
			return $candidate;
		}

		return $template;
	}

	/**
	 * Ordered CSS bundle file names (without .css) for the active style.
	 *
	 * @return string[]
	 */
	public static function get_stylesheet_bundle() {
		$style = self::get_active_style();
		$files = array(
			'layout',
			'header',
			'footer',
			'components',
			'responsive',
		);

		if ( hello_movieengine_is_movie_engine_active() ) {
			$files[] = 'movie-engine-compat';
		}

		if ( 'style-two' === $style ) {
			$files[] = 'home-feed';
		}

		return apply_filters( 'hello_movieengine_style_stylesheet_bundle', $files, $style );
	}

	/**
	 * Path under assets/{active-style}/.
	 *
	 * @param string $relative e.g. css/header.css or js/bottom-nav.js.
	 * @return string
	 */
	public static function style_path( $relative ) {
		return trailingslashit( HELLO_MOVIEENGINE_DIR ) . 'assets/' . self::get_active_style() . '/' . ltrim( $relative, '/' );
	}

	/**
	 * URL under assets/{active-style}/.
	 *
	 * @param string $relative e.g. css/header.css.
	 * @return string
	 */
	public static function style_uri( $relative ) {
		return trailingslashit( HELLO_MOVIEENGINE_URI ) . 'assets/' . self::get_active_style() . '/' . ltrim( $relative, '/' );
	}

	/**
	 * Core asset URL (shared across styles).
	 *
	 * @param string $relative Path under assets/core/.
	 * @return string
	 */
	public static function core_uri( $relative ) {
		return trailingslashit( HELLO_MOVIEENGINE_URI ) . 'assets/core/' . ltrim( $relative, '/' );
	}

	/**
	 * Enqueue active style CSS bundle from assets/{style}/css/.
	 *
	 * @param string $version Theme version string.
	 */
	public static function enqueue_styles( $version ) {
		$bundle   = self::get_stylesheet_bundle();
		$previous = 'hello-movieengine-base';

		foreach ( $bundle as $name ) {
			$handle = 'hello-movieengine-' . sanitize_key( str_replace( '/', '-', $name ) );
			$path   = self::style_path( 'css/' . $name . '.css' );

			if ( ! file_exists( $path ) ) {
				continue;
			}

			wp_enqueue_style( $handle, self::style_uri( 'css/' . $name . '.css' ), array( $previous ), $version );
			$previous = $handle;
		}
	}
}

/**
 * Active site style slug.
 *
 * @return string
 */
function hello_movieengine_get_active_style() {
	return Hello_Movie_Engine_Style_Registry::get_active_style();
}

/**
 * Load inc/templates/{area}/{active-style}/{name}.php
 *
 * @param string      $area  header, footer, navigation.
 * @param string|null $name  e.g. transparent, solid, default, bottom-nav.
 */
function hello_movieengine_get_style_template_part( $area, $name = null ) {
	$style    = hello_movieengine_get_active_style();
	$filename = $name ? $name : $area;
	$template = Hello_Movie_Engine_Style_Registry::layout_template_file( $area, $style, $filename );

	if ( '' !== $template ) {
		load_template( $template, false );
		return;
	}

	$fallback = Hello_Movie_Engine_Style_Registry::layout_template_file(
		$area,
		Hello_Movie_Engine_Style_Registry::DEFAULT_STYLE,
		$filename
	);

	if ( '' !== $fallback ) {
		load_template( $fallback, false );
	}
}