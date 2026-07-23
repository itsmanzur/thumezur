<?php
/**
 * Unified breadcrumbs for blog, shop, and singles.
 *
 * @package Themezur
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Themezur_Breadcrumbs
 */
class Themezur_Breadcrumbs {

	/**
	 * @return void
	 */
	public static function init() {
		add_action( 'wp', array( __CLASS__, 'setup_woocommerce' ) );
	}

	/**
	 * Replace WooCommerce default breadcrumbs when Themezur crumbs are on.
	 *
	 * @return void
	 */
	public static function setup_woocommerce() {
		if ( ! self::is_enabled() || ! class_exists( 'WooCommerce' ) ) {
			return;
		}
		remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
		add_action( 'woocommerce_before_main_content', array( __CLASS__, 'render_woo_wrap' ), 15 );
	}

	/**
	 * @return bool
	 */
	public static function is_enabled() {
		return (bool) Themezur_Options::get( 'general.breadcrumbs', true );
	}

	/**
	 * WooCommerce hook wrapper.
	 *
	 * @return void
	 */
	public static function render_woo_wrap() {
		self::render();
	}

	/**
	 * Print breadcrumb nav when appropriate.
	 *
	 * @return void
	 */
	public static function render() {
		if ( ! self::is_enabled() || is_front_page() ) {
			return;
		}

		$items = self::items();
		if ( count( $items ) < 2 ) {
			return;
		}

		echo '<nav class="tz-breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumb', 'themezur' ) . '">';
		echo '<ol class="tz-breadcrumbs__list">';
		$last = count( $items ) - 1;
		foreach ( $items as $i => $item ) {
			$is_last = ( $i === $last );
			echo '<li class="tz-breadcrumbs__item' . ( $is_last ? ' is-current' : '' ) . '">';
			if ( ! $is_last && ! empty( $item['url'] ) ) {
				echo '<a href="' . esc_url( $item['url'] ) . '">' . esc_html( $item['label'] ) . '</a>';
			} else {
				echo '<span>' . esc_html( $item['label'] ) . '</span>';
			}
			echo '</li>';
		}
		echo '</ol></nav>';
	}

	/**
	 * @return array[]
	 */
	public static function items() {
		$home = array(
			'label' => __( 'Home', 'themezur' ),
			'url'   => home_url( '/' ),
		);
		$items = array( $home );

		if ( class_exists( 'WooCommerce' ) && ( is_shop() || is_product_taxonomy() || is_product() || is_cart() || is_checkout() || is_account_page() ) ) {
			$shop_id = function_exists( 'wc_get_page_id' ) ? wc_get_page_id( 'shop' ) : 0;
			$shop_url = $shop_id > 0 ? get_permalink( $shop_id ) : '';
			$shop_label = $shop_id > 0 ? get_the_title( $shop_id ) : __( 'Shop', 'themezur' );

			if ( is_shop() ) {
				$items[] = array(
					'label' => $shop_label,
					'url'   => '',
				);
			} elseif ( is_product_taxonomy() ) {
				if ( $shop_url ) {
					$items[] = array(
						'label' => $shop_label,
						'url'   => $shop_url,
					);
				}
				$term = get_queried_object();
				if ( $term && ! is_wp_error( $term ) ) {
					$items[] = array(
						'label' => $term->name,
						'url'   => '',
					);
				}
			} elseif ( is_product() ) {
				if ( $shop_url ) {
					$items[] = array(
						'label' => $shop_label,
						'url'   => $shop_url,
					);
				}
				$terms = get_the_terms( get_the_ID(), 'product_cat' );
				if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
					$term = array_shift( $terms );
					$items[] = array(
						'label' => $term->name,
						'url'   => get_term_link( $term ),
					);
				}
				$items[] = array(
					'label' => get_the_title(),
					'url'   => '',
				);
			} elseif ( is_cart() ) {
				$items[] = array(
					'label' => __( 'Cart', 'themezur' ),
					'url'   => '',
				);
			} elseif ( is_checkout() ) {
				$items[] = array(
					'label' => __( 'Checkout', 'themezur' ),
					'url'   => '',
				);
			} elseif ( is_account_page() ) {
				$items[] = array(
					'label' => __( 'My account', 'themezur' ),
					'url'   => '',
				);
			}
			return $items;
		}

		if ( is_home() && ! is_front_page() ) {
			$items[] = array(
				'label' => single_post_title( '', false ),
				'url'   => '',
			);
			return $items;
		}

		if ( is_singular( 'post' ) ) {
			$blog_id = (int) get_option( 'page_for_posts' );
			if ( $blog_id > 0 ) {
				$items[] = array(
					'label' => get_the_title( $blog_id ),
					'url'   => get_permalink( $blog_id ),
				);
			}
			$cats = get_the_category();
			if ( ! empty( $cats[0] ) ) {
				$items[] = array(
					'label' => $cats[0]->name,
					'url'   => get_category_link( $cats[0]->term_id ),
				);
			}
			$items[] = array(
				'label' => get_the_title(),
				'url'   => '',
			);
			return $items;
		}

		if ( is_category() || is_tag() || is_tax() ) {
			$term = get_queried_object();
			if ( $term && ! is_wp_error( $term ) ) {
				$items[] = array(
					'label' => $term->name,
					'url'   => '',
				);
			}
			return $items;
		}

		if ( is_search() ) {
			$items[] = array(
				'label' => sprintf(
					/* translators: %s: search query */
					__( 'Search: %s', 'themezur' ),
					get_search_query()
				),
				'url'   => '',
			);
			return $items;
		}

		if ( is_404() ) {
			$items[] = array(
				'label' => __( 'Not found', 'themezur' ),
				'url'   => '',
			);
			return $items;
		}

		if ( is_page() ) {
			$ancestors = array_reverse( get_post_ancestors( get_the_ID() ) );
			foreach ( $ancestors as $aid ) {
				$items[] = array(
					'label' => get_the_title( $aid ),
					'url'   => get_permalink( $aid ),
				);
			}
			$items[] = array(
				'label' => get_the_title(),
				'url'   => '',
			);
		}

		return $items;
	}
}
