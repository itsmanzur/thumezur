<?php
/**
 * AJAX handlers for Themezur admin + frontend.
 *
 * @package Themezur
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Themezur_Ajax
 */
class Themezur_Ajax {

	/**
	 * Register AJAX actions.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'wp_ajax_themezur_save', array( __CLASS__, 'save' ) );
		add_action( 'wp_ajax_themezur_get_templates', array( __CLASS__, 'get_templates' ) );
		add_action( 'wp_ajax_themezur_product_search', array( __CLASS__, 'product_search' ) );
		add_action( 'wp_ajax_nopriv_themezur_product_search', array( __CLASS__, 'product_search' ) );
	}

	/**
	 * Capability + nonce guard (admin).
	 *
	 * @return void
	 */
	private static function authorize() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Forbidden.', 'themezur' ) ), 403 );
		}
		check_ajax_referer( 'themezur_admin', 'nonce' );
	}

	/**
	 * Save options payload.
	 *
	 * @return void
	 */
	public static function save() {
		self::authorize();

		$raw = array();
		if ( isset( $_POST['options'] ) ) {
			if ( is_string( $_POST['options'] ) ) {
				$decoded = json_decode( wp_unslash( $_POST['options'] ), true );
				$raw     = is_array( $decoded ) ? $decoded : array();
			} elseif ( is_array( $_POST['options'] ) ) {
				// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized in Themezur_Options::sanitize.
				$raw = wp_unslash( $_POST['options'] );
			}
		}

		Themezur_Options::update( $raw );
		Themezur_Elementor::bust_cache();

		wp_send_json_success(
			array(
				'message' => __( 'Settings saved.', 'themezur' ),
				'options' => Themezur_Options::get_all_for_admin(),
			)
		);
	}

	/**
	 * Return cached Elementor templates for a context.
	 *
	 * @return void
	 */
	public static function get_templates() {
		self::authorize();

		$context = isset( $_GET['context'] ) ? sanitize_key( wp_unslash( $_GET['context'] ) ) : 'header';
		if ( ! in_array( $context, array( 'header', 'footer' ), true ) ) {
			$context = 'header';
		}

		wp_send_json_success(
			array(
				'templates' => Themezur_Elementor::get_templates_for( $context ),
				'elementor' => Themezur_Elementor::is_active(),
			)
		);
	}

	/**
	 * Live product search suggestions (public).
	 *
	 * @return void
	 */
	public static function product_search() {
		check_ajax_referer( 'themezur_front', 'nonce' );

		$q = isset( $_GET['q'] ) ? sanitize_text_field( wp_unslash( $_GET['q'] ) ) : '';
		$min = (int) Themezur_Options::get( 'header.middle.search_min_chars', 2 );
		$limit = (int) Themezur_Options::get( 'header.middle.search_limit', 8 );

		if ( strlen( $q ) < $min ) {
			wp_send_json_success( array( 'items' => array() ) );
		}

		$post_type = post_type_exists( 'product' ) ? 'product' : 'post';

		$query = new WP_Query(
			array(
				'post_type'              => $post_type,
				'post_status'            => 'publish',
				's'                      => $q,
				'posts_per_page'         => max( 3, min( 20, $limit ) ),
				'no_found_rows'          => true,
				'ignore_sticky_posts'    => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
			)
		);

		$items = array();
		foreach ( $query->posts as $post ) {
			$thumb = get_the_post_thumbnail_url( $post, 'thumbnail' );
			$price = '';
			if ( 'product' === $post_type && function_exists( 'wc_get_product' ) ) {
				$product = wc_get_product( $post->ID );
				if ( $product ) {
					$price = wp_strip_all_tags( $product->get_price_html() );
				}
			}
			$items[] = array(
				'id'    => (int) $post->ID,
				'title' => get_the_title( $post ),
				'url'   => get_permalink( $post ),
				'image' => $thumb ? $thumb : '',
				'price' => $price,
			);
		}

		wp_send_json_success( array( 'items' => $items ) );
	}
}
