/**
 * Themezur Blog JS — Reading progress bar, Table of Contents, Social share copy-link.
 *
 * @package Themezur
 */
(function () {
	'use strict';

	document.addEventListener('DOMContentLoaded', function () {
		initReadingProgress();
		initTableOfContents();
		initCopyLink();
	});

	/* ----------------------------------------------------------------
	 * 1. Scroll Reading Progress Bar
	 * -------------------------------------------------------------- */
	function initReadingProgress() {
		var bar = document.getElementById('tz-blog-progress-bar');
		var content = document.getElementById('tz-blog-content');

		if (!bar || !content) return;

		function updateProgress() {
			var rect         = content.getBoundingClientRect();
			var windowHeight = window.innerHeight;
			var contentHeight= content.offsetHeight;
			var scrolled     = windowHeight - rect.top;
			var total        = contentHeight + windowHeight;
			var percent      = Math.min(100, Math.max(0, (scrolled / total) * 100));

			bar.style.width = percent.toFixed(1) + '%';
		}

		window.addEventListener('scroll', updateProgress, { passive: true });
		updateProgress();
	}

	/* ----------------------------------------------------------------
	 * 2. Auto Table of Contents (from H2, H3 headings)
	 * -------------------------------------------------------------- */
	function initTableOfContents() {
		var tocContainer = document.getElementById('tz-blog-toc');
		var tocList      = document.getElementById('tz-blog-toc-list');
		var content      = document.getElementById('tz-blog-content');

		if (!tocContainer || !tocList || !content) return;

		var headings = content.querySelectorAll('h2, h3');
		if (headings.length < 2) return; // Only build TOC if 2 or more headings exist.

		var ul = document.createElement('ul');
		ul.className = 'tz-toc-ul';

		headings.forEach(function (heading, index) {
			// Ensure heading has an ID
			if (!heading.id) {
				var slug = heading.textContent
					.toLowerCase()
					.replace(/[^\w\s-]/g, '')
					.replace(/\s+/g, '-');
				heading.id = (slug || 'heading') + '-' + (index + 1);
			}

			var li   = document.createElement('li');
			var link = document.createElement('a');

			li.className   = 'tz-toc-item tz-toc-item--' + heading.tagName.toLowerCase();
			link.href      = '#' + heading.id;
			link.textContent = heading.textContent;

			link.addEventListener('click', function (e) {
				e.preventDefault();
				var target = document.getElementById(heading.id);
				if (target) {
					var top = target.getBoundingClientRect().top + window.pageYOffset - 80;
					window.scrollTo({ top: top, behavior: 'smooth' });
				}
			});

			li.appendChild(link);
			ul.appendChild(li);
		});

		tocList.appendChild(ul);
		tocContainer.hidden = false;

		// Toggle handler
		var toggleBtn = tocContainer.querySelector('.tz-blog-toc__toggle');
		if (toggleBtn) {
			toggleBtn.addEventListener('click', function () {
				var expanded = toggleBtn.getAttribute('aria-expanded') === 'true';
				toggleBtn.setAttribute('aria-expanded', !expanded);
				tocList.hidden = expanded;
			});
		}
	}

	/* ----------------------------------------------------------------
	 * 3. Copy Link Handler
	 * -------------------------------------------------------------- */
	function initCopyLink() {
		document.addEventListener('click', function (e) {
			var btn = e.target.closest('.tz-blog-share__btn--copy');
			if (!btn) return;
			e.preventDefault();

			var url = btn.dataset.url || window.location.href;

			if (navigator.clipboard && navigator.clipboard.writeText) {
				navigator.clipboard.writeText(url).then(showCopiedNotice);
			} else {
				// Fallback
				var input = document.createElement('input');
				input.value = url;
				document.body.appendChild(input);
				input.select();
				document.execCommand('copy');
				document.body.removeChild(input);
				showCopiedNotice();
			}

			function showCopiedNotice() {
				btn.classList.add('tz-blog-share__btn--copied');
				var origHtml = btn.innerHTML;
				btn.innerHTML = '<span style="font-size:0.75rem;font-weight:700;">✓ Copied</span>';
				setTimeout(function () {
					btn.innerHTML = origHtml;
					btn.classList.remove('tz-blog-share__btn--copied');
				}, 1800);
			}
		});
	}

})();
