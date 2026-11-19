<?php
/**
 * Style Two global fixed left navigation (logo + menu).
 *
 * @package Hello_Movie Engine
 * @since 1.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Hello_Movie_Engine_Side_Rail {

	/**
	 * Whether the fixed left rail is active on this request.
	 *
	 * @return bool
	 */
	public static function is_active() {
		if ( 'style-two' !== hello_movieengine_get_active_style() ) {
			return false;
		}

		return (bool) get_theme_mod( 'hello_movieengine_side_rail_enable', true );
	}

	/**
	 * Show menu labels without hovering (pinned open state).
	 *
	 * @return bool
	 */
	public static function labels_pinned() {
		return (bool) get_theme_mod( 'hello_movieengine_side_rail_labels_pinned', false );
	}

	/**
	 * Rail widths for CSS variables.
	 *
	 * @return array{width:int, compact:int}
	 */
	public static function get_widths() {
		$width   = max( 56, min( 320, absint( get_theme_mod( 'hello_movieengine_side_rail_width', 240 ) ) ) );
		$compact = max( 56, min( 120, absint( get_theme_mod( 'hello_movieengine_side_rail_compact_width', 80 ) ) ) );

		return array(
			'width'   => $width,
			'compact' => $compact,
		);
	}

	/**
	 * Hook initialization.
	 */
	public static function init() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
		add_filter( 'body_class', array( __CLASS__, 'filter_body_class' ) );
	}

	/**
	 * @param string[] $classes Body classes.
	 * @return string[]
	 */
	public static function filter_body_class( $classes ) {
		if ( ! self::is_active() ) {
			return $classes;
		}

		$classes[] = 'hello-movieengine-has-side-rail';

		if ( self::labels_pinned() ) {
			$classes[] = 'hello-side-rail--labels-pinned';
		}

		return $classes;
	}

	/**
	 * Enqueue side rail assets (Style Two).
	 */
	public static function enqueue_assets() {
		if ( ! self::is_active() ) {
			return;
		}

		$css = Hello_Movie_Engine_Style_Registry::style_path( 'css/side-rail.css' );
		$js  = Hello_Movie_Engine_Style_Registry::style_path( 'js/side-rail.js' );

		if ( file_exists( $css ) ) {
			wp_enqueue_style(
				'hello-movieengine-side-rail',
				Hello_Movie_Engine_Style_Registry::style_uri( 'css/side-rail.css' ),
				array( 'hello-movieengine-base' ),
				HELLO_MOVIEENGINE_VERSION
			);

			$widths  = self::get_widths();
			$sh1     = max( 120, min( 400, absint( get_theme_mod( 'hello_movieengine_side_rail_sh1_width', 200 ) ) ) );
			$sh2     = max( 200, min( 600, absint( get_theme_mod( 'hello_movieengine_side_rail_sh2_width', 450 ) ) ) );
			$inline  = ':root{';
			$inline .= '--hm-side-rail-width:' . $widths['width'] . 'px;';
			$inline .= '--hm-side-rail-compact:' . $widths['compact'] . 'px;';
			$inline .= '--hm-side-rail-sh1-width:' . $sh1 . 'px;';
			$inline .= '--hm-side-rail-sh2-width:' . $sh2 . 'px;';
			$inline .= '}';
			wp_add_inline_style( 'hello-movieengine-side-rail', $inline );
		}

		if ( file_exists( $js ) ) {
			wp_enqueue_script(
				'hello-movieengine-side-rail',
				Hello_Movie_Engine_Style_Registry::style_uri( 'js/side-rail.js' ),
				array( 'jquery' ),
				HELLO_MOVIEENGINE_VERSION,
				true
			);
		}
	}

	/**
	 * Menu location for the left rail (Appearance → Menus).
	 *
	 * @return string
	 */
	public static function get_menu_location() {
		$loc = get_theme_mod( 'hello_movieengine_side_rail_menu_location', 'primary' );
		$locs = get_registered_nav_menus();
		if ( ! is_string( $loc ) || ! isset( $locs[ $loc ] ) ) {
			return 'primary';
		}
		return $loc;
	}

	/**
	 * Whether a WP menu is assigned to the rail location.
	 *
	 * @return bool
	 */
	public static function has_wp_menu() {
		return has_nav_menu( self::get_menu_location() );
	}

	/**
	 * Navigation items (shared Mobile Bottom Nav configuration).
	 *
	 * @return array
	 */
	public static function get_nav_items() {
		if ( ! class_exists( 'Hello_Movie_Engine_Bottom_Nav' ) ) {
			return array();
		}

		return Hello_Movie_Engine_Bottom_Nav::get_nav_items();
	}
}

Hello_Movie_Engine_Side_Rail::init();

/**
 * Shorthand for templates.
 *
 * @return bool
 */
function hello_movieengine_uses_side_rail() {
	return class_exists( 'Hello_Movie_Engine_Side_Rail' ) && Hello_Movie_Engine_Side_Rail::is_active();
}