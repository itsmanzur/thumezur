<?php
/**
 * Mobile drawer nav walker with mega-lite deep links.
 *
 * @package Themezur
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Themezur_Mobile_Nav_Walker
 */
class Themezur_Mobile_Nav_Walker extends Walker_Nav_Menu {

	/**
	 * @var WP_Post|null
	 */
	private $current_top = null;

	/**
	 * @param object $element           Element.
	 * @param array  $children_elements Children.
	 * @param int    $max_depth         Max depth.
	 * @param int    $depth             Depth.
	 * @param array  $args              Args.
	 * @param string $output            Output.
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

		$force_lite_lvl = ( 0 === $depth && $this->is_mega_lite( $element ) && empty( $children_elements[ $id ] ) );

		if ( ( ( 0 === (int) $max_depth || $max_depth > $depth + 1 ) && ! empty( $children_elements[ $id ] ) ) || $force_lite_lvl ) {
			$cb_args = array_merge( array( &$output, $depth ), $args );
			call_user_func_array( array( $this, 'start_lvl' ), $cb_args );

			if ( ! empty( $children_elements[ $id ] ) ) {
				foreach ( $children_elements[ $id ] as $child ) {
					$this->display_element( $child, $children_elements, $max_depth, $depth + 1, $args, $output );
				}
				unset( $children_elements[ $id ] );
			}

			call_user_func_array( array( $this, 'end_lvl' ), $cb_args );
		}

		$cb_args = array_merge( array( &$output, $element, $depth ), $args );
		call_user_func_array( array( $this, 'end_el' ), $cb_args );
	}

	/**
	 * @param string   $output Output.
	 * @param WP_Post  $item   Item.
	 * @param int      $depth  Depth.
	 * @param stdClass $args   Args.
	 * @param int      $id     ID.
	 * @return void
	 */
	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		if ( 0 === $depth ) {
			$this->current_top = $item;
		}

		$indent    = ( $depth ) ? str_repeat( "\t", $depth ) : '';
		$classes   = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'menu-item-' . $item->ID;

		if ( 0 === $depth && $this->is_mega_lite( $item ) ) {
			$classes[] = 'tz-mobile-mega';
			if ( ! in_array( 'menu-item-has-children', $classes, true ) ) {
				$classes[] = 'menu-item-has-children';
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

		$item_output  = $args->before ?? '';
		$item_output .= '<a' . $attributes . '>';
		$item_output .= ( $args->link_before ?? '' ) . $title . ( $args->link_after ?? '' );
		$item_output .= '</a>';
		$item_output .= $args->after ?? '';

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}

	/**
	 * @param string   $output Output.
	 * @param int      $depth  Depth.
	 * @param stdClass $args   Args.
	 * @return void
	 */
	public function start_lvl( &$output, $depth = 0, $args = null ) {
		$indent  = str_repeat( "\t", $depth );
		$output .= "\n{$indent}<ul class=\"sub-menu\">\n";
	}

	/**
	 * @param string   $output Output.
	 * @param int      $depth  Depth.
	 * @param stdClass $args   Args.
	 * @return void
	 */
	public function end_lvl( &$output, $depth = 0, $args = null ) {
		if ( 0 === $depth && $this->current_top && $this->is_mega_lite( $this->current_top ) ) {
			$output .= $this->get_lite_items_html( $this->current_top );
		}
		$indent  = str_repeat( "\t", $depth );
		$output .= "{$indent}</ul>\n";
	}

	/**
	 * @param string   $output Output.
	 * @param WP_Post  $item   Item.
	 * @param int      $depth  Depth.
	 * @param stdClass $args   Args.
	 * @return void
	 */
	public function end_el( &$output, $item, $depth = 0, $args = null ) {
		$output .= "</li>\n";
	}

	/**
	 * @param WP_Post $item Item.
	 * @return bool
	 */
	private function is_mega_lite( $item ) {
		return (bool) get_post_meta( $item->ID, '_tz_mega_enable', true ) && Themezur_Mega_Content::is_schedule_active( (int) $item->ID );
	}

	/**
	 * @param WP_Post $item Item.
	 * @return string
	 */
	private function get_lite_items_html( $item ) {
		$sections = array();

		if ( class_exists( 'WooCommerce' ) && function_exists( 'wc_get_page_permalink' ) ) {
			$shop = wc_get_page_permalink( 'shop' );
			if ( $shop && get_post_meta( $item->ID, '_tz_mega_cat_grid_enable', true ) && taxonomy_exists( 'product_cat' ) ) {
				$sections[] = array(
					'label' => __( 'Shop categories', 'themezur' ),
					'url'   => $shop,
				);
			}
			if ( $shop && ( get_post_meta( $item->ID, '_tz_mega_products_enable', true ) || get_post_meta( $item->ID, '_tz_mega_tabs_enable', true ) ) ) {
				$sections[] = array(
					'label' => __( 'New arrivals', 'themezur' ),
					'url'   => add_query_arg( 'orderby', 'date', $shop ),
				);
				$sections[] = array(
					'label' => __( 'On sale', 'themezur' ),
					'url'   => add_query_arg( 'on_sale', '1', $shop ),
				);
			}
		}

		if ( get_post_meta( $item->ID, '_tz_mega_brands_enable', true ) ) {
			$tax = Themezur_Mega_Content::resolve_brand_taxonomy( (string) get_post_meta( $item->ID, '_tz_mega_brands_taxonomy', true ) );
			if ( $tax ) {
				$terms = get_terms(
					array(
						'taxonomy'   => $tax,
						'hide_empty' => true,
						'number'     => 1,
					)
				);
				if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
					$link = get_term_link( $terms[0] );
					if ( ! is_wp_error( $link ) ) {
						$sections[] = array(
							'label' => __( 'Browse brands', 'themezur' ),
							'url'   => $link,
						);
					}
				}
			}
		}

		if ( empty( $sections ) ) {
			return '';
		}

		$html = '';
		foreach ( $sections as $section ) {
			$html .= '<li class="menu-item tz-mobile-mega__item"><a href="' . esc_url( $section['url'] ) . '">' . esc_html( $section['label'] ) . '</a></li>';
		}
		return $html;
	}
}
