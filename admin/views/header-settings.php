<?php
/**
 * Themezur admin — Header settings (triple-row), polished groups.
 *
 * @package Themezur
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section x-show="tab === 'header'" class="tz-section" x-cloak>
	<h2><?php esc_html_e( 'Header', 'themezur' ); ?></h2>
	<p class="tz-muted"><?php esc_html_e( 'Control the 3-row Themezur header — colors, content, and typography for every element.', 'themezur' ); ?></p>

	<div class="tz-toolbar">
		<div class="tz-field">
			<label for="tz-header-mode"><?php esc_html_e( 'Source mode', 'themezur' ); ?></label>
			<select id="tz-header-mode" x-model="options.header.mode" @change="options.header.mode === 'elementor' && loadTemplates('header')">
				<option value="theme"><?php esc_html_e( 'Themezur header (3-row)', 'themezur' ); ?></option>
				<option value="elementor"><?php esc_html_e( 'Elementor template', 'themezur' ); ?></option>
				<option value="none"><?php esc_html_e( 'None (hide header)', 'themezur' ); ?></option>
			</select>
		</div>
		<div class="tz-field" x-show="options.header.mode === 'theme'" style="min-width:260px;flex:1 1 280px;">
			<label for="tz-scroll-behavior"><?php esc_html_e( 'Scroll behavior', 'themezur' ); ?></label>
			<select id="tz-scroll-behavior" x-model="options.header.scroll.behavior">
				<option value="none"><?php esc_html_e( 'None', 'themezur' ); ?></option>
				<option value="sticky"><?php esc_html_e( 'Sticky (full header)', 'themezur' ); ?></option>
				<option value="shrink"><?php esc_html_e( 'Shrink on scroll', 'themezur' ); ?></option>
				<option value="bottom_sticky"><?php esc_html_e( 'Bottom bar sticky only', 'themezur' ); ?></option>
				<option value="transparent_solid"><?php esc_html_e( 'Transparent → solid over hero', 'themezur' ); ?></option>
			</select>
		</div>
		<div class="tz-field" x-show="options.header.mode === 'theme' && options.header.scroll.behavior !== 'none'" style="min-width:140px;">
			<label><?php esc_html_e( 'Scroll offset (px)', 'themezur' ); ?></label>
			<input type="text" x-model.number="options.header.scroll.offset" placeholder="40">
		</div>
		<div class="tz-field tz-field--row" x-show="options.header.mode === 'theme'" style="align-self:end;margin-bottom:4px;">
			<label><input type="checkbox" x-model="options.header.scroll.progress"> <?php esc_html_e( 'Scroll progress line', 'themezur' ); ?></label>
		</div>
	</div>

	<div class="tz-group" x-show="options.header.mode === 'theme'">
		<div class="tz-group__head">
			<div>
				<h3 class="tz-group__title"><?php esc_html_e( 'Announcement bar', 'themezur' ); ?></h3>
				<p class="tz-group__desc"><?php esc_html_e( 'Optional sitewide message above the header. Dismiss stores a cookie so it stays hidden.', 'themezur' ); ?></p>
			</div>
			<label class="tz-field--row" style="margin:0;padding:0;border:0;"><input type="checkbox" x-model="options.header.announce.enabled"></label>
		</div>
		<div class="tz-group__body" x-show="options.header.announce.enabled">
			<div class="tz-field">
				<label><?php esc_html_e( 'Message', 'themezur' ); ?></label>
				<input type="text" x-model="options.header.announce.text" placeholder="<?php esc_attr_e( 'e.g. Free shipping on orders over ৳2000', 'themezur' ); ?>">
			</div>
			<div class="tz-field-grid">
				<div class="tz-field">
					<label><?php esc_html_e( 'Link URL (optional)', 'themezur' ); ?></label>
					<input type="url" x-model="options.header.announce.link_url" placeholder="https://">
				</div>
				<div class="tz-field">
					<label><?php esc_html_e( 'Link text', 'themezur' ); ?></label>
					<input type="text" x-model="options.header.announce.link_text">
				</div>
			</div>
			<div class="tz-color-grid" style="margin-top:12px;">
				<div class="tz-color"><input type="color" x-model="options.header.announce.bg"><label><?php esc_html_e( 'Background', 'themezur' ); ?></label><input type="text" x-model="options.header.announce.bg" maxlength="7"></div>
				<div class="tz-color"><input type="color" x-model="options.header.announce.text_color"><label><?php esc_html_e( 'Text', 'themezur' ); ?></label><input type="text" x-model="options.header.announce.text_color" maxlength="7"></div>
			</div>
			<div class="tz-field tz-field--row" style="margin-top:12px;"><label><input type="checkbox" x-model="options.header.announce.dismissible"> <?php esc_html_e( 'Allow visitors to dismiss (cookie)', 'themezur' ); ?></label></div>
			<div class="tz-field-grid" x-show="options.header.announce.dismissible">
				<div class="tz-field">
					<label><?php esc_html_e( 'Cookie days', 'themezur' ); ?></label>
					<input type="text" x-model.number="options.header.announce.cookie_days" placeholder="7">
				</div>
				<div class="tz-field">
					<label><?php esc_html_e( 'Version key', 'themezur' ); ?></label>
					<input type="text" x-model="options.header.announce.version" placeholder="1">
					<p class="tz-hint"><?php esc_html_e( 'Bump this (e.g. 2) to show the bar again after a new campaign.', 'themezur' ); ?></p>
				</div>
			</div>
		</div>
	</div>

	<div class="tz-group" x-show="options.header.mode === 'elementor'">
		<div class="tz-group__head">
			<div>
				<h3 class="tz-group__title"><?php esc_html_e( 'Elementor template', 'themezur' ); ?></h3>
				<p class="tz-group__desc"><?php esc_html_e( 'Pick a published library template to render as the site header.', 'themezur' ); ?></p>
			</div>
		</div>
		<div class="tz-group__body">
			<div class="tz-field">
				<label for="tz-header-tpl"><?php esc_html_e( 'Template', 'themezur' ); ?></label>
				<p class="tz-muted" x-show="loadingTemplates.header" x-text="i18n.loading"></p>
				<select id="tz-header-tpl" x-model.number="options.header.template_id" x-show="!loadingTemplates.header">
					<option value="0"><?php esc_html_e( '— Select template —', 'themezur' ); ?></option>
					<template x-for="tpl in templates.header" :key="tpl.id">
						<option :value="tpl.id" x-text="tpl.title + ' (' + tpl.type + ')'"></option>
					</template>
				</select>
				<p class="tz-hint" x-show="!loadingTemplates.header && !templates.header.length" x-text="i18n.noTpl"></p>
				<p class="tz-warn" x-show="!elementor"><?php esc_html_e( 'Elementor is not active. Activate Elementor to use this mode.', 'themezur' ); ?></p>
			</div>
		</div>
	</div>

	<div x-show="options.header.mode === 'theme'">
		<div class="tz-subtabs" role="tablist">
			<button type="button" class="tz-subtabs__btn" :class="{ 'is-active': headerSubTab === 'top' }" @click="headerSubTab = 'top'"><?php esc_html_e( 'Top bar', 'themezur' ); ?></button>
			<button type="button" class="tz-subtabs__btn" :class="{ 'is-active': headerSubTab === 'middle' }" @click="headerSubTab = 'middle'"><?php esc_html_e( 'Middle bar', 'themezur' ); ?></button>
			<button type="button" class="tz-subtabs__btn" :class="{ 'is-active': headerSubTab === 'bottom' }" @click="headerSubTab = 'bottom'"><?php esc_html_e( 'Bottom bar', 'themezur' ); ?></button>
			<button type="button" class="tz-subtabs__btn" :class="{ 'is-active': headerSubTab === 'typo' }" @click="headerSubTab = 'typo'"><?php esc_html_e( 'Typography', 'themezur' ); ?></button>
			<button type="button" class="tz-subtabs__btn" :class="{ 'is-active': headerSubTab === 'mobile' }" @click="headerSubTab = 'mobile'"><?php esc_html_e( 'Mobile nav', 'themezur' ); ?></button>
			<button type="button" class="tz-subtabs__btn" :class="{ 'is-active': headerSubTab === 'layout' }" @click="headerSubTab = 'layout'"><?php esc_html_e( 'Layout', 'themezur' ); ?></button>
		</div>

		<!-- Top -->
		<div x-show="headerSubTab === 'top'">
			<div class="tz-switch-row">
				<span><?php esc_html_e( 'Enable top bar', 'themezur' ); ?></span>
				<input type="checkbox" x-model="options.header.top.enabled">
			</div>

			<div class="tz-group">
				<div class="tz-group__head">
					<div>
						<h3 class="tz-group__title"><?php esc_html_e( 'Colors', 'themezur' ); ?></h3>
						<p class="tz-group__desc"><?php esc_html_e( 'Background and text colors for this row.', 'themezur' ); ?></p>
					</div>
				</div>
				<div class="tz-color-grid">
					<div class="tz-color"><input type="color" x-model="options.header.top.bg"><label><?php esc_html_e( 'Background', 'themezur' ); ?></label><input type="text" x-model="options.header.top.bg" maxlength="7"></div>
					<div class="tz-color"><input type="color" x-model="options.header.top.text"><label><?php esc_html_e( 'Text', 'themezur' ); ?></label><input type="text" x-model="options.header.top.text" maxlength="7"></div>
					<div class="tz-color"><input type="color" x-model="options.header.top.muted"><label><?php esc_html_e( 'Muted', 'themezur' ); ?></label><input type="text" x-model="options.header.top.muted" maxlength="7"></div>
					<div class="tz-color"><input type="color" x-model="options.header.top.accent"><label><?php esc_html_e( 'Accent', 'themezur' ); ?></label><input type="text" x-model="options.header.top.accent" maxlength="7"></div>
				</div>
			</div>

			<div class="tz-group">
				<div class="tz-group__head">
					<div>
						<h3 class="tz-group__title"><?php esc_html_e( 'Trending / promo', 'themezur' ); ?></h3>
						<p class="tz-group__desc"><?php esc_html_e( 'Left side label and promo message.', 'themezur' ); ?></p>
					</div>
					<label class="tz-field--row" style="margin:0;padding:0;border:0;"><input type="checkbox" x-model="options.header.top.show_trending"></label>
				</div>
				<div class="tz-group__body" x-show="options.header.top.show_trending">
					<div class="tz-field-grid">
						<div class="tz-field">
							<label><?php esc_html_e( 'Trending label', 'themezur' ); ?></label>
							<input type="text" x-model="options.header.top.trending_label">
						</div>
						<div class="tz-field">
							<label><?php esc_html_e( 'Legacy single promo text', 'themezur' ); ?></label>
							<input type="text" x-model="options.header.top.trending_text" placeholder="<?php esc_attr_e( 'Used if promo list is empty', 'themezur' ); ?>">
						</div>
					</div>
					<div class="tz-field" style="margin-top:12px;">
						<label><?php esc_html_e( 'Promo messages (rotate)', 'themezur' ); ?></label>
						<template x-for="(item, index) in (options.header.top.promos || [])" :key="index">
							<div class="tz-social-row">
								<input type="text" x-model="item.text" placeholder="<?php esc_attr_e( 'Promo message…', 'themezur' ); ?>">
								<button type="button" class="button tz-social-row__remove" @click="removePromo(index)" aria-label="<?php esc_attr_e( 'Remove', 'themezur' ); ?>">×</button>
							</div>
						</template>
						<p class="tz-hint" x-show="!(options.header.top.promos || []).length"><?php esc_html_e( 'No rotating messages yet — add some, or use the single promo text above.', 'themezur' ); ?></p>
						<button type="button" class="button" @click="addPromo()"><?php esc_html_e( '+ Add promo message', 'themezur' ); ?></button>
					</div>
					<div class="tz-field tz-field--row" style="margin-top:12px;"><label><input type="checkbox" x-model="options.header.top.promo_rotate"> <?php esc_html_e( 'Rotate messages automatically', 'themezur' ); ?></label></div>
					<div class="tz-field" x-show="options.header.top.promo_rotate" style="max-width:200px;">
						<label><?php esc_html_e( 'Interval (seconds)', 'themezur' ); ?></label>
						<input type="text" x-model.number="options.header.top.promo_interval" placeholder="4">
					</div>
					<div class="tz-vis">
						<label><input type="checkbox" x-model="options.header.top.hide_mobile_trending"> <?php esc_html_e( 'Hide on mobile', 'themezur' ); ?></label>
						<label><input type="checkbox" x-model="options.header.top.hide_desktop_trending"> <?php esc_html_e( 'Hide on desktop', 'themezur' ); ?></label>
					</div>
				</div>
			</div>

			<div class="tz-group">
				<div class="tz-group__head">
					<div>
						<h3 class="tz-group__title"><?php esc_html_e( 'Phone / hotline', 'themezur' ); ?></h3>
						<p class="tz-group__desc"><?php esc_html_e( 'Clickable tel: link in the top bar (right side).', 'themezur' ); ?></p>
					</div>
					<label class="tz-field--row" style="margin:0;padding:0;border:0;"><input type="checkbox" x-model="options.header.top.show_phone"></label>
				</div>
				<div class="tz-group__body" x-show="options.header.top.show_phone">
					<div class="tz-field-grid">
						<div class="tz-field">
							<label><?php esc_html_e( 'Label', 'themezur' ); ?></label>
							<input type="text" x-model="options.header.top.phone_label" placeholder="<?php esc_attr_e( 'Hotline', 'themezur' ); ?>">
						</div>
						<div class="tz-field">
							<label><?php esc_html_e( 'Phone number', 'themezur' ); ?></label>
							<input type="text" x-model="options.header.top.phone_number" placeholder="+880 1XXX-XXXXXX">
						</div>
					</div>
					<div class="tz-vis">
						<label><input type="checkbox" x-model="options.header.top.hide_mobile_phone"> <?php esc_html_e( 'Hide on mobile', 'themezur' ); ?></label>
						<label><input type="checkbox" x-model="options.header.top.hide_desktop_phone"> <?php esc_html_e( 'Hide on desktop', 'themezur' ); ?></label>
					</div>
				</div>
			</div>

			<div class="tz-group">
				<div class="tz-group__head">
					<div>
						<h3 class="tz-group__title"><?php esc_html_e( 'Social icons', 'themezur' ); ?></h3>
						<p class="tz-group__desc"><?php esc_html_e( 'Add any networks you need. Empty URL rows are ignored on save.', 'themezur' ); ?></p>
					</div>
					<label class="tz-field--row" style="margin:0;padding:0;border:0;"><input type="checkbox" x-model="options.header.top.show_social"></label>
				</div>
				<div class="tz-group__body" x-show="options.header.top.show_social">
					<template x-for="(item, index) in options.header.top.socials" :key="index">
						<div class="tz-social-row">
							<select x-model="item.network" aria-label="<?php esc_attr_e( 'Network', 'themezur' ); ?>">
								<?php foreach ( Themezur_Social::networks() as $value => $label ) : ?>
									<option value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $label ); ?></option>
								<?php endforeach; ?>
							</select>
							<input type="url" x-model="item.url" placeholder="https://">
							<button type="button" class="button tz-social-row__remove" @click="removeSocial(index)" aria-label="<?php esc_attr_e( 'Remove', 'themezur' ); ?>">×</button>
						</div>
					</template>
					<p class="tz-hint" x-show="!(options.header.top.socials || []).length"><?php esc_html_e( 'No social links yet.', 'themezur' ); ?></p>
					<button type="button" class="button" @click="addSocial()"><?php esc_html_e( '+ Add social link', 'themezur' ); ?></button>
					<div class="tz-vis">
						<label><input type="checkbox" x-model="options.header.top.hide_mobile_social"> <?php esc_html_e( 'Hide on mobile', 'themezur' ); ?></label>
						<label><input type="checkbox" x-model="options.header.top.hide_desktop_social"> <?php esc_html_e( 'Hide on desktop', 'themezur' ); ?></label>
					</div>
				</div>
			</div>

			<div class="tz-group">
				<div class="tz-group__head">
					<div>
						<h3 class="tz-group__title"><?php esc_html_e( 'Date / custom text', 'themezur' ); ?></h3>
						<p class="tz-group__desc"><?php esc_html_e( 'Right-side text: live date, or any custom message.', 'themezur' ); ?></p>
					</div>
					<label class="tz-field--row" style="margin:0;padding:0;border:0;"><input type="checkbox" x-model="options.header.top.show_date"></label>
				</div>
				<div class="tz-group__body" x-show="options.header.top.show_date">
					<div class="tz-field">
						<label for="tz-date-mode"><?php esc_html_e( 'Content type', 'themezur' ); ?></label>
						<select id="tz-date-mode" x-model="options.header.top.date_mode">
							<option value="date"><?php esc_html_e( 'Auto date (WordPress timezone)', 'themezur' ); ?></option>
							<option value="custom"><?php esc_html_e( 'Custom text', 'themezur' ); ?></option>
						</select>
					</div>
					<div class="tz-field" x-show="options.header.top.date_mode !== 'custom'">
						<label><?php esc_html_e( 'Date format (PHP)', 'themezur' ); ?></label>
						<input type="text" x-model="options.header.top.date_format" placeholder="j M Y">
						<p class="tz-hint"><?php esc_html_e( 'Example: j M Y → 22 Jul 2026', 'themezur' ); ?></p>
					</div>
					<div class="tz-field" x-show="options.header.top.date_mode === 'custom'">
						<label><?php esc_html_e( 'Custom text', 'themezur' ); ?></label>
						<input type="text" x-model="options.header.top.date_custom" placeholder="<?php esc_attr_e( 'e.g. Free delivery this week', 'themezur' ); ?>">
					</div>
					<div class="tz-vis">
						<label><input type="checkbox" x-model="options.header.top.hide_mobile_date"> <?php esc_html_e( 'Hide on mobile', 'themezur' ); ?></label>
						<label><input type="checkbox" x-model="options.header.top.hide_desktop_date"> <?php esc_html_e( 'Hide on desktop', 'themezur' ); ?></label>
					</div>
				</div>
			</div>
			<div class="tz-group">
				<div class="tz-group__head">
					<div>
						<h3 class="tz-group__title"><?php esc_html_e( 'Custom HTML / shortcode', 'themezur' ); ?></h3>
						<p class="tz-group__desc"><?php esc_html_e( 'Drop any shortcode or HTML in the top bar (right side) - language switcher, currency switcher, plugin badge, etc.', 'themezur' ); ?></p>
					</div>
					<label class="tz-field--row" style="margin:0;padding:0;border:0;"><input type="checkbox" x-model="options.header.top.show_custom"></label>
				</div>
				<div class="tz-group__body" x-show="options.header.top.show_custom">
					<div class="tz-field">
						<label><?php esc_html_e( 'HTML / shortcode', 'themezur' ); ?></label>
						<textarea x-model="options.header.top.custom_html" rows="3" placeholder="[my_plugin_shortcode]"></textarea>
					</div>
					<div class="tz-vis">
						<label><input type="checkbox" x-model="options.header.top.hide_mobile_custom"> <?php esc_html_e( 'Hide on mobile', 'themezur' ); ?></label>
						<label><input type="checkbox" x-model="options.header.top.hide_desktop_custom"> <?php esc_html_e( 'Hide on desktop', 'themezur' ); ?></label>
					</div>
				</div>
			</div>
		</div>


		<!-- Middle -->
		<div x-show="headerSubTab === 'middle'">
			<div class="tz-switch-row">
				<span><?php esc_html_e( 'Enable middle bar', 'themezur' ); ?></span>
				<input type="checkbox" x-model="options.header.middle.enabled">
			</div>

			<div class="tz-group">
				<div class="tz-group__head">
					<div>
						<h3 class="tz-group__title"><?php esc_html_e( 'Colors', 'themezur' ); ?></h3>
						<p class="tz-group__desc"><?php esc_html_e( 'Background, text, and search accent.', 'themezur' ); ?></p>
					</div>
				</div>
				<div class="tz-color-grid">
					<div class="tz-color"><input type="color" x-model="options.header.middle.bg"><label><?php esc_html_e( 'Background', 'themezur' ); ?></label><input type="text" x-model="options.header.middle.bg" maxlength="7"></div>
					<div class="tz-color"><input type="color" x-model="options.header.middle.text"><label><?php esc_html_e( 'Text', 'themezur' ); ?></label><input type="text" x-model="options.header.middle.text" maxlength="7"></div>
					<div class="tz-color"><input type="color" x-model="options.header.middle.muted"><label><?php esc_html_e( 'Muted', 'themezur' ); ?></label><input type="text" x-model="options.header.middle.muted" maxlength="7"></div>
					<div class="tz-color"><input type="color" x-model="options.header.middle.accent"><label><?php esc_html_e( 'Accent', 'themezur' ); ?></label><input type="text" x-model="options.header.middle.accent" maxlength="7"></div>
					<div class="tz-color"><input type="color" x-model="options.header.middle.border"><label><?php esc_html_e( 'Border', 'themezur' ); ?></label><input type="text" x-model="options.header.middle.border" maxlength="7"></div>
				</div>
			</div>

			<div class="tz-group">
				<div class="tz-group__head">
					<div>
						<h3 class="tz-group__title"><?php esc_html_e( 'Brand', 'themezur' ); ?></h3>
						<p class="tz-group__desc"><?php esc_html_e( 'Logo is managed from Themezur only — not Customizer.', 'themezur' ); ?></p>
					</div>
				</div>
				<div class="tz-group__body">
					<div class="tz-field">
						<label><?php esc_html_e( 'Logo', 'themezur' ); ?></label>
						<div class="tz-logo-picker">
							<div class="tz-logo-picker__preview" x-show="options.general.logo_url"><img :src="options.general.logo_url" alt="" /></div>
							<div class="tz-logo-picker__empty" x-show="!options.general.logo_url"><?php esc_html_e( 'No logo — site title will show instead.', 'themezur' ); ?></div>
							<div class="tz-logo-picker__actions">
								<button type="button" class="button button-primary" @click="pickLogo()"><?php esc_html_e( 'Upload / Select logo', 'themezur' ); ?></button>
								<button type="button" class="button" x-show="options.general.logo_id" @click="removeLogo()"><?php esc_html_e( 'Remove', 'themezur' ); ?></button>
							</div>
						</div>
					</div>
					<div class="tz-field tz-field--row"><label><input type="checkbox" x-model="options.header.middle.show_logo"> <?php esc_html_e( 'Show logo / site title', 'themezur' ); ?></label></div>
					<div class="tz-vis" x-show="options.header.middle.show_logo">
						<label><input type="checkbox" x-model="options.header.middle.hide_mobile_logo"> <?php esc_html_e( 'Hide on mobile', 'themezur' ); ?></label>
						<label><input type="checkbox" x-model="options.header.middle.hide_desktop_logo"> <?php esc_html_e( 'Hide on desktop', 'themezur' ); ?></label>
					</div>
					<div class="tz-field tz-field--row"><label><input type="checkbox" x-model="options.header.middle.show_address"> <?php esc_html_e( 'Show address under logo', 'themezur' ); ?></label></div>
					<div class="tz-field" x-show="options.header.middle.show_address">
						<label><?php esc_html_e( 'Address text', 'themezur' ); ?></label>
						<input type="text" x-model="options.header.middle.address">
					</div>
					<div class="tz-vis" x-show="options.header.middle.show_address">
						<label><input type="checkbox" x-model="options.header.middle.hide_mobile_address"> <?php esc_html_e( 'Hide on mobile', 'themezur' ); ?></label>
						<label><input type="checkbox" x-model="options.header.middle.hide_desktop_address"> <?php esc_html_e( 'Hide on desktop', 'themezur' ); ?></label>
					</div>
				</div>
			</div>

			<div class="tz-group">
				<div class="tz-group__head">
					<div>
						<h3 class="tz-group__title"><?php esc_html_e( 'Search & actions', 'themezur' ); ?></h3>
						<p class="tz-group__desc"><?php esc_html_e( 'Search box, dark mode, and cart icon.', 'themezur' ); ?></p>
					</div>
				</div>
				<div class="tz-group__body">
					<div class="tz-field tz-field--row"><label><input type="checkbox" x-model="options.header.middle.show_search"> <?php esc_html_e( 'Show search', 'themezur' ); ?></label></div>
					<div class="tz-field" x-show="options.header.middle.show_search">
						<label><?php esc_html_e( 'Search placeholder', 'themezur' ); ?></label>
						<input type="text" x-model="options.header.middle.search_placeholder">
					</div>
					<div class="tz-field tz-field--row" x-show="options.header.middle.show_search"><label><input type="checkbox" x-model="options.header.middle.smart_search"> <?php esc_html_e( 'Smart search (AJAX live product suggestions)', 'themezur' ); ?></label></div>
					<div class="tz-field-grid" x-show="options.header.middle.show_search && options.header.middle.smart_search">
						<div class="tz-field">
							<label><?php esc_html_e( 'Min characters', 'themezur' ); ?></label>
							<input type="text" x-model.number="options.header.middle.search_min_chars">
						</div>
						<div class="tz-field">
							<label><?php esc_html_e( 'Result limit', 'themezur' ); ?></label>
							<input type="text" x-model.number="options.header.middle.search_limit">
						</div>
					</div>
					<div class="tz-vis" x-show="options.header.middle.show_search">
						<label><input type="checkbox" x-model="options.header.middle.hide_mobile_search"> <?php esc_html_e( 'Hide on mobile', 'themezur' ); ?></label>
						<label><input type="checkbox" x-model="options.header.middle.hide_desktop_search"> <?php esc_html_e( 'Hide on desktop', 'themezur' ); ?></label>
					</div>
					<p class="tz-hint" x-show="options.header.middle.smart_search"><?php esc_html_e( 'Uses WooCommerce products when available; otherwise falls back to posts.', 'themezur' ); ?></p>
					<div class="tz-field tz-field--row"><label><input type="checkbox" x-model="options.header.middle.show_dark_mode"> <?php esc_html_e( 'Show dark mode toggle', 'themezur' ); ?></label></div>
					<div class="tz-vis" x-show="options.header.middle.show_dark_mode">
						<label><input type="checkbox" x-model="options.header.middle.hide_mobile_dark"> <?php esc_html_e( 'Hide on mobile', 'themezur' ); ?></label>
						<label><input type="checkbox" x-model="options.header.middle.hide_desktop_dark"> <?php esc_html_e( 'Hide on desktop', 'themezur' ); ?></label>
					</div>
					<div class="tz-field tz-field--row"><label><input type="checkbox" x-model="options.header.middle.show_account"> <?php esc_html_e( 'Show account / login icon', 'themezur' ); ?></label></div>
					<div class="tz-vis" x-show="options.header.middle.show_account">
						<label><input type="checkbox" x-model="options.header.middle.hide_mobile_account"> <?php esc_html_e( 'Hide on mobile', 'themezur' ); ?></label>
						<label><input type="checkbox" x-model="options.header.middle.hide_desktop_account"> <?php esc_html_e( 'Hide on desktop', 'themezur' ); ?></label>
					</div>
					<p class="tz-hint" x-show="options.header.middle.show_account"><?php esc_html_e( 'Links to WooCommerce My Account when available; otherwise WordPress login / profile.', 'themezur' ); ?></p>
					<div class="tz-field tz-field--row"><label><input type="checkbox" x-model="options.header.middle.show_wishlist"> <?php esc_html_e( 'Show wishlist icon', 'themezur' ); ?></label></div>
					<div class="tz-field" x-show="options.header.middle.show_wishlist">
						<label><?php esc_html_e( 'Wishlist URL', 'themezur' ); ?></label>
						<input type="url" x-model="options.header.middle.wishlist_url" placeholder="https://">
						<p class="tz-hint"><?php esc_html_e( 'Optional — auto-detects YITH Wishlist page when empty.', 'themezur' ); ?></p>
					</div>
					<div class="tz-vis" x-show="options.header.middle.show_wishlist">
						<label><input type="checkbox" x-model="options.header.middle.hide_mobile_wishlist"> <?php esc_html_e( 'Hide on mobile', 'themezur' ); ?></label>
						<label><input type="checkbox" x-model="options.header.middle.hide_desktop_wishlist"> <?php esc_html_e( 'Hide on desktop', 'themezur' ); ?></label>
					</div>
					<div class="tz-field tz-field--row"><label><input type="checkbox" x-model="options.header.middle.show_compare"> <?php esc_html_e( 'Show compare icon', 'themezur' ); ?></label></div>
					<div class="tz-field" x-show="options.header.middle.show_compare">
						<label><?php esc_html_e( 'Compare URL', 'themezur' ); ?></label>
						<input type="url" x-model="options.header.middle.compare_url" placeholder="https://">
					</div>
					<div class="tz-vis" x-show="options.header.middle.show_compare">
						<label><input type="checkbox" x-model="options.header.middle.hide_mobile_compare"> <?php esc_html_e( 'Hide on mobile', 'themezur' ); ?></label>
						<label><input type="checkbox" x-model="options.header.middle.hide_desktop_compare"> <?php esc_html_e( 'Hide on desktop', 'themezur' ); ?></label>
					</div>
					<div class="tz-field tz-field--row"><label><input type="checkbox" x-model="options.header.middle.show_cart"> <?php esc_html_e( 'Show cart icon', 'themezur' ); ?></label></div>
					<div class="tz-field tz-field--row" x-show="options.header.middle.show_cart && woocommerce"><label><input type="checkbox" x-model="options.header.middle.mini_cart"> <?php esc_html_e( 'Open mini-cart drawer', 'themezur' ); ?></label></div>
					<div class="tz-vis" x-show="options.header.middle.show_cart">
						<label><input type="checkbox" x-model="options.header.middle.hide_mobile_cart"> <?php esc_html_e( 'Hide on mobile', 'themezur' ); ?></label>
						<label><input type="checkbox" x-model="options.header.middle.hide_desktop_cart"> <?php esc_html_e( 'Hide on desktop', 'themezur' ); ?></label>
					</div>
					<p class="tz-hint"><?php esc_html_e( 'Cart count and mini-cart contents stay synchronized with classic WooCommerce and Cart Blocks.', 'themezur' ); ?></p>
				</div>
			</div>

			<div class="tz-group">
				<div class="tz-group__head">
					<div>
						<h3 class="tz-group__title"><?php esc_html_e( 'Middle Bar Navigation & Button', 'themezur' ); ?></h3>
						<p class="tz-group__desc"><?php esc_html_e( 'Show main menu or CTA button inside middle bar for single-row / 2-row layouts.', 'themezur' ); ?></p>
					</div>
				</div>
				<div class="tz-group__body">
					<div class="tz-field tz-field--row"><label><input type="checkbox" x-model="options.header.middle.show_menu"> <?php esc_html_e( 'Show navigation menu in middle bar', 'themezur' ); ?></label></div>
					<div class="tz-field" x-show="options.header.middle.show_menu">
						<label><?php esc_html_e( 'Select Menu', 'themezur' ); ?></label>
						<select x-model.number="options.header.middle.menu_id">
							<option value="0"><?php esc_html_e( 'Primary Menu (Default Theme Location)', 'themezur' ); ?></option>
							<template x-for="m in menus" :key="'mid-m-' + m.id">
								<option :value="m.id" x-text="m.name"></option>
							</template>
						</select>
					</div>
					<div class="tz-vis" x-show="options.header.middle.show_menu">
						<label><input type="checkbox" x-model="options.header.middle.hide_mobile_menu"> <?php esc_html_e( 'Hide on mobile', 'themezur' ); ?></label>
						<label><input type="checkbox" x-model="options.header.middle.hide_desktop_menu"> <?php esc_html_e( 'Hide on desktop', 'themezur' ); ?></label>
					</div>

					<div class="tz-field tz-field--row" style="margin-top:12px;"><label><input type="checkbox" x-model="options.header.middle.show_button"> <?php esc_html_e( 'Show custom CTA button in middle bar', 'themezur' ); ?></label></div>
					<div class="tz-field-grid" x-show="options.header.middle.show_button">
						<div class="tz-field">
							<label><?php esc_html_e( 'Button text', 'themezur' ); ?></label>
							<input type="text" x-model="options.header.middle.button_text" placeholder="Order Now">
						</div>
						<div class="tz-field">
							<label><?php esc_html_e( 'Button URL', 'themezur' ); ?></label>
							<input type="url" x-model="options.header.middle.button_url" placeholder="https://">
						</div>
					</div>
					<div class="tz-field tz-field--row" x-show="options.header.middle.show_button">
						<label><input type="checkbox" x-model="options.header.middle.button_target" :true-value="'_blank'" :false-value="'_self'"> <?php esc_html_e( 'Open link in new tab', 'themezur' ); ?></label>
					</div>
					<div class="tz-vis" x-show="options.header.middle.show_button">
						<label><input type="checkbox" x-model="options.header.middle.hide_mobile_button"> <?php esc_html_e( 'Hide on mobile', 'themezur' ); ?></label>
						<label><input type="checkbox" x-model="options.header.middle.hide_desktop_button"> <?php esc_html_e( 'Hide on desktop', 'themezur' ); ?></label>
					</div>
				</div>
			</div>
			<div class="tz-group">
				<div class="tz-group__head">
					<div>
						<h3 class="tz-group__title"><?php esc_html_e( 'Custom HTML / shortcode', 'themezur' ); ?></h3>
						<p class="tz-group__desc"><?php esc_html_e( 'Drop any shortcode or HTML among the middle-bar action icons - language switcher, currency switcher, plugin badge, etc.', 'themezur' ); ?></p>
					</div>
					<label class="tz-field--row" style="margin:0;padding:0;border:0;"><input type="checkbox" x-model="options.header.middle.show_custom"></label>
				</div>
				<div class="tz-group__body" x-show="options.header.middle.show_custom">
					<div class="tz-field">
						<label><?php esc_html_e( 'HTML / shortcode', 'themezur' ); ?></label>
						<textarea x-model="options.header.middle.custom_html" rows="3" placeholder="[my_plugin_shortcode]"></textarea>
					</div>
					<div class="tz-vis">
						<label><input type="checkbox" x-model="options.header.middle.hide_mobile_custom"> <?php esc_html_e( 'Hide on mobile', 'themezur' ); ?></label>
						<label><input type="checkbox" x-model="options.header.middle.hide_desktop_custom"> <?php esc_html_e( 'Hide on desktop', 'themezur' ); ?></label>
					</div>
				</div>
			</div>
		</div>


		<!-- Bottom -->
		<div x-show="headerSubTab === 'bottom'">
			<div class="tz-switch-row">
				<span><?php esc_html_e( 'Enable bottom bar', 'themezur' ); ?></span>
				<input type="checkbox" x-model="options.header.bottom.enabled">
			</div>

			<div class="tz-group">
				<div class="tz-group__head">
					<div>
						<h3 class="tz-group__title"><?php esc_html_e( 'Colors', 'themezur' ); ?></h3>
						<p class="tz-group__desc"><?php esc_html_e( 'Nav bar and accent button colors.', 'themezur' ); ?></p>
					</div>
				</div>
				<div class="tz-color-grid">
					<div class="tz-color"><input type="color" x-model="options.header.bottom.bg"><label><?php esc_html_e( 'Background', 'themezur' ); ?></label><input type="text" x-model="options.header.bottom.bg" maxlength="7"></div>
					<div class="tz-color"><input type="color" x-model="options.header.bottom.text"><label><?php esc_html_e( 'Text', 'themezur' ); ?></label><input type="text" x-model="options.header.bottom.text" maxlength="7"></div>
					<div class="tz-color"><input type="color" x-model="options.header.bottom.accent"><label><?php esc_html_e( 'Accent', 'themezur' ); ?></label><input type="text" x-model="options.header.bottom.accent" maxlength="7"></div>
					<div class="tz-color"><input type="color" x-model="options.header.bottom.accent_text"><label><?php esc_html_e( 'Accent text', 'themezur' ); ?></label><input type="text" x-model="options.header.bottom.accent_text" maxlength="7"></div>
				</div>
			</div>

			<div class="tz-group">
				<div class="tz-group__head">
					<div>
						<h3 class="tz-group__title"><?php esc_html_e( 'Categories button', 'themezur' ); ?></h3>
						<p class="tz-group__desc"><?php esc_html_e( 'Mega mode loads WooCommerce product categories as a dropdown panel.', 'themezur' ); ?></p>
					</div>
					<label class="tz-field--row" style="margin:0;padding:0;border:0;"><input type="checkbox" x-model="options.header.bottom.show_categories"></label>
				</div>
				<div class="tz-group__body" x-show="options.header.bottom.show_categories">
					<div class="tz-field">
						<label><?php esc_html_e( 'Button text', 'themezur' ); ?></label>
						<input type="text" x-model="options.header.bottom.categories_text">
					</div>
					<div class="tz-field">
						<label><?php esc_html_e( 'Mode', 'themezur' ); ?></label>
						<select x-model="options.header.bottom.categories_mode">
							<option value="mega"><?php esc_html_e( 'Mega dropdown (product categories)', 'themezur' ); ?></option>
							<option value="link"><?php esc_html_e( 'Simple link', 'themezur' ); ?></option>
						</select>
					</div>
					<div class="tz-field-grid" x-show="options.header.bottom.categories_mode === 'mega'">
						<div class="tz-field">
							<label><?php esc_html_e( 'Columns', 'themezur' ); ?></label>
							<select x-model.number="options.header.bottom.categories_columns">
								<option :value="2">2</option>
								<option :value="3">3</option>
								<option :value="4">4</option>
							</select>
						</div>
						<div class="tz-field">
							<label><?php esc_html_e( 'Max parent categories', 'themezur' ); ?></label>
							<input type="text" x-model.number="options.header.bottom.categories_limit">
						</div>
					</div>
					<div class="tz-field" x-show="options.header.bottom.categories_mode === 'link'">
						<label><?php esc_html_e( 'URL', 'themezur' ); ?></label>
						<input type="url" x-model="options.header.bottom.categories_url" placeholder="https://">
					</div>
					<div class="tz-vis">
						<label><input type="checkbox" x-model="options.header.bottom.hide_mobile_categories"> <?php esc_html_e( 'Hide on mobile', 'themezur' ); ?></label>
						<label><input type="checkbox" x-model="options.header.bottom.hide_desktop_categories"> <?php esc_html_e( 'Hide on desktop', 'themezur' ); ?></label>
					</div>
				</div>
			</div>

			<div class="tz-group">
				<div class="tz-group__head">
					<div>
						<h3 class="tz-group__title"><?php esc_html_e( 'Navigation', 'themezur' ); ?></h3>
						<p class="tz-group__desc"><?php esc_html_e( 'Assign a menu to Appearance → Menus → Header.', 'themezur' ); ?></p>
					</div>
					<label class="tz-field--row" style="margin:0;padding:0;border:0;"><input type="checkbox" x-model="options.header.bottom.show_menu"></label>
				</div>
				<div class="tz-group__body" x-show="options.header.bottom.show_menu">
					<div class="tz-field">
						<label><?php esc_html_e( 'Select Menu', 'themezur' ); ?></label>
						<select x-model.number="options.header.bottom.menu_id">
							<option value="0"><?php esc_html_e( 'Primary Menu (Default Theme Location)', 'themezur' ); ?></option>
							<template x-for="m in menus" :key="'bot-m-' + m.id">
								<option :value="m.id" x-text="m.name"></option>
							</template>
						</select>
					</div>
					<div class="tz-vis">
						<label><input type="checkbox" x-model="options.header.bottom.hide_mobile_menu"> <?php esc_html_e( 'Hide on mobile', 'themezur' ); ?></label>
						<label><input type="checkbox" x-model="options.header.bottom.hide_desktop_menu"> <?php esc_html_e( 'Hide on desktop', 'themezur' ); ?></label>
					</div>
					<p class="tz-hint"><?php esc_html_e( 'Select any saved menu from Appearance → Menus.', 'themezur' ); ?></p>
				</div>
			</div>

			<div class="tz-group">
				<div class="tz-group__head">
					<div>
						<h3 class="tz-group__title"><?php esc_html_e( 'Deal / CTA', 'themezur' ); ?></h3>
					</div>
					<label class="tz-field--row" style="margin:0;padding:0;border:0;"><input type="checkbox" x-model="options.header.bottom.show_deal"></label>
				</div>
				<div class="tz-group__body" x-show="options.header.bottom.show_deal">
					<div class="tz-field-grid">
						<div class="tz-field">
							<label><?php esc_html_e( 'Deal text', 'themezur' ); ?></label>
							<input type="text" x-model="options.header.bottom.deal_text">
						</div>
						<div class="tz-field">
							<label><?php esc_html_e( 'Deal URL', 'themezur' ); ?></label>
							<input type="url" x-model="options.header.bottom.deal_url" placeholder="https://">
						</div>
					</div>
					<div class="tz-vis">
						<label><input type="checkbox" x-model="options.header.bottom.hide_mobile_deal"> <?php esc_html_e( 'Hide on mobile', 'themezur' ); ?></label>
						<label><input type="checkbox" x-model="options.header.bottom.hide_desktop_deal"> <?php esc_html_e( 'Hide on desktop', 'themezur' ); ?></label>
					</div>
				</div>
			</div>
		</div>

		<!-- Typography -->
		<div x-show="headerSubTab === 'typo'">
			<div class="tz-group">
				<div class="tz-group__head">
					<div>
						<h3 class="tz-group__title"><?php esc_html_e( 'Typography', 'themezur' ); ?></h3>
						<p class="tz-group__desc"><?php esc_html_e( 'Font stack and sizes for header rows. Use CSS units (px, rem).', 'themezur' ); ?></p>
					</div>
				</div>
				<div class="tz-group__body">
					<div class="tz-field">
					<label><?php esc_html_e( 'Header font', 'themezur' ); ?></label>
					<select x-model="options.header.typo.font_id" @change="onHeaderFontChange()">
						<option value="inherit"><?php esc_html_e( 'Inherit — use global body font', 'themezur' ); ?></option>
						<template x-for="f in fontCatalog.filter(f => f.id !== 'custom')" :key="'hf-' + f.id">
							<option :value="f.id" x-text="f.label" :style="f.family ? 'font-family:' + f.family : ''"></option>
						</template>
						<option value="custom"><?php esc_html_e( 'Custom stack…', 'themezur' ); ?></option>
					</select>
					<p class="tz-hint" x-show="options.header.typo.font_id !== 'inherit' && options.header.typo.font_id !== 'system' && options.header.typo.font_id !== 'custom'" :style="getFontStyle(options.header.typo.font_id)">
						<?php esc_html_e( 'Header preview text — শিরোনাম', 'themezur' ); ?>
					</p>
				</div>
				<div class="tz-field" x-show="options.header.typo.font_id === 'custom'">
					<label><?php esc_html_e( 'Custom font stack (CSS)', 'themezur' ); ?></label>
					<input type="text" x-model="options.header.typo.font_family" placeholder='&quot;MyFont&quot;, system-ui, sans-serif'>
					</div>
					<div class="tz-field-grid">
						<div class="tz-field">
							<label><?php esc_html_e( 'Top bar size', 'themezur' ); ?></label>
							<input type="text" x-model="options.header.typo.top_size" placeholder="13px">
						</div>
						<div class="tz-field">
							<label><?php esc_html_e( 'Top bar weight', 'themezur' ); ?></label>
							<select x-model="options.header.typo.top_weight">
								<option value="300">300</option><option value="400">400</option><option value="500">500</option><option value="600">600</option><option value="700">700</option>
							</select>
						</div>
						<div class="tz-field">
							<label><?php esc_html_e( 'Middle bar size', 'themezur' ); ?></label>
							<input type="text" x-model="options.header.typo.mid_size" placeholder="14px">
						</div>
						<div class="tz-field">
							<label><?php esc_html_e( 'Middle bar weight', 'themezur' ); ?></label>
							<select x-model="options.header.typo.mid_weight">
								<option value="300">300</option><option value="400">400</option><option value="500">500</option><option value="600">600</option><option value="700">700</option>
							</select>
						</div>
						<div class="tz-field">
							<label><?php esc_html_e( 'Nav size', 'themezur' ); ?></label>
							<input type="text" x-model="options.header.typo.nav_size" placeholder="15px">
						</div>
						<div class="tz-field">
							<label><?php esc_html_e( 'Nav weight', 'themezur' ); ?></label>
							<select x-model="options.header.typo.nav_weight">
								<option value="300">300</option><option value="400">400</option><option value="500">500</option><option value="600">600</option><option value="700">700</option>
							</select>
						</div>
						<div class="tz-field">
							<label><?php esc_html_e( 'Logo max height', 'themezur' ); ?></label>
							<input type="text" x-model="options.header.typo.logo_max_h" placeholder="44px">
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Mobile Nav -->
		<div x-show="headerSubTab === 'mobile'">
			<div class="tz-switch-row">
				<span><?php esc_html_e( 'Enable app-style bottom sticky navigation bar', 'themezur' ); ?></span>
				<input type="checkbox" x-model="options.header.mobile_nav.enabled">
			</div>

			<div class="tz-group">
				<div class="tz-group__head">
					<div>
						<h3 class="tz-group__title"><?php esc_html_e( 'Mobile Bottom Nav Buttons', 'themezur' ); ?></h3>
						<p class="tz-group__desc"><?php esc_html_e( 'App-style sticky navigation bar at the bottom of mobile screens.', 'themezur' ); ?></p>
					</div>
				</div>
				<div class="tz-group__body" x-show="options.header.mobile_nav.enabled">
					<div class="tz-field tz-field--row"><label><input type="checkbox" x-model="options.header.mobile_nav.show_home"> <?php esc_html_e( 'Show Home button', 'themezur' ); ?></label></div>
					<div class="tz-field tz-field--row"><label><input type="checkbox" x-model="options.header.mobile_nav.show_cats"> <?php esc_html_e( 'Show Categories button', 'themezur' ); ?></label></div>
					<div class="tz-field tz-field--row"><label><input type="checkbox" x-model="options.header.mobile_nav.show_search"> <?php esc_html_e( 'Show Search button', 'themezur' ); ?></label></div>
					<div class="tz-field tz-field--row"><label><input type="checkbox" x-model="options.header.mobile_nav.show_cart"> <?php esc_html_e( 'Show Cart button (with live count badge)', 'themezur' ); ?></label></div>
					<div class="tz-field tz-field--row"><label><input type="checkbox" x-model="options.header.mobile_nav.show_account"> <?php esc_html_e( 'Show Account / Menu button', 'themezur' ); ?></label></div>
				</div>
			</div>
		</div>

		<!-- Layout / spacing -->
		<div x-show="headerSubTab === 'layout'">
			<div class="tz-group">
				<div class="tz-group__head">
					<div>
						<h3 class="tz-group__title"><?php esc_html_e( 'Header presets', 'themezur' ); ?></h3>
						<p class="tz-group__desc"><?php esc_html_e( 'Same packs as General → Theme presets (also updates global colors + footer).', 'themezur' ); ?></p>
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
						<h3 class="tz-group__title"><?php esc_html_e( 'Layout & spacing', 'themezur' ); ?></h3>
						<p class="tz-group__desc"><?php esc_html_e( 'Container width, horizontal padding, and row min-heights. Use CSS units (px, rem).', 'themezur' ); ?></p>
					</div>
				</div>
				<div class="tz-group__body">
					<div class="tz-field-grid">
						<div class="tz-field">
							<label><?php esc_html_e( 'Container max width', 'themezur' ); ?></label>
							<input type="text" x-model="options.header.spacing.container_width" placeholder="1200px">
						</div>
						<div class="tz-field">
							<label><?php esc_html_e( 'Side padding', 'themezur' ); ?></label>
							<input type="text" x-model="options.header.spacing.side_padding" placeholder="1.15rem">
						</div>
						<div class="tz-field">
							<label><?php esc_html_e( 'Top bar min height', 'themezur' ); ?></label>
							<input type="text" x-model="options.header.spacing.top_min_h" placeholder="2.25rem">
						</div>
						<div class="tz-field">
							<label><?php esc_html_e( 'Middle bar min height', 'themezur' ); ?></label>
							<input type="text" x-model="options.header.spacing.mid_min_h" placeholder="4.5rem">
						</div>
						<div class="tz-field">
							<label><?php esc_html_e( 'Bottom bar min height', 'themezur' ); ?></label>
							<input type="text" x-model="options.header.spacing.bot_min_h" placeholder="3rem">
						</div>
					</div>
				</div>
			</div>

			<div class="tz-group">
				<div class="tz-group__head">
					<div>
						<h3 class="tz-group__title"><?php esc_html_e( 'Header Scroll & Transparent Overlay Effects', 'themezur' ); ?></h3>
						<p class="tz-group__desc"><?php esc_html_e( 'Configure sticky, smart auto-hide, or hero overlay transparent header behavior.', 'themezur' ); ?></p>
					</div>
				</div>
				<div class="tz-group__body">
					<div class="tz-field">
						<label><?php esc_html_e( 'Scroll Behavior', 'themezur' ); ?></label>
						<select x-model="options.header.scroll.behavior">
							<option value="none"><?php esc_html_e( 'None (Static Header)', 'themezur' ); ?></option>
							<option value="sticky"><?php esc_html_e( 'Standard Sticky Header', 'themezur' ); ?></option>
							<option value="shrink"><?php esc_html_e( 'Sticky with Shrink Height Effect', 'themezur' ); ?></option>
							<option value="bottom_sticky"><?php esc_html_e( 'Bottom Bar Only Sticky', 'themezur' ); ?></option>
							<option value="auto_hide"><?php esc_html_e( '📜 Smart Auto-Hide (Hide on scroll down, show on scroll up)', 'themezur' ); ?></option>
							<option value="transparent_solid"><?php esc_html_e( '👻 Transparent Hero Overlay (Transparent on top, solid on scroll)', 'themezur' ); ?></option>
						</select>
					</div>
					<div class="tz-field" style="margin-top:12px;">
						<label><?php esc_html_e( 'Scroll Trigger Offset (px)', 'themezur' ); ?></label>
						<input type="number" x-model.number="options.header.scroll.offset" placeholder="40">
					</div>
					<div class="tz-field tz-field--row" style="margin-top:12px;">
						<label><input type="checkbox" x-model="options.header.scroll.progress"> <?php esc_html_e( 'Show Reading Scroll Progress Bar at top', 'themezur' ); ?></label>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
