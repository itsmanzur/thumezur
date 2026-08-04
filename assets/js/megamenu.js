/**
 * Themezur Mega Menu JS helper.
 *
 * Smooth hover delays & accessibility handlers.
 */
(function () {
	'use strict';

	function initMegaMenu() {
		var megaItems = document.querySelectorAll('.tz-menu-item--mega');
		if (!megaItems.length) return;

		megaItems.forEach(function (item) {
			var timer = null;

			item.addEventListener('mouseenter', function () {
				if (timer) clearTimeout(timer);
				item.classList.add('is-active');
				var link = item.querySelector('a');
				if (link) link.setAttribute('aria-expanded', 'true');
			});

			item.addEventListener('mouseleave', function () {
				timer = setTimeout(function () {
					item.classList.remove('is-active');
					var link = item.querySelector('a');
					if (link) link.setAttribute('aria-expanded', 'false');
				}, 160);
			});
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initMegaMenu);
	} else {
		initMegaMenu();
	}
})();
