/**
 * Themezur header — nav, dark mode, scroll, cats mega, smart search, announce.
 */
(function () {
	'use strict';

	var DARK_KEY = 'themezur_dark';
	var cfg = window.themezurFront || {};

	function setCookie(name, value, days) {
		var maxAge = Math.max(1, parseInt(days, 10) || 7) * 24 * 60 * 60;
		var secure = location.protocol === 'https:' ? '; Secure' : '';
		document.cookie =
			encodeURIComponent(name) +
			'=' +
			encodeURIComponent(value) +
			'; path=/; Max-Age=' +
			maxAge +
			'; SameSite=Lax' +
			secure;
	}

	function initAnnounce(header) {
		var bar = header.querySelector('[data-tz-announce]');
		if (!bar) {
			return;
		}
		var btn = bar.querySelector('[data-tz-announce-dismiss]');
		if (!btn) {
			return;
		}
		btn.addEventListener('click', function () {
			var key = bar.getAttribute('data-tz-announce-key') || 'themezur_announce_1';
			var days = parseInt(bar.getAttribute('data-tz-announce-days') || '7', 10);
			try {
				setCookie(key, '1', days);
			} catch (e) {}
			bar.setAttribute('hidden', '');
			header.classList.add('is-announce-dismissed');
		});
	}

	function initNav(header) {
		var toggle = header.querySelector('[data-tz-nav-toggle]');
		var panel = header.querySelector('[data-tz-mobile-nav]');
		if (!toggle || !panel) {
			return;
		}

		function setOpen(open) {
			toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
			header.classList.toggle('is-nav-open', open);
			if (open) {
				panel.removeAttribute('hidden');
			} else {
				panel.setAttribute('hidden', '');
			}
		}

		toggle.addEventListener('click', function () {
			setOpen(toggle.getAttribute('aria-expanded') !== 'true');
		});

		panel.addEventListener('click', function (event) {
			if (event.target.closest('a')) {
				setOpen(false);
			}
		});

		document.addEventListener('keydown', function (event) {
			if (event.key === 'Escape') {
				setOpen(false);
			}
		});

		window.addEventListener('resize', function () {
			if (window.matchMedia('(min-width: 992px)').matches) {
				setOpen(false);
			}
		});
	}

	function initDark(header) {
		var darkBtn = header.querySelector('[data-tz-dark-toggle]');
		if (!darkBtn) {
			return;
		}
		darkBtn.addEventListener('click', function () {
			var on = !document.body.classList.contains('tz-dark');
			document.body.classList.toggle('tz-dark', on);
			try {
				localStorage.setItem(DARK_KEY, on ? '1' : '0');
			} catch (e) {}
		});
	}

	function initScroll(header) {
		var behavior = header.getAttribute('data-tz-scroll') || 'none';
		var offset = parseInt(header.getAttribute('data-tz-scroll-offset') || '40', 10);
		if (behavior === 'none') {
			return;
		}

		var ticking = false;
		function apply() {
			var y = window.scrollY || window.pageYOffset || 0;
			var active = y > offset;
			header.classList.toggle('is-scrolled', active);

			if (behavior === 'shrink') {
				header.classList.toggle('is-shrunk', active);
			}
			if (behavior === 'transparent_solid') {
				header.classList.toggle('is-solid', active);
			}
			if (behavior === 'bottom_sticky') {
				header.classList.toggle('is-bottom-stuck', active);
			}
		}

		window.addEventListener(
			'scroll',
			function () {
				if (!ticking) {
					window.requestAnimationFrame(function () {
						apply();
						ticking = false;
					});
					ticking = true;
				}
			},
			{ passive: true }
		);
		apply();
	}

	function initCats(header) {
		var root = header.querySelector('[data-tz-cats]');
		if (!root) {
			return;
		}
		var btn = root.querySelector('[data-tz-cats-toggle]');
		var panel = root.querySelector('[data-tz-cats-panel]');
		if (!btn || !panel) {
			return;
		}

		function setOpen(open) {
			btn.setAttribute('aria-expanded', open ? 'true' : 'false');
			root.classList.toggle('is-open', open);
			if (open) {
				panel.removeAttribute('hidden');
			} else {
				panel.setAttribute('hidden', '');
			}
		}

		btn.addEventListener('click', function (event) {
			event.preventDefault();
			setOpen(btn.getAttribute('aria-expanded') !== 'true');
		});

		document.addEventListener('click', function (event) {
			if (!root.contains(event.target)) {
				setOpen(false);
			}
		});

		document.addEventListener('keydown', function (event) {
			if (event.key === 'Escape') {
				setOpen(false);
			}
		});
	}

	function initSmartSearch(header) {
		var wrap = header.querySelector('[data-tz-smart-search]');
		if (!wrap || !cfg.ajaxUrl) {
			return;
		}
		var input = wrap.querySelector('[data-tz-search-input]');
		var box = wrap.querySelector('[data-tz-search-suggest]');
		if (!input || !box) {
			return;
		}

		var min = cfg.searchMin || 2;
		var timer = null;
		var controller = null;

		function hide() {
			box.innerHTML = '';
			box.setAttribute('hidden', '');
		}

		function render(items) {
			if (!items || !items.length) {
				box.innerHTML = '<div class="tz-search-suggest__empty">' + (cfg.i18nNoResults || 'No products found') + '</div>';
				box.removeAttribute('hidden');
				return;
			}
			var html = items
				.map(function (item) {
					var img = item.image
						? '<img src="' + item.image + '" alt="" width="40" height="40" loading="lazy" />'
						: '<span class="tz-search-suggest__ph"></span>';
					var price = item.price ? '<span class="tz-search-suggest__price">' + item.price + '</span>' : '';
					return (
						'<a class="tz-search-suggest__item" href="' +
						item.url +
						'">' +
						img +
						'<span class="tz-search-suggest__meta"><span class="tz-search-suggest__title">' +
						item.title +
						'</span>' +
						price +
						'</span></a>'
					);
				})
				.join('');
			box.innerHTML = html;
			box.removeAttribute('hidden');
		}

		function search(q) {
			if (controller) {
				controller.abort();
			}
			controller = typeof AbortController !== 'undefined' ? new AbortController() : null;
			var url =
				cfg.ajaxUrl +
				'?action=themezur_product_search&nonce=' +
				encodeURIComponent(cfg.nonce || '') +
				'&q=' +
				encodeURIComponent(q);

			fetch(url, {
				credentials: 'same-origin',
				signal: controller ? controller.signal : undefined,
			})
				.then(function (res) {
					return res.json();
				})
				.then(function (json) {
					if (!json || !json.success) {
						hide();
						return;
					}
					render((json.data && json.data.items) || []);
				})
				.catch(function () {
					/* aborted or network */
				});
		}

		input.addEventListener('input', function () {
			var q = (input.value || '').trim();
			clearTimeout(timer);
			if (q.length < min) {
				hide();
				return;
			}
			timer = setTimeout(function () {
				search(q);
			}, 220);
		});

		input.addEventListener('focus', function () {
			var q = (input.value || '').trim();
			if (q.length >= min && box.innerHTML) {
				box.removeAttribute('hidden');
			}
		});

		document.addEventListener('click', function (event) {
			if (!wrap.contains(event.target)) {
				hide();
			}
		});
	}

	function initPromoRotate(header) {
		var wrap = header.querySelector('[data-tz-promo-rotate]');
		if (!wrap) {
			return;
		}
		var textEl = wrap.querySelector('[data-tz-promo-text]');
		var raw = wrap.getAttribute('data-tz-promo-messages') || '[]';
		var messages = [];
		try {
			messages = JSON.parse(raw);
		} catch (e) {
			return;
		}
		if (!textEl || !Array.isArray(messages) || messages.length < 2) {
			return;
		}
		var interval = Math.max(2, parseInt(wrap.getAttribute('data-tz-promo-interval') || '4', 10)) * 1000;
		var index = 0;
		setInterval(function () {
			index = (index + 1) % messages.length;
			textEl.classList.add('is-fading');
			setTimeout(function () {
				textEl.textContent = messages[index];
				textEl.classList.remove('is-fading');
			}, 180);
		}, interval);
	}

	function initScrollProgress(header) {
		var bar = header.querySelector('[data-tz-scroll-progress-bar]');
		if (!bar) {
			return;
		}
		var ticking = false;
		function update() {
			var doc = document.documentElement;
			var scrollTop = window.scrollY || doc.scrollTop || 0;
			var height = (doc.scrollHeight - doc.clientHeight) || 1;
			var pct = Math.max(0, Math.min(100, (scrollTop / height) * 100));
			bar.style.width = pct + '%';
			ticking = false;
		}
		window.addEventListener(
			'scroll',
			function () {
				if (!ticking) {
					ticking = true;
					window.requestAnimationFrame(update);
				}
			},
			{ passive: true }
		);
		update();
	}

	function initCartCount() {
		if (typeof jQuery === 'undefined') {
			return;
		}
		jQuery(document.body).on('added_to_cart removed_from_cart updated_wc_div wc_fragments_refreshed', function () {
			/* Woo fragments replace .tz-cart-btn__count automatically when available. */
		});
	}

	try {
		if (localStorage.getItem(DARK_KEY) === '1') {
			document.body.classList.add('tz-dark');
		}
	} catch (e) {}

	document.querySelectorAll('[data-tz-header]').forEach(function (header) {
		initAnnounce(header);
		initNav(header);
		initDark(header);
		initScroll(header);
		initScrollProgress(header);
		initPromoRotate(header);
		initCats(header);
		initSmartSearch(header);
	});
	initCartCount();
})();
