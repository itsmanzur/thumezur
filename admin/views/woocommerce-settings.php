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
				</div>
				<div class="tz-toggles" style="margin-top:10px;">
					<label class="tz-toggle"><input type="checkbox" x-model="options.woocommerce.shop.show_result_count"><span><?php esc_html_e( 'Show result count', 'themezur' ); ?></span></label>
					<label class="tz-toggle"><input type="checkbox" x-model="options.woocommerce.shop.show_ordering"><span><?php esc_html_e( 'Show sorting dropdown', 'themezur' ); ?></span></label>
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
						<label><?php esc_html_e( 'Related products', 'themezur' ); ?></label>
						<input type="number" min="0" max="8" x-model.number="options.woocommerce.single.related_count">
					</div>
					<div class="tz-field">
						<label><?php esc_html_e( 'Upsells', 'themezur' ); ?></label>
						<input type="number" min="0" max="8" x-model.number="options.woocommerce.single.upsells_count">
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
