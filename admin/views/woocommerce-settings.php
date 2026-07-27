<?php
/**
 * Themezur admin — WooCommerce.
 *
 * @package Themezur
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section x-show="tab === 'woocommerce'" class="tz-section" x-cloak>
	<h2><?php esc_html_e( 'WooCommerce', 'themezur' ); ?></h2>
	<p class="tz-muted"><?php esc_html_e( 'Shop grid and single-product polish using Themezur design tokens.', 'themezur' ); ?></p>

	<p class="tz-warn" x-show="!woocommerce"><?php esc_html_e( 'WooCommerce is not active. Activate it to use these controls.', 'themezur' ); ?></p>

	<div class="tz-group">
		<div class="tz-group__head">
			<div>
				<h3 class="tz-group__title"><?php esc_html_e( 'Source', 'themezur' ); ?></h3>
			</div>
		</div>
		<div class="tz-group__body">
			<div class="tz-field">
				<label for="tz-woo-mode"><?php esc_html_e( 'Shop styling', 'themezur' ); ?></label>
				<select id="tz-woo-mode" x-model="options.woocommerce.mode" :disabled="!woocommerce">
					<option value="theme"><?php esc_html_e( 'Themezur polish', 'themezur' ); ?></option>
					<option value="default"><?php esc_html_e( 'WooCommerce / Hello default', 'themezur' ); ?></option>
				</select>
			</div>
		</div>
	</div>

	<div x-show="options.woocommerce.mode === 'theme' && woocommerce">
		<div class="tz-group">
			<div class="tz-group__head">
				<div>
					<h3 class="tz-group__title"><?php esc_html_e( 'Shop archive', 'themezur' ); ?></h3>
				</div>
			</div>
			<div class="tz-group__body">
				<div class="tz-field-grid">
					<div class="tz-field">
						<label><?php esc_html_e( 'Columns', 'themezur' ); ?></label>
						<select x-model.number="options.woocommerce.shop.columns">
							<option :value="2">2</option>
							<option :value="3">3</option>
							<option :value="4">4</option>
						</select>
					</div>
					<div class="tz-field">
						<label><?php esc_html_e( 'Products per page', 'themezur' ); ?></label>
						<input type="number" min="4" max="48" x-model.number="options.woocommerce.shop.products_per_page">
					</div>
					<div class="tz-field">
						<label><?php esc_html_e( 'Card style', 'themezur' ); ?></label>
						<select x-model="options.woocommerce.shop.card_style">
							<option value="soft"><?php esc_html_e( 'Soft cards', 'themezur' ); ?></option>
							<option value="minimal"><?php esc_html_e( 'Minimal', 'themezur' ); ?></option>
						</select>
					</div>
					<div class="tz-field">
						<label for="tz-woo-sidebar"><?php esc_html_e( 'Shop sidebar', 'themezur' ); ?></label>
						<select id="tz-woo-sidebar" x-model="options.woocommerce.shop.sidebar">
							<option value="none"><?php esc_html_e( 'None', 'themezur' ); ?></option>
							<option value="left"><?php esc_html_e( 'Left', 'themezur' ); ?></option>
							<option value="right"><?php esc_html_e( 'Right', 'themezur' ); ?></option>
						</select>
						<p class="tz-hint"><?php esc_html_e( 'Add WooCommerce filter widgets under Appearance → Widgets → Themezur Shop Sidebar. Mobile opens as a drawer.', 'themezur' ); ?></p>
					</div>
					<div class="tz-field">
						<label for="tz-woo-new-days"><?php esc_html_e( '“New” badge (days)', 'themezur' ); ?></label>
						<input id="tz-woo-new-days" type="number" min="0" max="90" x-model.number="options.woocommerce.shop.new_badge_days">
						<p class="tz-hint"><?php esc_html_e( '0 disables the badge. Products published within this many days show New.', 'themezur' ); ?></p>
					</div>
				</div>
				<div class="tz-toggles" style="margin-top:10px;">
					<label class="tz-toggle"><input type="checkbox" x-model="options.woocommerce.shop.show_result_count"><span><?php esc_html_e( 'Show result count', 'themezur' ); ?></span></label>
					<label class="tz-toggle"><input type="checkbox" x-model="options.woocommerce.shop.show_ordering"><span><?php esc_html_e( 'Show sorting dropdown', 'themezur' ); ?></span></label>
					<label class="tz-toggle"><input type="checkbox" x-model="options.woocommerce.shop.hover_image"><span><?php esc_html_e( 'Hover second gallery image', 'themezur' ); ?></span></label>
					<label class="tz-toggle"><input type="checkbox" x-model="options.woocommerce.shop.wishlist_on_card"><span><?php esc_html_e( 'Wishlist on cards (YITH)', 'themezur' ); ?></span></label>
				</div>
			</div>
		</div>

		<div class="tz-group">
			<div class="tz-group__head">
				<div>
					<h3 class="tz-group__title"><?php esc_html_e( 'Single product', 'themezur' ); ?></h3>
				</div>
			</div>
			<div class="tz-group__body">
				<div class="tz-field-grid">
					<div class="tz-field">
						<label for="tz-woo-layout"><?php esc_html_e( 'Layout', 'themezur' ); ?></label>
						<select id="tz-woo-layout" x-model="options.woocommerce.single.layout">
							<option value="classic"><?php esc_html_e( 'Classic (WC default columns)', 'themezur' ); ?></option>
							<option value="gallery_wide"><?php esc_html_e( 'Wide gallery + sticky summary', 'themezur' ); ?></option>
							<option value="stacked"><?php esc_html_e( 'Stacked (gallery above)', 'themezur' ); ?></option>
						</select>
					</div>
					<div class="tz-field">
						<label for="tz-woo-trust"><?php esc_html_e( 'Trust / shipping note', 'themezur' ); ?></label>
						<input id="tz-woo-trust" type="text" x-model="options.woocommerce.single.trust_note" placeholder="<?php esc_attr_e( 'e.g. Free shipping over $50 · 30-day returns', 'themezur' ); ?>">
					</div>
				</div>
				<div class="tz-toggles" style="margin-top:10px;">
					<label class="tz-toggle"><input type="checkbox" x-model="options.woocommerce.single.sale_percent"><span><?php esc_html_e( 'Sale percent badge (-X%)', 'themezur' ); ?></span></label>
					<label class="tz-toggle"><input type="checkbox" x-model="options.woocommerce.single.show_rating"><span><?php esc_html_e( 'Show rating', 'themezur' ); ?></span></label>
					<label class="tz-toggle"><input type="checkbox" x-model="options.woocommerce.single.show_sku"><span><?php esc_html_e( 'Show SKU', 'themezur' ); ?></span></label>
					<label class="tz-toggle"><input type="checkbox" x-model="options.woocommerce.single.show_stock"><span><?php esc_html_e( 'Show stock status', 'themezur' ); ?></span></label>
					<label class="tz-toggle"><input type="checkbox" x-model="options.woocommerce.single.sticky_atc"><span><?php esc_html_e( 'Sticky add to cart (desktop)', 'themezur' ); ?></span></label>
				</div>
				<div class="tz-field-grid" style="margin-top:14px;">
					<div class="tz-field">
						<label><?php esc_html_e( 'Related products', 'themezur' ); ?></label>
						<input type="number" min="0" max="8" x-model.number="options.woocommerce.single.related_count" :disabled="!options.woocommerce.single.show_related">
					</div>
					<div class="tz-field">
						<label><?php esc_html_e( 'Upsells', 'themezur' ); ?></label>
						<input type="number" min="0" max="8" x-model.number="options.woocommerce.single.upsells_count" :disabled="!options.woocommerce.single.show_upsells">
					</div>
				</div>
				<div class="tz-toggles" style="margin-top:10px;">
					<label class="tz-toggle"><input type="checkbox" x-model="options.woocommerce.single.show_related"><span><?php esc_html_e( 'Show related products', 'themezur' ); ?></span></label>
					<label class="tz-toggle"><input type="checkbox" x-model="options.woocommerce.single.show_upsells"><span><?php esc_html_e( 'Show upsells', 'themezur' ); ?></span></label>
				</div>
			</div>
		</div>

		<div class="tz-group">
			<div class="tz-group__head">
				<div>
					<h3 class="tz-group__title"><?php esc_html_e( 'Cart &amp; checkout', 'themezur' ); ?></h3>
				</div>
			</div>
			<div class="tz-group__body">
				<div class="tz-toggles">
					<label class="tz-toggle"><input type="checkbox" x-model="options.woocommerce.cart.mini_cart"><span><?php esc_html_e( 'Mini-cart drawer (header cart icon)', 'themezur' ); ?></span></label>
					<label class="tz-toggle" x-show="options.woocommerce.cart.mini_cart"><input type="checkbox" x-model="options.woocommerce.cart.open_on_add"><span><?php esc_html_e( 'Open drawer after add to cart', 'themezur' ); ?></span></label>
					<label class="tz-toggle"><input type="checkbox" x-model="options.woocommerce.checkout.sticky_review"><span><?php esc_html_e( 'Sticky order review on checkout (desktop)', 'themezur' ); ?></span></label>
				</div>
				<div class="tz-field" style="margin-top:12px;">
					<label for="tz-woo-checkout-trust"><?php esc_html_e( 'Checkout trust note', 'themezur' ); ?></label>
					<input id="tz-woo-checkout-trust" type="text" x-model="options.woocommerce.checkout.trust_note" placeholder="<?php esc_attr_e( 'e.g. Secure checkout · Free returns', 'themezur' ); ?>">
					<p class="tz-hint"><?php esc_html_e( 'Shown above the Place order button. Leave empty to hide.', 'themezur' ); ?></p>
				</div>
			</div>
		</div>

		<div class="tz-group">
			<div class="tz-group__head">
				<div>
					<h3 class="tz-group__title"><?php esc_html_e( 'My Account', 'themezur' ); ?></h3>
				</div>
			</div>
			<div class="tz-group__body">
				<div class="tz-field">
					<label for="tz-woo-account-density"><?php esc_html_e( 'Density', 'themezur' ); ?></label>
					<select id="tz-woo-account-density" x-model="options.woocommerce.account.density">
						<option value="comfortable"><?php esc_html_e( 'Comfortable', 'themezur' ); ?></option>
						<option value="compact"><?php esc_html_e( 'Compact', 'themezur' ); ?></option>
					</select>
					<p class="tz-hint"><?php esc_html_e( 'Token polish for navigation, forms, and orders tables.', 'themezur' ); ?></p>
				</div>
			</div>
		</div>
	</div>
</section>
