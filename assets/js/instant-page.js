/**
 * Themezur Instant Page Preloader (Hover Speculation)
 *
 * Preloads internal pages on link hover / touchstart for 0ms instant navigation.
 *
 * @package Themezur
 */

(function() {
	'use strict';

	var mouseoverTimer = null;
	var prefetchedUrls = new Set();

	// Check if URL is eligible for preloading
	function isEligible(url) {
		if (!url) return false;
		try {
			var loc = window.location;
			var u = new URL(url, loc.href);

			// Must be same origin
			if (u.origin !== loc.origin) return false;

			// Must be http or https
			if (u.protocol !== 'http:' && u.protocol !== 'https:') return false;

			// Current page or hash anchor on current page
			if (u.pathname === loc.pathname && u.search === loc.search) return false;
			if (u.hash && u.pathname === loc.pathname) return false;

			// Ignore wp-admin, wp-login, cart, checkout, logout
			var path = u.pathname.toLowerCase();
			if (path.indexOf('/wp-admin') !== -1 ||
				path.indexOf('/wp-login') !== -1 ||
				path.indexOf('logout') !== -1 ||
				path.indexOf('/cart') !== -1 ||
				path.indexOf('/checkout') !== -1 ||
				u.search.indexOf('add-to-cart=') !== -1) {
				return false;
			}

			// Ignore file downloads
			if (/\.(pdf|zip|rar|exe|dmg|jpg|jpeg|png|gif|webp|svg|mp3|mp4|doc|docx|xls|xlsx|ppt|pptx)$/i.test(path)) {
				return false;
			}

			return true;
		} catch (e) {
			return false;
		}
	}

	function prefetch(url) {
		if (prefetchedUrls.has(url)) return;
		prefetchedUrls.add(url);

		// Use Speculation Rules API if supported (Chrome 109+)
		if (HTMLScriptElement.supports && HTMLScriptElement.supports('speculationrules')) {
			var specScript = document.createElement('script');
			specScript.type = 'speculationrules';
			specScript.textContent = JSON.stringify({
				prerender: [{ source: 'list', urls: [url] }]
			});
			document.head.appendChild(specScript);
			return;
		}

		// Fallback to standard <link rel="prefetch">
		var link = document.createElement('link');
		link.rel = 'prefetch';
		link.href = url;
		link.as = 'document';
		document.head.appendChild(link);
	}

	function onPointerOver(e) {
		var a = e.target.closest('a');
		if (!a || !a.href) return;

		if (!isEligible(a.href)) return;

		mouseoverTimer = setTimeout(function() {
			prefetch(a.href);
		}, 65);
	}

	function onPointerOut(e) {
		if (mouseoverTimer) {
			clearTimeout(mouseoverTimer);
			mouseoverTimer = null;
		}
	}

	function onTouchStart(e) {
		var a = e.target.closest('a');
		if (a && a.href && isEligible(a.href)) {
			prefetch(a.href);
		}
	}

	document.addEventListener('mouseover', onPointerOver, { passive: true });
	document.addEventListener('mouseout', onPointerOut, { passive: true });
	document.addEventListener('touchstart', onTouchStart, { passive: true });
})();
