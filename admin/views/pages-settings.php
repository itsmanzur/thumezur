<?php
/**
 * Themezur admin — 404 & Search (Pages).
 *
 * @package Themezur
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section x-show="tab === 'pages'" class="tz-section" x-cloak>
	<h2><?php esc_html_e( '404 & Search', 'themezur' ); ?></h2>
	<p class="tz-muted"><?php esc_html_e( 'Native templates for missing pages and search results.', 'themezur' ); ?></p>

	<div class="tz-group">
		<div class="tz-group__head">
			<div>
				<h3 class="tz-group__title"><?php esc_html_e( 'Source', 'themezur' ); ?></h3>
			</div>
		</div>
		<div class="tz-group__body">
			<div class="tz-field">
				<label for="tz-pages-mode"><?php esc_html_e( 'Templates', 'themezur' ); ?></label>
				<select id="tz-pages-mode" x-model="options.pages.mode">
					<option value="theme"><?php esc_html_e( 'Themezur', 'themezur' ); ?></option>
					<option value="default"><?php esc_html_e( 'Hello Elementor default', 'themezur' ); ?></option>
				</select>
			</div>
		</div>
	</div>

	<div x-show="options.pages.mode === 'theme'">
		<div class="tz-group">
			<div class="tz-group__head">
				<div>
					<h3 class="tz-group__title"><?php esc_html_e( '404 page', 'themezur' ); ?></h3>
				</div>
			</div>
			<div class="tz-group__body">
				<div class="tz-field">
					<label><?php esc_html_e( 'Title', 'themezur' ); ?></label>
					<input type="text" x-model="options.pages.not_found.title">
				</div>
				<div class="tz-field">
					<label><?php esc_html_e( 'Message', 'themezur' ); ?></label>
					<textarea rows="3" x-model="options.pages.not_found.text"></textarea>
				</div>
				<div class="tz-field">
					<label><?php esc_html_e( 'Home button label', 'themezur' ); ?></label>
					<input type="text" x-model="options.pages.not_found.home_label">
				</div>
				<div class="tz-toggles">
					<label class="tz-toggle"><input type="checkbox" x-model="options.pages.not_found.show_search"><span><?php esc_html_e( 'Show search form', 'themezur' ); ?></span></label>
					<label class="tz-toggle"><input type="checkbox" x-model="options.pages.not_found.show_home_btn"><span><?php esc_html_e( 'Show home button', 'themezur' ); ?></span></label>
					<label class="tz-toggle"><input type="checkbox" x-model="options.pages.not_found.show_quick_links"><span><?php esc_html_e( 'Show Quick Navigation Links (Home, Shop, Blog)', 'themezur' ); ?></span></label>
				</div>
			</div>
		</div>

		<div class="tz-group">
			<div class="tz-group__head">
				<div>
					<h3 class="tz-group__title"><?php esc_html_e( 'Search results', 'themezur' ); ?></h3>
				</div>
			</div>
			<div class="tz-group__body">
				<div class="tz-field">
					<label><?php esc_html_e( 'Layout', 'themezur' ); ?></label>
					<select x-model="options.pages.search.layout">
						<option value="list"><?php esc_html_e( 'List', 'themezur' ); ?></option>
						<option value="grid"><?php esc_html_e( 'Grid', 'themezur' ); ?></option>
					</select>
				</div>
				<div class="tz-toggles" style="margin-top:10px;">
					<label class="tz-toggle"><input type="checkbox" x-model="options.pages.search.show_image"><span><?php esc_html_e( 'Show image', 'themezur' ); ?></span></label>
					<label class="tz-toggle"><input type="checkbox" x-model="options.pages.search.show_excerpt"><span><?php esc_html_e( 'Show excerpt', 'themezur' ); ?></span></label>
					<label class="tz-toggle"><input type="checkbox" x-model="options.pages.search.show_type"><span><?php esc_html_e( 'Show content type badge', 'themezur' ); ?></span></label>
					<label class="tz-toggle"><input type="checkbox" x-model="options.pages.search.show_product_details"><span><?php esc_html_e( 'Show product price & Add to Cart button (WooCommerce)', 'themezur' ); ?></span></label>
				</div>
			</div>
		</div>
	</div>
</section>
