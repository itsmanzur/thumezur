<?php
/**
 * Custom Walker_Nav_Menu for Themezur Mega Menu.
 *
 * @package Themezur
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Themezur_Mega_Walker
 */
class Themezur_Mega_Walker extends Walker_Nav_Menu {

	/**
	 * Track top-level item being processed.
	 *
	 * @var WP_Post|null
	 */
	private $current_top_item = null;

	/**
	 * Track whether a mega column container is open.
	 *
	 * @var bool
	 */
	private $in_mega_col = false;

	/**
	 * When Elementor mega panel already rendered — skip walking children into broken markup.
	 *
	 * @var bool
	 */
	private $skip_children = false;

	/**
	 * Whether start_lvl already opened a mega panel for the current top item.
	 *
	 * @var bool
	 */
	private $mega_panel_opened = false;

	/**
	 * Whether current top item uses tabbed mega layout.
	 *
	 * @var bool
	 */
	private $tabs_enabled = false;

	/**
	 * Mega is active for current top item (enable + schedule).
	 *
	 * @param int $item_id Menu item ID.
	 * @return bool
	 */
	private function is_mega_active( $item_id ) {
		if ( ! get_post_meta( $item_id, '_tz_mega_enable', true ) ) {
			return false;
		}
		return Themezur_Mega_Content::is_schedule_active( (int) $item_id );
	}

	/**
	 * Traverse elements — skip children when an Elementor mega panel already consumed them.
	 *
	 * @param object $element           Data object.
	 * @param array  $children_elements List of elements to continue traversing.
	 * @param int    $max_depth         Max depth to traverse.
	 * @param int    $depth             Depth of current element.
	 * @param array  $args              An array of arguments.
	 * @param string $output            Used to append additional content.
	 * @return void
	 */
	public function display_element( $element, &$children_elements, $max_depth, $depth, $args, &$output ) {
		if ( ! $element ) {
			return;
		}

		$id_field           = $this->db_fields['id'];
		$id                 = $element->$id_field;
		$this->has_children = ! empty( $children_elements[ $id ] );
		if ( isset( $args[0] ) && is_object( $args[0] ) ) {
			$args[0]->has_children = $this->has_children;
		}

		$cb_args = array_merge( array( &$output, $element, $depth ), $args );
		call_user_func_array( array( $this, 'start_el' ), $cb_args );

		if ( ( 0 === (int) $max_depth || $max_depth > $depth + 1 ) && ! empty( $children_elements[ $id ] ) ) {
			foreach ( $children_elements[ $id ] as $child ) {
				if ( ! isset( $newlevel ) ) {
					$newlevel = true;
					$cb_args  = array_merge( array( &$output, $depth ), $args );
					call_user_func_array( array( $this, 'start_lvl' ), $cb_args );

					// Elementor mega already rendered a closed panel — do not walk children.
					if ( $this->skip_children ) {
						unset( $children_elements[ $id ] );
						break;
					}
				}
				$this->display_element( $child, $children_elements, $max_depth, $depth + 1, $args, $output );
			}
			unset( $children_elements[ $id ] );
		}

		if ( isset( $newlevel ) && $newlevel ) {
			$cb_args = array_merge( array( &$output, $depth ), $args );
			call_user_func_array( array( $this, 'end_lvl' ), $cb_args );
		}

		$cb_args = array_merge( array( &$output, $element, $depth ), $args );
		call_user_func_array( array( $this, 'end_el' ), $cb_args );
	}

	/**
	 * Starts the list before elements are added.
	 *
	 * @param string   $output Used to append additional content.
	 * @param int      $depth  Depth of menu item.
	 * @param stdClass $args   An object of wp_nav_menu() arguments.
	 * @return void
	 */
	public function start_lvl( &$output, $depth = 0, $args = null ) {
		if ( $this->skip_children ) {
			return;
		}

		$indent = str_repeat( "\t", $depth );

		if ( 0 === $depth && $this->current_top_item ) {
			$item_id = (int) $this->current_top_item->ID;
			$is_mega = $this->is_mega_active( $item_id );
			$layout  = get_post_meta( $item_id, '_tz_mega_layout', true );
			$layout  = $layout ? $layout : 'saas';

			if ( $is_mega ) {
				if ( 'elementor' === $layout ) {
					$output                  .= $this->get_elementor_panel_html( $item_id, $indent );
					$this->mega_panel_opened = true;
					$this->skip_children     = true;
					return;
				}

				$this->in_mega_col       = false;
				$this->mega_panel_opened = true;
				$this->tabs_enabled      = (bool) get_post_meta( $item_id, '_tz_mega_tabs_enable', true );
				$output                 .= $this->get_mega_panel_open_html( $item_id, $indent );
				return;
			}
		}

		$classes = array( 'sub-menu' );
		if ( 0 === $depth ) {
			$classes[] = 'tz-dropdown';
		}
		$class_names = implode( ' ', $classes );
		$output     .= "\n{$indent}<ul class=\"{$class_names}\">\n";
	}

	/**
	 * Ends the list after elements are added.
	 *
	 * @param string   $output Used to append additional content.
	 * @param int      $depth  Depth of menu item.
	 * @param stdClass $args   An object of wp_nav_menu() arguments.
	 * @return void
	 */
	public function end_lvl( &$output, $depth = 0, $args = null ) {
		$indent = str_repeat( "\t", $depth );

		if ( 0 === $depth && $this->current_top_item ) {
			$item_id = (int) $this->current_top_item->ID;
			$is_mega = $this->is_mega_active( $item_id );
			$layout  = get_post_meta( $item_id, '_tz_mega_layout', true );
			$layout  = $layout ? $layout : 'saas';

			if ( $is_mega && 'elementor' === $layout ) {
				// Panel already fully closed in start_lvl; do not emit </ul>.
				$this->skip_children = false;
				return;
			}

			if ( $is_mega && 'elementor' !== $layout ) {
				$this->close_mega_panel_body( $output, $item_id, $indent );
				return;
			}
		}

		$output .= "{$indent}</ul>\n";
	}

	/**
	 * Start element output.
	 *
	 * @param string   $output Used to append additional content.
	 * @param WP_Post  $item   Menu item data object.
	 * @param int      $depth  Depth of menu item.
	 * @param stdClass $args   An object of wp_nav_menu() arguments.
	 * @param int      $id     Current item ID.
	 * @return void
	 */
	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		if ( 0 === $depth ) {
			$this->current_top_item  = $item;
			$this->in_mega_col       = false;
			$this->skip_children     = false;
			$this->mega_panel_opened = false;
			$this->tabs_enabled      = false;
		}

		if ( $depth > 0 && $this->skip_children ) {
			return;
		}

		$is_mega_parent = ( $this->current_top_item && $this->is_mega_active( (int) $this->current_top_item->ID ) );

		if ( $depth > 0 && $is_mega_parent ) {
			$section_label = get_post_meta( $item->ID, '_tz_mega_section_label', true );

			if ( $section_label || ! $this->in_mega_col ) {
				if ( $this->in_mega_col ) {
					$output .= "</div><!-- /.tz-mega-col -->\n";
				}
				$output          .= "<div class=\"tz-mega-col\">\n";
				$this->in_mega_col = true;

				if ( $section_label ) {
					$output .= '<div class="tz-mega-section__label"><span>' . esc_html( $section_label ) . '</span></div>' . "\n";
				}
			}

			$atts           = array();
			$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
			$atts['target'] = ! empty( $item->target ) ? $item->target : '';
			$atts['rel']    = ! empty( $item->xfn ) ? $item->xfn : '';
			$atts['href']   = ! empty( $item->url ) ? $item->url : '';

			$atts       = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );
			$attributes = '';
			foreach ( $atts as $attr => $value ) {
				if ( ! empty( $value ) ) {
					$value       = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
					$attributes .= ' ' . $attr . '="' . $value . '"';
				}
			}

			$title = apply_filters( 'the_title', $item->title, $item->ID );
			$title = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );

			$icon        = get_post_meta( $item->ID, '_tz_mega_icon', true );
			$desc        = get_post_meta( $item->ID, '_tz_mega_desc', true );
			$badge       = get_post_meta( $item->ID, '_tz_mega_badge', true );
			$badge_color = get_post_meta( $item->ID, '_tz_mega_badge_color', true );
			$badge_color = $badge_color ? $badge_color : 'green';

			$item_output = '<a class="tz-mega-item"' . $attributes . '>';
			if ( $icon ) {
				$item_output .= '<div class="tz-mega-item__icon-wrap">' . self::get_icon_svg( $icon ) . '</div>';
			}
			$item_output .= '<div class="tz-mega-item__content">';
			$item_output .= '<div class="tz-mega-item__head">';
			$item_output .= '<span class="tz-mega-item__title">' . $title . '</span>';
			if ( $badge ) {
				$item_output .= '<span class="tz-mega-badge tz-mega-badge--' . esc_attr( $badge_color ) . '">' . esc_html( $badge ) . '</span>';
			}
			$item_output .= '</div>';
			if ( $desc ) {
				$item_output .= '<p class="tz-mega-item__desc">' . esc_html( $desc ) . '</p>';
			}
			$item_output .= '</div>';
			$item_output .= '</a>' . "\n";

			$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
			return;
		}

		$indent    = ( $depth ) ? str_repeat( "\t", $depth ) : '';
		$classes   = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'menu-item-' . $item->ID;

		$is_mega_top = ( 0 === $depth && $this->is_mega_active( (int) $item->ID ) );
		if ( $is_mega_top ) {
			$classes[] = 'menu-item-has-children';
			$classes[] = 'tz-menu-item--mega';
			if ( get_post_meta( $item->ID, '_tz_mega_tabs_enable', true ) ) {
				$classes[] = 'tz-menu-item--mega-tabs';
			}
		}

		$class_names = implode( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

		$id_attr = apply_filters( 'nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args, $depth );
		$id_attr = $id_attr ? ' id="' . esc_attr( $id_attr ) . '"' : '';

		$output .= $indent . '<li' . $id_attr . $class_names . '>';

		$atts           = array();
		$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
		$atts['target'] = ! empty( $item->target ) ? $item->target : '';
		$atts['rel']    = ! empty( $item->xfn ) ? $item->xfn : '';
		$atts['href']   = ! empty( $item->url ) ? $item->url : '';
		if ( $is_mega_top ) {
			$atts['aria-haspopup'] = 'true';
			$atts['aria-expanded'] = 'false';
		}

		$atts       = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );
		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( ! empty( $value ) || '0' === $value ) {
				$value       = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}

		$title = apply_filters( 'the_title', $item->title, $item->ID );
		$title = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );

		$top_badge = ( 0 === $depth ) ? get_post_meta( $item->ID, '_tz_mega_top_badge', true ) : '';
		$top_color = ( 0 === $depth ) ? get_post_meta( $item->ID, '_tz_mega_top_badge_color', true ) : 'red';
		$top_color = $top_color ? $top_color : 'red';

		$item_output  = $args->before ?? '';
		$item_output .= '<a' . $attributes . '>';
		$item_output .= ( $args->link_before ?? '' ) . $title . ( $args->link_after ?? '' );
		if ( $top_badge ) {
			$item_output .= '<span class="tz-glowing-badge tz-glowing-badge--' . esc_attr( $top_color ) . '">' . esc_html( $top_badge ) . '</span>';
		}
		$item_output .= '</a>';
		$item_output .= $args->after ?? '';

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}

	/**
	 * End element output.
	 *
	 * @param string   $output Used to append additional content.
	 * @param WP_Post  $item   Page data object.
	 * @param int      $depth  Depth of page.
	 * @param stdClass $args   An object of wp_nav_menu() arguments.
	 * @return void
	 */
	public function end_el( &$output, $item, $depth = 0, $args = null ) {
		if ( $depth > 0 && $this->skip_children ) {
			return;
		}

		$is_mega_parent = ( $this->current_top_item && $this->is_mega_active( (int) $this->current_top_item->ID ) );

		if ( $depth > 0 && $is_mega_parent ) {
			return;
		}

		// Mega with zero children: WP never calls start_lvl — inject panel here.
		if ( 0 === $depth && $is_mega_parent && ! $this->mega_panel_opened ) {
			$layout = get_post_meta( $item->ID, '_tz_mega_layout', true );
			$layout = $layout ? $layout : 'saas';
			if ( 'elementor' === $layout ) {
				$output                  .= $this->get_elementor_panel_html( (int) $item->ID, "\t" );
				$this->mega_panel_opened = true;
			} else {
				$this->tabs_enabled      = (bool) get_post_meta( $item->ID, '_tz_mega_tabs_enable', true );
				$output                 .= $this->get_mega_panel_open_html( (int) $item->ID, "\t" );
				// Close empty grid + extras the same as end_lvl.
				$fake = '';
				$this->close_mega_panel_body( $fake, (int) $item->ID, "\t" );
				$output                 .= $fake;
				$this->mega_panel_opened = true;
			}
		}

		$output .= "</li>\n";
	}

	/**
	 * Close mega grid / tabs / promo / footer (shared by end_lvl and zero-child path).
	 *
	 * @param string $output  Output by ref.
	 * @param int    $item_id Menu item ID.
	 * @param string $indent  Indent.
	 * @return void
	 */
	private function close_mega_panel_body( &$output, $item_id, $indent ) {
		if ( $this->in_mega_col ) {
			$output            .= "</div><!-- /.tz-mega-col -->\n";
			$this->in_mega_col = false;
		}

		if ( ! $this->tabs_enabled ) {
			$output .= $this->get_extra_columns_html( $item_id );
			if ( get_post_meta( $item_id, '_tz_mega_promo_enable', true ) ) {
				$output .= $this->get_promo_column_html( $item_id );
			}
		}

		$output .= "{$indent}</div><!-- /.tz-mega-grid -->\n";

		if ( $this->tabs_enabled ) {
			$output .= "</div><!-- /.tz-mega-tabpanel links -->\n";
			$output .= $this->get_tab_panels_html( $item_id );
			$output .= "</div><!-- /.tz-mega-tabs -->\n";
			if ( get_post_meta( $item_id, '_tz_mega_promo_enable', true ) ) {
				$output .= '<div class="tz-mega-tabs-promo">' . $this->get_promo_column_html( $item_id ) . '</div>';
			}
		}

		$bottom_text = get_post_meta( $item_id, '_tz_mega_bottom_text', true );
		$btn_label   = get_post_meta( $item_id, '_tz_mega_bottom_btn_label', true );
		$btn_url     = get_post_meta( $item_id, '_tz_mega_bottom_btn_url', true );

		if ( $bottom_text || $btn_label ) {
			$output .= "{$indent}<div class=\"tz-mega-footer\">";
			if ( $bottom_text ) {
				$output .= '<span class="tz-mega-footer__text">' . esc_html( $bottom_text ) . '</span>';
			}
			if ( $btn_label ) {
				$btn_href = $btn_url ? $btn_url : '#';
				$output  .= '<a href="' . esc_url( $btn_href ) . '" class="tz-mega-footer__btn">' . esc_html( $btn_label ) . '</a>';
			}
			$output .= "</div>\n";
		}

		$output             .= "{$indent}</div></div><!-- /.tz-mega-dropdown -->\n";
		$this->tabs_enabled = false;
	}

	/**
	 * Open markup for non-Elementor mega panels (+ cat grid + grid wrapper).
	 *
	 * @param int    $item_id Menu item ID.
	 * @param string $indent  Indent.
	 * @return string
	 */
	private function get_mega_panel_open_html( $item_id, $indent ) {
		$width      = get_post_meta( $item_id, '_tz_mega_width', true );
		$cols       = get_post_meta( $item_id, '_tz_mega_cols', true );
		$layout     = get_post_meta( $item_id, '_tz_mega_layout', true );
		$theme      = get_post_meta( $item_id, '_tz_mega_theme', true );
		$bg_color   = get_post_meta( $item_id, '_tz_mega_bg_color', true );
		$text_color = get_post_meta( $item_id, '_tz_mega_text_color', true );
		$animation  = get_post_meta( $item_id, '_tz_mega_animation', true );
		$bg_image   = get_post_meta( $item_id, '_tz_mega_bg_image', true );
		$cat_grid   = get_post_meta( $item_id, '_tz_mega_cat_grid_enable', true );

		$layout    = $layout ? $layout : 'saas';
		$width     = $width ? $width : 'compact';
		$cols      = $cols ? (int) $cols : 2;
		$theme     = $theme ? $theme : 'dark';
		$animation = $animation ? $animation : 'slide';

		$styles = array();
		if ( 'custom' === $theme ) {
			if ( $bg_color ) {
				$styles[] = '--tz-mega-custom-bg:' . esc_attr( $bg_color );
			}
			if ( $text_color ) {
				$styles[] = '--tz-mega-custom-text:' . esc_attr( $text_color );
			}
		}
		if ( $bg_image ) {
			$styles[] = '--tz-mega-bg-img:url("' . esc_url( $bg_image ) . '")';
		}
		$style_attr  = ! empty( $styles ) ? ' style="' . implode( ';', $styles ) . '"' : '';
		$panel_class = "tz-mega-dropdown tz-mega-dropdown--{$width} tz-mega-dropdown--theme-{$theme} tz-mega-anim--{$animation}";
		if ( $this->tabs_enabled ) {
			$panel_class .= ' tz-mega-dropdown--tabs';
		}

		$html  = "\n{$indent}<div class=\"{$panel_class} tz-mega-dropdown--{$layout}\"{$style_attr} role=\"region\"><div class=\"tz-mega-dropdown__inner\">";
		if ( $bg_image ) {
			$html .= '<div class="tz-mega-bg-overlay"></div>';
		}

		if ( $this->tabs_enabled ) {
			$html .= $this->get_tabs_nav_html( $item_id );
			$html .= '<div class="tz-mega-tabpanel is-active" data-tz-mega-tabpanel="links" role="tabpanel">';
		} elseif ( $cat_grid && taxonomy_exists( 'product_cat' ) ) {
			$html .= self::get_cached_cat_grid_html();
		}

		$html .= "<div class=\"tz-mega-grid tz-mega-grid--cols-{$cols}\">\n";
		return $html;
	}

	/**
	 * Extra product + brand columns (non-tabbed layout).
	 *
	 * @param int $item_id Menu item ID.
	 * @return string
	 */
	private function get_extra_columns_html( $item_id ) {
		$html = '';
		if ( get_post_meta( $item_id, '_tz_mega_products_enable', true ) ) {
			$html .= Themezur_Mega_Content::render_products_column(
				array(
					'source' => get_post_meta( $item_id, '_tz_mega_products_source', true ),
					'limit'  => (int) get_post_meta( $item_id, '_tz_mega_products_limit', true ),
					'ids'    => get_post_meta( $item_id, '_tz_mega_products_ids', true ),
					'title'  => get_post_meta( $item_id, '_tz_mega_products_title', true ),
				)
			);
		}
		if ( get_post_meta( $item_id, '_tz_mega_brands_enable', true ) ) {
			$html .= Themezur_Mega_Content::render_brands_column(
				(string) get_post_meta( $item_id, '_tz_mega_brands_taxonomy', true ),
				(int) get_post_meta( $item_id, '_tz_mega_brands_limit', true ),
				__( 'Brands', 'themezur' )
			);
		}
		if ( get_post_meta( $item_id, '_tz_mega_custom_enable', true ) ) {
			$html .= Themezur_Mega_Content::render_custom_column( $this->get_custom_column_args( $item_id ) );
		}
		return $html;
	}

	/**
	 * Collect custom-column meta into render_custom_column() args.
	 *
	 * @param int $item_id Menu item ID.
	 * @return array
	 */
	private function get_custom_column_args( $item_id ) {
		return array(
			'source'    => get_post_meta( $item_id, '_tz_mega_custom_source', true ),
			'title'     => get_post_meta( $item_id, '_tz_mega_custom_title', true ),
			'post_type' => get_post_meta( $item_id, '_tz_mega_custom_post_type', true ),
			'limit'     => (int) get_post_meta( $item_id, '_tz_mega_custom_limit', true ),
			'sidebar'   => get_post_meta( $item_id, '_tz_mega_custom_sidebar', true ),
			'html'      => get_post_meta( $item_id, '_tz_mega_custom_html', true ),
		);
	}

	/**
	 * Tab button list for enabled sections.
	 *
	 * @param int $item_id Menu item ID.
	 * @return string
	 */
	private function get_tabs_nav_html( $item_id ) {
		$tabs = $this->get_enabled_tabs( $item_id );
		if ( empty( $tabs ) ) {
			return '';
		}
		$html = '<div class="tz-mega-tabs" data-tz-mega-tabs><div class="tz-mega-tabs__nav" role="tablist">';
		$i    = 0;
		foreach ( $tabs as $key => $label ) {
			$active = ( 0 === $i ) ? ' is-active' : '';
			$sel    = ( 0 === $i ) ? 'true' : 'false';
			$html  .= '<button type="button" class="tz-mega-tabs__btn' . $active . '" role="tab" aria-selected="' . $sel . '" data-tz-mega-tab="' . esc_attr( $key ) . '">' . esc_html( $label ) . '</button>';
			++$i;
		}
		$html .= '</div>';
		return $html;
	}

	/**
	 * @param int $item_id Menu item ID.
	 * @return array<string,string>
	 */
	private function get_enabled_tabs( $item_id ) {
		$tabs = array( 'links' => __( 'Links', 'themezur' ) );
		if ( get_post_meta( $item_id, '_tz_mega_cat_grid_enable', true ) && taxonomy_exists( 'product_cat' ) ) {
			$tabs['categories'] = __( 'Categories', 'themezur' );
		}
		if ( class_exists( 'WooCommerce' ) ) {
			$tabs['new']  = __( 'New', 'themezur' );
			$tabs['sale'] = __( 'Sale', 'themezur' );
		}
		if ( get_post_meta( $item_id, '_tz_mega_brands_enable', true ) && Themezur_Mega_Content::resolve_brand_taxonomy( (string) get_post_meta( $item_id, '_tz_mega_brands_taxonomy', true ) ) ) {
			$tabs['brands'] = __( 'Brands', 'themezur' );
		}
		if ( get_post_meta( $item_id, '_tz_mega_custom_enable', true ) ) {
			$custom_title    = (string) get_post_meta( $item_id, '_tz_mega_custom_title', true );
			$tabs['custom'] = $custom_title ? $custom_title : __( 'More', 'themezur' );
		}
		return $tabs;
	}

	/**
	 * Non-links tab panels HTML.
	 *
	 * @param int $item_id Menu item ID.
	 * @return string
	 */
	private function get_tab_panels_html( $item_id ) {
		$tabs = $this->get_enabled_tabs( $item_id );
		$html = '';

		if ( isset( $tabs['categories'] ) ) {
			$html .= '<div class="tz-mega-tabpanel" data-tz-mega-tabpanel="categories" role="tabpanel" hidden>';
			$html .= self::get_cached_cat_grid_html();
			$html .= '</div>';
		}
		if ( isset( $tabs['new'] ) ) {
			$html .= '<div class="tz-mega-tabpanel" data-tz-mega-tabpanel="new" role="tabpanel" hidden>';
			$html .= '<div class="tz-mega-grid tz-mega-grid--cols-1">';
			$html .= Themezur_Mega_Content::render_products_column(
				array(
					'source' => 'latest',
					'limit'  => 6,
					'title'  => __( 'New arrivals', 'themezur' ),
				)
			);
			$html .= '</div></div>';
		}
		if ( isset( $tabs['sale'] ) ) {
			$html .= '<div class="tz-mega-tabpanel" data-tz-mega-tabpanel="sale" role="tabpanel" hidden>';
			$html .= '<div class="tz-mega-grid tz-mega-grid--cols-1">';
			$html .= Themezur_Mega_Content::render_products_column(
				array(
					'source' => 'on_sale',
					'limit'  => 6,
					'title'  => __( 'On sale', 'themezur' ),
				)
			);
			$html .= '</div></div>';
		}
		if ( isset( $tabs['brands'] ) ) {
			$html .= '<div class="tz-mega-tabpanel" data-tz-mega-tabpanel="brands" role="tabpanel" hidden>';
			$html .= '<div class="tz-mega-grid tz-mega-grid--cols-1">';
			$html .= Themezur_Mega_Content::render_brands_column(
				(string) get_post_meta( $item_id, '_tz_mega_brands_taxonomy', true ),
				(int) get_post_meta( $item_id, '_tz_mega_brands_limit', true ),
				__( 'Brands', 'themezur' )
			);
			$html .= '</div></div>';
		}
		if ( isset( $tabs['custom'] ) ) {
			$html .= '<div class="tz-mega-tabpanel" data-tz-mega-tabpanel="custom" role="tabpanel" hidden>';
			$html .= '<div class="tz-mega-grid tz-mega-grid--cols-1">';
			$html .= Themezur_Mega_Content::render_custom_column( $this->get_custom_column_args( $item_id ) );
			$html .= '</div></div>';
		}

		return $html;
	}

	/**
	 * Full Elementor mega panel HTML.
	 *
	 * @param int    $item_id Menu item ID.
	 * @param string $indent  Indent.
	 * @return string
	 */
	private function get_elementor_panel_html( $item_id, $indent ) {
		$elementor_id = (int) get_post_meta( $item_id, '_tz_mega_elementor_id', true );
		$width        = get_post_meta( $item_id, '_tz_mega_width', true );
		$theme        = get_post_meta( $item_id, '_tz_mega_theme', true );
		$animation    = get_post_meta( $item_id, '_tz_mega_animation', true );
		$bg_color     = get_post_meta( $item_id, '_tz_mega_bg_color', true );
		$text_color   = get_post_meta( $item_id, '_tz_mega_text_color', true );
		$bg_image     = get_post_meta( $item_id, '_tz_mega_bg_image', true );

		$width     = $width ? $width : 'compact';
		$theme     = $theme ? $theme : 'dark';
		$animation = $animation ? $animation : 'slide';

		$styles = array();
		if ( 'custom' === $theme ) {
			if ( $bg_color ) {
				$styles[] = '--tz-mega-custom-bg:' . esc_attr( $bg_color );
			}
			if ( $text_color ) {
				$styles[] = '--tz-mega-custom-text:' . esc_attr( $text_color );
			}
		}
		if ( $bg_image ) {
			$styles[] = '--tz-mega-bg-img:url("' . esc_url( $bg_image ) . '")';
		}
		$style_attr  = ! empty( $styles ) ? ' style="' . implode( ';', $styles ) . '"' : '';
		$panel_class = "tz-mega-dropdown tz-mega-dropdown--{$width} tz-mega-dropdown--theme-{$theme} tz-mega-anim--{$animation}";

		$html  = "\n{$indent}<div class=\"{$panel_class} tz-mega-dropdown--elementor\"{$style_attr} role=\"region\"><div class=\"tz-mega-dropdown__inner\">\n";
		if ( $elementor_id > 0 ) {
			if ( class_exists( '\Elementor\Plugin' ) ) {
				$html .= \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $elementor_id );
			} else {
				$html .= do_shortcode( '[elementor-template id="' . absint( $elementor_id ) . '"]' );
			}
		}
		$html .= "\n{$indent}</div></div>\n";
		return $html;
	}

	/**
	 * Promo card column HTML.
	 *
	 * @param int $item_id Menu item ID.
	 * @return string
	 */
	private function get_promo_column_html( $item_id ) {
		$p_img    = get_post_meta( $item_id, '_tz_mega_promo_img', true );
		$p_title  = get_post_meta( $item_id, '_tz_mega_promo_title', true );
		$p_desc   = get_post_meta( $item_id, '_tz_mega_promo_desc', true );
		$p_btn    = get_post_meta( $item_id, '_tz_mega_promo_btn_label', true );
		$p_url    = get_post_meta( $item_id, '_tz_mega_promo_btn_url', true );
		$p_badge  = get_post_meta( $item_id, '_tz_mega_promo_badge', true );
		$p_html   = get_post_meta( $item_id, '_tz_mega_promo_html', true );
		$p_layout = get_post_meta( $item_id, '_tz_mega_promo_layout', true );
		$p_layout = in_array( $p_layout, array( 'card', 'image_overlay', 'html_only' ), true ) ? $p_layout : 'card';

		$output = '<div class="tz-mega-col tz-mega-col--promo tz-mega-col--promo-' . esc_attr( $p_layout ) . '">';

		if ( 'html_only' === $p_layout ) {
			$output .= '<div class="tz-mega-promo-html">' . wp_kses_post( $p_html ) . '</div></div>' . "\n";
			return $output;
		}

		$output .= '<div class="tz-mega-promo-card tz-mega-promo-card--' . esc_attr( $p_layout ) . '">';
		if ( $p_badge ) {
			$output .= '<span class="tz-mega-promo-card__badge">' . esc_html( $p_badge ) . '</span>';
		}
		if ( $p_img ) {
			$output .= '<img src="' . esc_url( $p_img ) . '" class="tz-mega-promo-card__img" alt="' . esc_attr( $p_title ) . '" loading="lazy" />';
		}
		$output .= '<div class="tz-mega-promo-card__body">';
		if ( $p_title ) {
			$output .= '<h5 class="tz-mega-promo-card__title">' . esc_html( $p_title ) . '</h5>';
		}
		if ( $p_desc ) {
			$output .= '<p class="tz-mega-promo-card__desc">' . esc_html( $p_desc ) . '</p>';
		}
		if ( $p_html ) {
			$output .= '<div class="tz-mega-promo-card__html">' . wp_kses_post( $p_html ) . '</div>';
		}
		if ( $p_btn ) {
			$p_href  = $p_url ? $p_url : '#';
			$output .= '<a href="' . esc_url( $p_href ) . '" class="tz-btn tz-btn--sm tz-mega-promo-card__btn">' . esc_html( $p_btn ) . '</a>';
		}
		$output .= '</div></div></div>' . "\n";
		return $output;
	}

	/**
	 * Cached WooCommerce category cards for mega panels.
	 *
	 * @return string
	 */
	public static function get_cached_cat_grid_html() {
		$cache_key = 'themezur_mega_cat_grid_v1';
		$cached    = get_transient( $cache_key );
		if ( is_string( $cached ) ) {
			return $cached;
		}

		$html  = '<div class="tz-mega-cat-grid">';
		$terms = get_terms(
			array(
				'taxonomy'   => 'product_cat',
				'hide_empty' => false,
				'number'     => 6,
			)
		);
		if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				$thumb_id = get_term_meta( $term->term_id, 'thumbnail_id', true );
				$img_url  = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'thumbnail' ) : '';
				$link     = get_term_link( $term );
				if ( is_wp_error( $link ) ) {
					continue;
				}
				$html .= '<a href="' . esc_url( $link ) . '" class="tz-mega-cat-card">';
				if ( $img_url ) {
					$html .= '<img src="' . esc_url( $img_url ) . '" class="tz-mega-cat-card__img" alt="' . esc_attr( $term->name ) . '" loading="lazy" />';
				} else {
					$html .= '<div class="tz-mega-cat-card__placeholder" aria-hidden="true"></div>';
				}
				$html .= '<div class="tz-mega-cat-card__info"><strong class="tz-mega-cat-card__name">' . esc_html( $term->name ) . '</strong>';
				$html .= '<span class="tz-mega-cat-card__count">' . esc_html( (string) $term->count ) . ' ' . esc_html__( 'items', 'themezur' ) . '</span></div>';
				$html .= '</a>';
			}
		}
		$html .= '</div>';

		set_transient( $cache_key, $html, HOUR_IN_SECONDS );
		return $html;
	}

	/**
	 * Get SVG icon markup for common SaaS & E-commerce keys.
	 *
	 * @param string $icon Icon key or URL.
	 * @return string
	 */
	public static function get_icon_svg( $icon ) {
		$icon = trim( (string) $icon );
		if ( '' === $icon ) {
			return '';
		}

		if ( filter_var( $icon, FILTER_VALIDATE_URL ) ) {
			return '<img src="' . esc_url( $icon ) . '" class="tz-mega-item__img" alt="" />';
		}

		if ( 0 === strpos( $icon, 'dashicons-' ) || 0 === strpos( $icon, 'dashicon-' ) ) {
			return '<span class="dashicons ' . esc_attr( $icon ) . '" style="font-size:18px; width:18px; height:18px; line-height:1; display:flex; align-items:center; justify-content:center;" aria-hidden="true"></span>';
		}

		$svgs = array(
			'scanner'   => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M3 7V5a2 2 0 0 1 2-2h2"/><path d="M17 3h2a2 2 0 0 1 2 2v2"/><path d="M21 17v2a2 2 0 0 1-2 2h-2"/><path d="M7 21H5a2 2 0 0 1-2-2v-2"/><rect x="7" y="7" width="10" height="10" rx="1"/></svg>',
			'heatmaps'  => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 3.5z"/></svg>',
			'analytics' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M3 3v18h18"/><path d="M18 17V9"/><path d="M13 17V5"/><path d="M8 17v-3"/></svg>',
			'widget'    => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>',
			'ai'        => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/><circle cx="12" cy="12" r="4"/></svg>',
			'pdf'       => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>',
			'agency'    => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>',
			'crawler'   => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>',
			'shopping'  => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>',
			'star'      => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>',
			'zap'       => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>',
			'lock'      => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>',
			'gift'      => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><polyline points="20 12 20 22 4 22 4 12"/><rect x="2" y="7" width="20" height="5"/><line x1="12" y1="22" x2="12" y2="7"/><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"/><path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"/></svg>',
		);

		return $svgs[ $icon ] ?? '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 8v8M8 12h8"/></svg>';
	}
}
