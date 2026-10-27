<?php
/**
 * Customizer sanitize callbacks and helpers.
 *
 * @package Hello_Movie Engine
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function hello_movieengine_sanitize_select( $value, $setting ) {
	$choices = $setting->manager->get_control( $setting->id )->choices;
	return array_key_exists( $value, $choices ) ? $value : $setting->default;
}

function hello_movieengine_sanitize_toggle( $value ) {
	return (bool) $value;
}

function hello_movieengine_sanitize_spacing( $value ) {
	$decoded = json_decode( $value, true );
	if ( ! is_array( $decoded ) ) {
		return wp_json_encode( array(
			'top' => '0', 'right' => '4', 'bottom' => '0', 'left' => '4', 'unit' => 'rem', 'linked' => false,
		) );
	}
	$valid_units = array( 'px', 'rem', 'em', '%', 'vw', 'vh' );
	$clean       = array();
	foreach ( array( 'top', 'right', 'bottom', 'left' ) as $side ) {
		$clean[ $side ] = isset( $decoded[ $side ] ) && is_numeric( $decoded[ $side ] ) ? $decoded[ $side ] : '0';
	}
	$clean['unit']   = isset( $decoded['unit'] ) && in_array( $decoded['unit'], $valid_units, true ) ? $decoded['unit'] : 'rem';
	$clean['linked'] = ! empty( $decoded['linked'] );
	return wp_json_encode( $clean );
}

function hello_movieengine_sanitize_color_alpha( $value ) {
	$value = trim( $value );
	if ( '' === $value ) {
		return '';
	}
	if ( preg_match( '/^#([A-Fa-f0-9]{3}){1,2}$/', $value ) ) {
		return $value;
	}
	if ( preg_match( '/^rgba?\(\s*\d{1,3}\s*,\s*\d{1,3}\s*,\s*\d{1,3}\s*(,\s*(0|1|0?\.\d+))?\s*\)$/', $value ) ) {
		return $value;
	}
	return '';
}

function hello_movieengine_sanitize_multiselect( $value ) {
	$decoded = json_decode( $value, true );
	if ( ! is_array( $decoded ) ) {
		return '[]';
	}
	$clean = array_map( 'sanitize_key', $decoded );
	return wp_json_encode( array_values( $clean ) );
}

function hello_movieengine_sanitize_nav_repeater( $value ) {
	if ( is_string( $value ) ) {
		$decoded = json_decode( $value, true );
	} elseif ( is_array( $value ) ) {
		$decoded = $value;
	} else {
		$decoded = null;
	}

	if ( ! is_array( $decoded ) ) {
		return '[]';
	}

	$valid_types       = array( 'home', 'movies', 'series', 'watchlist', 'account', 'search', 'genres', 'subscription', 'custom' );
	$valid_visibility  = array( 'all', 'logged_in', 'logged_out' );
	$clean_items       = array();

	foreach ( $decoded as $index => $item ) {
		if ( ! is_array( $item ) ) {
			continue;
		}

		$type = isset( $item['type'] ) ? sanitize_key( $item['type'] ) : 'custom';
		if ( ! in_array( $type, $valid_types, true ) ) {
			$type = 'custom';
		}

		$visibility = isset( $item['visibility'] ) ? sanitize_key( $item['visibility'] ) : 'all';
		if ( ! in_array( $visibility, $valid_visibility, true ) ) {
			$visibility = 'all';
		}

		$clean_items[] = array(
			'id'          => ! empty( $item['id'] ) ? sanitize_key( $item['id'] ) : 'item_' . $index,
			'type'        => $type,
			'label'       => isset( $item['label'] ) ? sanitize_text_field( $item['label'] ) : '',
			'icon'        => isset( $item['icon'] ) ? sanitize_text_field( $item['icon'] ) : '',
			'custom_icon' => isset( $item['custom_icon'] ) ? sanitize_text_field( $item['custom_icon'] ) : '',
			'page_id'     => isset( $item['page_id'] ) ? absint( $item['page_id'] ) : 0,
			'url'         => isset( $item['url'] ) ? esc_url_raw( trim( $item['url'] ) ) : '',
			'visibility'  => $visibility,
			'enabled'     => ! isset( $item['enabled'] ) || ! empty( $item['enabled'] ),
		);
	}

	return wp_json_encode( $clean_items );
}

function hello_movieengine_sanitize_page_select( $value ) {
	$decoded = json_decode( $value, true );
	if ( ! is_array( $decoded ) ) {
		return '[]';
	}
	$clean = array_map( 'absint', $decoded );
	$clean = array_filter( $clean );
	return wp_json_encode( array_values( $clean ) );
}

function hello_movieengine_get_copyright_text() {
	$custom = get_theme_mod( 'hello_movieengine_footer_copyright', '' );
	if ( $custom ) {
		return wp_kses_post( $custom );
	}
	return sprintf(
		/* translators: 1: current year, 2: site name */
		esc_html__( '&copy; %1$s %2$s. All rights reserved.', 'hello-movieengine' ),
		date_i18n( 'Y' ),
		get_bloginfo( 'name' )
	);
}
