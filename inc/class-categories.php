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

	const TRANSIENT_PREFIX = 'themezur_cat_tree_';

	/**
	 * Register cache bust hooks.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'created_product_cat', array( __CLASS__, 'bust_cache' ) );
		add_action( 'edited_product_cat', array( __CLASS__, 'bust_cache' ) );
		add_action( 'delete_product_cat', array( __CLASS__, 'bust_cache' ) );
	}

	/**
	 * Clear category tree transients.
	 *
	 * @return void
	 */
	public static function bust_cache() {
		global $wpdb;
		$like = $wpdb->esc_like( '_transient_' . self::TRANSIENT_PREFIX ) . '%';
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$keys = $wpdb->get_col( $wpdb->prepare( "SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s", $like ) );
		if ( empty( $keys ) ) {
			return;
		}
		foreach ( $keys as $option_name ) {
			$key = str_replace( '_transient_', '', $option_name );
			delete_transient( $key );
		}
		delete_transient( 'themezur_mega_cat_grid_v1' );
	}

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

		$limit     = max( 1, absint( $limit ) );
		$cache_key = self::TRANSIENT_PREFIX . $limit;
		$cached    = get_transient( $cache_key );
		if ( is_array( $cached ) ) {
			return $cached;
		}

		$parents = get_terms(
			array(
				'taxonomy'   => 'product_cat',
				'hide_empty' => true,
				'parent'     => 0,
				'number'     => $limit,
				'orderby'    => 'name',
				'order'      => 'ASC',
			)
		);

		if ( is_wp_error( $parents ) || empty( $parents ) ) {
			set_transient( $cache_key, array(), HOUR_IN_SECONDS );
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
					$child_link = get_term_link( $child );
					$children[] = array(
						'id'   => (int) $child->term_id,
						'name' => $child->name,
						'url'  => is_wp_error( $child_link ) ? '#' : $child_link,
					);
				}
			}

			$link   = get_term_link( $term );
			$tree[] = array(
				'id'       => (int) $term->term_id,
				'name'     => $term->name,
				'url'      => is_wp_error( $link ) ? '#' : $link,
				'children' => $children,
			);
		}

		set_transient( $cache_key, $tree, HOUR_IN_SECONDS );
		return $tree;
	}
}
