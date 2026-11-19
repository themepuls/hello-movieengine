/**
 * Hello Movie Engine – Customizer controls JS.
 *
 * Handles:
 * - Spacing control with link/unlink
 * - Alpha color picker
 * - Toggle switch
 *
 * @package Hello_Movie Engine
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

	/* ==========================================================
	   Mobile Bottom Navigation Repeater Control & Visual Icon Picker
	   ========================================================== */

	var defaultTypeIcons = {
		home: 'svg:home',
		movies: 'svg:film',
		series: 'svg:tv',
		watchlist: 'svg:bookmark-plus',
		account: 'svg:user',
		search: 'svg:search',
		genres: 'svg:tags',
		subscription: 'svg:crown',
		custom: 'svg:link'
	};

	var $activePickerRow = null;

	function syncNavRepeater( $control ) {
		var $hidden   = $control.find( '.hello-movieengine-nav-repeater-value' );
		var settingId = $hidden.data( 'customize-setting-link' );
		var items     = [];

		$control.find( '.hello-movieengine-repeater-row' ).each( function ( index ) {
			var $row = $( this );
			var id   = $row.data( 'id' ) || ( 'item_' + Date.now() + '_' + index );
			$row.data( 'id', id );

			var type        = $row.find( '.hello-movieengine-item-type' ).val() || 'custom';
			var typeText    = $row.find( '.hello-movieengine-item-type option:selected' ).text().trim();
			var label       = $row.find( '.hello-movieengine-item-label' ).val() || '';
			var icon        = $row.find( '.hello-movieengine-item-icon' ).val() || ( defaultTypeIcons[ type ] || 'svg:link' );
			var customIcon  = $row.find( '.hello-movieengine-item-custom-icon' ).val() || '';
			var pageId      = parseInt( $row.find( '.hello-movieengine-item-page' ).val(), 10 ) || 0;
			var url         = $row.find( '.hello-movieengine-item-url' ).val() || '';
			var visibility  = $row.find( '.hello-movieengine-item-visibility' ).val() || 'all';
			var enabled     = $row.find( '.hello-movieengine-item-enabled' ).is( ':checked' );

			// Update header title text and type badge in real-time.
			$row.find( '.hello-movieengine-repeater-type-badge' ).text( typeText );
			$row.find( '.hello-movieengine-repeater-title-text' ).text( label || typeText );

			items.push( {
				id: id,
				type: type,
				label: label,
				icon: icon,
				custom_icon: customIcon,
				page_id: pageId,
				url: url,
				visibility: visibility,
				enabled: enabled
			} );
		} );

		var val = JSON.stringify( items );
		$hidden.val( val ).trigger( 'change' );

		if ( settingId && typeof wp !== 'undefined' && wp.customize ) {
			wp.customize( settingId ).set( val );
		}
	}

	function initNavRepeaterSortable() {
		$( '.hello-movieengine-nav-repeater-items' ).each( function () {
			var $list = $( this );
			if ( $list.data( 'ui-sortable' ) || ! $.fn.sortable ) {
				return;
			}
			$list.sortable( {
				handle: '.hello-movieengine-repeater-drag',
				items: '.hello-movieengine-repeater-row',
				axis: 'y',
				opacity: 0.75,
				placeholder: 'ui-sortable-placeholder',
				update: function () {
					syncNavRepeater( $list.closest( '.hello-movieengine-nav-repeater-control' ) );
				}
			} );
		} );
	}

	// Accordion toggle.
	$( document ).on( 'click', '.hello-movieengine-repeater-header', function ( e ) {
		if ( $( e.target ).closest( '.hello-movieengine-repeater-actions, .hello-movieengine-repeater-drag' ).length ) {
			return;
		}
		var $row = $( this ).closest( '.hello-movieengine-repeater-row' );
		$row.toggleClass( 'is-open' );
		$row.find( '.hello-movieengine-repeater-body' ).first().slideToggle( 180 );
	} );

	$( document ).on( 'click', '.hello-movieengine-repeater-toggle-edit', function ( e ) {
		e.preventDefault();
		var $row = $( this ).closest( '.hello-movieengine-repeater-row' );
		$row.toggleClass( 'is-open' );
		$row.find( '.hello-movieengine-repeater-body' ).first().slideToggle( 180 );
	} );

	// Move Up.
	$( document ).on( 'click', '.hello-movieengine-repeater-move-up', function ( e ) {
		e.preventDefault();
		var $row = $( this ).closest( '.hello-movieengine-repeater-row' );
		var $prev = $row.prev( '.hello-movieengine-repeater-row' );
		if ( $prev.length ) {
			$row.insertBefore( $prev );
			syncNavRepeater( $row.closest( '.hello-movieengine-nav-repeater-control' ) );
		}
	} );

	// Move Down.
	$( document ).on( 'click', '.hello-movieengine-repeater-move-down', function ( e ) {
		e.preventDefault();
		var $row = $( this ).closest( '.hello-movieengine-repeater-row' );
		var $next = $row.next( '.hello-movieengine-repeater-row' );
		if ( $next.length ) {
			$row.insertAfter( $next );
			syncNavRepeater( $row.closest( '.hello-movieengine-nav-repeater-control' ) );
		}
	} );

	// Delete item.
	$( document ).on( 'click', '.hello-movieengine-repeater-delete', function ( e ) {
		e.preventDefault();
		var $control = $( this ).closest( '.hello-movieengine-nav-repeater-control' );
		var rowCount = $control.find( '.hello-movieengine-repeater-row' ).length;
		if ( rowCount <= 1 ) {
			alert( 'You must keep at least 1 navigation item.' );
			return;
		}
		$( this ).closest( '.hello-movieengine-repeater-row' ).slideUp( 150, function () {
			$( this ).remove();
			syncNavRepeater( $control );
		} );
	} );

	// Add item.
	$( document ).on( 'click', '.hello-movieengine-repeater-add', function ( e ) {
		e.preventDefault();
		var $control = $( this ).closest( '.hello-movieengine-nav-repeater-control' );
		var $list    = $control.find( '.hello-movieengine-nav-repeater-items' );
		var $first   = $list.find( '.hello-movieengine-repeater-row' ).first();

		if ( ! $first.length ) {
			return;
		}

		var $newRow = $first.clone();
		var newId   = 'item_' + Date.now();

		$newRow.data( 'id', newId ).attr( 'data-id', newId );
		$newRow.removeClass( 'is-open' );
		$newRow.find( '.hello-movieengine-repeater-body' ).hide();

		// Default to custom link.
		$newRow.find( '.hello-movieengine-item-type' ).val( 'custom' );
		$newRow.find( '.hello-movieengine-item-label' ).val( 'New Item' );
		$newRow.find( '.hello-movieengine-item-icon' ).val( 'svg:link' );
		$newRow.find( '.hello-movieengine-item-custom-icon' ).val( '' );
		$newRow.find( '.hello-movieengine-icon-display-name' ).text( 'External Link' );
		$newRow.find( '.hello-movieengine-item-page' ).val( '0' );
		$newRow.find( '.hello-movieengine-item-url' ).val( '' );
		$newRow.find( '.hello-movieengine-item-visibility' ).val( 'all' );
		$newRow.find( '.hello-movieengine-item-enabled' ).prop( 'checked', true );

		$list.append( $newRow );
		$newRow.addClass( 'is-open' ).find( '.hello-movieengine-repeater-body' ).slideDown( 180 );

		syncNavRepeater( $control );
	} );

	// Item type change: update default icon if unchanged.
	$( document ).on( 'change', '.hello-movieengine-item-type', function () {
		var $row     = $( this ).closest( '.hello-movieengine-repeater-row' );
		var newType  = $( this ).val();

		if ( defaultTypeIcons[ newType ] ) {
			var newIcon = defaultTypeIcons[ newType ];
			$row.find( '.hello-movieengine-item-icon' ).val( newIcon );
			$row.find( '.hello-movieengine-item-custom-icon' ).val( '' );

			// Match card in picker to update preview box and name
			var $card = $( '.hello-movieengine-icon-card[data-icon="' + newIcon + '"]' ).first();
			if ( $card.length ) {
				$row.find( '.hello-movieengine-icon-preview-render' ).html( $card.find( '.hello-movieengine-icon-card-preview' ).html() );
				$row.find( '.hello-movieengine-icon-display-name' ).text( $card.find( '.hello-movieengine-icon-card-label' ).text() );
			}
		}

		syncNavRepeater( $row.closest( '.hello-movieengine-nav-repeater-control' ) );
	} );

	// Generic input/select changes in repeater row.
	$( document ).on( 'input change', '.hello-movieengine-item-label, .hello-movieengine-item-page, .hello-movieengine-item-url, .hello-movieengine-item-visibility, .hello-movieengine-item-enabled', function () {
		syncNavRepeater( $( this ).closest( '.hello-movieengine-nav-repeater-control' ) );
	} );

	/* --------------------------------------------------------
	   Visual Icon Picker Modal Interactions
	   -------------------------------------------------------- */

	// Open Picker.
	$( document ).on( 'click', '.hello-movieengine-open-icon-picker, .hello-movieengine-icon-preview-box', function ( e ) {
		e.preventDefault();
		$activePickerRow = $( this ).closest( '.hello-movieengine-repeater-row' );
		if ( ! $activePickerRow.length ) {
			return;
		}

		var currentIcon = $activePickerRow.find( '.hello-movieengine-item-icon' ).val() || '';
		var $modal      = $( '#hello-movieengine-icon-picker-modal' );

		// Reset search & categories
		$modal.find( '.hello-movieengine-icon-search-input' ).val( '' );
		$modal.find( '.hello-movieengine-icon-search-clear' ).hide();
		$modal.find( '.hello-movieengine-cat-pill' ).removeClass( 'is-active' ).first().addClass( 'is-active' );
		$modal.find( '.hello-movieengine-icon-card' ).show().removeClass( 'is-selected' );
		$modal.find( '.hello-movieengine-icon-grid' ).show();
		$modal.find( '.hello-movieengine-icon-custom-panel' ).hide();

		var totalCards = $modal.find( '.hello-movieengine-icon-card' ).length;
		$modal.find( '.hello-movieengine-icon-total-badge' ).text( totalCards + ' icons' );

		// Highlight currently selected icon
		if ( currentIcon ) {
			var $selectedCard = $modal.find( '.hello-movieengine-icon-card[data-icon="' + currentIcon + '"]' );
			if ( $selectedCard.length ) {
				$selectedCard.addClass( 'is-selected' );
			}
		}

		$modal.fadeIn( 150 );
		$modal.find( '.hello-movieengine-icon-search-input' ).trigger( 'focus' );
	} );

	// Close Picker.
	function closeIconPicker() {
		$( '#hello-movieengine-icon-picker-modal' ).fadeOut( 120 );
		$activePickerRow = null;
	}

	$( document ).on( 'click', '.hello-movieengine-icon-picker-close, .hello-movieengine-icon-picker-backdrop', function ( e ) {
		e.preventDefault();
		closeIconPicker();
	} );

	function updateIconBadge( $modal, cat, query ) {
		var totalCount = $modal.find( '.hello-movieengine-icon-card' ).length;
		if ( 'custom' === cat ) {
			$modal.find( '.hello-movieengine-icon-total-badge' ).text( 'Custom' );
			return;
		}
		var visibleCount = $modal.find( '.hello-movieengine-icon-card:visible' ).length;
		if ( query || 'all' !== cat ) {
			$modal.find( '.hello-movieengine-icon-total-badge' ).text( visibleCount + ' found' );
		} else {
			$modal.find( '.hello-movieengine-icon-total-badge' ).text( totalCount + ' icons' );
		}
	}

	// Category filter.
	$( document ).on( 'click', '.hello-movieengine-cat-pill', function ( e ) {
		e.preventDefault();
		var $btn  = $( this );
		var cat   = $btn.data( 'cat' );
		var $modal= $btn.closest( '.hello-movieengine-icon-picker-modal' );

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

	// Search filter.
	$( document ).on( 'input', '.hello-movieengine-icon-search-input', function () {
		var query = $( this ).val().toLowerCase().trim();
		var $modal= $( this ).closest( '.hello-movieengine-icon-picker-modal' );
		var cat   = $modal.find( '.hello-movieengine-cat-pill.is-active' ).data( 'cat' ) || 'all';

		$modal.find( '.hello-movieengine-icon-search-clear' ).toggle( query.length > 0 );

		if ( 'custom' === cat ) {
			$modal.find( '.hello-movieengine-cat-pill[data-cat="all"]' ).trigger( 'click' );
			cat = 'all';
		}

		$modal.find( '.hello-movieengine-icon-card' ).each( function () {
			var cardCat  = $( this ).data( 'cat' );
			var cardName = $( this ).data( 'name' ) || '';
			var matchesCat   = ( 'all' === cat || cardCat === cat );
			var matchesQuery = ( ! query || cardName.indexOf( query ) !== -1 );

			$( this ).toggle( matchesCat && matchesQuery );
		} );

		updateIconBadge( $modal, cat, query );
	} );

	// Clear search.
	$( document ).on( 'click', '.hello-movieengine-icon-search-clear', function () {
		var $modal = $( this ).closest( '.hello-movieengine-icon-picker-modal' );
		$modal.find( '.hello-movieengine-icon-search-input' ).val( '' ).trigger( 'input' ).trigger( 'focus' );
	} );

	// Select icon card.
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

		syncNavRepeater( $activePickerRow.closest( '.hello-movieengine-nav-repeater-control' ) );
		closeIconPicker();
	} );

	// Apply custom icon class.
	$( document ).on( 'click', '.hello-movieengine-apply-custom-icon', function ( e ) {
		e.preventDefault();
		if ( ! $activePickerRow || ! $activePickerRow.length ) {
			return;
		}

		var customClass = $( '.hello-movieengine-custom-class-field' ).val().trim();
		if ( ! customClass ) {
			return;
		}

		$activePickerRow.find( '.hello-movieengine-item-icon' ).val( customClass );
		$activePickerRow.find( '.hello-movieengine-item-custom-icon' ).val( customClass );
		$activePickerRow.find( '.hello-movieengine-icon-preview-render' ).html( '<i class="' + customClass + '" aria-hidden="true"></i>' );
		$activePickerRow.find( '.hello-movieengine-icon-display-name' ).text( customClass );

		syncNavRepeater( $activePickerRow.closest( '.hello-movieengine-nav-repeater-control' ) );
		closeIconPicker();
	} );

	$( window ).on( 'load', function () {
		setTimeout( initNavRepeaterSortable, 400 );
	} );

} )( jQuery );


