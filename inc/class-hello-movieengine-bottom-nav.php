<?php
/**
 * Hello MovieEngine Mobile Bottom Navigation handler.
 *
 * Resolves dynamic destinations, permissions, active states, and renders the mobile bottom bar.
 *
 * @package Hello_Movie Engine
 * @since 1.0.8
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Hello_Movie_Engine_Bottom_Nav {

	/**
	 * Hook initialization.
	 */
	public static function init() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
		add_filter( 'body_class', array( __CLASS__, 'filter_body_class' ) );
	}

	/**
	 * Default items configuration.
	 *
	 * @return array
	 */
	public static function get_default_items_config() {
		return array(
			array(
				'id'          => 'nav_home',
				'type'        => 'home',
				'label'       => '',
				'icon'        => 'svg:home',
				'custom_icon' => '',
				'url'         => '',
				'page_id'     => 0,
				'visibility'  => 'all',
				'enabled'     => true,
			),
			array(
				'id'          => 'nav_movies',
				'type'        => 'movies',
				'label'       => '',
				'icon'        => 'svg:film',
				'custom_icon' => '',
				'url'         => '',
				'page_id'     => 0,
				'visibility'  => 'all',
				'enabled'     => true,
			),
			array(
				'id'          => 'nav_series',
				'type'        => 'series',
				'label'       => '',
				'icon'        => 'svg:tv',
				'custom_icon' => '',
				'url'         => '',
				'page_id'     => 0,
				'visibility'  => 'all',
				'enabled'     => true,
			),
			array(
				'id'          => 'nav_watchlist',
				'type'        => 'watchlist',
				'label'       => '',
				'icon'        => 'svg:bookmark-plus',
				'custom_icon' => '',
				'url'         => '',
				'page_id'     => 0,
				'visibility'  => 'all',
				'enabled'     => true,
			),
			array(
				'id'          => 'nav_account',
				'type'        => 'account',
				'label'       => '',
				'icon'        => 'svg:user',
				'custom_icon' => '',
				'url'         => '',
				'page_id'     => 0,
				'visibility'  => 'all',
				'enabled'     => true,
			),
		);
	}

	/**
	 * Check if mobile bottom nav is enabled for the current request.
	 *
	 * @return bool
	 */
	public static function is_enabled() {
		$enabled = get_theme_mod( 'hello_movieengine_bottom_nav_enable', true );
		if ( ! $enabled ) {
			return false;
		}

		// Check excluded pages.
		$excluded_raw = get_theme_mod( 'hello_movieengine_bottom_nav_exclude_pages', '[]' );
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
	 * Get responsive breakpoint setting ('mobile' or 'tablet').
	 *
	 * @return string
	 */
	public static function get_breakpoint() {
		$breakpoint = get_theme_mod( 'hello_movieengine_bottom_nav_breakpoint', 'mobile' );
		return in_array( $breakpoint, array( 'mobile', 'tablet' ), true ) ? $breakpoint : 'mobile';
	}

	/**
	 * Check if labels should be displayed.
	 *
	 * @return bool
	 */
	public static function show_labels() {
		return (bool) get_theme_mod( 'hello_movieengine_bottom_nav_show_labels', true );
	}

	/**
	 * Filter body classes to add clearance and breakpoint modifiers.
	 *
	 * @param array $classes Body classes.
	 * @return array
	 */
	public static function filter_body_class( $classes ) {
		if ( self::is_enabled() ) {
			$classes[] = 'has-hello-bottom-nav';
			$classes[] = 'hello-bottom-nav--bp-' . self::get_breakpoint();
			$classes[] = self::show_labels() ? 'hello-bottom-nav--has-labels' : 'hello-bottom-nav--no-labels';
		}
		return $classes;
	}

	/**
	 * Enqueue frontend CSS and JS.
	 */
	public static function enqueue_assets() {
		if ( ! self::is_enabled() ) {
			return;
		}

		$v = HELLO_MOVIEENGINE_VERSION;

		wp_enqueue_style(
			'hello-movieengine-bottom-nav',
			HELLO_MOVIEENGINE_URI . '/assets/css/bottom-nav.css',
			array( 'hello-movieengine-base' ),
			$v
		);

		wp_enqueue_script(
			'hello-movieengine-bottom-nav',
			HELLO_MOVIEENGINE_URI . '/assets/js/bottom-nav.js',
			array( 'jquery' ),
			$v,
			true
		);

		// Output dynamic CSS custom properties for custom colors.
		self::output_inline_colors();
	}

	/**
	 * Output inline color variables for the navigation bar.
	 */
	protected static function output_inline_colors() {
		$bg_color     = get_theme_mod( 'hello_movieengine_bottom_nav_bg_color', 'rgba(18, 18, 18, 0.96)' );
		$border_color = get_theme_mod( 'hello_movieengine_bottom_nav_border_color', 'rgba(255, 255, 255, 0.08)' );
		$icon_color   = get_theme_mod( 'hello_movieengine_bottom_nav_icon_color', '#a0a0a0' );
		$label_color  = get_theme_mod( 'hello_movieengine_bottom_nav_label_color', '#8d8d8d' );
		$active_color = get_theme_mod( 'hello_movieengine_bottom_nav_active_color', '' );

		$css = ':root {';
		if ( $bg_color ) {
			$css .= '--hello-bottom-nav-bg: ' . esc_attr( $bg_color ) . ';';
		}
		if ( $border_color ) {
			$css .= '--hello-bottom-nav-border: ' . esc_attr( $border_color ) . ';';
		}
		if ( $icon_color ) {
			$css .= '--hello-bottom-nav-icon: ' . esc_attr( $icon_color ) . ';';
		}
		if ( $label_color ) {
			$css .= '--hello-bottom-nav-label: ' . esc_attr( $label_color ) . ';';
		}
		if ( $active_color ) {
			$css .= '--hello-bottom-nav-active: ' . esc_attr( $active_color ) . ';';
		}
		$css .= '}';

		wp_add_inline_style( 'hello-movieengine-bottom-nav', $css );
	}

	/**
	 * Retrieve MovieEngine design settings array.
	 *
	 * @return array
	 */
	public static function get_movie_engine_settings() {
		return get_option( 'movie_engine_design_settings', array() );
	}

	/**
	 * Resolve destination URL and default label for an item.
	 *
	 * @param array $item Navigation item configuration.
	 * @return array Processed item with resolved 'url', 'label', and 'is_button'.
	 */
	public static function resolve_item_destination( $item ) {
		$type        = isset( $item['type'] ) ? $item['type'] : 'custom';
		$custom_url  = isset( $item['url'] ) ? trim( $item['url'] ) : '';
		$page_id     = isset( $item['page_id'] ) ? absint( $item['page_id'] ) : 0;
		$user_label  = isset( $item['label'] ) ? trim( $item['label'] ) : '';
		$me_settings = self::get_movie_engine_settings();
		$is_logged_in= is_user_logged_in();

		$url           = '';
		$default_label = '';
		$is_button     = false;

		// If a page ID override is provided, use it.
		if ( $page_id > 0 ) {
			$url = get_permalink( $page_id );
		}

		// Helper dashboard and login URLs.
		$dashboard_page_id = ! empty( $me_settings['dashboard_page'] ) ? absint( $me_settings['dashboard_page'] ) : 0;
		$dashboard_url     = $dashboard_page_id ? get_permalink( $dashboard_page_id ) : home_url( '/dashboard/' );

		$login_page_id = ! empty( $me_settings['login_page'] ) ? absint( $me_settings['login_page'] ) : 0;
		$login_url     = $login_page_id ? get_permalink( $login_page_id ) : wp_login_url();

		$pricing_page_id = ! empty( $me_settings['pricing_page'] ) ? absint( $me_settings['pricing_page'] ) : 0;
		$pricing_url     = $pricing_page_id ? get_permalink( $pricing_page_id ) : home_url( '/pricing/' );

		switch ( $type ) {
			case 'home':
				$default_label = esc_html__( 'Home', 'hello-movieengine' );
				if ( ! $url ) {
					$url = home_url( '/' );
				}
				break;

			case 'movies':
				$default_label = esc_html__( 'Movies', 'hello-movieengine' );
				if ( ! $url ) {
					if ( ! empty( $me_settings['movie_archive_page'] ) ) {
						$url = get_permalink( absint( $me_settings['movie_archive_page'] ) );
					} elseif ( ! empty( $me_settings['browse_page'] ) ) {
						$url = get_permalink( absint( $me_settings['browse_page'] ) );
					} elseif ( post_type_exists( 'movie_engine_movie' ) ) {
						$url = get_post_type_archive_link( 'movie_engine_movie' );
					}
					if ( ! $url ) {
						$url = home_url( '/movies/' );
					}
				}
				break;

			case 'series':
				$default_label = esc_html__( 'Series', 'hello-movieengine' );
				if ( ! $url ) {
					if ( ! empty( $me_settings['series_archive_page'] ) ) {
						$url = get_permalink( absint( $me_settings['series_archive_page'] ) );
					} elseif ( ! empty( $me_settings['tv_series_page'] ) ) {
						$url = get_permalink( absint( $me_settings['tv_series_page'] ) );
					} elseif ( post_type_exists( 'movie_engine_series' ) ) {
						$url = get_post_type_archive_link( 'movie_engine_series' );
					}
					if ( ! $url ) {
						$url = home_url( '/tv-series/' );
					}
				}
				break;

			case 'watchlist':
				$default_label = esc_html__( 'Watchlist', 'hello-movieengine' );
				if ( ! $url ) {
					$watchlist_dest = add_query_arg( 'movie_engine_view', 'watchlist', $dashboard_url );
					if ( $is_logged_in ) {
						$url = $watchlist_dest;
					} else {
						// Redirect to login page with redirect_to pointing to watchlist in dashboard.
						$url = add_query_arg( 'redirect_to', rawurlencode( $watchlist_dest ), $login_url );
					}
				}
				break;

			case 'account':
				$default_label = $is_logged_in ? esc_html__( 'Account', 'hello-movieengine' ) : esc_html__( 'Sign In', 'hello-movieengine' );
				if ( ! $url ) {
					$url = $is_logged_in ? $dashboard_url : $login_url;
				}
				break;

			case 'search':
				$default_label = esc_html__( 'Search', 'hello-movieengine' );
				$is_button     = true; // Open search overlay by default.
				if ( ! $url ) {
					if ( ! empty( $me_settings['search_page'] ) ) {
						$url = get_permalink( absint( $me_settings['search_page'] ) );
					} else {
						$url = home_url( '/?s=' );
					}
				}
				break;

			case 'genres':
				$default_label = esc_html__( 'Genres', 'hello-movieengine' );
				if ( ! $url ) {
					$url = home_url( '/genres/' );
				}
				break;

			case 'subscription':
				$default_label = esc_html__( 'Subscribe', 'hello-movieengine' );
				if ( ! $url ) {
					$url = $pricing_url;
				}
				break;

			case 'custom':
			default:
				$default_label = esc_html__( 'Link', 'hello-movieengine' );
				if ( ! $url && $custom_url ) {
					$url = $custom_url;
				}
				break;
		}

		// Custom URL override takes precedence if explicitly provided for any item.
		if ( $custom_url && ( 'custom' === $type || $custom_url !== '' ) ) {
			$url = $custom_url;
		}

		$final_label = ! empty( $user_label ) ? $user_label : $default_label;

		return array(
			'url'       => $url,
			'label'     => $final_label,
			'is_button' => $is_button,
		);
	}

	/**
	 * Get resolved and filtered navigation items.
	 *
	 * @return array
	 */
	public static function get_nav_items() {
		$raw_value = get_theme_mod( 'hello_movieengine_bottom_nav_items', '' );
		$items     = json_decode( (string) $raw_value, true );

		if ( ! is_array( $items ) || empty( $items ) ) {
			$items = self::get_default_items_config();
		}

		$is_logged_in = is_user_logged_in();
		$visible_items= array();

		$legacy_map = array(
			'fa-solid fa-house'            => 'svg:home',
			'fa-solid fa-film'             => 'svg:film',
			'fa-solid fa-tv'               => 'svg:tv',
			'fa-solid fa-plus'             => 'svg:bookmark-plus',
			'fa-regular fa-user'           => 'svg:user',
			'fa-solid fa-magnifying-glass' => 'svg:search',
			'fa-solid fa-tags'             => 'svg:tags',
			'fa-solid fa-crown'            => 'svg:crown',
			'fa-solid fa-link'             => 'svg:link',
		);

		foreach ( $items as $item ) {
			if ( ! is_array( $item ) ) {
				continue;
			}

			// Smoothly upgrade legacy solid defaults to sleek modern SVGs
			if ( isset( $item['icon'] ) && isset( $legacy_map[ $item['icon'] ] ) && empty( $item['custom_icon'] ) ) {
				$item['icon'] = $legacy_map[ $item['icon'] ];
			}

			// Enabled check.
			if ( isset( $item['enabled'] ) && ! $item['enabled'] ) {
				continue;
			}

			// Visibility check.
			$visibility = isset( $item['visibility'] ) ? $item['visibility'] : 'all';
			if ( 'logged_in' === $visibility && ! $is_logged_in ) {
				continue;
			}
			if ( 'logged_out' === $visibility && $is_logged_in ) {
				continue;
			}

			// Resolve destination & label.
			$resolved = self::resolve_item_destination( $item );

			// Skip invalid items with no URL and not an action button.
			if ( empty( $resolved['url'] ) && empty( $resolved['is_button'] ) ) {
				continue;
			}

			$item['resolved_url']  = $resolved['url'];
			$item['final_label']   = $resolved['label'];
			$item['is_button']     = $resolved['is_button'];
			$item['is_active']     = false; // will calculate next

			$visible_items[] = $item;
		}

		// Calculate the single active item.
		self::calculate_active_item( $visible_items );

		return $visible_items;
	}

	/**
	 * Calculate which single item is active, ensuring no multiple simultaneous active states.
	 *
	 * @param array &$items Processed items array.
	 */
	protected static function calculate_active_item( &$items ) {
		if ( empty( $items ) ) {
			return;
		}

		$me_settings  = self::get_movie_engine_settings();
		$active_index = -1;

		$dashboard_page_id = ! empty( $me_settings['dashboard_page'] ) ? absint( $me_settings['dashboard_page'] ) : 0;
		$movie_archive_id  = ! empty( $me_settings['movie_archive_page'] ) ? absint( $me_settings['movie_archive_page'] ) : 0;
		$browse_page_id    = ! empty( $me_settings['browse_page'] ) ? absint( $me_settings['browse_page'] ) : 0;
		$series_archive_id = ! empty( $me_settings['series_archive_page'] ) ? absint( $me_settings['series_archive_page'] ) : 0;
		$tv_series_id      = ! empty( $me_settings['tv_series_page'] ) ? absint( $me_settings['tv_series_page'] ) : 0;
		$pricing_page_id   = ! empty( $me_settings['pricing_page'] ) ? absint( $me_settings['pricing_page'] ) : 0;
		$search_page_id    = ! empty( $me_settings['search_page'] ) ? absint( $me_settings['search_page'] ) : 0;
		$login_page_id     = ! empty( $me_settings['login_page'] ) ? absint( $me_settings['login_page'] ) : 0;
		$register_page_id  = ! empty( $me_settings['register_page'] ) ? absint( $me_settings['register_page'] ) : 0;

		$current_page_id = get_queried_object_id();
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$current_view    = isset( $_GET['movie_engine_view'] ) ? sanitize_text_field( wp_unslash( $_GET['movie_engine_view'] ) ) : '';

		// Priority 1: Watchlist.
		$is_watchlist = false;
		if ( $dashboard_page_id && $current_page_id === $dashboard_page_id && 'watchlist' === $current_view ) {
			$is_watchlist = true;
		}

		// Priority 2: Account / Dashboard / Login / Register.
		$is_account = false;
		if ( ! $is_watchlist ) {
			if ( $dashboard_page_id && $current_page_id === $dashboard_page_id ) {
				$is_account = true;
			} elseif ( $login_page_id && $current_page_id === $login_page_id ) {
				$is_account = true;
			} elseif ( $register_page_id && $current_page_id === $register_page_id ) {
				$is_account = true;
			} elseif ( is_page( array( 'login', 'sign-in', 'register', 'dashboard' ) ) ) {
				$is_account = true;
			}
		}

		// Priority 3: Movies.
		$is_movies = false;
		if ( is_singular( 'movie_engine_movie' ) || is_post_type_archive( 'movie_engine_movie' ) ) {
			$is_movies = true;
		} elseif ( $current_page_id && ( $current_page_id === $movie_archive_id || $current_page_id === $browse_page_id ) ) {
			$is_movies = true;
		}

		// Priority 4: Series.
		$is_series = false;
		if ( is_singular( array( 'movie_engine_series', 'movie_engine_season', 'movie_engine_episode' ) ) || is_post_type_archive( 'movie_engine_series' ) ) {
			$is_series = true;
		} elseif ( $current_page_id && ( $current_page_id === $series_archive_id || $current_page_id === $tv_series_id ) ) {
			$is_series = true;
		}

		// Priority 5: Search.
		$is_search = is_search() || ( $search_page_id && $current_page_id === $search_page_id );

		// Priority 6: Subscription.
		$is_subscription = $pricing_page_id && $current_page_id === $pricing_page_id;

		// Priority 7: Genres.
		$is_genres = is_tax( 'movie_engine_genre' );

		// Priority 8: Home.
		$is_home = is_front_page() || is_home();

		// Match item in order of priority.
		if ( $is_watchlist ) {
			$active_index = self::find_first_item_index_by_type( $items, 'watchlist' );
		}

		if ( -1 === $active_index && $is_account ) {
			$active_index = self::find_first_item_index_by_type( $items, 'account' );
		}

		if ( -1 === $active_index && $is_movies ) {
			$active_index = self::find_first_item_index_by_type( $items, 'movies' );
		}

		if ( -1 === $active_index && $is_series ) {
			$active_index = self::find_first_item_index_by_type( $items, 'series' );
		}

		if ( -1 === $active_index && $is_search ) {
			$active_index = self::find_first_item_index_by_type( $items, 'search' );
		}

		if ( -1 === $active_index && $is_subscription ) {
			$active_index = self::find_first_item_index_by_type( $items, 'subscription' );
		}

		if ( -1 === $active_index && $is_genres ) {
			$active_index = self::find_first_item_index_by_type( $items, 'genres' );
		}

		// Custom link check (matches exact URL or selected page).
		if ( -1 === $active_index && $current_page_id ) {
			foreach ( $items as $idx => $item ) {
				if ( ! empty( $item['page_id'] ) && absint( $item['page_id'] ) === $current_page_id ) {
					$active_index = $idx;
					break;
				}
			}
		}

		if ( -1 === $active_index && ! empty( $_SERVER['REQUEST_URI'] ) ) {
			$current_request = wp_unslash( $_SERVER['REQUEST_URI'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			foreach ( $items as $idx => $item ) {
				if ( 'custom' === $item['type'] && ! empty( $item['resolved_url'] ) ) {
					$parsed = wp_parse_url( $item['resolved_url'], PHP_URL_PATH );
					if ( $parsed && strlen( $parsed ) > 1 && untrailingslashit( $parsed ) === untrailingslashit( $current_request ) ) {
						$active_index = $idx;
						break;
					}
				}
			}
		}

		if ( -1 === $active_index && $is_home ) {
			$active_index = self::find_first_item_index_by_type( $items, 'home' );
		}

		// Set active flag for the chosen item.
		if ( $active_index >= 0 && isset( $items[ $active_index ] ) ) {
			$items[ $active_index ]['is_active'] = true;
		}
	}

	/**
	 * Find the index of the first item matching a given type.
	 *
	 * @param array  $items Items list.
	 * @param string $type Item type.
	 * @return int Index or -1 if not found.
	 */
	protected static function find_first_item_index_by_type( $items, $type ) {
		foreach ( $items as $idx => $item ) {
			if ( isset( $item['type'] ) && $item['type'] === $type ) {
				return $idx;
			}
		}
		return -1;
	}

	/**
	/**
	 * Available icon library for bottom navigation.
	 *
	 * Curated modern, sleek line icons (1.75px stroke) + FontAwesome options.
	 *
	 * @return array
	 */
	public static function get_icon_library() {
		static $icons = null;
		if ( null !== $icons ) {
			return $icons;
		}

		$icons = array(
			// ==========================================
			// 1. Streaming, Cinema & Media (media)
			// ==========================================
			'home' => array(
				'name'     => esc_html__( 'Home', 'hello-movieengine' ),
				'category' => 'media',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>',
			),
			'film' => array(
				'name'     => esc_html__( 'Movies / Film', 'hello-movieengine' ),
				'category' => 'media',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="20" height="20" x="2" y="2" rx="2.5"/><path d="M7 2v20M17 2v20M2 12h20M2 7h5M2 17h5M17 17h5M17 7h5"/></svg>',
			),
			'filmstrip' => array(
				'name'     => esc_html__( 'Filmstrip / Reel', 'hello-movieengine' ),
				'category' => 'media',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M7 3v18M17 3v18M3 7.5h4M3 12h4M3 16.5h4M17 7.5h4M17 12h4M17 16.5h4"/></svg>',
			),
			'tv' => array(
				'name'     => esc_html__( 'Series / TV', 'hello-movieengine' ),
				'category' => 'media',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="20" height="15" x="2" y="7" rx="2"/><polyline points="17 2 12 7 7 2"/></svg>',
			),
			'tv-2' => array(
				'name'     => esc_html__( 'Retro TV / Shows', 'hello-movieengine' ),
				'category' => 'media',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m21 8-2 2-1.5-3.7"/><rect width="20" height="14" x="2" y="7" rx="2"/><path d="M12 21v-3"/></svg>',
			),
			'play' => array(
				'name'     => esc_html__( 'Play', 'hello-movieengine' ),
				'category' => 'media',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polygon points="6 3 20 12 6 21 6 3"/></svg>',
			),
			'play-circle' => array(
				'name'     => esc_html__( 'Play Circle', 'hello-movieengine' ),
				'category' => 'media',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polygon points="10 8 16 12 10 16 10 8"/></svg>',
			),
			'pause' => array(
				'name'     => esc_html__( 'Pause', 'hello-movieengine' ),
				'category' => 'media',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="4" height="16" x="6" y="4"/><rect width="4" height="16" x="14" y="4"/></svg>',
			),
			'pause-circle' => array(
				'name'     => esc_html__( 'Pause Circle', 'hello-movieengine' ),
				'category' => 'media',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="10" y1="15" x2="10" y2="9"/><line x1="14" y1="15" x2="14" y2="9"/></svg>',
			),
			'clapperboard' => array(
				'name'     => esc_html__( 'Clapperboard', 'hello-movieengine' ),
				'category' => 'media',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20.2 6 3 11l-.9-2.4c-.4-1.1.2-2.4 1.3-2.8l12.8-4.8c1.1-.4 2.4.2 2.8 1.3l1.2 3.7z"/><path d="m6.2 5.3 3.1 4"/><path d="m12.2 3.3 3.1 4"/><rect width="20" height="12" x="2" y="8" rx="2"/></svg>',
			),
			'ticket' => array(
				'name'     => esc_html__( 'Cinema Ticket', 'hello-movieengine' ),
				'category' => 'media',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z"/><path d="M13 5v2"/><path d="M13 17v2"/><path d="M13 11v2"/></svg>',
			),
			'popcorn' => array(
				'name'     => esc_html__( 'Popcorn / Snacks', 'hello-movieengine' ),
				'category' => 'media',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 8a3 3 0 0 0 0-6 3 3 0 0 0-4 1.3A3 3 0 0 0 8 3a3 3 0 0 0-2 5"/><path d="M5 8h14l-1.5 13H6.5L5 8z"/><path d="M10 8v13M14 8v13"/></svg>',
			),
			'video' => array(
				'name'     => esc_html__( 'Video Camera', 'hello-movieengine' ),
				'category' => 'media',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="14" height="12" x="2" y="6" rx="2"/><path d="m22 7-6 5 6 5V7z"/></svg>',
			),
			'video-off' => array(
				'name'     => esc_html__( 'Video Off', 'hello-movieengine' ),
				'category' => 'media',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M10.66 6H14a2 2 0 0 1 2 2v2.5l5.248-3.062A1 1 0 0 1 23 8.307v7.385a1 1 0 0 1-1.752.668L16 13.5V16a2 2 0 0 1-2 2H6"/><line x1="2" y1="2" x2="22" y2="22"/><path d="M2 6a2 2 0 0 1 2-2h1.34M2 10v6a2 2 0 0 0 2 2h6"/></svg>',
			),
			'camera' => array(
				'name'     => esc_html__( 'Camera / Photos', 'hello-movieengine' ),
				'category' => 'media',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z"/><circle cx="12" cy="13" r="3"/></svg>',
			),
			'projector' => array(
				'name'     => esc_html__( 'Projector / Cinema', 'hello-movieengine' ),
				'category' => 'media',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 7 3 5"/><path d="M9 6V3"/><path d="m13 7 2-2"/><circle cx="9" cy="13" r="3"/><path d="M11.83 12H20a2 2 0 0 1 2 2v4a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2v-4a2 2 0 0 1 2-2h2.17"/></svg>',
			),
			'radio' => array(
				'name'     => esc_html__( 'Live Broadcast / Stream', 'hello-movieengine' ),
				'category' => 'media',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4.9 19.1C1 15.2 1 8.8 4.9 4.9"/><path d="M7.8 16.2c-2.3-2.3-2.3-6.1 0-8.5"/><circle cx="12" cy="12" r="2"/><path d="M16.2 7.8c2.3 2.3 2.3 6.1 0 8.5"/><path d="M19.1 4.9C23 8.8 23 15.1 19.1 19"/></svg>',
			),
			'cast' => array(
				'name'     => esc_html__( 'Chromecast / Cast', 'hello-movieengine' ),
				'category' => 'media',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 8V6a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-6"/><path d="M2 12a9 9 0 0 1 8 8"/><path d="M2 16a5 5 0 0 1 4 4"/><line x1="2" y1="20" x2="2.01" y2="20"/></svg>',
			),
			'airplay' => array(
				'name'     => esc_html__( 'AirPlay', 'hello-movieengine' ),
				'category' => 'media',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 17H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-1"/><polygon points="12 15 17 21 7 21 12 15"/></svg>',
			),
			'screen-share' => array(
				'name'     => esc_html__( 'Screen Share', 'hello-movieengine' ),
				'category' => 'media',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M13 3H4a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-3"/><path d="M8 21h8"/><path d="M12 17v4"/><path d="m17 8 5-5"/><path d="M17 3h5v5"/></svg>',
			),
			'monitor' => array(
				'name'     => esc_html__( 'Monitor / Display', 'hello-movieengine' ),
				'category' => 'media',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="20" height="14" x="2" y="3" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>',
			),
			'smartphone' => array(
				'name'     => esc_html__( 'Mobile Phone', 'hello-movieengine' ),
				'category' => 'media',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="14" height="20" x="5" y="2" rx="2" ry="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>',
			),
			'tablet' => array(
				'name'     => esc_html__( 'Tablet', 'hello-movieengine' ),
				'category' => 'media',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="16" height="20" x="4" y="2" rx="2" ry="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>',
			),
			'subtitles' => array(
				'name'     => esc_html__( 'Subtitles / CC', 'hello-movieengine' ),
				'category' => 'media',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="20" height="14" x="2" y="5" rx="2"/><path d="M7 15h4M15 15h2M7 11h2M13 11h4"/></svg>',
			),
			'disc' => array(
				'name'     => esc_html__( 'Disc / CD / DVD', 'hello-movieengine' ),
				'category' => 'media',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="3"/></svg>',
			),
			'rewind' => array(
				'name'     => esc_html__( 'Rewind (10s)', 'hello-movieengine' ),
				'category' => 'media',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polygon points="11 19 2 12 11 5 11 19"/><polygon points="22 19 13 12 22 5 22 19"/></svg>',
			),
			'fast-forward' => array(
				'name'     => esc_html__( 'Fast Forward', 'hello-movieengine' ),
				'category' => 'media',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polygon points="13 19 22 12 13 5 13 19"/><polygon points="2 19 11 12 2 5 2 19"/></svg>',
			),
			'skip-back' => array(
				'name'     => esc_html__( 'Previous Track', 'hello-movieengine' ),
				'category' => 'media',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polygon points="19 20 9 12 19 4 19 20"/><line x1="5" y1="19" x2="5" y2="5"/></svg>',
			),
			'skip-forward' => array(
				'name'     => esc_html__( 'Next Episode', 'hello-movieengine' ),
				'category' => 'media',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polygon points="5 4 15 12 5 20 5 4"/><line x1="19" y1="5" x2="19" y2="19"/></svg>',
			),
			'repeat' => array(
				'name'     => esc_html__( 'Repeat / Loop', 'hello-movieengine' ),
				'category' => 'media',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m17 2 4 4-4 4"/><path d="M3 11v-1a4 4 0 0 1 4-4h14"/><path d="m7 22-4-4 4-4"/><path d="M21 13v1a4 4 0 0 1-4 4H3"/></svg>',
			),
			'headphones' => array(
				'name'     => esc_html__( 'Headphones', 'hello-movieengine' ),
				'category' => 'media',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 14h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-7a9 9 0 0 1 18 0v7a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3"/></svg>',
			),
			'volume' => array(
				'name'     => esc_html__( 'Volume Low', 'hello-movieengine' ),
				'category' => 'media',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/></svg>',
			),
			'volume-1' => array(
				'name'     => esc_html__( 'Volume Medium', 'hello-movieengine' ),
				'category' => 'media',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/><path d="M15.54 8.46a5 5 0 0 1 0 7.07"/></svg>',
			),
			'volume-2' => array(
				'name'     => esc_html__( 'Volume High', 'hello-movieengine' ),
				'category' => 'media',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/><path d="M15.54 8.46a5 5 0 0 1 0 7.07"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14"/></svg>',
			),
			'volume-x' => array(
				'name'     => esc_html__( 'Mute Audio', 'hello-movieengine' ),
				'category' => 'media',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/><line x1="22" y1="9" x2="16" y2="15"/><line x1="16" y1="9" x2="22" y2="15"/></svg>',
			),
			'mic' => array(
				'name'     => esc_html__( 'Microphone', 'hello-movieengine' ),
				'category' => 'media',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2"/><line x1="12" y1="19" x2="12" y2="22"/></svg>',
			),
			'music' => array(
				'name'     => esc_html__( 'Music / OST', 'hello-movieengine' ),
				'category' => 'media',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>',
			),
			'award' => array(
				'name'     => esc_html__( 'Oscars / Award', 'hello-movieengine' ),
				'category' => 'media',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/></svg>',
			),
			'trophy' => array(
				'name'     => esc_html__( 'Trophy / Winner', 'hello-movieengine' ),
				'category' => 'media',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/><path d="M4 22h16"/><path d="M10 14.66V17c0 .55-.45 1-1 1H8c-.55 0-1 .45-1 1v1h10v-1c0-.55-.45-1-1-1h-1c-.55 0-1-.45-1-1v-2.34"/><path d="M18 4H6v7a6 6 0 0 0 12 0V4Z"/></svg>',
			),
			'medal' => array(
				'name'     => esc_html__( 'Medal', 'hello-movieengine' ),
				'category' => 'media',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7.21 15 2.66 7.14a2 2 0 0 1 .13-2.2L4.4 2.8A2 2 0 0 1 6 2h12a2 2 0 0 1 1.6.8l1.6 2.14a2 2 0 0 1 .14 2.2L16.79 15"/><circle cx="12" cy="17" r="5"/></svg>',
			),
			'maximize-2' => array(
				'name'     => esc_html__( 'Fullscreen / Theater', 'hello-movieengine' ),
				'category' => 'media',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="15 3 21 3 21 9"/><polyline points="9 21 3 21 3 15"/><line x1="21" y1="3" x2="14" y2="10"/><line x1="3" y1="21" x2="10" y2="14"/></svg>',
			),
			'minimize-2' => array(
				'name'     => esc_html__( 'Exit Fullscreen', 'hello-movieengine' ),
				'category' => 'media',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="4 14 10 14 10 20"/><polyline points="20 10 14 10 14 4"/><line x1="14" y1="10" x2="21" y2="3"/><line x1="3" y1="21" x2="10" y2="14"/></svg>',
			),

			// ==========================================
			// 2. Navigation & Discovery (nav)
			// ==========================================
			'search' => array(
				'name'     => esc_html__( 'Search', 'hello-movieengine' ),
				'category' => 'nav',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>',
			),
			'compass' => array(
				'name'     => esc_html__( 'Explore / Discover', 'hello-movieengine' ),
				'category' => 'nav',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"/></svg>',
			),
			'flame' => array(
				'name'     => esc_html__( 'Trending / Hot', 'hello-movieengine' ),
				'category' => 'nav',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 2.5z"/></svg>',
			),
			'grid' => array(
				'name'     => esc_html__( 'Browse / Grid', 'hello-movieengine' ),
				'category' => 'nav',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="7" height="7" x="3" y="3" rx="1"/><rect width="7" height="7" x="14" y="3" rx="1"/><rect width="7" height="7" x="14" y="14" rx="1"/><rect width="7" height="7" x="3" y="14" rx="1"/></svg>',
			),
			'layout-grid' => array(
				'name'     => esc_html__( 'Gallery Layout', 'hello-movieengine' ),
				'category' => 'nav',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="8" height="8" x="3" y="3" rx="1"/><rect width="8" height="8" x="13" y="3" rx="1"/><rect width="8" height="8" x="13" y="13" rx="1"/><rect width="8" height="8" x="3" y="13" rx="1"/></svg>',
			),
			'layout' => array(
				'name'     => esc_html__( 'Portal Layout', 'hello-movieengine' ),
				'category' => 'nav',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M3 9h18M9 21V9"/></svg>',
			),
			'list' => array(
				'name'     => esc_html__( 'List View', 'hello-movieengine' ),
				'category' => 'nav',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>',
			),
			'tags' => array(
				'name'     => esc_html__( 'Genres / Tags', 'hello-movieengine' ),
				'category' => 'nav',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 2H2v10l9.29 9.29c.94.94 2.48.94 3.42 0l6.58-6.58c.94-.94.94-2.48 0-3.42L12 2Z"/><path d="M7 7h.01"/></svg>',
			),
			'tag' => array(
				'name'     => esc_html__( 'Single Tag / Category', 'hello-movieengine' ),
				'category' => 'nav',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 2H2v10l9.29 9.29a2.41 2.41 0 0 0 3.42 0l4.58-4.58a2.41 2.41 0 0 0 0-3.42L12 2Z"/><circle cx="7" cy="7" r="1"/></svg>',
			),
			'layers' => array(
				'name'     => esc_html__( 'Collections / Library', 'hello-movieengine' ),
				'category' => 'nav',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>',
			),
			'globe' => array(
				'name'     => esc_html__( 'World / International', 'hello-movieengine' ),
				'category' => 'nav',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>',
			),
			'map-pin' => array(
				'name'     => esc_html__( 'Location / Regions', 'hello-movieengine' ),
				'category' => 'nav',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>',
			),
			'navigation' => array(
				'name'     => esc_html__( 'Navigate / Quick Jump', 'hello-movieengine' ),
				'category' => 'nav',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polygon points="3 11 22 2 13 21 11 13 3 11"/></svg>',
			),
			'trending-up' => array(
				'name'     => esc_html__( 'Top Charts / Popular', 'hello-movieengine' ),
				'category' => 'nav',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/><polyline points="16 7 22 7 22 13"/></svg>',
			),
			'trending-down' => array(
				'name'     => esc_html__( 'Trending Down', 'hello-movieengine' ),
				'category' => 'nav',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="22 17 13.5 8.5 8.5 13.5 2 7"/><polyline points="16 17 22 17 22 11"/></svg>',
			),
			'bar-chart' => array(
				'name'     => esc_html__( 'Rankings / Stats', 'hello-movieengine' ),
				'category' => 'nav',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="12" y1="20" x2="12" y2="10"/><line x1="18" y1="20" x2="18" y2="4"/><line x1="6" y1="20" x2="6" y2="16"/></svg>',
			),
			'activity' => array(
				'name'     => esc_html__( 'Activity / Live Feed', 'hello-movieengine' ),
				'category' => 'nav',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>',
			),
			'filter' => array(
				'name'     => esc_html__( 'Filter / Sort Movies', 'hello-movieengine' ),
				'category' => 'nav',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>',
			),
			'sliders' => array(
				'name'     => esc_html__( 'Adjust / Advanced Filters', 'hello-movieengine' ),
				'category' => 'nav',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="4" y1="21" x2="4" y2="14"/><line x1="4" y1="10" x2="4" y2="3"/><line x1="12" y1="21" x2="12" y2="12"/><line x1="12" y1="8" x2="12" y2="3"/><line x1="20" y1="21" x2="20" y2="16"/><line x1="20" y1="12" x2="20" y2="3"/><line x1="1" y1="14" x2="7" y2="14"/><line x1="9" y1="8" x2="15" y2="8"/><line x1="17" y1="16" x2="23" y2="16"/></svg>',
			),
			'calendar' => array(
				'name'     => esc_html__( 'Release Schedule', 'hello-movieengine' ),
				'category' => 'nav',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>',
			),
			'clock' => array(
				'name'     => esc_html__( 'Watch History / Clock', 'hello-movieengine' ),
				'category' => 'nav',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>',
			),
			'history' => array(
				'name'     => esc_html__( 'Recently Viewed', 'hello-movieengine' ),
				'category' => 'nav',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/><polyline points="12 7 12 12 15 15"/></svg>',
			),
			'folder' => array(
				'name'     => esc_html__( 'Folder / Category', 'hello-movieengine' ),
				'category' => 'nav',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 20a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.9a2 2 0 0 1-1.69-.9L9.6 3.9A2 2 0 0 0 7.93 3H4a2 2 0 0 0-2 2v13a2 2 0 0 0 2 2Z"/></svg>',
			),
			'archive' => array(
				'name'     => esc_html__( 'Archive / Past Seasons', 'hello-movieengine' ),
				'category' => 'nav',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="20" height="5" x="2" y="3" rx="1"/><path d="M4 8v11a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8"/><path d="M10 12h4"/></svg>',
			),
			'box' => array(
				'name'     => esc_html__( 'Box Office / Bundles', 'hello-movieengine' ),
				'category' => 'nav',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>',
			),
			'eye' => array(
				'name'     => esc_html__( 'Watch / Views', 'hello-movieengine' ),
				'category' => 'nav',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>',
			),
			'eye-off' => array(
				'name'     => esc_html__( 'Hide / Unseen', 'hello-movieengine' ),
				'category' => 'nav',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" y1="2" x2="22" y2="22"/></svg>',
			),
			'moon' => array(
				'name'     => esc_html__( 'Dark Mode / Night', 'hello-movieengine' ),
				'category' => 'nav',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/></svg>',
			),
			'sun' => array(
				'name'     => esc_html__( 'Light Mode / Day', 'hello-movieengine' ),
				'category' => 'nav',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/></svg>',
			),

			// ==========================================
			// 3. Watchlist & Actions (action)
			// ==========================================
			'bookmark-plus' => array(
				'name'     => esc_html__( 'Watchlist (+)', 'hello-movieengine' ),
				'category' => 'action',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m19 21-7-4-7 4V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16z"/><line x1="12" y1="7" x2="12" y2="13"/><line x1="9" y1="10" x2="15" y2="10"/></svg>',
			),
			'bookmark' => array(
				'name'     => esc_html__( 'Bookmark / Saved', 'hello-movieengine' ),
				'category' => 'action',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m19 21-7-4-7 4V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16z"/></svg>',
			),
			'bookmark-check' => array(
				'name'     => esc_html__( 'Already Watched', 'hello-movieengine' ),
				'category' => 'action',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m19 21-7-4-7 4V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16z"/><polyline points="9 10 11 12 15 8"/></svg>',
			),
			'plus' => array(
				'name'     => esc_html__( 'Plus (+)', 'hello-movieengine' ),
				'category' => 'action',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>',
			),
			'plus-circle' => array(
				'name'     => esc_html__( 'Add to Queue', 'hello-movieengine' ),
				'category' => 'action',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>',
			),
			'minus' => array(
				'name'     => esc_html__( 'Minus (-)', 'hello-movieengine' ),
				'category' => 'action',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/></svg>',
			),
			'check' => array(
				'name'     => esc_html__( 'Checkmark', 'hello-movieengine' ),
				'category' => 'action',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>',
			),
			'check-circle' => array(
				'name'     => esc_html__( 'Completed / Done', 'hello-movieengine' ),
				'category' => 'action',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="9 12 11 14 15 10"/></svg>',
			),
			'heart' => array(
				'name'     => esc_html__( 'Favorites / Heart', 'hello-movieengine' ),
				'category' => 'action',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>',
			),
			'star' => array(
				'name'     => esc_html__( 'Top Rated / Star', 'hello-movieengine' ),
				'category' => 'action',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>',
			),
			'thumbs-up' => array(
				'name'     => esc_html__( 'Like / Thumbs Up', 'hello-movieengine' ),
				'category' => 'action',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 10v12"/><path d="M15 5.88 14 10h5.83a2 2 0 0 1 1.92 2.56l-2.33 8A2 2 0 0 1 17.5 22H4a2 2 0 0 1-2-2v-8a2 2 0 0 1 2-2h3Z"/></svg>',
			),
			'thumbs-down' => array(
				'name'     => esc_html__( 'Dislike', 'hello-movieengine' ),
				'category' => 'action',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17 14V2"/><path d="M9 18.12 10 14H4.17a2 2 0 0 1-1.92-2.56l2.33-8A2 2 0 0 1 6.5 2H20a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2h-3Z"/></svg>',
			),
			'sparkles' => array(
				'name'     => esc_html__( 'Featured / Recommendations', 'hello-movieengine' ),
				'category' => 'action',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/><path d="M5 3v4"/><path d="M19 17v4"/><path d="M3 5h4"/><path d="M17 19h4"/></svg>',
			),
			'download' => array(
				'name'     => esc_html__( 'Offline Downloads', 'hello-movieengine' ),
				'category' => 'action',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>',
			),
			'download-cloud' => array(
				'name'     => esc_html__( 'Cloud Download', 'hello-movieengine' ),
				'category' => 'action',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="8 17 12 21 16 17"/><line x1="12" y1="12" x2="12" y2="21"/><path d="M20.88 18.09A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.29"/></svg>',
			),
			'upload' => array(
				'name'     => esc_html__( 'Upload Video', 'hello-movieengine' ),
				'category' => 'action',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>',
			),
			'share-2' => array(
				'name'     => esc_html__( 'Share Movie', 'hello-movieengine' ),
				'category' => 'action',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>',
			),
			'link' => array(
				'name'     => esc_html__( 'Trailer Link', 'hello-movieengine' ),
				'category' => 'action',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>',
			),
			'external-link' => array(
				'name'     => esc_html__( 'External Website', 'hello-movieengine' ),
				'category' => 'action',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>',
			),
			'shuffle' => array(
				'name'     => esc_html__( 'Surprise Me / Random', 'hello-movieengine' ),
				'category' => 'action',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="16 3 21 3 21 8"/><line x1="4" y1="20" x2="21" y2="3"/><polyline points="21 16 21 21 16 21"/><line x1="15" y1="15" x2="21" y2="21"/><line x1="4" y1="4" x2="9" y2="9"/></svg>',
			),
			'refresh-cw' => array(
				'name'     => esc_html__( 'Refresh / Reload', 'hello-movieengine' ),
				'category' => 'action',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12a9 9 0 0 0-9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/><path d="M3 12a9 9 0 0 0 9 9 9.75 9.75 0 0 0 6.74-2.74L21 16"/><path d="M16 21h5v-5"/></svg>',
			),
			'trash-2' => array(
				'name'     => esc_html__( 'Remove / Clear', 'hello-movieengine' ),
				'category' => 'action',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>',
			),
			'edit' => array(
				'name'     => esc_html__( 'Write Review / Edit', 'hello-movieengine' ),
				'category' => 'action',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>',
			),
			'message-square' => array(
				'name'     => esc_html__( 'Discussion / Comments', 'hello-movieengine' ),
				'category' => 'action',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>',
			),
			'message-circle' => array(
				'name'     => esc_html__( 'Live Chat', 'hello-movieengine' ),
				'category' => 'action',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/></svg>',
			),
			'send' => array(
				'name'     => esc_html__( 'Send / Direct', 'hello-movieengine' ),
				'category' => 'action',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>',
			),
			'bell' => array(
				'name'     => esc_html__( 'Notifications / Alerts', 'hello-movieengine' ),
				'category' => 'action',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>',
			),
			'bell-off' => array(
				'name'     => esc_html__( 'Mute Notifications', 'hello-movieengine' ),
				'category' => 'action',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M8.7 3A6 6 0 0 1 18 8a21.3 21.3 0 0 0 .6 5"/><path d="M17 17H3s3-2 3-9a4.67 4.67 0 0 1 .3-1.7"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/><line x1="2" y1="2" x2="22" y2="22"/></svg>',
			),
			'shield' => array(
				'name'     => esc_html__( 'Security / Parental Control', 'hello-movieengine' ),
				'category' => 'action',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>',
			),
			'shield-check' => array(
				'name'     => esc_html__( 'Verified / Safe Stream', 'hello-movieengine' ),
				'category' => 'action',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/><path d="m9 12 2 2 4-4"/></svg>',
			),
			'lock' => array(
				'name'     => esc_html__( 'Private / Premium Lock', 'hello-movieengine' ),
				'category' => 'action',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>',
			),
			'unlock' => array(
				'name'     => esc_html__( 'Unlocked Access', 'hello-movieengine' ),
				'category' => 'action',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 9.9-1"/></svg>',
			),
			'key' => array(
				'name'     => esc_html__( 'API Key / Access Code', 'hello-movieengine' ),
				'category' => 'action',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="7.5" cy="15.5" r="5.5"/><path d="m21 2-9.6 9.6"/><path d="m15.5 7.5 3 3L22 7l-3-3"/></svg>',
			),
			'zap' => array(
				'name'     => esc_html__( 'Fast Play / Energy', 'hello-movieengine' ),
				'category' => 'action',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>',
			),
			'power' => array(
				'name'     => esc_html__( 'Power / Sleep', 'hello-movieengine' ),
				'category' => 'action',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18.36 6.64a9 9 0 1 1-12.73 0"/><line x1="12" y1="2" x2="12" y2="12"/></svg>',
			),
			'settings' => array(
				'name'     => esc_html__( 'Settings / Preferences', 'hello-movieengine' ),
				'category' => 'action',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>',
			),
			'help-circle' => array(
				'name'     => esc_html__( 'Help / FAQ', 'hello-movieengine' ),
				'category' => 'action',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>',
			),
			'info' => array(
				'name'     => esc_html__( 'Movie Info / Details', 'hello-movieengine' ),
				'category' => 'action',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>',
			),
			'alert-circle' => array(
				'name'     => esc_html__( 'Notice / Warning', 'hello-movieengine' ),
				'category' => 'action',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>',
			),
			'log-in' => array(
				'name'     => esc_html__( 'Log In / Sign In', 'hello-movieengine' ),
				'category' => 'action',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>',
			),
			'log-out' => array(
				'name'     => esc_html__( 'Log Out', 'hello-movieengine' ),
				'category' => 'action',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>',
			),

			// ==========================================
			// 4. User, Account & Billing (account)
			// ==========================================
			'user' => array(
				'name'     => esc_html__( 'Account / Profile', 'hello-movieengine' ),
				'category' => 'account',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>',
			),
			'user-plus' => array(
				'name'     => esc_html__( 'Register / Add Profile', 'hello-movieengine' ),
				'category' => 'account',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>',
			),
			'user-check' => array(
				'name'     => esc_html__( 'Verified Member', 'hello-movieengine' ),
				'category' => 'account',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><polyline points="16 11 18 13 22 9"/></svg>',
			),
			'user-cog' => array(
				'name'     => esc_html__( 'Profile Settings', 'hello-movieengine' ),
				'category' => 'account',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="9" cy="7" r="4"/><path d="M10 15H6a4 4 0 0 0-4 4v2"/><circle cx="19" cy="11" r="2"/><path d="m19 8.5v1M19 12.5v1M17.2 9.5l.9.5M19.9 11.5l.9.5M17.2 12.5l.9-.5M19.9 10.5l.9-.5"/></svg>',
			),
			'users' => array(
				'name'     => esc_html__( 'Community / Friends', 'hello-movieengine' ),
				'category' => 'account',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
			),
			'crown' => array(
				'name'     => esc_html__( 'VIP / Premium Subscription', 'hello-movieengine' ),
				'category' => 'account',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m2 4 3 12h14l3-12-6 7-4-7-4 7-6-7zm3 16h14"/></svg>',
			),
			'gem' => array(
				'name'     => esc_html__( 'Diamond / Exclusive', 'hello-movieengine' ),
				'category' => 'account',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 3h12l4 6-10 13L2 9Z"/><path d="M11 3 8 9l4 13 4-13-3-6"/><path d="M2 9h20"/></svg>',
			),
			'credit-card' => array(
				'name'     => esc_html__( 'Membership / Billing', 'hello-movieengine' ),
				'category' => 'account',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>',
			),
			'wallet' => array(
				'name'     => esc_html__( 'Wallet / Balance', 'hello-movieengine' ),
				'category' => 'account',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12V7H5a2 2 0 0 1 0-4h14v4"/><path d="M3 5v14a2 2 0 0 0 2 2h16v-5"/><path d="M18 12a2 2 0 0 0 0 4h4v-4Z"/></svg>',
			),
			'gift' => array(
				'name'     => esc_html__( 'Free Trial / Gift Code', 'hello-movieengine' ),
				'category' => 'account',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="18" height="14" x="3" y="8" rx="2"/><path d="M12 5a3 3 0 1 0-3 3"/><path d="M12 5a3 3 0 1 1 3 3"/><line x1="12" y1="8" x2="12" y2="22"/><line x1="3" y1="12" x2="21" y2="12"/></svg>',
			),
			'shopping-bag' => array(
				'name'     => esc_html__( 'Store / Merch', 'hello-movieengine' ),
				'category' => 'account',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>',
			),
			'badge-check' => array(
				'name'     => esc_html__( 'Official Badge', 'hello-movieengine' ),
				'category' => 'account',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3.85 8.62a4 4 0 0 1 4.78-4.77 4 4 0 0 1 6.74 0 4 4 0 0 1 4.78 4.78 4 4 0 0 1 0 6.74 4 4 0 0 1-4.77 4.78 4 4 0 0 1-6.75 0 4 4 0 0 1-4.78-4.77 4 4 0 0 1 0-6.76Z"/><polyline points="9 12 11 14 15 10"/></svg>',
			),
			'mail' => array(
				'name'     => esc_html__( 'Inbox / Contact', 'hello-movieengine' ),
				'category' => 'account',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>',
			),
			'at-sign' => array(
				'name'     => esc_html__( 'Social Handle', 'hello-movieengine' ),
				'category' => 'account',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path d="M16 8v5a3 3 0 0 0 6 0v-1a10 10 0 1 0-4 8"/></svg>',
			),
			'phone' => array(
				'name'     => esc_html__( 'Support Hotline', 'hello-movieengine' ),
				'category' => 'account',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>',
			),

			// ==========================================
			// 5. Genres & Movie Moods (genre)
			// ==========================================
			'swords' => array(
				'name'     => esc_html__( 'Action & Adventure', 'hello-movieengine' ),
				'category' => 'genre',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="14.5 17.5 3 6 3 3 6 3 17.5 14.5"/><line x1="13" y1="19" x2="19" y2="13"/><line x1="16" y1="16" x2="20" y2="20"/><line x1="19" y1="21" x2="21" y2="19"/><polyline points="14.5 6.5 18 3 21 3 21 6 17.5 9.5"/><line x1="5" y1="14" x2="9" y2="18"/><line x1="7" y1="17" x2="3" y2="21"/><line x1="2" y1="20" x2="4" y2="22"/></svg>',
			),
			'ghost' => array(
				'name'     => esc_html__( 'Horror / Paranormal', 'hello-movieengine' ),
				'category' => 'genre',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 10h.01"/><path d="M15 10h.01"/><path d="M12 2a8 8 0 0 0-8 8v12l3-3 2.5 2.5L12 19l2.5 2.5L17 19l3 3V10a8 8 0 0 0-8-8z"/></svg>',
			),
			'skull' => array(
				'name'     => esc_html__( 'Crime / Mystery / Thriller', 'hello-movieengine' ),
				'category' => 'genre',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="9" cy="12" r="1"/><circle cx="15" cy="12" r="1"/><path d="M8 20v2h8v-2"/><path d="m12.5 17-.5-1-.5 1h1z"/><path d="M16 20a3 3 0 0 0 1.56-5.59 8 8 0 1 0-11.12 0A3 3 0 0 0 8 20"/></svg>',
			),
			'smile' => array(
				'name'     => esc_html__( 'Comedy / Feel Good', 'hello-movieengine' ),
				'category' => 'genre',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M8 14s1.5 2 4 2 4-2 4-2"/><line x1="9" y1="9" x2="9.01" y2="9"/><line x1="15" y1="9" x2="15.01" y2="9"/></svg>',
			),
			'frown' => array(
				'name'     => esc_html__( 'Drama / Emotional', 'hello-movieengine' ),
				'category' => 'genre',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M16 16s-1.5-2-4-2-4 2-4 2"/><line x1="9" y1="9" x2="9.01" y2="9"/><line x1="15" y1="9" x2="15.01" y2="9"/></svg>',
			),
			'heart-pulse' => array(
				'name'     => esc_html__( 'Romance / Passion', 'hello-movieengine' ),
				'category' => 'genre',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/><path d="M3.22 12H9.5l1.5-3 2 6.5 1.5-3.5h6.28"/></svg>',
			),
			'wand' => array(
				'name'     => esc_html__( 'Fantasy / Sci-Magic', 'hello-movieengine' ),
				'category' => 'genre',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m21.64 3.64-1.28-1.28a1.21 1.21 0 0 0-1.72 0L2.36 18.64a1.21 1.21 0 0 0 0 1.72l1.28 1.28a1.2 1.2 0 0 0 1.72 0L21.64 5.36a1.2 1.2 0 0 0 0-1.72Z"/><path d="m14 7 3 3"/><path d="M5 6v4"/><path d="M19 14v4"/><path d="M10 2v2"/><path d="M7 8H3"/><path d="M21 16h-4"/><path d="M11 3H9"/></svg>',
			),
			'rocket' => array(
				'name'     => esc_html__( 'Sci-Fi / Space Exploration', 'hello-movieengine' ),
				'category' => 'genre',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4.5 16.5c-1.5 1.26-2 5-2 5s3.74-.5 5-2c.71-.84.7-2.13-.09-2.91a2.18 2.18 0 0 0-2.91-.09z"/><path d="m12 15-3-3a22 22 0 0 1 2-3.95A12.88 12.88 0 0 1 22 2c0 2.72-.78 7.5-6 11a22.35 22.35 0 0 1-4 2z"/><path d="M9 12H4s.55-3.03 2-4c1.62-1.08 5 0 5 0"/><path d="M12 15v5s3.03-.55 4-2c1.08-1.62 0-5 0-5"/></svg>',
			),
			'crosshair' => array(
				'name'     => esc_html__( 'War / Military / Sniper', 'hello-movieengine' ),
				'category' => 'genre',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="22" y1="12" x2="18" y2="12"/><line x1="6" y1="12" x2="2" y2="12"/><line x1="12" y1="6" x2="12" y2="2"/><line x1="12" y1="22" x2="12" y2="18"/></svg>',
			),
			'book-open' => array(
				'name'     => esc_html__( 'Documentary / Biography', 'hello-movieengine' ),
				'category' => 'genre',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>',
			),
			'graduation-cap' => array(
				'name'     => esc_html__( 'Educational / Academy', 'hello-movieengine' ),
				'category' => 'genre',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>',
			),
			'dumbbell' => array(
				'name'     => esc_html__( 'Sports / Fitness', 'hello-movieengine' ),
				'category' => 'genre',
				'svg'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m6.5 6.5 11 11"/><path d="m21 21-1-1"/><path d="m3 3 1 1"/><path d="m18 22 4-4"/><path d="m2 6 4-4"/><path d="m3 10 7-7"/><path d="m14 21 7-7"/></svg>',
			),
		);

		return $icons;
	}

	/**
	 * Get registered SVG markup for a given key.
	 *
	 * @param string $key Icon key.
	 * @return string SVG or empty.
	 */
	public static function get_registered_svg( $key ) {
		$clean_key = strtolower( str_replace( 'svg:', '', trim( $key ) ) );
		$lib = self::get_icon_library();
		if ( isset( $lib[ $clean_key ]['svg'] ) ) {
			return $lib[ $clean_key ]['svg'];
		}
		return '';
	}

	/**
	 * Render the appropriate icon for an item.
	 *
	 * Supports clean modern SVGs, FontAwesome classes, and type fallbacks.
	 *
	 * @param array $item Item configuration.
	 */
	public static function render_icon( $item ) {
		$custom_icon = ! empty( $item['custom_icon'] ) ? trim( $item['custom_icon'] ) : '';
		$icon        = ! empty( $item['icon'] ) ? trim( $item['icon'] ) : '';
		$active_icon = $custom_icon ? $custom_icon : $icon;
		$type        = isset( $item['type'] ) ? $item['type'] : 'custom';

		// 1. Explicit SVG key (e.g. "svg:home" or "home")
		if ( strpos( $active_icon, 'svg:' ) === 0 || self::get_registered_svg( $active_icon ) ) {
			$svg = self::get_registered_svg( $active_icon );
			if ( $svg ) {
				echo $svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				return;
			}
		}

		// 2. FontAwesome or custom class
		if ( $active_icon && ( strpos( $active_icon, 'fa-' ) !== false || strpos( $active_icon, 'fa ' ) !== false ) ) {
			echo '<i class="' . esc_attr( $active_icon ) . '" aria-hidden="true"></i>';
			return;
		}

		// 3. Match type directly against modern SVG registry
		$type_svg = self::get_registered_svg( $type );
		if ( $type_svg ) {
			echo $type_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			return;
		}

		// 4. Default fallback
		echo self::get_type_svg( $type ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Fallback SVG icon by item type.
	 *
	 * @param string $type Item type.
	 * @return string SVG markup.
	 */
	public static function get_type_svg( $type ) {
		$reg = self::get_registered_svg( $type );
		if ( $reg ) {
			return $reg;
		}
		return '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/></svg>';
	}
}

Hello_Movie_Engine_Bottom_Nav::init();
