/**
 * Themezur WooCommerce — sticky ATC, qty stepper, shop filters, mini-cart qty.
 *
 * @package Themezur
 */
(function () {
	'use strict';

	function ready(fn) {
		if (document.readyState === 'loading') {
			document.addEventListener('DOMContentLoaded', fn);
		} else {
			fn();
		}
	}

	function applyFragments(fragments) {
		if (!fragments) {
			return;
		}
		Object.keys(fragments).forEach(function (selector) {
			var html = fragments[selector];
			document.querySelectorAll(selector).forEach(function (el) {
				var wrap = document.createElement('div');
				wrap.innerHTML = html;
				var next = wrap.firstElementChild;
				if (next) {
					el.replaceWith(next);
				} else {
					el.outerHTML = html;
				}
			});
		});
	}

	function updateCartBadges(count, label) {
		document.querySelectorAll('[data-tz-cart-count]').forEach(function (badge) {
			var visual = badge.querySelector('[aria-hidden="true"]');
			var accessible = badge.querySelector('.screen-reader-text');
			if (visual) {
				visual.textContent = String(count);
			}
			if (accessible && label) {
				accessible.textContent = label;
			}
		});
	}

	function initStickyAtc() {
		var bar = document.querySelector('[data-tz-sticky-atc]');
		if (!bar) {
			return;
		}

		var form = document.querySelector('form.cart');
		var mainBtn = document.querySelector('form.cart .single_add_to_cart_button');
		var stickyBtn = bar.querySelector('.tz-woo-sticky-atc__btn, .tz-sticky-bar__btn');
		if (!mainBtn || !stickyBtn) {
			return;
		}

		function setVisible(on) {
			if (on) {
				bar.removeAttribute('hidden');
				bar.setAttribute('aria-hidden', 'false');
				bar.classList.add('is-visible');
				document.body.classList.add('tz-woo-sticky-atc-visible');
			} else {
				bar.setAttribute('hidden', '');
				bar.setAttribute('aria-hidden', 'true');
				bar.classList.remove('is-visible');
				document.body.classList.remove('tz-woo-sticky-atc-visible');
			}
		}

		if ('IntersectionObserver' in window) {
			var io = new IntersectionObserver(
				function (entries) {
					var entry = entries[0];
					if (!entry) {
						return;
					}
					setVisible(!entry.isIntersecting);
				},
				{ root: null, threshold: 0, rootMargin: '0px' }
			);
			io.observe(mainBtn);
		}

		stickyBtn.addEventListener('click', function () {
			if (form && typeof form.requestSubmit === 'function') {
				form.requestSubmit(mainBtn);
				return;
			}
			mainBtn.click();
		});
	}

	function initQuantityStepper() {
		function wrapInputs() {
			document.querySelectorAll('.tz-qty-input:not(.tz-qty-wrapped)').forEach(function (input) {
				var wrapper = document.createElement('div');
				wrapper.className = 'tz-qty-wrap';
				input.parentNode.insertBefore(wrapper, input);

				var minus = document.createElement('button');
				minus.type = 'button';
				minus.className = 'tz-qty-btn tz-qty-minus';
				minus.setAttribute('aria-label', '−');
				minus.textContent = '−';

				var plus = document.createElement('button');
				plus.type = 'button';
				plus.className = 'tz-qty-btn tz-qty-plus';
				plus.setAttribute('aria-label', '+');
				plus.textContent = '+';

				wrapper.appendChild(minus);
				wrapper.appendChild(input);
				wrapper.appendChild(plus);
				input.classList.add('tz-qty-wrapped');

				minus.addEventListener('click', function () {
					var val = parseInt(input.value, 10) || 1;
					var min = parseInt(input.getAttribute('min'), 10) || 1;
					if (val > min) {
						input.value = val - 1;
						input.dispatchEvent(new Event('change', { bubbles: true }));
					}
				});
				plus.addEventListener('click', function () {
					var val = parseInt(input.value, 10) || 1;
					var max = parseInt(input.getAttribute('max'), 10) || 9999;
					if (val < max) {
						input.value = val + 1;
						input.dispatchEvent(new Event('change', { bubbles: true }));
					}
				});
			});
		}

		wrapInputs();
	}

	function initShopFilters() {
		var toggle = document.querySelector('[data-tz-woo-filters-toggle]');
		var sidebar = document.querySelector('[data-tz-woo-shop-sidebar]');
		var overlay = document.querySelector('[data-tz-woo-filters-overlay]');
		var closeBtn = document.querySelector('[data-tz-woo-filters-close]');
		if (!toggle || !sidebar) {
			return;
		}

		function setOpen(on) {
			document.body.classList.toggle('tz-woo-filters-open', on);
			toggle.setAttribute('aria-expanded', on ? 'true' : 'false');
			if (overlay) {
				if (on) {
					overlay.removeAttribute('hidden');
				} else {
					overlay.setAttribute('hidden', '');
				}
			}
		}

		toggle.setAttribute('aria-expanded', 'false');
		toggle.addEventListener('click', function () {
			setOpen(!document.body.classList.contains('tz-woo-filters-open'));
		});

		if (closeBtn) {
			closeBtn.addEventListener('click', function () {
				setOpen(false);
			});
		}

		if (overlay) {
			overlay.addEventListener('click', function () {
				setOpen(false);
			});
		}

		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape' && document.body.classList.contains('tz-woo-filters-open')) {
				setOpen(false);
			}
		});
	}

	function initMiniCartQty() {
		var root = document.querySelector('[data-tz-mini-cart]');
		if (!root) {
			return;
		}

		var cfg = window.themezurFront || {};

		function postQty(key, quantity, remove) {
			if (!cfg.ajaxUrl || !cfg.nonce) {
				return;
			}
			var body = new FormData();
			body.append('action', 'themezur_mini_cart');
			body.append('nonce', cfg.nonce);
			body.append('cart_item_key', key);
			if (remove) {
				body.append('remove', '1');
			} else {
				body.append('quantity', String(quantity));
			}
			root.classList.add('is-loading');
			fetch(cfg.ajaxUrl, { method: 'POST', body: body, credentials: 'same-origin' })
				.then(function (r) {
					return r.json();
				})
				.then(function (json) {
					if (!json || !json.success || !json.data) {
						return;
					}
					if (json.data.fragments) {
						applyFragments(json.data.fragments);
					}
					if (typeof json.data.count !== 'undefined') {
						updateCartBadges(json.data.count, json.data.label || '');
					}
					document.body.dispatchEvent(new CustomEvent('themezur_mini_cart_updated', { bubbles: true, detail: json.data }));
				})
				.finally(function () {
					root.classList.remove('is-loading');
				});
		}

		root.addEventListener('click', function (e) {
			var item = e.target.closest('.tz-woo-mini-cart__item');
			if (!item || !root.contains(item)) {
				return;
			}
			var key = item.getAttribute('data-key');
			if (!key) {
				return;
			}
			if (e.target.closest('[data-tz-mini-remove]')) {
				e.preventDefault();
				postQty(key, 0, true);
				return;
			}
			var qtyBtn = e.target.closest('[data-tz-mini-qty]');
			if (qtyBtn) {
				e.preventDefault();
				var input = item.querySelector('[data-tz-mini-qty-input]');
				var delta = parseInt(qtyBtn.getAttribute('data-tz-mini-qty'), 10) || 0;
				var next = (parseInt(input && input.value, 10) || 0) + delta;
				if (next < 0) {
					next = 0;
				}
				postQty(key, next, next === 0);
			}
		});

		root.addEventListener('change', function (e) {
			var input = e.target.closest('[data-tz-mini-qty-input]');
			if (!input || !root.contains(input)) {
				return;
			}
			var item = input.closest('.tz-woo-mini-cart__item');
			if (!item) {
				return;
			}
			var key = item.getAttribute('data-key');
			var next = parseInt(input.value, 10);
			if (isNaN(next) || next < 0) {
				next = 0;
			}
			postQty(key, next, next === 0);
		});
	}

	ready(function () {
		initStickyAtc();
		initQuantityStepper();
		initShopFilters();
		initMiniCartQty();
	});
})();
