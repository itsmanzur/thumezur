<?php
/**
 * Themezur admin — Footer settings (4-column store, improved editors).
 *
 * @package Themezur
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section x-show="tab === 'footer'" class="tz-section" x-cloak>
	<h2><?php esc_html_e( 'Footer', 'themezur' ); ?></h2>
	<p class="tz-muted"><?php esc_html_e( '4-column store footer. Turn a column off and the rest expand to fill the row.', 'themezur' ); ?></p>

	<div class="tz-group">
		<div class="tz-group__head">
			<div>
				<h3 class="tz-group__title"><?php esc_html_e( 'Source', 'themezur' ); ?></h3>
			</div>
		</div>
		<div class="tz-group__body">
			<div class="tz-field">
				<label for="tz-footer-mode"><?php esc_html_e( 'Source mode', 'themezur' ); ?></label>
				<select id="tz-footer-mode" x-model="options.footer.mode" @change="options.footer.mode === 'elementor' && loadTemplates('footer')">
					<option value="theme"><?php esc_html_e( 'Themezur footer (4-column)', 'themezur' ); ?></option>
					<option value="elementor"><?php esc_html_e( 'Elementor template', 'themezur' ); ?></option>
					<option value="none"><?php esc_html_e( 'None (hide footer)', 'themezur' ); ?></option>
				</select>
			</div>
			<div class="tz-field" x-show="options.footer.mode === 'elementor'">
				<label for="tz-footer-tpl"><?php esc_html_e( 'Elementor template', 'themezur' ); ?></label>
				<p class="tz-muted" x-show="loadingTemplates.footer" x-text="i18n.loading"></p>
				<select id="tz-footer-tpl" x-model.number="options.footer.template_id" x-show="!loadingTemplates.footer">
					<option value="0"><?php esc_html_e( '— Select template —', 'themezur' ); ?></option>
					<template x-for="tpl in templates.footer" :key="tpl.id">
						<option :value="tpl.id" x-text="tpl.title + ' (' + tpl.type + ')'"></option>
					</template>
				</select>
				<p class="tz-hint" x-show="!loadingTemplates.footer && !templates.footer.length" x-text="i18n.noTpl"></p>
				<p class="tz-warn" x-show="!elementor"><?php esc_html_e( 'Elementor is not active. Activate Elementor to use this mode.', 'themezur' ); ?></p>
			</div>
		</div>
	</div>

	<div x-show="options.footer.mode === 'theme'">
		<div class="tz-group">
			<div class="tz-group__head">
				<div>
					<h3 class="tz-group__title"><?php esc_html_e( 'Colors & width', 'themezur' ); ?></h3>
				</div>
			</div>
			<div class="tz-color-grid">
				<div class="tz-color"><input type="color" x-model="options.footer.bg"><label><?php esc_html_e( 'Background', 'themezur' ); ?></label><input type="text" x-model="options.footer.bg" maxlength="7"></div>
				<div class="tz-color"><input type="color" x-model="options.footer.text"><label><?php esc_html_e( 'Text', 'themezur' ); ?></label><input type="text" x-model="options.footer.text" maxlength="7"></div>
				<div class="tz-color"><input type="color" x-model="options.footer.muted"><label><?php esc_html_e( 'Muted', 'themezur' ); ?></label><input type="text" x-model="options.footer.muted" maxlength="7"></div>
				<div class="tz-color"><input type="color" x-model="options.footer.accent"><label><?php esc_html_e( 'Accent', 'themezur' ); ?></label><input type="text" x-model="options.footer.accent" maxlength="7"></div>
				<div class="tz-color"><input type="color" x-model="options.footer.border"><label><?php esc_html_e( 'Border', 'themezur' ); ?></label><input type="text" x-model="options.footer.border" maxlength="7"></div>
			</div>
			<div class="tz-field-grid" style="margin-top:12px;">
				<div class="tz-field">
					<label><?php esc_html_e( 'Container max width', 'themezur' ); ?></label>
					<input type="text" x-model="options.footer.container_width" placeholder="1200px">
				</div>
				<div class="tz-field">
					<label><?php esc_html_e( 'Side padding', 'themezur' ); ?></label>
					<input type="text" x-model="options.footer.side_padding" placeholder="1.15rem">
				</div>
			</div>
		</div>

		<!-- Trust Badges (Pre-Footer Bar) -->
		<div class="tz-group">
			<div class="tz-group__head">
				<div>
					<h3 class="tz-group__title"><?php esc_html_e( '🛡️ Trust Badges / Pre-Footer Features Bar', 'themezur' ); ?></h3>
					<p class="tz-group__desc"><?php esc_html_e( 'Display feature highlights (Free Shipping, Return Policy, Secure Payment, Support) above footer columns.', 'themezur' ); ?></p>
				</div>
				<label class="tz-field--row" style="margin:0;padding:0;border:0;">
					<input type="checkbox" x-model="options.footer.trust_badges.enabled">
					<span><?php esc_html_e( 'Enabled', 'themezur' ); ?></span>
				</label>
			</div>
			<div class="tz-group__body" x-show="options.footer.trust_badges.enabled">
				<template x-for="(badge, idx) in options.footer.trust_badges.items" :key="'tb-' + idx">
					<div style="display:flex; gap:10px; margin-bottom:10px; align-items:center;">
						<select x-model="badge.icon" style="width:140px;">
							<option value="shipping"><?php esc_html_e( '🚚 Shipping', 'themezur' ); ?></option>
							<option value="return"><?php esc_html_e( '🔄 Return', 'themezur' ); ?></option>
							<option value="secure"><?php esc_html_e( '🛡️ Secure', 'themezur' ); ?></option>
							<option value="support"><?php esc_html_e( '💬 Support', 'themezur' ); ?></option>
						</select>
						<input type="text" x-model="badge.title" placeholder="Title" style="flex:1;">
						<input type="text" x-model="badge.subtitle" placeholder="Subtitle" style="flex:1.5;">
					</div>
				</template>
			</div>
		</div>

		<!-- Pre-Footer Newsletter Banner -->
		<div class="tz-group">
			<div class="tz-group__head">
				<div>
					<h3 class="tz-group__title"><?php esc_html_e( '✉️ Full-Width Pre-Footer Newsletter Banner', 'themezur' ); ?></h3>
					<p class="tz-group__desc"><?php esc_html_e( 'Stylish full-width newsletter subscription row right above footer columns.', 'themezur' ); ?></p>
				</div>
				<label class="tz-field--row" style="margin:0;padding:0;border:0;">
					<input type="checkbox" x-model="options.footer.newsletter_row.enabled">
					<span><?php esc_html_e( 'Enabled', 'themezur' ); ?></span>
				</label>
			</div>
			<div class="tz-group__body" x-show="options.footer.newsletter_row.enabled">
				<div class="tz-field-grid">
					<div class="tz-field">
						<label><?php esc_html_e( 'Banner Title', 'themezur' ); ?></label>
						<input type="text" x-model="options.footer.newsletter_row.title" placeholder="Subscribe to our Newsletter">
					</div>
					<div class="tz-field">
						<label><?php esc_html_e( 'Banner Subtitle', 'themezur' ); ?></label>
						<input type="text" x-model="options.footer.newsletter_row.subtitle" placeholder="Get 10% off your first order!">
					</div>
					<div class="tz-field">
						<label><?php esc_html_e( 'Form Action URL (Mailchimp / CPT)', 'themezur' ); ?></label>
						<input type="text" x-model="options.footer.newsletter_row.action" placeholder="https://...">
					</div>
					<div class="tz-field">
						<label><?php esc_html_e( 'Button Text', 'themezur' ); ?></label>
						<input type="text" x-model="options.footer.newsletter_row.button" placeholder="Subscribe Now">
					</div>
				</div>
			</div>
		</div>

		<div class="tz-group">
			<div class="tz-group__head">
				<div>
					<h3 class="tz-group__title"><?php esc_html_e( 'Columns overview', 'themezur' ); ?></h3>
					<p class="tz-group__desc"><?php esc_html_e( 'Quick enable/disable and jump into a column editor. Off columns free space for the rest.', 'themezur' ); ?></p>
				</div>
			</div>
			<div class="tz-ft-overview">
				<template x-for="colKey in ['1','2','3','4','5']" :key="'ov-' + colKey">
					<button
						type="button"
						class="tz-ft-overview__card"
						:class="{ 'is-active': footerSubTab === colKey, 'is-off': !options.footer.columns[colKey].enabled }"
						@click="footerSubTab = colKey"
					>
						<div class="tz-ft-overview__top">
							<strong x-text="'Col ' + colKey"></strong>
							<label class="tz-ft-overview__switch" @click.stop>
								<input type="checkbox" x-model="options.footer.columns[colKey].enabled">
							</label>
						</div>
						<span class="tz-ft-overview__title" x-text="options.footer.columns[colKey].title || '—'"></span>
						<span class="tz-ft-overview__type" x-text="footerTypeLabel(options.footer.columns[colKey].type)"></span>
					</button>
				</template>
				<button
					type="button"
					class="tz-ft-overview__card tz-ft-overview__card--bottom"
					:class="{ 'is-active': footerSubTab === 'bottom' }"
					@click="footerSubTab = 'bottom'"
				>
					<div class="tz-ft-overview__top"><strong><?php esc_html_e( 'Bottom', 'themezur' ); ?></strong></div>
					<span class="tz-ft-overview__title"><?php esc_html_e( 'Copyright & menu', 'themezur' ); ?></span>
					<span class="tz-ft-overview__type"><?php esc_html_e( 'Bar', 'themezur' ); ?></span>
				</button>
			</div>
		</div>

		<template x-for="colKey in ['1','2','3','4','5']" :key="'ed-' + colKey">
			<div class="tz-group" x-show="footerSubTab === colKey" x-cloak>
				<div class="tz-group__head">
					<div>
						<h3 class="tz-group__title" x-text="'<?php echo esc_js( __( 'Edit column', 'themezur' ) ); ?> ' + colKey"></h3>
						<p class="tz-group__desc"><?php esc_html_e( 'Pick a content type, then fill only the fields that type needs.', 'themezur' ); ?></p>
					</div>
					<label class="tz-field--row" style="margin:0;padding:0;border:0;">
						<input type="checkbox" x-model="options.footer.columns[colKey].enabled">
						<span><?php esc_html_e( 'Enabled', 'themezur' ); ?></span>
					</label>
				</div>
				<div class="tz-group__body" x-show="options.footer.columns[colKey].enabled">
					<div class="tz-field-grid">
						<div class="tz-field">
							<label><?php esc_html_e( 'Column title', 'themezur' ); ?></label>
							<input type="text" x-model="options.footer.columns[colKey].title" placeholder="<?php esc_attr_e( 'e.g. About Us', 'themezur' ); ?>">
						</div>
						<div class="tz-field">
							<label><?php esc_html_e( 'Subtitle (optional)', 'themezur' ); ?></label>
							<input type="text" x-model="options.footer.columns[colKey].subtitle" placeholder="<?php esc_attr_e( 'Short line under the title', 'themezur' ); ?>">
						</div>
					</div>

					<div class="tz-field" style="margin-top:14px;">
						<label><?php esc_html_e( 'Content type', 'themezur' ); ?></label>
						<div class="tz-type-grid" role="radiogroup">
							<button type="button" class="tz-type-card" :class="{ 'is-active': options.footer.columns[colKey].type === 'about' }" @click="options.footer.columns[colKey].type = 'about'">
								<strong><?php esc_html_e( 'About', 'themezur' ); ?></strong>
								<span><?php esc_html_e( 'Logo, story text, CTA, socials', 'themezur' ); ?></span>
							</button>
							<button type="button" class="tz-type-card" :class="{ 'is-active': options.footer.columns[colKey].type === 'menu' }" @click="options.footer.columns[colKey].type = 'menu'">
								<strong><?php esc_html_e( 'Menu', 'themezur' ); ?></strong>
								<span><?php esc_html_e( 'Pick a WP menu from Appearance', 'themezur' ); ?></span>
							</button>
							<button type="button" class="tz-type-card" :class="{ 'is-active': options.footer.columns[colKey].type === 'links' }" @click="options.footer.columns[colKey].type = 'links'">
								<strong><?php esc_html_e( 'Links', 'themezur' ); ?></strong>
								<span><?php esc_html_e( 'Custom label + URL list', 'themezur' ); ?></span>
							</button>
							<button type="button" class="tz-type-card" :class="{ 'is-active': options.footer.columns[colKey].type === 'contact' }" @click="options.footer.columns[colKey].type = 'contact'">
								<strong><?php esc_html_e( 'Contact', 'themezur' ); ?></strong>
								<span><?php esc_html_e( 'Address, phone, email, hours', 'themezur' ); ?></span>
							</button>
							<button type="button" class="tz-type-card" :class="{ 'is-active': options.footer.columns[colKey].type === 'posts' }" @click="options.footer.columns[colKey].type = 'posts'">
								<strong><?php esc_html_e( 'Posts', 'themezur' ); ?></strong>
								<span><?php esc_html_e( 'Recent blog posts with thumb', 'themezur' ); ?></span>
							</button>
							<button type="button" class="tz-type-card" :class="{ 'is-active': options.footer.columns[colKey].type === 'products' }" @click="options.footer.columns[colKey].type = 'products'">
								<strong><?php esc_html_e( 'Products', 'themezur' ); ?></strong>
								<span><?php esc_html_e( 'Recent / featured Woo products', 'themezur' ); ?></span>
							</button>
							<button type="button" class="tz-type-card" :class="{ 'is-active': options.footer.columns[colKey].type === 'newsletter' }" @click="options.footer.columns[colKey].type = 'newsletter'">
								<strong><?php esc_html_e( 'Newsletter', 'themezur' ); ?></strong>
								<span><?php esc_html_e( 'Email form or plugin shortcode', 'themezur' ); ?></span>
							</button>
							<button type="button" class="tz-type-card" :class="{ 'is-active': options.footer.columns[colKey].type === 'shortcode' }" @click="options.footer.columns[colKey].type = 'shortcode'">
								<strong><?php esc_html_e( 'Shortcode', 'themezur' ); ?></strong>
								<span><?php esc_html_e( 'Any shortcode or HTML block', 'themezur' ); ?></span>
							</button>
						</div>
					</div>

					<!-- About -->
					<div class="tz-ft-panel" x-show="options.footer.columns[colKey].type === 'about'">
						<div class="tz-field">
							<label><?php esc_html_e( 'Logo / image', 'themezur' ); ?></label>
							<div class="tz-logo-source" role="radiogroup">
								<label class="tz-logo-source__opt" :class="{ 'is-active': options.footer.columns[colKey].logo_source === 'none' }">
									<input type="radio" value="none" x-model="options.footer.columns[colKey].logo_source">
									<span><?php esc_html_e( 'None', 'themezur' ); ?></span>
								</label>
								<label class="tz-logo-source__opt" :class="{ 'is-active': options.footer.columns[colKey].logo_source === 'site' }">
									<input type="radio" value="site" x-model="options.footer.columns[colKey].logo_source">
									<span><?php esc_html_e( 'Site logo', 'themezur' ); ?></span>
								</label>
								<label class="tz-logo-source__opt" :class="{ 'is-active': options.footer.columns[colKey].logo_source === 'custom' }">
									<input type="radio" value="custom" x-model="options.footer.columns[colKey].logo_source">
									<span><?php esc_html_e( 'Custom image', 'themezur' ); ?></span>
								</label>
							</div>
							<p class="tz-hint" x-show="options.footer.columns[colKey].logo_source === 'site'">
								<?php esc_html_e( 'Uses the logo from General / Header. If none is set, the site title is shown.', 'themezur' ); ?>
							</p>
							<div class="tz-logo-picker" x-show="options.footer.columns[colKey].logo_source === 'site' && options.general.logo_url" style="margin-top:0.5rem">
								<div class="tz-logo-picker__preview"><img :src="options.general.logo_url" alt="" /></div>
							</div>
							<div class="tz-logo-picker" x-show="options.footer.columns[colKey].logo_source === 'custom'" style="margin-top:0.5rem">
								<div class="tz-logo-picker__preview" x-show="options.footer.columns[colKey].logo_url">
									<img :src="options.footer.columns[colKey].logo_url" alt="" />
								</div>
								<div class="tz-logo-picker__empty" x-show="!options.footer.columns[colKey].logo_url">
									<?php esc_html_e( 'No custom image selected.', 'themezur' ); ?>
								</div>
								<div class="tz-logo-picker__actions">
									<button type="button" class="button button-primary" @click="pickFooterLogo(colKey)"><?php esc_html_e( 'Upload / Select image', 'themezur' ); ?></button>
									<button type="button" class="button" x-show="options.footer.columns[colKey].logo_url" @click="removeFooterLogo(colKey)"><?php esc_html_e( 'Remove', 'themezur' ); ?></button>
								</div>
							</div>
						</div>
						<div class="tz-field">
							<label><?php esc_html_e( 'About text', 'themezur' ); ?></label>
							<textarea rows="4" x-model="options.footer.columns[colKey].text" placeholder="<?php esc_attr_e( 'Tell customers who you are. Use new lines for paragraphs.', 'themezur' ); ?>"></textarea>
						</div>
						<div class="tz-field-grid">
							<div class="tz-field">
								<label><?php esc_html_e( 'CTA button label', 'themezur' ); ?></label>
								<input type="text" x-model="options.footer.columns[colKey].cta_label" placeholder="<?php esc_attr_e( 'e.g. Shop now', 'themezur' ); ?>">
							</div>
							<div class="tz-field">
								<label><?php esc_html_e( 'CTA button URL', 'themezur' ); ?></label>
								<input type="url" x-model="options.footer.columns[colKey].cta_url" placeholder="https://">
							</div>
						</div>
						<div class="tz-field tz-field--row"><label><input type="checkbox" x-model="options.footer.columns[colKey].show_social"> <?php esc_html_e( 'Show social icons (from Header → Top bar)', 'themezur' ); ?></label></div>
					</div>

					<!-- Menu -->
					<div class="tz-ft-panel" x-show="options.footer.columns[colKey].type === 'menu'">
						<div class="tz-field">
							<label><?php esc_html_e( 'WordPress menu', 'themezur' ); ?></label>
							<select x-model.number="options.footer.columns[colKey].menu_id">
								<option :value="0"><?php esc_html_e( '— Select menu —', 'themezur' ); ?></option>
								<template x-for="m in menus" :key="m.id">
									<option :value="m.id" x-text="m.name"></option>
								</template>
							</select>
							<p class="tz-hint">
								<?php esc_html_e( 'Create or edit menus under Appearance → Menus, then pick it here.', 'themezur' ); ?>
								<span x-show="!menus.length"> <?php esc_html_e( 'No menus found yet.', 'themezur' ); ?></span>
							</p>
						</div>
					</div>

					<!-- Links -->
					<div class="tz-ft-panel" x-show="options.footer.columns[colKey].type === 'links'">
						<div class="tz-link-list">
							<template x-for="(link, linkIndex) in (options.footer.columns[colKey].links || [])" :key="colKey + '-l-' + linkIndex">
								<div class="tz-link-row">
									<span class="tz-link-row__num" x-text="linkIndex + 1"></span>
									<input type="text" x-model="link.label" placeholder="<?php esc_attr_e( 'Label', 'themezur' ); ?>" aria-label="<?php esc_attr_e( 'Link label', 'themezur' ); ?>">
									<input type="url" x-model="link.url" placeholder="https://" aria-label="<?php esc_attr_e( 'Link URL', 'themezur' ); ?>">
									<div class="tz-link-row__actions">
										<button type="button" class="button" @click="moveFooterLink(colKey, linkIndex, -1)" :disabled="linkIndex === 0" title="<?php esc_attr_e( 'Move up', 'themezur' ); ?>">↑</button>
										<button type="button" class="button" @click="moveFooterLink(colKey, linkIndex, 1)" :disabled="linkIndex >= (options.footer.columns[colKey].links.length - 1)" title="<?php esc_attr_e( 'Move down', 'themezur' ); ?>">↓</button>
										<button type="button" class="button tz-social-row__remove" @click="removeFooterLink(colKey, linkIndex)" aria-label="<?php esc_attr_e( 'Remove', 'themezur' ); ?>">×</button>
									</div>
								</div>
							</template>
						</div>
						<p class="tz-hint" x-show="!(options.footer.columns[colKey].links || []).length"><?php esc_html_e( 'No links yet. Add shipping, returns, FAQ, or any store pages.', 'themezur' ); ?></p>
						<button type="button" class="button button-primary" @click="addFooterLink(colKey)"><?php esc_html_e( '+ Add link', 'themezur' ); ?></button>
					</div>

					<!-- Contact -->
					<div class="tz-ft-panel" x-show="options.footer.columns[colKey].type === 'contact'">
						<div class="tz-field">
							<label><?php esc_html_e( 'Address', 'themezur' ); ?></label>
							<textarea rows="3" x-model="options.footer.columns[colKey].address" placeholder="<?php esc_attr_e( 'Street, city, country…', 'themezur' ); ?>"></textarea>
						</div>
						<div class="tz-field-grid">
							<div class="tz-field">
								<label><?php esc_html_e( 'Phone', 'themezur' ); ?></label>
								<input type="text" x-model="options.footer.columns[colKey].phone" placeholder="+880…">
							</div>
							<div class="tz-field">
								<label><?php esc_html_e( 'WhatsApp', 'themezur' ); ?></label>
								<input type="text" x-model="options.footer.columns[colKey].whatsapp" placeholder="8801XXXXXXXXX">
								<p class="tz-hint"><?php esc_html_e( 'Digits only preferred; opens wa.me link.', 'themezur' ); ?></p>
							</div>
							<div class="tz-field">
								<label><?php esc_html_e( 'Email', 'themezur' ); ?></label>
								<input type="text" x-model="options.footer.columns[colKey].email" placeholder="hello@example.com">
							</div>
							<div class="tz-field">
								<label><?php esc_html_e( 'Map URL', 'themezur' ); ?></label>
								<input type="url" x-model="options.footer.columns[colKey].map_url" placeholder="https://maps.google.com/…">
							</div>
						</div>
						<div class="tz-field">
							<label><?php esc_html_e( 'Business hours', 'themezur' ); ?></label>
							<textarea rows="3" x-model="options.footer.columns[colKey].hours" placeholder="<?php esc_attr_e( "Sat–Thu: 10am–8pm\nFriday: Closed", 'themezur' ); ?>"></textarea>
						</div>
					</div>

					<!-- Posts -->
					<div class="tz-ft-panel" x-show="options.footer.columns[colKey].type === 'posts'">
						<div class="tz-field-grid">
							<div class="tz-field">
								<label><?php esc_html_e( 'Number of posts', 'themezur' ); ?></label>
								<input type="number" min="1" max="8" x-model.number="options.footer.columns[colKey].posts_count">
							</div>
							<div class="tz-field">
								<label><?php esc_html_e( 'Category (optional)', 'themezur' ); ?></label>
								<select x-model.number="options.footer.columns[colKey].posts_category">
									<option :value="0"><?php esc_html_e( '— All categories —', 'themezur' ); ?></option>
									<template x-for="cat in categories" :key="'pc-' + cat.id">
										<option :value="cat.id" x-text="cat.name"></option>
									</template>
								</select>
							</div>
						</div>
						<div class="tz-field tz-field--row"><label><input type="checkbox" x-model="options.footer.columns[colKey].posts_show_thumb"> <?php esc_html_e( 'Show thumbnail', 'themezur' ); ?></label></div>
						<div class="tz-field tz-field--row"><label><input type="checkbox" x-model="options.footer.columns[colKey].posts_show_date"> <?php esc_html_e( 'Show date', 'themezur' ); ?></label></div>
					</div>

					<!-- Products -->
					<div class="tz-ft-panel" x-show="options.footer.columns[colKey].type === 'products'">
						<p class="tz-warn" x-show="!woocommerce"><?php esc_html_e( 'WooCommerce is not active. Activate it to show products.', 'themezur' ); ?></p>
						<div class="tz-field-grid">
							<div class="tz-field">
								<label><?php esc_html_e( 'Number of products', 'themezur' ); ?></label>
								<input type="number" min="1" max="8" x-model.number="options.footer.columns[colKey].products_count">
							</div>
							<div class="tz-field">
								<label><?php esc_html_e( 'Source', 'themezur' ); ?></label>
								<select x-model="options.footer.columns[colKey].products_source">
									<option value="recent"><?php esc_html_e( 'Recent', 'themezur' ); ?></option>
									<option value="featured"><?php esc_html_e( 'Featured', 'themezur' ); ?></option>
									<option value="on_sale"><?php esc_html_e( 'On sale', 'themezur' ); ?></option>
									<option value="top_rated"><?php esc_html_e( 'Top rated', 'themezur' ); ?></option>
								</select>
							</div>
						</div>
						<div class="tz-field tz-field--row"><label><input type="checkbox" x-model="options.footer.columns[colKey].products_show_thumb"> <?php esc_html_e( 'Show image', 'themezur' ); ?></label></div>
						<div class="tz-field tz-field--row"><label><input type="checkbox" x-model="options.footer.columns[colKey].products_show_price"> <?php esc_html_e( 'Show price', 'themezur' ); ?></label></div>
					</div>

					<!-- Newsletter -->
					<div class="tz-ft-panel" x-show="options.footer.columns[colKey].type === 'newsletter'">
						<div class="tz-field">
							<label><?php esc_html_e( 'Intro text', 'themezur' ); ?></label>
							<textarea rows="2" x-model="options.footer.columns[colKey].newsletter_text" placeholder="<?php esc_attr_e( 'Get deals & updates in your inbox.', 'themezur' ); ?>"></textarea>
						</div>
						<div class="tz-field">
							<label><?php esc_html_e( 'Mode', 'themezur' ); ?></label>
							<div class="tz-logo-source" role="radiogroup">
								<label class="tz-logo-source__opt" :class="{ 'is-active': options.footer.columns[colKey].newsletter_mode === 'form' }">
									<input type="radio" value="form" x-model="options.footer.columns[colKey].newsletter_mode">
									<span><?php esc_html_e( 'Form', 'themezur' ); ?></span>
								</label>
								<label class="tz-logo-source__opt" :class="{ 'is-active': options.footer.columns[colKey].newsletter_mode === 'shortcode' }">
									<input type="radio" value="shortcode" x-model="options.footer.columns[colKey].newsletter_mode">
									<span><?php esc_html_e( 'Shortcode', 'themezur' ); ?></span>
								</label>
							</div>
						</div>
						<div x-show="options.footer.columns[colKey].newsletter_mode === 'form'">
							<div class="tz-field-grid">
								<div class="tz-field">
									<label><?php esc_html_e( 'Placeholder', 'themezur' ); ?></label>
									<input type="text" x-model="options.footer.columns[colKey].newsletter_placeholder">
								</div>
								<div class="tz-field">
									<label><?php esc_html_e( 'Button label', 'themezur' ); ?></label>
									<input type="text" x-model="options.footer.columns[colKey].newsletter_button">
								</div>
								<div class="tz-field">
									<label><?php esc_html_e( 'Form action URL', 'themezur' ); ?></label>
									<input type="url" x-model="options.footer.columns[colKey].newsletter_action" placeholder="https://…">
									<p class="tz-hint"><?php esc_html_e( 'Mailchimp / provider embed form action. Leave empty until set.', 'themezur' ); ?></p>
								</div>
								<div class="tz-field">
									<label><?php esc_html_e( 'Email field name', 'themezur' ); ?></label>
									<input type="text" x-model="options.footer.columns[colKey].newsletter_email_name" placeholder="EMAIL">
								</div>
							</div>
						</div>
						<div class="tz-field" x-show="options.footer.columns[colKey].newsletter_mode === 'shortcode'">
							<label><?php esc_html_e( 'Newsletter shortcode', 'themezur' ); ?></label>
							<textarea rows="3" x-model="options.footer.columns[colKey].newsletter_shortcode" placeholder='[fluentform id="1"]'></textarea>
						</div>
					</div>

					<!-- Shortcode / HTML -->
					<div class="tz-ft-panel" x-show="options.footer.columns[colKey].type === 'shortcode'">
						<div class="tz-field">
							<label><?php esc_html_e( 'Shortcode or HTML', 'themezur' ); ?></label>
							<textarea rows="5" x-model="options.footer.columns[colKey].shortcode" placeholder='[gallery ids="1,2,3"]'></textarea>
							<p class="tz-hint"><?php esc_html_e( 'Paste any shortcode or allowed HTML (payment icons, trust badges, embeds).', 'themezur' ); ?></p>
						</div>
					</div>
				</div>
				<p class="tz-hint" x-show="!options.footer.columns[colKey].enabled"><?php esc_html_e( 'This column is off — enable it to edit content. Other columns will use its space.', 'themezur' ); ?></p>
			</div>
		</template>

		<div class="tz-group" x-show="footerSubTab === 'bottom'" x-cloak>
			<div class="tz-group__head">
				<div>
					<h3 class="tz-group__title"><?php esc_html_e( 'Bottom bar', 'themezur' ); ?></h3>
					<p class="tz-group__desc"><?php esc_html_e( 'Copyright line and optional secondary links.', 'themezur' ); ?></p>
				</div>
				<label class="tz-field--row" style="margin:0;padding:0;border:0;">
					<input type="checkbox" x-model="options.footer.bottom.enabled">
					<span><?php esc_html_e( 'Enabled', 'themezur' ); ?></span>
				</label>
			</div>
			<div class="tz-group__body" x-show="options.footer.bottom.enabled">
				<div class="tz-field">
					<label><?php esc_html_e( 'Copyright text', 'themezur' ); ?></label>
					<input type="text" x-model="options.footer.bottom.copyright" placeholder="<?php esc_attr_e( 'e.g. © {year} {site_name}. All rights reserved.', 'themezur' ); ?>">
					<p class="tz-hint"><?php esc_html_e( 'Use {year} for current year and {site_name} for website title.', 'themezur' ); ?></p>
				</div>
				<div class="tz-field tz-field--row"><label><input type="checkbox" x-model="options.footer.bottom.show_menu"> <?php esc_html_e( 'Show secondary menu', 'themezur' ); ?></label></div>
				<div class="tz-field" x-show="options.footer.bottom.show_menu">
					<label><?php esc_html_e( 'Bottom menu', 'themezur' ); ?></label>
					<select x-model.number="options.footer.bottom.menu_id">
						<option :value="0"><?php esc_html_e( '— Select menu —', 'themezur' ); ?></option>
						<template x-for="m in menus" :key="'bm-' + m.id">
							<option :value="m.id" x-text="m.name"></option>
						</template>
					</select>
				</div>
				<div class="tz-field tz-field--row" style="margin-top:12px;"><label><input type="checkbox" x-model="options.footer.bottom.show_payments"> <?php esc_html_e( 'Show payment method badges (bKash, Nagad, Rocket, Cash on Delivery, Visa, Mastercard, AMEX, PayPal)', 'themezur' ); ?></label></div>
			</div>
		</div>

		<div class="tz-group" x-show="footerSubTab === 'bottom'" x-cloak>
			<div class="tz-group__head">
				<div>
					<h3 class="tz-group__title"><?php esc_html_e( 'Back to top', 'themezur' ); ?></h3>
					<p class="tz-group__desc"><?php esc_html_e( 'Floating button after the visitor scrolls down. Works with Themezur or Elementor footer.', 'themezur' ); ?></p>
				</div>
				<label class="tz-field--row" style="margin:0;padding:0;border:0;">
					<input type="checkbox" x-model="options.footer.back_to_top.enabled">
					<span><?php esc_html_e( 'Enabled', 'themezur' ); ?></span>
				</label>
			</div>
			<div class="tz-group__body" x-show="options.footer.back_to_top.enabled">
				<div class="tz-field">
					<label><?php esc_html_e( 'Show after scroll (px)', 'themezur' ); ?></label>
					<input type="number" min="100" max="2000" step="50" x-model.number="options.footer.back_to_top.threshold">
				</div>
			</div>
		</div>
	</div>
</section>
