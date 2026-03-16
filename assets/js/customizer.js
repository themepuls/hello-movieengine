/**
 * Hello Movie Engine – Customizer live preview.
 *
 * @package Hello_Movie Engine
 */
( function ( $ ) {
	'use strict';

	wp.customize( 'blogname', function ( value ) {
		value.bind( function ( to ) {
			$( '.hello-movieengine-header__site-title a' ).text( to );
		} );
	} );

	wp.customize( 'blogdescription', function ( value ) {
		value.bind( function ( to ) {
			$( '.hello-movieengine-header__site-desc' ).text( to );
		} );
	} );

	wp.customize( 'hello_movieengine_footer_copyright', function ( value ) {
		value.bind( function ( to ) {
			$( '.hello-movieengine-footer__copyright-text' ).html( to || '' );
		} );
	} );
} )( jQuery );
