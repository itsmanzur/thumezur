/**
 * Themezur Wishlist — localStorage heart toggle.
 *
 * Reads/writes an array of product IDs to localStorage key 'tz_wishlist'.
 * Updates all heart buttons and count badges on the page.
 * On the [themezur_wishlist] shortcode page, loads the product cards via AJAX.
 *
 * @package Themezur
 */
(function () {
	'use strict';

	var STORAGE_KEY = 'tz_wishlist';

	/* ----------------------------------------------------------------
	 * Read / Write helpers
	 * -------------------------------------------------------------- */
	function getIds() {
		try {
			var stored = localStorage.getItem(STORAGE_KEY);
			return stored ? JSON.parse(stored) : [];
		} catch (e) {
			return [];
		}
	}

	function setIds(ids) {
		try {
			localStorage.setItem(STORAGE_KEY, JSON.stringify(ids));
		} catch (e) {}
	}

	function hasId(id) {
		return getIds().indexOf(id) !== -1;
	}

	function addId(id) {
		var ids = getIds();
		if (ids.indexOf(id) === -1) {
			ids.push(id);
			setIds(ids);
		}
	}

	function removeId(id) {
		var ids = getIds().filter(function (i) { return i !== id; });
		setIds(ids);
	}

	/* ----------------------------------------------------------------
	 * UI sync
	 * -------------------------------------------------------------- */
	function syncButtons() {
		document.querySelectorAll('.tz-wl-btn').forEach(function (btn) {
			var id      = parseInt(btn.dataset.productId, 10);
			var active  = hasId(id);
			btn.classList.toggle('tz-wl-btn--active', active);
			btn.setAttribute('aria-pressed', active ? 'true' : 'false');
			btn.setAttribute(
				'aria-label',
				active
					? (window.themezurFront && window.themezurFront.i18nRemoveWishlist) || 'Remove from Wishlist'
					: (window.themezurFront && window.themezurFront.i18nAddWishlist)    || 'Add to Wishlist'
			);
		});
	}

	function syncCounts() {
		var ids   = getIds();
		var count = ids.length;
		document.querySelectorAll('.tz-wl-count').forEach(function (el) {
			el.textContent = count > 0 ? String(count) : '';
			el.hidden = count === 0;
		});
	}

	function sync() {
		syncButtons();
		syncCounts();
	}

	/* ----------------------------------------------------------------
	 * Toggle handler
	 * -------------------------------------------------------------- */
	document.addEventListener('click', function (e) {
		var btn = e.target.closest('.tz-wl-btn');
		if (!btn) return;
		e.preventDefault();

		var id = parseInt(btn.dataset.productId, 10);
		if (!id) return;

		if (hasId(id)) {
			removeId(id);
		} else {
			addId(id);
			// Heart animation
			btn.classList.add('tz-wl-btn--pop');
			btn.addEventListener('animationend', function () {
				btn.classList.remove('tz-wl-btn--pop');
			}, { once: true });
		}

		sync();

		// If on the wishlist page, refresh the grid.
		if (document.getElementById('tz-wishlist-page')) {
			loadWishlistGrid();
		}
	});

	/* ----------------------------------------------------------------
	 * Wishlist page — load product cards
	 * -------------------------------------------------------------- */
	function loadWishlistGrid() {
		var grid  = document.getElementById('tz-wishlist-grid');
		var empty = document.getElementById('tz-wishlist-empty');
		if (!grid) return;

		var ids = getIds();

		if (ids.length === 0) {
			grid.innerHTML  = '';
			if (empty) empty.hidden = false;
			return;
		}

		if (empty) empty.hidden = true;

		var body = new URLSearchParams({
			action: 'themezur_wishlist_products',
			nonce:  (window.themezurFront && window.themezurFront.nonce) || '',
			ids:    ids.join(','),
		});

		fetch(
			(window.themezurFront && window.themezurFront.ajaxUrl) || '/wp-admin/admin-ajax.php',
			{
				method:      'POST',
				credentials: 'same-origin',
				headers:     { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
				body:        body.toString(),
			}
		)
		.then(function (res) { return res.json(); })
		.then(function (data) {
			if (data.success) {
				grid.innerHTML = data.data.html || '';
				sync(); // re-sync buttons in freshly injected HTML
			}
		})
		.catch(function () {});
	}

	/* ----------------------------------------------------------------
	 * Boot
	 * -------------------------------------------------------------- */
	document.addEventListener('DOMContentLoaded', function () {
		sync();

		// If on the wishlist shortcode page, load immediately.
		if (document.getElementById('tz-wishlist-page')) {
			loadWishlistGrid();
		}
	});

})();
