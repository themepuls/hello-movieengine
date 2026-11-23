/**
 * Single studio archive: All / Movies / TV Series tabs (no search).
 *
 * @package Hello_Movie Engine
 */
( function ( $ ) {
	'use strict';

	if ( typeof movieEngineData === 'undefined' ) {
		return;
	}

	function getStudioSlug( $container, $form ) {
		var slug = $form.find( '[name="me_studio"]' ).val();
		if ( slug ) {
			return slug;
		}
		return $container.attr( 'data-me_studio' ) || '';
	}

	function ensureGridVisible( $container ) {
		$container.find( '.movie-engine-section, .movie-engine-items-grid' ).addClass( 'hme-in' );
	}

	function applyGridHtml( $grid, html ) {
		var $wrap = $( '<div>' ).html( html || '' );
		var $cards = $wrap.find( '.movie-engine-movie-card, .movie-engine-card, .movie-engine-movie-grid > *' );

		if ( $cards.length ) {
			$grid.empty().append( $cards );
			return;
		}

		var $empty = $wrap.find( '.movie-engine-empty-state, .movie-engine-no-results' );
		if ( $empty.length ) {
			$grid.html( '<div class="hello-me-studio-grid-empty">' + $empty.first().prop( 'outerHTML' ) + '</div>' );
			return;
		}

		$grid.html( html );
	}

	function loadStudioGrid( $form, page ) {
		var $container  = $form.closest( '.movie-engine-ajax-container' );
		var $grid       = $container.find( '.movie-engine-items-grid' );
		var $pagination = $container.find( '.movie-engine-pagination' );
		var postType    = $form.find( '[name="me_post_type"]' ).val() || '';
		var studio      = getStudioSlug( $container, $form );

		if ( ! $grid.length ) {
			return;
		}

		$grid.css( 'opacity', '0.55' );

		$.ajax( {
			url: movieEngineData.ajax_url,
			type: 'GET',
			data: {
				action: 'movie_engine_filter_movies',
				nonce: movieEngineData.nonce,
				me_studio: studio,
				me_post_type: postType,
				post_type: postType,
				orderby: 'date',
				paged: page || 1,
			},
			success: function ( response ) {
				if ( ! response || ! response.success ) {
					$grid.css( 'opacity', '1' );
					return;
				}

				applyGridHtml( $grid, response.data.html );
				$grid.css( 'opacity', '1' );
				ensureGridVisible( $container );

				if ( response.data.pagination && response.data.found_posts > 0 ) {
					if ( $pagination.length ) {
						$pagination.replaceWith( response.data.pagination );
					} else {
						$grid.after( response.data.pagination );
					}
				} else if ( $pagination.length ) {
					$pagination.remove();
				}

				var url = new URL( window.location.href );
				if ( postType ) {
					url.searchParams.set( 'me_post_type', postType );
				} else {
					url.searchParams.delete( 'me_post_type' );
				}
				url.searchParams.delete( 'paged' );
				window.history.replaceState( {}, '', url.toString() );
			},
			error: function () {
				$grid.css( 'opacity', '1' );
			},
		} );
	}

	function syncTabsFromUrl( $bar ) {
		var params = new URLSearchParams( window.location.search );
		var type   = params.get( 'me_post_type' ) || '';
		var $form  = $bar.find( '.movie-engine-filter-form' );

		$form.find( '[name="me_post_type"]' ).val( type );
		$bar.find( '.hello-me-studio-type-tab' ).removeClass( 'is-active' );
		$bar.find( '.hello-me-studio-type-tab' ).each( function () {
			var tabType = $( this ).data( 'post-type' ) || '';
			if ( tabType === type ) {
				$( this ).addClass( 'is-active' );
			}
		} );

		if ( ! $bar.find( '.hello-me-studio-type-tab.is-active' ).length ) {
			$bar.find( '.hello-me-studio-type-tab' ).first().addClass( 'is-active' );
		}
	}

	$( document ).on( 'click', '.hello-me-studio-type-tab', function ( e ) {
		e.preventDefault();
		var $tab  = $( this );
		var $bar  = $tab.closest( '.hello-me-studio-type-bar' );
		var $form = $bar.find( '.movie-engine-filter-form' );
		var val   = $tab.data( 'post-type' ) || '';

		$form.find( '[name="me_post_type"]' ).val( val );
		$tab.siblings( '.hello-me-studio-type-tab' ).removeClass( 'is-active' );
		$tab.addClass( 'is-active' );

		loadStudioGrid( $form, 1 );
	} );

	function initStudioTaxArchive() {
		var $archive = $( '.hello-me-studio-tax-archive' );
		if ( ! $archive.length ) {
			return;
		}

		ensureGridVisible( $archive );
		$archive.find( '.hello-me-studio-type-bar' ).each( function () {
			syncTabsFromUrl( $( this ) );
		} );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', initStudioTaxArchive );
	} else {
		initStudioTaxArchive();
	}

	window.addEventListener( 'load', initStudioTaxArchive );
}( jQuery ) );