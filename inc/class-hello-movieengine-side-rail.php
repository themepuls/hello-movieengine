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

		if ( ! (bool) get_theme_mod( 'hello_movieengine_side_rail_enable', true ) ) {
			return false;
		}

		// Check pages excluded from side rail (pages that use default top header).
		$excluded_raw = get_theme_mod( 'hello_movieengine_side_rail_exclude_pages', '[]' );
		$excluded     = json_decode( (string) $excluded_raw, true );
		if ( is_array( $excluded ) && ! empty( $excluded ) ) {
			$current_id = get_queried_object_id();
			if ( $current_id && in_array( $current_id, array_map( 'absint', $excluded ), true ) ) {
				return false;
			}
		}

		return true;
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
				filemtime( $css )
			);

			$widths  = self::get_widths();
			$sh1     = max( 120, min( 400, absint( get_theme_mod( 'hello_movieengine_side_rail_sh1_width', 200 ) ) ) );
			$sh2     = max( 200, min( 600, absint( get_theme_mod( 'hello_movieengine_side_rail_sh2_width', 450 ) ) ) );
			$logo_h  = max( 20, min( 60, absint( get_theme_mod( 'hello_movieengine_side_rail_logo_height', 34 ) ) ) );
			$inline  = ':root{';
			$inline .= '--hm-side-rail-width:' . $widths['width'] . 'px;';
			$inline .= '--hm-side-rail-compact:' . $widths['compact'] . 'px;';
			$inline .= '--hm-side-rail-sh1-width:' . $sh1 . 'px;';
			$inline .= '--hm-side-rail-sh2-width:' . $sh2 . 'px;';
			$inline .= '--hm-side-rail-logo-h:' . $logo_h . 'px;';
			$inline .= '}';
			$inline .= '.hello-movieengine-side-rail__fly.hello-movieengine-side-rail__fly--mega-rich{position:absolute!important;left:100%!important;display:flex!important;flex-direction:column!important;overflow:hidden!important;max-height:calc(100vh - 32px)!important;padding:0!important;border-radius:18px;clip-path:inset(0 round 18px);}';
			$inline .= '.hello-movieengine-side-rail__fly--mega-rich>.hello-movieengine-mega-panel{flex:1 1 auto!important;min-height:0!important;max-height:calc(100vh - 32px)!important;overflow-x:hidden!important;overflow-y:auto!important;padding:20px 14px 18px 22px!important;}';
			$inline .= '.hello-movieengine-mega-panel,.hello-movieengine-side-rail__fly-list{color-scheme:dark;scrollbar-width:thin!important;scrollbar-color:rgba(255,255,255,.4) rgba(255,255,255,.06)!important;}';
			$inline .= '.hello-movieengine-mega-panel::-webkit-scrollbar,.hello-movieengine-side-rail__fly-list::-webkit-scrollbar,.hello-movieengine-side-rail__fly::-webkit-scrollbar{-webkit-appearance:none!important;width:2px!important;height:2px!important;background:transparent!important;}';
			$inline .= '.hello-movieengine-mega-panel::-webkit-scrollbar-thumb,.hello-movieengine-side-rail__fly-list::-webkit-scrollbar-thumb{background:rgba(255,255,255,.4)!important;border-radius:99px!important;}';
			wp_add_inline_style( 'hello-movieengine-side-rail', $inline );
		}

		if ( file_exists( $js ) ) {
			wp_enqueue_script(
				'hello-movieengine-side-rail',
				Hello_Movie_Engine_Style_Registry::style_uri( 'js/side-rail.js' ),
				array( 'jquery' ),
				filemtime( $js ),
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