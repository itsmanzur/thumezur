<?php
/**
 * Themezur Help / Docs registry.
 *
 * When adding a feature, append a section or accordion item here
 * (or via the `themezur_admin_docs` filter) so usage stays documented.
 *
 * @package Themezur
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Themezur_Docs
 */
class Themezur_Docs {

	/**
	 * Documentation sections for the Help panel.
	 *
	 * Each section: id, title, items[] with title + body (HTML allowed, escaped on output via wp_kses_post).
	 *
	 * @return array[]
	 */
	public static function get_sections() {
		$sections = array(
			array(
				'id'    => 'getting-started',
				'title' => __( 'Getting Started', 'themezur' ),
				'items' => array(
					array(
						'title' => __( 'What is Themezur Panel?', 'themezur' ),
						'body'  => __(
							'<p>Themezur is a Hello Elementor child theme control panel. Design headers/footers in <strong>Elementor</strong>, then assign and toggle them here — plus performance and color options.</p>
							<ol>
								<li>Open <strong>Themezur</strong> in the WordPress admin sidebar.</li>
								<li>Change settings in any tab.</li>
								<li>Click <strong>Save changes</strong> (nothing is auto-saved).</li>
								<li>View the front of the site to confirm.</li>
							</ol>',
							'themezur'
						),
					),
					array(
						'title' => __( 'Save & security', 'themezur' ),
						'body'  => __(
							'<p>Settings are stored in a single WordPress option (<code>themezur_options</code>). Only users with <code>manage_options</code> can save. Every AJAX request is nonce-protected.</p>',
							'themezur'
						),
					),
				),
			),
			array(
				'id'    => 'header-footer',
				'title' => __( 'Header & Footer', 'themezur' ),
				'items' => array(
					array(
						'title' => __( 'Source modes', 'themezur' ),
						'body'  => __(
							'<p>Each of Header and Footer has three modes:</p>
							<ul>
								<li><strong>Themezur header / footer</strong> — built-in markup (header: 3-row; footer: 4-column store layout).</li>
								<li><strong>Elementor template</strong> — renders a published template from the Elementor library by ID.</li>
								<li><strong>None</strong> — hides that region completely.</li>
							</ul>
							<p>If Elementor is inactive or the template ID is invalid, Themezur safely falls back to the Themezur built-in header/footer.</p>',
							'themezur'
						),
					),
					array(
						'title' => __( 'Themezur native header', 'themezur' ),
						'body'  => __(
							'<p>Themezur uses a <strong>3-row header</strong> (structure inspired by store headers; colors are yours):</p>
							<ol>
								<li><strong>Top bar</strong> — promo/trending, social links, date</li>
								<li><strong>Middle bar</strong> — logo (from Themezur panel), address, search, dark mode, cart</li>
								<li><strong>Bottom bar</strong> — categories button, main menu, deal CTA</li>
							</ol>
							<p>Every row and element can be enabled/disabled. Background and element colors, plus typography (font stack, sizes, weights, logo max height), are under <strong>Themezur → Header</strong> sub-tabs.</p>
							<p><strong>Scroll behavior</strong> — none, sticky, shrink, bottom-bar sticky, or transparent→solid over a hero.</p>
							<p><strong>Announcement bar</strong> — optional message above the header. Enable dismiss to store a cookie (days + version key). Bump the version key to show a new campaign after visitors dismissed the old one.</p>
							<p><strong>Mobile / desktop visibility</strong> — each header element has Hide on mobile / Hide on desktop checkboxes (breakpoint 992px). Bottom-bar “Hide on mobile” for the menu does not remove the hamburger drawer.</p>
							<p><strong>Categories</strong> — mega dropdown from WooCommerce <code>product_cat</code> (or simple link mode).</p>
							<p><strong>Smart search</strong> — AJAX live product suggestions while typing.</p>
							<p><strong>Account icon</strong> — middle-bar login / My Account link (WooCommerce My Account when available; otherwise WP login or profile).</p>
							<p><strong>Phone / hotline</strong> — optional top-bar tel: link with label + number (mobile/desktop visibility supported).</p>
							<p><strong>Layout &amp; spacing</strong> — container max width, side padding, and min-heights for top / middle / bottom rows (Header → Layout).</p>
							<p><strong>Rotating promos</strong> — add multiple promo messages under Top bar; enable rotate + interval to cycle them.</p>
							<p><strong>Cart AJAX</strong> — cart badge updates via WooCommerce fragments after add-to-cart (no full reload).</p>
							<p><strong>Wishlist / Compare</strong> — optional middle-bar icons with custom URLs (YITH Wishlist page auto-detected when URL is empty).</p>
							<p><strong>Presets</strong> — Midnight / Clean Light / Amber Store color packs under Header → Layout.</p>
							<p><strong>Scroll progress</strong> — thin progress line on the header while the page scrolls.</p>
							<p>Assign a menu to <strong>Appearance → Menus → Header</strong>. Save changes, then view the front end.</p>',
							'themezur'
						),
					),
					array(
						'title' => __( 'Themezur 4-column footer', 'themezur' ),
						'body'  => __(
							'<p>Under <strong>Themezur → Footer</strong>, Source = Themezur footer (4-column):</p>
							<ul>
								<li>Each column can be <strong>enabled/disabled</strong>. Disabled columns are not rendered; remaining columns <strong>flex-grow</strong> to share the row.</li>
								<li>Use the <strong>Columns overview</strong> cards to toggle and open an editor quickly.</li>
								<li>Column types: <strong>About</strong> (site / custom logo), <strong>Menu</strong>, <strong>Links</strong>, <strong>Contact</strong>, <strong>Posts</strong> (recent WP posts), <strong>Products</strong> (WooCommerce), <strong>Newsletter</strong> (form or shortcode), <strong>Shortcode</strong> (any shortcode/HTML).</li>
								<li>Optional subtitle under each column title.</li>
								<li>Bottom bar: copyright + optional secondary menu.</li>
								<li><strong>Back to top</strong> floating button (scroll threshold) — works with Themezur or Elementor footer.</li>
							</ul>
							<p>Social icons in the About column reuse the links from <strong>Header → Top bar → Social icons</strong>. Products degrade gracefully if WooCommerce is inactive.</p>',
							'themezur'
						),
					),
					array(
						'title' => __( 'How to use an Elementor header', 'themezur' ),
						'body'  => __(
							'<ol>
								<li>In Elementor, create a <strong>Header</strong> (or Section) template and publish it.</li>
								<li>Go to <strong>Themezur → Header</strong>.</li>
								<li>Set Source mode to <strong>Elementor template</strong>.</li>
								<li>Pick the template from the dropdown.</li>
								<li>Optional: enable <strong>Sticky header</strong>.</li>
								<li>Click <strong>Save changes</strong>.</li>
							</ol>
							<p>Footer works the same under <strong>Themezur → Footer</strong>.</p>',
							'themezur'
						),
					),
				),
			),
			array(
				'id'    => 'assignments',
				'title' => __( 'Assignments', 'themezur' ),
				'items' => array(
					array(
						'title' => __( 'Scope — where modes apply', 'themezur' ),
						'body'  => __(
							'<p>Assignments control <em>where</em> Themezur’s Elementor / None modes are active. Outside the scope, Themezur falls back to the <strong>built-in theme</strong> header/footer.</p>
							<ul>
								<li><strong>Entire site</strong> — everywhere (default).</li>
								<li><strong>Front page / Home</strong> — only the site front page.</li>
								<li><strong>Shop</strong> — WooCommerce shop, product categories/tags, and single products.</li>
								<li><strong>Blog</strong> — posts index, single posts, and post archives.</li>
								<li><strong>Custom selection</strong> — pick any mix of Home / Shop / Blog / Everything else.</li>
							</ul>
							<p><strong>Exclude post/page IDs</strong> — comma-separated IDs. On those singular pages Themezur always falls back to the built-in header/footer.</p>',
							'themezur'
						),
					),
					array(
						'title' => __( 'Conditional headers (Home / Shop / Blog)', 'themezur' ),
						'body'  => __(
							'<p>Under <strong>Themezur → Assignments → Conditional headers</strong>, enable an override for Home, Shop, or Blog to use a different header source than the global Header tab:</p>
							<ul>
								<li><strong>Inherit</strong> — use Header → Source mode.</li>
								<li><strong>Themezur / Elementor / None</strong> — force that mode for the context (pick an Elementor template when needed).</li>
							</ul>
							<p>Overrides only apply when the current page is inside the Scope above. Priority when detecting context: Home → Shop → Blog → other.</p>',
							'themezur'
						),
					),
				),
			),
			array(
				'id'    => 'blog',
				'title' => __( 'Blog', 'themezur' ),
				'items' => array(
					array(
						'title' => __( 'Themezur blog archive & single', 'themezur' ),
						'body'  => __(
							'<p>Under <strong>Themezur → Blog</strong>:</p>
							<ul>
								<li><strong>Themezur blog</strong> — native archive (grid/list) and single post templates.</li>
								<li><strong>Hello Elementor default</strong> — falls back to the parent theme markup.</li>
							</ul>
							<p><strong>Archive</strong> — layout, columns, featured image, excerpt length, meta (date/author/category), read more.</p>
							<p><strong>Single</strong> — featured image, meta, tags, author box, previous/next, related posts, comments, content max width.</p>
							<p>If an <strong>Elementor Theme Builder</strong> archive/single template is assigned, Elementor still takes priority.</p>
							<p>Pages are unchanged — only <code>post</code> singles use the Themezur single template.</p>',
							'themezur'
						),
					),
				),
			),
			array(
				'id'    => 'pages',
				'title' => __( '404 & Search', 'themezur' ),
				'items' => array(
					array(
						'title' => __( '404 and search templates', 'themezur' ),
						'body'  => __(
							'<p>Under <strong>Themezur → 404 &amp; Search</strong>:</p>
							<ul>
								<li><strong>404</strong> — custom title/message, optional search form, home button.</li>
								<li><strong>Search</strong> — list or grid results with image, excerpt, and content-type badge.</li>
							</ul>
							<p>Set Templates to <strong>Hello Elementor default</strong> to fall back to the parent theme. Elementor Theme Builder locations still take priority when assigned.</p>',
							'themezur'
						),
					),
				),
			),
			array(
				'id'    => 'woocommerce',
				'title' => __( 'WooCommerce', 'themezur' ),
				'items' => array(
					array(
						'title' => __( 'Shop polish', 'themezur' ),
						'body'  => __(
							'<p>Under <strong>Themezur → WooCommerce</strong> (requires WooCommerce active):</p>
							<ul>
								<li><strong>Shop archive</strong> — columns (2–4), products per page, soft/minimal cards, result count &amp; sorting toggles.</li>
								<li><strong>Single product</strong> — related / upsell counts.</li>
							</ul>
							<p>Styles use Themezur design tokens (buttons, radius, accent). Set Shop styling to default to disable Themezur polish.</p>',
							'themezur'
						),
					),
				),
			),
			array(
				'id'    => 'performance',
				'title' => __( 'Performance', 'themezur' ),
				'items' => array(
					array(
						'title' => __( 'Safe disable toggles', 'themezur' ),
						'body'  => __(
							'<p>Use these only when you know the asset is unused:</p>
							<ul>
								<li><strong>Hello reset.css / theme.css</strong> — parent theme base styles.</li>
								<li><strong>Hello header-footer.css</strong> — styles for the default theme header/footer.</li>
								<li><strong>Emoji scripts</strong> — removes WP emoji detection CSS/JS.</li>
								<li><strong>wp-embed</strong> — deregisters the embed script on the frontend.</li>
							</ul>
							<p>If the layout breaks after enabling a toggle, turn it off and Save again.</p>
							<p><strong>Asset audit</strong> on the same tab lists Themezur / Hello / core assets expected from your current settings (On / Conditional / Off).</p>',
							'themezur'
						),
					),
				),
			),
			array(
				'id'    => 'general',
				'title' => __( 'General', 'themezur' ),
				'items' => array(
					array(
						'title' => __( 'Colors & scripts', 'themezur' ),
						'body'  => __(
							'<p><strong>Theme presets</strong> (Midnight / Clean Light / Amber Store / Forest) apply global colors + header + footer in one click — then Save.</p>
							<p><strong>Site logo</strong> — Media Library upload in General / Header.</p>
							<p><strong>Design tokens</strong> print as CSS variables on <code>:root</code> (<code>--themezur-*</code>).</p>
							<p><strong>Fonts</strong> — curated picker (System + Google Fonts). Google CSS loads only when a Google font is selected. Custom stack available.</p>
							<p><strong>Breadcrumbs</strong> — unified trail on blog, shop, singles, search (toggle in General).</p>
							<p>Utility classes: <code>.tz-container</code>, <code>.tz-btn</code>, <code>.tz-btn--ghost</code>, <code>.tz-surface</code>.</p>',
							'themezur'
						),
					),
				),
			),
			array(
				'id'    => 'for-developers',
				'title' => __( 'For Developers', 'themezur' ),
				'items' => array(
					array(
						'title' => __( 'How to document a new feature', 'themezur' ),
						'body'  => __(
							'<p>Whenever you add a Themezur feature, also add Help docs:</p>
							<ol>
								<li>Open <code>admin/docs/registry.php</code> (this file’s data via <code>Themezur_Docs::get_sections()</code>).</li>
								<li>Add a new section or accordion item with <code>title</code> + <code>body</code> HTML.</li>
								<li>Or hook <code>themezur_admin_docs</code> from your feature class and append a section.</li>
							</ol>
							<p>Docs appear under <strong>Themezur → Help / Docs</strong> as tabs + accordion — no separate markdown site needed.</p>',
							'themezur'
						),
					),
				),
			),
		);

		/**
		 * Filter Help / Docs sections.
		 *
		 * @param array[] $sections Doc sections.
		 */
		return apply_filters( 'themezur_admin_docs', $sections );
	}
}
