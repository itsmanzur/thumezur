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
	 * Starts the list before the elements are added.
	 *
	 * @param string   $output Used to append additional content.
	 * @param int      $depth  Depth of menu item.
	 * @param stdClass $args   An object of wp_nav_menu() arguments.
	 * @return void
	 */
	public function start_lvl( &$output, $depth = 0, $args = null ) {
		$indent = str_repeat( "\t", $depth );

		if ( 0 === $depth && $this->current_top_item ) {
			$item_id      = $this->current_top_item->ID;
			$is_mega      = get_post_meta( $item_id, '_tz_mega_enable', true );
			$layout       = get_post_meta( $item_id, '_tz_mega_layout', true );
			$width        = get_post_meta( $item_id, '_tz_mega_width', true );
			$cols         = get_post_meta( $item_id, '_tz_mega_cols', true );
			$elementor_id = get_post_meta( $item_id, '_tz_mega_elementor_id', true );

			$layout = $layout ? $layout : 'saas';
			$width  = $width ? $width : 'compact';
			$cols   = $cols ? (int) $cols : 2;

			if ( $is_mega ) {
				if ( 'elementor' === $layout && $elementor_id > 0 ) {
					$output .= "\n{$indent}<div class=\"tz-mega-dropdown tz-mega-dropdown--{$width} tz-mega-dropdown--elementor\"><div class=\"tz-mega-dropdown__inner\">\n";
					if ( class_exists( '\Elementor\Plugin' ) ) {
						$output .= \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $elementor_id );
					} else {
						$output .= do_shortcode( "[elementor-template id=\"{$elementor_id}\"]" );
					}
					$output .= "\n{$indent}</div></div>\n";
					return;
				}

				$output .= "\n{$indent}<div class=\"tz-mega-dropdown tz-mega-dropdown--{$width} tz-mega-dropdown--{$layout}\"><div class=\"tz-mega-dropdown__inner\"><div class=\"tz-mega-grid tz-mega-grid--cols-{$cols}\">\n";
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
	 * Ends the list of after the elements are added.
	 *
	 * @param string   $output Used to append additional content.
	 * @param int      $depth  Depth of menu item.
	 * @param stdClass $args   An object of wp_nav_menu() arguments.
	 * @return void
	 */
	public function end_lvl( &$output, $depth = 0, $args = null ) {
		$indent = str_repeat( "\t", $depth );

		if ( 0 === $depth && $this->current_top_item ) {
			$item_id     = $this->current_top_item->ID;
			$is_mega     = get_post_meta( $item_id, '_tz_mega_enable', true );
			$layout      = get_post_meta( $item_id, '_tz_mega_layout', true );
			$bottom_text = get_post_meta( $item_id, '_tz_mega_bottom_text', true );
			$btn_label   = get_post_meta( $item_id, '_tz_mega_bottom_btn_label', true );
			$btn_url     = get_post_meta( $item_id, '_tz_mega_bottom_btn_url', true );

			if ( $is_mega && 'elementor' !== $layout ) {
				$output .= "\n{$indent}</div><!-- /.tz-mega-grid -->\n";

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

				$output .= "{$indent}</div></div><!-- /.tz-mega-dropdown -->\n";
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
			$this->current_top_item = $item;
		}

		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'menu-item-' . $item->ID;

		$is_mega_top = ( 0 === $depth && get_post_meta( $item->ID, '_tz_mega_enable', true ) );
		if ( $is_mega_top ) {
			$classes[] = 'menu-item-has-children';
			$classes[] = 'tz-menu-item--mega';
		}

		$class_names = implode( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

		$id_attr = apply_filters( 'nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args, $depth );
		$id_attr = $id_attr ? ' id="' . esc_attr( $id_attr ) . '"' : '';

		$output .= $indent . '<li' . $id_attr . $class_names . '>';

		// Section header label for sub-items
		$section_label = get_post_meta( $item->ID, '_tz_mega_section_label', true );
		if ( $depth > 0 && $section_label ) {
			$output .= '<div class="tz-mega-section__label">' . esc_html( $section_label ) . '</div>';
		}

		$atts           = array();
		$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
		$atts['target'] = ! empty( $item->target ) ? $item->target : '';
		$atts['rel']    = ! empty( $item->xfn ) ? $item->xfn : '';
		$atts['href']   = ! empty( $item->url ) ? $item->url : '';

		$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );
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

		$item_output = $args->before ?? '';

		if ( $depth > 0 && ( $icon || $desc || $badge ) ) {
			// Rich SaaS Item Format
			$item_output .= '<a class="tz-mega-item"' . $attributes . '>';
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
			$item_output .= '</a>';
		} else {
			// Standard Item Format
			$item_output .= '<a' . $attributes . '>';
			$item_output .= ( $args->link_before ?? '' ) . $title . ( $args->link_after ?? '' );
			$item_output .= '</a>';
		}

		$item_output .= $args->after ?? '';

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
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
			return '<img src="' . esc_url( $icon ) . '" class="tz-mega-item__img" alt="Icon" />';
		}

		$svgs = array(
			'scanner'   => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 7V5a2 2 0 0 1 2-2h2"/><path d="M17 3h2a2 2 0 0 1 2 2v2"/><path d="M21 17v2a2 2 0 0 1-2 2h-2"/><path d="M7 21H5a2 2 0 0 1-2-2v-2"/><rect x="7" y="7" width="10" height="10" rx="1"/></svg>',
			'heatmaps'  => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 3.5z"/></svg>',
			'analytics' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3v18h18"/><path d="M18 17V9"/><path d="M13 17V5"/><path d="M8 17v-3"/></svg>',
			'widget'    => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>',
			'ai'        => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/><circle cx="12" cy="12" r="4"/></svg>',
			'pdf'       => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>',
			'agency'    => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>',
			'crawler'   => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>',
			'shopping'  => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>',
			'star'      => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>',
			'zap'       => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>',
			'lock'      => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>',
			'gift'      => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 12 20 22 4 22 4 12"/><rect x="2" y="7" width="20" height="5"/><line x1="12" y1="22" x2="12" y2="7"/><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"/><path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"/></svg>',
		);

		return $svgs[ $icon ] ?? '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v8M8 12h8"/></svg>';
	}
}
