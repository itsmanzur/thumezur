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
		add_action( 'wp_nav_menu_item_custom_fields', array( __CLASS__, 'render_custom_fields' ), 10, 5 );
		add_action( 'wp_update_nav_menu_item', array( __CLASS__, 'save_custom_fields' ), 10, 3 );
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
		$enable        = get_post_meta( $item_id, '_tz_mega_enable', true );
		$layout        = get_post_meta( $item_id, '_tz_mega_layout', true );
		$width         = get_post_meta( $item_id, '_tz_mega_width', true );
		$cols          = get_post_meta( $item_id, '_tz_mega_cols', true );
		$elementor_id  = get_post_meta( $item_id, '_tz_mega_elementor_id', true );
		$section_label = get_post_meta( $item_id, '_tz_mega_section_label', true );
		$icon          = get_post_meta( $item_id, '_tz_mega_icon', true );
		$desc          = get_post_meta( $item_id, '_tz_mega_desc', true );
		$badge         = get_post_meta( $item_id, '_tz_mega_badge', true );
		$badge_color   = get_post_meta( $item_id, '_tz_mega_badge_color', true );
		$bottom_text   = get_post_meta( $item_id, '_tz_mega_bottom_text', true );
		$btn_label     = get_post_meta( $item_id, '_tz_mega_bottom_btn_label', true );
		$btn_url       = get_post_meta( $item_id, '_tz_mega_bottom_btn_url', true );

		$layout      = $layout ? $layout : 'saas';
		$width       = $width ? $width : 'compact';
		$cols        = $cols ? (int) $cols : 2;
		$badge_color = $badge_color ? $badge_color : 'green';
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

				<p class="description description-wide" style="margin-bottom: 8px;">
					<label for="tz-mega-layout-<?php echo esc_attr( (string) $item_id ); ?>">
						<?php esc_html_e( 'Dropdown Layout Style', 'themezur' ); ?><br>
						<select id="tz-mega-layout-<?php echo esc_attr( (string) $item_id ); ?>" name="tz_mega_layout[<?php echo esc_attr( (string) $item_id ); ?>]" style="width: 100%;">
							<option value="saas" <?php selected( $layout, 'saas' ); ?>><?php esc_html_e( 'Rich SaaS / Tech Grid (Icons, Titles, Badges & Descs)', 'themezur' ); ?></option>
							<option value="grid" <?php selected( $layout, 'grid' ); ?>><?php esc_html_e( 'Standard Multi-Column Links Grid', 'themezur' ); ?></option>
							<option value="elementor" <?php selected( $layout, 'elementor' ); ?>><?php esc_html_e( 'Elementor / Block Saved Template', 'themezur' ); ?></option>
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

				<p class="description description-thin">
					<label for="tz-mega-icon-<?php echo esc_attr( (string) $item_id ); ?>">
						<?php esc_html_e( 'Item Icon (SVG name / Dashicon)', 'themezur' ); ?><br>
						<input type="text" id="tz-mega-icon-<?php echo esc_attr( (string) $item_id ); ?>" name="tz_mega_icon[<?php echo esc_attr( (string) $item_id ); ?>]" value="<?php echo esc_attr( $icon ); ?>" placeholder="scanner, heatmaps, ai, pdf..." class="widefat" />
					</label>
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
