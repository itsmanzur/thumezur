/**
 * Themezur Mega Menu — hover delay + keyboard accessibility.
 */
(function () {
	'use strict';

	var HOVER_CLOSE_MS = 160;
	var desktopMq = window.matchMedia('(min-width: 992px)');

	function topLink(item) {
		return item.querySelector(':scope > a');
	}

	function setOpen(item, open) {
		item.classList.toggle('is-active', open);
		var link = topLink(item);
		if (link) {
			link.setAttribute('aria-expanded', open ? 'true' : 'false');
		}
	}

	function closeAll(except) {
		document.querySelectorAll('.tz-menu-item--mega.is-active').forEach(function (item) {
			if (except && item === except) return;
			setOpen(item, false);
		});
	}

	function initMegaMenu() {
		var megaItems = document.querySelectorAll('.tz-nav-list:not(.tz-nav-list--mobile) > .tz-menu-item--mega');
		if (!megaItems.length) return;

		megaItems.forEach(function (item) {
			var timer = null;
			var link = topLink(item);
			var panel = item.querySelector(':scope > .tz-mega-dropdown');

			item.addEventListener('mouseenter', function () {
				if (!desktopMq.matches) return;
				if (timer) clearTimeout(timer);
				closeAll(item);
				setOpen(item, true);
			});

			item.addEventListener('mouseleave', function () {
				if (!desktopMq.matches) return;
				timer = setTimeout(function () {
					setOpen(item, false);
				}, HOVER_CLOSE_MS);
			});

			if (link) {
				link.addEventListener('focus', function () {
					if (!desktopMq.matches) return;
					closeAll(item);
					setOpen(item, true);
				});

				link.addEventListener('keydown', function (event) {
					if (!desktopMq.matches) return;

					if ('Escape' === event.key) {
						if (item.classList.contains('is-active')) {
							event.preventDefault();
							setOpen(item, false);
							link.focus();
						}
						return;
					}

					if ('ArrowDown' === event.key) {
						event.preventDefault();
						setOpen(item, true);
						if (panel) {
							var first = panel.querySelector('a[href], button:not([disabled])');
							if (first) first.focus();
						}
					}
				});
			}

			if (panel) {
				panel.addEventListener('keydown', function (event) {
					if ('Escape' !== event.key || !desktopMq.matches) return;
					event.preventDefault();
					setOpen(item, false);
					if (link) link.focus();
				});
			}
		});

		document.addEventListener('focusin', function (event) {
			if (!desktopMq.matches) return;
			var target = event.target;
			if (!(target instanceof Element)) return;
			var mega = target.closest('.tz-menu-item--mega');
			if (!mega) {
				closeAll(null);
			}
		});

		document.addEventListener('keydown', function (event) {
			if ('Escape' === event.key && desktopMq.matches) {
				closeAll(null);
			}
		});

		initMegaTabs();
	}

	function initMegaTabs() {
		document.querySelectorAll('[data-tz-mega-tabs]').forEach(function (root) {
			var buttons = root.querySelectorAll('[data-tz-mega-tab]');
			var panels = root.querySelectorAll('[data-tz-mega-tabpanel]');
			if (!buttons.length || !panels.length) return;

			function activate(key) {
				buttons.forEach(function (btn) {
					var on = btn.getAttribute('data-tz-mega-tab') === key;
					btn.classList.toggle('is-active', on);
					btn.setAttribute('aria-selected', on ? 'true' : 'false');
				});
				panels.forEach(function (panel) {
					var on = panel.getAttribute('data-tz-mega-tabpanel') === key;
					panel.classList.toggle('is-active', on);
					if (on) panel.removeAttribute('hidden');
					else panel.setAttribute('hidden', '');
				});
			}

			buttons.forEach(function (btn, index) {
				btn.addEventListener('click', function () {
					activate(btn.getAttribute('data-tz-mega-tab'));
				});
				btn.addEventListener('keydown', function (event) {
					if ('ArrowRight' !== event.key && 'ArrowLeft' !== event.key) return;
					event.preventDefault();
					var next = 'ArrowRight' === event.key ? index + 1 : index - 1;
					if (next < 0) next = buttons.length - 1;
					if (next >= buttons.length) next = 0;
					buttons[next].focus();
					activate(buttons[next].getAttribute('data-tz-mega-tab'));
				});
			});
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initMegaMenu);
	} else {
		initMegaMenu();
	}
})();
