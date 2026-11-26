/**
 * Style Two motion: rows fade in as they scroll into view.
 *
 * @package Hello_Movie Engine
 * @since 1.2.0
 */
( function () {
	'use strict';

	var SELECTOR = '.hme-motion-scope, .hme-motion-section, .movie-engine-section:not(.movie-engine-hero-section), .movie-engine-taxonomies-wrapper, .movie-engine-library-container, .movie-engine-pricing-table-container, .movie-engine-dashboard-wrapper';
	var config = window.helloMovieEngineMotionConfig || { enabled: true, scrollReveal: true };

	function motionFlagOff( val ) {
		return val === false || val === 0 || val === '0' || val === '';
	}

	function motionFlagOn( val ) {
		return ! motionFlagOff( val );
	}

	function reduceMotion() {
		return window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
	}

	function isStyleTwoPack() {
		var root = document.documentElement;
		var body = document.body;
		return (
			( root && root.classList.contains( 'hmestyle-style-two' ) ) ||
			( body && body.classList.contains( 'hello-movieengine-style-two' ) )
		);
	}

	function mark( el ) {
		if ( el.classList.contains( 'hme-in' ) ) {
			return;
		}
		el.classList.add( 'hme-reveal' );
	}

	function reveal( el ) {
		/* Reference parity: only the observed target reveals. Nested sections
		   have their own observer and fade independently as they scroll in. */
		el.classList.add( 'hme-in' );
	}

	function isInViewport( el ) {
		/* Elements in a closed panel (tabs, accordions) have no layout box.
		   Leave them pending so the fade plays when the panel opens. */
		if ( ! el.getClientRects().length ) {
			return false;
		}

		var rect = el.getBoundingClientRect();
		var vh = window.innerHeight || document.documentElement.clientHeight;

		/* Reference parity: the safety net reveals on the same line as the
		   observer (8% up from the bottom edge), never earlier — so a reveal
		   triggered here still plays on screen instead of before it is seen.
		   Elements fully above the viewport stay pending; the observer fades
		   them in when scrolled back to. */
		return rect.top < vh * 0.92 && rect.bottom > 0;
	}

	function revealVisibleRows( nodes, io ) {
		var i;
		for ( i = 0; i < nodes.length; i++ ) {
			if ( ! nodes[ i ].classList.contains( 'hme-reveal' ) || nodes[ i ].classList.contains( 'hme-in' ) ) {
				continue;
			}
			if ( ! isInViewport( nodes[ i ] ) ) {
				continue;
			}
			reveal( nodes[ i ] );
			if ( io ) {
				io.unobserve( nodes[ i ] );
			}
		}
	}

	/**
	 * Safety net: never leave content permanently hidden.
	 * Reveals any pending .hme-reveal node once it is close to the viewport,
	 * even if the IntersectionObserver missed it (dynamic content, AJAX, etc.).
	 */
	function flushPendingReveals() {
		var pending = document.querySelectorAll( '.hme-reveal:not(.hme-in)' );
		var i;

		for ( i = 0; i < pending.length; i++ ) {
			if ( isInViewport( pending[ i ] ) ) {
				reveal( pending[ i ] );
			}
		}
	}
	function bind( root ) {
		var nodes = ( root || document ).querySelectorAll( SELECTOR );
		var tracked = [];
		var i;

		if ( ! motionFlagOn( config.scrollReveal ) || reduceMotion() || ! ( 'IntersectionObserver' in window ) ) {
			for ( i = 0; i < nodes.length; i++ ) {
				reveal( nodes[ i ] );
			}
			return;
		}

		var io = new IntersectionObserver( function ( entries ) {
			entries.forEach( function ( entry ) {
				if ( ! entry.isIntersecting ) {
					return;
				}
				reveal( entry.target );
				io.unobserve( entry.target );
			} );
		}, {
			root: null,
			/* Reference parity: reveal starts once the target is 8% into the viewport,
			   so the fade is always visible instead of finishing before it is seen. */
			rootMargin: '0px 0px -8% 0px',
			threshold: 0.04
		} );

		var aboveFoldIndex = 0;
		var vhBind = window.innerHeight || document.documentElement.clientHeight;

		for ( i = 0; i < nodes.length; i++ ) {
			if ( nodes[ i ].closest( '.movie-engine-hero-section' ) ) {
				continue;
			}
			/* Rows already on screen: stagger in on load so enabling motion is obvious. */
			if (
				nodes[ i ].getClientRects().length &&
				nodes[ i ].getBoundingClientRect().top < vhBind &&
				nodes[ i ].getBoundingClientRect().bottom > 0
			) {
				if ( reduceMotion() ) {
					reveal( nodes[ i ] );
				} else {
					mark( nodes[ i ] );
					( function ( el, idx ) {
						window.setTimeout( function () {
							reveal( el );
						}, Math.min( idx * 85, 510 ) );
					} )( nodes[ i ], aboveFoldIndex );
					aboveFoldIndex++;
				}
				continue;
			}
			/* A library inside an outer reveal target fades with its parent — do not nest transforms. */
			if (
				nodes[ i ].classList.contains( 'movie-engine-library-container' ) &&
				nodes[ i ].parentElement &&
				nodes[ i ].parentElement.closest( '.movie-engine-section, .movie-engine-taxonomies-wrapper, .movie-engine-pricing-table-container, .movie-engine-dashboard-wrapper, .movie-engine-library-container' )
			) {
				continue;
			}
			/* Already revealed — never hide it again */
			if ( nodes[ i ].classList.contains( 'hme-in' ) ) {
				continue;
			}
			mark( nodes[ i ] );
			io.observe( nodes[ i ] );
			tracked.push( nodes[ i ] );
		}

		revealVisibleRows( tracked, io );
		requestAnimationFrame( function () {
			revealVisibleRows( tracked, io );
		} );
		window.addEventListener( 'load', function () {
			revealVisibleRows( tracked, io );
		}, { once: true } );
		setTimeout( function () {
			revealVisibleRows( tracked, io );
		}, 180 );
	}

	function enhanceFilterDrawer() {
		/* Browse filter drawer UI + styles live in Style Two (archive-browse.css). */
		if ( ! isStyleTwoPack() ) {
			return;
		}

		var containers = document.querySelectorAll( '.movie-engine-library-container.me-filter-sidebar' );
		var i;

		for ( i = 0; i < containers.length; i++ ) {
			( function ( container ) {
				var bar;
				var header;
				var toggle;
				var close;
				var backdrop;

				if ( container.getAttribute( 'data-hme-filters' ) ) {
					return;
				}
				container.setAttribute( 'data-hme-filters', '1' );

				bar = container.querySelector( '.me-library-top-bar .me-top-bar-left' );
				if ( bar && ! container.querySelector( '.hme-filters-toggle' ) ) {
					toggle = document.createElement( 'button' );
					toggle.type = 'button';
					toggle.className = 'hme-filters-toggle';
					toggle.setAttribute( 'aria-expanded', 'false' );
					toggle.innerHTML = '<span class="fa-solid fa-sliders" aria-hidden="true"></span><span>' + ( toggle.getAttribute( 'data-label' ) || 'Filters' ) + '</span>';
					bar.insertBefore( toggle, bar.firstChild );
				}

				header = container.querySelector( '.me-filter-sidebar-header' );
				if ( header && ! header.querySelector( '.hme-filters-close' ) ) {
					close = document.createElement( 'button' );
					close.type = 'button';
					close.className = 'hme-filters-close';
					close.setAttribute( 'aria-label', 'Close filters' );
					close.innerHTML = '<span class="fa-solid fa-xmark" aria-hidden="true"></span>';
					header.appendChild( close );
				}

				if ( ! container.querySelector( '.hme-filters-backdrop' ) ) {
					backdrop = document.createElement( 'button' );
					backdrop.type = 'button';
					backdrop.className = 'hme-filters-backdrop';
					backdrop.setAttribute( 'aria-label', 'Close filters' );
					container.insertBefore( backdrop, container.firstChild );
				}

				container.addEventListener( 'click', function ( event ) {
					var open = container.classList.contains( 'hme-filters-open' );
					var t = event.target.closest( '.hme-filters-toggle, .hme-filters-close, .hme-filters-backdrop' );
					if ( ! t ) {
						return;
					}
					event.preventDefault();
					if ( t.classList.contains( 'hme-filters-toggle' ) ) {
						open = ! open;
					} else {
						open = false;
					}
					container.classList.toggle( 'hme-filters-open', open );
					toggle = container.querySelector( '.hme-filters-toggle' );
					if ( toggle ) {
						toggle.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
					}
				} );
			} )( containers[ i ] );
		}
	}

	function tagMotionSections( root ) {
		var scope = root || document;
		scope.querySelectorAll( '.hme-motion-scope .movie-engine-section:not(.movie-engine-hero-section)' ).forEach( function ( section ) {
			section.classList.add( 'hme-motion-section' );
		} );
	}

	function start() {
		if ( motionFlagOff( config.enabled ) || document.body.classList.contains( 'hello-movieengine-motion-off' ) ) {
			enhanceFilterDrawer();
			return;
		}

		tagMotionSections( document );
		bind( document );
		enhanceFilterDrawer();

		/* Public API — lets the plugin re-trigger motion after AJAX updates. */
		window.movieEngineMotion = {
			refresh: function ( root ) {
				var ctx = root || document;
				tagMotionSections( ctx );
				bind( ctx );
				flushPendingReveals();
			},
			flush: flushPendingReveals
		};

		/* Re-bind when new content is injected (AJAX filters, pagination, infinite scroll). */
		if ( window.jQuery ) {
			window.jQuery( document ).on( 'ajaxComplete', function () {
				bind( document );
				flushPendingReveals();
			} );
		}

		/* Safety net for anything the observer might have missed. */
		var ticking = false;
		window.addEventListener( 'scroll', function () {
			if ( ticking ) {
				return;
			}
			ticking = true;
			window.requestAnimationFrame( function () {
				flushPendingReveals();
				ticking = false;
			} );
		}, { passive: true } );

		window.addEventListener( 'resize', flushPendingReveals, { passive: true } );

		/*
		 * Panels revealed by a click (dashboard tabs, accordions, filter drawers)
		 * never scroll into view, so the observer cannot fire for them.
		 * Flush on the next frame and again shortly after, so a panel that is
		 * un-hidden by the click still fades in instead of staying hidden.
		 */
		document.addEventListener( 'click', function () {
			window.requestAnimationFrame( flushPendingReveals );
			window.setTimeout( flushPendingReveals, 120 );
		}, true );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', start );
	} else {
		start();
	}
} )();
