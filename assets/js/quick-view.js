/**
 * Themezur Quick View — modal, AJAX product load, AJAX add-to-cart.
 *
 * @package Themezur
 */
(function () {
	'use strict';

	/* ----------------------------------------------------------------
	 * State
	 * -------------------------------------------------------------- */
	var modal     = null;
	var backdrop  = null;
	var body_el   = null;
	var close_btn = null;
	var isOpen    = false;

	/* ----------------------------------------------------------------
	 * Init — runs after DOM ready
	 * -------------------------------------------------------------- */
	document.addEventListener('DOMContentLoaded', function () {
		modal     = document.getElementById('tz-qv-modal');
		backdrop  = document.getElementById('tz-qv-backdrop');
		body_el   = document.getElementById('tz-qv-body');
		close_btn = document.getElementById('tz-qv-close');

		if (!modal) return;

		// Delegate — works for cards loaded via AJAX filters.
		document.body.addEventListener('click', handleClick);
		close_btn && close_btn.addEventListener('click', closeModal);
		backdrop && backdrop.addEventListener('click', closeModal);
		document.addEventListener('keydown', function (e) {
			if (isOpen && (e.key === 'Escape' || e.key === 'Esc')) closeModal();
		});
	});

	/* ----------------------------------------------------------------
	 * Click delegation
	 * -------------------------------------------------------------- */
	function handleClick(e) {
		// Quick View button
		var qvBtn = e.target.closest('.tz-qv-btn');
		if (qvBtn) {
			e.preventDefault();
			openModal(parseInt(qvBtn.dataset.productId, 10));
			return;
		}

		// Add to Cart inside Quick View (AJAX)
		var atcBtn = e.target.closest('.tz-qv-atc[data-product-id]');
		if (atcBtn) {
			e.preventDefault();
			ajaxAddToCart(atcBtn);
			return;
		}

		// Gallery thumb inside Quick View
		var thumb = e.target.closest('.tz-qv-thumb');
		if (thumb && modal && modal.contains(thumb)) {
			e.preventDefault();
			switchMainImage(thumb);
			return;
		}

		// Sticky bar add-to-cart
		var stickyBtn = e.target.closest('.tz-sticky-bar__btn[data-product-id]');
		if (stickyBtn) {
			e.preventDefault();
			ajaxAddToCartById(parseInt(stickyBtn.dataset.productId, 10), stickyBtn);
			return;
		}
	}

	/* ----------------------------------------------------------------
	 * Modal open / close
	 * -------------------------------------------------------------- */
	function openModal(productId) {
		if (!modal || !body_el) return;

		// Reset
		body_el.innerHTML = '<div class="tz-qv-spinner" aria-hidden="true"></div>';
		modal.hidden = false;
		modal.removeAttribute('hidden');
		modal.classList.add('tz-qv-modal--open');
		document.body.classList.add('tz-qv-active');
		isOpen = true;

		// Trap focus
		setTimeout(function () {
			var firstFocusable = modal.querySelector('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
			if (firstFocusable) firstFocusable.focus();
		}, 80);

		fetchProduct(productId);
	}

	function closeModal() {
		if (!modal) return;
		modal.classList.remove('tz-qv-modal--open');
		document.body.classList.remove('tz-qv-active');
		isOpen = false;
		setTimeout(function () {
			modal.hidden = true;
		}, 260);
	}

	/* ----------------------------------------------------------------
	 * Fetch product HTML via AJAX
	 * -------------------------------------------------------------- */
	function fetchProduct(productId) {
		var params = new URLSearchParams({
			action:     'themezur_quick_view',
			nonce:      (window.themezurFront && window.themezurFront.nonce) || '',
			product_id: productId,
		});

		fetch(
			((window.themezurFront && window.themezurFront.ajaxUrl) || '/wp-admin/admin-ajax.php')
			+ '?' + params.toString(),
			{ credentials: 'same-origin' }
		)
		.then(function (res) { return res.json(); })
		.then(function (data) {
			if (!data.success || !body_el) return;
			body_el.innerHTML = data.data.html;
		})
		.catch(function () {
			if (body_el) body_el.innerHTML = '<p style="padding:2rem;text-align:center;">' + (window.themezurFront && window.themezurFront.i18nError || 'Error loading product.') + '</p>';
		});
	}

	/* ----------------------------------------------------------------
	 * Gallery thumbnail swap
	 * -------------------------------------------------------------- */
	function switchMainImage(thumbBtn) {
		var mainImg = document.getElementById('tz-qv-main-img');
		if (!mainImg) return;

		var url = thumbBtn.dataset.img;
		if (!url) return;

		// Active state
		var thumbs = modal.querySelectorAll('.tz-qv-thumb');
		thumbs.forEach(function (t) { t.classList.remove('tz-qv-thumb--active'); });
		thumbBtn.classList.add('tz-qv-thumb--active');

		mainImg.style.opacity = '0.6';
		mainImg.src = url;
		mainImg.onload = function () { mainImg.style.opacity = ''; };
	}

	/* ----------------------------------------------------------------
	 * AJAX Add to Cart (Quick View inline button)
	 * -------------------------------------------------------------- */
	function ajaxAddToCart(btn) {
		var productId = parseInt(btn.dataset.productId, 10);
		if (!productId) return;
		ajaxAddToCartById(productId, btn);
	}

	function ajaxAddToCartById(productId, btn) {
		btn.disabled = true;
		var originalText = btn.textContent;
		btn.textContent = '…';

		var body = new URLSearchParams({
			action:     'themezur_add_to_cart',
			nonce:      (window.themezurFront && window.themezurFront.nonce) || '',
			product_id: productId,
			quantity:   1,
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
			btn.disabled = false;
			if (data.success) {
				btn.textContent = data.data.message || '✓ Added';
				updateCartUI(data.data);
				setTimeout(function () {
					btn.textContent = originalText;
					closeModal();
				}, 1400);
			} else {
				btn.textContent = data.data && data.data.message ? data.data.message : 'Error';
				setTimeout(function () { btn.textContent = originalText; }, 2000);
			}
		})
		.catch(function () {
			btn.disabled = false;
			btn.textContent = originalText;
		});
	}

	/* ----------------------------------------------------------------
	 * Update header cart count / mini-cart after AJAX add
	 * -------------------------------------------------------------- */
	function updateCartUI(data) {
		if (!data) return;

		// Count badges
		var badges = document.querySelectorAll('.tz-cart-count, [data-cart-count]');
		badges.forEach(function (el) {
			if (data.label !== undefined) el.textContent = data.label;
		});

		// Mini-cart HTML
		if (data.mini_cart) {
			var miniContainers = document.querySelectorAll('.tz-mini-cart-content');
			miniContainers.forEach(function (el) {
				el.innerHTML = data.mini_cart;
			});
		}

		// Fire WC fragment refresh event so WooCommerce block cart also updates.
		document.body.dispatchEvent(new CustomEvent('wc_fragment_refresh', { bubbles: true }));
	}

	/* ----------------------------------------------------------------
	 * Sticky Add to Cart bar
	 * -------------------------------------------------------------- */
	(function initStickyBar() {
		var bar      = document.getElementById('tz-sticky-bar');
		var dataEl   = document.querySelector('.tz-sticky-data');
		var formBtn  = document.querySelector('.single_add_to_cart_button');

		if (!bar || !formBtn) return;

		var observer = new IntersectionObserver(
			function (entries) {
				entries.forEach(function (entry) {
					var visible = !entry.isIntersecting;
					bar.hidden  = !visible;
					bar.setAttribute('aria-hidden', visible ? 'false' : 'true');
				});
			},
			{ threshold: 0 }
		);
		observer.observe(formBtn);
	})();

	/* ----------------------------------------------------------------
	 * Quantity Stepper (+/− buttons)
	 * -------------------------------------------------------------- */
	(function initQuantityStepper() {
		function wrapInputs() {
			document.querySelectorAll('.tz-qty-input:not(.tz-qty-wrapped)').forEach(function (input) {
				var wrapper = document.createElement('div');
				wrapper.className = 'tz-qty-wrap';
				input.parentNode.insertBefore(wrapper, input);

				var minus = document.createElement('button');
				minus.type      = 'button';
				minus.className = 'tz-qty-btn tz-qty-minus';
				minus.setAttribute('aria-label', '−');
				minus.textContent = '−';

				var plus = document.createElement('button');
				plus.type      = 'button';
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

		document.addEventListener('DOMContentLoaded', wrapInputs);
	})();

})();
