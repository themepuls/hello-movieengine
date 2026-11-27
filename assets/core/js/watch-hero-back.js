/**
 * Style Two — predictable Back targets (archive / home; no /player/ loop).
 *
 * @package Hello_Movie Engine
 */
(function () {
	'use strict';

	var FLAG_FROM_PLAYER = 'hme_back_after_player';

	function sameOrigin(url) {
		try {
			return new URL(url, window.location.href).origin === window.location.origin;
		} catch (err) {
			return false;
		}
	}

	function isPlayerUrl(url) {
		try {
			return /\/player\/?(\?|#|$)/i.test(new URL(url, window.location.href).pathname);
		} catch (err) {
			return false;
		}
	}

	function goToHref(href) {
		if (href) {
			window.location.assign(href);
		}
	}

	function tryHistoryBackThen(fallbackHref) {
		if (window.history.length <= 1) {
			goToHref(fallbackHref);
			return;
		}
		var start = window.location.href;
		window.history.back();
		window.setTimeout(function () {
			if (window.location.href === start) {
				goToHref(fallbackHref);
			}
		}, 400);
	}

	function navigateBack(link) {
		var fallback =
			link.getAttribute('data-fallback-href') ||
			link.getAttribute('href') ||
			'/';
		var forceFallback = link.getAttribute('data-back-mode') === 'fallback';

		if (forceFallback) {
			try {
				sessionStorage.removeItem(FLAG_FROM_PLAYER);
			} catch (err) {
				/* ignore */
			}
			goToHref(fallback);
			return;
		}

		tryHistoryBackThen(fallback);
	}

	if (!isPlayerUrl(window.location.href) && isPlayerUrl(document.referrer)) {
		try {
			sessionStorage.setItem(FLAG_FROM_PLAYER, '1');
		} catch (err) {
			/* ignore */
		}
	}

	document.addEventListener('click', function (e) {
		var back = e.target.closest('.hme-watch-hero__back');
		if (back) {
			var scope = back.closest(
				'.hme-single-cinema, .movie-engine-movie-hero, .hello-me-browse__hero'
			);
			if (!scope) {
				return;
			}
			e.preventDefault();
			navigateBack(back);
			return;
		}

		var playerClose = e.target.closest('.movie-engine-player-page-close');
		if (!playerClose) {
			return;
		}
		e.preventDefault();
		var closeFallback =
			playerClose.getAttribute('data-fallback-href') ||
			playerClose.getAttribute('href') ||
			'/';
		try {
			sessionStorage.setItem(FLAG_FROM_PLAYER, '1');
		} catch (err) {
			/* ignore */
		}
		tryHistoryBackThen(closeFallback);
	});
})();