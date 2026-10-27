/**
 * Hello MovieEngine Mobile Bottom Navigation JS
 *
 * Handles search modal trigger, fullscreen video detection, and active interactions.
 *
 * @package Hello_Movie Engine
 * @since 1.0.8
 */

( function ( $ ) {
	'use strict';

	$( function () {
		var $bottomNav = $( '#hello-movieengine-bottom-nav' );
		if ( ! $bottomNav.length ) {
			return;
		}

		/* --------------------------------------------------------
		   Search Trigger Action
		   -------------------------------------------------------- */
		$bottomNav.on( 'click', '.hello-movieengine-bottom-nav__btn--search, [data-type="search"]', function ( e ) {
			e.preventDefault();

			// 1. Look for theme header search button toggle.
			var $themeSearchToggle = $( '.hello-movieengine-header__search-toggle, .me-search-toggle, .movie-engine-search-toggle, [data-bs-target="#me-search-modal"], [data-target="#me-search-modal"]' ).first();
			if ( $themeSearchToggle.length ) {
				$themeSearchToggle.trigger( 'click' );
				return;
			}

			// 2. Look for any search modal / overlay on page.
			var $modal = $( '#me-search-modal, .me-search-modal, .hello-movieengine-search-modal' ).first();
			if ( $modal.length ) {
				if ( typeof bootstrap !== 'undefined' && bootstrap.Modal ) {
					var bsModal = bootstrap.Modal.getInstance( $modal[0] ) || new bootstrap.Modal( $modal[0] );
					bsModal.show();
					return;
				}
				$modal.addClass( 'is-active show' ).fadeIn( 200 );
				$modal.find( 'input[type="search"], .me-ls-input' ).first().trigger( 'focus' );
				return;
			}

			// 3. Look for visible or hidden search input and focus it.
			var $searchInput = $( '.me-ls-input, .movie-engine-search-field, input[type="search"]' ).first();
			if ( $searchInput.length && $searchInput.is( ':visible' ) ) {
				$( 'html, body' ).animate( { scrollTop: 0 }, 250, function () {
					$searchInput.trigger( 'focus' );
				} );
				return;
			}

			// 4. Fallback: navigate to search page or home search query.
			window.location.href = $( this ).data( 'url' ) || '/?s=';
		} );

		/* --------------------------------------------------------
		   Fullscreen Detection (Video Player auto-hide)
		   -------------------------------------------------------- */
		function checkFullscreen() {
			var isFullscreen = !! (
				document.fullscreenElement ||
				document.webkitFullscreenElement ||
				document.mozFullScreenElement ||
				document.msFullscreenElement
			);

			$( 'body' ).toggleClass( 'is-fullscreen', isFullscreen );
		}

		$( document ).on(
			'fullscreenchange webkitfullscreenchange mozfullscreenchange MSFullscreenChange',
			checkFullscreen
		);
	} );

} )( jQuery );
