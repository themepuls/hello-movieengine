/**
 * Appearance → Menus: side rail icon picker (shared with Customizer library).
 *
 * @package Hello_Movie Engine
 */
( function ( $ ) {
	'use strict';

	var $activePickerRow = null;

	function getPickerRow( $el ) {
		var $field = $el.closest( '.hello-movieengine-menu-icon-field' );
		if ( $field.length ) {
			return $field;
		}
		return $el.closest( '.menu-item' );
	}

	function showIconModal( $modal ) {
		$modal.css( 'display', 'flex' ).hide().fadeIn( 150 );
	}

	function hideIconModal( $modal ) {
		$modal.fadeOut( 120, function () {
			$modal.css( 'display', 'none' );
		} );
	}

	$( document ).on( 'click', '.hello-movieengine-open-icon-picker, .hello-movieengine-icon-preview-box', function ( e ) {
		e.preventDefault();
		e.stopPropagation();

		$activePickerRow = getPickerRow( $( this ) );
		if ( ! $activePickerRow.length ) {
			return;
		}

		var currentIcon = $activePickerRow.find( '.hello-movieengine-item-icon' ).val() || '';
		var $modal      = $( '#hello-movieengine-icon-picker-modal' );

		if ( ! $modal.length ) {
			return;
		}

		$modal.find( '.hello-movieengine-icon-search-input' ).val( '' );
		$modal.find( '.hello-movieengine-icon-search-clear' ).hide();
		$modal.find( '.hello-movieengine-cat-pill' ).removeClass( 'is-active' ).first().addClass( 'is-active' );
		$modal.find( '.hello-movieengine-icon-card' ).show().removeClass( 'is-selected' );
		$modal.find( '.hello-movieengine-icon-grid' ).show();
		$modal.find( '.hello-movieengine-icon-custom-panel' ).hide();

		if ( currentIcon ) {
			$modal.find( '.hello-movieengine-icon-card[data-icon="' + currentIcon + '"]' ).addClass( 'is-selected' );
		}

		showIconModal( $modal );
		$modal.find( '.hello-movieengine-icon-search-input' ).trigger( 'focus' );
	} );

	function closeIconPicker() {
		var $modal = $( '#hello-movieengine-icon-picker-modal' );
		if ( $modal.length ) {
			hideIconModal( $modal );
		}
		$activePickerRow = null;
	}

	$( document ).on( 'click', '.hello-movieengine-icon-picker-close, .hello-movieengine-icon-picker-backdrop', function ( e ) {
		e.preventDefault();
		closeIconPicker();
	} );

	function updateIconBadge( $modal, cat, query ) {
		if ( 'custom' === cat ) {
			$modal.find( '.hello-movieengine-icon-total-badge' ).text( 'Custom' );
			return;
		}
		var visibleCount = $modal.find( '.hello-movieengine-icon-card:visible' ).length;
		if ( query || 'all' !== cat ) {
			$modal.find( '.hello-movieengine-icon-total-badge' ).text( visibleCount + ' found' );
		}
	}

	$( document ).on( 'click', '.hello-movieengine-cat-pill', function ( e ) {
		e.preventDefault();
		var $btn   = $( this );
		var cat    = $btn.data( 'cat' );
		var $modal = $btn.closest( '.hello-movieengine-icon-picker-modal' );

		$btn.siblings().removeClass( 'is-active' );
		$btn.addClass( 'is-active' );

		if ( 'custom' === cat ) {
			$modal.find( '.hello-movieengine-icon-grid' ).hide();
			$modal.find( '.hello-movieengine-icon-custom-panel' ).show();
			updateIconBadge( $modal, cat, '' );
			return;
		}

		$modal.find( '.hello-movieengine-icon-custom-panel' ).hide();
		$modal.find( '.hello-movieengine-icon-grid' ).show();

		var query = $modal.find( '.hello-movieengine-icon-search-input' ).val().toLowerCase().trim();

		$modal.find( '.hello-movieengine-icon-card' ).each( function () {
			var cardCat  = $( this ).data( 'cat' );
			var cardName = $( this ).data( 'name' ) || '';
			var matchesCat   = ( 'all' === cat || cardCat === cat );
			var matchesQuery = ( ! query || cardName.indexOf( query ) !== -1 );
			$( this ).toggle( matchesCat && matchesQuery );
		} );

		updateIconBadge( $modal, cat, query );
	} );

	$( document ).on( 'input', '.hello-movieengine-icon-search-input', function () {
		var $modal = $( this ).closest( '.hello-movieengine-icon-picker-modal' );
		$modal.find( '.hello-movieengine-cat-pill.is-active' ).trigger( 'click' );
	} );

	$( document ).on( 'click', '.hello-movieengine-icon-search-clear', function () {
		var $modal = $( this ).closest( '.hello-movieengine-icon-picker-modal' );
		$modal.find( '.hello-movieengine-icon-search-input' ).val( '' ).trigger( 'input' ).trigger( 'focus' );
	} );

	$( document ).on( 'click', '.hello-movieengine-icon-card', function ( e ) {
		e.preventDefault();
		if ( ! $activePickerRow || ! $activePickerRow.length ) {
			return;
		}

		var iconVal  = $( this ).data( 'icon' );
		var iconName = $( this ).find( '.hello-movieengine-icon-card-label' ).text();
		var iconHtml = $( this ).find( '.hello-movieengine-icon-card-preview' ).html();

		$activePickerRow.find( '.hello-movieengine-item-icon' ).val( iconVal );
		$activePickerRow.find( '.hello-movieengine-item-custom-icon' ).val( '' );
		$activePickerRow.find( '.hello-movieengine-icon-preview-render' ).html( iconHtml );
		$activePickerRow.find( '.hello-movieengine-icon-display-name' ).text( iconName );

		closeIconPicker();
	} );

	$( document ).on( 'click', '.hello-movieengine-apply-custom-icon', function ( e ) {
		e.preventDefault();
		if ( ! $activePickerRow || ! $activePickerRow.length ) {
			return;
		}

		var customClass = $( this ).closest( '.hello-movieengine-icon-custom-panel' ).find( '.hello-movieengine-custom-class-field' ).val().trim();
		if ( ! customClass ) {
			return;
		}

		$activePickerRow.find( '.hello-movieengine-item-icon' ).val( customClass );
		$activePickerRow.find( '.hello-movieengine-item-custom-icon' ).val( customClass );
		$activePickerRow.find( '.hello-movieengine-icon-preview-render' ).html( '<i class="' + customClass + '" aria-hidden="true"></i>' );
		$activePickerRow.find( '.hello-movieengine-icon-display-name' ).text( customClass );

		closeIconPicker();
	} );

	$( document ).on( 'click', '.hello-movieengine-apply-custom-svg', function ( e ) {
		e.preventDefault();
		if ( ! $activePickerRow || ! $activePickerRow.length ) {
			return;
		}

		var svg = $( this ).closest( '.hello-movieengine-icon-custom-panel' ).find( '.hello-movieengine-custom-svg-field' ).val().trim();
		if ( ! svg || svg.toLowerCase().indexOf( '<svg' ) === -1 ) {
			window.alert( 'Paste valid SVG markup.' );
			return;
		}

		$activePickerRow.find( '.hello-movieengine-item-icon' ).val( 'custom:svg' );
		$activePickerRow.find( '.hello-movieengine-item-custom-icon' ).val( svg );
		$activePickerRow.find( '.hello-movieengine-icon-preview-render' ).html( svg );
		$activePickerRow.find( '.hello-movieengine-icon-display-name' ).text( 'Custom SVG' );

		closeIconPicker();
	} );

	var svgMediaFrame = null;

	function restoreIconPickerAfterMedia() {
		var $modal = $( '#hello-movieengine-icon-picker-modal' );
		if ( ! $modal.length ) {
			return;
		}
		$modal.find( '.hello-movieengine-cat-pill' ).removeClass( 'is-active' );
		$modal.find( '.hello-movieengine-cat-pill[data-cat="custom"]' ).addClass( 'is-active' );
		$modal.find( '.hello-movieengine-icon-grid' ).hide();
		$modal.find( '.hello-movieengine-icon-custom-panel' ).show();
		showIconModal( $modal );
	}

	$( document ).on( 'click', '.hello-movieengine-upload-svg', function ( e ) {
		e.preventDefault();
		e.stopPropagation();

		if ( typeof wp === 'undefined' || ! wp.media ) {
			return;
		}

		hideIconModal( $( '#hello-movieengine-icon-picker-modal' ) );

		if ( svgMediaFrame ) {
			svgMediaFrame.open();
			return;
		}

		svgMediaFrame = wp.media( {
			title: 'Select SVG',
			library: { type: [ 'image/svg+xml', 'image' ] },
			button: { text: 'Use this SVG' },
			multiple: false
		} );

		svgMediaFrame.on( 'select', function () {
			var attachment = svgMediaFrame.state().get( 'selection' ).first().toJSON();
			var $panel = $( '#hello-movieengine-icon-picker-modal .hello-movieengine-icon-custom-panel' );

			if ( attachment && attachment.url ) {
				$.get( attachment.url ).done( function ( data ) {
					if ( typeof data === 'string' && data.toLowerCase().indexOf( '<svg' ) !== -1 ) {
						$panel.find( '.hello-movieengine-custom-svg-field' ).val( data );
					} else {
						$panel.find( '.hello-movieengine-custom-svg-field' ).val( '<img src="' + attachment.url + '" alt="" />' );
					}
					restoreIconPickerAfterMedia();
				} ).fail( function () {
					restoreIconPickerAfterMedia();
				} );
			} else {
				restoreIconPickerAfterMedia();
			}
		} );

		svgMediaFrame.on( 'close', function () {
			window.setTimeout( function () {
				if ( $( '.media-modal:visible' ).length ) {
					return;
				}
				if ( $( '#hello-movieengine-icon-picker-modal' ).is( ':visible' ) ) {
					return;
				}
				restoreIconPickerAfterMedia();
			}, 50 );
		} );

		svgMediaFrame.open();
	} );
} )( jQuery );