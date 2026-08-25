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
							</ol>
							<p><strong>Panel appearance</strong> — use the sun/moon button next to Save to switch the Themezur panel between <em>dark</em> and <em>light</em>. Preference is stored in this browser only (<code>localStorage</code>), not in site options.</p>',
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
								<li><strong>Elementor template</strong> — renders a published Elementor library template by ID (Themezur’s own assignment, not Theme Builder locations).</li>
								<li><strong>None</strong> — hides that region completely.</li>
							</ul>
							<p><strong>Theme Builder vs Themezur:</strong> Themezur does <em>not</em> call <code>elementor_theme_do_location( \'header\' )</code> / <code>footer</code>. Choosing “Elementor template” here prints a specific template ID from the Themezur panel. If you also assign an Elementor Theme Builder header/footer, that Theme Builder template can still win via Elementor’s own theme support — use one approach to avoid double headers. Blog/shop/product Theme Builder archive/single templates still take priority for content areas when assigned.</p>
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
								<li><strong>Middle bar</strong> — logo (from Themezur panel), optional WordPress menu, address, search, dark mode, cart</li>
								<li><strong>Bottom bar</strong> — categories button, main menu, deal CTA</li>
							</ol>
							<p>Every row and element can be enabled/disabled. Background and element colors, plus typography (font stack, sizes, weights, logo max height), are under <strong>Themezur → Header</strong> sub-tabs.</p>
							<p><strong>Middle bar menu</strong> — Header → Middle bar → <em>Middle bar menu</em>. Enable it, pick a menu, set alignment (<em>Left / Center / Right</em>). Right sits the links before Order Now / icons. Use <em>Show hamburger</em> + Hide on desktop/mobile to control the drawer button (often hide on desktop when menus are already visible).</p>
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
						'title' => __( 'Themezur Ideal Mega Menu Engine Usage Guide', 'themezur' ),
						'body'  => __(
							'<p>Themezur includes a high-performance, native <strong>Mega Menu Engine</strong> integrated directly into WordPress <code>Appearance → Menus</code> without requiring any external plugins.</p>
							<h4>1. How to Enable Mega Menu on a Top-Level Menu Item:</h4>
							<ol>
								<li>Go to <strong>Appearance → Menus</strong> in the WordPress admin sidebar.</li>
								<li>Click on any main top-level menu item (e.g., <em>Features</em>, <em>Shop</em>, or <em>Categories</em>) to expand its options panel.</li>
								<li>Check the box <strong>Enable Mega Menu Dropdown for this item</strong>.</li>
								<li>Choose your <strong>Dropdown Layout Style</strong> (<em>Rich SaaS Grid</em>, <em>Standard Multi-Column Grid</em>, or <em>Elementor Saved Template</em>).</li>
								<li>Set <strong>Panel Width</strong> (<em>Compact 750px</em>, <em>Container 1200px</em>, or <em>Full Width 100%</em>) and <strong>Columns Count</strong> (2, 3, or 4 columns).</li>
								<li>Pick a <strong>Color Theme</strong> (<em>Dark SaaS Card #0f172a</em>, <em>Clean White Card #ffffff</em>, or <em>Custom Colors</em>) and <strong>Entrance Animation</strong> (<em>Slide Down</em>, <em>Fade In</em>, <em>Scale Zoom</em>, or <em>3D Flip</em>).</li>
							</ol>

							<h4>2. How to Add Icons, Section Headers, Badges &amp; Descriptions:</h4>
							<ol>
								<li>Drag your sub-links underneath the top-level menu item as indented sub-items.</li>
								<li>Open any sub-item to configure its rich options:
									<ul>
										<li><strong>Section Group Header:</strong> Type a header label (e.g. <code>FREE</code>, <code>PRO</code>, <code>AGENCY</code>, <code>CATEGORIES</code>) on the 1st item of a group to start a new column with a stylish divider line.</li>
										<li><strong>Item Icon:</strong> Choose from 15+ built-in SVG presets (<code>scanner</code>, <code>heatmaps</code>, <code>analytics</code>, <code>ai</code>, <code>pdf</code>, <code>star</code>, <code>shopping</code>), 20+ WordPress <strong>Dashicons</strong> (<code>dashicons-cart</code>, <code>dashicons-store</code>, <code>dashicons-heart</code>), or click <strong>🖼️ Upload Image</strong> to select any custom image/SVG from your Media Library!</li>
										<li><strong>Badge Text &amp; Color:</strong> Type badge text (e.g. <code>PRO</code>, <code>HOT</code>, <code>NEW</code>) and choose a badge color (<em>Green</em>, <em>Purple</em>, <em>Blue</em>, <em>Red</em>).</li>
										<li><strong>Item Short Subtitle / Description:</strong> Type a 1-line description displayed directly below the title.</li>
									</ul>
								</li>
							</ol>

							<h4>3. Advanced Features (Promo Card, Categories Grid, Glowing Badges):</h4>
							<ul>
								<li><strong>🛍️ Featured Product / Promo Card Column:</strong> Check <em>Add Featured Product / Promo Banner Card Column</em> under the top-level menu item and fill in image, title, price, and button link to render a 3D offer card column.</li>
								<li><strong>🖼️ Background Image Overlay:</strong> Add a banner background image URL under <em>Dropdown Panel Background Image Overlay</em>.</li>
								<li><strong>🔥 Main Bar Glowing Badges:</strong> Type badge text (e.g. <code>HOT</code>, <code>50% OFF</code>) under <em>Main Bar Glowing Animated Badge</em> to render pulsing glow badges on the main navigation bar.</li>
								<li><strong>📂 WooCommerce Category Grid:</strong> Check <em>Auto Render WooCommerce Product Categories Grid</em> to automatically render visual product category thumbnail cards inside the mega dropdown.</li>
							</ul>
							<p><strong>Mobile drawer:</strong> uses a lite walker (unique IDs, no desktop mega panels). Mega-enabled items get accordion deep links (categories / new / sale / brands) instead of full product grids.</p>
							<p><strong>Advanced blocks (Appearance → Menus):</strong> product collections (latest / on sale / best sellers / manual IDs), brand logo grid (auto taxonomy), rich promo HTML layouts, tabbed mega (Links | Categories | New | Sale | Brands), and campaign start/end scheduling (site timezone).</p>',
							'themezur'
						),
					),
					array(
						'title' => __( 'Themezur 4-column footer', 'themezur' ),
						'body'  => __(
							'<p>Under <strong>Themezur → Footer</strong>, Source = Themezur footer (4-column):</p>
							<ul>
								<li>Each column can be <strong>enabled/disabled</strong>. Disabled columns are not rendered; remaining columns <strong>flex-grow</strong> to share the row.</li>
								<li><strong>Column order &amp; widths</strong> — reorder with ↑/↓ and presets (equal, 2-1-1-1, 1-1-1-2, about wide).</li>
								<li>Column types: <strong>About</strong>, <strong>Menu</strong>, <strong>Links</strong>, <strong>Contact</strong>, <strong>Posts</strong>, <strong>Products</strong>, <strong>Newsletter</strong> (AJAX to external action URL, or shortcode), <strong>Shortcode</strong>.</li>
								<li><strong>Store locator row</strong> and <strong>App badges + QR</strong> (media library image ID).</li>
								<li>Newsletter AJAX posts to your Mailchimp / FluentCRM form action — Themezur does not store subscribers.</li>
								<li><strong>Back to top</strong> floating button (scroll threshold).</li>
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
							<p>Footer works the same under <strong>Themezur → Footer</strong>.</p>
							<p>This is Themezur’s template-ID mode — it is separate from Elementor Theme Builder’s Header/Footer locations. Prefer one system so visitors do not get two headers.</p>',
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
						'title' => __( 'Conditional headers & footers', 'themezur' ),
						'body'  => __(
							'<p>Under <strong>Themezur → Assignments</strong>, use <strong>Conditional headers</strong> (Home / Shop / Blog) and <strong>Conditional footers</strong> (Home / Shop / Blog / Checkout) to override the global Header/Footer source mode:</p>
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
								<li><strong>Shop archive</strong> — columns (2–5), products per page, soft / bordered / minimal cards, result count &amp; sorting toggles.</li>
								<li><strong>Quick View</strong> — Themezur AJAX modal on product cards (not a third-party plugin).</li>
								<li><strong>Wishlist</strong> — Themezur heart button saving product IDs to <code>localStorage</code> (not YITH).</li>
								<li><strong>Hover image</strong> — swaps to the first gallery image on card hover.</li>
								<li><strong>New badge</strong> — day window (0–90; 0 = off) for recently published products. Sale <code>-X%</code> uses Single → Sale percent.</li>
								<li><strong>Shop sidebar</strong> — None / Left / Right. Assign widgets to <strong>Appearance → Widgets → Themezur Shop Sidebar</strong>. On mobile the sidebar opens as a filter drawer (widget content only).</li>
							</ul>
							<p>Styles use Themezur design tokens. Set Shop styling to default to disable Themezur polish. Assets load only on WooCommerce contexts (plus mini-cart CSS when the header drawer is on).</p>',
							'themezur'
						),
					),
					array(
						'title' => __( 'Single product', 'themezur' ),
						'body'  => __(
							'<p>Same tab → <strong>Single product</strong>:</p>
							<ul>
								<li><strong>Layout</strong> — Classic, Wide gallery (sticky summary), or Stacked (gallery above). Body classes + CSS only.</li>
								<li><strong>Sale percent badge</strong> — shows <code>-X%</code> instead of a plain Sale label (shop cards + single).</li>
								<li><strong>Rating / SKU / stock</strong> — toggles for summary meta.</li>
								<li><strong>Sticky Add to Cart</strong> — option key <code>sticky_cart</code>; desktop-only bar when the main button scrolls away. Lives in <code>woocommerce.js</code> (independent of Quick View).</li>
								<li><strong>Quantity stepper</strong> — +/− controls around the qty input (<code>quantity_stepper</code>).</li>
								<li><strong>Trust / shipping note</strong> — optional text under add to cart.</li>
								<li><strong>Related &amp; upsells</strong> — show/hide plus counts (0–8).</li>
							</ul>
							<p>Uses hooks + CSS (no full WooCommerce template copies). Elementor Theme Builder single templates still take priority when assigned.</p>',
							'themezur'
						),
					),
					array(
						'title' => __( 'Cart, checkout &amp; account', 'themezur' ),
						'body'  => __(
							'<p>Under <strong>Themezur → WooCommerce</strong> and <strong>Header → Middle</strong>:</p>
							<ul>
								<li><strong>Mini-cart drawer</strong> — enable under Header → Mini cart. Qty ± / remove via AJAX (<code>themezur_mini_cart</code>) inside the existing header drawer. Fragment root is <code>div.widget_shopping_cart_content</code>.</li>
								<li><strong>Open on add</strong> — <code>woocommerce.cart.open_on_add</code> opens the drawer after add-to-cart (requires header mini-cart).</li>
								<li><strong>Cart / checkout polish</strong> — token styling for tables, totals, fields, and Place order. Optional sticky order review (desktop) and checkout trust note above Place order.</li>
								<li><strong>My Account</strong> — welcome banner, Comfortable / Compact density, plus form &amp; orders table polish.</li>
							</ul>
							<p>No full WooCommerce template copies.</p>',
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
