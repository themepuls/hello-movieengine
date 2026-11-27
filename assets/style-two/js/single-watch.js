/**
 * Style Two single movie — reveal full-width player on Watch Now.
 */
(function () {
	'use strict';

	function closePlayerStage() {
		var stage = document.getElementById('hme-watch-player-stage');
		var page = document.querySelector('.hme-single-cinema');
		if (!stage) {
			return;
		}
		stage.hidden = true;
		stage.classList.remove('is-open');
		if (page) {
			page.classList.remove('hme-single-cinema--player-open');
		}
		var root = playerRoot();
		if (root) {
			var video = root.querySelector('video');
			if (video && typeof video.pause === 'function') {
				video.pause();
			}
		}
	}

	function openPlayerStage() {
		var stage = document.getElementById('hme-watch-player-stage');
		var page = document.querySelector('.hme-single-cinema');
		if (!stage) {
			return;
		}
		stage.hidden = false;
		stage.classList.add('is-open');
		if (page) {
			page.classList.add('hme-single-cinema--player-open');
		}
	}

	function scrollToPlayer() {
		var target = document.getElementById('movie-engine-player');
		if (!target) {
			return;
		}
		var top = target.getBoundingClientRect().top + window.pageYOffset - 16;
		window.scrollTo({ top: Math.max(0, top), behavior: 'smooth' });
	}

	function playerRoot() {
		return document.getElementById('movie-engine-player');
	}

	function hasFakeOverlay() {
		return !!document.querySelector('.movie-engine-fake-player-overlay');
	}

	function hasLazyOverlay(root) {
		return !!(root && root.querySelector('.me-player-lazy-overlay'));
	}

	function requestDeferredSource(autoplay) {
		var root = playerRoot();
		if (!root) {
			return;
		}
		var container = root.querySelector('.movie-engine-player-container[data-post-id]');
		if (!container || !window.CMEPlayer || !window.CMEPlayer.bridge) {
			return;
		}
		var postId = container.getAttribute('data-post-id');
		if (!postId || typeof window.CMEPlayer.bridge.ensureInitialSource !== 'function') {
			return;
		}
		window.CMEPlayer.bridge.ensureInitialSource(postId, {
			force: true,
			autoplay: autoplay && !hasFakeOverlay()
		});
	}

	function tryPlay() {
		var root = playerRoot();
		if (!root || hasFakeOverlay() || hasLazyOverlay(root)) {
			return;
		}
		var video = root.querySelector('video');
		if (video && typeof video.play === 'function') {
			video.play().catch(function () {});
		}
		var bigPlay = root.querySelector('.cmeplayer__big-play, .plyr__control--overlaid, button[data-plyr="play"]');
		if (bigPlay) {
			bigPlay.click();
		}
	}

	function onPlayClick(e) {
		var trigger = e.target.closest('.hme-watch-hero__play, .me-inline-play-now');
		if (!trigger || !document.querySelector('.hme-single-cinema')) {
			return;
		}
		if (!document.getElementById('hme-watch-player-stage')) {
			return;
		}
		e.preventDefault();
		openPlayerStage();
		requestDeferredSource(true);
		window.setTimeout(function () {
			scrollToPlayer();
			if (!hasFakeOverlay()) {
				tryPlay();
			}
		}, 80);
	}

	document.addEventListener('click', onPlayClick);

	document.addEventListener('click', function (e) {
		var back = e.target.closest('#hme-watch-player-back');
		if (!back || !document.querySelector('.hme-single-cinema')) {
			return;
		}
		e.preventDefault();
		closePlayerStage();
		var hero = document.getElementById('hme-watch-hero');
		if (hero) {
			var top = hero.getBoundingClientRect().top + window.pageYOffset - 12;
			window.scrollTo({ top: Math.max(0, top), behavior: 'smooth' });
		}
	});

	if (window.location.hash === '#movie-engine-player') {
		openPlayerStage();
	}
})();