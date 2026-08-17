/**
 * Themezur footer — back-to-top + newsletter AJAX.
 *
 * @package Themezur
 */
(function () {
	'use strict';

	function initBackToTop() {
		var btn = document.querySelector('[data-tz-btt]');
		if (!btn) return;

		var progressCircle = document.getElementById('tz-btt-progress');
		var radius = 18;
		var circumference = 2 * Math.PI * radius;

		if (progressCircle) {
			progressCircle.style.strokeDasharray = circumference + ' ' + circumference;
			progressCircle.style.strokeDashoffset = circumference;
		}

		var threshold = parseInt(btn.getAttribute('data-tz-threshold') || '400', 10);
		if (isNaN(threshold) || threshold < 100) threshold = 400;

		var ticking = false;

		function update() {
			ticking = false;
			var doc = document.documentElement;
			var scrollTop = window.pageYOffset || doc.scrollTop || 0;
			var maxScroll = (doc.scrollHeight - doc.clientHeight) || 1;
			var show = scrollTop > threshold;
			btn.classList.toggle('is-visible', show);
			if (show) btn.removeAttribute('hidden');
			else btn.setAttribute('hidden', 'hidden');

			if (progressCircle) {
				var pct = Math.max(0, Math.min(1, scrollTop / maxScroll));
				progressCircle.style.strokeDashoffset = circumference - (pct * circumference);
			}
		}

		btn.addEventListener('click', function () {
			var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
			window.scrollTo({ top: 0, behavior: reduce ? 'auto' : 'smooth' });
		});

		window.addEventListener('scroll', function () {
			if (!ticking) {
				ticking = true;
				window.requestAnimationFrame(update);
			}
		}, { passive: true });
		update();
	}

	function initNewsletter() {
		var cfg = window.themezurFooter || {};
		var forms = document.querySelectorAll('[data-tz-newsletter]');
		if (!forms.length || !cfg.ajaxUrl) return;

		forms.forEach(function (form) {
			form.addEventListener('submit', function (event) {
				if (form.getAttribute('data-tz-nl-empty') === '1') {
					event.preventDefault();
					return;
				}

				var actionUrl = form.getAttribute('data-tz-nl-action') || '';
				if (!actionUrl) return; // allow native submit fallback

				event.preventDefault();
				var emailInput = form.querySelector('input[type="email"]');
				var msg = form.querySelector('[data-tz-nl-msg]');
				var btn = form.querySelector('button[type="submit"]');
				var emailName = form.getAttribute('data-tz-nl-email-name') || 'EMAIL';
				if (!emailInput) return;

				var body = new FormData();
				body.append('action', 'themezur_newsletter_subscribe');
				body.append('nonce', cfg.nonce || '');
				body.append('email', emailInput.value || '');
				body.append('action_url', actionUrl);
				body.append('email_name', emailName);

				if (btn) {
					btn.disabled = true;
					btn.classList.add('is-loading');
				}
				if (msg) {
					msg.hidden = true;
					msg.textContent = '';
					msg.classList.remove('is-error', 'is-success');
				}

				fetch(cfg.ajaxUrl, { method: 'POST', body: body, credentials: 'same-origin' })
					.then(function (res) { return res.json(); })
					.then(function (json) {
						var ok = json && json.success;
						var text = (json && json.data && json.data.message) || (ok ? (cfg.i18nSuccess || 'Thanks!') : (cfg.i18nError || 'Error'));
						if (msg) {
							msg.hidden = false;
							msg.textContent = text;
							msg.classList.add(ok ? 'is-success' : 'is-error');
						}
						if (ok) form.reset();
					})
					.catch(function () {
						if (msg) {
							msg.hidden = false;
							msg.textContent = cfg.i18nError || 'Error';
							msg.classList.add('is-error');
						}
					})
					.finally(function () {
						if (btn) {
							btn.disabled = false;
							btn.classList.remove('is-loading');
						}
					});
			});
		});
	}

	initBackToTop();
	initNewsletter();
})();
