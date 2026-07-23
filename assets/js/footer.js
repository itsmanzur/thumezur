/**
 * Themezur footer extras (back to top).
 */
(function () {
	'use strict';

	var btn = document.querySelector('[data-tz-btt]');
	if (!btn) {
		return;
	}

	var threshold = parseInt(btn.getAttribute('data-tz-threshold') || '400', 10);
	if (isNaN(threshold) || threshold < 100) {
		threshold = 400;
	}

	var ticking = false;

	function update() {
		ticking = false;
		var y = window.pageYOffset || document.documentElement.scrollTop || 0;
		var show = y > threshold;
		btn.classList.toggle('is-visible', show);
		if (show) {
			btn.removeAttribute('hidden');
		} else {
			btn.setAttribute('hidden', 'hidden');
		}
	}

	function onScroll() {
		if (!ticking) {
			ticking = true;
			window.requestAnimationFrame(update);
		}
	}

	btn.addEventListener('click', function () {
		var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
		window.scrollTo({ top: 0, behavior: reduce ? 'auto' : 'smooth' });
	});

	window.addEventListener('scroll', onScroll, { passive: true });
	update();
})();
