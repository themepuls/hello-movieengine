/**
 * Style Two global side rail (search trigger, fullscreen hide).
 *
 * @package Hello_Movie Engine
 * @since 1.2.0
 */

( function ( $ ) {
	'use strict';

	$( function () {
		var $rail = $( '#hello-movieengine-side-rail' );
		if ( ! $rail.length ) {
			return;
		}

		var $body = $( 'body' );
		var touchExpanded = false;

		$rail.on( 'mouseenter', function () {
			$body.addClass( 'hello-side-rail-hover' );
		} );
		$rail.on( 'mouseleave', function () {
			if ( ! touchExpanded ) {
				$body.removeClass( 'hello-side-rail-hover' );
			}
		} );

		$rail.on( 'click', function ( e ) {
			if ( window.matchMedia( '(hover: hover)' ).matches ) {
				return;
			}
			if ( ! $( e.target ).closest( '.hello-movieengine-side-rail__it' ).length ) {
				e.preventDefault();
				touchExpanded = ! touchExpanded;
				$rail.toggleClass( 'is-expanded', touchExpanded );
				$body.toggleClass( 'hello-side-rail-hover', touchExpanded );
			}
		} );

		$rail.on( 'click', '.hello-movieengine-side-rail__btn--search, [data-type="search"]', function ( e ) {
			e.preventDefault();

			var $themeSearchToggle = $( '.hello-movieengine-header__search-toggle, .me-search-toggle, .movie-engine-search-toggle, [data-bs-target="#me-search-modal"], [data-target="#me-search-modal"]' ).first();
			if ( $themeSearchToggle.length ) {
				$themeSearchToggle.trigger( 'click' );
				return;
			}

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

			var $searchInput = $( '.me-ls-input, .movie-engine-search-field, input[type="search"]' ).first();
			if ( $searchInput.length && $searchInput.is( ':visible' ) ) {
				$searchInput.trigger( 'focus' );
				return;
			}

			window.location.href = $( this ).data( 'url' ) || '/?s=';
		} );

		function checkFullscreen() {
			var isFullscreen = !! (
				document.fullscreenElement ||
				document.webkitFullscreenElement ||
				document.mozFullScreenElement ||
				document.msFullscreenElement
			);

			$( 'body' ).toggleClass( 'is-fullscreen', isFullscreen );
			$rail.css( 'visibility', isFullscreen ? 'hidden' : '' );
		}

		$( document ).on(
			'fullscreenchange webkitfullscreenchange mozfullscreenchange MSFullscreenChange',
			checkFullscreen
		);

		function fitHero() {
			if ( ! window.matchMedia( '(min-width: 768px)' ).matches ) {
				return;
			}

			var heroes = document.querySelectorAll(
				'.movie-engine-hero-section, .movie-engine-hero, .movie-engine-movie-hero, .me-hero'
			);

			heroes.forEach( function ( hero ) {
				hero.style.setProperty( 'margin-left', '0px', 'important' );
				hero.style.setProperty( 'width', document.documentElement.clientWidth + 'px', 'important' );
				hero.style.setProperty(
					'margin-left',
					( -hero.getBoundingClientRect().left ) + 'px',
					'important'
				);
			} );
		}

		fitHero();
		window.addEventListener( 'resize', fitHero );

		var flyHideTimer = null;

		function positionFly( $grp ) {
			var $fly = $grp.children( '.hello-movieengine-side-rail__fly' );
			var item = $grp.children( '.hello-movieengine-side-rail__it' ).get( 0 );
			var grp  = $grp.get( 0 );
			if ( ! $fly.length || ! item || ! grp ) {
				return;
			}

			$fly.css( { top: '', left: '', maxHeight: '', transform: '' } );

			var isMega     = $fly.hasClass( 'hello-movieengine-side-rail__fly--mega' );
			var isMegaRich = $fly.hasClass( 'hello-movieengine-side-rail__fly--mega-rich' );
			var itemRect   = item.getBoundingClientRect();
			var grpRect    = grp.getBoundingClientRect();
			var pad        = 16;
			var vh         = window.innerHeight;
			var maxH       = vh - pad * 2;
			var flyH;
			var absTop;

			if ( isMegaRich ) {
				$fly.css( {
					left: '',
					transform: 'none',
					'max-height': maxH + 'px',
					overflow: 'hidden'
				} );
				$fly.children( '.hello-movieengine-mega-panel' ).css( {
					'max-height': maxH + 'px',
					'overflow-x': 'hidden',
					'overflow-y': 'auto'
				} );
				void $fly[ 0 ].offsetHeight;
				flyH   = Math.min( maxH, $fly[ 0 ].offsetHeight );
				absTop = itemRect.top + ( itemRect.height / 2 ) - ( flyH / 2 );
				if ( absTop < pad ) {
					absTop = pad;
				}
				if ( absTop + flyH > vh - pad ) {
					absTop = Math.max( pad, vh - pad - flyH );
				}
				$fly.css( 'top', ( absTop - grpRect.top ) + 'px' );
				return;
			}

			flyH = $fly.outerHeight();

			if ( isMega ) {
				absTop = itemRect.top + ( itemRect.height / 2 ) - ( flyH / 2 );
			} else {
				absTop = itemRect.top;
			}

			if ( flyH > maxH ) {
				$fly.css( 'max-height', maxH + 'px' );
				flyH   = maxH;
				absTop = pad;
			} else {
				if ( absTop < pad ) {
					absTop = pad;
				}
				if ( absTop + flyH > vh - pad ) {
					absTop = vh - pad - flyH;
				}
			}

			$fly.css( {
				top: ( absTop - grpRect.top ) + 'px',
				transform: 'none'
			} );
		}

		function openFly( $grp ) {
			if ( flyHideTimer ) {
				window.clearTimeout( flyHideTimer );
				flyHideTimer = null;
			}

			$rail.find( '.hello-movieengine-side-rail__grp.is-fly-open' ).not( $grp ).each( function () {
				$( this ).removeClass( 'is-fly-open' )
					.children( '.hello-movieengine-side-rail__fly' )
					.css( { top: '', left: '', maxHeight: '', transform: '' } );
			} );

			$grp.addClass( 'is-fly-open' );
			$rail.addClass( 'is-fly-open' );
			$grp.children( '.hello-movieengine-side-rail__fly' ).removeAttr( 'hidden' );
			positionFly( $grp );

			$grp.children( '.hello-movieengine-side-rail__fly' ).find( 'img' ).one( 'load', function () {
				positionFly( $grp );
			} );
		}

		function scheduleCloseFly( $grp ) {
			if ( flyHideTimer ) {
				window.clearTimeout( flyHideTimer );
			}

			flyHideTimer = window.setTimeout( function () {
				$grp.removeClass( 'is-fly-open' );
				$grp.children( '.hello-movieengine-side-rail__fly' )
					.attr( 'hidden', '' )
					.css( {
						top: '',
						left: '',
						maxHeight: '',
						transform: ''
					} );

				if ( ! $rail.find( '.hello-movieengine-side-rail__grp.is-fly-open' ).length ) {
					$rail.removeClass( 'is-fly-open' );
				}

				flyHideTimer = null;
			}, 280 );
		}

		$rail.on( 'mouseenter', '.hello-movieengine-side-rail__grp.has-flyout', function () {
			openFly( $( this ) );
		} );

		$rail.on( 'mouseleave', '.hello-movieengine-side-rail__grp.has-flyout', function ( e ) {
			var $grp  = $( this );
			var $fly  = $grp.children( '.hello-movieengine-side-rail__fly' );
			var flyEl = $fly.get( 0 );
			var next  = e.relatedTarget;

			if ( flyEl && next && ( flyEl === next || $.contains( flyEl, next ) ) ) {
				return;
			}

			scheduleCloseFly( $grp );
		} );

		$rail.on( 'mouseenter', '.hello-movieengine-side-rail__fly', function () {
			var $grp = $( this ).closest( '.hello-movieengine-side-rail__grp' );
			if ( ! $grp.length || ! $grp.hasClass( 'is-fly-open' ) ) {
				return;
			}
			if ( flyHideTimer ) {
				window.clearTimeout( flyHideTimer );
				flyHideTimer = null;
			}
			$rail.addClass( 'is-fly-open' );
		} );

		$rail.on( 'mouseleave', '.hello-movieengine-side-rail__fly', function () {
			var $grp = $( this ).closest( '.hello-movieengine-side-rail__grp' );
			if ( $grp.length ) {
				scheduleCloseFly( $grp );
			}
		} );

		// Submenu / mega flyouts (touch: tap parent toggles panel).
		$rail.on( 'click', '.hello-movieengine-side-rail__it.has-children', function ( e ) {
			if ( window.matchMedia( '(hover: hover)' ).matches ) {
				return;
			}
			var $grp = $( this ).closest( '.hello-movieengine-side-rail__grp' );
			if ( ! $grp.length ) {
				return;
			}
			e.preventDefault();
			$rail.find( '.hello-movieengine-side-rail__grp.is-fly-open' ).not( $grp ).removeClass( 'is-fly-open' );
			$grp.toggleClass( 'is-fly-open' );
			var expanded = $grp.hasClass( 'is-fly-open' );
			$( this ).attr( 'aria-expanded', expanded ? 'true' : 'false' );
			if ( expanded ) {
				positionFly( $grp );
			}
		} );
	} );
} )( jQuery );