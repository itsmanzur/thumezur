<?php
/**
 * Themezur admin panel shell.
 *
 * @package Themezur
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wrap themezur-wrap">
	<div
		id="themezur-panel"
		class="tz-panel"
		data-tz-admin-theme="dark"
		x-data="themezurPanel()"
		:data-tz-admin-theme="adminTheme"
		x-cloak
	>
		<script>
		(function () {
			try {
				var t = localStorage.getItem('themezur_admin_theme');
				if (t === 'light' || t === 'dark') {
					var p = document.getElementById('themezur-panel');
					if (p) {
						p.setAttribute('data-tz-admin-theme', t);
					}
				}
			} catch (e) {}
		})();
		</script>
		<header class="tz-panel__header">
			<div class="tz-panel__brand">
				<span class="tz-panel__mark" aria-hidden="true">Tz</span>
				<div>
					<h1><?php esc_html_e( 'Themezur', 'themezur' ); ?></h1>
					<p><?php esc_html_e( 'Header, footer, blog, shop & design controls', 'themezur' ); ?></p>
				</div>
			</div>
			<div class="tz-panel__actions">
				<span class="tz-toast" x-show="toast" x-text="toast" x-transition.opacity></span>
				<button
					type="button"
					class="tz-admin-theme-toggle"
					@click="toggleAdminTheme()"
					:aria-label="adminTheme === 'dark' ? '<?php echo esc_js( __( 'Switch to light mode', 'themezur' ) ); ?>' : '<?php echo esc_js( __( 'Switch to dark mode', 'themezur' ) ); ?>'"
					:title="adminTheme === 'dark' ? '<?php echo esc_js( __( 'Light mode', 'themezur' ) ); ?>' : '<?php echo esc_js( __( 'Dark mode', 'themezur' ) ); ?>'"
				>
					<svg class="tz-admin-theme-toggle__moon" width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M21 14.3A8.5 8.5 0 119.7 3a7 7 0 0011.3 11.3z"/></svg>
					<svg class="tz-admin-theme-toggle__sun" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/></svg>
				</button>
				<button
					type="button"
					class="button button-primary tz-save"
					@click="save()"
					:disabled="saving"
					x-show="tab !== 'help'"
				>
					<span x-text="saving ? i18n.saving : '<?php echo esc_js( __( 'Save changes', 'themezur' ) ); ?>'"></span>
				</button>
			</div>
		</header>

		<div class="tz-panel__body">
			<nav class="tz-nav" aria-label="<?php esc_attr_e( 'Themezur sections', 'themezur' ); ?>">
				<button type="button" class="tz-nav__item" :class="{ 'is-active': tab === 'dashboard' }" @click="tab = 'dashboard'"><?php esc_html_e( 'Dashboard', 'themezur' ); ?></button>
				<button type="button" class="tz-nav__item" :class="{ 'is-active': tab === 'header' }" @click="tab = 'header'; loadTemplates('header')"><?php esc_html_e( 'Header', 'themezur' ); ?></button>
				<button type="button" class="tz-nav__item" :class="{ 'is-active': tab === 'footer' }" @click="tab = 'footer'; loadTemplates('footer')"><?php esc_html_e( 'Footer', 'themezur' ); ?></button>
				<button type="button" class="tz-nav__item" :class="{ 'is-active': tab === 'blog' }" @click="tab = 'blog'"><?php esc_html_e( 'Blog', 'themezur' ); ?></button>
				<button type="button" class="tz-nav__item" :class="{ 'is-active': tab === 'pages' }" @click="tab = 'pages'"><?php esc_html_e( '404 & Search', 'themezur' ); ?></button>
				<button type="button" class="tz-nav__item" :class="{ 'is-active': tab === 'woocommerce' }" @click="tab = 'woocommerce'"><?php esc_html_e( 'WooCommerce', 'themezur' ); ?></button>
				<button type="button" class="tz-nav__item" :class="{ 'is-active': tab === 'assignments' }" @click="tab = 'assignments'"><?php esc_html_e( 'Assignments', 'themezur' ); ?></button>
				<button type="button" class="tz-nav__item" :class="{ 'is-active': tab === 'performance' }" @click="tab = 'performance'"><?php esc_html_e( 'Performance', 'themezur' ); ?></button>
				<button type="button" class="tz-nav__item" :class="{ 'is-active': tab === 'general' }" @click="tab = 'general'"><?php esc_html_e( 'General', 'themezur' ); ?></button>
				<button type="button" class="tz-nav__item" :class="{ 'is-active': tab === 'help' }" @click="tab = 'help'"><?php esc_html_e( 'Help / Docs', 'themezur' ); ?></button>
			</nav>

			<main class="tz-content">
				<section x-show="tab === 'dashboard'" class="tz-section">
					<h2><?php esc_html_e( 'Overview', 'themezur' ); ?></h2>
					<p class="tz-muted"><?php esc_html_e( 'Active sources for the current saved settings.', 'themezur' ); ?></p>
					<div class="tz-cards">
						<article class="tz-card">
							<h3><?php esc_html_e( 'Header', 'themezur' ); ?></h3>
							<p class="tz-stat" x-text="modeLabel(options.header.mode)"></p>
							<p class="tz-muted" x-show="options.header.mode === 'elementor'">
								<?php esc_html_e( 'Template ID:', 'themezur' ); ?>
								<strong x-text="options.header.template_id || '—'"></strong>
							</p>
						</article>
						<article class="tz-card">
							<h3><?php esc_html_e( 'Footer', 'themezur' ); ?></h3>
							<p class="tz-stat" x-text="modeLabel(options.footer.mode)"></p>
							<p class="tz-muted" x-show="options.footer.mode === 'elementor'">
								<?php esc_html_e( 'Template ID:', 'themezur' ); ?>
								<strong x-text="options.footer.template_id || '—'"></strong>
							</p>
						</article>
						<article class="tz-card">
							<h3><?php esc_html_e( 'Assignments', 'themezur' ); ?></h3>
							<p class="tz-stat" x-text="scopeLabel(options.assignments.scope)"></p>
							<p class="tz-muted">
								<?php esc_html_e( 'Excluded IDs:', 'themezur' ); ?>
								<strong x-text="(options.assignments.exclude_ids || []).join(', ') || '—'"></strong>
							</p>
						</article>
						<article class="tz-card">
							<h3><?php esc_html_e( 'Blog', 'themezur' ); ?></h3>
							<p class="tz-stat" x-text="options.blog.mode === 'theme' ? '<?php echo esc_js( __( 'Themezur blog', 'themezur' ) ); ?>' : '<?php echo esc_js( __( 'Hello default', 'themezur' ) ); ?>'"></p>
							<p class="tz-muted" x-show="options.blog.mode === 'theme'" x-text="(options.blog.archive.layout === 'list' ? '<?php echo esc_js( __( 'List', 'themezur' ) ); ?>' : '<?php echo esc_js( __( 'Grid', 'themezur' ) ); ?>') + ' · ' + (options.blog.archive.columns || 3) + ' col'"></p>
						</article>
						<article class="tz-card">
							<h3><?php esc_html_e( 'Elementor', 'themezur' ); ?></h3>
							<p class="tz-stat" x-text="elementor ? '<?php echo esc_js( __( 'Active', 'themezur' ) ); ?>' : '<?php echo esc_js( __( 'Not detected', 'themezur' ) ); ?>'"></p>
						</article>
					</div>
				</section>

				<?php require THEMEZUR_DIR . '/admin/views/header-settings.php'; ?>
				<?php require THEMEZUR_DIR . '/admin/views/footer-settings.php'; ?>
				<?php require THEMEZUR_DIR . '/admin/views/blog-settings.php'; ?>
				<?php require THEMEZUR_DIR . '/admin/views/pages-settings.php'; ?>
				<?php require THEMEZUR_DIR . '/admin/views/woocommerce-settings.php'; ?>

				<section x-show="tab === 'assignments'" class="tz-section" x-cloak>
					<h2><?php esc_html_e( 'Assignments', 'themezur' ); ?></h2>
					<p class="tz-muted"><?php esc_html_e( 'Where Themezur modes apply, plus optional Home / Shop / Blog header overrides.', 'themezur' ); ?></p>

					<div class="tz-group">
						<div class="tz-group__head">
							<div>
								<h3 class="tz-group__title"><?php esc_html_e( 'Scope', 'themezur' ); ?></h3>
								<p class="tz-group__desc"><?php esc_html_e( 'Outside this scope, Themezur falls back to the built-in (theme) header/footer — ignoring Elementor / None.', 'themezur' ); ?></p>
							</div>
						</div>
						<div class="tz-group__body">
							<div class="tz-field">
								<label for="tz-scope"><?php esc_html_e( 'Apply Themezur modes on', 'themezur' ); ?></label>
								<select id="tz-scope" x-model="options.assignments.scope">
									<option value="entire_site"><?php esc_html_e( 'Entire site', 'themezur' ); ?></option>
									<option value="front_page"><?php esc_html_e( 'Front page / Home only', 'themezur' ); ?></option>
									<option value="shop"><?php esc_html_e( 'Shop only (WooCommerce)', 'themezur' ); ?></option>
									<option value="blog"><?php esc_html_e( 'Blog only', 'themezur' ); ?></option>
									<option value="custom"><?php esc_html_e( 'Custom selection…', 'themezur' ); ?></option>
								</select>
							</div>
							<div class="tz-toggles" x-show="options.assignments.scope === 'custom'" style="margin-top:10px;">
								<label class="tz-toggle"><input type="checkbox" x-model="options.assignments.include.front_page"><span><?php esc_html_e( 'Front page / Home', 'themezur' ); ?></span></label>
								<label class="tz-toggle"><input type="checkbox" x-model="options.assignments.include.shop"><span><?php esc_html_e( 'Shop (products & categories)', 'themezur' ); ?></span></label>
								<label class="tz-toggle"><input type="checkbox" x-model="options.assignments.include.blog"><span><?php esc_html_e( 'Blog (posts & archives)', 'themezur' ); ?></span></label>
								<label class="tz-toggle"><input type="checkbox" x-model="options.assignments.include.other"><span><?php esc_html_e( 'Everything else (pages, etc.)', 'themezur' ); ?></span></label>
							</div>
							<div class="tz-field" style="margin-top:14px;">
								<label for="tz-exclude"><?php esc_html_e( 'Exclude post/page IDs', 'themezur' ); ?></label>
								<input
									id="tz-exclude"
									type="text"
									:value="(options.assignments.exclude_ids || []).join(', ')"
									@input="options.assignments.exclude_ids = $event.target.value.split(/[\s,]+/).filter(Boolean).map((n) => parseInt(n, 10)).filter((n) => n > 0)"
									placeholder="12, 45, 78"
								>
								<p class="tz-hint"><?php esc_html_e( 'Comma-separated IDs. On those singular pages, Themezur always falls back to the built-in header/footer.', 'themezur' ); ?></p>
							</div>
						</div>
					</div>

					<div class="tz-group">
						<div class="tz-group__head">
							<div>
								<h3 class="tz-group__title"><?php esc_html_e( 'Conditional headers', 'themezur' ); ?></h3>
								<p class="tz-group__desc"><?php esc_html_e( 'Override the global Header → Source mode for specific contexts. “Inherit” uses the global setting.', 'themezur' ); ?></p>
							</div>
						</div>
						<div class="tz-group__body">
							<div class="tz-cond-card">
								<div class="tz-cond-card__head">
									<strong><?php esc_html_e( 'Front page / Home', 'themezur' ); ?></strong>
									<label class="tz-field--row" style="margin:0;padding:0;border:0;">
										<input type="checkbox" x-model="options.assignments.header_overrides.front_page.enabled">
										<span><?php esc_html_e( 'Override', 'themezur' ); ?></span>
									</label>
								</div>
								<div class="tz-cond-card__body" x-show="options.assignments.header_overrides.front_page.enabled">
									<div class="tz-field">
										<label><?php esc_html_e( 'Header mode', 'themezur' ); ?></label>
										<select
											x-model="options.assignments.header_overrides.front_page.mode"
											@change="options.assignments.header_overrides.front_page.mode === 'elementor' && loadTemplates('header')"
										>
											<option value="inherit"><?php esc_html_e( 'Inherit (global Header setting)', 'themezur' ); ?></option>
											<option value="theme"><?php esc_html_e( 'Themezur header (3-row)', 'themezur' ); ?></option>
											<option value="elementor"><?php esc_html_e( 'Elementor template', 'themezur' ); ?></option>
											<option value="none"><?php esc_html_e( 'None (hide header)', 'themezur' ); ?></option>
										</select>
									</div>
									<div class="tz-field" x-show="options.assignments.header_overrides.front_page.mode === 'elementor'">
										<label><?php esc_html_e( 'Elementor template', 'themezur' ); ?></label>
										<p class="tz-hint" x-show="!elementor"><?php esc_html_e( 'Elementor is not active.', 'themezur' ); ?></p>
										<button type="button" class="button" x-show="elementor && !loadedTemplates.header" @click="loadTemplates('header')" :disabled="loadingTemplates.header">
											<span x-text="loadingTemplates.header ? (i18n.loading || 'Loading…') : (i18n.loadTemplates || 'Load templates')"></span>
										</button>
										<select
											x-show="elementor && loadedTemplates.header"
											x-model.number="options.assignments.header_overrides.front_page.template_id"
										>
											<option :value="0"><?php esc_html_e( '— Select —', 'themezur' ); ?></option>
											<template x-for="tpl in templates.header" :key="tpl.id">
												<option :value="tpl.id" x-text="tpl.title"></option>
											</template>
										</select>
									</div>
								</div>
							</div>

							<div class="tz-cond-card">
								<div class="tz-cond-card__head">
									<strong><?php esc_html_e( 'Shop (WooCommerce)', 'themezur' ); ?></strong>
									<label class="tz-field--row" style="margin:0;padding:0;border:0;">
										<input type="checkbox" x-model="options.assignments.header_overrides.shop.enabled">
										<span><?php esc_html_e( 'Override', 'themezur' ); ?></span>
									</label>
								</div>
								<div class="tz-cond-card__body" x-show="options.assignments.header_overrides.shop.enabled">
									<div class="tz-field">
										<label><?php esc_html_e( 'Header mode', 'themezur' ); ?></label>
										<select
											x-model="options.assignments.header_overrides.shop.mode"
											@change="options.assignments.header_overrides.shop.mode === 'elementor' && loadTemplates('header')"
										>
											<option value="inherit"><?php esc_html_e( 'Inherit (global Header setting)', 'themezur' ); ?></option>
											<option value="theme"><?php esc_html_e( 'Themezur header (3-row)', 'themezur' ); ?></option>
											<option value="elementor"><?php esc_html_e( 'Elementor template', 'themezur' ); ?></option>
											<option value="none"><?php esc_html_e( 'None (hide header)', 'themezur' ); ?></option>
										</select>
									</div>
									<div class="tz-field" x-show="options.assignments.header_overrides.shop.mode === 'elementor'">
										<label><?php esc_html_e( 'Elementor template', 'themezur' ); ?></label>
										<p class="tz-hint" x-show="!elementor"><?php esc_html_e( 'Elementor is not active.', 'themezur' ); ?></p>
										<button type="button" class="button" x-show="elementor && !loadedTemplates.header" @click="loadTemplates('header')" :disabled="loadingTemplates.header">
											<span x-text="loadingTemplates.header ? (i18n.loading || 'Loading…') : (i18n.loadTemplates || 'Load templates')"></span>
										</button>
										<select
											x-show="elementor && loadedTemplates.header"
											x-model.number="options.assignments.header_overrides.shop.template_id"
										>
											<option :value="0"><?php esc_html_e( '— Select —', 'themezur' ); ?></option>
											<template x-for="tpl in templates.header" :key="tpl.id">
												<option :value="tpl.id" x-text="tpl.title"></option>
											</template>
										</select>
									</div>
								</div>
							</div>

							<div class="tz-cond-card">
								<div class="tz-cond-card__head">
									<strong><?php esc_html_e( 'Blog', 'themezur' ); ?></strong>
									<label class="tz-field--row" style="margin:0;padding:0;border:0;">
										<input type="checkbox" x-model="options.assignments.header_overrides.blog.enabled">
										<span><?php esc_html_e( 'Override', 'themezur' ); ?></span>
									</label>
								</div>
								<div class="tz-cond-card__body" x-show="options.assignments.header_overrides.blog.enabled">
									<div class="tz-field">
										<label><?php esc_html_e( 'Header mode', 'themezur' ); ?></label>
										<select
											x-model="options.assignments.header_overrides.blog.mode"
											@change="options.assignments.header_overrides.blog.mode === 'elementor' && loadTemplates('header')"
										>
											<option value="inherit"><?php esc_html_e( 'Inherit (global Header setting)', 'themezur' ); ?></option>
											<option value="theme"><?php esc_html_e( 'Themezur header (3-row)', 'themezur' ); ?></option>
											<option value="elementor"><?php esc_html_e( 'Elementor template', 'themezur' ); ?></option>
											<option value="none"><?php esc_html_e( 'None (hide header)', 'themezur' ); ?></option>
										</select>
									</div>
									<div class="tz-field" x-show="options.assignments.header_overrides.blog.mode === 'elementor'">
										<label><?php esc_html_e( 'Elementor template', 'themezur' ); ?></label>
										<p class="tz-hint" x-show="!elementor"><?php esc_html_e( 'Elementor is not active.', 'themezur' ); ?></p>
										<button type="button" class="button" x-show="elementor && !loadedTemplates.header" @click="loadTemplates('header')" :disabled="loadingTemplates.header">
											<span x-text="loadingTemplates.header ? (i18n.loading || 'Loading…') : (i18n.loadTemplates || 'Load templates')"></span>
										</button>
										<select
											x-show="elementor && loadedTemplates.header"
											x-model.number="options.assignments.header_overrides.blog.template_id"
										>
											<option :value="0"><?php esc_html_e( '— Select —', 'themezur' ); ?></option>
											<template x-for="tpl in templates.header" :key="tpl.id">
												<option :value="tpl.id" x-text="tpl.title"></option>
											</template>
										</select>
									</div>
								</div>
							</div>

							<p class="tz-hint"><?php esc_html_e( 'Shop matches WooCommerce shop, product archives, and single products. Blog matches the posts index, single posts, and post archives.', 'themezur' ); ?></p>
						</div>
					</div>

					<div class="tz-group">
						<div class="tz-group__head">
							<div>
								<h3 class="tz-group__title"><?php esc_html_e( 'Conditional footers', 'themezur' ); ?></h3>
								<p class="tz-group__desc"><?php esc_html_e( 'Override the global Footer → Source mode for Home, Shop, Blog, or Checkout.', 'themezur' ); ?></p>
							</div>
						</div>
						<div class="tz-group__body">
							<template x-for="ctx in [
								{ key: 'front_page', label: '<?php echo esc_js( __( 'Front page / Home', 'themezur' ) ); ?>' },
								{ key: 'shop', label: '<?php echo esc_js( __( 'Shop (WooCommerce)', 'themezur' ) ); ?>' },
								{ key: 'blog', label: '<?php echo esc_js( __( 'Blog', 'themezur' ) ); ?>' },
								{ key: 'checkout', label: '<?php echo esc_js( __( 'Checkout', 'themezur' ) ); ?>' }
							]" :key="ctx.key">
								<div class="tz-cond-card">
									<div class="tz-cond-card__head">
										<strong x-text="ctx.label"></strong>
										<label class="tz-field--row" style="margin:0;padding:0;border:0;">
											<input type="checkbox" x-model="options.assignments.footer_overrides[ctx.key].enabled">
											<span><?php esc_html_e( 'Override', 'themezur' ); ?></span>
										</label>
									</div>
									<div class="tz-cond-card__body" x-show="options.assignments.footer_overrides[ctx.key].enabled">
										<div class="tz-field">
											<label><?php esc_html_e( 'Footer mode', 'themezur' ); ?></label>
											<select
												x-model="options.assignments.footer_overrides[ctx.key].mode"
												@change="options.assignments.footer_overrides[ctx.key].mode === 'elementor' && loadTemplates('footer')"
											>
												<option value="inherit"><?php esc_html_e( 'Inherit (global Footer setting)', 'themezur' ); ?></option>
												<option value="theme"><?php esc_html_e( 'Themezur footer', 'themezur' ); ?></option>
												<option value="elementor"><?php esc_html_e( 'Elementor template', 'themezur' ); ?></option>
												<option value="none"><?php esc_html_e( 'None (hide footer)', 'themezur' ); ?></option>
											</select>
										</div>
										<div class="tz-field" x-show="options.assignments.footer_overrides[ctx.key].mode === 'elementor'">
											<label><?php esc_html_e( 'Elementor template', 'themezur' ); ?></label>
											<select x-model.number="options.assignments.footer_overrides[ctx.key].template_id">
												<option :value="0"><?php esc_html_e( '— Select —', 'themezur' ); ?></option>
												<template x-for="tpl in templates.footer" :key="tpl.id">
													<option :value="tpl.id" x-text="tpl.title"></option>
												</template>
											</select>
										</div>
									</div>
								</div>
							</template>
						</div>
					</div>
				</section>

				<section x-show="tab === 'performance'" class="tz-section" x-cloak>
					<h2><?php esc_html_e( 'Performance', 'themezur' ); ?></h2>
					<p class="tz-muted"><?php esc_html_e( 'Disable unused assets for a lighter frontend. Use carefully.', 'themezur' ); ?></p>
					<div class="tz-toggles">
						<label class="tz-toggle">
							<input type="checkbox" x-model="options.performance.disable_hello_reset">
							<span><?php esc_html_e( 'Disable Hello Elementor reset.css', 'themezur' ); ?></span>
						</label>
						<label class="tz-toggle">
							<input type="checkbox" x-model="options.performance.disable_hello_theme_style">
							<span><?php esc_html_e( 'Disable Hello Elementor theme.css', 'themezur' ); ?></span>
						</label>
						<label class="tz-toggle">
							<input type="checkbox" x-model="options.performance.disable_hello_header_footer_css">
							<span><?php esc_html_e( 'Disable Hello header-footer.css', 'themezur' ); ?></span>
						</label>
						<label class="tz-toggle">
							<input type="checkbox" x-model="options.performance.disable_emoji">
							<span><?php esc_html_e( 'Disable WordPress emoji scripts', 'themezur' ); ?></span>
						</label>
						<label class="tz-toggle">
							<input type="checkbox" x-model="options.performance.disable_wp_embed">
							<span><?php esc_html_e( 'Disable wp-embed script', 'themezur' ); ?></span>
						</label>
						<label class="tz-toggle">
							<input type="checkbox" x-model="options.performance.preload_google_fonts">
							<span><?php esc_html_e( 'Preconnect & Preload Google Fonts (fonts.gstatic.com)', 'themezur' ); ?></span>
						</label>
						<label class="tz-toggle">
							<input type="checkbox" x-model="options.performance.opt_cart_fragments">
							<span><?php esc_html_e( 'Optimize WooCommerce Cart Fragments (dequeue on non-shop pages for +300ms speed)', 'themezur' ); ?></span>
						</label>
						<label class="tz-toggle">
							<input type="checkbox" x-model="options.performance.remove_query_strings">
							<span><?php esc_html_e( 'Remove version query strings (?ver=x.y) for better static caching', 'themezur' ); ?></span>
						</label>
						<label class="tz-toggle">
							<input type="checkbox" x-model="options.performance.disable_jquery_migrate">
							<span><?php esc_html_e( 'Disable jQuery Migrate script on frontend', 'themezur' ); ?></span>
						</label>
						<label class="tz-toggle">
							<input type="checkbox" x-model="options.performance.instant_page_preloader">
							<span><strong>⚡ <?php esc_html_e( 'Enable Instant Page Preloader (Hover Speculation for 0ms instant page loads)', 'themezur' ); ?></strong></span>
						</label>
						<label class="tz-toggle">
							<input type="checkbox" x-model="options.performance.disable_gutenberg_css">
							<span><?php esc_html_e( 'Disable Block Library CSS (wp-block-library.css)', 'themezur' ); ?></span>
						</label>
					</div>

					<div class="tz-group" style="margin-top:18px;">
						<div class="tz-group__head">
							<div>
								<h3 class="tz-group__title"><?php esc_html_e( 'Asset audit', 'themezur' ); ?></h3>
								<p class="tz-group__desc"><?php esc_html_e( 'Expected Themezur / Hello / core assets from your current panel settings (not a live browser network scan). Conditional = loads only on matching pages.', 'themezur' ); ?></p>
							</div>
						</div>
						<div class="tz-group__body">
							<table class="tz-audit-table">
								<thead>
									<tr>
										<th><?php esc_html_e( 'Handle', 'themezur' ); ?></th>
										<th><?php esc_html_e( 'Type', 'themezur' ); ?></th>
										<th><?php esc_html_e( 'Status', 'themezur' ); ?></th>
										<th><?php esc_html_e( 'When', 'themezur' ); ?></th>
										<th><?php esc_html_e( 'Note', 'themezur' ); ?></th>
									</tr>
								</thead>
								<tbody>
									<template x-for="(row, idx) in assetAudit()" :key="'audit-' + idx">
										<tr :class="'is-' + row.status">
											<td x-text="row.handle"></td>
											<td x-text="row.type"></td>
											<td><span class="tz-audit-pill" :class="'is-' + row.status" x-text="auditStatusLabel(row.status)"></span></td>
											<td x-text="row.when"></td>
											<td x-text="row.note"></td>
										</tr>
									</template>
								</tbody>
							</table>
						</div>
					</div>
				</section>

				<section x-show="tab === 'general'" class="tz-section" x-cloak>
					<h2><?php esc_html_e( 'General', 'themezur' ); ?></h2>
					<p class="tz-muted"><?php esc_html_e( 'Site-wide branding, presets, typography, and Themezur defaults.', 'themezur' ); ?></p>

					<div class="tz-subtabs" role="tablist">
						<button type="button" class="tz-subtabs__btn" :class="{ 'is-active': generalSubTab === 'brand' }" @click="generalSubTab = 'brand'"><?php esc_html_e( 'Brand', 'themezur' ); ?></button>
						<button type="button" class="tz-subtabs__btn" :class="{ 'is-active': generalSubTab === 'colors' }" @click="generalSubTab = 'colors'"><?php esc_html_e( 'Colors', 'themezur' ); ?></button>
						<button type="button" class="tz-subtabs__btn" :class="{ 'is-active': generalSubTab === 'type' }" @click="generalSubTab = 'type'"><?php esc_html_e( 'Type & layout', 'themezur' ); ?></button>
					</div>

					<div x-show="generalSubTab === 'brand'" x-cloak>
						<div class="tz-group">
							<div class="tz-group__head">
								<div>
									<h3 class="tz-group__title"><?php esc_html_e( 'Theme presets', 'themezur' ); ?></h3>
									<p class="tz-group__desc"><?php esc_html_e( 'One click applies global colors + header + footer. Then Save to publish.', 'themezur' ); ?></p>
								</div>
							</div>
							<div class="tz-group__body">
								<div class="tz-preset-row">
									<button type="button" class="button" @click="applyPreset('midnight')"><?php esc_html_e( 'Midnight', 'themezur' ); ?></button>
									<button type="button" class="button" @click="applyPreset('clean')"><?php esc_html_e( 'Clean Light', 'themezur' ); ?></button>
									<button type="button" class="button" @click="applyPreset('amber')"><?php esc_html_e( 'Amber Store', 'themezur' ); ?></button>
									<button type="button" class="button" @click="applyPreset('forest')"><?php esc_html_e( 'Forest', 'themezur' ); ?></button>
								</div>
							</div>
						</div>

						<div class="tz-group">
							<div class="tz-group__head">
								<div>
									<h3 class="tz-group__title"><?php esc_html_e( 'Site logo', 'themezur' ); ?></h3>
									<p class="tz-group__desc"><?php esc_html_e( 'Controlled only from Themezur — not Appearance → Customize.', 'themezur' ); ?></p>
								</div>
							</div>
							<div class="tz-group__body">
								<div class="tz-field">
									<div class="tz-logo-picker">
										<div class="tz-logo-picker__preview" x-show="options.general.logo_url">
											<img :src="options.general.logo_url" alt="" />
										</div>
										<div class="tz-logo-picker__empty" x-show="!options.general.logo_url">
											<?php esc_html_e( 'No logo selected — site title will show in the header.', 'themezur' ); ?>
										</div>
										<div class="tz-logo-picker__actions">
											<button type="button" class="button button-primary" @click="pickLogo()"><?php esc_html_e( 'Upload / Select logo', 'themezur' ); ?></button>
											<button type="button" class="button" x-show="options.general.logo_id" @click="removeLogo()"><?php esc_html_e( 'Remove logo', 'themezur' ); ?></button>
										</div>
									</div>
								</div>
								<div class="tz-field">
									<label><?php esc_html_e( 'Second logo (optional)', 'themezur' ); ?></label>
									<p class="tz-hint"><?php esc_html_e( 'Shown next to the first logo in the header — e.g. a plugin icon beside a brand wordmark.', 'themezur' ); ?></p>
									<div class="tz-logo-picker">
										<div class="tz-logo-picker__preview" x-show="options.general.logo_url_2">
											<img :src="options.general.logo_url_2" alt="" />
										</div>
										<div class="tz-logo-picker__empty" x-show="!options.general.logo_url_2">
											<?php esc_html_e( 'No second logo selected.', 'themezur' ); ?>
										</div>
										<div class="tz-logo-picker__actions">
											<button type="button" class="button button-primary" @click="pickLogo2()"><?php esc_html_e( 'Upload / Select second logo', 'themezur' ); ?></button>
											<button type="button" class="button" x-show="options.general.logo_id_2" @click="removeLogo2()"><?php esc_html_e( 'Remove second logo', 'themezur' ); ?></button>
										</div>
									</div>
								</div>
								<div class="tz-field tz-field--row" x-show="options.general.logo_id_2">
									<label>
										<input type="checkbox" x-model="options.general.logo_divider">
										<?php esc_html_e( 'Show divider line between the two logos', 'themezur' ); ?>
									</label>
								</div>
								<div class="tz-field tz-field--row">
									<label>
										<input type="checkbox" x-model="options.general.show_tagline">
										<?php esc_html_e( 'Show site tagline under logo / title', 'themezur' ); ?>
									</label>
								</div>
								<div class="tz-field tz-field--row">
									<label>
										<input type="checkbox" x-model="options.general.scripts_enabled">
										<?php esc_html_e( 'Enable Themezur frontend script hooks', 'themezur' ); ?>
									</label>
								</div>
							</div>
						</div>
					</div>

					<div x-show="generalSubTab === 'colors'" x-cloak>
						<div class="tz-group">
							<div class="tz-group__head">
								<div>
									<h3 class="tz-group__title"><?php esc_html_e( 'Theme colors', 'themezur' ); ?></h3>
									<p class="tz-group__desc"><?php esc_html_e( 'Global CSS variables for the frontend (header/footer/blog share these).', 'themezur' ); ?></p>
								</div>
							</div>
							<div class="tz-color-grid tz-color-grid--dense">
								<div class="tz-color"><input type="color" x-model="options.general.primary_color"><label><?php esc_html_e( 'Primary', 'themezur' ); ?></label><input type="text" x-model="options.general.primary_color" maxlength="7"></div>
								<div class="tz-color"><input type="color" x-model="options.general.accent_color"><label><?php esc_html_e( 'Accent', 'themezur' ); ?></label><input type="text" x-model="options.general.accent_color" maxlength="7"></div>
								<div class="tz-color"><input type="color" x-model="options.general.text_color"><label><?php esc_html_e( 'Text', 'themezur' ); ?></label><input type="text" x-model="options.general.text_color" maxlength="7"></div>
								<div class="tz-color"><input type="color" x-model="options.general.muted_color"><label><?php esc_html_e( 'Muted', 'themezur' ); ?></label><input type="text" x-model="options.general.muted_color" maxlength="7"></div>
								<div class="tz-color"><input type="color" x-model="options.general.bg_color"><label><?php esc_html_e( 'Background', 'themezur' ); ?></label><input type="text" x-model="options.general.bg_color" maxlength="7"></div>
								<div class="tz-color"><input type="color" x-model="options.general.surface_color"><label><?php esc_html_e( 'Surface', 'themezur' ); ?></label><input type="text" x-model="options.general.surface_color" maxlength="7"></div>
								<div class="tz-color"><input type="color" x-model="options.general.border_color"><label><?php esc_html_e( 'Border', 'themezur' ); ?></label><input type="text" x-model="options.general.border_color" maxlength="7"></div>
								<div class="tz-color"><input type="color" x-model="options.general.link_color"><label><?php esc_html_e( 'Link', 'themezur' ); ?></label><input type="text" x-model="options.general.link_color" maxlength="7"></div>
								<div class="tz-color"><input type="color" x-model="options.general.link_hover"><label><?php esc_html_e( 'Link hover', 'themezur' ); ?></label><input type="text" x-model="options.general.link_hover" maxlength="7"></div>
								<div class="tz-color"><input type="color" x-model="options.general.button_bg"><label><?php esc_html_e( 'Button BG', 'themezur' ); ?></label><input type="text" x-model="options.general.button_bg" maxlength="7"></div>
								<div class="tz-color"><input type="color" x-model="options.general.button_text"><label><?php esc_html_e( 'Button text', 'themezur' ); ?></label><input type="text" x-model="options.general.button_text" maxlength="7"></div>
							</div>
						</div>
					</div>

					<div x-show="generalSubTab === 'type'" x-cloak>
						<div class="tz-group">
							<div class="tz-group__head">
								<div>
									<h3 class="tz-group__title"><?php esc_html_e( 'Typography & layout', 'themezur' ); ?></h3>
									<p class="tz-group__desc"><?php esc_html_e( 'Pick curated fonts (Google loaded when needed), size, radius, container.', 'themezur' ); ?></p>
								</div>
							</div>
							<div class="tz-group__body">
								<div class="tz-field-grid">
									<div class="tz-field">
										<label><?php esc_html_e( 'Body font', 'themezur' ); ?></label>
										<select x-model="options.general.font_body_id" @change="onFontBodyChange()">
											<template x-for="f in fontCatalog" :key="'fb-' + f.id">
												<option :value="f.id" x-text="f.label" :style="f.family ? 'font-family:' + f.family : ''"></option>
											</template>
										</select>
										<p class="tz-hint" x-show="options.general.font_body_id !== 'system'" :style="getFontStyle(options.general.font_body_id)">
											<?php esc_html_e( 'The quick brown fox — সুন্দর বাংলা ফন্ট', 'themezur' ); ?>
										</p>
										<div class="tz-weight-row" x-show="options.general.font_body_id !== 'system'">
											<span class="tz-weight-label"><?php esc_html_e( 'Body weights:', 'themezur' ); ?></span>
											<template x-for="w in getFontWeights(options.general.font_body_id)" :key="'bw-' + w">
												<label class="tz-weight-check">
													<input type="checkbox" :value="w" x-model="options.general.font_body_weights">
													<span x-text="w"></span>
												</label>
											</template>
										</div>
									</div>
									<div class="tz-field">
										<label><?php esc_html_e( 'Heading font', 'themezur' ); ?></label>
										<select x-model="options.general.font_heading_id" @change="onFontHeadingChange()">
											<template x-for="f in fontCatalog" :key="'fh-' + f.id">
												<option :value="f.id" x-text="f.label" :style="f.family ? 'font-family:' + f.family : ''"></option>
											</template>
										</select>
										<p class="tz-hint" x-show="options.general.font_heading_id !== 'system'" :style="getFontStyle(options.general.font_heading_id)">
											<?php esc_html_e( 'Bold Heading — শিরোনাম', 'themezur' ); ?>
										</p>
										<div class="tz-weight-row" x-show="options.general.font_heading_id !== 'system'">
											<span class="tz-weight-label"><?php esc_html_e( 'Heading weights:', 'themezur' ); ?></span>
											<template x-for="w in getFontWeights(options.general.font_heading_id)" :key="'hw-' + w">
												<label class="tz-weight-check">
													<input type="checkbox" :value="w" x-model="options.general.font_heading_weights">
													<span x-text="w"></span>
												</label>
											</template>
										</div>
									</div>
								</div>
								<div class="tz-field" x-show="options.general.font_body_id === 'custom'">
									<label><?php esc_html_e( 'Custom body stack', 'themezur' ); ?></label>
									<input type="text" x-model="options.general.font_body">
								</div>
								<div class="tz-field" x-show="options.general.font_heading_id === 'custom'">
									<label><?php esc_html_e( 'Custom heading stack', 'themezur' ); ?></label>
									<input type="text" x-model="options.general.font_heading">
								</div>
								<div class="tz-field-grid">
									<div class="tz-field">
										<label><?php esc_html_e( 'Base font size', 'themezur' ); ?></label>
										<input type="text" x-model="options.general.font_size" placeholder="16px">
									</div>
									<div class="tz-field">
										<label><?php esc_html_e( 'Line height', 'themezur' ); ?></label>
										<input type="text" x-model="options.general.line_height" placeholder="1.65">
									</div>
									<div class="tz-field">
										<label><?php esc_html_e( 'Corner radius', 'themezur' ); ?></label>
										<input type="text" x-model="options.general.radius" placeholder="8px">
									</div>
									<div class="tz-field">
										<label><?php esc_html_e( 'Button radius', 'themezur' ); ?></label>
										<input type="text" x-model="options.general.button_radius" placeholder="8px">
									</div>
									<div class="tz-field">
										<label><?php esc_html_e( 'Container max width', 'themezur' ); ?></label>
										<input type="text" x-model="options.general.container_width" placeholder="1200px">
									</div>
								</div>
								<div class="tz-field tz-field--row" style="margin-top:10px;">
									<label>
										<input type="checkbox" x-model="options.general.breadcrumbs">
										<?php esc_html_e( 'Show breadcrumbs (blog, shop, singles, search)', 'themezur' ); ?>
									</label>
								</div>
								<p class="tz-hint"><?php esc_html_e( 'Utility classes: .tz-container, .tz-btn, .tz-btn--ghost, .tz-surface', 'themezur' ); ?></p>
							</div>
						</div>
					</div>
				</section>

				<?php
				require_once THEMEZUR_DIR . '/admin/docs/registry.php';
				$themezur_docs = Themezur_Docs::get_sections();
				require THEMEZUR_DIR . '/admin/views/help.php';
				?>
			</main>
		</div>
	</div>
</div>
