/* Moviestime New Look: sidebar, search, hero trailers and motion for the website (never the TV app). */
(function () {
    'use strict';
    if (window.__cmes) return;
    window.__cmes = true;
    var C = window.CMES || {};
    var doc = document;
    var EASE = 'cubic-bezier(.22,1,.36,1)';
  
    // ---------- icons (24px line icons) ----------
    var P = {
      home: '<path d="M3 10.5 12 3l9 7.5V20a1 1 0 0 1-1 1h-5v-6h-6v6H4a1 1 0 0 1-1-1z"/>',
      tvapp: '<path d="M12 3v11m0 0-4.5-4.5M12 14l4.5-4.5"/><path d="M5 17v2a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-2"/>',
      search: '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.6-3.6"/>',
      movies: '<rect x="3" y="4" width="18" height="16" rx="2"/><path d="M7 4v16M17 4v16M3 9h4M3 15h4M17 9h4M17 15h4"/>',
      series: '<rect x="2.5" y="5" width="19" height="13" rx="2"/><path d="M8 21h8M12 18v3"/>',
      anime: '<path d="M12 3a9 9 0 1 0 9 9"/><path d="M12 8a4 4 0 1 0 4 4"/><circle cx="18.5" cy="5.5" r="2"/>',
      genres: '<rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/>',
      list: '<path d="M6 3h12a1 1 0 0 1 1 1v17l-7-4-7 4V4a1 1 0 0 1 1-1z"/>',
      user: '<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>',
      mute: '<path d="M11 5 6 9H3v6h3l5 4z"/><path d="m22 9-6 6M16 9l6 6"/>',
      sound: '<path d="M11 5 6 9H3v6h3l5 4z"/><path d="M15.5 8.5a5 5 0 0 1 0 7M18.5 5.5a9 9 0 0 1 0 13"/>',
      close: '<path d="M6 6l12 12M18 6 6 18"/>'
    };
    function icon(n) {
      return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' + P[n] + '</svg>';
    }
    function esc(s) { return String(s).replace(/[&<>"]/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]; }); }
  
    // ---------- sidebar ----------
    function subOf(re) {
      var li = [].filter.call(doc.querySelectorAll('#site-navigation li'), function (l) {
        var a = l.querySelector('a'); return a && re.test(a.textContent.trim());
      })[0];
      if (!li) return [];
      var seen = {};
      return [].map.call(li.querySelectorAll('ul a[href]'), function (a) {
        return { label: a.textContent.trim(), href: a.href };
      }).filter(function (x) { if (seen[x.href]) return false; seen[x.href] = 1; return true; });
    }
    function current() {
      var p = location.pathname, q = location.search;
      if (p === '/' || p === '') return 'home';
      if (/movie_engine_view=watchlist/.test(q)) return 'list';
      if (/movie-engine-dashboard|dashboard|login/.test(p)) return 'user';
      // anime titles and episodes, and anime-filtered lists, belong to Anime
      if (doc.body.classList.contains('me-is-anime') || (/me_genre[^&]*=animation/.test(q) && /me_language[^&]*=japanese/.test(q))) return 'anime';
      if (/^\/(series|tv-series|episode|season)\//.test(p)) return 'series';
      if (/^\/anime/.test(p)) return 'anime';
      if (/^\/(genre|movie-genre)/.test(p)) return 'genres';
      if (/^\/movies?\//.test(p)) return 'movies';
      return '';
    }
    function buildSide() {
      var dash = C.account || '/movie-engine-dashboard/';
      var items = [
        { id: 'home', label: 'Home', href: C.home || '/' },
        { id: 'search', label: 'Search', href: '#search' },
        { id: 'movies', label: 'Movies', href: '/movies/', sub: subOf(/^Movies$/i) },
        { id: 'series', label: 'Series', href: '/series/', sub: subOf(/^Series$/i) },
        { id: 'anime', label: 'Anime', href: '/anime/', sub: subOf(/^Anime$/i) },
        { id: 'genres', label: 'Genres', href: '/movies/', sub: subOf(/^Genres$/i) },
        { id: 'list', key: 'watchlist', label: 'Watchlist', href: C.signedIn ? dash + '?movie_engine_view=watchlist' : (C.login || dash) },
        { id: 'user', key: 'account', label: C.signedIn ? 'My Space' : 'Sign in', href: C.signedIn ? dash : (C.login || dash) }
      ];
      if (C.items && C.items.length) {
        var byKey = {};
        items.forEach(function (it) { byKey[it.key || it.id] = it; });
        items = C.items.map(function (k) { return byKey[k]; }).filter(Boolean);
      }
      if (C.tvapp) items.push({ id: 'tvapp', label: 'Get the apps', href: C.tvapp });
      var cur = current();
      var h = '<div class="cmes-sh1"></div><div class="cmes-sh2"></div>' +
        '<a class="cmes-logo" href="' + esc(C.home || '/') + '" aria-label="' + esc(C.name || 'Home') + '">' +
          (C.logo ? '<img src="' + esc(C.logo) + '" alt="">' : '<i>' + esc((C.name || 'M').charAt(0)) + '</i>') + '</a>' +
        '<nav aria-label="Main">';
      items.forEach(function (it, i) {
        var ic = it.id === 'user' && C.avatar ? '<img src="' + esc(C.avatar) + '" alt="">' : icon(it.id);
        h += '<div class="cmes-grp" style="--i:' + i + '"><a class="cmes-it' + (it.id === cur ? ' on' : '') + '" data-id="' + it.id + '" href="' + esc(it.href) + '"' +
          (it.id === cur ? ' aria-current="page"' : '') + '><span class="cmes-bar"></span>' + ic + '<b>' + esc(it.label) + '</b></a>';
        if (C.flyouts !== false && it.sub && it.sub.length) {
          h += '<div class="cmes-fly"><div class="cmes-fly-t">' + esc(it.label) + '</div>';
          it.sub.forEach(function (s) { h += '<a href="' + esc(s.href) + '">' + esc(s.label) + '</a>'; });
          h += '</div>';
        }
        h += '</div>';
      });
      h += '</nav>';
      // phones: the top bar shows the same minimal letter logo
      var st = doc.querySelector('.hello-movieengine-header__site-title');
      if (st) {
        st.setAttribute('data-cmes-letter', (C.name || st.textContent.trim() || 'M').charAt(0));
        if (C.logo) st.setAttribute('data-cmes-logo', '1'), st.style.setProperty('--cmes-logo-url', 'url("' + C.logo.replace(/"/g, '%22') + '")');
      }
      var side = doc.createElement('aside');
      side.id = 'cmes-side';
      side.innerHTML = h;
      doc.body.appendChild(side);
      var sb = side.querySelector('[data-id="search"]');
      if (sb) sb.addEventListener('click', function (e) { e.preventDefault(); openSearch(); });
    }
  
    // ---------- search (re-uses the site's own live search) ----------
    var searchEl;
    function openSearch() {
      if (!searchEl) {
        searchEl = doc.createElement('div');
        searchEl.id = 'cmes-search';
        searchEl.innerHTML = '<button class="cmes-x" aria-label="Close search">' + icon('close') + '</button><div class="cmes-sbox"><div class="cmes-sk">Search</div></div>';
        doc.body.appendChild(searchEl);
        var live = doc.querySelector('.hello-movieengine-header__search-box .movie-engine-live-search-wrapper') ||
          doc.querySelector('.movie-engine-live-search-wrapper');
        var box = searchEl.querySelector('.cmes-sbox');
        if (live) box.appendChild(live);
        else box.insertAdjacentHTML('beforeend', '<form action="' + esc(C.home || '/') + '"><input class="movie-engine-ls-input" name="s" placeholder="Search movies & TV series..."></form>');
        searchEl.querySelector('.cmes-x').addEventListener('click', closeSearch);
        searchEl.addEventListener('click', function (e) { if (e.target === searchEl) closeSearch(); });
      }
      doc.documentElement.classList.add('cmes-searching');
      requestAnimationFrame(function () { searchEl.classList.add('open'); });
      setTimeout(function () { var i = searchEl.querySelector('input'); if (i) i.focus(); }, 60);
    }
    function closeSearch() {
      if (!searchEl) return;
      searchEl.classList.remove('open');
      doc.documentElement.classList.remove('cmes-searching');
    }
    // the top bar's search icon (phones) opens the same full-screen search
    doc.addEventListener('click', function (e) {
      var t = e.target.closest && e.target.closest('.hello-movieengine-header__search-toggle, .hello-movieengine-bottom-nav [href*="search"], .hello-movieengine-bottom-nav__item--search a');
      if (!t) return;
      e.preventDefault(); e.stopImmediatePropagation();
      openSearch();
    }, true);
    doc.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') closeSearch();
      if (e.key === '/' && !/INPUT|TEXTAREA/.test((doc.activeElement || {}).tagName || '')) { e.preventDefault(); openSearch(); }
    });
  
    // ---------- hero: Bingr-style banner with silent background trailers ----------
    var hero, muted = true, trTimer, advTimer;
    function ytId(u) {
      var m = /(?:youtu\.be\/|v=|embed\/|shorts\/)([\w-]{11})/.exec(u || '');
      return m && m[1];
    }
    function activeItem() { return hero && (hero.querySelector('.me-hero-item.is-active') || hero.querySelector('.me-hero-item')); }
    // Trailers YouTube won't play here (blocked in this country, removed, embedding off) are remembered,
    // so their banners simply show the picture.
    function ytBad(id) { try { return (localStorage.getItem('cmes_ytbad') || '').indexOf(id) !== -1; } catch (e) { return false; } }
    function ytMarkBad(id) { try { var v = (localStorage.getItem('cmes_ytbad') || '').split(',').filter(Boolean); if (v.indexOf(id) === -1) v.push(id); localStorage.setItem('cmes_ytbad', v.slice(-300).join(',')); } catch (e) {} }
    /** Watch a YouTube iframe: onPlay once it really plays, onFail if it errors or never starts. */
    function ytWatch(fr, id, onPlay, onFail, waitMs) {
      var done = false;
      var finish = function (ok) { if (done) return; done = true; window.removeEventListener('message', listen); clearTimeout(t); if (ok) onPlay(); else { ytMarkBad(id); onFail(); } };
      var listen = function (e) {
        if (e.source !== fr.contentWindow) return;
        var d; try { d = typeof e.data === 'string' ? JSON.parse(e.data) : e.data; } catch (x) { return; }
        if (!d) return;
        if (d.event === 'onError') finish(false);
        else if (d.info && d.info.playerState === 1) finish(true);       // playing
        else if (d.event === 'onStateChange' && d.info === 1) finish(true);
      };
      window.addEventListener('message', listen);
      fr.addEventListener('load', function () {
        // ask the player to report its state and errors
        try { fr.contentWindow.postMessage(JSON.stringify({ event: 'listening', id: 1, channel: 'widget' }), '*'); } catch (e) {}
        try { fr.contentWindow.postMessage(JSON.stringify({ event: 'command', func: 'addEventListener', args: ['onError'] }), '*'); } catch (e) {}
      });
      var t = setTimeout(function () { finish(false); }, waitMs || 7000);
    }
    function next() {
      var b = hero && hero.querySelector('.movie-engine-slider-next');
      if (b) b.click();
    }
    function clearTrailer() {
      clearTimeout(trTimer); clearTimeout(advTimer);
      [].forEach.call(hero.querySelectorAll('.cmes-tr'), function (t) {
        t.classList.remove('on');
        setTimeout(function () { if (t.parentNode) t.parentNode.removeChild(t); }, 900);
      });
      hero.classList.remove('cmes-playing');
    }
    function ytCmd(f) {
      var fr = hero.querySelector('.cmes-tr iframe');
      if (fr && fr.contentWindow) fr.contentWindow.postMessage(JSON.stringify({ event: 'command', func: f, args: [] }), '*');
    }
    function onSlide() {
      var it = activeItem();
      if (!it) return;
      clearTrailer();
      if (hero.__thumbs) {
        var all = [].slice.call(hero.querySelectorAll('.me-hero-item'));
        [].forEach.call(hero.__thumbs.children, function (b, i) { b.classList.toggle('on', all[i] === it); });
      }
      // replay the entrance motion on the new slide
      var inner = it.querySelector('.me-hero-content-inner');
      if (inner) { inner.classList.remove('cmes-in'); void inner.offsetWidth; inner.classList.add('cmes-in'); }
      var a = it.querySelector('.me-hero-trailer-link, .me-open-trailer');
      var id = C.trailers !== false && a && ytId(a.getAttribute('data-url') || a.href);
      var reduce = window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches;
      if (!id || ytBad(id) || reduce || window.innerWidth < 768) { advTimer = setTimeout(next, (C.slideTime || 9) * 1000); return; }
      trTimer = setTimeout(function () {
        var w = doc.createElement('div');
        w.className = 'cmes-tr';
        w.innerHTML = '<iframe src="https://www.youtube-nocookie.com/embed/' + id + '?autoplay=1&mute=1&controls=0&disablekb=1&fs=0&rel=0&iv_load_policy=3&modestbranding=1&playsinline=1&loop=1&playlist=' + id +
          '&start=4&enablejsapi=1&origin=' + encodeURIComponent(location.origin) + '" allow="autoplay; encrypted-media" tabindex="-1" title="Trailer"></iframe>';
        var ov = it.querySelector('.me-hero-overlay');
        it.insertBefore(w, ov || it.firstChild);
        // show the video only once it really plays; a blocked or missing trailer leaves the picture
        ytWatch(w.querySelector('iframe'), id, function () {
          setTimeout(function () {   // let YouTube's own title/logo fade first
            if (!w.parentNode) return;
            w.classList.add('on');
            hero.classList.add('cmes-playing');
            if (!muted) ytCmd('unMute');
          }, 1600);
        }, function () {
          if (w.parentNode) w.parentNode.removeChild(w);
          clearTimeout(advTimer);
          advTimer = setTimeout(next, (C.slideTime || 9) * 1000);
        });
        advTimer = setTimeout(next, (C.trailerTime || 45) * 1000);
      }, (C.trailerDelay != null ? C.trailerDelay : 1.6) * 1000);
    }
    function setupHero() {
      hero = doc.querySelector('.movie-engine-hero-section.me-hero-slider-wrap, .movie-engine-hero-section');
      if (!hero) return;
      // Phones: a simple stacked banner (picture, title, round play + See More), no trailer.
      if (window.innerWidth < 768) {
        if (C.phoneBanner === false) return;
        hero.classList.add('cmes-mhero');
        var fitM = function () {
          hero.style.removeProperty('margin-left');
          hero.style.removeProperty('width');
        };
        fitM();
        window.addEventListener('resize', fitM);
        [].forEach.call(hero.querySelectorAll('.me-hero-item'), function (it) {
          var acts = it.querySelector('.me-primary-actions') || it.querySelector('.movie-engine-hero-actions');
          var link = it.querySelector('.me-hero-title a[href]');
          if (acts && link && !acts.querySelector('.cmes-see')) {
            var see = doc.createElement('a');
            see.className = 'cmes-see';
            see.href = link.href;
            see.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="9"/><path d="M12 11v5M12 7.6v.1"/></svg>See More';
            acts.appendChild(see);
          }
        });
        return;
      }
      hero.classList.add('cmes-hero');
      var fitHero = function () {
        if (window.innerWidth <= 1024) {
          hero.style.removeProperty('margin-left');
          hero.style.removeProperty('width');
          return;
        }
      };
      fitHero();
      window.addEventListener('resize', fitHero);
      hero.setAttribute('data-autoplay', 'false'); // the banner moves on by itself after the trailer
      var mb = doc.createElement('button');
      mb.className = 'cmes-mute';
      mb.setAttribute('aria-label', 'Sound on');
      mb.innerHTML = icon('mute');
      mb.addEventListener('click', function () {
        muted = !muted;
        mb.innerHTML = icon(muted ? 'mute' : 'sound');
        mb.setAttribute('aria-label', muted ? 'Sound on' : 'Sound off');
        ytCmd(muted ? 'mute' : 'unMute');
      });
      hero.appendChild(mb);
      // Bingr-style banner: round play + "See More", and picture thumbnails to switch banners
      var items = [].slice.call(hero.querySelectorAll('.me-hero-item'));
      items.forEach(function (it) {
        var acts = it.querySelector('.me-primary-actions') || it.querySelector('.movie-engine-hero-actions');
        var link = it.querySelector('.me-hero-title a[href]');
        if (acts && link && !acts.querySelector('.cmes-see')) {
          var see = doc.createElement('a');
          see.className = 'cmes-see';
          see.href = link.href;
          see.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="9"/><path d="M12 11v5M12 7.6v.1"/></svg>See More';
          acts.appendChild(see);
        }
      });
      if (items.length > 1) {
        var th = doc.createElement('div');
        th.className = 'cmes-thumbs';
        items.forEach(function (it, i) {
          var bg = it.querySelector('.me-hero-bg'), u = bg ? (bg.style.backgroundImage || '').replace(/\/t\/p\/original\//, '/t/p/w300/') : '';
          var b = doc.createElement('button');
          b.setAttribute('aria-label', 'Show banner ' + (i + 1));
          if (u) b.style.backgroundImage = u;
          b.addEventListener('click', function () {
            var dots = hero.querySelectorAll('.me-dot');
            if (dots[i]) dots[i].click();
            else it.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'start' });
          });
          th.appendChild(b);
        });
        hero.appendChild(th);
        hero.__thumbs = th;
      }
      // follow the slider: it marks the visible banner with .is-active
      var last = null;
      new MutationObserver(function () {
        var it = activeItem();
        if (it && it !== last) { last = it; onSlide(); }
      }).observe(hero, { subtree: true, attributes: true, attributeFilter: ['class'] });
      // stop trailers when the banner is scrolled away or the tab is hidden
      if ('IntersectionObserver' in window) {
        new IntersectionObserver(function (es) {
          es.forEach(function (e) { ytCmd(e.isIntersecting ? 'playVideo' : 'pauseVideo'); });
        }, { threshold: 0.35 }).observe(hero);
      }
      doc.addEventListener('visibilitychange', function () { ytCmd(doc.hidden ? 'pauseVideo' : 'playVideo'); });
      last = activeItem();
      onSlide();
    }
  
    // ---------- rows: arrows, reveal on scroll ----------
    function setupRows() {
      [].forEach.call(doc.querySelectorAll('.movie-engine-section .movie-engine-items-grid'), function (g) {
        if (g.closest('.movie-engine-hero-section') || g.__cmes) return;
        g.__cmes = true;
        var sec = g.closest('.movie-engine-section');
        if (getComputedStyle(g).overflowX !== 'auto' && getComputedStyle(g).overflowX !== 'scroll') return;
        sec.classList.add('cmes-row');
        if (C.arrows !== false) ['prev', 'next'].forEach(function (d) {
          var b = doc.createElement('button');
          b.className = 'cmes-arrow cmes-' + d;
          b.setAttribute('aria-label', d === 'prev' ? 'Scroll left' : 'Scroll right');
          b.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="' + (d === 'prev' ? 'm15 5-7 7 7 7' : 'm9 5 7 7-7 7') + '"/></svg>';
          b.addEventListener('click', function () {
            g.scrollBy({ left: (d === 'prev' ? -1 : 1) * g.clientWidth * 0.85, behavior: 'smooth' });
          });
          sec.appendChild(b);
        });
        var upd = function () {
          sec.classList.toggle('cmes-at-start', g.scrollLeft < 8);
          sec.classList.toggle('cmes-at-end', g.scrollLeft + g.clientWidth > g.scrollWidth - 8);
        };
        g.addEventListener('scroll', upd, { passive: true });
        setTimeout(upd, 300);
      });
      if (!('IntersectionObserver' in window) || C.motion === false) return;
      var io = new IntersectionObserver(function (es) {
        es.forEach(function (e) { if (e.isIntersecting) { e.target.classList.add('cmes-in'); io.unobserve(e.target); } });
      }, { rootMargin: '0px 0px -8% 0px' });
      [].forEach.call(doc.querySelectorAll('.movie-engine-section:not(.cmes-rv), .movie-engine-taxonomies-wrapper:not(.cmes-rv)'), function (s) {
        if (s.closest('.movie-engine-hero-section')) return;
        var r = s.getBoundingClientRect();
        if (r.top < innerHeight) return; // already on screen: no motion
        s.classList.add('cmes-rv');
        io.observe(s);
      });
    }
  
    // ---------- header becomes a see-through bar on phones ----------
    function onScroll() {
      doc.documentElement.classList.toggle('cmes-scrolled', window.scrollY > 40);
    }
  
    // Detail pages: a soft blurred picture of the title behind the top of the page.
    function setupBackdrop() {
      if (!doc.body.classList.contains('movie-engine-single') || C.item || doc.querySelector('.cmes-hero, .movie-engine-movie-hero')) return;
      var m = doc.querySelector('meta[property="og:image"]');
      var img = m && m.content;
      if (!img) return;
      img = img.replace(/\/t\/p\/w\d+\//, '/t/p/w780/');
      var d = doc.createElement('div');
      d.id = 'cmes-backdrop';
      d.style.backgroundImage = 'url("' + img.replace(/"/g, '%22') + '")';
      var page = doc.getElementById('page') || doc.body;
      page.insertBefore(d, page.firstChild);
    }
  
    // Button labels written in capitals ("WATCH NOW") read calmer in title case ("Watch Now").
    function calmLabels(root) {
      [].forEach.call((root || doc).querySelectorAll('.movie-engine-btn, .movie-engine-watchlist-text'), function (b) {
        [].forEach.call(b.childNodes, function (n) {
          if (n.nodeType !== 3) return;
          var t = n.nodeValue;
          if (/[A-Z]{3}/.test(t) && t === t.toUpperCase() && !/^\s*(HD|4K|VIP|TV)\s*$/.test(t)) {
            n.nodeValue = t.toLowerCase().replace(/(^|\s)(\S)/g, function (m, s1, c) { return s1 + c.toUpperCase(); });
          }
        });
      });
    }
  
    // Browse pages (Movies, Series, Anime...): a page heading and filter chips that open one at a time.
    function setupBrowse() {
      var lib = doc.querySelector('.movie-engine-library-container.me-has-filters');
      if (!lib || doc.querySelector('.cmes-ph')) return;
      var t = (doc.title || '').split(/\s[\u2013\u2014|-]\s/)[0].replace(/\s*Archives?$/i, '').trim();
      if (!doc.querySelector('.cmes-browse') && !doc.querySelector('.cmes-only')) {   // a View all page has its own title
        var h = doc.createElement('h1');
        h.className = 'cmes-ph';
        h.textContent = t || 'Browse';
        lib.parentNode.insertBefore(h, lib);
      }
      var secs = [].slice.call(lib.querySelectorAll('.me-filter-section'));
      var mark = function () {
        secs.forEach(function (s) { s.classList.toggle('cmes-has-pick', !!s.querySelector('input:checked')); });
      };
      secs.forEach(function (s) {
        s.classList.add('is-collapsed');
        var title = s.querySelector('.me-filter-section-title');
        if (title) title.addEventListener('click', function () {
          // the engine opens / closes this one; close the others
          setTimeout(function () { secs.forEach(function (o) { if (o !== s) o.classList.add('is-collapsed'); }); }, 0);
        });
        s.addEventListener('change', mark);
      });
      mark();
      doc.addEventListener('click', function (e) {
        if (!e.target.closest('.me-filter-section')) secs.forEach(function (o) { o.classList.add('is-collapsed'); });
      });
      doc.addEventListener('keydown', function (e) { if (e.key === 'Escape') secs.forEach(function (o) { o.classList.add('is-collapsed'); }); });
    }
  
  
    // Endless lists (Hotstar style): near the bottom the next page loads in under the titles already shown.
    // It drives the engine's own page links, so active filters and sorting are kept.
    function setupInfinite() {
      if (C.infinite === false || !('IntersectionObserver' in window)) return;
      [].forEach.call(doc.querySelectorAll('.movie-engine-library-container'), function (lib) {
        if (lib.__cmesInf || !lib.querySelector('.me-ajax-pagination')) return;
        lib.__cmesInf = true;
        lib.classList.add('cmes-infinite');
        var content = lib.querySelector('.movie-engine-library-content') || lib;
        var more = doc.createElement('div');
        more.className = 'cmes-more';
        more.innerHTML = '<div class="cmes-more-spin"></div><div class="cmes-more-end">You\u2019ve reached the end</div>';
        content.appendChild(more);
        var busy = false, kept = null, keepY = 0, guard = null, pauseUntil = 0;
        // more titles only once the viewer starts to scroll: a page that has just opened stays still (no redraw, no jump)
        var userMoved = false;
        var moved = function () { userMoved = true; ['wheel', 'touchmove', 'keydown', 'scroll'].forEach(function (t) { window.removeEventListener(t, moved, true); }); };
        setTimeout(function () { ['wheel', 'touchmove', 'keydown', 'scroll'].forEach(function (t) { window.addEventListener(t, moved, { capture: true, passive: true }); }); }, 700);
        // A filter, sort or search change starts the list again: drop anything that was loading.
        var reset = function () {
          kept = null; busy = false; clearTimeout(guard);
          gen++; ahead = null;
          pauseUntil = Date.now() + 1800;
          more.classList.remove('loading');
          var mk = lib.querySelector('.cmes-kept-marker');
          if (mk) mk.parentNode.removeChild(mk);
        };
        lib.addEventListener('change', function (e) { if (!e.target.closest('.movie-engine-items-grid')) reset(); }, true);
        lib.addEventListener('click', function (e) {
          if (e.target.closest('.me-sort-list li, .me-clear-all-filters, .me-active-filter, [class*="remove-filter"], .me-filter-checkbox')) reset();
        }, true);
        lib.addEventListener('input', function (e) { if (e.target.closest('.me-library-search')) reset(); }, true);
        function grid() { return lib.querySelector('.movie-engine-items-grid'); }
        function nextLink() { return lib.querySelector('.movie-engine-pagination .next[href], .movie-engine-pagination a.next'); }
        function update() {
          var nx = nextLink();
          more.classList.toggle('end', !nx && !!grid() && grid().children.length > 0);
          if (more.parentNode !== content || content.lastElementChild !== more) content.appendChild(more); // stays last after the engine redraws
        }
        function finish() {
          if (!busy) return; // cancelled by a filter change
          var g = grid();
          if (kept && g) {
            var frag = doc.createDocumentFragment();
            kept.forEach(function (n) { frag.appendChild(n); });
            g.insertBefore(frag, g.firstChild);
          }
          kept = null;
          // the engine scrolls back to the top of the list: stay where the viewer was
          if (window.jQuery) { try { window.jQuery('html, body').stop(true, false); } catch (e) {} }
          var html = doc.documentElement, sb = html.style.scrollBehavior;
          html.style.scrollBehavior = 'auto';
          window.scrollTo(0, keepY);
          html.style.scrollBehavior = sb;
          // keep the address on the first page (a reload starts from the top of the list)
          try {
            var u = new URL(location.href);
            if (u.searchParams.has('paged')) { u.searchParams.delete('paged'); history.replaceState(history.state, '', u.toString()); }
          } catch (e) {}
          busy = false;
          more.classList.remove('loading');
          update();
          setTimeout(check, 300);
        }
        new MutationObserver(function () {
          if (!busy) { update(); return; } // a filter or sort change: the list simply starts again
          var g = grid();
          if (g && g.children.length && !g.querySelector('.cmes-kept-marker')) {
            clearTimeout(guard);
            guard = setTimeout(finish, 60); // let the engine finish its redraw first
          }
        }).observe(lib, { childList: true, subtree: true });
        // Smooth way: fetch the next page quietly and add its titles under the ones shown. Nothing on
        // screen is replaced and the scroll position is never touched. One page is fetched ahead.
        var ahead = null, gen = 0;
        function pageUrl(a) {
          var h = a && a.getAttribute('href');
          if (!h || h.charAt(0) === '#' || !/[?&]paged=\d+|\/page\/\d+/.test(h)) return '';
          try { return new URL(h.replace(/#038;/g, '&'), location.href).toString(); } catch (e) { return ''; }
        }
        function fetchPage(url) {
          var my = gen;
          return fetch(url, { credentials: 'same-origin' }).then(function (r) { if (!r.ok) throw 0; return r.text(); }).then(function (html) {
            if (my !== gen) throw 0;
            var d = new DOMParser().parseFromString(html, 'text/html');
            var libs = [].slice.call(d.querySelectorAll('.movie-engine-library-container')).filter(function (l) { return l.querySelector('.movie-engine-pagination') && l.querySelector('.movie-engine-items-grid'); });
            var src = libs[libs.length - 1];
            if (!src) throw 0;
            return { url: url, items: [].slice.call(src.querySelector('.movie-engine-items-grid').children), nav: src.querySelector('.movie-engine-pagination') };
          });
        }
        function prefetch() {
          var url = pageUrl(nextLink());
          if (!url || (ahead && ahead.url === url)) return;
          ahead = { url: url, p: fetchPage(url).catch(function () { return null; }) };
        }
        function append(page) {
          var g = grid(), nav = lib.querySelector('.movie-engine-pagination');
          if (!page || !g || !nav) return false;
          var seen = {};
          [].forEach.call(g.querySelectorAll('a[href]'), function (a) { seen[a.href] = 1; });
          var frag = doc.createDocumentFragment(), added = 0;
          page.items.forEach(function (n) {
            var a = n.querySelector && n.querySelector('a[href]');
            var href = a ? new URL(a.getAttribute('href'), location.href).href : '';
            if (href && seen[href]) return;
            seen[href] = 1;
            frag.appendChild(doc.importNode(n, true));
            added++;
          });
          g.appendChild(frag);
          // the new page's numbers: its "next" link points one page further
          if (page.nav) nav.innerHTML = page.nav.innerHTML;
          else nav.innerHTML = '';
          if (!added) nav.innerHTML = ''; // nothing new: treat as the end
          return true;
        }
        function load() {
          if (!userMoved) return;
          var nx = nextLink(), g = grid();
          if (busy || !nx || !g || Date.now() < pauseUntil) return;
          var url = pageUrl(nx);
          if (url) {
            busy = true;
            more.classList.add('loading');
            var my = gen;
            if (!ahead || ahead.url !== url) prefetch();
            ahead.p.then(function (page) {
              if (my !== gen) return;
              ahead = null;
              busy = false; // set first: our own redraw must not count as the engine's
              var ok = append(page);
              more.classList.remove('loading');
              update();
              if (!ok) { pauseUntil = Date.now() + 5000; return; }
              prefetch();
              setTimeout(check, 200);
            });
            return;
          }
          // fallback: the engine's own page link
          busy = true;
          more.classList.add('loading');
          keepY = window.scrollY;
          kept = [].slice.call(g.children);
          var mk = doc.createElement('span');
          mk.className = 'cmes-kept-marker';
          g.appendChild(mk); // a marker: the grid counts as redrawn once the engine has replaced it
          nx.click();
          setTimeout(function () { if (busy) { if (mk.parentNode) mk.parentNode.removeChild(mk); finish(); } }, 12000); // never stay stuck
        }
        function check() {
          var r = more.getBoundingClientRect();
          if (r.top < innerHeight + 1400) load();
        }
        setInterval(function () { if (!busy && Date.now() >= pauseUntil && lib.offsetParent !== null) check(); }, 1500);
        new IntersectionObserver(function (es) { if (es[0].isIntersecting) load(); }, { rootMargin: '0px 0px 1400px 0px' }).observe(more);
        update();
        setTimeout(prefetch, 1200);
      });
    }
  
  
    // Studio / genre pages: big logo banner, "Trending on …" with Movies / Series tabs, poster grid.
    function setupStudio() {
      var T = C.term, hero = doc.querySelector('.me-taxonomy-hero');
      if (!T || !hero || hero.__cmes) return;
      hero.__cmes = true;
      doc.documentElement.classList.add('cmes-studio');
      var bgEl = hero.querySelector('.me-hero-bg');
      var bg = bgEl ? getComputedStyle(bgEl).backgroundImage : 'none';
      hero.insertAdjacentHTML('afterbegin',
        '<div class="cmes-st-glow"' + (bg && bg !== 'none' ? ' style="background-image:' + bg.replace(/"/g, "'") + '"' : '') + '></div>' +
        '<div class="cmes-st-in">' +
          (T.logo ? '<img class="cmes-st-logo" src="' + esc(T.logo) + '" alt="' + esc(T.name) + '">' : '<h1 class="cmes-st-name">' + esc(T.name) + '</h1>') +
          '<div class="cmes-st-meta">' + esc(T.kind) + (T.count ? ' \u00b7 ' + T.count + ' titles' : '') + '</div>' +
        '</div>');
      // heading + tabs, driven by the page's own type filter
      var wrap = doc.querySelector('.movie-engine-taxonomy-wrapper');
      var sel = doc.querySelector('.movie-engine-filter-form select[name="me_post_type"]');
      if (!wrap) return;
      var bar = doc.createElement('div');
      bar.className = 'cmes-st-bar';
      var tabs = '';
      if (sel) {
        [].forEach.call(sel.options, function (o) {
          var label = o.value === '' ? 'All' : o.textContent.trim().replace(/^TV\s+/i, '');
          tabs += '<button type="button" data-v="' + esc(o.value) + '"' + (o.selected ? ' class="on"' : '') + '>' + esc(label) + '</button>';
        });
      }
      bar.innerHTML = '<h2>Trending on ' + esc(T.name) + '</h2>' + (tabs ? '<div class="cmes-st-tabs" role="tablist">' + tabs + '</div>' : '');
      wrap.insertBefore(bar, wrap.firstChild);
      if (sel) bar.addEventListener('click', function (e) {
        var b = e.target.closest('button[data-v]');
        if (!b || b.classList.contains('on')) return;
        [].forEach.call(bar.querySelectorAll('button'), function (x) { x.classList.toggle('on', x === b); });
        sel.value = b.getAttribute('data-v');
        sel.dispatchEvent(new Event('change', { bubbles: true }));
        if (window.jQuery) { try { window.jQuery(sel).trigger('change'); } catch (er) {} }
      });
    }
  
    // Bingr-style card details: "★ 8.0 · 2026 · Movie" under the title (the rating badge leaves the poster).
    function rateCards() {
      [].forEach.call(doc.querySelectorAll('.movie-engine-movie-card:not([data-cmes-r])'), function (c) {
        c.setAttribute('data-cmes-r', '1');
        var r = c.querySelector('.movie-engine-card-rating'), m = c.querySelector('.movie-engine-card-meta');
        if (!r || !m) return;
        var v = parseFloat(r.textContent.replace(/[^0-9.]/g, ''));
        if (!(v > 0)) return;
        var s = doc.createElement('span');
        s.className = 'cmes-rate';
        s.textContent = '\u2605 ' + v.toFixed(1);
        m.insertBefore(s, m.firstChild);
        c.classList.add('cmes-rated');
      });
    }
  
    // Long banner titles get smaller type, so they never run off the banner
    function sizeTitles() {
      [].forEach.call(doc.querySelectorAll('.me-hero-title'), function (h) {
        var n = (h.textContent || '').trim().length;
        h.classList.toggle('cmes-t-long', n > 20 && n <= 40);
        h.classList.toggle('cmes-t-xlong', n > 40);
      });
    }
  
    // Rows and sections slide in as they come into view
    function reveal() {
      if (!('IntersectionObserver' in window) || (window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches)) return;
      var io = reveal.io || (reveal.io = new IntersectionObserver(function (es) {
        es.forEach(function (e) { if (e.isIntersecting) { e.target.classList.add('cmes-in'); io.unobserve(e.target); } });
      }, { rootMargin: '0px 0px -8% 0px', threshold: 0.04 }));
      [].forEach.call(doc.querySelectorAll('.movie-engine-library-container:not(.cmes-reveal), .cmes-browse-all:not(.cmes-reveal), .mt-studios:not(.cmes-reveal)'), function (el) {
        if (el.getBoundingClientRect().top < innerHeight * 0.92) return;  // already on screen: leave it as it is
        el.classList.add('cmes-reveal');
        io.observe(el);
      });
    }
  
    // Phone banners: a wide backdrop on a tall phone screen shows only a thin middle slice, so on phones each
    // slide uses its portrait poster instead (the slider already has them, as its thumbnails).
    function phonePosters() {
      if (window.innerWidth >= 768) return;
      [].forEach.call(doc.querySelectorAll('.cmes-mhero'), function (hero) {
        if (hero.__cmesPosters) return;
        var items = hero.querySelectorAll('.me-hero-item');
        var thumbs = hero.querySelectorAll('.me-hero-thumbnails-wrapper img, [class*="thumbnail"] img');
        if (!items.length || thumbs.length < items.length) return;
        hero.__cmesPosters = true;
        [].forEach.call(items, function (it, i) {
          var t = thumbs[i], bg = it.querySelector('.me-hero-bg');
          var src = t && (t.getAttribute('src') || t.getAttribute('data-src') || '');
          if (!bg || !/image\.tmdb\.org\/t\/p\//.test(src)) return;
          src = src.replace(/\/t\/p\/w\d+\//, '/t/p/w780/');
          bg.style.backgroundImage = "url('" + src + "')";
        });
        hero.classList.add('cmes-mposter');
      });
    }
  
    // Phone bottom bar (the theme's): add Anime after Series, and light up the right button
    function setupBottomNav() {
      var bar = doc.querySelector('.hello-movieengine-bottom-nav__items');
      if (!bar || bar.querySelector('.hello-movieengine-bottom-nav__item--anime')) return;
      var series = bar.querySelector('.hello-movieengine-bottom-nav__item--series');
      if (!series) return;
      var li = series.cloneNode(true);
      li.className = li.className.replace('--series', '--anime');
      li.classList.remove('is-active');
      var a = li.querySelector('a');
      a.href = (C.home || '/').replace(/\/$/, '') + '/anime/';
      a.removeAttribute('aria-current');
      var lab = li.querySelector('.hello-movieengine-bottom-nav__label');
      if (lab) lab.textContent = 'Anime';
      var svg = li.querySelector('svg');
      if (svg) svg.innerHTML = P.anime;
      series.parentNode.insertBefore(li, series.nextSibling);
      if (current() === 'anime') {
        [].forEach.call(bar.querySelectorAll('.hello-movieengine-bottom-nav__item'), function (x) {
          x.classList.remove('is-active');
          var l = x.querySelector('a'); if (l) l.removeAttribute('aria-current');
        });
        li.classList.add('is-active');
        a.setAttribute('aria-current', 'page');
      }
    }
  
    function start() {
      doc.documentElement.classList.add('cmes-on');
      // each part on its own, so one problem never leaves the page hidden or half-styled
      [sizeTitles, setupBottomNav, setupBrowse, calmLabels, setupBackdrop, buildSide, setupHero, phonePosters, setupRows, setupInfinite, setupStudio, rateCards, reveal].forEach(function (fn) {
        try { fn(); } catch (e) { if (window.console) console.warn('cmes', fn.name, e); }
      });
      // rows the site loads later (filters, "load more")
      new MutationObserver(function (ms) {
        for (var i = 0; i < ms.length; i++) if (ms[i].addedNodes.length) { clearTimeout(start.t); start.t = setTimeout(function () { setupRows(); calmLabels(); rateCards(); setupInfinite(); phonePosters(); }, 150); return; }
      }).observe(doc.body, { childList: true, subtree: true });
      window.addEventListener('scroll', onScroll, { passive: true });
      onScroll();
      setTimeout(function () { doc.documentElement.classList.add('cmes-ready'); }, 0);
    }
    // our own Premium lock (not the engine's default): the title's backdrop behind it, a gold lock and what Premium gives
    function premiumLock() {
      [].forEach.call(doc.querySelectorAll('.me-paywall-content:not(.cmes-lock)'), function (c) {
        c.classList.add('cmes-lock');
        var box = c.closest('.me-paywall-container');
        var og = doc.querySelector('meta[property="og:image"]');
        if (box && og && og.content) box.style.setProperty('--cmes-lock-bg', 'url("' + og.content.replace(/"/g, '') + '")');
        var ic = c.querySelector('.me-paywall-icon');
        if (ic) ic.outerHTML = '<span class="cmes-lock-badge"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2a5 5 0 0 0-5 5v3H6a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8a2 2 0 0 0-2-2h-1V7a5 5 0 0 0-5-5zm-3 8V7a3 3 0 1 1 6 0v3H9z"/></svg></span><span class="cmes-lock-k">PREMIUM</span>';
        var msg = c.querySelector('.me-paywall-message');
        if (msg && !c.querySelector('.cmes-lock-perks')) msg.insertAdjacentHTML('afterend', '<ul class="cmes-lock-perks"><li>Every movie, series and anime</li><li>Full HD with your language and subtitles</li><li>Watch on phone, TV and the web</li></ul>');
      });
    }
    function startAll() { start(); premiumLock(); new MutationObserver(function () { premiumLock(); }).observe(doc.body, { childList: true, subtree: true }); }
    if (doc.readyState === 'loading') doc.addEventListener('DOMContentLoaded', startAll);
    else startAll();
  })();
  