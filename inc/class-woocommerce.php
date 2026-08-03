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

		// Quick View hooks.
		add_action( 'woocommerce_after_shop_loop_item_title', array( __CLASS__, 'quick_view_button' ), 5 );
		add_action( 'wp_footer', array( __CLASS__, 'quick_view_modal' ) );
		add_action( 'wp_ajax_themezur_quick_view', array( __CLASS__, 'ajax_quick_view' ) );
		add_action( 'wp_ajax_nopriv_themezur_quick_view', array( __CLASS__, 'ajax_quick_view' ) );

		// Wishlist hooks.
		add_action( 'woocommerce_before_shop_loop_item_title', array( __CLASS__, 'wishlist_button' ), 15 );

		// Single product hooks.
		add_action( 'woocommerce_after_add_to_cart_button', array( __CLASS__, 'sticky_cart_data' ) );
		add_action( 'wp_footer', array( __CLASS__, 'sticky_cart_bar' ) );
		add_filter( 'woocommerce_quantity_input_args', array( __CLASS__, 'quantity_stepper_args' ) );

		// My Account — welcome header.
		add_action( 'woocommerce_account_content', array( __CLASS__, 'account_welcome' ), 1 );

		// AJAX — add to cart (for Quick View AJAX flow).
		add_action( 'wp_ajax_themezur_add_to_cart', array( __CLASS__, 'ajax_add_to_cart' ) );
		add_action( 'wp_ajax_nopriv_themezur_add_to_cart', array( __CLASS__, 'ajax_add_to_cart' ) );
	}

	/* ----------------------------------------------------------------
	 * Feature flags
	 * -------------------------------------------------------------- */

	/**
	 * @return bool
	 */
	public static function is_enabled() {
		return 'theme' === Themezur_Options::get( 'woocommerce.mode', 'theme' );
	}

	/**
	 * @return bool
	 */
	public static function quick_view_enabled() {
		return self::is_enabled() && (bool) Themezur_Options::get( 'woocommerce.shop.quick_view', true );
	}

	/**
	 * @return bool
	 */
	public static function wishlist_enabled() {
		return self::is_enabled() && (bool) Themezur_Options::get( 'woocommerce.shop.wishlist', true );
	}

	/**
	 * @return bool
	 */
	public static function sticky_cart_enabled() {
		return self::is_enabled() && (bool) Themezur_Options::get( 'woocommerce.single.sticky_cart', true );
	}

	/**
	 * @return bool
	 */
	public static function quantity_stepper_enabled() {
		return self::is_enabled() && (bool) Themezur_Options::get( 'woocommerce.single.quantity_stepper', true );
	}

	/* ----------------------------------------------------------------
	 * Context detection
	 * -------------------------------------------------------------- */

	/**
	 * Whether the current request contains a WooCommerce storefront experience.
	 *
	 * @return bool
	 */
	public static function is_context() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return false;
		}

		if ( is_woocommerce() || is_cart() || is_checkout() || is_account_page() ) {
			return true;
		}

		if ( is_search() ) {
			$post_type  = get_query_var( 'post_type' );
			$post_types = is_array( $post_type ) ? $post_type : array( $post_type );
			if ( in_array( 'product', $post_types, true ) ) {
				return true;
			}
		}

		$post = get_queried_object();
		if ( ! $post instanceof WP_Post ) {
			return false;
		}

		if ( self::has_woocommerce_shortcode( $post->post_content ) ) {
			return true;
		}

		if ( ! has_blocks( $post->post_content ) ) {
			return false;
		}

		return self::contains_woocommerce_block( parse_blocks( $post->post_content ) );
	}

	/**
	 * Whether Themezur's WooCommerce assets should load on this request.
	 *
	 * @return bool
	 */
	public static function should_enqueue_assets() {
		return self::is_enabled() && self::is_context();
	}

	/* ----------------------------------------------------------------
	 * Shop grid helpers
	 * -------------------------------------------------------------- */

	/**
	 * @param int $columns Columns.
	 * @return int
	 */
	public static function shop_columns( $columns ) {
		if ( ! self::is_enabled() ) {
			return $columns;
		}
		$cols = (int) Themezur_Options::get( 'woocommerce.shop.columns', 3 );
		return max( 2, min( 5, $cols ) );
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
		$count               = (int) Themezur_Options::get( 'woocommerce.single.related_count', 4 );
		$count               = max( 0, min( 8, $count ) );
		$args['posts_per_page'] = $count;
		$args['columns']     = min( 4, max( 2, $count ) );
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
		$count                  = (int) Themezur_Options::get( 'woocommerce.single.upsells_count', 4 );
		$count                  = max( 0, min( 8, $count ) );
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
		if ( ! self::should_enqueue_assets() ) {
			return $classes;
		}

		$classes[] = 'tz-woo';
		$style      = Themezur_Options::get( 'woocommerce.shop.card_style', 'soft' );
		$classes[]  = 'tz-woo-cards--' . sanitize_html_class( $style );
		$classes[]  = 'tz-woo-cols-' . (int) self::shop_columns( 3 );

		if ( self::quick_view_enabled() ) {
			$classes[] = 'tz-woo-qv';
		}
		if ( self::wishlist_enabled() ) {
			$classes[] = 'tz-woo-wl';
		}
		if ( self::sticky_cart_enabled() ) {
			$classes[] = 'tz-woo-sticky';
		}
		if ( self::quantity_stepper_enabled() ) {
			$classes[] = 'tz-woo-stepper';
		}

		return $classes;
	}

	/* ----------------------------------------------------------------
	 * Quick View
	 * -------------------------------------------------------------- */

	/**
	 * Render the Quick View button inside each product card.
	 *
	 * @return void
	 */
	public static function quick_view_button() {
		if ( ! self::quick_view_enabled() ) {
			return;
		}
		global $product;
		if ( ! $product ) {
			return;
		}
		printf(
			'<button class="tz-qv-btn" data-product-id="%d" aria-label="%s" type="button">%s</button>',
			(int) $product->get_id(),
			esc_attr__( 'Quick View', 'themezur' ),
			/* translators: Quick view button label */
			'<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg> '
			. esc_html__( 'Quick View', 'themezur' )
		);
	}

	/**
	 * Render the Quick View modal shell in the footer (one per page).
	 *
	 * @return void
	 */
	public static function quick_view_modal() {
		if ( ! self::quick_view_enabled() || ! self::is_context() ) {
			return;
		}
		?>
		<div id="tz-qv-modal" class="tz-qv-modal" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Quick View', 'themezur' ); ?>" hidden>
			<div class="tz-qv-backdrop" id="tz-qv-backdrop"></div>
			<div class="tz-qv-panel">
				<button class="tz-qv-close" id="tz-qv-close" aria-label="<?php esc_attr_e( 'Close', 'themezur' ); ?>" type="button">
					<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
				</button>
				<div class="tz-qv-body" id="tz-qv-body">
					<div class="tz-qv-spinner" aria-hidden="true"></div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * AJAX: return Quick View product HTML.
	 *
	 * @return void
	 */
	public static function ajax_quick_view() {
		check_ajax_referer( 'themezur_front', 'nonce' );

		$product_id = isset( $_GET['product_id'] ) ? absint( $_GET['product_id'] ) : 0;
		if ( ! $product_id ) {
			wp_send_json_error( array( 'message' => __( 'Invalid product.', 'themezur' ) ), 400 );
		}

		$product = wc_get_product( $product_id );
		if ( ! $product || ! $product->is_visible() ) {
			wp_send_json_error( array( 'message' => __( 'Product not found.', 'themezur' ) ), 404 );
		}

		$image_url = wp_get_attachment_image_url( $product->get_image_id(), 'woocommerce_single' );
		if ( ! $image_url ) {
			$image_url = wc_placeholder_img_src( 'woocommerce_single' );
		}

		$gallery_ids  = $product->get_gallery_image_ids();
		$gallery_html = '';
		if ( ! empty( $gallery_ids ) ) {
			$gallery_html .= '<div class="tz-qv-thumbs">';
			$gallery_html .= '<button class="tz-qv-thumb tz-qv-thumb--active" data-img="' . esc_url( $image_url ) . '" type="button"><img src="' . esc_url( $image_url ) . '" alt="" loading="lazy"></button>';
			foreach ( array_slice( $gallery_ids, 0, 4 ) as $gid ) {
				$g_url = wp_get_attachment_image_url( $gid, 'woocommerce_gallery_thumbnail' );
				$g_full = wp_get_attachment_image_url( $gid, 'woocommerce_single' );
				if ( $g_url ) {
					$gallery_html .= '<button class="tz-qv-thumb" data-img="' . esc_url( $g_full ) . '" type="button"><img src="' . esc_url( $g_url ) . '" alt="" loading="lazy"></button>';
				}
			}
			$gallery_html .= '</div>';
		}

		$rating_html = '';
		if ( $product->get_review_count() > 0 ) {
			$rating_html = '<div class="tz-qv-rating">' . wc_get_rating_html( $product->get_average_rating() ) . '<span class="tz-qv-rating-count">(' . $product->get_review_count() . ')</span></div>';
		}

		$stock_html = '';
		if ( $product->is_in_stock() ) {
			$stock_html = '<span class="tz-qv-stock tz-qv-stock--in">' . esc_html__( 'In Stock', 'themezur' ) . '</span>';
		} else {
			$stock_html = '<span class="tz-qv-stock tz-qv-stock--out">' . esc_html__( 'Out of Stock', 'themezur' ) . '</span>';
		}

		$desc = $product->get_short_description();
		if ( empty( $desc ) ) {
			$desc = wp_trim_words( wp_strip_all_tags( $product->get_description() ), 30, '...' );
		}

		$add_to_cart_url = '';
		$add_to_cart_text = $product->add_to_cart_text();
		if ( $product->is_purchasable() && $product->is_in_stock() && $product->is_type( 'simple' ) ) {
			$add_to_cart_url = add_query_arg(
				array(
					'add-to-cart' => $product->get_id(),
					'quantity'    => 1,
				),
				wc_get_cart_url()
			);
		} else {
			$add_to_cart_url = $product->get_permalink();
		}

		$html  = '<div class="tz-qv-image-col">';
		$html .= '<img class="tz-qv-main-img" id="tz-qv-main-img" src="' . esc_url( $image_url ) . '" alt="' . esc_attr( $product->get_name() ) . '">';
		$html .= $gallery_html;
		$html .= '</div>';
		$html .= '<div class="tz-qv-info-col">';
		$html .= '<p class="tz-qv-cats">' . wc_get_product_category_list( $product->get_id(), ', ' ) . '</p>';
		$html .= '<h2 class="tz-qv-title">' . esc_html( $product->get_name() ) . '</h2>';
		$html .= $rating_html;
		$html .= '<div class="tz-qv-price">' . $product->get_price_html() . '</div>';
		$html .= $stock_html;
		if ( $desc ) {
			$html .= '<div class="tz-qv-desc">' . wp_kses_post( $desc ) . '</div>';
		}
		// Simple product: AJAX add to cart; others: go to product page.
		if ( $product->is_purchasable() && $product->is_in_stock() && $product->is_type( 'simple' ) ) {
			$html .= '<div class="tz-qv-actions">';
			$html .= '<button class="button tz-qv-atc" data-product-id="' . (int) $product->get_id() . '" type="button">' . esc_html( $add_to_cart_text ) . '</button>';
			$html .= '<a class="tz-qv-view" href="' . esc_url( $product->get_permalink() ) . '">' . esc_html__( 'View details', 'themezur' ) . '</a>';
			$html .= '</div>';
		} else {
			$html .= '<div class="tz-qv-actions">';
			$html .= '<a class="button tz-qv-atc" href="' . esc_url( $product->get_permalink() ) . '">' . esc_html( $add_to_cart_text ) . '</a>';
			$html .= '</div>';
		}
		$html .= '</div>';

		wp_send_json_success( array( 'html' => $html ) );
	}

	/* ----------------------------------------------------------------
	 * Wishlist
	 * -------------------------------------------------------------- */

	/**
	 * Render the Wishlist heart button inside each product card.
	 *
	 * @return void
	 */
	public static function wishlist_button() {
		if ( ! self::wishlist_enabled() ) {
			return;
		}
		global $product;
		if ( ! $product ) {
			return;
		}
		printf(
			'<button class="tz-wl-btn" data-product-id="%d" aria-label="%s" aria-pressed="false" type="button"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg></button>',
			(int) $product->get_id(),
			esc_attr__( 'Add to Wishlist', 'themezur' )
		);
	}

	/* ----------------------------------------------------------------
	 * Single Product — Sticky Add-to-Cart bar
	 * -------------------------------------------------------------- */

	/**
	 * Inject hidden data attributes for the sticky bar.
	 *
	 * @return void
	 */
	public static function sticky_cart_data() {
		if ( ! self::sticky_cart_enabled() ) {
			return;
		}
		global $product;
		if ( ! $product ) {
			return;
		}
		echo '<span class="tz-sticky-data" data-title="' . esc_attr( $product->get_name() ) . '" data-price="' . esc_attr( wp_strip_all_tags( $product->get_price_html() ) ) . '" hidden></span>';
	}

	/**
	 * Render the sticky bar shell in the footer (single product only).
	 *
	 * @return void
	 */
	public static function sticky_cart_bar() {
		if ( ! self::sticky_cart_enabled() || ! is_singular( 'product' ) ) {
			return;
		}
		global $product;
		if ( ! $product ) {
			return;
		}
		?>
		<div class="tz-sticky-bar" id="tz-sticky-bar" aria-hidden="true" hidden>
			<div class="tz-sticky-bar__inner">
				<div class="tz-sticky-bar__product">
					<?php
					$thumb = get_the_post_thumbnail_url( $product->get_id(), 'thumbnail' );
					if ( $thumb ) :
						?>
						<img src="<?php echo esc_url( $thumb ); ?>" alt="" class="tz-sticky-bar__thumb" loading="lazy">
					<?php endif; ?>
					<div>
						<span class="tz-sticky-bar__title"><?php echo esc_html( $product->get_name() ); ?></span>
						<span class="tz-sticky-bar__price"><?php echo wp_kses_post( $product->get_price_html() ); ?></span>
					</div>
				</div>
				<button class="button single_add_to_cart_button tz-sticky-bar__btn" data-product-id="<?php echo (int) $product->get_id(); ?>" type="button">
					<?php echo esc_html( $product->single_add_to_cart_text() ); ?>
				</button>
			</div>
		</div>
		<?php
	}

	/* ----------------------------------------------------------------
	 * Single Product — Quantity Stepper
	 * -------------------------------------------------------------- */

	/**
	 * Expose a data attribute for JS to attach the stepper.
	 *
	 * @param array $args Quantity input args.
	 * @return array
	 */
	public static function quantity_stepper_args( $args ) {
		if ( self::quantity_stepper_enabled() ) {
			$args['classes'][] = 'tz-qty-input';
		}
		return $args;
	}

	/* ----------------------------------------------------------------
	 * My Account — welcome header
	 * -------------------------------------------------------------- */

	/**
	 * Output a welcome banner on the My Account page.
	 *
	 * @return void
	 */
	public static function account_welcome() {
		if ( ! is_account_page() || ! self::is_enabled() ) {
			return;
		}
		$user = wp_get_current_user();
		if ( ! $user->ID ) {
			return;
		}
		$name    = $user->display_name ?: $user->user_login;
		$initial = mb_strtoupper( mb_substr( $name, 0, 1 ) );
		$orders  = wc_get_orders( array( 'customer' => $user->ID, 'limit' => -1, 'return' => 'ids' ) );
		$count   = count( $orders );
		?>
		<div class="tz-account-welcome">
			<div class="tz-account-avatar" aria-hidden="true"><?php echo esc_html( $initial ); ?></div>
			<div class="tz-account-info">
				<p class="tz-account-greeting">
					<?php
					printf(
						/* translators: %s: user display name */
						esc_html__( 'Welcome back, %s!', 'themezur' ),
						'<strong>' . esc_html( $name ) . '</strong>'
					);
					?>
				</p>
				<p class="tz-account-meta">
					<?php
					printf(
						/* translators: %d: order count */
						esc_html( _n( '%d order placed', '%d orders placed', $count, 'themezur' ) ),
						(int) $count
					);
					?>
				</p>
			</div>
		</div>
		<?php
	}

	/* ----------------------------------------------------------------
	 * AJAX — Add to Cart (for Quick View)
	 * -------------------------------------------------------------- */

	/**
	 * AJAX add-to-cart for Quick View.
	 *
	 * @return void
	 */
	public static function ajax_add_to_cart() {
		check_ajax_referer( 'themezur_front', 'nonce' );

		$product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
		$quantity   = isset( $_POST['quantity'] ) ? absint( $_POST['quantity'] ) : 1;
		$quantity   = max( 1, $quantity );

		if ( ! $product_id ) {
			wp_send_json_error( array( 'message' => __( 'Invalid product.', 'themezur' ) ), 400 );
		}

		$added = WC()->cart->add_to_cart( $product_id, $quantity );

		if ( false === $added ) {
			$notices = wc_get_notices( 'error' );
			$msg     = ! empty( $notices ) ? wp_strip_all_tags( $notices[0]['notice'] ) : __( 'Could not add product to cart.', 'themezur' );
			wc_clear_notices();
			wp_send_json_error( array( 'message' => $msg ), 400 );
		}

		wc_clear_notices();

		$count      = (int) WC()->cart->get_cart_contents_count();
		$mini_cart  = Themezur_Options::get( 'header.middle.mini_cart', true )
			? Themezur_Frontend::mini_cart_markup()
			: '';

		wp_send_json_success(
			array(
				'count'     => $count,
				'label'     => Themezur_Frontend::cart_count_label( $count ),
				'mini_cart' => $mini_cart,
				'message'   => __( 'Product added to cart!', 'themezur' ),
			)
		);
	}

	/* ----------------------------------------------------------------
	 * Private helpers
	 * -------------------------------------------------------------- */

	/**
	 * Recursively detect WooCommerce blocks in parsed post content.
	 *
	 * @param array[] $blocks Parsed blocks.
	 * @return bool
	 */
	private static function contains_woocommerce_block( array $blocks ) {
		foreach ( $blocks as $block ) {
			$name = isset( $block['blockName'] ) ? (string) $block['blockName'] : '';
			if ( 0 === strpos( $name, 'woocommerce/' ) ) {
				return true;
			}

			if ( ! empty( $block['innerBlocks'] ) && self::contains_woocommerce_block( $block['innerBlocks'] ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Detect commonly used WooCommerce shortcodes.
	 *
	 * @param string $content Post content.
	 * @return bool
	 */
	private static function has_woocommerce_shortcode( $content ) {
		$shortcodes = array(
			'woocommerce_cart',
			'woocommerce_checkout',
			'woocommerce_my_account',
			'woocommerce_order_tracking',
			'product_page',
			'products',
			'product',
			'product_category',
			'product_categories',
			'add_to_cart',
			'add_to_cart_url',
			'shop_messages',
			'recent_products',
			'featured_products',
			'sale_products',
			'best_selling_products',
			'top_rated_products',
			'product_attribute',
		);

		foreach ( $shortcodes as $shortcode ) {
			if ( has_shortcode( $content, $shortcode ) ) {
				return true;
			}
		}

		return false;
	}
}
