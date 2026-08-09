<?php
/**
 * Mega menu product / brand content helpers.
 *
 * @package Themezur
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Themezur_Mega_Content
 */
class Themezur_Mega_Content {

	const PRODUCTS_TRANSIENT = 'themezur_mega_products_';
	const BRANDS_TRANSIENT   = 'themezur_mega_brands_';

	/**
	 * Register cache bust hooks.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'save_post_product', array( __CLASS__, 'bust_products_cache' ) );
		add_action( 'woocommerce_update_product', array( __CLASS__, 'bust_products_cache' ) );
		add_action( 'deleted_post', array( __CLASS__, 'maybe_bust_product_cache' ) );

		foreach ( self::detect_brand_taxonomies() as $tax ) {
			add_action( "created_{$tax}", array( __CLASS__, 'bust_brands_cache' ) );
			add_action( "edited_{$tax}", array( __CLASS__, 'bust_brands_cache' ) );
			add_action( "delete_{$tax}", array( __CLASS__, 'bust_brands_cache' ) );
		}
	}

	/**
	 * Known brand taxonomy candidates.
	 *
	 * @return string[]
	 */
	public static function brand_taxonomy_candidates() {
		return array( 'product_brand', 'pwb-brand', 'yith_product_brand' );
	}

	/**
	 * Existing brand taxonomies on this site.
	 *
	 * @return string[]
	 */
	public static function detect_brand_taxonomies() {
		$found = array();
		foreach ( self::brand_taxonomy_candidates() as $tax ) {
			if ( taxonomy_exists( $tax ) ) {
				$found[] = $tax;
			}
		}
		return $found;
	}

	/**
	 * Resolve brand taxonomy slug for a menu item.
	 *
	 * @param string $requested Requested slug from meta.
	 * @return string Empty if none available.
	 */
	public static function resolve_brand_taxonomy( $requested = '' ) {
		$requested = sanitize_key( (string) $requested );
		if ( $requested && taxonomy_exists( $requested ) ) {
			return $requested;
		}
		$detected = self::detect_brand_taxonomies();
		return ! empty( $detected ) ? $detected[0] : '';
	}

	/**
	 * Bust product block transients.
	 *
	 * @return void
	 */
	public static function bust_products_cache() {
		self::delete_transients_by_prefix( self::PRODUCTS_TRANSIENT );
	}

	/**
	 * Bust brand grid transients.
	 *
	 * @return void
	 */
	public static function bust_brands_cache() {
		self::delete_transients_by_prefix( self::BRANDS_TRANSIENT );
	}

	/**
	 * @param int $post_id Post ID.
	 * @return void
	 */
	public static function maybe_bust_product_cache( $post_id ) {
		if ( 'product' === get_post_type( $post_id ) ) {
			self::bust_products_cache();
		}
	}

	/**
	 * @param string $prefix Transient prefix.
	 * @return void
	 */
	private static function delete_transients_by_prefix( $prefix ) {
		global $wpdb;
		$like = $wpdb->esc_like( '_transient_' . $prefix ) . '%';
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$keys = $wpdb->get_col( $wpdb->prepare( "SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s", $like ) );
		if ( empty( $keys ) ) {
			return;
		}
		foreach ( $keys as $option_name ) {
			delete_transient( str_replace( '_transient_', '', $option_name ) );
		}
	}

	/**
	 * Query product IDs for mega blocks.
	 *
	 * @param string $source latest|on_sale|best_sellers|manual.
	 * @param int    $limit  Max products.
	 * @param string $ids    Comma-separated IDs for manual.
	 * @return int[]
	 */
	public static function get_product_ids( $source, $limit = 4, $ids = '' ) {
		if ( ! class_exists( 'WooCommerce' ) || ! function_exists( 'wc_get_products' ) ) {
			return array();
		}

		$source = sanitize_key( $source );
		$limit  = max( 2, min( 8, absint( $limit ) ) );
		$ids    = preg_replace( '/[^0-9,]/', '', (string) $ids );
		$key    = self::PRODUCTS_TRANSIENT . $source . '_' . $limit . '_' . md5( $ids );

		$cached = get_transient( $key );
		if ( is_array( $cached ) ) {
			return array_map( 'absint', $cached );
		}

		$args = array(
			'status'  => 'publish',
			'limit'   => $limit,
			'orderby' => 'date',
			'order'   => 'DESC',
			'return'  => 'ids',
		);

		switch ( $source ) {
			case 'on_sale':
				$sale_ids = array_map( 'absint', wc_get_product_ids_on_sale() );
				if ( empty( $sale_ids ) ) {
					set_transient( $key, array(), HOUR_IN_SECONDS );
					return array();
				}
				$args['include'] = $sale_ids;
				break;
			case 'best_sellers':
				$args['orderby']  = 'meta_value_num';
				$args['meta_key'] = 'total_sales'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				$args['order']    = 'DESC';
				break;
			case 'manual':
				$manual = array_filter( array_map( 'absint', explode( ',', $ids ) ) );
				if ( empty( $manual ) ) {
					set_transient( $key, array(), HOUR_IN_SECONDS );
					return array();
				}
				$args['include'] = $manual;
				$args['orderby'] = 'include';
				$args['limit']   = count( $manual );
				break;
			default:
				$source = 'latest';
				break;
		}

		$result = wc_get_products( $args );
		$result = is_array( $result ) ? array_map( 'absint', $result ) : array();
		set_transient( $key, $result, HOUR_IN_SECONDS );
		return $result;
	}

	/**
	 * Render product column HTML.
	 *
	 * @param array $args source, limit, ids, title.
	 * @return string
	 */
	public static function render_products_column( array $args ) {
		$source = isset( $args['source'] ) ? $args['source'] : 'latest';
		$limit  = isset( $args['limit'] ) ? (int) $args['limit'] : 4;
		$ids    = isset( $args['ids'] ) ? $args['ids'] : '';
		$title  = isset( $args['title'] ) ? $args['title'] : '';

		$product_ids = self::get_product_ids( $source, $limit, $ids );
		if ( empty( $product_ids ) ) {
			return '';
		}

		$html = '<div class="tz-mega-col tz-mega-col--products">';
		if ( $title ) {
			$html .= '<div class="tz-mega-section__label"><span>' . esc_html( $title ) . '</span></div>';
		}
		$html .= '<div class="tz-mega-products">';

		foreach ( $product_ids as $product_id ) {
			$product = wc_get_product( $product_id );
			if ( ! $product ) {
				continue;
			}
			$permalink = $product->get_permalink();
			$name      = $product->get_name();
			$price     = $product->get_price_html();
			$img       = $product->get_image( 'woocommerce_thumbnail', array( 'class' => 'tz-mega-product__img', 'loading' => 'lazy' ) );

			$html .= '<a class="tz-mega-product" href="' . esc_url( $permalink ) . '">';
			$html .= '<span class="tz-mega-product__thumb">' . $img . '</span>';
			$html .= '<span class="tz-mega-product__meta">';
			$html .= '<span class="tz-mega-product__title">' . esc_html( $name ) . '</span>';
			if ( $price ) {
				$html .= '<span class="tz-mega-product__price">' . wp_kses_post( $price ) . '</span>';
			}
			$html .= '</span></a>';
		}

		$html .= '</div></div>';
		return $html;
	}

	/**
	 * Brand terms for grid.
	 *
	 * @param string $taxonomy Taxonomy slug.
	 * @param int    $limit    Max terms.
	 * @return array<int,array{id:int,name:string,url:string,image:string}>
	 */
	public static function get_brands( $taxonomy, $limit = 8 ) {
		$taxonomy = self::resolve_brand_taxonomy( $taxonomy );
		if ( ! $taxonomy ) {
			return array();
		}

		$limit = max( 2, min( 16, absint( $limit ) ) );
		$key   = self::BRANDS_TRANSIENT . $taxonomy . '_' . $limit;
		$cached = get_transient( $key );
		if ( is_array( $cached ) ) {
			return $cached;
		}

		$terms = get_terms(
			array(
				'taxonomy'   => $taxonomy,
				'hide_empty' => true,
				'number'     => $limit,
				'orderby'    => 'name',
				'order'      => 'ASC',
			)
		);

		$brands = array();
		if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
			foreach ( $terms as $term ) {
				$link = get_term_link( $term );
				if ( is_wp_error( $link ) ) {
					continue;
				}
				$thumb_id = (int) get_term_meta( $term->term_id, 'thumbnail_id', true );
				if ( ! $thumb_id ) {
					$thumb_id = (int) get_term_meta( $term->term_id, 'pwb_brand_image', true );
				}
				$img = $thumb_id ? (string) wp_get_attachment_image_url( $thumb_id, 'medium' ) : '';
				$brands[] = array(
					'id'    => (int) $term->term_id,
					'name'  => $term->name,
					'url'   => $link,
					'image' => $img,
				);
			}
		}

		set_transient( $key, $brands, HOUR_IN_SECONDS );
		return $brands;
	}

	/**
	 * Render brand logo grid column.
	 *
	 * @param string $taxonomy Taxonomy.
	 * @param int    $limit    Limit.
	 * @param string $title    Optional label.
	 * @return string
	 */
	public static function render_brands_column( $taxonomy, $limit = 8, $title = '' ) {
		$brands = self::get_brands( $taxonomy, $limit );
		if ( empty( $brands ) ) {
			return '';
		}

		$html = '<div class="tz-mega-col tz-mega-col--brands">';
		if ( $title ) {
			$html .= '<div class="tz-mega-section__label"><span>' . esc_html( $title ) . '</span></div>';
		}
		$html .= '<div class="tz-mega-brands">';
		foreach ( $brands as $brand ) {
			$html .= '<a class="tz-mega-brand" href="' . esc_url( $brand['url'] ) . '">';
			if ( ! empty( $brand['image'] ) ) {
				$html .= '<img src="' . esc_url( $brand['image'] ) . '" alt="' . esc_attr( $brand['name'] ) . '" class="tz-mega-brand__img" loading="lazy" />';
			} else {
				$html .= '<span class="tz-mega-brand__name">' . esc_html( $brand['name'] ) . '</span>';
			}
			$html .= '</a>';
		}
		$html .= '</div></div>';
		return $html;
	}

	/**
	 * Whether mega scheduling allows the panel right now.
	 *
	 * @param int $item_id Menu item ID.
	 * @return bool
	 */
	public static function is_schedule_active( $item_id ) {
		if ( ! get_post_meta( $item_id, '_tz_mega_schedule_enable', true ) ) {
			return true;
		}

		$start = (string) get_post_meta( $item_id, '_tz_mega_schedule_start', true );
		$end   = (string) get_post_meta( $item_id, '_tz_mega_schedule_end', true );
		$now   = current_time( 'timestamp' );

		if ( $start ) {
			$start_ts = self::parse_site_datetime( $start );
			if ( $start_ts && $now < $start_ts ) {
				return false;
			}
		}

		if ( $end ) {
			$end_ts = self::parse_site_datetime( $end );
			if ( $end_ts && $now > $end_ts ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Parse datetime-local string in site timezone.
	 *
	 * @param string $value Datetime string.
	 * @return int|false
	 */
	private static function parse_site_datetime( $value ) {
		$value = trim( (string) $value );
		if ( '' === $value ) {
			return false;
		}
		try {
			$tz  = wp_timezone();
			$dt  = date_create( $value, $tz );
			return $dt ? $dt->getTimestamp() : false;
		} catch ( Exception $e ) {
			return false;
		}
	}
}
