<?php
/**
 * Themezur admin — Blog settings.
 *
 * @package Themezur
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section x-show="tab === 'blog'" class="tz-section" x-cloak>
	<h2><?php esc_html_e( 'Blog', 'themezur' ); ?></h2>
	<p class="tz-muted"><?php esc_html_e( 'Native archive and single post layouts. Elementor Theme Builder still wins when a location template is assigned.', 'themezur' ); ?></p>

	<div class="tz-group">
		<div class="tz-group__head">
			<div>
				<h3 class="tz-group__title"><?php esc_html_e( 'Source', 'themezur' ); ?></h3>
			</div>
		</div>
		<div class="tz-group__body">
			<div class="tz-field">
				<label for="tz-blog-mode"><?php esc_html_e( 'Blog templates', 'themezur' ); ?></label>
				<select id="tz-blog-mode" x-model="options.blog.mode">
					<option value="theme"><?php esc_html_e( 'Themezur blog', 'themezur' ); ?></option>
					<option value="default"><?php esc_html_e( 'Hello Elementor default', 'themezur' ); ?></option>
				</select>
			</div>
			<div class="tz-field-grid" x-show="options.blog.mode === 'theme'">
				<div class="tz-field">
					<label><?php esc_html_e( 'Archive container width', 'themezur' ); ?></label>
					<input type="text" x-model="options.blog.container_width" placeholder="1100px">
				</div>
				<div class="tz-field">
					<label><?php esc_html_e( 'Single content max width', 'themezur' ); ?></label>
					<input type="text" x-model="options.blog.single.content_width" placeholder="720px">
				</div>
			</div>
		</div>
	</div>

	<div x-show="options.blog.mode === 'theme'">
		<div class="tz-subtabs" role="tablist">
			<button type="button" class="tz-subtabs__btn" :class="{ 'is-active': blogSubTab === 'archive' }" @click="blogSubTab = 'archive'"><?php esc_html_e( 'Archive', 'themezur' ); ?></button>
			<button type="button" class="tz-subtabs__btn" :class="{ 'is-active': blogSubTab === 'single' }" @click="blogSubTab = 'single'"><?php esc_html_e( 'Single post', 'themezur' ); ?></button>
		</div>

		<!-- Archive tab -->
		<div class="tz-group" x-show="blogSubTab === 'archive'" x-cloak>
			<div class="tz-group__head">
				<div>
					<h3 class="tz-group__title"><?php esc_html_e( 'Archive layout', 'themezur' ); ?></h3>
					<p class="tz-group__desc"><?php esc_html_e( 'Blog index, categories, tags, author and date archives.', 'themezur' ); ?></p>
				</div>
			</div>
			<div class="tz-group__body">
				<div class="tz-field-grid">
					<div class="tz-field">
						<label><?php esc_html_e( 'Layout', 'themezur' ); ?></label>
						<select x-model="options.blog.archive.layout">
							<option value="grid"><?php esc_html_e( 'Grid', 'themezur' ); ?></option>
							<option value="list"><?php esc_html_e( 'List', 'themezur' ); ?></option>
						</select>
					</div>
					<div class="tz-field" x-show="options.blog.archive.layout === 'grid'">
						<label><?php esc_html_e( 'Columns', 'themezur' ); ?></label>
						<select x-model.number="options.blog.archive.columns">
							<option :value="2">2</option>
							<option :value="3">3</option>
							<option :value="4">4</option>
						</select>
					</div>
					<div class="tz-field">
						<label><?php esc_html_e( 'Card style', 'themezur' ); ?></label>
						<select x-model="options.blog.archive.card_style">
							<option value="soft"><?php esc_html_e( 'Soft cards', 'themezur' ); ?></option>
							<option value="bordered"><?php esc_html_e( 'Bordered cards', 'themezur' ); ?></option>
							<option value="minimal"><?php esc_html_e( 'Minimal', 'themezur' ); ?></option>
						</select>
					</div>
					<div class="tz-field">
						<label><?php esc_html_e( 'Excerpt length (words)', 'themezur' ); ?></label>
						<input type="number" min="5" max="80" x-model.number="options.blog.archive.excerpt_length">
					</div>
					<div class="tz-field">
						<label><?php esc_html_e( 'Read more label', 'themezur' ); ?></label>
						<input type="text" x-model="options.blog.archive.read_more_text">
					</div>
				</div>
				<div class="tz-toggles" style="margin-top:12px;">
					<label class="tz-toggle"><input type="checkbox" x-model="options.blog.archive.featured_hero"><span><?php esc_html_e( 'Featured Hero (first post featured on top)', 'themezur' ); ?></span></label>
					<label class="tz-toggle"><input type="checkbox" x-model="options.blog.archive.show_title"><span><?php esc_html_e( 'Archive title', 'themezur' ); ?></span></label>
					<label class="tz-toggle"><input type="checkbox" x-model="options.blog.archive.show_description"><span><?php esc_html_e( 'Archive description', 'themezur' ); ?></span></label>
					<label class="tz-toggle"><input type="checkbox" x-model="options.blog.archive.show_image"><span><?php esc_html_e( 'Featured image', 'themezur' ); ?></span></label>
					<label class="tz-toggle"><input type="checkbox" x-model="options.blog.archive.show_badge"><span><?php esc_html_e( 'Category badge on image', 'themezur' ); ?></span></label>
					<label class="tz-toggle"><input type="checkbox" x-model="options.blog.archive.show_excerpt"><span><?php esc_html_e( 'Excerpt', 'themezur' ); ?></span></label>
					<label class="tz-toggle"><input type="checkbox" x-model="options.blog.archive.show_date"><span><?php esc_html_e( 'Date', 'themezur' ); ?></span></label>
					<label class="tz-toggle"><input type="checkbox" x-model="options.blog.archive.show_author"><span><?php esc_html_e( 'Author', 'themezur' ); ?></span></label>
					<label class="tz-toggle"><input type="checkbox" x-model="options.blog.archive.show_category"><span><?php esc_html_e( 'Category', 'themezur' ); ?></span></label>
					<label class="tz-toggle"><input type="checkbox" x-model="options.blog.archive.show_reading_time"><span><?php esc_html_e( 'Estimated reading time', 'themezur' ); ?></span></label>
					<label class="tz-toggle"><input type="checkbox" x-model="options.blog.archive.show_read_more"><span><?php esc_html_e( 'Read more link', 'themezur' ); ?></span></label>
				</div>
			</div>
		</div>

		<!-- Single post tab -->
		<div class="tz-group" x-show="blogSubTab === 'single'" x-cloak>
			<div class="tz-group__head">
				<div>
					<h3 class="tz-group__title"><?php esc_html_e( 'Single post', 'themezur' ); ?></h3>
					<p class="tz-group__desc"><?php esc_html_e( 'Controls for individual blog posts only (pages stay on Hello / Elementor).', 'themezur' ); ?></p>
				</div>
			</div>
			<div class="tz-group__body">
				<div class="tz-field-grid" style="margin-bottom:12px;">
					<div class="tz-field">
						<label><?php esc_html_e( 'Single layout', 'themezur' ); ?></label>
						<select x-model="options.blog.single.layout">
							<option value="standard"><?php esc_html_e( 'Standard (centered column)', 'themezur' ); ?></option>
							<option value="sidebar"><?php esc_html_e( 'With right sidebar', 'themezur' ); ?></option>
						</select>
					</div>
					<div class="tz-field" x-show="options.blog.single.show_related">
						<label><?php esc_html_e( 'Related posts count', 'themezur' ); ?></label>
						<input type="number" min="1" max="6" x-model.number="options.blog.single.related_count">
					</div>
				</div>
				<div class="tz-toggles">
					<label class="tz-toggle"><input type="checkbox" x-model="options.blog.single.show_progress_bar"><span><?php esc_html_e( 'Scroll reading progress bar', 'themezur' ); ?></span></label>
					<label class="tz-toggle"><input type="checkbox" x-model="options.blog.single.show_toc"><span><?php esc_html_e( 'Auto Table of Contents (from H2/H3 headings)', 'themezur' ); ?></span></label>
					<label class="tz-toggle"><input type="checkbox" x-model="options.blog.single.show_social_share"><span><?php esc_html_e( 'Social share buttons (FB, X, LinkedIn, WhatsApp, Copy)', 'themezur' ); ?></span></label>
					<label class="tz-toggle"><input type="checkbox" x-model="options.blog.single.show_reading_time"><span><?php esc_html_e( 'Estimated reading time', 'themezur' ); ?></span></label>
					<label class="tz-toggle"><input type="checkbox" x-model="options.blog.single.show_image"><span><?php esc_html_e( 'Featured image', 'themezur' ); ?></span></label>
					<label class="tz-toggle"><input type="checkbox" x-model="options.blog.single.show_date"><span><?php esc_html_e( 'Date', 'themezur' ); ?></span></label>
					<label class="tz-toggle"><input type="checkbox" x-model="options.blog.single.show_author"><span><?php esc_html_e( 'Author in meta', 'themezur' ); ?></span></label>
					<label class="tz-toggle"><input type="checkbox" x-model="options.blog.single.show_category"><span><?php esc_html_e( 'Category', 'themezur' ); ?></span></label>
					<label class="tz-toggle"><input type="checkbox" x-model="options.blog.single.show_tags"><span><?php esc_html_e( 'Tags', 'themezur' ); ?></span></label>
					<label class="tz-toggle"><input type="checkbox" x-model="options.blog.single.show_author_box"><span><?php esc_html_e( 'Author box', 'themezur' ); ?></span></label>
					<label class="tz-toggle"><input type="checkbox" x-model="options.blog.single.show_nav"><span><?php esc_html_e( 'Previous / next posts nav', 'themezur' ); ?></span></label>
					<label class="tz-toggle"><input type="checkbox" x-model="options.blog.single.show_related"><span><?php esc_html_e( 'Related posts', 'themezur' ); ?></span></label>
					<label class="tz-toggle"><input type="checkbox" x-model="options.blog.single.show_comments"><span><?php esc_html_e( 'Comments', 'themezur' ); ?></span></label>
				</div>
			</div>
		</div>
	</div>
</section>
