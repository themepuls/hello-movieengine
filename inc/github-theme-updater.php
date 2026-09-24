<?php
/**
 * GitHub Theme Updater (remove before WordPress.org submission)
 *
 * When submitting Hello MovieEngine to WordPress.org:
 * 1. Delete this file.
 * 2. Remove the require line from functions.php.
 *
 * Update flow: create a GitHub Release on themepuls/hello-movieengine
 * with tag matching style.css Version (e.g. 1.0.3 or v1.0.3).
 * Prefer attaching hello-movieengine.zip (root folder = hello-movieengine).
 * If no asset is attached, the release zipball is used and renamed on install.
 *
 * @package Hello_MovieEngine
 * @since 1.0.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Hello_MovieEngine_GitHub_Updater
 */
class Hello_MovieEngine_GitHub_Updater {

	/**
	 * GitHub owner/repo.
	 *
	 * @var string
	 */
	const REPO = 'themepuls/hello-movieengine';

	/**
	 * Theme stylesheet (folder) slug.
	 *
	 * @var string
	 */
	const SLUG = 'hello-movieengine';

	/**
	 * Transient key for cached GitHub release data.
	 *
	 * @var string
	 */
	const CACHE_KEY = 'hello_movieengine_github_release';

	/**
	 * Cache lifetime in seconds (12 hours).
	 *
	 * @var int
	 */
	const CACHE_TTL = 43200;

	/**
	 * Bootstrap hooks.
	 */
	public static function init() {
		add_filter( 'pre_set_site_transient_update_themes', array( __CLASS__, 'check_update' ) );
		add_filter( 'themes_api', array( __CLASS__, 'themes_api' ), 10, 3 );
		add_filter( 'upgrader_source_selection', array( __CLASS__, 'fix_source_folder' ), 10, 4 );
	}

	/**
	 * Inject update data when a newer GitHub release exists.
	 *
	 * @param object $transient Update transient.
	 * @return object
	 */
	public static function check_update( $transient ) {
		if ( empty( $transient->checked ) ) {
			return $transient;
		}

		$theme   = wp_get_theme( self::SLUG );
		$current = $theme->exists() ? $theme->get( 'Version' ) : '';

		if ( '' === $current ) {
			return $transient;
		}

		$release = self::get_latest_release();
		if ( empty( $release['version'] ) || empty( $release['package'] ) ) {
			return $transient;
		}

		if ( version_compare( $release['version'], $current, '<=' ) ) {
			return $transient;
		}

		$transient->response[ self::SLUG ] = array(
			'theme'       => self::SLUG,
			'new_version' => $release['version'],
			'url'         => $release['url'],
			'package'     => $release['package'],
			'requires'    => $theme->get( 'RequiresWP' ) ? $theme->get( 'RequiresWP' ) : '',
			'requires_php'=> $theme->get( 'RequiresPHP' ) ? $theme->get( 'RequiresPHP' ) : '',
		);

		return $transient;
	}

	/**
	 * Provide theme details for the update UI modal.
	 *
	 * @param false|object|array $result  Result object or array.
	 * @param string             $action  API action.
	 * @param object             $args    Request args.
	 * @return false|object|array
	 */
	public static function themes_api( $result, $action, $args ) {
		if ( 'theme_information' !== $action || empty( $args->slug ) || self::SLUG !== $args->slug ) {
			return $result;
		}

		$release = self::get_latest_release();
		$theme   = wp_get_theme( self::SLUG );

		if ( empty( $release['version'] ) ) {
			return $result;
		}

		return (object) array(
			'name'          => $theme->get( 'Name' ),
			'slug'          => self::SLUG,
			'version'       => $release['version'],
			'author'        => $theme->get( 'Author' ),
			'homepage'      => $theme->get( 'ThemeURI' ),
			'download_link' => $release['package'],
			'sections'      => array(
				'description' => $theme->get( 'Description' ),
				'changelog'   => ! empty( $release['body'] ) ? $release['body'] : '',
			),
		);
	}

	/**
	 * Rename GitHub zipball folder to the theme slug.
	 *
	 * @param string      $source        Source path.
	 * @param string      $remote_source Remote source path.
	 * @param WP_Upgrader $upgrader      Upgrader instance.
	 * @param array       $hook_extra    Extra data.
	 * @return string|WP_Error
	 */
	public static function fix_source_folder( $source, $remote_source, $upgrader, $hook_extra ) {
		global $wp_filesystem;

		$is_theme_update = false;

		if ( ! empty( $hook_extra['theme'] ) && self::SLUG === $hook_extra['theme'] ) {
			$is_theme_update = true;
		}

		if ( ! $is_theme_update && ! empty( $upgrader->skin->theme_info ) ) {
			$info = $upgrader->skin->theme_info;
			if ( is_object( $info ) && method_exists( $info, 'get_stylesheet' ) && self::SLUG === $info->get_stylesheet() ) {
				$is_theme_update = true;
			}
		}

		if ( ! $is_theme_update ) {
			return $source;
		}

		$corrected = trailingslashit( $remote_source ) . self::SLUG;

		if ( trailingslashit( $source ) === trailingslashit( $corrected ) ) {
			return $source;
		}

		if ( $wp_filesystem->is_dir( $corrected ) ) {
			$wp_filesystem->delete( $corrected, true );
		}

		if ( ! $wp_filesystem->move( $source, $corrected ) ) {
			return new WP_Error(
				'hello_movieengine_rename_failed',
				__( 'Could not rename the theme update package folder.', 'hello-movieengine' )
			);
		}

		return trailingslashit( $corrected );
	}

	/**
	 * Fetch and cache the latest GitHub release.
	 *
	 * @return array{version?:string,package?:string,url?:string,body?:string}
	 */
	private static function get_latest_release() {
		$cached = get_site_transient( self::CACHE_KEY );
		if ( is_array( $cached ) && ! empty( $cached['version'] ) && ! empty( $cached['package'] ) ) {
			/*
			 * If the cached release is not newer than the installed theme,
			 * re-check GitHub sooner so a new Release is not hidden for 12 hours.
			 */
			$theme   = wp_get_theme( self::SLUG );
			$current = $theme->exists() ? $theme->get( 'Version' ) : '';
			if ( '' === $current || version_compare( $cached['version'], $current, '>' ) ) {
				return $cached;
			}
		}

		$response = wp_remote_get(
			'https://api.github.com/repos/' . self::REPO . '/releases/latest',
			array(
				'timeout' => 15,
				'headers' => array(
					'Accept'     => 'application/vnd.github+json',
					'User-Agent' => 'Hello-MovieEngine-Theme-Updater',
				),
			)
		);

		if ( is_wp_error( $response ) || 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
			set_site_transient( self::CACHE_KEY, array(), HOUR_IN_SECONDS );
			return array();
		}

		$data = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( empty( $data['tag_name'] ) ) {
			set_site_transient( self::CACHE_KEY, array(), HOUR_IN_SECONDS );
			return array();
		}

		$version = ltrim( (string) $data['tag_name'], 'vV' );
		$package = '';

		if ( ! empty( $data['assets'] ) && is_array( $data['assets'] ) ) {
			foreach ( $data['assets'] as $asset ) {
				$name = isset( $asset['name'] ) ? (string) $asset['name'] : '';
				$url  = isset( $asset['browser_download_url'] ) ? (string) $asset['browser_download_url'] : '';
				if ( '' === $url ) {
					continue;
				}
				// Prefer a properly named theme zip.
				if ( 'hello-movieengine.zip' === strtolower( $name ) || self::SLUG . '.zip' === strtolower( $name ) ) {
					$package = $url;
					break;
				}
				if ( '' === $package && preg_match( '/\.zip$/i', $name ) ) {
					$package = $url;
				}
			}
		}

		if ( '' === $package && ! empty( $data['zipball_url'] ) ) {
			$package = (string) $data['zipball_url'];
		}

		$result = array(
			'version' => $version,
			'package' => $package,
			'url'     => ! empty( $data['html_url'] ) ? (string) $data['html_url'] : 'https://github.com/' . self::REPO,
			'body'    => ! empty( $data['body'] ) ? (string) $data['body'] : '',
		);

		set_site_transient( self::CACHE_KEY, $result, self::CACHE_TTL );

		return $result;
	}
}

Hello_MovieEngine_GitHub_Updater::init();
