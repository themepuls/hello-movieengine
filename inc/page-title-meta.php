<?php
/**
 * Per-page Page Title banner (block editor + show/hide).
 *
 * @package Hello_Movie Engine
 * @since 1.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'HELLO_MOVIEENGINE_PAGE_TITLE_META', '_hello_movieengine_page_title_mode' );
define( 'HELLO_MOVIEENGINE_PAGE_TITLE_DESC_META', '_hello_movieengine_page_title_description' );
define( 'HELLO_MOVIEENGINE_PAGE_TITLE_BG_META', '_hello_movieengine_page_title_bg_id' );

/**
 * Register post meta for pages.
 */
function hello_movieengine_register_page_title_meta() {
	register_post_meta(
		'page',
		HELLO_MOVIEENGINE_PAGE_TITLE_META,
		array(
			'type'              => 'string',
			'single'            => true,
			'default'           => '',
			'show_in_rest'      => true,
			'auth_callback'     => static function () {
				return current_user_can( 'edit_pages' );
			},
			'sanitize_callback' => static function ( $value ) {
				$value = is_string( $value ) ? $value : '';
				return in_array( $value, array( '', 'show', 'hide' ), true ) ? $value : '';
			},
		)
	);

	register_post_meta(
		'page',
		HELLO_MOVIEENGINE_PAGE_TITLE_DESC_META,
		array(
			'type'              => 'string',
			'single'            => true,
			'default'           => '',
			'show_in_rest'      => true,
			'auth_callback'     => static function () {
				return current_user_can( 'edit_pages' );
			},
			'sanitize_callback' => static function ( $value ) {
				return sanitize_textarea_field( (string) $value );
			},
		)
	);

	register_post_meta(
		'page',
		HELLO_MOVIEENGINE_PAGE_TITLE_BG_META,
		array(
			'type'              => 'integer',
			'single'            => true,
			'default'           => 0,
			'show_in_rest'      => true,
			'auth_callback'     => static function () {
				return current_user_can( 'edit_pages' );
			},
			'sanitize_callback' => static function ( $value ) {
				$id = absint( $value );
				if ( $id > 0 && 'attachment' !== get_post_type( $id ) ) {
					return 0;
				}
				return $id;
			},
		)
	);
}
add_action( 'init', 'hello_movieengine_register_page_title_meta' );

/**
 * @param int $page_id Page ID.
 * @return string show|hide|''
 */
function hello_movieengine_get_page_title_mode( $page_id = 0 ) {
	$page_id = $page_id ? (int) $page_id : (int) get_queried_object_id();
	if ( $page_id < 1 ) {
		return '';
	}
	$mode = get_post_meta( $page_id, HELLO_MOVIEENGINE_PAGE_TITLE_META, true );
	return in_array( $mode, array( 'show', 'hide' ), true ) ? $mode : '';
}

/**
 * Block editor control in Page sidebar.
 */
function hello_movieengine_enqueue_page_title_editor() {
	$asset = HELLO_MOVIEENGINE_DIR . '/assets/admin/js/page-title-editor.js';
	if ( ! file_exists( $asset ) ) {
		return;
	}

	wp_enqueue_script(
		'hello-movieengine-page-title-editor',
		HELLO_MOVIEENGINE_URI . '/assets/admin/js/page-title-editor.js',
		array( 'wp-plugins', 'wp-edit-post', 'wp-block-editor', 'wp-components', 'wp-element', 'wp-data', 'wp-i18n' ),
		filemtime( $asset ),
		true
	);
}
add_action( 'enqueue_block_editor_assets', 'hello_movieengine_enqueue_page_title_editor' );