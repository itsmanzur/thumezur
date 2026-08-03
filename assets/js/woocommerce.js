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

	function initStickyAtc() {
		var bar = document.querySelector('[data-tz-sticky-atc]');
		if (!bar) {
			return;
		}

		var form = document.querySelector('form.cart');
		var mainBtn = document.querySelector('.single_add_to_cart_button');
		var stickyBtn = bar.querySelector('.tz-woo-sticky-atc__btn');
		if (!mainBtn || !stickyBtn) {
			return;
		}

		function setVisible(on) {
			if (on) {
				bar.removeAttribute('hidden');
				bar.classList.add('is-visible');
				document.body.classList.add('tz-woo-sticky-atc-visible');
			} else {
				bar.setAttribute('hidden', '');
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

	function initMiniCart() {
		var root = document.querySelector('[data-tz-mini-cart]');
		if (!root) {
			return;
		}

		var cfg = window.themezurFront || {};
		var openOnAdd = cfg.miniCartOpenOnAdd !== false;

		function setOpen(on) {
			if (on) {
				root.removeAttribute('hidden');
				requestAnimationFrame(function () {
					document.body.classList.add('tz-woo-mini-cart-open');
				});
			} else {
				document.body.classList.remove('tz-woo-mini-cart-open');
				window.setTimeout(function () {
					if (!document.body.classList.contains('tz-woo-mini-cart-open')) {
						root.setAttribute('hidden', '');
					}
				}, 230);
			}
		}

		document.addEventListener('click', function (e) {
			var openBtn = e.target.closest('[data-tz-mini-cart-open]');
			if (openBtn) {
				e.preventDefault();
				setOpen(true);
				return;
			}
			if (e.target.closest('[data-tz-mini-cart-close]') || e.target.closest('[data-tz-mini-cart-overlay]')) {
				setOpen(false);
			}
		});

		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape' && document.body.classList.contains('tz-woo-mini-cart-open')) {
				setOpen(false);
			}
		});

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
					if (json && json.success && json.data && json.data.fragments) {
						applyFragments(json.data.fragments);
					}
				})
				.finally(function () {
					root.classList.remove('is-loading');
				});
		}

		root.addEventListener('click', function (e) {
			var item = e.target.closest('.tz-woo-mini-cart__item');
			if (!item) {
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
			if (!input) {
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

		if (openOnAdd && typeof jQuery !== 'undefined') {
			jQuery(document.body).on('added_to_cart', function () {
				setOpen(true);
			});
		}
	}

	ready(function () {
		initStickyAtc();
		initShopFilters();
		initMiniCart();
	});
})();
