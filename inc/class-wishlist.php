<?php
/**
 * Wishlist (localStorage-based) for Themezur.
 *
 * Provides the [themezur_wishlist] shortcode and enqueues
 * the wishlist JS when the feature is enabled.
 *
 * @package Themezur
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Themezur_Wishlist
 */
class Themezur_Wishlist {

	/**
	 * @return void
	 */
	public static function init() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}

		add_shortcode( 'themezur_wishlist', array( __CLASS__, 'render_page' ) );
		add_action( 'wp_footer', array( __CLASS__, 'wishlist_page_shell' ) );

		// AJAX: return product cards for IDs stored in localStorage.
		add_action( 'wp_ajax_themezur_wishlist_products', array( __CLASS__, 'ajax_products' ) );
		add_action( 'wp_ajax_nopriv_themezur_wishlist_products', array( __CLASS__, 'ajax_products' ) );
	}

	/**
	 * Render the wishlist page via [themezur_wishlist] shortcode.
	 *
	 * @return string
	 */
	public static function render_page() {
		if ( ! Themezur_WooCommerce::wishlist_enabled() ) {
			return '<p>' . esc_html__( 'Wishlist is disabled.', 'themezur' ) . '</p>';
		}

		ob_start();
		?>
		<div class="tz-wishlist-page" id="tz-wishlist-page">
			<p class="tz-wishlist-empty" id="tz-wishlist-empty" hidden>
				<?php esc_html_e( 'Your wishlist is empty.', 'themezur' ); ?>
				<a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>">
					<?php esc_html_e( 'Browse products', 'themezur' ); ?>
				</a>
			</p>
			<div class="tz-wishlist-grid" id="tz-wishlist-grid"></div>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Render the wishlist floating icon shell in footer (for header count badge).
	 *
	 * @return void
	 */
	public static function wishlist_page_shell() {
		if ( ! Themezur_WooCommerce::wishlist_enabled() ) {
			return;
		}
		// Page URL data attribute for JS to know where to link.
		$page_id  = get_option( 'themezur_wishlist_page_id', 0 );
		$page_url = $page_id ? get_permalink( $page_id ) : '';
		echo '<span id="tz-wishlist-page-url" data-url="' . esc_url( $page_url ) . '" hidden></span>';
	}

	/**
	 * AJAX: return rendered product cards for given IDs.
	 *
	 * @return void
	 */
	public static function ajax_products() {
		check_ajax_referer( 'themezur_front', 'nonce' );

		$raw_ids = isset( $_POST['ids'] ) ? wp_unslash( $_POST['ids'] ) : '';
		if ( is_string( $raw_ids ) ) {
			$ids = array_filter( array_map( 'absint', explode( ',', $raw_ids ) ) );
		} elseif ( is_array( $raw_ids ) ) {
			$ids = array_filter( array_map( 'absint', $raw_ids ) );
		} else {
			$ids = array();
		}

		if ( empty( $ids ) ) {
			wp_send_json_success( array( 'html' => '' ) );
		}

		$ids = array_slice( $ids, 0, 50 ); // safety cap

		$query = new WP_Query(
			array(
				'post_type'      => 'product',
				'post_status'    => 'publish',
				'post__in'       => $ids,
				'orderby'        => 'post__in',
				'posts_per_page' => 50,
				'no_found_rows'  => true,
			)
		);

		ob_start();
		if ( $query->have_posts() ) {
			woocommerce_product_loop_start();
			while ( $query->have_posts() ) {
				$query->the_post();
				wc_get_template_part( 'content', 'product' );
			}
			woocommerce_product_loop_end();
			wp_reset_postdata();
		}
		$html = ob_get_clean();

		wp_send_json_success( array( 'html' => $html ) );
	}
}
