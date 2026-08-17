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
		add_action( 'wp_ajax_themezur_cart_count', array( __CLASS__, 'cart_count' ) );
		add_action( 'wp_ajax_nopriv_themezur_cart_count', array( __CLASS__, 'cart_count' ) );
		add_action( 'wp_ajax_themezur_newsletter_subscribe', array( __CLASS__, 'newsletter_subscribe' ) );
		add_action( 'wp_ajax_nopriv_themezur_newsletter_subscribe', array( __CLASS__, 'newsletter_subscribe' ) );
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
				'title' => wp_strip_all_tags( get_the_title( $post ) ),
				'url'   => esc_url_raw( get_permalink( $post ) ),
				'image' => $thumb ? esc_url_raw( $thumb ) : '',
				'price' => $price,
			);
		}

		wp_send_json_success( array( 'items' => $items ) );
	}

	/**
	 * Newsletter endpoints an administrator has actually configured.
	 *
	 * The browser sends back the form action, but it must never be trusted as the
	 * request target — otherwise any visitor could point the server at an internal
	 * host. Only URLs saved in the Themezur panel are accepted.
	 *
	 * @return string[]
	 */
	private static function allowed_newsletter_endpoints() {
		$allowed = array();

		$row = Themezur_Options::get( 'footer.newsletter_row.action', '' );
		if ( is_string( $row ) && '' !== $row ) {
			$allowed[] = esc_url_raw( $row );
		}

		$columns = Themezur_Options::get( 'footer.columns', array() );
		if ( is_array( $columns ) ) {
			foreach ( $columns as $col ) {
				if ( ! is_array( $col ) || empty( $col['newsletter_action'] ) ) {
					continue;
				}
				$allowed[] = esc_url_raw( (string) $col['newsletter_action'] );
			}
		}

		return array_values( array_unique( array_filter( $allowed ) ) );
	}

	/**
	 * Forward newsletter signup to an external form action URL (Mailchimp / FluentCRM / etc).
	 *
	 * @return void
	 */
	public static function newsletter_subscribe() {
		check_ajax_referer( 'themezur_front', 'nonce' );

		$email      = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
		$action_url = isset( $_POST['action_url'] ) ? esc_url_raw( wp_unslash( $_POST['action_url'] ) ) : '';
		$email_name = isset( $_POST['email_name'] ) ? preg_replace( '/[^A-Za-z0-9_\-\[\]]/', '', wp_unslash( $_POST['email_name'] ) ) : 'EMAIL';
		if ( ! $email_name ) {
			$email_name = 'EMAIL';
		}

		if ( ! is_email( $email ) ) {
			wp_send_json_error( array( 'message' => __( 'Please enter a valid email address.', 'themezur' ) ), 400 );
		}

		$allowed = self::allowed_newsletter_endpoints();
		if ( empty( $allowed ) ) {
			wp_send_json_error( array( 'message' => __( 'Newsletter form action URL is not configured.', 'themezur' ) ), 400 );
		}
		if ( ! $action_url || ! in_array( $action_url, $allowed, true ) ) {
			wp_send_json_error( array( 'message' => __( 'Newsletter form action URL is not configured.', 'themezur' ) ), 400 );
		}

		$response = wp_safe_remote_post(
			$action_url,
			array(
				'timeout'     => 15,
				'redirection' => 0,
				'blocking'    => true,
				'headers'     => array(
					'Content-Type' => 'application/x-www-form-urlencoded',
				),
				'body'        => array(
					$email_name => $email,
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			wp_send_json_error( array( 'message' => __( 'Could not reach the newsletter service. Try again later.', 'themezur' ) ), 502 );
		}

		$code = (int) wp_remote_retrieve_response_code( $response );
		if ( $code >= 400 && 0 !== $code ) {
			wp_send_json_error( array( 'message' => __( 'Newsletter service rejected the request.', 'themezur' ) ), 502 );
		}

		wp_send_json_success( array( 'message' => __( 'Thanks for subscribing!', 'themezur' ) ) );
	}

	/**
	 * Return the current WooCommerce cart count for classic and block carts.
	 *
	 * @return void
	 */
	public static function cart_count() {
		check_ajax_referer( 'themezur_front', 'nonce' );

		$count = 0;
		if ( function_exists( 'WC' ) && WC()->cart ) {
			$count = (int) WC()->cart->get_cart_contents_count();
		}

		wp_send_json_success(
			array(
				'count'     => $count,
				'label'     => Themezur_Frontend::cart_count_label( $count ),
				'mini_cart' => Themezur_Options::get( 'header.middle.mini_cart', true ) ? Themezur_Frontend::mini_cart_markup() : '',
			)
		);
	}
}
