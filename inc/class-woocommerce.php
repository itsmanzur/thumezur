<?php
/**
 * WooCommerce shop polish for Themezur.
 *
 * @package Themezur
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Themezur_WooCommerce
 */
class Themezur_WooCommerce {

	/**
	 * @return void
	 */
	public static function init() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}

		add_filter( 'loop_shop_columns', array( __CLASS__, 'shop_columns' ) );
		add_filter( 'loop_shop_per_page', array( __CLASS__, 'products_per_page' ), 20 );
		add_filter( 'woocommerce_output_related_products_args', array( __CLASS__, 'related_args' ) );
		add_filter( 'woocommerce_upsell_display_args', array( __CLASS__, 'upsell_args' ) );
		add_action( 'wp', array( __CLASS__, 'maybe_remove_catalog_tools' ) );
		add_filter( 'body_class', array( __CLASS__, 'body_class' ) );
	}

	/**
	 * @return bool
	 */
	public static function is_enabled() {
		return 'theme' === Themezur_Options::get( 'woocommerce.mode', 'theme' );
	}

	/**
	 * @param int $columns Columns.
	 * @return int
	 */
	public static function shop_columns( $columns ) {
		if ( ! self::is_enabled() ) {
			return $columns;
		}
		$cols = (int) Themezur_Options::get( 'woocommerce.shop.columns', 3 );
		return max( 2, min( 4, $cols ) );
	}

	/**
	 * @param int $per_page Per page.
	 * @return int
	 */
	public static function products_per_page( $per_page ) {
		if ( ! self::is_enabled() ) {
			return $per_page;
		}
		$n = (int) Themezur_Options::get( 'woocommerce.shop.products_per_page', 12 );
		return max( 4, min( 48, $n ) );
	}

	/**
	 * @param array $args Args.
	 * @return array
	 */
	public static function related_args( $args ) {
		if ( ! self::is_enabled() ) {
			return $args;
		}
		$count = (int) Themezur_Options::get( 'woocommerce.single.related_count', 4 );
		$count = max( 0, min( 8, $count ) );
		$args['posts_per_page'] = $count;
		$args['columns']        = min( 4, max( 2, $count ) );
		return $args;
	}

	/**
	 * @param array $args Args.
	 * @return array
	 */
	public static function upsell_args( $args ) {
		if ( ! self::is_enabled() ) {
			return $args;
		}
		$count = (int) Themezur_Options::get( 'woocommerce.single.upsells_count', 4 );
		$count = max( 0, min( 8, $count ) );
		$args['posts_per_page'] = $count;
		$args['columns']        = min( 4, max( 2, $count ? $count : 2 ) );
		return $args;
	}

	/**
	 * Optionally hide result count / ordering.
	 *
	 * @return void
	 */
	public static function maybe_remove_catalog_tools() {
		if ( ! self::is_enabled() ) {
			return;
		}
		if ( ! Themezur_Options::get( 'woocommerce.shop.show_result_count', true ) ) {
			remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
		}
		if ( ! Themezur_Options::get( 'woocommerce.shop.show_ordering', true ) ) {
			remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
		}
	}

	/**
	 * @param string[] $classes Body classes.
	 * @return string[]
	 */
	public static function body_class( $classes ) {
		if ( ! self::is_enabled() ) {
			return $classes;
		}
		if ( is_shop() || is_product_taxonomy() || is_product() || is_cart() || is_checkout() || is_account_page() ) {
			$classes[] = 'tz-woo';
			$style     = Themezur_Options::get( 'woocommerce.shop.card_style', 'soft' );
			$classes[] = 'tz-woo-cards--' . sanitize_html_class( $style );
			$classes[] = 'tz-woo-cols-' . (int) self::shop_columns( 3 );
		}
		return $classes;
	}
}
