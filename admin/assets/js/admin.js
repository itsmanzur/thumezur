/**
 * Themezur admin panel — Alpine component.
 */
(function () {
	'use strict';

	function deepClone(value) {
		return JSON.parse(JSON.stringify(value));
	}

	document.addEventListener('alpine:init', function () {
		Alpine.data('themezurPanel', function () {
			var cfg = window.themezurAdmin || {};

			return {
				tab: (cfg.initialTab === 'help') ? 'help' : 'dashboard',
				headerSubTab: 'top',
				footerSubTab: '1',
				blogSubTab: 'archive',
				generalSubTab: 'brand',
				saving: false,
				toast: '',
				toastTimer: null,
				elementor: !!cfg.elementor,
				i18n: cfg.i18n || {},
				options: deepClone(cfg.options || {}),
				menus: cfg.menus || [],
				categories: cfg.categories || [],
				woocommerce: !!cfg.woocommerce,
				fontCatalog: cfg.fontCatalog || [],
				templates: {
					header: [],
					footer: [],
				},
				loadingTemplates: {
					header: false,
					footer: false,
				},
				loadedTemplates: {
					header: false,
					footer: false,
				},
				mediaFrame: null,
				socialNetworks: cfg.socialNetworks || [],

				init() {
					this.ensureHeaderScroll();
					this.ensureAnnounce();
					this.ensureSpacing();
					this.ensureAssignments();
					this.ensureSocials();
					this.ensurePromos();
					this.ensureFooter();
					this.ensureBlog();
					this.ensurePages();
					this.ensureWoo();
					this.ensureGeneralDesign();
				},

				ensureGeneralDesign: function () {
					if (!this.options.general) {
						this.options.general = {};
					}
					if (!this.options.general.font_body_id) {
						this.options.general.font_body_id = 'system';
					}
					if (!this.options.general.font_heading_id) {
						this.options.general.font_heading_id = 'system';
					}
					if (!this.options.general.font_body_weights || !Array.isArray(this.options.general.font_body_weights)) {
						this.options.general.font_body_weights = ['400'];
					}
					if (!this.options.general.font_heading_weights || !Array.isArray(this.options.general.font_heading_weights)) {
						this.options.general.font_heading_weights = ['700'];
					}
					if (typeof this.options.general.breadcrumbs === 'undefined') {
						this.options.general.breadcrumbs = true;
					}
					// Ensure header typo and middle defaults.
					if (!this.options.header) { this.options.header = {}; }
					if (!this.options.header.typo) { this.options.header.typo = {}; }
					if (!this.options.header.typo.font_id) {
						this.options.header.typo.font_id = 'inherit';
					}
					if (!this.options.header.middle) { this.options.header.middle = {}; }
					if (typeof this.options.header.middle.show_menu === 'undefined') {
						this.options.header.middle.show_menu = false;
					}
					if (typeof this.options.header.middle.menu_id === 'undefined') {
						this.options.header.middle.menu_id = 0;
					}
					if (typeof this.options.header.middle.show_button === 'undefined') {
						this.options.header.middle.show_button = false;
						this.options.header.middle.button_text = 'Order Now';
						this.options.header.middle.button_url = '';
						this.options.header.middle.button_target = '_self';
					}
					if (!this.options.header.bottom) { this.options.header.bottom = {}; }
					if (typeof this.options.header.bottom.menu_id === 'undefined') {
						this.options.header.bottom.menu_id = 0;
					}
					if (!this.options.header.mobile_nav) {
						this.options.header.mobile_nav = {
							enabled: true,
							show_home: true,
							show_cats: true,
							show_search: true,
							show_cart: true,
							show_account: true
						};
					}
					if (!this.options.footer) { this.options.footer = {}; }
					if (!this.options.footer.trust_badges) {
						this.options.footer.trust_badges = {
							enabled: true,
							items: [
								{ icon: 'shipping', title: 'Free Shipping', subtitle: 'On orders over $50' },
								{ icon: 'return', title: '7 Days Return', subtitle: 'Money back guarantee' },
								{ icon: 'secure', title: '100% Secure', subtitle: 'Protected payments' },
								{ icon: 'support', title: '24/7 Support', subtitle: 'Dedicated help center' }
							]
						};
					}
					if (!this.options.footer.newsletter_row) {
						this.options.footer.newsletter_row = {
							enabled: false,
							title: 'Subscribe to our Newsletter',
							subtitle: 'Get 10% off your first order and stay updated with latest deals!',
							action: '',
							placeholder: 'Enter your email address',
							button: 'Subscribe Now'
						};
					}
					if (this.options.footer.columns && !this.options.footer.columns['5']) {
						this.options.footer.columns['5'] = {
							enabled: false,
							title: 'Newsletter',
							subtitle: '',
							type: 'newsletter',
							text: 'Subscribe for exclusive offers.',
							logo_source: 'none',
							logo_id: 0,
							show_logo: false,
							show_social: true,
							cta_label: '',
							cta_url: '',
							menu_id: 0,
							links: [],
							address: '',
							phone: '',
							email: '',
							whatsapp: '',
							hours: '',
							map_url: '',
							posts_count: 3,
							posts_show_thumb: true,
							posts_show_date: true,
							posts_category: 0,
							products_count: 3,
							products_source: 'recent',
							products_show_thumb: true,
							products_show_price: true,
							shortcode: '',
							newsletter_mode: 'form',
							newsletter_text: '',
							newsletter_placeholder: 'Your email',
							newsletter_button: 'Subscribe',
							newsletter_action: '',
							newsletter_email_name: 'EMAIL',
							newsletter_shortcode: ''
						};
					}
				},




				/**
				 * Get the available weights for a font id.
				 */
				getFontWeights: function (fontId) {
					var catalog = this.fontCatalog || [];
					for (var i = 0; i < catalog.length; i++) {
						if (catalog[i].id === fontId) {
							return catalog[i].weights || ['300', '400', '500', '600', '700'];
						}
					}
					return ['300', '400', '500', '600', '700'];
				},

				/**
				 * Return an inline style string to render preview text in the selected font.
				 */
				getFontStyle: function (fontId) {
					var catalog = this.fontCatalog || [];
					for (var i = 0; i < catalog.length; i++) {
						if (catalog[i].id === fontId && catalog[i].family) {
							return 'font-family:' + catalog[i].family + ';font-size:0.95em;';
						}
					}
					return '';
				},

				/**
				 * Inject a Google Fonts link into the admin <head> for live preview.
				 */
				loadFontPreview: function (fontId) {
					var catalog = this.fontCatalog || [];
					var font = null;
					for (var i = 0; i < catalog.length; i++) {
						if (catalog[i].id === fontId) { font = catalog[i]; break; }
					}
					if (!font || !font.google) { return; }
					var linkId = 'tz-preview-' + fontId;
					if (document.getElementById(linkId)) { return; } // already loaded
					var link = document.createElement('link');
					link.id = linkId;
					link.rel = 'stylesheet';
					link.href = 'https://fonts.googleapis.com/css2?family=' + font.google + ':wght@400;700&display=swap';
					document.head.appendChild(link);
				},

				/**
				 * Called when the body font picker changes.
				 * Resets weights to the font's sensible defaults and loads preview.
				 */
				onFontBodyChange: function () {
					var id = this.options.general.font_body_id;
					var weights = this.getFontWeights(id);
					// Default to 400 if available, else first weight.
					this.options.general.font_body_weights = weights.indexOf('400') !== -1 ? ['400'] : [weights[0]];
					this.loadFontPreview(id);
				},

				/**
				 * Called when the heading font picker changes.
				 * Resets weights to the font's sensible defaults and loads preview.
				 */
				onFontHeadingChange: function () {
					var id = this.options.general.font_heading_id;
					var weights = this.getFontWeights(id);
					// Default to 700 if available, else last weight.
					this.options.general.font_heading_weights = weights.indexOf('700') !== -1 ? ['700'] : [weights[weights.length - 1]];
					this.loadFontPreview(id);
				},

				/**
				 * Called when the header font picker changes.
				 */
				onHeaderFontChange: function () {
					var id = this.options.header.typo.font_id;
					if (id && id !== 'inherit' && id !== 'custom') {
						this.loadFontPreview(id);
					}
				},

				ensureHeaderScroll: function () {
					if (!this.options.header) {
						this.options.header = {};
					}
					if (!this.options.header.scroll || typeof this.options.header.scroll !== 'object') {
						this.options.header.scroll = { behavior: 'none', offset: 40, progress: false };
					}
					if (!this.options.header.scroll.behavior) {
						this.options.header.scroll.behavior = 'none';
					}
					if (typeof this.options.header.scroll.offset === 'undefined' || this.options.header.scroll.offset === null) {
						this.options.header.scroll.offset = 40;
					}
					if (typeof this.options.header.scroll.progress === 'undefined') {
						this.options.header.scroll.progress = false;
					}
				},

				ensureAnnounce: function () {
					if (!this.options.header) {
						this.options.header = {};
					}
					if (!this.options.header.announce || typeof this.options.header.announce !== 'object') {
						this.options.header.announce = {
							enabled: false,
							text: '',
							link_url: '',
							link_text: 'Learn more',
							bg: '#2563eb',
							text_color: '#ffffff',
							dismissible: true,
							cookie_days: 7,
							version: '1',
						};
					}
				},

				ensureSpacing: function () {
					if (!this.options.header) {
						this.options.header = {};
					}
					if (!this.options.header.spacing || typeof this.options.header.spacing !== 'object') {
						this.options.header.spacing = {
							container_width: '1200px',
							side_padding: '1.15rem',
							top_min_h: '2.25rem',
							mid_min_h: '4.5rem',
							bot_min_h: '3rem',
						};
					}
				},

				ensureAssignments: function () {
					if (!this.options.assignments) {
						this.options.assignments = {};
					}
					if (!this.options.assignments.include || typeof this.options.assignments.include !== 'object') {
						this.options.assignments.include = {
							front_page: true,
							shop: true,
							blog: true,
							other: false,
						};
					}
					var keys = ['front_page', 'shop', 'blog'];
					if (!this.options.assignments.header_overrides || typeof this.options.assignments.header_overrides !== 'object') {
						this.options.assignments.header_overrides = {};
					}
					keys.forEach(function (key) {
						if (!this.options.assignments.header_overrides[key]) {
							this.options.assignments.header_overrides[key] = {
								enabled: false,
								mode: 'inherit',
								template_id: 0,
							};
						}
					}.bind(this));
				},

				ensureSocials: function () {
					if (!this.options.header) {
						this.options.header = {};
					}
					if (!this.options.header.top) {
						this.options.header.top = {};
					}
					if (!Array.isArray(this.options.header.top.socials)) {
						this.options.header.top.socials = [];
					}
				},

				addSocial: function () {
					this.ensureSocials();
					this.options.header.top.socials.push({
						network: 'facebook',
						url: '',
					});
				},

				removeSocial: function (index) {
					this.ensureSocials();
					this.options.header.top.socials.splice(index, 1);
				},

				ensurePromos: function () {
					if (!this.options.header) {
						this.options.header = {};
					}
					if (!this.options.header.top) {
						this.options.header.top = {};
					}
					if (!Array.isArray(this.options.header.top.promos)) {
						this.options.header.top.promos = [];
					}
					if (typeof this.options.header.top.promo_rotate === 'undefined') {
						this.options.header.top.promo_rotate = false;
					}
					if (!this.options.header.top.promo_interval) {
						this.options.header.top.promo_interval = 4;
					}
				},

				addPromo: function () {
					this.ensurePromos();
					this.options.header.top.promos.push({ text: '' });
				},

				removePromo: function (index) {
					this.ensurePromos();
					this.options.header.top.promos.splice(index, 1);
				},

				applyPreset: function (name) {
					var packs = {
						midnight: {
							general: {
								primary_color: '#0b1220',
								accent_color: '#38bdf8',
								text_color: '#e2e8f0',
								muted_color: '#94a3b8',
								bg_color: '#020617',
								surface_color: '#0f172a',
								border_color: '#1e293b',
								link_color: '#38bdf8',
								link_hover: '#7dd3fc',
								button_bg: '#38bdf8',
								button_text: '#0b1220',
								font_body_id: 'inter',
								font_heading_id: 'inter',
							},
							header: {
								top: { bg: '#0b1220', text: '#e5e7eb', muted: '#94a3b8', accent: '#38bdf8' },
								middle: { bg: '#111827', text: '#f8fafc', muted: '#94a3b8', accent: '#38bdf8', border: '#1f2937' },
								bottom: { bg: '#020617', text: '#f8fafc', muted: '#94a3b8', accent: '#38bdf8', accent_text: '#0b1220' },
								announce: { bg: '#0ea5e9', text_color: '#082f49' },
							},
							footer: { bg: '#020617', text: '#e2e8f0', muted: '#94a3b8', accent: '#38bdf8', border: '#1e293b' },
						},
						clean: {
							general: {
								primary_color: '#0f172a',
								accent_color: '#2563eb',
								text_color: '#0f172a',
								muted_color: '#64748b',
								bg_color: '#ffffff',
								surface_color: '#f8fafc',
								border_color: '#e2e8f0',
								link_color: '#2563eb',
								link_hover: '#1d4ed8',
								button_bg: '#2563eb',
								button_text: '#ffffff',
								font_body_id: 'dm_sans',
								font_heading_id: 'dm_sans',
							},
							header: {
								top: { bg: '#f8fafc', text: '#0f172a', muted: '#64748b', accent: '#2563eb' },
								middle: { bg: '#ffffff', text: '#0f172a', muted: '#64748b', accent: '#2563eb', border: '#e2e8f0' },
								bottom: { bg: '#0f172a', text: '#ffffff', muted: '#cbd5e1', accent: '#2563eb', accent_text: '#ffffff' },
								announce: { bg: '#2563eb', text_color: '#ffffff' },
							},
							footer: { bg: '#0f172a', text: '#e2e8f0', muted: '#94a3b8', accent: '#2563eb', border: '#1e293b' },
						},
						amber: {
							general: {
								primary_color: '#111827',
								accent_color: '#f59e0b',
								text_color: '#0f172a',
								muted_color: '#64748b',
								bg_color: '#ffffff',
								surface_color: '#fffbeb',
								border_color: '#e2e8f0',
								link_color: '#d97706',
								link_hover: '#b45309',
								button_bg: '#f59e0b',
								button_text: '#111827',
								font_body_id: 'plus_jakarta',
								font_heading_id: 'plus_jakarta',
							},
							header: {
								top: { bg: '#111827', text: '#f3f4f6', muted: '#9ca3af', accent: '#f59e0b' },
								middle: { bg: '#ffffff', text: '#0f172a', muted: '#64748b', accent: '#f59e0b', border: '#e2e8f0' },
								bottom: { bg: '#111827', text: '#ffffff', muted: '#cbd5e1', accent: '#f59e0b', accent_text: '#111827' },
								announce: { bg: '#f59e0b', text_color: '#111827' },
							},
							footer: { bg: '#111827', text: '#e2e8f0', muted: '#94a3b8', accent: '#f59e0b', border: '#1f2937' },
						},
						forest: {
							general: {
								primary_color: '#14532d',
								accent_color: '#16a34a',
								text_color: '#14532d',
								muted_color: '#4b5563',
								bg_color: '#ffffff',
								surface_color: '#f0fdf4',
								border_color: '#dcfce7',
								link_color: '#15803d',
								link_hover: '#166534',
								button_bg: '#16a34a',
								button_text: '#ffffff',
								font_body_id: 'manrope',
								font_heading_id: 'manrope',
							},
							header: {
								top: { bg: '#14532d', text: '#ecfdf5', muted: '#86efac', accent: '#4ade80' },
								middle: { bg: '#ffffff', text: '#14532d', muted: '#4b5563', accent: '#16a34a', border: '#dcfce7' },
								bottom: { bg: '#166534', text: '#ffffff', muted: '#bbf7d0', accent: '#4ade80', accent_text: '#14532d' },
								announce: { bg: '#16a34a', text_color: '#ffffff' },
							},
							footer: { bg: '#14532d', text: '#ecfdf5', muted: '#86efac', accent: '#4ade80', border: '#166534' },
						},
					};
					var pack = packs[name];
					if (!pack) {
						return;
					}
					if (!this.options.general) {
						this.options.general = {};
					}
					if (!this.options.header) {
						this.options.header = {};
					}
					if (!this.options.footer) {
						this.options.footer = {};
					}
					Object.keys(pack.general).forEach(function (key) {
						this.options.general[key] = pack.general[key];
					}.bind(this));
					['top', 'middle', 'bottom', 'announce'].forEach(function (section) {
						if (!this.options.header[section]) {
							this.options.header[section] = {};
						}
						Object.keys(pack.header[section]).forEach(function (key) {
							this.options.header[section][key] = pack.header[section][key];
						}.bind(this));
					}.bind(this));
					Object.keys(pack.footer).forEach(function (key) {
						this.options.footer[key] = pack.footer[key];
					}.bind(this));
					this.showToast((this.i18n && this.i18n.presetApplied) || 'Preset applied — Save to publish');
				},

				assetAudit: function () {
					return this.computePerfAudit();
				},

				computePerfAudit: function () {
					var o = this.options || {};
					var g = o.general || {};
					var p = o.performance || {};
					var headerMode = (o.header && o.header.mode) || 'theme';
					var footerMode = (o.footer && o.footer.mode) || 'theme';
					var blogMode = (o.blog && o.blog.mode) || 'theme';
					var pagesMode = (o.pages && o.pages.mode) || 'theme';
					var wooMode = (o.woocommerce && o.woocommerce.mode) || 'theme';
					var scriptsOn = !!g.scripts_enabled;
					var bttOn = !!(o.footer && o.footer.back_to_top && o.footer.back_to_top.enabled);
					var fonts = this.fontCatalog || [];
					var needsG = false;
					[g.font_body_id || 'system', g.font_heading_id || 'system'].forEach(function (id) {
						fonts.forEach(function (f) {
							if (f.id === id && f.google) {
								needsG = true;
							}
						});
					});
					function row(handle, type, when, status, note) {
						return { handle: handle, type: type, when: when, status: status, note: note };
					}
					return [
						row('themezur-style', 'css', 'Always', 'on', 'style.css'),
						row('themezur-design', 'css', 'Always', 'on', 'design.css'),
						row('themezur-mobile', 'css', 'Always', 'on', 'mobile.css'),
						row('themezur-google-fonts', 'css', 'Google font selected', needsG ? 'on' : 'off', 'fonts.googleapis.com'),
						row('themezur-header', 'css', 'Header = Themezur', headerMode === 'theme' ? 'on' : 'off', 'header.css'),
						row('themezur-header', 'js', 'Header + scripts', headerMode === 'theme' && scriptsOn ? 'on' : 'off', 'header.js'),
						row('themezur-footer', 'css', 'Footer Themezur / BTT', footerMode === 'theme' || bttOn ? 'on' : 'off', 'footer.css'),
						row('themezur-footer', 'js', 'Back to top + scripts', bttOn && scriptsOn ? 'on' : 'off', 'footer.js'),
						row('themezur-blog', 'css', 'Blog pages', blogMode === 'theme' ? 'cond' : 'off', 'blog.css'),
						row('themezur-pages', 'css', '404 / Search', pagesMode === 'theme' ? 'cond' : 'off', 'pages.css'),
						row('themezur-woocommerce', 'css', 'Shop / mini-cart', wooMode === 'theme' && this.woocommerce ? 'cond' : 'off', this.woocommerce ? 'woocommerce.css' : 'Woo inactive'),
						row('themezur-woocommerce', 'js', 'Sticky ATC / filters / mini-cart', wooMode === 'theme' && this.woocommerce && scriptsOn && (!!(o.woocommerce && o.woocommerce.single && o.woocommerce.single.sticky_atc) || !!(o.woocommerce && o.woocommerce.shop && o.woocommerce.shop.sidebar && o.woocommerce.shop.sidebar !== 'none') || !!(o.woocommerce && o.woocommerce.cart && o.woocommerce.cart.mini_cart)) ? 'cond' : 'off', 'woocommerce.js'),
						row('themezur-breadcrumbs', 'css', 'Breadcrumbs on', g.breadcrumbs ? 'cond' : 'off', 'breadcrumbs.css'),
						row('hello reset.css', 'css', 'Parent', p.disable_hello_reset ? 'off' : 'on', 'Hello Elementor'),
						row('hello theme.css', 'css', 'Parent', p.disable_hello_theme_style ? 'off' : 'on', 'Hello Elementor'),
						row('hello header-footer.css', 'css', 'Parent', p.disable_hello_header_footer_css ? 'off' : 'on', 'Hello Elementor'),
						row('wp-emoji', 'js', 'Core', p.disable_emoji ? 'off' : 'on', 'Emoji'),
						row('wp-embed', 'js', 'Core', p.disable_wp_embed ? 'off' : 'on', 'oEmbed'),
					];
				},

				auditStatusLabel: function (status) {
					if (status === 'on') {
						return 'On';
					}
					if (status === 'cond') {
						return 'Conditional';
					}
					return 'Off';
				},
				ensureFooter: function () {
					if (!this.options.footer) {
						this.options.footer = {};
					}
					if (!this.options.footer.columns || typeof this.options.footer.columns !== 'object') {
						this.options.footer.columns = {};
					}
					var blank = {
						enabled: true,
						title: '',
						subtitle: '',
						type: 'about',
						text: '',
						logo_source: 'none',
						logo_id: 0,
						logo_url: '',
						show_logo: false,
						show_social: false,
						cta_label: '',
						cta_url: '',
						menu_id: 0,
						links: [],
						address: '',
						phone: '',
						email: '',
						whatsapp: '',
						hours: '',
						map_url: '',
						posts_count: 3,
						posts_show_thumb: true,
						posts_show_date: true,
						posts_category: 0,
						products_count: 3,
						products_source: 'recent',
						products_show_thumb: true,
						products_show_price: true,
						shortcode: '',
						newsletter_mode: 'form',
						newsletter_text: '',
						newsletter_placeholder: 'Your email',
						newsletter_button: 'Subscribe',
						newsletter_action: '',
						newsletter_email_name: 'EMAIL',
						newsletter_shortcode: '',
					};
					var defaults = {
						'1': Object.assign({}, blank, { title: 'About', type: 'about', logo_source: 'site', show_logo: true, show_social: true }),
						'2': Object.assign({}, blank, { title: 'Quick Links', type: 'menu' }),
						'3': Object.assign({}, blank, { title: 'Customer Care', type: 'links' }),
						'4': Object.assign({}, blank, { title: 'Contact', type: 'contact' }),
					};
					['1', '2', '3', '4'].forEach(function (key) {
						if (!this.options.footer.columns[key]) {
							this.options.footer.columns[key] = defaults[key];
						} else {
							Object.keys(blank).forEach(function (field) {
								if (typeof this.options.footer.columns[key][field] === 'undefined') {
									this.options.footer.columns[key][field] = blank[field];
								}
							}.bind(this));
							if (!this.options.footer.columns[key].logo_source) {
								this.options.footer.columns[key].logo_source = this.options.footer.columns[key].show_logo
									? 'site'
									: 'none';
							}
						}
						if (!Array.isArray(this.options.footer.columns[key].links)) {
							this.options.footer.columns[key].links = [];
						}
					}.bind(this));
					if (!this.options.footer.bottom || typeof this.options.footer.bottom !== 'object') {
						this.options.footer.bottom = { enabled: true, copyright: '', show_menu: true, menu_id: 0 };
					}
					if (!this.options.footer.back_to_top || typeof this.options.footer.back_to_top !== 'object') {
						this.options.footer.back_to_top = { enabled: true, threshold: 400 };
					}
					if (typeof this.options.footer.back_to_top.enabled === 'undefined') {
						this.options.footer.back_to_top.enabled = true;
					}
					if (typeof this.options.footer.back_to_top.threshold === 'undefined') {
						this.options.footer.back_to_top.threshold = 400;
					}
				},

				ensureBlog: function () {
					if (!this.options.blog || typeof this.options.blog !== 'object') {
						this.options.blog = {};
					}
					if (!this.options.blog.mode) {
						this.options.blog.mode = 'theme';
					}
					if (!this.options.blog.container_width) {
						this.options.blog.container_width = '1100px';
					}
					if (!this.options.blog.archive || typeof this.options.blog.archive !== 'object') {
						this.options.blog.archive = {};
					}
					var archDefaults = {
						layout: 'grid',
						columns: 3,
						show_title: true,
						show_description: true,
						show_image: true,
						show_excerpt: true,
						excerpt_length: 22,
						show_date: true,
						show_author: false,
						show_category: true,
						show_read_more: true,
						read_more_text: 'Read more',
					};
					Object.keys(archDefaults).forEach(function (key) {
						if (typeof this.options.blog.archive[key] === 'undefined') {
							this.options.blog.archive[key] = archDefaults[key];
						}
					}.bind(this));
					if (!this.options.blog.single || typeof this.options.blog.single !== 'object') {
						this.options.blog.single = {};
					}
					var singleDefaults = {
						content_width: '720px',
						show_image: true,
						show_date: true,
						show_author: true,
						show_category: true,
						show_tags: true,
						show_author_box: true,
						show_nav: true,
						show_related: true,
						related_count: 3,
						show_comments: true,
					};
					Object.keys(singleDefaults).forEach(function (key) {
						if (typeof this.options.blog.single[key] === 'undefined') {
							this.options.blog.single[key] = singleDefaults[key];
						}
					}.bind(this));
				},

				ensurePages: function () {
					if (!this.options.pages || typeof this.options.pages !== 'object') {
						this.options.pages = {};
					}
					if (!this.options.pages.mode) {
						this.options.pages.mode = 'theme';
					}
					if (!this.options.pages.not_found || typeof this.options.pages.not_found !== 'object') {
						this.options.pages.not_found = {};
					}
					var nfDefaults = {
						title: 'Page not found',
						text: 'The page you are looking for may have been moved or no longer exists.',
						show_search: true,
						show_home_btn: true,
						home_label: 'Back to home',
					};
					Object.keys(nfDefaults).forEach(function (key) {
						if (typeof this.options.pages.not_found[key] === 'undefined') {
							this.options.pages.not_found[key] = nfDefaults[key];
						}
					}.bind(this));
					if (!this.options.pages.search || typeof this.options.pages.search !== 'object') {
						this.options.pages.search = {};
					}
					var searchDefaults = {
						layout: 'list',
						show_image: true,
						show_excerpt: true,
						show_type: true,
					};
					Object.keys(searchDefaults).forEach(function (key) {
						if (typeof this.options.pages.search[key] === 'undefined') {
							this.options.pages.search[key] = searchDefaults[key];
						}
					}.bind(this));
				},

				ensureWoo: function () {
					if (!this.options.woocommerce || typeof this.options.woocommerce !== 'object') {
						this.options.woocommerce = {};
					}
					if (!this.options.woocommerce.mode) {
						this.options.woocommerce.mode = 'theme';
					}
					if (!this.options.woocommerce.shop || typeof this.options.woocommerce.shop !== 'object') {
						this.options.woocommerce.shop = {};
					}
					var shopDefaults = {
						columns: 3,
						products_per_page: 12,
						card_style: 'soft',
						show_result_count: true,
						show_ordering: true,
						hover_image: true,
						new_badge_days: 14,
						wishlist_on_card: true,
						sidebar: 'none',
					};
					Object.keys(shopDefaults).forEach(function (key) {
						if (typeof this.options.woocommerce.shop[key] === 'undefined') {
							this.options.woocommerce.shop[key] = shopDefaults[key];
						}
					}.bind(this));
					if (!this.options.woocommerce.single || typeof this.options.woocommerce.single !== 'object') {
						this.options.woocommerce.single = {};
					}
					var wooSingleDefaults = {
						layout: 'classic',
						sale_percent: true,
						show_rating: true,
						show_sku: true,
						show_stock: true,
						sticky_atc: true,
						trust_note: '',
						show_related: true,
						related_count: 4,
						show_upsells: true,
						upsells_count: 4,
					};
					Object.keys(wooSingleDefaults).forEach(function (key) {
						if (typeof this.options.woocommerce.single[key] === 'undefined') {
							this.options.woocommerce.single[key] = wooSingleDefaults[key];
						}
					}.bind(this));
					if (!this.options.woocommerce.cart || typeof this.options.woocommerce.cart !== 'object') {
						this.options.woocommerce.cart = {};
					}
					var wooCartDefaults = { mini_cart: true, open_on_add: true };
					Object.keys(wooCartDefaults).forEach(function (key) {
						if (typeof this.options.woocommerce.cart[key] === 'undefined') {
							this.options.woocommerce.cart[key] = wooCartDefaults[key];
						}
					}.bind(this));
					if (!this.options.woocommerce.checkout || typeof this.options.woocommerce.checkout !== 'object') {
						this.options.woocommerce.checkout = {};
					}
					var wooCheckoutDefaults = { trust_note: '', sticky_review: true };
					Object.keys(wooCheckoutDefaults).forEach(function (key) {
						if (typeof this.options.woocommerce.checkout[key] === 'undefined') {
							this.options.woocommerce.checkout[key] = wooCheckoutDefaults[key];
						}
					}.bind(this));
					if (!this.options.woocommerce.account || typeof this.options.woocommerce.account !== 'object') {
						this.options.woocommerce.account = {};
					}
					if (typeof this.options.woocommerce.account.density === 'undefined') {
						this.options.woocommerce.account.density = 'comfortable';
					}
				},

				footerTypeLabel: function (type) {
					var map = {
						about: 'About',
						menu: 'Menu',
						links: 'Links',
						contact: 'Contact',
						posts: 'Posts',
						products: 'Products',
						newsletter: 'Newsletter',
						shortcode: 'Shortcode',
					};
					return map[type] || type || '—';
				},

				addFooterLink: function (colKey) {
					this.ensureFooter();
					if (!this.options.footer.columns[colKey]) {
						return;
					}
					if (!Array.isArray(this.options.footer.columns[colKey].links)) {
						this.options.footer.columns[colKey].links = [];
					}
					this.options.footer.columns[colKey].links.push({ label: '', url: '' });
				},

				removeFooterLink: function (colKey, index) {
					this.ensureFooter();
					if (!this.options.footer.columns[colKey] || !Array.isArray(this.options.footer.columns[colKey].links)) {
						return;
					}
					this.options.footer.columns[colKey].links.splice(index, 1);
				},

				moveFooterLink: function (colKey, index, dir) {
					this.ensureFooter();
					var links = this.options.footer.columns[colKey] && this.options.footer.columns[colKey].links;
					if (!Array.isArray(links)) {
						return;
					}
					var next = index + dir;
					if (next < 0 || next >= links.length) {
						return;
					}
					var tmp = links[index];
					links.splice(index, 1);
					links.splice(next, 0, tmp);
				},

				modeLabel: function (mode) {
					var map = {
						theme: 'Themezur built-in',
						elementor: 'Elementor template',
						none: 'Hidden',
					};
					return map[mode] || mode;
				},

				scopeLabel: function (scope) {
					var map = {
						entire_site: 'Entire site',
						front_page: 'Front page / Home',
						shop: 'Shop only',
						blog: 'Blog only',
						custom: 'Custom selection',
					};
					return map[scope] || scope;
				},

				pickLogo: function () {
					var self = this;
					if (typeof wp === 'undefined' || !wp.media) {
						self.showToast(self.i18n.error || 'Error');
						return;
					}

					if (self.mediaFrame) {
						self.mediaFrame.open();
						return;
					}

					self.mediaFrame = wp.media({
						title: (self.i18n && self.i18n.logoTitle) || 'Select logo',
						button: {
							text: (self.i18n && self.i18n.logoButton) || 'Use this logo',
						},
						library: {
							type: 'image',
						},
						multiple: false,
					});

					self.mediaFrame.on('select', function () {
						var attachment = self.mediaFrame.state().get('selection').first().toJSON();
						if (!attachment || !attachment.id) {
							return;
						}
						self.options.general.logo_id = attachment.id;
						self.options.general.logo_url =
							(attachment.sizes && attachment.sizes.medium && attachment.sizes.medium.url) ||
							attachment.url ||
							'';
					});

					self.mediaFrame.open();
				},

				removeLogo: function () {
					this.options.general.logo_id = 0;
					this.options.general.logo_url = '';
				},

				pickFooterLogo: function (colKey) {
					var self = this;
					if (typeof wp === 'undefined' || !wp.media) {
						self.showToast(self.i18n.error || 'Error');
						return;
					}
					if (!self.options.footer.columns[colKey]) {
						return;
					}

					var frame = wp.media({
						title: (self.i18n && self.i18n.logoTitle) || 'Select image',
						button: {
							text: (self.i18n && self.i18n.logoButton) || 'Use this image',
						},
						library: {
							type: 'image',
						},
						multiple: false,
					});

					frame.on('select', function () {
						var attachment = frame.state().get('selection').first().toJSON();
						if (!attachment || !attachment.id) {
							return;
						}
						self.options.footer.columns[colKey].logo_id = attachment.id;
						self.options.footer.columns[colKey].logo_url =
							(attachment.sizes && attachment.sizes.medium && attachment.sizes.medium.url) ||
							attachment.url ||
							'';
						self.options.footer.columns[colKey].logo_source = 'custom';
					});

					frame.open();
				},

				removeFooterLogo: function (colKey) {
					if (!this.options.footer.columns[colKey]) {
						return;
					}
					this.options.footer.columns[colKey].logo_id = 0;
					this.options.footer.columns[colKey].logo_url = '';
				},

				showToast: function (message) {
					var self = this;
					this.toast = message;
					if (this.toastTimer) {
						clearTimeout(this.toastTimer);
					}
					this.toastTimer = setTimeout(function () {
						self.toast = '';
					}, 2500);
				},

				loadTemplates: function (context) {
					var self = this;
					if (this.loadedTemplates[context] || this.loadingTemplates[context]) {
						return;
					}

					this.loadingTemplates[context] = true;

					var url =
						cfg.ajaxUrl +
						'?action=themezur_get_templates&context=' +
						encodeURIComponent(context) +
						'&nonce=' +
						encodeURIComponent(cfg.nonce);

					fetch(url, {
						credentials: 'same-origin',
					})
						.then(function (res) {
							return res.json();
						})
						.then(function (json) {
							self.loadingTemplates[context] = false;
							if (!json || !json.success) {
								self.showToast(self.i18n.error || 'Error');
								return;
							}
							self.templates[context] = (json.data && json.data.templates) || [];
							self.elementor = !!(json.data && json.data.elementor);
							self.loadedTemplates[context] = true;
						})
						.catch(function () {
							self.loadingTemplates[context] = false;
							self.showToast(self.i18n.error || 'Error');
						});
				},

				save: function () {
					var self = this;
					if (this.saving) {
						return;
					}
					this.saving = true;

					var body = new FormData();
					body.append('action', 'themezur_save');
					body.append('nonce', cfg.nonce);
					body.append('options', JSON.stringify(this.options));

					fetch(cfg.ajaxUrl, {
						method: 'POST',
						credentials: 'same-origin',
						body: body,
					})
						.then(function (res) {
							return res.json();
						})
						.then(function (json) {
							self.saving = false;
							if (!json || !json.success) {
								self.showToast(self.i18n.error || 'Error');
								return;
							}
							if (json.data && json.data.options) {
								self.options = deepClone(json.data.options);
							}
							self.showToast((json.data && json.data.message) || self.i18n.saved || 'Saved');
						})
						.catch(function () {
							self.saving = false;
							self.showToast(self.i18n.error || 'Error');
						});
				},
			};
		});
	});
})();
