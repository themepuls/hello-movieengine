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

	/**
	 * Push hidden/linked control values into the Customizer API (required for dirty state + preview).
	 *
	 * @param {jQuery} $input Linked input with data-customize-setting-link.
	 */
	function syncCustomizeLinkedInput( $input ) {
		var val       = $input.val();
		var settingId = $input.data( 'customize-setting-link' );

		$input.trigger( 'change' );

		if ( settingId && typeof wp !== 'undefined' && wp.customize && wp.customize( settingId ) ) {
			wp.customize( settingId ).set( val );
		}
	}

	/* ==========================================================
	   Button Group Control (Segmented Pill Switcher)
	   ========================================================== */
	$( document ).on( 'click', '.hello-movieengine-btn-group-item', function ( e ) {
		e.preventDefault();
		var $btn   = $( this );
		var $group = $btn.closest( '.hello-movieengine-btn-group' );
		var $wrap  = $btn.closest( '.hello-movieengine-button-group-control' );
		var $input = $wrap.find( '.hello-movieengine-btn-group-value' );
		var val    = $btn.data( 'value' );

		$group.find( '.hello-movieengine-btn-group-item' ).removeClass( 'is-active' );
		$btn.addClass( 'is-active' );

		$input.val( val );
		syncCustomizeLinkedInput( $input );

		if ( $input.data( 'customize-setting-link' ) === 'movie_engine_design_settings[color_preset]' ) {
			toggleMeCustomColorControls();
		}
	} );

	/* ==========================================================
	   Visual Radio Cards (SVG Layout Wireframes)
	   ========================================================== */
	$( document ).on( 'click', '.hello-movieengine-visual-card', function ( e ) {
		e.preventDefault();
		var $card  = $( this );
		var $grid  = $card.closest( '.hello-movieengine-visual-cards-grid' );
		var $wrap  = $card.closest( '.hello-movieengine-visual-radio-control' );
		var $input = $wrap.find( '.hello-movieengine-visual-radio-value' );
		var val    = $card.data( 'value' );

		$grid.find( '.hello-movieengine-visual-card' ).removeClass( 'is-active' ).attr( 'aria-checked', 'false' );
		$card.addClass( 'is-active' ).attr( 'aria-checked', 'true' );

		$input.val( val );
		syncCustomizeLinkedInput( $input );
	} );

	/* ==========================================================
	   Section Tabs (GENERAL vs DESIGN)
	   ========================================================== */
	function applySectionTabs( $section ) {
		if ( ! $section || ! $section.length ) {
			return;
		}

		var $tabWrap = $section.find( '.hello-movieengine-section-tabs-wrap' );
		if ( ! $tabWrap.length ) {
			return;
		}

		var activeTab = $tabWrap.find( '.hello-movieengine-section-tab-btn.is-active' ).data( 'tab' ) || 'general';
		var $controls = $section.find( 'li.customize-control' ).not( $tabWrap.closest( 'li.customize-control' ) );

		$controls.each( function () {
			var $ctrl = $( this );
			var id    = $ctrl.attr( 'id' ) || '';
			var cl    = $ctrl.attr( 'class' ) || '';

			// Classify control as design if it relates to styling, spacing, color, padding, margin, etc.
			var isDesign = /padding|margin|spacing|color|bg|background|border|radius|alpha|overlay|shadow|height|gap|scrim|size|width/i.test( id + ' ' + cl );

			if ( activeTab === 'design' ) {
				if ( isDesign ) {
					$ctrl.css( 'display', '' );
				} else {
					$ctrl.css( 'display', 'none' );
				}
			} else {
				if ( isDesign ) {
					$ctrl.css( 'display', 'none' );
				} else {
					$ctrl.css( 'display', '' );
				}
			}
		} );
	}

	$( document ).on( 'click', '.hello-movieengine-section-tab-btn', function ( e ) {
		e.preventDefault();
		var $btn     = $( this );
		var $nav     = $btn.closest( '.hello-movieengine-section-tabs-nav' );
		var $section = $btn.closest( '.control-section' );

		$nav.find( '.hello-movieengine-section-tab-btn' ).removeClass( 'is-active' ).attr( 'aria-selected', 'false' );
		$btn.addClass( 'is-active' ).attr( 'aria-selected', 'true' );

		applySectionTabs( $section );
	} );

	// Initialize tabs when a section opens
	$( document ).on( 'click', '.accordion-section-title', function () {
		var $section = $( this ).closest( '.control-section' );
		setTimeout( function () {
			applySectionTabs( $section );
		}, 60 );
	} );

	if ( typeof wp !== 'undefined' && wp.customize && wp.customize.section ) {
		wp.customize.section.each( function ( section ) {
			section.expanded.bind( function ( isExpanded ) {
				if ( isExpanded && section.container ) {
					setTimeout( function () {
						applySectionTabs( section.container );
					}, 60 );
				}
			} );
		} );
	}

	/* ==========================================================
	   Homepage Drag & Drop Section Builder Control
	   ========================================================== */
	function initSectionBuilderSortable() {
		$( '.hello-movieengine-section-builder-items' ).each( function () {
			var $container = $( this );
			if ( $container.hasClass( 'ui-sortable' ) ) {
				return;
			}
			$container.sortable( {
				items: '.hello-movieengine-builder-row',
				handle: '.hello-movieengine-builder-drag',
				axis: 'y',
				opacity: 0.75,
				stop: function () {
					syncSectionBuilder( $container.closest( '.hello-movieengine-section-builder-control' ) );
				}
			} );
		} );
	}

	function updateRowQuerySourceUI( $row ) {
		if ( ! $row || ! $row.length ) {
			return;
		}
		var libSource = $row.find( '.hello-movieengine-section-query-source' ).val() || 'site';
		$row.find( '.hello-movieengine-query-site' ).toggle( libSource === 'site' );
		$row.find( '.hello-movieengine-query-tmdb' ).toggle( libSource === 'tmdb' );
		$row.find( '.hello-movieengine-query-site-only' ).toggle( libSource === 'site' );

		var heroSource = $row.find( '.hello-movieengine-section-hero-query-source' ).val() || 'site';
		$row.find( '.hello-movieengine-hero-query-site' ).toggle( heroSource === 'site' );
		$row.find( '.hello-movieengine-hero-query-tmdb' ).toggle( heroSource === 'tmdb' );
	}

	function syncSectionBuilder( $wrap ) {
		if ( ! $wrap || ! $wrap.length ) {
			return;
		}
		var data = [];
		$wrap.find( '.hello-movieengine-builder-row' ).each( function () {
			var $row = $( this );
			var type = $row.find( '.hello-movieengine-section-type' ).val() || 'library';
			var isEnabled = $row.find( '.hello-movieengine-builder-eye' ).data( 'enabled' ) == 1;

			var sectionCount  = parseInt( $row.find( '.hello-movieengine-section-count' ).val(), 10 ) || 12;
			var sectionCols   = parseInt( $row.find( '.hello-movieengine-section-columns' ).val(), 10 ) || 6;
			var sectionSlider = $row.find( '.hello-movieengine-section-slider' ).is( ':checked' ) ? 1 : 0;
			if ( type === 'person_list' ) {
				var personCountVal = parseInt( $row.find( '.hello-movieengine-section-person-count' ).val(), 10 );
				var personColsVal  = parseInt( $row.find( '.hello-movieengine-section-person-columns' ).val(), 10 );
				sectionCount  = ! isNaN( personCountVal ) ? personCountVal : 12;
				sectionCols   = ! isNaN( personColsVal ) ? personColsVal : 6;
				sectionSlider = $row.find( '.hello-movieengine-section-person-slider' ).is( ':checked' ) ? 1 : 0;
			}

			var sec = {
				id: $row.data( 'id' ) || 'sec_' + Math.random().toString( 36 ).substr( 2, 9 ),
				type: type,
				enabled: isEnabled ? 1 : 0,
				title: $row.find( '.hello-movieengine-section-title' ).val() || '',
				query_source: $row.find( '.hello-movieengine-section-query-source' ).val() || 'site',
				query_type: $row.find( '.hello-movieengine-section-query' ).val() || 'trending',
				tmdb_type: $row.find( '.hello-movieengine-section-tmdb-type' ).val() || 'day',
				post_type: $row.find( '.hello-movieengine-section-post-type' ).val() || 'movie_engine_movie',
				ids: $row.find( '.hello-movieengine-section-ids' ).val() || '',
				genre: $row.find( '.hello-movieengine-section-genre' ).val() || '',
				year: $row.find( '.hello-movieengine-section-year' ).val() || '',
				language: $row.find( '.hello-movieengine-section-language' ).val() || '',
				orderby: $row.find( '.hello-movieengine-section-orderby' ).val() || 'date',
				order: $row.find( '.hello-movieengine-section-order' ).val() || 'DESC',
				count: sectionCount,
				columns: sectionCols,
				slider: sectionSlider,
				show_view_all: $row.find( '.hello-movieengine-section-view-all' ).length ? ( $row.find( '.hello-movieengine-section-view-all' ).is( ':checked' ) ? 1 : 0 ) : 1,
				slider_autoplay: $row.find( '.hello-movieengine-section-slider-autoplay' ).is( ':checked' ) ? 1 : 0,
				slider_speed: parseInt( $row.find( '.hello-movieengine-section-slider-speed' ).val(), 10 ) || 3000,
				slider_loop: $row.find( '.hello-movieengine-section-slider-loop' ).length ? ( $row.find( '.hello-movieengine-section-slider-loop' ).is( ':checked' ) ? 1 : 0 ) : 0,
				slider_pause: $row.find( '.hello-movieengine-section-slider-pause' ).length ? ( $row.find( '.hello-movieengine-section-slider-pause' ).is( ':checked' ) ? 1 : 0 ) : 1,
				slider_dots: $row.find( '.hello-movieengine-section-slider-dots' ).length ? ( $row.find( '.hello-movieengine-section-slider-dots' ).is( ':checked' ) ? 1 : 0 ) : 0,
				show_rating: $row.find( '.hello-movieengine-section-show-rating' ).length ? ( $row.find( '.hello-movieengine-section-show-rating' ).is( ':checked' ) ? 1 : 0 ) : 1,
				show_year: $row.find( '.hello-movieengine-section-show-year' ).length ? ( $row.find( '.hello-movieengine-section-show-year' ).is( ':checked' ) ? 1 : 0 ) : 1,
				show_quality: $row.find( '.hello-movieengine-section-show-quality' ).length ? ( $row.find( '.hello-movieengine-section-show-quality' ).is( ':checked' ) ? 1 : 0 ) : 1,
				department: $row.find( '.hello-movieengine-section-department' ).val() || '',
				person_orderby: $row.find( '.hello-movieengine-section-person-orderby' ).val() || 'title',
				person_style: $row.find( '.hello-movieengine-section-person-style' ).val() || 'overlay',
				person_autoplay: $row.find( '.hello-movieengine-section-person-autoplay' ).length ? ( $row.find( '.hello-movieengine-section-person-autoplay' ).is( ':checked' ) ? 1 : 0 ) : 0,
				person_speed: parseInt( $row.find( '.hello-movieengine-section-person-speed' ).val(), 10 ) || 3000,
				hero_style: $row.find( '.hello-movieengine-section-hero-style' ).val() || 'style1',
				hero_ids: $row.find( '.hello-movieengine-section-hero-ids' ).val() || '',
				hero_query_source: $row.find( '.hello-movieengine-section-hero-query-source' ).val() || 'site',
				hero_type: $row.find( '.hello-movieengine-section-hero-type' ).val() || 'trending',
				hero_tmdb_type: $row.find( '.hello-movieengine-section-hero-tmdb-type' ).val() || 'day',
				hero_height: $row.find( '.hello-movieengine-section-hero-height' ).val() || '70vh',
				hero_limit: parseInt( $row.find( '.hello-movieengine-section-hero-limit' ).val(), 10 ) || 5,
				hero_post_type: $row.find( '.hello-movieengine-section-hero-post-type' ).val() || 'movie_engine_movie,movie_engine_series',
				hero_genre: $row.find( '.hello-movieengine-section-hero-genre' ).val() || '',
				hero_year: $row.find( '.hello-movieengine-section-hero-year' ).val() || '',
				hero_speed: parseInt( $row.find( '.hello-movieengine-section-hero-speed' ).val(), 10 ) || 5000,
				hero_loop: $row.find( '.hello-movieengine-section-hero-loop' ).length ? ( $row.find( '.hello-movieengine-section-hero-loop' ).is( ':checked' ) ? 1 : 0 ) : 1,
				hero_pause: $row.find( '.hello-movieengine-section-hero-pause' ).length ? ( $row.find( '.hello-movieengine-section-hero-pause' ).is( ':checked' ) ? 1 : 0 ) : 1,
				hero_nav: $row.find( '.hello-movieengine-section-hero-nav' ).length ? ( $row.find( '.hello-movieengine-section-hero-nav' ).is( ':checked' ) ? 1 : 0 ) : 1,
				hero_dots: $row.find( '.hello-movieengine-section-hero-dots' ).length ? ( $row.find( '.hello-movieengine-section-hero-dots' ).is( ':checked' ) ? 1 : 0 ) : 1,
				autoplay: $row.find( '.hello-movieengine-section-autoplay' ).is( ':checked' ) ? 1 : 0,
				show_trailer: $row.find( '.hello-movieengine-section-hero-trailer' ).length ? ( $row.find( '.hello-movieengine-section-hero-trailer' ).is( ':checked' ) ? 1 : 0 ) : 1,
				show_watchlist: $row.find( '.hello-movieengine-section-hero-watchlist' ).length ? ( $row.find( '.hello-movieengine-section-hero-watchlist' ).is( ':checked' ) ? 1 : 0 ) : 1,
				show_imdb: $row.find( '.hello-movieengine-section-hero-imdb' ).length ? ( $row.find( '.hello-movieengine-section-hero-imdb' ).is( ':checked' ) ? 1 : 0 ) : 1,
				show_desc: $row.find( '.hello-movieengine-section-hero-desc' ).length ? ( $row.find( '.hello-movieengine-section-hero-desc' ).is( ':checked' ) ? 1 : 0 ) : 1,
				studio_layout: $row.find( '.hello-movieengine-section-studio-layout' ).val() || 'grid',
				studio_columns: parseInt( $row.find( '.hello-movieengine-section-studio-columns' ).val(), 10 ) || 6,
				studio_limit: parseInt( $row.find( '.hello-movieengine-section-studio-limit' ).val(), 10 ) || 12,
				studio_title: $row.find( '.hello-movieengine-section-studio-title' ).is( ':checked' ) ? 1 : 0,
				genre_layout: $row.find( '.hello-movieengine-section-genre-layout' ).val() || 'pills',
				genre_limit: parseInt( $row.find( '.hello-movieengine-section-genre-limit' ).val(), 10 ) || 12,
				genre_count: $row.find( '.hello-movieengine-section-genre-count' ).length ? ( $row.find( '.hello-movieengine-section-genre-count' ).is( ':checked' ) ? 1 : 0 ) : 1,
				history_count: parseInt( $row.find( '.hello-movieengine-section-history-count' ).val(), 10 ) || 6,
				history_columns: parseInt( $row.find( '.hello-movieengine-section-history-columns' ).val(), 10 ) || 4,
				year_layout: $row.find( '.hello-movieengine-section-year-layout' ).val() || 'pills',
				year_limit: parseInt( $row.find( '.hello-movieengine-section-year-limit' ).val(), 10 ) || 10,
				search_placeholder: $row.find( '.hello-movieengine-section-search-placeholder' ).val() || '',
				search_icon: $row.find( '.hello-movieengine-section-search-icon' ).length ? ( $row.find( '.hello-movieengine-section-search-icon' ).is( ':checked' ) ? 1 : 0 ) : 1,
				shortcode: $row.find( '.hello-movieengine-section-shortcode' ).val() || ''
			};
			data.push( sec );
		} );

		var json    = JSON.stringify( data );
		var $hidden = $wrap.find( '.hello-movieengine-section-builder-value' );
		$hidden.val( json ).trigger( 'change' );

		var settingId = $hidden.data( 'customize-setting-link' );
		if ( settingId && typeof wp !== 'undefined' && wp.customize && wp.customize( settingId ) ) {
			wp.customize( settingId ).set( json );
		}
	}

	// Toggle Visibility (Eye)
	$( document ).on( 'click', '.hello-movieengine-builder-eye', function ( e ) {
		e.stopPropagation();
		var $btn      = $( this );
		var $row      = $btn.closest( '.hello-movieengine-builder-row' );
		var isEnabled = $btn.data( 'enabled' ) == 1;

		if ( isEnabled ) {
			$btn.data( 'enabled', 0 );
			$btn.find( '.dashicons' ).removeClass( 'dashicons-visibility' ).addClass( 'dashicons-hidden' );
			$row.removeClass( 'is-enabled' ).addClass( 'is-disabled' );
		} else {
			$btn.data( 'enabled', 1 );
			$btn.find( '.dashicons' ).removeClass( 'dashicons-hidden' ).addClass( 'dashicons-visibility' );
			$row.removeClass( 'is-disabled' ).addClass( 'is-enabled' );
		}

		syncSectionBuilder( $row.closest( '.hello-movieengine-section-builder-control' ) );
	} );

	// Toggle Accordion Body
	$( document ).on( 'click', '.hello-movieengine-builder-header', function ( e ) {
		if ( $( e.target ).closest( '.hello-movieengine-builder-eye, .hello-movieengine-builder-delete, .hello-movieengine-builder-drag' ).length ) {
			return;
		}
		var $row  = $( this ).closest( '.hello-movieengine-builder-row' );
		var $body = $row.find( '.hello-movieengine-builder-body' );
		var $btn  = $row.find( '.hello-movieengine-builder-toggle-edit .dashicons' );

		$body.slideToggle( 180, function () {
			if ( $body.is( ':visible' ) ) {
				$btn.removeClass( 'dashicons-arrow-down-alt2' ).addClass( 'dashicons-arrow-up-alt2' );
			} else {
				$btn.removeClass( 'dashicons-arrow-up-alt2' ).addClass( 'dashicons-arrow-down-alt2' );
			}
		} );
	} );

	// Title input change
	$( document ).on( 'input change', '.hello-movieengine-section-title', function () {
		var $input = $( this );
		var $row   = $input.closest( '.hello-movieengine-builder-row' );
		var val    = $input.val() || $row.find( '.hello-movieengine-section-type option:selected' ).text();
		$row.find( '.hello-movieengine-builder-title-text' ).text( val );
		syncSectionBuilder( $row.closest( '.hello-movieengine-section-builder-control' ) );
	} );

	// Section type change
	$( document ).on( 'change', '.hello-movieengine-section-type', function () {
		var $select = $( this );
		var $row    = $select.closest( '.hello-movieengine-builder-row' );
		var type    = $select.val();
		var label   = $select.find( 'option:selected' ).text();

		$row.find( '.hello-movieengine-builder-type-badge' ).text( label );
		if ( ! $row.find( '.hello-movieengine-section-title' ).val() ) {
			$row.find( '.hello-movieengine-builder-title-text' ).text( label );
		}

		$row.find( '.hello-movieengine-fields-library' ).toggle( type === 'library' || type === 'top10' );
		$row.find( '.hello-movieengine-fields-hero' ).toggle( type === 'hero' );
		$row.find( '.hello-movieengine-fields-person' ).toggle( type === 'person_list' );
		$row.find( '.hello-movieengine-fields-studios' ).toggle( type === 'studios' );
		$row.find( '.hello-movieengine-fields-genres' ).toggle( type === 'genres' );
		$row.find( '.hello-movieengine-fields-history' ).toggle( type === 'history' );
		$row.find( '.hello-movieengine-fields-years' ).toggle( type === 'years' );
		$row.find( '.hello-movieengine-fields-live_search' ).toggle( type === 'live_search' );
		$row.find( '.hello-movieengine-fields-shortcode' ).toggle( type === 'shortcode' );
		$row.find( '.hello-movieengine-fields-page-content' ).toggle( type === 'page_content' );

		updateRowQuerySourceUI( $row );
		syncSectionBuilder( $row.closest( '.hello-movieengine-section-builder-control' ) );
	} );

	$( document ).on( 'change', '.hello-movieengine-section-query-source, .hello-movieengine-section-hero-query-source', function () {
		var $row = $( this ).closest( '.hello-movieengine-builder-row' );
		updateRowQuerySourceUI( $row );
		syncSectionBuilder( $row.closest( '.hello-movieengine-section-builder-control' ) );
	} );

	// Other field changes
	$( document ).on( 'change input', '.hello-movieengine-builder-body input, .hello-movieengine-builder-body select, .hello-movieengine-builder-body textarea', function () {
		if ( $( this ).hasClass( 'hello-movieengine-section-title' ) || $( this ).hasClass( 'hello-movieengine-section-type' ) ) {
			return;
		}
		syncSectionBuilder( $( this ).closest( '.hello-movieengine-section-builder-control' ) );
	} );

	// Delete Section Row
	$( document ).on( 'click', '.hello-movieengine-builder-delete', function ( e ) {
		e.stopPropagation();
		if ( ! confirm( 'Remove this section from homepage?' ) ) {
			return;
		}
		var $row  = $( this ).closest( '.hello-movieengine-builder-row' );
		var $wrap = $row.closest( '.hello-movieengine-section-builder-control' );

		$row.slideUp( 180, function () {
			$row.remove();
			syncSectionBuilder( $wrap );
		} );
	} );

	// Add Section Row
	$( document ).on( 'click', '.hello-movieengine-builder-add-btn', function ( e ) {
		e.preventDefault();
		var $wrap      = $( this ).closest( '.hello-movieengine-section-builder-control' );
		var $container = $wrap.find( '.hello-movieengine-section-builder-items' );
		var id         = 'sec_' + Date.now();

		var tpl = '<div class="hello-movieengine-builder-row is-enabled" data-id="' + id + '">' +
			'<div class="hello-movieengine-builder-header">' +
				'<span class="hello-movieengine-builder-drag"><svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><circle cx="9" cy="5" r="2"/><circle cx="9" cy="12" r="2"/><circle cx="9" cy="19" r="2"/><circle cx="15" cy="5" r="2"/><circle cx="15" cy="12" r="2"/><circle cx="15" cy="19" r="2"/></svg></span>' +
				'<button type="button" class="hello-movieengine-builder-eye" data-enabled="1"><span class="dashicons dashicons-visibility"></span></button>' +
				'<span class="hello-movieengine-builder-title"><strong class="hello-movieengine-builder-title-text">New Section Row</strong><span class="hello-movieengine-builder-type-badge">Carousel</span></span>' +
				'<div class="hello-movieengine-builder-actions">' +
					'<button type="button" class="hello-movieengine-builder-toggle-edit"><span class="dashicons dashicons-arrow-up-alt2"></span></button>' +
					'<button type="button" class="hello-movieengine-builder-delete"><span class="dashicons dashicons-trash"></span></button>' +
				'</div>' +
			'</div>' +
			'<div class="hello-movieengine-builder-body" style="display: block;">' +
				'<div class="hello-movieengine-builder-field"><label>Section Type</label><select class="hello-movieengine-section-type">' +
					'<option value="library" selected>Movie / Series Carousel (Slider)</option>' +
					'<option value="hero">Hero Billboard / Banner</option>' +
					'<option value="top10">Top 10 (Rank Numbers)</option>' +
					'<option value="person_list">Cast & Stars (Celebrities)</option>' +
					'<option value="studios">Studio / Brand Chips</option>' +
					'<option value="genres">Genre Category Bar</option>' +
					'<option value="history">Continue Watching (User History)</option>' +
					'<option value="years">Release Years Filter</option>' +
					'<option value="live_search">Interactive Live Search Bar</option>' +
					'<option value="page_content">Page Content (From Editor)</option>' +
					'<option value="shortcode">Custom Shortcode</option>' +
				'</select></div>' +
				'<div class="hello-movieengine-builder-field"><label>Section Title / Heading</label><input type="text" class="hello-movieengine-section-title" value="New Section Row" placeholder="e.g. Popular Movies" /></div>' +

				'<!-- 1. Library & Top10 -->' +
				'<div class="hello-movieengine-fields-library">' +
					'<div class="hello-movieengine-builder-group-title"><span class="dashicons dashicons-filter"></span>Content Query & Filters</div>' +
					'<div class="hello-movieengine-builder-field"><label>List data source</label><select class="hello-movieengine-section-query-source"><option value="site" selected>Website — views, dates &amp; ratings</option><option value="tmdb">TMDb — live list (imported only)</option></select></div>' +
					'<div class="hello-movieengine-query-site"><div class="hello-movieengine-builder-field"><label>Website preset (type)</label><select class="hello-movieengine-section-query"><option value="trending" selected>Trending Now</option><option value="new_releases">New Releases</option><option value="upcoming">Upcoming Releases</option><option value="popular">Most Popular</option><option value="top_rated">Top Rated</option></select></div></div>' +
					'<div class="hello-movieengine-query-tmdb" style="display:none;"><div class="hello-movieengine-builder-field"><label>TMDb list (tmdb_type)</label><select class="hello-movieengine-section-tmdb-type"><option value="day" selected>Trending today</option><option value="week">Trending this week</option><option value="now_playing">Now playing / On the air</option><option value="popular">Popular</option><option value="top_rated">Top rated</option><option value="upcoming">Upcoming</option></select></div></div>' +
					'<div class="hello-movieengine-builder-field"><label>Post Type</label><select class="hello-movieengine-section-post-type"><option value="movie_engine_movie" selected>Movies Only</option><option value="movie_engine_series">TV Series Only</option><option value="movie_engine_movie,movie_engine_series">Both Movies & TV Series</option></select></div>' +
					'<div class="hello-movieengine-builder-field"><label>Specific Post IDs (optional)</label><input type="text" class="hello-movieengine-section-ids" value="" placeholder="e.g. 101, 204, 315 (leave blank for dynamic query)" /></div>' +
					'<div class="hello-movieengine-builder-row-grid-3 hello-movieengine-query-site-only">' +
						'<div class="hello-movieengine-builder-field"><label>Genre Slug</label><input type="text" class="hello-movieengine-section-genre" value="" placeholder="e.g. action" /></div>' +
						'<div class="hello-movieengine-builder-field"><label>Year</label><input type="text" class="hello-movieengine-section-year" value="" placeholder="e.g. 2025" /></div>' +
						'<div class="hello-movieengine-builder-field"><label>Language</label><input type="text" class="hello-movieengine-section-language" value="" placeholder="e.g. en, ko" /></div>' +
					'</div>' +
					'<div class="hello-movieengine-builder-row-grid hello-movieengine-query-site-only">' +
						'<div class="hello-movieengine-builder-field"><label>Order By</label><select class="hello-movieengine-section-orderby"><option value="date" selected>Release Date</option><option value="views">Views Count</option><option value="title">Title (A-Z)</option><option value="rand">Random</option></select></div>' +
						'<div class="hello-movieengine-builder-field"><label>Sort Direction</label><select class="hello-movieengine-section-order"><option value="DESC" selected>Descending</option><option value="ASC">Ascending</option></select></div>' +
					'</div>' +
					'<div class="hello-movieengine-builder-group-title"><span class="dashicons dashicons-layout"></span>Layout & Display</div>' +
					'<div class="hello-movieengine-builder-row-grid">' +
						'<div class="hello-movieengine-builder-field"><label>Items Count</label><input type="number" class="hello-movieengine-section-count" value="12" min="3" max="40" /></div>' +
						'<div class="hello-movieengine-builder-field"><label>Columns (Desktop)</label><input type="number" class="hello-movieengine-section-columns" value="6" min="3" max="8" /></div>' +
					'</div>' +
					'<div class="hello-movieengine-builder-row-grid">' +
						'<div class="hello-movieengine-builder-field hello-movieengine-builder-toggle-field"><label><input type="checkbox" class="hello-movieengine-section-slider" value="1" checked /> Slider Carousel</label></div>' +
						'<div class="hello-movieengine-builder-field hello-movieengine-builder-toggle-field"><label><input type="checkbox" class="hello-movieengine-section-view-all" value="1" checked /> Show "View All" link</label></div>' +
					'</div>' +
					'<div class="hello-movieengine-builder-group-title"><span class="dashicons dashicons-controls-play"></span>Slider & Carousel Options</div>' +
					'<div class="hello-movieengine-builder-row-grid">' +
						'<div class="hello-movieengine-builder-field hello-movieengine-builder-toggle-field"><label><input type="checkbox" class="hello-movieengine-section-slider-autoplay" value="1" /> Auto-slide carousel</label></div>' +
						'<div class="hello-movieengine-builder-field"><label>Speed (ms)</label><input type="number" class="hello-movieengine-section-slider-speed" value="3000" min="1500" max="10000" step="500" /></div>' +
					'</div>' +
					'<div class="hello-movieengine-builder-row-grid">' +
						'<div class="hello-movieengine-builder-field hello-movieengine-builder-toggle-field"><label><input type="checkbox" class="hello-movieengine-section-slider-loop" value="1" /> Infinite Loop</label></div>' +
						'<div class="hello-movieengine-builder-field hello-movieengine-builder-toggle-field"><label><input type="checkbox" class="hello-movieengine-section-slider-pause" value="1" checked /> Pause on Hover</label></div>' +
					'</div>' +
					'<div class="hello-movieengine-builder-field hello-movieengine-builder-toggle-field"><label><input type="checkbox" class="hello-movieengine-section-slider-dots" value="1" /> Show Dot Indicators</label></div>' +
					'<div class="hello-movieengine-builder-group-title"><span class="dashicons dashicons-visibility"></span>Card Badges & Meta</div>' +
					'<div class="hello-movieengine-builder-row-grid-3">' +
						'<div class="hello-movieengine-builder-field hello-movieengine-builder-toggle-field"><label><input type="checkbox" class="hello-movieengine-section-show-rating" value="1" checked /> Rating Badge</label></div>' +
						'<div class="hello-movieengine-builder-field hello-movieengine-builder-toggle-field"><label><input type="checkbox" class="hello-movieengine-section-show-year" value="1" checked /> Release Year</label></div>' +
						'<div class="hello-movieengine-builder-field hello-movieengine-builder-toggle-field"><label><input type="checkbox" class="hello-movieengine-section-show-quality" value="1" checked /> Quality (4K/HD)</label></div>' +
					'</div>' +
				'</div>' +

				'<!-- 2. Hero Billboard -->' +
				'<div class="hello-movieengine-fields-hero" style="display: none;">' +
					'<div class="hello-movieengine-builder-group-title"><span class="dashicons dashicons-layout"></span>Hero Style & Layout</div>' +
					'<div class="hello-movieengine-builder-field"><label>Hero Banner Style</label><select class="hello-movieengine-section-hero-style"><option value="style1" selected>Style 1 — Classic Fullwidth Billboard</option><option value="style2">Style 2 — Bottom Thumbnail Slider</option><option value="style3">Style 3 — Compact Thumbnail Strip</option><option value="style4">Style 4 — Cinema Poster Cards</option><option value="style5">Style 5 — Arc / Curved Thumbnails Rail</option></select></div>' +
					'<div class="hello-movieengine-builder-row-grid">' +
						'<div class="hello-movieengine-builder-field"><label>Slides Count</label><input type="number" class="hello-movieengine-section-hero-limit" value="5" min="1" max="15" /></div>' +
						'<div class="hello-movieengine-builder-field"><label>Hero Height (vh or px)</label><input type="text" class="hello-movieengine-section-hero-height" value="70vh" placeholder="70vh" /></div>' +
					'</div>' +
					'<div class="hello-movieengine-builder-group-title"><span class="dashicons dashicons-filter"></span>Content Source & Query</div>' +
					'<div class="hello-movieengine-builder-field"><label>Specific Post IDs (optional)</label><input type="text" class="hello-movieengine-section-hero-ids" value="" placeholder="e.g. 101, 204, 315 (leave blank for preset)" /><span class="description" style="font-size: 11px; color: #64748b;">Overrides preset below when entered.</span></div>' +
					'<div class="hello-movieengine-builder-field"><label>List data source</label><select class="hello-movieengine-section-hero-query-source"><option value="site" selected>Website — views, dates &amp; ratings</option><option value="tmdb">TMDb — live list (imported only)</option></select></div>' +
					'<div class="hello-movieengine-hero-query-site"><div class="hello-movieengine-builder-field"><label>Website preset (type)</label><select class="hello-movieengine-section-hero-type"><option value="trending" selected>Trending Spotlight</option><option value="new_releases">Latest Releases</option><option value="popular">Most Popular</option><option value="top_rated">Top Rated</option><option value="upcoming">Upcoming</option></select></div></div>' +
					'<div class="hello-movieengine-hero-query-tmdb" style="display:none;"><div class="hello-movieengine-builder-field"><label>TMDb list (tmdb_type)</label><select class="hello-movieengine-section-hero-tmdb-type"><option value="day" selected>Trending today</option><option value="week">Trending this week</option><option value="now_playing">Now playing / On the air</option><option value="popular">Popular</option><option value="top_rated">Top rated</option><option value="upcoming">Upcoming</option></select></div></div>' +
					'<div class="hello-movieengine-builder-field"><label>Post Type</label><select class="hello-movieengine-section-hero-post-type"><option value="movie_engine_movie,movie_engine_series" selected>Both</option><option value="movie_engine_movie">Movies Only</option><option value="movie_engine_series">TV Series Only</option></select></div>' +
					'<div class="hello-movieengine-builder-row-grid">' +
						'<div class="hello-movieengine-builder-field"><label>Genre Slug (optional)</label><input type="text" class="hello-movieengine-section-hero-genre" value="" placeholder="e.g. action" /></div>' +
						'<div class="hello-movieengine-builder-field"><label>Year (optional)</label><input type="text" class="hello-movieengine-section-hero-year" value="" placeholder="e.g. 2025" /></div>' +
					'</div>' +
					'<div class="hello-movieengine-builder-group-title"><span class="dashicons dashicons-controls-play"></span>Slider & Carousel Options</div>' +
					'<div class="hello-movieengine-builder-row-grid">' +
						'<div class="hello-movieengine-builder-field hello-movieengine-builder-toggle-field"><label><input type="checkbox" class="hello-movieengine-section-autoplay" value="1" checked /> Enable Autoplay</label></div>' +
						'<div class="hello-movieengine-builder-field"><label>Speed (ms)</label><input type="number" class="hello-movieengine-section-hero-speed" value="5000" min="2000" max="15000" step="500" /></div>' +
					'</div>' +
					'<div class="hello-movieengine-builder-row-grid">' +
						'<div class="hello-movieengine-builder-field hello-movieengine-builder-toggle-field"><label><input type="checkbox" class="hello-movieengine-section-hero-loop" value="1" checked /> Infinite Loop</label></div>' +
						'<div class="hello-movieengine-builder-field hello-movieengine-builder-toggle-field"><label><input type="checkbox" class="hello-movieengine-section-hero-pause" value="1" checked /> Pause on Hover</label></div>' +
					'</div>' +
					'<div class="hello-movieengine-builder-row-grid">' +
						'<div class="hello-movieengine-builder-field hello-movieengine-builder-toggle-field"><label><input type="checkbox" class="hello-movieengine-section-hero-nav" value="1" checked /> Prev/Next Arrows</label></div>' +
						'<div class="hello-movieengine-builder-field hello-movieengine-builder-toggle-field"><label><input type="checkbox" class="hello-movieengine-section-hero-dots" value="1" checked /> Dot Indicators</label></div>' +
					'</div>' +
					'<div class="hello-movieengine-builder-group-title"><span class="dashicons dashicons-visibility"></span>Action Buttons & Badges</div>' +
					'<div class="hello-movieengine-builder-row-grid">' +
						'<div class="hello-movieengine-builder-field hello-movieengine-builder-toggle-field"><label><input type="checkbox" class="hello-movieengine-section-hero-trailer" value="1" checked /> Show Trailer Button</label></div>' +
						'<div class="hello-movieengine-builder-field hello-movieengine-builder-toggle-field"><label><input type="checkbox" class="hello-movieengine-section-hero-watchlist" value="1" checked /> Show Watchlist Button</label></div>' +
					'</div>' +
					'<div class="hello-movieengine-builder-row-grid">' +
						'<div class="hello-movieengine-builder-field hello-movieengine-builder-toggle-field"><label><input type="checkbox" class="hello-movieengine-section-hero-imdb" value="1" checked /> Show IMDb Rating</label></div>' +
						'<div class="hello-movieengine-builder-field hello-movieengine-builder-toggle-field"><label><input type="checkbox" class="hello-movieengine-section-hero-desc" value="1" checked /> Show Synopsis Text</label></div>' +
					'</div>' +
				'</div>' +

				'<!-- 3. Person List -->' +
				'<div class="hello-movieengine-fields-person" style="display: none;">' +
					'<div class="hello-movieengine-builder-group-title"><span class="dashicons dashicons-admin-users"></span>Cast & Stars Query</div>' +
					'<div class="hello-movieengine-builder-field"><label>Department / Role</label><select class="hello-movieengine-section-department"><option value="" selected>All Roles (Actors, Directors, etc.)</option><option value="Acting">Acting (Actors)</option><option value="Directing">Directing (Directors)</option><option value="Writing">Writing (Writers)</option></select></div>' +
					'<div class="hello-movieengine-builder-field"><label>Order By</label><select class="hello-movieengine-section-person-orderby"><option value="title" selected>Name (A to Z)</option><option value="date">Recently Added</option><option value="rand">Random</option></select></div>' +
					'<div class="hello-movieengine-builder-field"><label>Card Style</label><select class="hello-movieengine-section-person-style"><option value="overlay" selected>Style 1 — Gradient overlay on photo</option><option value="stacked">Style 2 — Name &amp; role below photo</option></select></div>' +
					'<div class="hello-movieengine-builder-row-grid">' +
						'<div class="hello-movieengine-builder-field"><label>Stars Count</label><input type="number" class="hello-movieengine-section-person-count" value="12" min="3" max="30" step="1" /></div>' +
						'<div class="hello-movieengine-builder-field"><label>Columns</label><input type="number" class="hello-movieengine-section-person-columns" value="6" min="3" max="8" step="1" /></div>' +
					'</div>' +
					'<div class="hello-movieengine-builder-field hello-movieengine-builder-toggle-field"><label><input type="checkbox" class="hello-movieengine-section-person-slider" value="1" checked /> Enable Horizontal Slider Carousel</label></div>' +
					'<div class="hello-movieengine-builder-row-grid">' +
						'<div class="hello-movieengine-builder-field hello-movieengine-builder-toggle-field"><label><input type="checkbox" class="hello-movieengine-section-person-autoplay" value="1" /> Auto-slide</label></div>' +
						'<div class="hello-movieengine-builder-field"><label>Speed (ms)</label><input type="number" class="hello-movieengine-section-person-speed" value="3000" min="1500" max="10000" step="500" /></div>' +
					'</div>' +
				'</div>' +

				'<!-- 4. Studios -->' +
				'<div class="hello-movieengine-fields-studios" style="display: none;">' +
					'<div class="hello-movieengine-builder-group-title"><span class="dashicons dashicons-tag"></span>Studio Display & Layout</div>' +
					'<div class="hello-movieengine-builder-row-grid">' +
						'<div class="hello-movieengine-builder-field"><label>Layout</label><select class="hello-movieengine-section-studio-layout"><option value="grid" selected>Grid</option><option value="slider">Slider Carousel</option></select></div>' +
						'<div class="hello-movieengine-builder-field"><label>Columns (Desktop)</label><input type="number" class="hello-movieengine-section-studio-columns" value="6" min="3" max="8" /></div>' +
					'</div>' +
					'<div class="hello-movieengine-builder-row-grid">' +
						'<div class="hello-movieengine-builder-field"><label>Max Studios Limit</label><input type="number" class="hello-movieengine-section-studio-limit" value="12" min="4" max="30" /></div>' +
						'<div class="hello-movieengine-builder-field hello-movieengine-builder-toggle-field" style="margin-top: 20px;"><label><input type="checkbox" class="hello-movieengine-section-studio-title" value="1" /> Show Name Text</label></div>' +
					'</div>' +
				'</div>' +

				'<!-- 5. Genres -->' +
				'<div class="hello-movieengine-fields-genres" style="display: none;">' +
					'<div class="hello-movieengine-builder-group-title"><span class="dashicons dashicons-category"></span>Genre Bar Settings</div>' +
					'<div class="hello-movieengine-builder-row-grid">' +
						'<div class="hello-movieengine-builder-field"><label>Layout</label><select class="hello-movieengine-section-genre-layout"><option value="pills" selected>Pill Buttons</option><option value="slider">Slider Carousel</option><option value="grid">Grid Cards</option></select></div>' +
						'<div class="hello-movieengine-builder-field"><label>Max Genres Count</label><input type="number" class="hello-movieengine-section-genre-limit" value="12" min="4" max="30" /></div>' +
					'</div>' +
					'<div class="hello-movieengine-builder-field hello-movieengine-builder-toggle-field"><label><input type="checkbox" class="hello-movieengine-section-genre-count" value="1" checked /> Show Title Counts (e.g. Action (42))</label></div>' +
				'</div>' +

				'<!-- 6. History -->' +
				'<div class="hello-movieengine-fields-history" style="display: none;">' +
					'<div class="hello-movieengine-builder-group-title"><span class="dashicons dashicons-clock"></span>Continue Watching Settings</div>' +
					'<p class="description" style="font-size: 11px; color: #64748b; margin-bottom: 8px;">Shows unfinished movies/episodes for logged-in users with progress bar.</p>' +
					'<div class="hello-movieengine-builder-row-grid">' +
						'<div class="hello-movieengine-builder-field"><label>Max History Items</label><input type="number" class="hello-movieengine-section-history-count" value="6" min="2" max="20" /></div>' +
						'<div class="hello-movieengine-builder-field"><label>Columns (Desktop)</label><input type="number" class="hello-movieengine-section-history-columns" value="4" min="2" max="6" /></div>' +
					'</div>' +
				'</div>' +

				'<!-- 7. Years -->' +
				'<div class="hello-movieengine-fields-years" style="display: none;">' +
					'<div class="hello-movieengine-builder-group-title"><span class="dashicons dashicons-calendar-alt"></span>Release Years Settings</div>' +
					'<div class="hello-movieengine-builder-row-grid">' +
						'<div class="hello-movieengine-builder-field"><label>Layout</label><select class="hello-movieengine-section-year-layout"><option value="pills" selected>Pills (Buttons)</option><option value="slider">Slider Carousel</option></select></div>' +
						'<div class="hello-movieengine-builder-field"><label>Years Count</label><input type="number" class="hello-movieengine-section-year-limit" value="10" min="4" max="30" /></div>' +
					'</div>' +
				'</div>' +

				'<!-- 8. Live Search -->' +
				'<div class="hello-movieengine-fields-live_search" style="display: none;">' +
					'<div class="hello-movieengine-builder-group-title"><span class="dashicons dashicons-search"></span>Live Search Bar Settings</div>' +
					'<div class="hello-movieengine-builder-field"><label>Search Input Placeholder</label><input type="text" class="hello-movieengine-section-search-placeholder" value="" placeholder="Search movies & TV series..." /></div>' +
					'<div class="hello-movieengine-builder-field hello-movieengine-builder-toggle-field"><label><input type="checkbox" class="hello-movieengine-section-search-icon" value="1" checked /> Show Search Icon</label></div>' +
				'</div>' +

				'<!-- 9. Shortcode -->' +
				'<div class="hello-movieengine-fields-shortcode" style="display: none;">' +
					'<div class="hello-movieengine-builder-field"><label>Custom Shortcode</label><textarea class="hello-movieengine-section-shortcode" rows="2" placeholder="[my_shortcode parameter=\'1\']"></textarea></div>' +
				'</div>' +

				'<!-- 10. Page Content -->' +
				'<div class="hello-movieengine-fields-page-content" style="display: none;">' +
					'<p class="description" style="margin: 4px 0 8px; font-size: 11.5px; color: #64748b;">This block will render any text, Gutenberg blocks, or Elementor content written inside your homepage editor.</p>' +
				'</div>' +
			'</div>' +
		'</div>';

		$container.append( tpl );
		var $newRow = $container.children( '.hello-movieengine-builder-row' ).last();
		updateRowQuerySourceUI( $newRow );
		initSectionBuilderSortable();
		syncSectionBuilder( $wrap );
	} );

	function initSectionBuilderQueryUI() {
		$( '.hello-movieengine-builder-row' ).each( function () {
			updateRowQuerySourceUI( $( this ) );
		} );
	}

	$( window ).on( 'load', function () {
		setTimeout( initNavRepeaterSortable, 400 );
		setTimeout( function () {
			initSectionBuilderSortable();
			initSectionBuilderQueryUI();
		}, 500 );
	} );

	if ( typeof wp !== 'undefined' && wp.customize && wp.customize.section ) {
		wp.customize.section.each( function ( section ) {
			section.expanded.bind( function ( isExpanded ) {
				if ( isExpanded ) {
					setTimeout( function () {
						initSectionBuilderSortable();
						initSectionBuilderQueryUI();
					}, 200 );
				}
			} );
		} );
	}

	/* ==========================================================
	   Movie Engine color preset → custom pickers visibility
	   ========================================================== */

	var meCustomColorSettingIds = [
		'movie_engine_design_settings[primary_color]',
		'movie_engine_design_settings[primary_hover_color]',
		'movie_engine_design_settings[text_color]',
		'movie_engine_design_settings[gray_color]',
		'movie_engine_design_settings[border_color]',
		'movie_engine_design_settings[background_color]',
		'movie_engine_design_settings[surface_color]'
	];

	function getMeColorPreset() {
		var preset = 'netflix';
		if ( typeof wp !== 'undefined' && wp.customize ) {
			var setting = wp.customize( 'movie_engine_design_settings[color_preset]' );
			if ( setting ) {
				preset = setting.get() || preset;
			}
		}
		var $input = $( '#customize-control-movie_engine_design_settings-color_preset .hello-movieengine-btn-group-value' );
		if ( $input.length ) {
			preset = $input.val() || preset;
		}
		return preset;
	}

	function toggleMeCustomColorControls() {
		var show = getMeColorPreset() === 'custom';
		var hasApi = typeof wp !== 'undefined' && wp.customize && wp.customize.control;

		meCustomColorSettingIds.forEach( function ( settingId ) {
			if ( hasApi ) {
				var control = wp.customize.control( settingId );
				if ( control && control.container ) {
					control.container.toggle( show );
					return;
				}
			}
		} );

		$( '#customize-theme-controls .hello-movieengine-me-custom-color' ).toggle( show );
	}

	$( document ).on( 'change', '#customize-control-movie_engine_design_settings-color_preset .hello-movieengine-btn-group-value', toggleMeCustomColorControls );

	if ( typeof wp !== 'undefined' && wp.customize ) {
		wp.customize.bind( 'ready', function () {
			var setting = wp.customize( 'movie_engine_design_settings[color_preset]' );
			if ( setting ) {
				setting.bind( toggleMeCustomColorControls );
			}
			toggleMeCustomColorControls();
		} );

		wp.customize.section( 'hello_movieengine_colors_section', function ( section ) {
			section.expanded.bind( function ( isExpanded ) {
				if ( isExpanded ) {
					setTimeout( toggleMeCustomColorControls, 50 );
				}
			} );
		} );
	}

} )( jQuery );


