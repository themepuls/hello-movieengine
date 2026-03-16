/**
 * Hello Movie Engine – Navigation & header behaviour.
 *
 * Handles:
 * - Mobile menu toggle
 * - Transparent header scroll → sticky with blur
 * - Inline expanding search
 * - Dropdown keyboard/focus handling
 *
 * @package Hello_Movie Engine
 */
( function () {
	'use strict';

	var siteBody = document.body;
	var header   = document.getElementById( 'masthead' );

	/* -------------------------------------------------------
	   Mobile menu toggle
	   ------------------------------------------------------- */
	var mobileToggle = document.querySelector( '.hello-movieengine-header__mobile-toggle' );
	var mobileNav    = document.querySelector( '.hello-movieengine-header__nav' );
	var savedScrollY = 0;

	function openMobileNav() {
		savedScrollY = window.scrollY;
		siteBody.style.top = '-' + savedScrollY + 'px';
		siteBody.classList.add( 'hello-movieengine-mobile-nav-open' );
		if ( mobileToggle ) {
			mobileToggle.setAttribute( 'aria-expanded', 'true' );
		}
		setTimeout( function () {
			var closeBtn = mobileNav && mobileNav.querySelector( '.hello-movieengine-mobile-nav-close' );
			var firstLink = mobileNav && mobileNav.querySelector( 'a' );
			if ( closeBtn ) {
				closeBtn.focus();
			} else if ( firstLink ) {
				firstLink.focus();
			}
		}, 100 );
	}

	function closeMobileNav() {
		siteBody.classList.remove( 'hello-movieengine-mobile-nav-open' );
		siteBody.style.top = '';
		window.scrollTo( 0, savedScrollY );
		if ( mobileToggle ) {
			mobileToggle.setAttribute( 'aria-expanded', 'false' );
			mobileToggle.focus();
		}
	}

	/* Trap Tab key inside mobile menu when open */
	function handleMobileNavFocusTrap( e ) {
		if ( e.key !== 'Tab' ) return;
		if ( ! siteBody.classList.contains( 'hello-movieengine-mobile-nav-open' ) ) return;
		if ( ! mobileNav ) return;

		var focusable = mobileNav.querySelectorAll(
			'a[href], button:not([disabled]), input:not([disabled]), textarea:not([disabled]), select:not([disabled]), [tabindex]:not([tabindex="-1"])'
		);
		if ( ! focusable.length ) return;

		var first = focusable[0];
		var last  = focusable[ focusable.length - 1 ];

		if ( e.shiftKey ) {
			if ( document.activeElement === first ) {
				e.preventDefault();
				last.focus();
			}
		} else {
			if ( document.activeElement === last ) {
				e.preventDefault();
				first.focus();
			}
		}
	}

	document.addEventListener( 'keydown', handleMobileNavFocusTrap );

	if ( mobileToggle ) {
		mobileToggle.addEventListener( 'click', function () {
			var isOpen = siteBody.classList.contains( 'hello-movieengine-mobile-nav-open' );
			if ( isOpen ) {
				closeMobileNav();
			} else {
				openMobileNav();
				if ( window.matchMedia && window.matchMedia( '(max-width: 1024px)' ).matches ) {
					setupMobileSubmenuToggles();
				}
			}
		} );
	}

	/* Click overlay to close */
	document.addEventListener( 'click', function ( e ) {
		if ( ! siteBody.classList.contains( 'hello-movieengine-mobile-nav-open' ) ) return;
		if ( mobileNav && mobileNav.contains( e.target ) ) return;
		if ( mobileToggle && mobileToggle.contains( e.target ) ) return;
		closeMobileNav();
	} );

	function ensureMobileCloseButton() {
		if ( ! mobileNav ) return;
		if ( ! window.matchMedia || ! window.matchMedia( '(max-width: 1024px)' ).matches ) return;
		if ( mobileNav.querySelector( '.hello-movieengine-mobile-nav-close' ) ) return;

		var closeBtn = document.createElement( 'button' );
		closeBtn.type = 'button';
		closeBtn.className = 'hello-movieengine-mobile-nav-close';
		closeBtn.setAttribute( 'aria-label', 'Close menu' );
		closeBtn.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>';
		closeBtn.addEventListener( 'click', function () {
			closeMobileNav();
		} );
		mobileNav.insertBefore( closeBtn, mobileNav.firstChild );
	}

	/* Submenu toggles (mobile) */
	function setupMobileSubmenuToggles() {
		if ( ! mobileNav ) return;
		if ( ! window.matchMedia || ! window.matchMedia( '(max-width: 1024px)' ).matches ) return;

		var parentItems = mobileNav.querySelectorAll( '.hello-movieengine-nav-menu li.menu-item-has-children, .hello-movieengine-nav-menu li.page_item_has_children' );

		parentItems.forEach( function ( li ) {
			/* Avoid duplicating toggle */
			if ( li.querySelector( '.hello-movieengine-submenu-toggle' ) ) return;

			var submenu = li.querySelector( ':scope > ul' );
			var link    = li.querySelector( ':scope > a' );
			if ( ! submenu || ! link ) return;

			var btn = document.createElement( 'button' );
			btn.type = 'button';
			btn.className = 'hello-movieengine-submenu-toggle';
			btn.setAttribute( 'aria-expanded', 'false' );
			btn.setAttribute( 'aria-label', 'Toggle submenu' );
			btn.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><polyline points="6 9 12 15 18 9"></polyline></svg>';

			li.insertBefore( btn, submenu );
		} );
	}

	/* Single delegated handler so submenu toggles work reliably (e.g. after re-inject) */
	function initSubmenuToggleDelegation() {
		if ( ! mobileNav ) return;
		mobileNav.addEventListener( 'click', handleSubmenuToggleClick );
	}

	function handleSubmenuToggleClick( e ) {
		var btn = e.target.closest( '.hello-movieengine-submenu-toggle' );
		if ( ! btn ) return;
		e.preventDefault();
		e.stopPropagation();
		var li = btn.closest( 'li' );
		if ( ! li ) return;
		var isOpen = li.classList.contains( 'is-submenu-open' );
		li.classList.toggle( 'is-submenu-open', ! isOpen );
		btn.setAttribute( 'aria-expanded', String( ! isOpen ) );
	}

	function teardownMobileSubmenuToggles() {
		if ( ! mobileNav ) return;
		mobileNav.querySelectorAll( '.hello-movieengine-mobile-nav-close' ).forEach( function ( btn ) {
			btn.remove();
		} );
		mobileNav.querySelectorAll( '.hello-movieengine-submenu-toggle' ).forEach( function ( btn ) {
			btn.remove();
		} );
		mobileNav.querySelectorAll( '.hello-movieengine-nav-menu li.is-submenu-open' ).forEach( function ( li ) {
			li.classList.remove( 'is-submenu-open' );
		} );
	}

	function syncMobileMenuEnhancements() {
		var isMobile = window.matchMedia && window.matchMedia( '(max-width: 1024px)' ).matches;
		if ( isMobile ) {
			ensureMobileCloseButton();
			setupMobileSubmenuToggles();
		} else {
			teardownMobileSubmenuToggles();
			closeMobileNav();
		}
	}

	syncMobileMenuEnhancements();
	initSubmenuToggleDelegation();
	window.addEventListener( 'resize', function () {
		syncMobileMenuEnhancements();
	} );

	/* -------------------------------------------------------
	   Transparent header: scroll behaviour
	   ------------------------------------------------------- */
	if ( header && header.classList.contains( 'hello-movieengine-header--transparent' ) ) {
		var scrollThreshold = 50;

		function onScroll() {
			if ( window.scrollY > scrollThreshold ) {
				header.classList.add( 'is-scrolled' );
			} else {
				header.classList.remove( 'is-scrolled' );
			}
		}

		window.addEventListener( 'scroll', onScroll, { passive: true } );
		onScroll();
	}

	/* -------------------------------------------------------
	   Inline expanding search
	   ------------------------------------------------------- */
	var searchWrapper = document.querySelector( '.hello-movieengine-header__search' );
	var searchToggle  = document.querySelector( '.hello-movieengine-header__search-toggle' );
	var searchClose   = document.querySelector( '.hello-movieengine-header__search-close' );
	var searchBox     = document.querySelector( '.hello-movieengine-header__search-box' );

	function openSearch() {
		if ( ! searchWrapper ) return;
		searchWrapper.classList.add( 'is-open' );
		if ( searchToggle ) searchToggle.setAttribute( 'aria-expanded', 'true' );

		var input = searchBox.querySelector( 'input[type="text"], input[type="search"], .movie-engine-ls-input' );
		if ( input ) {
			setTimeout( function () { input.focus(); }, 200 );
		}
	}

	function closeSearch() {
		if ( ! searchWrapper ) return;
		searchWrapper.classList.remove( 'is-open' );
		if ( searchToggle ) searchToggle.setAttribute( 'aria-expanded', 'false' );
	}

	if ( searchToggle ) {
		searchToggle.addEventListener( 'click', function ( e ) {
			e.preventDefault();
			openSearch();
		} );
	}

	if ( searchClose ) {
		searchClose.addEventListener( 'click', function ( e ) {
			e.preventDefault();
			closeSearch();
		} );
	}

	/* Close search when clicking outside */
	document.addEventListener( 'click', function ( e ) {
		if ( searchWrapper && searchWrapper.classList.contains( 'is-open' ) ) {
			if ( ! searchWrapper.contains( e.target ) ) {
				closeSearch();
			}
		}
	} );

	/* -------------------------------------------------------
	   Escape key: close search & mobile menu
	   ------------------------------------------------------- */
	document.addEventListener( 'keydown', function ( e ) {
		if ( e.key === 'Escape' ) {
			closeSearch();

			if ( siteBody.classList.contains( 'hello-movieengine-mobile-nav-open' ) ) {
				closeMobileNav();
			}
		}
	} );

	/* -------------------------------------------------------
	   Make Movie Engine user-trigger keyboard accessible
	   (plugin outputs a plain <div>, not a <button> or <a>)
	   ------------------------------------------------------- */
	var userTrigger = document.querySelector( '.movie-engine-user-trigger' );
	if ( userTrigger && ! userTrigger.hasAttribute( 'tabindex' ) ) {
		userTrigger.setAttribute( 'tabindex', '0' );
		userTrigger.setAttribute( 'role', 'button' );
		userTrigger.setAttribute( 'aria-label', 'User menu' );
		userTrigger.addEventListener( 'keydown', function ( e ) {
			if ( e.key === 'Enter' || e.key === ' ' ) {
				e.preventDefault();
				userTrigger.click();
			}
		} );
	}

	/* -------------------------------------------------------
	   Dropdown keyboard/focus accessibility
	   Uses focusin/focusout (which bubble) so parent li
	   elements keep .focus while any descendant has focus.
	   ------------------------------------------------------- */
	var navMenu = document.querySelector( '.hello-movieengine-nav-menu' );

	if ( navMenu ) {
		navMenu.addEventListener( 'focusin', function ( e ) {
			var li = e.target.closest( 'li' );
			while ( li && navMenu.contains( li ) ) {
				li.classList.add( 'focus' );
				li = li.parentElement ? li.parentElement.closest( 'li' ) : null;
			}
		} );

		navMenu.addEventListener( 'focusout', function () {
			setTimeout( function () {
				var active = document.activeElement;
				navMenu.querySelectorAll( 'li.focus' ).forEach( function ( li ) {
					if ( ! li.contains( active ) ) {
						li.classList.remove( 'focus' );
					}
				} );
			}, 0 );
		} );
	}
} )();
