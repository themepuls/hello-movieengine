/**
 * Hello Cine Movie Engine – Customizer controls JS.
 *
 * Handles:
 * - Spacing control with link/unlink
 * - Alpha color picker
 * - Toggle switch
 *
 * @package Hello_Cine Movie Engine
 */
( function ( $ ) {
	'use strict';

	/* ==========================================================
	   Spacing Control
	   ========================================================== */

	function spacingSync( $wrap ) {
		var $hidden = $wrap.find( '.hello-movieengine-spacing-value' );
		var data    = {};

		$wrap.find( '.hello-movieengine-spacing-input' ).each( function () {
			data[ $( this ).data( 'side' ) ] = $( this ).val();
		} );

		data.unit   = $wrap.find( '.hello-movieengine-spacing-unit-select' ).val();
		data.linked = $wrap.data( 'linked' ) === 1 || $wrap.data( 'linked' ) === '1';

		$hidden.val( JSON.stringify( data ) ).trigger( 'change' );
	}

	$( document ).on( 'click', '.hello-movieengine-spacing-link', function () {
		var $btn  = $( this );
		var $wrap = $btn.closest( '.hello-movieengine-spacing-control' );
		var isLinked = $btn.hasClass( 'is-linked' );

		if ( isLinked ) {
			$btn.removeClass( 'is-linked' );
			$wrap.data( 'linked', 0 );
		} else {
			$btn.addClass( 'is-linked' );
			$wrap.data( 'linked', 1 );

			var firstVal = $wrap.find( '.hello-movieengine-spacing-input' ).first().val();
			$wrap.find( '.hello-movieengine-spacing-input' ).val( firstVal );
		}

		spacingSync( $wrap );
	} );

	$( document ).on( 'input change', '.hello-movieengine-spacing-input', function () {
		var $wrap = $( this ).closest( '.hello-movieengine-spacing-control' );
		var linked = $wrap.data( 'linked' ) === 1 || $wrap.data( 'linked' ) === '1';

		if ( linked ) {
			var val = $( this ).val();
			$wrap.find( '.hello-movieengine-spacing-input' ).val( val );
		}

		spacingSync( $wrap );
	} );

	$( document ).on( 'change', '.hello-movieengine-spacing-unit-select', function () {
		spacingSync( $( this ).closest( '.hello-movieengine-spacing-control' ) );
	} );

	/* ==========================================================
	   Alpha Color Picker
	   ========================================================== */

	function syncColor( $wrap, val ) {
		var $hidden   = $wrap.find( '.hello-movieengine-alpha-color-value' );
		var settingId = $hidden.data( 'customize-setting-link' );

		$hidden.val( val );

		if ( settingId && typeof wp !== 'undefined' && wp.customize ) {
			wp.customize( settingId ).set( val );
		}
	}

	function applyAlpha( hex, alpha ) {
		if ( alpha >= 1 ) {
			return hex;
		}
		if ( ! hex || hex.charAt( 0 ) !== '#' || hex.length < 7 ) {
			return hex;
		}
		var r = parseInt( hex.substring( 1, 3 ), 16 );
		var g = parseInt( hex.substring( 3, 5 ), 16 );
		var b = parseInt( hex.substring( 5, 7 ), 16 );
		return 'rgba(' + r + ',' + g + ',' + b + ',' + alpha.toFixed( 2 ) + ')';
	}

	function hexFromRgba( str ) {
		var m = str.match( /rgba?\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)/ );
		if ( ! m ) return str;
		var r = parseInt( m[1], 10 ).toString( 16 ).padStart( 2, '0' );
		var g = parseInt( m[2], 10 ).toString( 16 ).padStart( 2, '0' );
		var b = parseInt( m[3], 10 ).toString( 16 ).padStart( 2, '0' );
		return '#' + r + g + b;
	}

	function initAlphaColorPickers() {
		$( '.hello-movieengine-alpha-color-control' ).each( function () {
			var $wrap   = $( this );
			var $input  = $wrap.find( '.hello-movieengine-alpha-color-input' );
			var $slider = $wrap.find( '.hello-movieengine-alpha-slider' );
			var $valTxt = $wrap.find( '.hello-movieengine-alpha-value' );

			if ( $input.data( 'hello-movieengine-init' ) ) {
				return;
			}
			$input.data( 'hello-movieengine-init', true );

			var currentVal = $wrap.find( '.hello-movieengine-alpha-color-value' ).val() || $input.val();
			var alpha = 1;
			var rgbaMatch = currentVal.match( /rgba?\(\s*\d+\s*,\s*\d+\s*,\s*\d+\s*,\s*([\d.]+)\s*\)/ );
			if ( rgbaMatch ) {
				alpha = parseFloat( rgbaMatch[1] );
			}
			if ( $slider.length ) {
				$slider.val( alpha );
				$valTxt.text( Math.round( alpha * 100 ) + '%' );
			}

			$input.wpColorPicker( {
				defaultColor: $input.data( 'default-color' ) || '',
				change: function ( event, ui ) {
					var hex  = ui.color.toString();
					var aVal = $slider.length ? parseFloat( $slider.val() ) : 1;
					syncColor( $wrap, applyAlpha( hex, aVal ) );
				},
				clear: function () {
					syncColor( $wrap, '' );
				}
			} );

			if ( $slider.length ) {
				$slider.on( 'input', function () {
					var aVal = parseFloat( $( this ).val() );
					$valTxt.text( Math.round( aVal * 100 ) + '%' );

					var currentHidden = $wrap.find( '.hello-movieengine-alpha-color-value' ).val();
					var hex = currentHidden;
					if ( currentHidden && currentHidden.indexOf( 'rgb' ) === 0 ) {
						hex = hexFromRgba( currentHidden );
					}
					if ( hex ) {
						syncColor( $wrap, applyAlpha( hex, aVal ) );
					}
				} );
			}
		} );
	}

	$( window ).on( 'load', function () {
		setTimeout( initAlphaColorPickers, 300 );
	} );

	/* ==========================================================
	   Toggle Switch
	   ========================================================== */

	$( document ).on( 'change', '.hello-movieengine-toggle-input', function () {
		$( this ).val( this.checked ? '1' : '' ).trigger( 'change' );
	} );

	/* ==========================================================
	   Multi-select (pill toggles)
	   ========================================================== */

	$( document ).on( 'click', '.hello-movieengine-pill', function () {
		var $pill  = $( this );
		var $wrap  = $pill.closest( '.hello-movieengine-multiselect-control' );
		var $hidden = $wrap.find( '.hello-movieengine-multiselect-value' );

		$pill.toggleClass( 'is-active' );

		var selected = [];
		$wrap.find( '.hello-movieengine-pill.is-active' ).each( function () {
			selected.push( $( this ).data( 'value' ) );
		} );

		var val       = JSON.stringify( selected );
		var settingId = $hidden.data( 'customize-setting-link' );

		$hidden.val( val );
		if ( settingId && typeof wp !== 'undefined' && wp.customize ) {
			wp.customize( settingId ).set( val );
		}
	} );

	/* ==========================================================
	   Page Select (checkbox list with search)
	   ========================================================== */

	$( document ).on( 'input', '.hello-movieengine-page-select-search', function () {
		var query = $( this ).val().toLowerCase();
		$( this ).closest( '.hello-movieengine-page-select-control' )
			.find( '.hello-movieengine-page-select-item' ).each( function () {
				var title = $( this ).data( 'title' ) || '';
				$( this ).toggleClass( 'is-hidden', title.indexOf( query ) === -1 );
			} );
	} );

	$( document ).on( 'change', '.hello-movieengine-page-select-item input[type="checkbox"]', function () {
		var $wrap   = $( this ).closest( '.hello-movieengine-page-select-control' );
		var $hidden = $wrap.find( '.hello-movieengine-page-select-value' );
		var selected = [];

		$wrap.find( '.hello-movieengine-page-select-item input:checked' ).each( function () {
			selected.push( parseInt( $( this ).val(), 10 ) );
		} );

		var val       = JSON.stringify( selected );
		var settingId = $hidden.data( 'customize-setting-link' );
		var count     = selected.length;

		$hidden.val( val );
		$wrap.find( '.hello-movieengine-page-select-count' ).text(
			count > 0 ? count + ' selected' : ''
		);

		if ( settingId && typeof wp !== 'undefined' && wp.customize ) {
			wp.customize( settingId ).set( val );
		}
	} );

} )( jQuery );
