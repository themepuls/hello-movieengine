<?php
/**
 * Style Two only: render plugin fullwidth movie hero and inject Back control.
 * Does not modify plugin files — includes the stock template once.
 *
 * @package Hello_Movie Engine
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'MOVIE_ENGINE_PATH' ) ) {
	return;
}

$hello_me_plugin_hero = MOVIE_ENGINE_PATH . 'templates/single-movie/hero-fullwidth.php';
if ( ! is_readable( $hello_me_plugin_hero ) ) {
	return;
}

ob_start();
include $hello_me_plugin_hero;
$hello_me_hero_html = ob_get_clean();

if ( function_exists( 'hello_movieengine_inject_watch_hero_back' ) ) {
	$hello_me_hero_html = hello_movieengine_inject_watch_hero_back( $hello_me_hero_html, 'movie' );
}

// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Plugin template output; Back escaped in helper.
echo $hello_me_hero_html;