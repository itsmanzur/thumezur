<?php
/**
 * Themezur Mega Menu custom admin fields & settings.
 *
 * @package Themezur
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Themezur_Mega_Menu
 */
class Themezur_Mega_Menu {

	/**
	 * Init hooks.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_admin_assets' ) );
		add_action( 'wp_nav_menu_item_custom_fields', array( __CLASS__, 'render_custom_fields' ), 10, 5 );
		add_action( 'wp_update_nav_menu_item', array( __CLASS__, 'save_custom_fields' ), 10, 3 );
	}

	/**
	 * Enqueue WP Media scripts on nav-menus.php.
	 *
	 * @param string $hook Admin page hook name.
	 * @return void
	 */
	public static function enqueue_admin_assets( $hook ) {
		if ( 'nav-menus.php' !== $hook ) {
			return;
		}

		wp_enqueue_media();
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );

		// Inline script for Media Upload & Icon Picker helper
		$inline_js = "
		jQuery(document).ready(function($){
			// Initialize color pickers
			$('.tz-color-field').wpColorPicker();

			// Media Upload Button Handler
			$(document).on('click', '.tz-mega-upload-btn', function(e){
				e.preventDefault();
				var btn = $(this);
				var input = btn.siblings('.tz-mega-icon-input');

				var customUploader = wp.media({
					title: 'Select Mega Menu Icon / Image',
					button: { text: 'Use this image' },
					multiple: false
				}).on('select', function() {
					var attachment = customUploader.state().get('selection').first().toJSON();
					input.val(attachment.url).trigger('change');
				}).open();
			});

			// Preset icon dropdown sync
			$(document).on('change', '.tz-mega-icon-preset', function(){
				var val = $(this).val();
				var input = $(this).siblings('.tz-mega-icon-input');
				if (val && val !== 'custom') {
					input.val(val);
				}
			});
		});
		";
		wp_add_inline_script( 'nav-menu', $inline_js );
	}

	/**
	 * Render custom admin fields inside Appearance -> Menus.
	 *
	 * @param int      $item_id Menu item ID.
	 * @param WP_Post  $item    Menu item data object.
	 * @param int      $depth   Depth of menu item.
	 * @param stdClass $args    Menu args.
	 * @param int      $id      Nav menu ID.
	 * @return void
	 */
	public static function render_custom_fields( $item_id, $item, $depth, $args, $id = 0 ) {
		$enable           = get_post_meta( $item_id, '_tz_mega_enable', true );
		$layout           = get_post_meta( $item_id, '_tz_mega_layout', true );
		$width            = get_post_meta( $item_id, '_tz_mega_width', true );
		$cols             = get_post_meta( $item_id, '_tz_mega_cols', true );
		$theme            = get_post_meta( $item_id, '_tz_mega_theme', true );
		$bg_color         = get_post_meta( $item_id, '_tz_mega_bg_color', true );
		$text_color       = get_post_meta( $item_id, '_tz_mega_text_color', true );
		$animation        = get_post_meta( $item_id, '_tz_mega_animation', true );
		$bg_image         = get_post_meta( $item_id, '_tz_mega_bg_image', true );
		$top_badge        = get_post_meta( $item_id, '_tz_mega_top_badge', true );
		$top_badge_color  = get_post_meta( $item_id, '_tz_mega_top_badge_color', true );
		$promo_enable     = get_post_meta( $item_id, '_tz_mega_promo_enable', true );
		$promo_img        = get_post_meta( $item_id, '_tz_mega_promo_img', true );
		$promo_title      = get_post_meta( $item_id, '_tz_mega_promo_title', true );
		$promo_desc       = get_post_meta( $item_id, '_tz_mega_promo_desc', true );
		$promo_btn_label  = get_post_meta( $item_id, '_tz_mega_promo_btn_label', true );
		$promo_btn_url    = get_post_meta( $item_id, '_tz_mega_promo_btn_url', true );
		$promo_badge      = get_post_meta( $item_id, '_tz_mega_promo_badge', true );
		$cat_grid_enable  = get_post_meta( $item_id, '_tz_mega_cat_grid_enable', true );
		$elementor_id     = get_post_meta( $item_id, '_tz_mega_elementor_id', true );
		$section_label    = get_post_meta( $item_id, '_tz_mega_section_label', true );
		$icon             = get_post_meta( $item_id, '_tz_mega_icon', true );
		$desc             = get_post_meta( $item_id, '_tz_mega_desc', true );
		$badge            = get_post_meta( $item_id, '_tz_mega_badge', true );
		$badge_color      = get_post_meta( $item_id, '_tz_mega_badge_color', true );
		$bottom_text      = get_post_meta( $item_id, '_tz_mega_bottom_text', true );
		$btn_label        = get_post_meta( $item_id, '_tz_mega_bottom_btn_label', true );
		$btn_url          = get_post_meta( $item_id, '_tz_mega_bottom_btn_url', true );

		$layout           = $layout ? $layout : 'saas';
		$width            = $width ? $width : 'compact';
		$cols             = $cols ? (int) $cols : 2;
		$theme            = $theme ? $theme : 'dark';
		$animation        = $animation ? $animation : 'slide';
		$top_badge_color  = $top_badge_color ? $top_badge_color : 'red';
		$badge_color      = $badge_color ? $badge_color : 'green';

		$presets = array(
			''                                => '— Select Preset Icon —',
			'scanner'                         => '🔍 Scanner / Audit (SVG)',
			'heatmaps'                        => '🔥 Heatmaps / Fire (SVG)',
			'analytics'                       => '📊 Analytics / Chart (SVG)',
			'widget'                          => '🧩 Widget / Modules (SVG)',
			'ai'                              => '🤖 AI Features / Sparkles (SVG)',
			'pdf'                             => '📄 PDF / Document (SVG)',
			'agency'                          => '🏢 Agency / Briefcase (SVG)',
			'crawler'                         => '🌐 Crawler / Site Scan (SVG)',
			'shopping'                        => '🛒 Shopping Cart (SVG)',
			'star'                            => '⭐️ Star / Featured (SVG)',
			'zap'                             => '⚡ Zap / Speed (SVG)',
			'lock'                            => '🔒 Security / Lock (SVG)',
			'gift'                            => '🎁 Gift / Offer (SVG)',
			'dashicons-admin-home'            => '🏠 Home (Dashicon)',
			'dashicons-store'                 => '🏪 Store / Shop (Dashicon)',
			'dashicons-cart'                  => '🛒 Cart (Dashicon)',
			'dashicons-heart'                 => '❤️ Heart / Wishlist (Dashicon)',
			'dashicons-category'              => '📂 Category (Dashicon)',
			'dashicons-tag'                   => '🏷️ Tag / Sale (Dashicon)',
			'dashicons-email'                 => '✉️ Email / Contact (Dashicon)',
			'dashicons-phone'                 => '📞 Phone (Dashicon)',
			'dashicons-location'              => '📍 Location / Map (Dashicon)',
			'dashicons-clock'                 => '⏰ Clock / Hours (Dashicon)',
			'dashicons-wordpress'             => '🔷 WordPress (Dashicon)',
			'dashicons-welcome-widgets-menus' => '⚙️ Widgets / Config (Dashicon)',
			'dashicons-shield'                => '🛡️ Shield / Guarantee (Dashicon)',
			'dashicons-desktop'               => '💻 Desktop / PC (Dashicon)',
			'dashicons-smartphone'            => '📱 Mobile (Dashicon)',
			'dashicons-camera'                => '📷 Camera (Dashicon)',
			'dashicons-video-alt3'            => '🎥 Video (Dashicon)',
			'dashicons-download'              => '⬇️ Download (Dashicon)',
			'dashicons-hammer'                => '🛠️ Tools / Services (Dashicon)',
			'dashicons-star-filled'           => '🌟 Star Rating (Dashicon)',
			'dashicons-info'                  => 'ℹ️ Info / Help (Dashicon)',
			'custom'                          => '🖼️ Custom Image / Uploaded URL',
		);
		$preset_selected = array_key_exists( $icon, $presets ) ? $icon : ( filter_var( $icon, FILTER_VALIDATE_URL ) ? 'custom' : '' );
		?>
		<div class="tz-menu-item-meta-wrap" style="clear: both; margin: 12px 0 8px; padding: 12px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px;">
			<h4 style="margin: 0 0 10px; font-weight: 700; color: #0f172a; font-size: 13px;">🚀 Themezur Mega Menu Options</h4>

			<?php if ( 0 === $depth ) : ?>
				<!-- Top Level Item Options -->
				<p class="description description-wide" style="margin-bottom: 8px;">
					<label for="tz-mega-enable-<?php echo esc_attr( (string) $item_id ); ?>">
						<input type="checkbox" id="tz-mega-enable-<?php echo esc_attr( (string) $item_id ); ?>" name="tz_mega_enable[<?php echo esc_attr( (string) $item_id ); ?>]" value="1" <?php checked( $enable, '1' ); ?> />
						<strong><?php esc_html_e( 'Enable Mega Menu Dropdown for this item', 'themezur' ); ?></strong>
					</label>
				</p>

				<!-- Main Bar Glowing Badge (Feature 4) -->
				<div class="description description-wide" style="margin-bottom: 8px; padding: 8px; background: #fff; border: 1px solid #cbd5e1; border-radius: 6px;">
					<strong>🔥 <?php esc_html_e( 'Main Bar Glowing Animated Badge (e.g. HOT, NEW, 50% OFF)', 'themezur' ); ?></strong><br>
					<div style="display: flex; gap: 8px; margin-top: 4px;">
						<input type="text" name="tz_mega_top_badge[<?php echo esc_attr( (string) $item_id ); ?>]" value="<?php echo esc_attr( $top_badge ); ?>" placeholder="HOT, NEW, 50% OFF" style="flex:1;" />
						<select name="tz_mega_top_badge_color[<?php echo esc_attr( (string) $item_id ); ?>]">
							<option value="red" <?php selected( $top_badge_color, 'red' ); ?>>🔴 Red Glow</option>
							<option value="green" <?php selected( $top_badge_color, 'green' ); ?>>🟢 Green Glow</option>
							<option value="purple" <?php selected( $top_badge_color, 'purple' ); ?>>🟣 Purple Glow</option>
							<option value="gold" <?php selected( $top_badge_color, 'gold' ); ?>>🟡 Gold Glow</option>
						</select>
					</div>
				</div>

				<p class="description description-thin">
					<label for="tz-mega-layout-<?php echo esc_attr( (string) $item_id ); ?>">
						<?php esc_html_e( 'Dropdown Layout', 'themezur' ); ?><br>
						<select id="tz-mega-layout-<?php echo esc_attr( (string) $item_id ); ?>" name="tz_mega_layout[<?php echo esc_attr( (string) $item_id ); ?>]" style="width: 100%;">
							<option value="saas" <?php selected( $layout, 'saas' ); ?>><?php esc_html_e( 'Rich SaaS Grid (Icons & Descs)', 'themezur' ); ?></option>
							<option value="grid" <?php selected( $layout, 'grid' ); ?>><?php esc_html_e( 'Standard Multi-Column Grid', 'themezur' ); ?></option>
							<option value="elementor" <?php selected( $layout, 'elementor' ); ?>><?php esc_html_e( 'Elementor Saved Template', 'themezur' ); ?></option>
						</select>
					</label>
				</p>

				<!-- Entrance Animation Selector (Feature 3) -->
				<p class="description description-thin">
					<label for="tz-mega-animation-<?php echo esc_attr( (string) $item_id ); ?>">
						✨ <?php esc_html_e( 'Entrance Animation', 'themezur' ); ?><br>
						<select id="tz-mega-animation-<?php echo esc_attr( (string) $item_id ); ?>" name="tz_mega_animation[<?php echo esc_attr( (string) $item_id ); ?>]" style="width: 100%;">
							<option value="slide" <?php selected( $animation, 'slide' ); ?>>Slide Down</option>
							<option value="fade" <?php selected( $animation, 'fade' ); ?>>Fade In</option>
							<option value="scale" <?php selected( $animation, 'scale' ); ?>>Scale Zoom Up</option>
							<option value="flip" <?php selected( $animation, 'flip' ); ?>>3D Flip Up</option>
						</select>
					</label>
				</p>

				<p class="description description-thin">
					<label for="tz-mega-theme-<?php echo esc_attr( (string) $item_id ); ?>">
						🎨 <?php esc_html_e( 'Color Theme', 'themezur' ); ?><br>
						<select id="tz-mega-theme-<?php echo esc_attr( (string) $item_id ); ?>" name="tz_mega_theme[<?php echo esc_attr( (string) $item_id ); ?>]" style="width: 100%;">
							<option value="dark" <?php selected( $theme, 'dark' ); ?>><?php esc_html_e( 'Dark SaaS Card (#0f172a)', 'themezur' ); ?></option>
							<option value="light" <?php selected( $theme, 'light' ); ?>><?php esc_html_e( 'Clean White Card (#ffffff)', 'themezur' ); ?></option>
							<option value="custom" <?php selected( $theme, 'custom' ); ?>><?php esc_html_e( 'Custom Colors', 'themezur' ); ?></option>
						</select>
					</label>
				</p>

				<p class="description description-thin">
					<label for="tz-mega-width-<?php echo esc_attr( (string) $item_id ); ?>">
						<?php esc_html_e( 'Panel Width', 'themezur' ); ?><br>
						<select id="tz-mega-width-<?php echo esc_attr( (string) $item_id ); ?>" name="tz_mega_width[<?php echo esc_attr( (string) $item_id ); ?>]">
							<option value="compact" <?php selected( $width, 'compact' ); ?>><?php esc_html_e( 'Compact (750px)', 'themezur' ); ?></option>
							<option value="container" <?php selected( $width, 'container' ); ?>><?php esc_html_e( 'Container (1200px)', 'themezur' ); ?></option>
							<option value="full" <?php selected( $width, 'full' ); ?>><?php esc_html_e( 'Full Width (100%)', 'themezur' ); ?></option>
						</select>
					</label>
				</p>

				<p class="description description-thin">
					<label for="tz-mega-cols-<?php echo esc_attr( (string) $item_id ); ?>">
						<?php esc_html_e( 'Columns Count', 'themezur' ); ?><br>
						<select id="tz-mega-cols-<?php echo esc_attr( (string) $item_id ); ?>" name="tz_mega_cols[<?php echo esc_attr( (string) $item_id ); ?>]">
							<option value="2" <?php selected( $cols, 2 ); ?>>2 Columns</option>
							<option value="3" <?php selected( $cols, 3 ); ?>>3 Columns</option>
							<option value="4" <?php selected( $cols, 4 ); ?>>4 Columns</option>
						</select>
					</label>
				</p>

				<!-- Panel Background Image Overlay (Feature 2) -->
				<p class="description description-wide" style="margin-top: 6px;">
					<label>🖼️ <?php esc_html_e( 'Dropdown Panel Background Image Overlay', 'themezur' ); ?></label><br>
					<div style="display: flex; gap: 6px;">
						<input type="text" class="tz-mega-icon-input widefat" name="tz_mega_bg_image[<?php echo esc_attr( (string) $item_id ); ?>]" value="<?php echo esc_attr( $bg_image ); ?>" placeholder="https://..." />
						<button type="button" class="button tz-mega-upload-btn" style="white-space: nowrap;">🖼️ <?php esc_html_e( 'Upload Image', 'themezur' ); ?></button>
					</div>
				</p>

				<!-- WooCommerce Categories Grid Option (Feature 5) -->
				<p class="description description-wide" style="margin-top: 8px;">
					<label for="tz-mega-cat-grid-<?php echo esc_attr( (string) $item_id ); ?>">
						<input type="checkbox" id="tz-mega-cat-grid-<?php echo esc_attr( (string) $item_id ); ?>" name="tz_mega_cat_grid_enable[<?php echo esc_attr( (string) $item_id ); ?>]" value="1" <?php checked( $cat_grid_enable, '1' ); ?> />
						<strong>📂 <?php esc_html_e( 'Auto Render WooCommerce Product Categories Grid with Thumbnails', 'themezur' ); ?></strong>
					</label>
				</p>

				<!-- Featured Product / Promo Card Column (Feature 1) -->
				<div class="description description-wide" style="margin-top: 10px; padding: 10px; background: #fff; border: 1px solid #cbd5e1; border-radius: 6px;">
					<label for="tz-mega-promo-enable-<?php echo esc_attr( (string) $item_id ); ?>">
						<input type="checkbox" id="tz-mega-promo-enable-<?php echo esc_attr( (string) $item_id ); ?>" name="tz_mega_promo_enable[<?php echo esc_attr( (string) $item_id ); ?>]" value="1" <?php checked( $promo_enable, '1' ); ?> />
						<strong>🛍️ <?php esc_html_e( 'Add Featured Product / Promo Banner Card Column', 'themezur' ); ?></strong>
					</label>
					<div style="margin-top: 8px;" x-show="true">
						<div style="display: flex; gap: 6px; margin-bottom: 6px;">
							<input type="text" class="tz-mega-icon-input widefat" name="tz_mega_promo_img[<?php echo esc_attr( (string) $item_id ); ?>]" value="<?php echo esc_attr( $promo_img ); ?>" placeholder="Product / Banner Image URL" />
							<button type="button" class="button tz-mega-upload-btn" style="white-space: nowrap;">🖼️ <?php esc_html_e( 'Upload', 'themezur' ); ?></button>
						</div>
						<div style="display: flex; gap: 6px; margin-bottom: 6px;">
							<input type="text" name="tz_mega_promo_title[<?php echo esc_attr( (string) $item_id ); ?>]" value="<?php echo esc_attr( $promo_title ); ?>" placeholder="Card Title (e.g. Summer Special Deal)" style="flex:1.5;" />
							<input type="text" name="tz_mega_promo_badge[<?php echo esc_attr( (string) $item_id ); ?>]" value="<?php echo esc_attr( $promo_badge ); ?>" placeholder="Badge (50% OFF)" style="flex:1;" />
						</div>
						<input type="text" name="tz_mega_promo_desc[<?php echo esc_attr( (string) $item_id ); ?>]" value="<?php echo esc_attr( $promo_desc ); ?>" placeholder="Subtitle / Price (e.g. Starting from $49.00)" class="widefat" style="margin-bottom: 6px;" />
						<div style="display: flex; gap: 6px;">
							<input type="text" name="tz_mega_promo_btn_label[<?php echo esc_attr( (string) $item_id ); ?>]" value="<?php echo esc_attr( $promo_btn_label ); ?>" placeholder="Button Text (Shop Now →)" style="flex:1;" />
							<input type="text" name="tz_mega_promo_btn_url[<?php echo esc_attr( (string) $item_id ); ?>]" value="<?php echo esc_attr( $promo_btn_url ); ?>" placeholder="Link URL (https://...)" style="flex:1;" />
						</div>
					</div>
				</div>

				<div class="description description-wide" style="margin-top: 6px; display: flex; gap: 12px; align-items: center;">
					<label>
						<?php esc_html_e( 'Custom Background:', 'themezur' ); ?>
						<input type="text" class="tz-color-field" name="tz_mega_bg_color[<?php echo esc_attr( (string) $item_id ); ?>]" value="<?php echo esc_attr( $bg_color ); ?>" data-default-color="#0f172a" />
					</label>
					<label>
						<?php esc_html_e( 'Custom Text Color:', 'themezur' ); ?>
						<input type="text" class="tz-color-field" name="tz_mega_text_color[<?php echo esc_attr( (string) $item_id ); ?>]" value="<?php echo esc_attr( $text_color ); ?>" data-default-color="#ffffff" />
					</label>
				</div>

				<p class="description description-wide" style="margin-top: 8px;">
					<label for="tz-mega-elementor-id-<?php echo esc_attr( (string) $item_id ); ?>">
						<?php esc_html_e( 'Elementor Template ID (If Elementor Layout chosen)', 'themezur' ); ?><br>
						<input type="number" id="tz-mega-elementor-id-<?php echo esc_attr( (string) $item_id ); ?>" name="tz_mega_elementor_id[<?php echo esc_attr( (string) $item_id ); ?>]" value="<?php echo esc_attr( (string) $elementor_id ); ?>" placeholder="e.g. 1234" class="widefat" />
					</label>
				</p>

				<p class="description description-wide" style="margin-top: 8px;">
					<label for="tz-mega-bottom-text-<?php echo esc_attr( (string) $item_id ); ?>">
						<?php esc_html_e( 'Bottom Banner Text (Optional)', 'themezur' ); ?><br>
						<input type="text" id="tz-mega-bottom-text-<?php echo esc_attr( (string) $item_id ); ?>" name="tz_mega_bottom_text[<?php echo esc_attr( (string) $item_id ); ?>]" value="<?php echo esc_attr( $bottom_text ); ?>" placeholder="Free plan includes scanner, heatmaps, analytics + widget" class="widefat" />
					</label>
				</p>

				<p class="description description-thin">
					<label for="tz-mega-btn-label-<?php echo esc_attr( (string) $item_id ); ?>">
						<?php esc_html_e( 'Bottom Button Text', 'themezur' ); ?><br>
						<input type="text" id="tz-mega-btn-label-<?php echo esc_attr( (string) $item_id ); ?>" name="tz_mega_bottom_btn_label[<?php echo esc_attr( (string) $item_id ); ?>]" value="<?php echo esc_attr( $btn_label ); ?>" placeholder="Install free →" class="widefat" />
					</label>
				</p>

				<p class="description description-thin">
					<label for="tz-mega-btn-url-<?php echo esc_attr( (string) $item_id ); ?>">
						<?php esc_html_e( 'Bottom Button Link URL', 'themezur' ); ?><br>
						<input type="text" id="tz-mega-btn-url-<?php echo esc_attr( (string) $item_id ); ?>" name="tz_mega_bottom_btn_url[<?php echo esc_attr( (string) $item_id ); ?>]" value="<?php echo esc_attr( $btn_url ); ?>" placeholder="https://..." class="widefat" />
					</label>
				</p>

			<?php else : ?>
				<!-- Sub-Item / Column Item Options -->
				<p class="description description-wide" style="margin-bottom: 6px;">
					<label for="tz-mega-section-label-<?php echo esc_attr( (string) $item_id ); ?>">
						<?php esc_html_e( 'Section Group Header (e.g. FREE, PRO, AGENCY, CATEGORIES)', 'themezur' ); ?><br>
						<input type="text" id="tz-mega-section-label-<?php echo esc_attr( (string) $item_id ); ?>" name="tz_mega_section_label[<?php echo esc_attr( (string) $item_id ); ?>]" value="<?php echo esc_attr( $section_label ); ?>" placeholder="FREE" class="widefat" />
					</label>
				</p>

				<p class="description description-wide" style="margin-bottom: 6px;">
					<label><?php esc_html_e( 'Item Icon (Select Preset or Upload Image/SVG)', 'themezur' ); ?></label><br>
					<select class="tz-mega-icon-preset" style="width: 100%; margin-bottom: 4px;">
						<?php foreach ( $presets as $p_val => $p_name ) : ?>
							<option value="<?php echo esc_attr( $p_val ); ?>" <?php selected( $preset_selected, $p_val ); ?>><?php echo esc_html( $p_name ); ?></option>
						<?php endforeach; ?>
					</select>
					<div style="display: flex; gap: 6px; align-items: center;">
						<input type="text" class="tz-mega-icon-input widefat" id="tz-mega-icon-<?php echo esc_attr( (string) $item_id ); ?>" name="tz_mega_icon[<?php echo esc_attr( (string) $item_id ); ?>]" value="<?php echo esc_attr( $icon ); ?>" placeholder="scanner, ai, or image URL" />
						<button type="button" class="button tz-mega-upload-btn" style="white-space: nowrap;">🖼️ <?php esc_html_e( 'Upload', 'themezur' ); ?></button>
					</div>
				</p>

				<p class="description description-thin">
					<label for="tz-mega-badge-<?php echo esc_attr( (string) $item_id ); ?>">
						<?php esc_html_e( 'Badge Text (PRO, AGENCY, HOT)', 'themezur' ); ?><br>
						<input type="text" id="tz-mega-badge-<?php echo esc_attr( (string) $item_id ); ?>" name="tz_mega_badge[<?php echo esc_attr( (string) $item_id ); ?>]" value="<?php echo esc_attr( $badge ); ?>" placeholder="PRO" class="widefat" />
					</label>
				</p>

				<p class="description description-thin">
					<label for="tz-mega-badge-color-<?php echo esc_attr( (string) $item_id ); ?>">
						<?php esc_html_e( 'Badge Color Theme', 'themezur' ); ?><br>
						<select id="tz-mega-badge-color-<?php echo esc_attr( (string) $item_id ); ?>" name="tz_mega_badge_color[<?php echo esc_attr( (string) $item_id ); ?>]" style="width: 100%;">
							<option value="green" <?php selected( $badge_color, 'green' ); ?>>Green (e.g. PRO)</option>
							<option value="purple" <?php selected( $badge_color, 'purple' ); ?>>Purple (e.g. AGENCY)</option>
							<option value="blue" <?php selected( $badge_color, 'blue' ); ?>>Blue</option>
							<option value="red" <?php selected( $badge_color, 'red' ); ?>>Red (e.g. HOT/SALE)</option>
						</select>
					</label>
				</p>

				<p class="description description-wide" style="margin-top: 6px;">
					<label for="tz-mega-desc-<?php echo esc_attr( (string) $item_id ); ?>">
						<?php esc_html_e( 'Item Short Subtitle / Description', 'themezur' ); ?><br>
						<input type="text" id="tz-mega-desc-<?php echo esc_attr( (string) $item_id ); ?>" name="tz_mega_desc[<?php echo esc_attr( (string) $item_id ); ?>]" value="<?php echo esc_attr( $desc ); ?>" placeholder="Unlimited A/AA/AAA scans with element-level results" class="widefat" />
					</label>
				</p>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Save custom admin fields.
	 *
	 * @param int   $menu_id         Nav menu ID.
	 * @param int   $menu_item_db_id Menu item DB ID.
	 * @param array $args            Menu args.
	 * @return void
	 */
	public static function save_custom_fields( $menu_id, $menu_item_db_id, $args ) {
		// Enable mega menu
		$enable = ! empty( $_POST['tz_mega_enable'][ $menu_item_db_id ] ) ? '1' : '';
		update_post_meta( $menu_item_db_id, '_tz_mega_enable', $enable );

		// Layout
		if ( isset( $_POST['tz_mega_layout'][ $menu_item_db_id ] ) ) {
			$layout = sanitize_key( $_POST['tz_mega_layout'][ $menu_item_db_id ] );
			update_post_meta( $menu_item_db_id, '_tz_mega_layout', $layout );
		}

		// Width
		if ( isset( $_POST['tz_mega_width'][ $menu_item_db_id ] ) ) {
			$width = sanitize_key( $_POST['tz_mega_width'][ $menu_item_db_id ] );
			update_post_meta( $menu_item_db_id, '_tz_mega_width', $width );
		}

		// Cols
		if ( isset( $_POST['tz_mega_cols'][ $menu_item_db_id ] ) ) {
			$cols = absint( $_POST['tz_mega_cols'][ $menu_item_db_id ] );
			update_post_meta( $menu_item_db_id, '_tz_mega_cols', $cols );
		}

		// Theme
		if ( isset( $_POST['tz_mega_theme'][ $menu_item_db_id ] ) ) {
			$theme = sanitize_key( $_POST['tz_mega_theme'][ $menu_item_db_id ] );
			update_post_meta( $menu_item_db_id, '_tz_mega_theme', $theme );
		}
		if ( isset( $_POST['tz_mega_bg_color'][ $menu_item_db_id ] ) ) {
			update_post_meta( $menu_item_db_id, '_tz_mega_bg_color', sanitize_hex_color( $_POST['tz_mega_bg_color'][ $menu_item_db_id ] ) );
		}
		if ( isset( $_POST['tz_mega_text_color'][ $menu_item_db_id ] ) ) {
			update_post_meta( $menu_item_db_id, '_tz_mega_text_color', sanitize_hex_color( $_POST['tz_mega_text_color'][ $menu_item_db_id ] ) );
		}

		// Animation (Feature 3)
		if ( isset( $_POST['tz_mega_animation'][ $menu_item_db_id ] ) ) {
			update_post_meta( $menu_item_db_id, '_tz_mega_animation', sanitize_key( $_POST['tz_mega_animation'][ $menu_item_db_id ] ) );
		}

		// Panel Background Image (Feature 2)
		if ( isset( $_POST['tz_mega_bg_image'][ $menu_item_db_id ] ) ) {
			update_post_meta( $menu_item_db_id, '_tz_mega_bg_image', esc_url_raw( $_POST['tz_mega_bg_image'][ $menu_item_db_id ] ) );
		}

		// Main Bar Glowing Badge (Feature 4)
		if ( isset( $_POST['tz_mega_top_badge'][ $menu_item_db_id ] ) ) {
			update_post_meta( $menu_item_db_id, '_tz_mega_top_badge', sanitize_text_field( $_POST['tz_mega_top_badge'][ $menu_item_db_id ] ) );
		}
		if ( isset( $_POST['tz_mega_top_badge_color'][ $menu_item_db_id ] ) ) {
			update_post_meta( $menu_item_db_id, '_tz_mega_top_badge_color', sanitize_key( $_POST['tz_mega_top_badge_color'][ $menu_item_db_id ] ) );
		}

		// Promo Card Column (Feature 1)
		$promo_enable = ! empty( $_POST['tz_mega_promo_enable'][ $menu_item_db_id ] ) ? '1' : '';
		update_post_meta( $menu_item_db_id, '_tz_mega_promo_enable', $promo_enable );

		if ( isset( $_POST['tz_mega_promo_img'][ $menu_item_db_id ] ) ) {
			update_post_meta( $menu_item_db_id, '_tz_mega_promo_img', esc_url_raw( $_POST['tz_mega_promo_img'][ $menu_item_db_id ] ) );
		}
		if ( isset( $_POST['tz_mega_promo_title'][ $menu_item_db_id ] ) ) {
			update_post_meta( $menu_item_db_id, '_tz_mega_promo_title', sanitize_text_field( $_POST['tz_mega_promo_title'][ $menu_item_db_id ] ) );
		}
		if ( isset( $_POST['tz_mega_promo_desc'][ $menu_item_db_id ] ) ) {
			update_post_meta( $menu_item_db_id, '_tz_mega_promo_desc', sanitize_text_field( $_POST['tz_mega_promo_desc'][ $menu_item_db_id ] ) );
		}
		if ( isset( $_POST['tz_mega_promo_btn_label'][ $menu_item_db_id ] ) ) {
			update_post_meta( $menu_item_db_id, '_tz_mega_promo_btn_label', sanitize_text_field( $_POST['tz_mega_promo_btn_label'][ $menu_item_db_id ] ) );
		}
		if ( isset( $_POST['tz_mega_promo_btn_url'][ $menu_item_db_id ] ) ) {
			update_post_meta( $menu_item_db_id, '_tz_mega_promo_btn_url', esc_url_raw( $_POST['tz_mega_promo_btn_url'][ $menu_item_db_id ] ) );
		}
		if ( isset( $_POST['tz_mega_promo_badge'][ $menu_item_db_id ] ) ) {
			update_post_meta( $menu_item_db_id, '_tz_mega_promo_badge', sanitize_text_field( $_POST['tz_mega_promo_badge'][ $menu_item_db_id ] ) );
		}

		// WooCommerce Cat Grid (Feature 5)
		$cat_grid = ! empty( $_POST['tz_mega_cat_grid_enable'][ $menu_item_db_id ] ) ? '1' : '';
		update_post_meta( $menu_item_db_id, '_tz_mega_cat_grid_enable', $cat_grid );

		// Elementor ID
		if ( isset( $_POST['tz_mega_elementor_id'][ $menu_item_db_id ] ) ) {
			$eid = absint( $_POST['tz_mega_elementor_id'][ $menu_item_db_id ] );
			update_post_meta( $menu_item_db_id, '_tz_mega_elementor_id', $eid );
		}

		// Bottom text & link
		if ( isset( $_POST['tz_mega_bottom_text'][ $menu_item_db_id ] ) ) {
			update_post_meta( $menu_item_db_id, '_tz_mega_bottom_text', sanitize_text_field( $_POST['tz_mega_bottom_text'][ $menu_item_db_id ] ) );
		}
		if ( isset( $_POST['tz_mega_bottom_btn_label'][ $menu_item_db_id ] ) ) {
			update_post_meta( $menu_item_db_id, '_tz_mega_bottom_btn_label', sanitize_text_field( $_POST['tz_mega_bottom_btn_label'][ $menu_item_db_id ] ) );
		}
		if ( isset( $_POST['tz_mega_bottom_btn_url'][ $menu_item_db_id ] ) ) {
			update_post_meta( $menu_item_db_id, '_tz_mega_bottom_btn_url', esc_url_raw( $_POST['tz_mega_bottom_btn_url'][ $menu_item_db_id ] ) );
		}

		// Section Header
		if ( isset( $_POST['tz_mega_section_label'][ $menu_item_db_id ] ) ) {
			update_post_meta( $menu_item_db_id, '_tz_mega_section_label', sanitize_text_field( $_POST['tz_mega_section_label'][ $menu_item_db_id ] ) );
		}

		// Icon
		if ( isset( $_POST['tz_mega_icon'][ $menu_item_db_id ] ) ) {
			update_post_meta( $menu_item_db_id, '_tz_mega_icon', sanitize_text_field( $_POST['tz_mega_icon'][ $menu_item_db_id ] ) );
		}

		// Badge & color
		if ( isset( $_POST['tz_mega_badge'][ $menu_item_db_id ] ) ) {
			update_post_meta( $menu_item_db_id, '_tz_mega_badge', sanitize_text_field( $_POST['tz_mega_badge'][ $menu_item_db_id ] ) );
		}
		if ( isset( $_POST['tz_mega_badge_color'][ $menu_item_db_id ] ) ) {
			update_post_meta( $menu_item_db_id, '_tz_mega_badge_color', sanitize_key( $_POST['tz_mega_badge_color'][ $menu_item_db_id ] ) );
		}

		// Desc
		if ( isset( $_POST['tz_mega_desc'][ $menu_item_db_id ] ) ) {
			update_post_meta( $menu_item_db_id, '_tz_mega_desc', sanitize_text_field( $_POST['tz_mega_desc'][ $menu_item_db_id ] ) );
		}
	}
}

Themezur_Mega_Menu::init();
