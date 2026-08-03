/**
 * Themezur footer extras — Circular scroll progress Back-to-Top button.
 *
 * @package Themezur
 */
(function () {
	'use strict';

	var btn = document.querySelector('[data-tz-btt]');
	if (!btn) return;

	var progressCircle = document.getElementById('tz-btt-progress');
	var radius          = 18;
	var circumference   = 2 * Math.PI * radius; // ~113.097

	if (progressCircle) {
		progressCircle.style.strokeDasharray  = circumference + ' ' + circumference;
		progressCircle.style.strokeDashoffset = circumference;
	}

	var threshold = parseInt(btn.getAttribute('data-tz-threshold') || '400', 10);
	if (isNaN(threshold) || threshold < 100) {
		threshold = 400;
	}

	var ticking = false;

	function update() {
		ticking = false;
		var doc       = document.documentElement;
		var scrollTop = window.pageYOffset || doc.scrollTop || 0;
		var maxScroll = (doc.scrollHeight - doc.clientHeight) || 1;

		var show = scrollTop > threshold;
		btn.classList.toggle('is-visible', show);

		if (show) {
			btn.removeAttribute('hidden');
		} else {
			btn.setAttribute('hidden', 'hidden');
		}

		if (progressCircle) {
			var pct    = Math.max(0, Math.min(1, scrollTop / maxScroll));
			var offset = circumference - (pct * circumference);
			progressCircle.style.strokeDashoffset = offset;
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
