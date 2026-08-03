/**
 * Themezur header — nav, off-canvas mobile drawer, dark mode, scroll, cats mega, smart search, announce.
 *
 * @package Themezur
 */
(function () {
	'use strict';

	var DARK_KEY = 'themezur_dark';
	var cfg      = window.themezurFront || {};

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
		if (!bar) return;
		var btn = bar.querySelector('[data-tz-announce-dismiss]');
		if (!btn) return;

		btn.addEventListener('click', function () {
			var key  = bar.getAttribute('data-tz-announce-key') || 'themezur_announce_1';
			var days = parseInt(bar.getAttribute('data-tz-announce-days') || '7', 10);
			try {
				setCookie(key, '1', days);
			} catch (e) {}

			bar.classList.add('is-dismissing');
			setTimeout(function () {
				bar.setAttribute('hidden', '');
				header.classList.add('is-announce-dismissed');
			}, 240);
		});
	}

	/* ----------------------------------------------------------------
	 * Mobile Off-Canvas Drawer Nav + Accordion Submenus
	 * -------------------------------------------------------------- */
	function initNav(header) {
		var toggle = header.querySelector('[data-tz-nav-toggle]');
		var drawer = document.getElementById('tz-mobile-drawer');
		if (!toggle || !drawer) return;

		var closeBtns = drawer.querySelectorAll('[data-tz-drawer-close]');
		var panel     = drawer.querySelector('.tz-mobile-drawer__panel');
		var nav       = drawer.querySelector('.tz-mobile-drawer__nav');

		// Attach accordion arrow buttons to parent menu items inside mobile drawer.
		if (nav) {
			nav.querySelectorAll('.menu-item-has-children').forEach(function (li) {
				var link = li.querySelector(':scope > a');
				if (link && !li.querySelector('.tz-menu-arrow')) {
					var arrowBtn = document.createElement('button');
					arrowBtn.type = 'button';
					arrowBtn.className = 'tz-menu-arrow';
					arrowBtn.setAttribute('aria-label', 'Toggle sub-menu');
					arrowBtn.innerHTML = '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>';

					link.after(arrowBtn);

					arrowBtn.addEventListener('click', function (e) {
						e.preventDefault();
						e.stopPropagation();
						var open = li.classList.toggle('sub-menu-open');
						arrowBtn.classList.toggle('is-open', open);
					});
				}
			});
		}

		function setOpen(open) {
			toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
			header.classList.toggle('is-nav-open', open);
			document.body.classList.toggle('tz-drawer-open', open);

			if (open) {
				drawer.removeAttribute('hidden');
				requestAnimationFrame(function () {
					drawer.classList.add('is-open');
					if (panel) panel.focus();
				});
			} else {
				drawer.classList.remove('is-open');
				setTimeout(function () {
					drawer.setAttribute('hidden', '');
				}, 260);
			}
		}

		// Drawer tabs (Menu vs Categories)
		var tabs  = drawer.querySelectorAll('[data-tz-drawer-tab]');
		var panes = drawer.querySelectorAll('[data-tz-drawer-pane]');

		function switchTab(tabName) {
			tabs.forEach(function (t) {
				t.classList.toggle('is-active', t.getAttribute('data-tz-drawer-tab') === tabName);
			});
			panes.forEach(function (p) {
				var active = p.getAttribute('data-tz-drawer-pane') === tabName;
				p.classList.toggle('is-active', active);
				if (active) {
					p.removeAttribute('hidden');
				} else {
					p.setAttribute('hidden', '');
				}
			});
		}

		tabs.forEach(function (btn) {
			btn.addEventListener('click', function () {
				switchTab(btn.getAttribute('data-tz-drawer-tab'));
			});
		});

		// External open-drawer triggers (e.g. from Mobile Bottom Nav)
		document.querySelectorAll('[data-tz-open-drawer]').forEach(function (btn) {
			btn.addEventListener('click', function () {
				var targetTab = btn.getAttribute('data-tz-open-drawer');
				switchTab(targetTab || 'menu');
				setOpen(true);
			});
		});

		// Focus search input trigger
		document.querySelectorAll('[data-tz-focus-search]').forEach(function (btn) {
			btn.addEventListener('click', function () {
				var searchInput = document.getElementById('tz-header-search') || document.querySelector('.tz-mobile-drawer__search input');
				if (searchInput) {
					window.scrollTo({ top: 0, behavior: 'smooth' });
					setTimeout(function () { searchInput.focus(); }, 150);
				}
			});
		});

		toggle.addEventListener('click', function () {
			setOpen(drawer.hasAttribute('hidden') || !drawer.classList.contains('is-open'));
		});

		closeBtns.forEach(function (btn) {
			btn.addEventListener('click', function () { setOpen(false); });
		});

		drawer.addEventListener('click', function (event) {
			if (event.target.closest('a:not(.menu-item-has-children > a)')) {
				setOpen(false);
			}
		});

		document.addEventListener('keydown', function (event) {
			if (event.key === 'Escape' && !drawer.hasAttribute('hidden')) {
				setOpen(false);
			}
		});

		window.addEventListener('resize', function () {
			if (window.matchMedia('(min-width: 992px)').matches && !drawer.hasAttribute('hidden')) {
				setOpen(false);
			}
		});
	}

	/* ----------------------------------------------------------------
	 * Dark Mode Toggle
	 * -------------------------------------------------------------- */
	function initDark(header) {
		var darkBtns = document.querySelectorAll('[data-tz-dark-toggle]');
		if (!darkBtns.length) return;
		darkBtns.forEach(function (btn) {
			btn.addEventListener('click', function () {
				var on = !document.body.classList.contains('tz-dark');
				document.body.classList.toggle('tz-dark', on);
				try {
					localStorage.setItem(DARK_KEY, on ? '1' : '0');
				} catch (e) {}
			});
		});
	}


	/* ----------------------------------------------------------------
	 * Sticky Header Scroll Behavior
	 * -------------------------------------------------------------- */
	function initScroll(header) {
		var behavior = header.getAttribute('data-tz-scroll') || 'none';
		var offset   = parseInt(header.getAttribute('data-tz-scroll-offset') || '40', 10);
		if (behavior === 'none') return;

		var lastY   = 0;
		var ticking = false;
		function apply() {
			var y      = window.scrollY || window.pageYOffset || 0;
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
			if (behavior === 'auto_hide') {
				if (y > offset && y > lastY + 10) {
					header.classList.add('is-hidden');
				} else if (y < lastY - 5 || y <= offset) {
					header.classList.remove('is-hidden');
				}
			}
			lastY = y;
		}

		window.addEventListener('scroll', function () {
			if (!ticking) {
				window.requestAnimationFrame(function () {
					apply();
					ticking = false;
				});
				ticking = true;
			}
		}, { passive: true });
		apply();
	}

	/* ----------------------------------------------------------------
	 * Categories Mega Menu Dropdown
	 * -------------------------------------------------------------- */
	function initCats(header) {
		var root  = header.querySelector('[data-tz-cats]');
		if (!root) return;
		var btn   = root.querySelector('[data-tz-cats-toggle]');
		var panel = root.querySelector('[data-tz-cats-panel]');
		if (!btn || !panel) return;

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

	/* ----------------------------------------------------------------
	 * Smart Live Search with Clear (×) Button and Arrow Key Nav
	 * -------------------------------------------------------------- */
	function initSmartSearch(header) {
		var wrap = header.querySelector('[data-tz-smart-search]');
		if (!wrap || !cfg.ajaxUrl) return;
		var input    = wrap.querySelector('[data-tz-search-input]');
		var box      = wrap.querySelector('[data-tz-search-suggest]');
		var clearBtn = wrap.querySelector('[data-tz-search-clear]');
		if (!input || !box) return;

		var min         = cfg.searchMin || 2;
		var timer       = null;
		var controller  = null;
		var activeIndex = -1;

		function toggleClearBtn() {
			if (!clearBtn) return;
			if (input.value.trim().length > 0) {
				clearBtn.removeAttribute('hidden');
			} else {
				clearBtn.setAttribute('hidden', '');
			}
		}

		if (clearBtn) {
			clearBtn.addEventListener('click', function () {
				input.value = '';
				toggleClearBtn();
				hide();
				input.focus();
			});
		}

		function hide() {
			box.replaceChildren();
			box.setAttribute('hidden', '');
			input.setAttribute('aria-expanded', 'false');
			input.removeAttribute('aria-activedescendant');
			activeIndex = -1;
		}

		function render(items) {
			box.replaceChildren();
			activeIndex = -1;
			input.removeAttribute('aria-activedescendant');

			if (!items || !items.length) {
				var empty = document.createElement('div');
				empty.className = 'tz-search-suggest__empty';
				empty.setAttribute('role', 'status');
				empty.textContent = cfg.i18nNoResults || 'No products found';
				box.appendChild(empty);
				box.removeAttribute('hidden');
				input.setAttribute('aria-expanded', 'true');
				return;
			}

			items.forEach(function (item, index) {
				var link  = document.createElement('a');
				var media;
				var meta  = document.createElement('span');
				var title = document.createElement('span');

				link.className = 'tz-search-suggest__item';
				link.href      = item.url || '#';
				link.id        = 'tz-search-option-' + index;
				link.setAttribute('role', 'option');
				link.setAttribute('aria-selected', 'false');

				if (item.image) {
					media         = document.createElement('img');
					media.src     = item.image;
					media.alt     = '';
					media.width   = 44;
					media.height  = 44;
					media.loading = 'lazy';
				} else {
					media           = document.createElement('span');
					media.className = 'tz-search-suggest__ph';
				}

				meta.className  = 'tz-search-suggest__meta';
				title.className = 'tz-search-suggest__title';
				title.textContent = item.title || '';
				meta.appendChild(title);

				if (item.price) {
					var price       = document.createElement('span');
					price.className = 'tz-search-suggest__price';
					price.textContent = item.price;
					meta.appendChild(price);
				}

				link.appendChild(media);
				link.appendChild(meta);
				box.appendChild(link);
			});

			box.removeAttribute('hidden');
			input.setAttribute('aria-expanded', 'true');
		}

		function setActive(index) {
			var options = box.querySelectorAll('[role="option"]');
			if (!options.length) return;

			if (activeIndex >= 0 && options[activeIndex]) {
				options[activeIndex].classList.remove('is-selected');
				options[activeIndex].setAttribute('aria-selected', 'false');
			}

			activeIndex = (index + options.length) % options.length;
			options[activeIndex].classList.add('is-selected');
			options[activeIndex].setAttribute('aria-selected', 'true');
			input.setAttribute('aria-activedescendant', options[activeIndex].id);
			options[activeIndex].scrollIntoView({ block: 'nearest' });
		}

		function search(q) {
			if (controller) controller.abort();
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
				.then(function (res) { return res.json(); })
				.then(function (json) {
					if (!json || !json.success) {
						hide();
						return;
					}
					render((json.data && json.data.items) || []);
				})
				.catch(function () {});
		}

		input.addEventListener('input', function () {
			toggleClearBtn();
			var q = (input.value || '').trim();
			clearTimeout(timer);
			activeIndex = -1;
			input.removeAttribute('aria-activedescendant');

			if (q.length < min) {
				hide();
				return;
			}
			timer = setTimeout(function () { search(q); }, 220);
		});

		input.addEventListener('focus', function () {
			toggleClearBtn();
			var q = (input.value || '').trim();
			if (q.length >= min && box.children.length > 0) {
				box.removeAttribute('hidden');
			}
		});

		input.addEventListener('keydown', function (event) {
			var options = box.querySelectorAll('[role="option"]');

			if ('Escape' === event.key) {
				hide();
				return;
			}

			if (box.hasAttribute('hidden') || !options.length) return;

			if ('ArrowDown' === event.key) {
				event.preventDefault();
				setActive(activeIndex + 1);
			} else if ('ArrowUp' === event.key) {
				event.preventDefault();
				setActive(activeIndex - 1);
			} else if ('Enter' === event.key && activeIndex >= 0 && options[activeIndex]) {
				event.preventDefault();
				options[activeIndex].click();
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
		if (!wrap) return;
		var textEl   = wrap.querySelector('[data-tz-promo-text]');
		var raw      = wrap.getAttribute('data-tz-promo-messages') || '[]';
		var messages = [];
		try { messages = JSON.parse(raw); } catch (e) { return; }
		if (!textEl || !Array.isArray(messages) || messages.length < 2) return;

		var interval = Math.max(2, parseInt(wrap.getAttribute('data-tz-promo-interval') || '4', 10)) * 1000;
		var index    = 0;

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
		if (!bar) return;
		var ticking = false;

		function update() {
			var doc       = document.documentElement;
			var scrollTop = window.scrollY || doc.scrollTop || 0;
			var height    = (doc.scrollHeight - doc.clientHeight) || 1;
			var pct       = Math.max(0, Math.min(100, (scrollTop / height) * 100));
			bar.style.width = pct + '%';
			ticking       = false;
		}

		window.addEventListener('scroll', function () {
			if (!ticking) {
				ticking = true;
				window.requestAnimationFrame(update);
			}
		}, { passive: true });
		update();
	}

	function initCartCount() {
		var refreshTimer = null;
		var request      = null;

		function updateBadge(count, label) {
			var badge = document.querySelector('[data-tz-cart-count]');
			if (!badge) return;
			var visual     = badge.querySelector('[aria-hidden="true"]');
			var accessible = badge.querySelector('.screen-reader-text');
			if (visual) visual.textContent = String(count);
			if (accessible) accessible.textContent = label;
		}

		function updateMiniCart(markup) {
			var current = document.querySelector('[data-tz-mini-cart] .widget_shopping_cart_content');
			if (!current || !markup) return;
			var template = document.createElement('template');
			template.innerHTML = markup.trim();
			var replacement = template.content.firstElementChild;
			if (replacement && replacement.classList.contains('widget_shopping_cart_content')) {
				current.replaceWith(replacement);
			}
		}

		function refresh() {
			if (!cfg.ajaxUrl || !cfg.nonce || !document.querySelector('[data-tz-cart-count]')) return;
			if (request) request.abort();
			request = typeof AbortController !== 'undefined' ? new AbortController() : null;

			var body = new URLSearchParams();
			body.set('action', 'themezur_cart_count');
			body.set('nonce', cfg.nonce);

			fetch(cfg.ajaxUrl, {
				method: 'POST',
				credentials: 'same-origin',
				headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
				body: body.toString(),
				signal: request ? request.signal : undefined,
			})
				.then(function (res) { return res.json(); })
				.then(function (json) {
					if (json && json.success && json.data) {
						updateBadge(parseInt(json.data.count || 0, 10), json.data.label || '');
						updateMiniCart(json.data.mini_cart || '');
					}
				})
				.catch(function () {});
		}

		function scheduleRefresh() {
			clearTimeout(refreshTimer);
			refreshTimer = setTimeout(refresh, 180);
		}

		if (typeof jQuery !== 'undefined') {
			jQuery(document.body).on('added_to_cart removed_from_cart updated_wc_div wc_fragments_refreshed', scheduleRefresh);
		}

		document.body.addEventListener('wc-blocks_added_to_cart', scheduleRefresh);
		document.body.addEventListener('wc-blocks_removed_from_cart', scheduleRefresh);

		var cartBlock = document.querySelector('.wp-block-woocommerce-cart');
		if (cartBlock && typeof MutationObserver !== 'undefined') {
			new MutationObserver(scheduleRefresh).observe(cartBlock, { childList: true, subtree: true });
			scheduleRefresh();
		}
	}

	function initMiniCart() {
		var drawer       = document.querySelector('[data-tz-mini-cart]');
		var toggle       = document.querySelector('[data-tz-mini-cart-toggle]');
		if (!drawer || !toggle) return;
		var panel        = drawer.querySelector('[data-tz-mini-cart-panel]');
		var closeButtons = drawer.querySelectorAll('[data-tz-mini-cart-close]');
		var lastFocus    = null;
		var closeTimer  = null;

		if (!panel) return;

		function focusableElements() {
			return Array.prototype.filter.call(
				panel.querySelectorAll('a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])'),
				function (el) { return el.offsetParent !== null; }
			);
		}

		function openDrawer() {
			clearTimeout(closeTimer);
			lastFocus = document.activeElement;
			drawer.hidden = false;
			toggle.setAttribute('aria-expanded', 'true');
			document.body.classList.add('tz-mini-cart-open');

			window.requestAnimationFrame(function () {
				drawer.classList.add('is-open');
				var focusable = focusableElements();
				(focusable[0] || panel).focus();
			});
		}

		function closeDrawer() {
			if (drawer.hidden) return;
			drawer.classList.remove('is-open');
			toggle.setAttribute('aria-expanded', 'false');
			document.body.classList.remove('tz-mini-cart-open');

			closeTimer = setTimeout(function () {
				drawer.hidden = true;
				if (lastFocus && typeof lastFocus.focus === 'function') lastFocus.focus();
			}, 260);
		}

		toggle.addEventListener('click', function (event) {
			event.preventDefault();
			if (drawer.hidden) openDrawer();
			else closeDrawer();
		});

		closeButtons.forEach(function (button) {
			button.addEventListener('click', closeDrawer);
		});

		drawer.addEventListener('keydown', function (event) {
			if ('Escape' === event.key) {
				event.preventDefault();
				closeDrawer();
				return;
			}
			if ('Tab' !== event.key) return;

			var focusable = focusableElements();
			if (!focusable.length) {
				event.preventDefault();
				panel.focus();
				return;
			}
			var first = focusable[0];
			var last  = focusable[focusable.length - 1];
			if (event.shiftKey && document.activeElement === first) {
				event.preventDefault();
				last.focus();
			} else if (!event.shiftKey && document.activeElement === last) {
				event.preventDefault();
				first.focus();
			}
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
	initMiniCart();
})();
