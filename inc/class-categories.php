<?php
/**
 * Themezur product category helpers for mega menu.
 *
 * @package Themezur
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Themezur_Categories
 */
class Themezur_Categories {

	/**
	 * Whether product categories are available.
	 *
	 * @return bool
	 */
	public static function has_product_cats() {
		return taxonomy_exists( 'product_cat' );
	}

	/**
	 * Get hierarchical product categories for mega menu.
	 *
	 * @param int $limit Max parent terms.
	 * @return array<int,array{id:int,name:string,url:string,children:array}>
	 */
	public static function get_tree( $limit = 18 ) {
		if ( ! self::has_product_cats() ) {
			return array();
		}

		$parents = get_terms(
			array(
				'taxonomy'   => 'product_cat',
				'hide_empty' => true,
				'parent'     => 0,
				'number'     => max( 1, absint( $limit ) ),
				'orderby'    => 'name',
				'order'      => 'ASC',
			)
		);

		if ( is_wp_error( $parents ) || empty( $parents ) ) {
			return array();
		}

		$tree = array();
		foreach ( $parents as $term ) {
			$children_raw = get_terms(
				array(
					'taxonomy'   => 'product_cat',
					'hide_empty' => true,
					'parent'     => (int) $term->term_id,
					'number'     => 12,
					'orderby'    => 'name',
					'order'      => 'ASC',
				)
			);

			$children = array();
			if ( ! is_wp_error( $children_raw ) ) {
				foreach ( $children_raw as $child ) {
					$children[] = array(
						'id'   => (int) $child->term_id,
						'name' => $child->name,
						'url'  => get_term_link( $child ),
					);
				}
			}

			$link = get_term_link( $term );
			$tree[] = array(
				'id'       => (int) $term->term_id,
				'name'     => $term->name,
				'url'      => is_wp_error( $link ) ? '#' : $link,
				'children' => $children,
			);
		}

		return $tree;
	}
}
