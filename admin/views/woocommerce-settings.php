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
	<p class="tz-muted"><?php esc_html_e( 'Shop grid, product cards, Quick View, Wishlist, single product, cart drawer, checkout and account controls.', 'themezur' ); ?></p>

	<p class="tz-warn" x-show="!woocommerce"><?php esc_html_e( 'WooCommerce is not active. Activate it to use these controls.', 'themezur' ); ?></p>

	<!-- Source -->
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

		<!-- Shop Archive -->
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
							<option :value="5">5</option>
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
							<option value="bordered"><?php esc_html_e( 'Bordered cards', 'themezur' ); ?></option>
							<option value="minimal"><?php esc_html_e( 'Minimal', 'themezur' ); ?></option>
						</select>
					</div>
					<div class="tz-field">
						<label><?php esc_html_e( 'Shop sidebar', 'themezur' ); ?></label>
						<select x-model="options.woocommerce.shop.sidebar">
							<option value="none"><?php esc_html_e( 'None', 'themezur' ); ?></option>
							<option value="left"><?php esc_html_e( 'Left', 'themezur' ); ?></option>
							<option value="right"><?php esc_html_e( 'Right', 'themezur' ); ?></option>
						</select>
						<p class="tz-muted" style="margin:.35rem 0 0;"><?php esc_html_e( 'Assign widgets to Appearance → Widgets → Themezur Shop Sidebar. On mobile the sidebar opens as a filter drawer.', 'themezur' ); ?></p>
					</div>
					<div class="tz-field">
						<label><?php esc_html_e( 'New badge (days)', 'themezur' ); ?></label>
						<input type="number" min="0" max="90" x-model.number="options.woocommerce.shop.new_badge_days">
						<p class="tz-muted" style="margin:.35rem 0 0;"><?php esc_html_e( '0 = hide New badge. Products published within this window show a New badge.', 'themezur' ); ?></p>
					</div>
				</div>
				<div class="tz-toggles" style="margin-top:10px;">
					<label class="tz-toggle"><input type="checkbox" x-model="options.woocommerce.shop.show_result_count"><span><?php esc_html_e( 'Show result count', 'themezur' ); ?></span></label>
					<label class="tz-toggle"><input type="checkbox" x-model="options.woocommerce.shop.show_ordering"><span><?php esc_html_e( 'Show sorting dropdown', 'themezur' ); ?></span></label>
					<label class="tz-toggle"><input type="checkbox" x-model="options.woocommerce.shop.hover_image"><span><?php esc_html_e( 'Hover second image', 'themezur' ); ?> <small class="tz-muted"><?php esc_html_e( '— swaps to first gallery image', 'themezur' ); ?></small></span></label>
				</div>
			</div>
		</div>

		<!-- Features -->
		<div class="tz-group">
			<div class="tz-group__head">
				<div>
					<h3 class="tz-group__title"><?php esc_html_e( 'Product card features', 'themezur' ); ?></h3>
					<p class="tz-muted" style="margin:0;"><?php esc_html_e( 'Shown on product cards in the shop grid. No extra plugin needed.', 'themezur' ); ?></p>
				</div>
			</div>
			<div class="tz-group__body">
				<div class="tz-toggles">
					<label class="tz-toggle">
						<input type="checkbox" x-model="options.woocommerce.shop.quick_view">
						<span>
							<?php esc_html_e( 'Quick View', 'themezur' ); ?>
							<small class="tz-muted"><?php esc_html_e( '— Themezur modal (AJAX product preview)', 'themezur' ); ?></small>
						</span>
					</label>
					<label class="tz-toggle">
						<input type="checkbox" x-model="options.woocommerce.shop.wishlist">
						<span>
							<?php esc_html_e( 'Wishlist', 'themezur' ); ?>
							<small class="tz-muted"><?php esc_html_e( '— Themezur heart button (localStorage)', 'themezur' ); ?></small>
						</span>
					</label>
				</div>
			</div>
		</div>

		<!-- Single product -->
		<div class="tz-group">
			<div class="tz-group__head">
				<div>
					<h3 class="tz-group__title"><?php esc_html_e( 'Single product', 'themezur' ); ?></h3>
				</div>
			</div>
			<div class="tz-group__body">
				<div class="tz-field-grid">
					<div class="tz-field">
						<label><?php esc_html_e( 'Layout', 'themezur' ); ?></label>
						<select x-model="options.woocommerce.single.layout">
							<option value="classic"><?php esc_html_e( 'Classic', 'themezur' ); ?></option>
							<option value="gallery_wide"><?php esc_html_e( 'Wide gallery', 'themezur' ); ?></option>
							<option value="stacked"><?php esc_html_e( 'Stacked', 'themezur' ); ?></option>
						</select>
					</div>
					<div class="tz-field">
						<label><?php esc_html_e( 'Related products', 'themezur' ); ?></label>
						<input type="number" min="0" max="8" x-model.number="options.woocommerce.single.related_count" :disabled="!options.woocommerce.single.show_related">
					</div>
					<div class="tz-field">
						<label><?php esc_html_e( 'Upsells', 'themezur' ); ?></label>
						<input type="number" min="0" max="8" x-model.number="options.woocommerce.single.upsells_count" :disabled="!options.woocommerce.single.show_upsells">
					</div>
				</div>
				<div class="tz-field" style="margin-top:10px;">
					<label><?php esc_html_e( 'Trust / shipping note', 'themezur' ); ?></label>
					<textarea rows="2" x-model="options.woocommerce.single.trust_note" placeholder="<?php esc_attr_e( 'Free shipping over $50 · 7-day returns', 'themezur' ); ?>"></textarea>
				</div>
				<div class="tz-toggles" style="margin-top:10px;">
					<label class="tz-toggle">
						<input type="checkbox" x-model="options.woocommerce.single.sale_percent">
						<span><?php esc_html_e( 'Sale percent badge (−X%)', 'themezur' ); ?></span>
					</label>
					<label class="tz-toggle">
						<input type="checkbox" x-model="options.woocommerce.single.show_rating">
						<span><?php esc_html_e( 'Show rating', 'themezur' ); ?></span>
					</label>
					<label class="tz-toggle">
						<input type="checkbox" x-model="options.woocommerce.single.show_sku">
						<span><?php esc_html_e( 'Show SKU', 'themezur' ); ?></span>
					</label>
					<label class="tz-toggle">
						<input type="checkbox" x-model="options.woocommerce.single.show_stock">
						<span><?php esc_html_e( 'Show stock status', 'themezur' ); ?></span>
					</label>
					<label class="tz-toggle">
						<input type="checkbox" x-model="options.woocommerce.single.show_related">
						<span><?php esc_html_e( 'Show related products', 'themezur' ); ?></span>
					</label>
					<label class="tz-toggle">
						<input type="checkbox" x-model="options.woocommerce.single.show_upsells">
						<span><?php esc_html_e( 'Show upsells', 'themezur' ); ?></span>
					</label>
					<label class="tz-toggle">
						<input type="checkbox" x-model="options.woocommerce.single.sticky_cart">
						<span>
							<?php esc_html_e( 'Sticky Add to Cart bar', 'themezur' ); ?>
							<small class="tz-muted"><?php esc_html_e( '— desktop only; appears when main button scrolls out of view', 'themezur' ); ?></small>
						</span>
					</label>
					<label class="tz-toggle">
						<input type="checkbox" x-model="options.woocommerce.single.quantity_stepper">
						<span>
							<?php esc_html_e( 'Quantity +/− stepper buttons', 'themezur' ); ?>
							<small class="tz-muted"><?php esc_html_e( '— replaces plain number input', 'themezur' ); ?></small>
						</span>
					</label>
				</div>
			</div>
		</div>

		<!-- Cart / checkout -->
		<div class="tz-group">
			<div class="tz-group__head">
				<div>
					<h3 class="tz-group__title"><?php esc_html_e( 'Cart & checkout', 'themezur' ); ?></h3>
					<p class="tz-muted" style="margin:0;"><?php esc_html_e( 'Mini-cart drawer is controlled under Header → Middle → Mini cart. These options polish cart/checkout behavior.', 'themezur' ); ?></p>
				</div>
			</div>
			<div class="tz-group__body">
				<div class="tz-toggles">
					<label class="tz-toggle">
						<input type="checkbox" x-model="options.woocommerce.cart.open_on_add">
						<span>
							<?php esc_html_e( 'Open mini-cart on add to cart', 'themezur' ); ?>
							<small class="tz-muted"><?php esc_html_e( '— requires Header mini-cart drawer', 'themezur' ); ?></small>
						</span>
					</label>
					<label class="tz-toggle">
						<input type="checkbox" x-model="options.woocommerce.checkout.sticky_review">
						<span>
							<?php esc_html_e( 'Sticky order review (desktop)', 'themezur' ); ?>
						</span>
					</label>
				</div>
				<div class="tz-field" style="margin-top:10px;">
					<label><?php esc_html_e( 'Checkout trust note', 'themezur' ); ?></label>
					<textarea rows="2" x-model="options.woocommerce.checkout.trust_note" placeholder="<?php esc_attr_e( 'Secure checkout · SSL encrypted', 'themezur' ); ?>"></textarea>
					<p class="tz-muted" style="margin:.35rem 0 0;"><?php esc_html_e( 'Shown above the Place order button.', 'themezur' ); ?></p>
				</div>
			</div>
		</div>

		<!-- My Account -->
		<div class="tz-group">
			<div class="tz-group__head">
				<div>
					<h3 class="tz-group__title"><?php esc_html_e( 'My Account', 'themezur' ); ?></h3>
				</div>
			</div>
			<div class="tz-group__body">
				<div class="tz-field">
					<label><?php esc_html_e( 'Density', 'themezur' ); ?></label>
					<select x-model="options.woocommerce.account.density">
						<option value="comfortable"><?php esc_html_e( 'Comfortable', 'themezur' ); ?></option>
						<option value="compact"><?php esc_html_e( 'Compact', 'themezur' ); ?></option>
					</select>
				</div>
			</div>
		</div>

	</div>
</section>
