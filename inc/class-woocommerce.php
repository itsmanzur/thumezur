<?php
/**
 * WooCommerce shop + single-product polish for Themezur.
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
	 * Whether the shop layout wrapper is open.
	 *
	 * @var bool
	 */
	private static $shop_wrap_open = false;

	/**
	 * @return void
	 */
	public static function init() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}

		add_action( 'widgets_init', array( __CLASS__, 'register_sidebar' ) );
		add_filter( 'loop_shop_columns', array( __CLASS__, 'shop_columns' ) );
		add_filter( 'loop_shop_per_page', array( __CLASS__, 'products_per_page' ), 20 );
		add_filter( 'woocommerce_output_related_products_args', array( __CLASS__, 'related_args' ) );
		add_filter( 'woocommerce_upsell_display_args', array( __CLASS__, 'upsell_args' ) );
		add_action( 'wp', array( __CLASS__, 'maybe_remove_catalog_tools' ) );
		add_action( 'wp', array( __CLASS__, 'setup_single' ) );
		add_action( 'wp', array( __CLASS__, 'setup_shop_archive' ) );
		add_filter( 'body_class', array( __CLASS__, 'body_class' ) );
		add_filter( 'woocommerce_sale_flash', array( __CLASS__, 'sale_flash' ), 10, 3 );
		add_action( 'wp_footer', array( __CLASS__, 'render_sticky_atc' ), 20 );
		add_action( 'wp_footer', array( __CLASS__, 'render_mini_cart_drawer' ), 25 );
		add_filter( 'woocommerce_add_to_cart_fragments', array( __CLASS__, 'mini_cart_fragment' ) );
		add_action( 'wp', array( __CLASS__, 'setup_checkout' ) );
		add_action( 'wp_ajax_themezur_mini_cart', array( __CLASS__, 'ajax_mini_cart' ) );
		add_action( 'wp_ajax_nopriv_themezur_mini_cart', array( __CLASS__, 'ajax_mini_cart' ) );
	}

	/**
	 * Whether mini-cart drawer is active.
	 *
	 * @return bool
	 */
	public static function mini_cart_enabled() {
		return self::is_enabled() && Themezur_Options::get( 'woocommerce.cart.mini_cart', true );
	}

	/**
	 * @return bool
	 */
	public static function is_enabled() {
		return 'theme' === Themezur_Options::get( 'woocommerce.mode', 'theme' );
	}

	/**
	 * Shop / category / tag archives (not single product).
	 *
	 * @return bool
	 */
	public static function is_catalog() {
		return function_exists( 'is_shop' ) && ( is_shop() || is_product_taxonomy() );
	}

	/**
	 * @return void
	 */
	public static function register_sidebar() {
		register_sidebar(
			array(
				'name'          => __( 'Themezur Shop Sidebar', 'themezur' ),
				'id'            => 'themezur-shop',
				'description'   => __( 'Filters and widgets for the shop and product category archives. Use WooCommerce Layered Nav, Price Filter, etc.', 'themezur' ),
				'before_widget' => '<div id="%1$s" class="tz-woo-widget widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h3 class="tz-woo-widget__title">',
				'after_title'   => '</h3>',
			)
		);
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
		if ( ! Themezur_Options::get( 'woocommerce.single.show_related', true ) ) {
			$args['posts_per_page'] = 0;
			return $args;
		}
		$count = (int) Themezur_Options::get( 'woocommerce.single.related_count', 4 );
		$count = max( 0, min( 8, $count ) );
		$args['posts_per_page'] = $count;
		$args['columns']        = min( 4, max( 2, $count ? $count : 2 ) );
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
		if ( ! Themezur_Options::get( 'woocommerce.single.show_upsells', true ) ) {
			$args['posts_per_page'] = 0;
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
	 * Catalog card hooks + sidebar layout.
	 *
	 * @return void
	 */
	public static function setup_shop_archive() {
		if ( ! self::is_enabled() ) {
			return;
		}

		// Card polish applies in any product loop (shop, related, upsells).
		remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
		add_action( 'woocommerce_before_shop_loop_item_title', array( __CLASS__, 'loop_thumbnail' ), 10 );
		add_action( 'woocommerce_before_shop_loop_item_title', array( __CLASS__, 'render_new_badge' ), 9 );
		add_action( 'woocommerce_after_shop_loop_item', array( __CLASS__, 'render_loop_wishlist' ), 15 );

		if ( ! self::is_catalog() ) {
			return;
		}

		$sidebar = Themezur_Options::get( 'woocommerce.shop.sidebar', 'none' );
		if ( in_array( $sidebar, array( 'left', 'right' ), true ) ) {
			add_action( 'woocommerce_before_main_content', array( __CLASS__, 'open_shop_layout' ), 15 );
			add_action( 'woocommerce_after_main_content', array( __CLASS__, 'close_shop_layout' ), 5 );
			remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );
			add_action( 'woocommerce_before_shop_loop', array( __CLASS__, 'render_filters_toggle' ), 5 );
			add_action( 'woocommerce_no_products_found', array( __CLASS__, 'render_filters_toggle' ), 5 );
		}
	}

	/**
	 * Single product toggles (hooks only — no full template overrides).
	 *
	 * @return void
	 */
	public static function setup_single() {
		if ( ! self::is_enabled() || ! is_product() ) {
			return;
		}

		if ( ! Themezur_Options::get( 'woocommerce.single.show_rating', true ) ) {
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
		}

		if ( ! Themezur_Options::get( 'woocommerce.single.show_sku', true ) ) {
			add_filter( 'wc_product_sku_enabled', '__return_false' );
		}

		if ( ! Themezur_Options::get( 'woocommerce.single.show_stock', true ) ) {
			add_filter( 'woocommerce_get_stock_html', '__return_empty_string', 10, 2 );
		}

		if ( ! Themezur_Options::get( 'woocommerce.single.show_related', true ) ) {
			remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
		}

		if ( ! Themezur_Options::get( 'woocommerce.single.show_upsells', true ) ) {
			remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
		}

		$trust = trim( (string) Themezur_Options::get( 'woocommerce.single.trust_note', '' ) );
		if ( '' !== $trust ) {
			add_action( 'woocommerce_single_product_summary', array( __CLASS__, 'render_trust_note' ), 35 );
		}
	}

	/**
	 * @return string
	 */
	private static function sidebar_position() {
		$sidebar = Themezur_Options::get( 'woocommerce.shop.sidebar', 'none' );
		return in_array( $sidebar, array( 'left', 'right' ), true ) ? $sidebar : 'none';
	}

	/**
	 * @return bool
	 */
	private static function has_shop_sidebar() {
		return is_active_sidebar( 'themezur-shop' );
	}

	/**
	 * Open shop + sidebar wrapper.
	 *
	 * @return void
	 */
	public static function open_shop_layout() {
		if ( self::$shop_wrap_open || ! self::is_catalog() ) {
			return;
		}
		$side = self::sidebar_position();
		if ( 'none' === $side ) {
			return;
		}

		self::$shop_wrap_open = true;
		$has = self::has_shop_sidebar();

		echo '<div class="tz-woo-shop tz-woo-shop--' . esc_attr( $side ) . ( $has ? ' has-sidebar' : ' no-sidebar' ) . '">';

		if ( 'left' === $side && $has ) {
			self::render_shop_sidebar();
		}

		echo '<div class="tz-woo-shop__main">';
	}

	/**
	 * Close shop + sidebar wrapper.
	 *
	 * @return void
	 */
	public static function close_shop_layout() {
		if ( ! self::$shop_wrap_open ) {
			return;
		}

		echo '</div>'; // .tz-woo-shop__main

		$side = self::sidebar_position();
		if ( 'right' === $side && self::has_shop_sidebar() ) {
			self::render_shop_sidebar();
		}

		echo '</div>'; // .tz-woo-shop
		echo '<div class="tz-woo-filters-overlay" data-tz-woo-filters-overlay hidden></div>';

		self::$shop_wrap_open = false;
	}

	/**
	 * @return void
	 */
	public static function render_shop_sidebar() {
		if ( ! self::has_shop_sidebar() ) {
			return;
		}
		?>
		<aside class="tz-woo-shop__sidebar" data-tz-woo-shop-sidebar aria-label="<?php echo esc_attr__( 'Shop filters', 'themezur' ); ?>">
			<div class="tz-woo-shop__sidebar-head">
				<span class="tz-woo-shop__sidebar-title"><?php esc_html_e( 'Filters', 'themezur' ); ?></span>
				<button type="button" class="tz-woo-filters-close" data-tz-woo-filters-close aria-label="<?php echo esc_attr__( 'Close filters', 'themezur' ); ?>">
					&times;
				</button>
			</div>
			<div class="tz-woo-shop__sidebar-body">
				<?php dynamic_sidebar( 'themezur-shop' ); ?>
			</div>
		</aside>
		<?php
	}

	/**
	 * Mobile filters toggle (only when sidebar has widgets).
	 *
	 * @return void
	 */
	public static function render_filters_toggle() {
		if ( ! self::has_shop_sidebar() || 'none' === self::sidebar_position() ) {
			return;
		}
		static $printed = false;
		if ( $printed ) {
			return;
		}
		$printed = true;
		?>
		<button type="button" class="tz-woo-filters-toggle" data-tz-woo-filters-toggle>
			<?php esc_html_e( 'Filters', 'themezur' ); ?>
		</button>
		<?php
	}

	/**
	 * Loop thumbnail + optional hover gallery image.
	 *
	 * @return void
	 */
	public static function loop_thumbnail() {
		global $product;
		if ( ! $product instanceof WC_Product ) {
			echo woocommerce_get_product_thumbnail(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			return;
		}

		$hover = Themezur_Options::get( 'woocommerce.shop.hover_image', true );
		$ids   = $hover ? $product->get_gallery_image_ids() : array();
		$has_hover = ! empty( $ids[0] );

		echo '<span class="tz-woo-card__media' . ( $has_hover ? ' has-hover' : '' ) . '">';
		echo woocommerce_get_product_thumbnail(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		if ( $has_hover ) {
			echo wp_get_attachment_image(
				(int) $ids[0],
				'woocommerce_thumbnail',
				false,
				array(
					'class'         => 'tz-woo-card__hover-img',
					'alt'           => '',
					'aria-hidden'   => 'true',
					'loading'       => 'lazy',
					'decoding'      => 'async',
				)
			);
		}

		echo '</span>';
	}

	/**
	 * “New” badge for recently published products.
	 *
	 * @return void
	 */
	public static function render_new_badge() {
		global $product;
		$days = (int) Themezur_Options::get( 'woocommerce.shop.new_badge_days', 14 );
		if ( $days <= 0 || ! $product instanceof WC_Product ) {
			return;
		}

		$created = $product->get_date_created();
		if ( ! $created ) {
			return;
		}

		$cutoff = time() - ( $days * DAY_IN_SECONDS );
		if ( $created->getTimestamp() < $cutoff ) {
			return;
		}

		echo '<span class="tz-woo-badge tz-woo-badge--new">' . esc_html__( 'New', 'themezur' ) . '</span>';
	}

	/**
	 * YITH wishlist button on product cards (degrades if plugin inactive).
	 *
	 * @return void
	 */
	public static function render_loop_wishlist() {
		if ( ! Themezur_Options::get( 'woocommerce.shop.wishlist_on_card', true ) ) {
			return;
		}
		if ( ! function_exists( 'YITH_WCWL' ) && ! shortcode_exists( 'yith_wcwl_add_to_wishlist' ) ) {
			return;
		}

		global $product;
		if ( ! $product instanceof WC_Product ) {
			return;
		}

		echo '<div class="tz-woo-card__wish">';
		echo do_shortcode( '[yith_wcwl_add_to_wishlist product_id="' . absint( $product->get_id() ) . '"]' );
		echo '</div>';
	}

	/**
	 * Trust / shipping note under add to cart.
	 *
	 * @return void
	 */
	public static function render_trust_note() {
		$trust = trim( (string) Themezur_Options::get( 'woocommerce.single.trust_note', '' ) );
		if ( '' === $trust ) {
			return;
		}
		echo '<p class="tz-woo-trust">' . esc_html( $trust ) . '</p>';
	}

	/**
	 * Sale badge with optional percent off.
	 *
	 * @param string     $html    Flash HTML.
	 * @param WP_Post    $post    Post.
	 * @param WC_Product $product Product.
	 * @return string
	 */
	public static function sale_flash( $html, $post, $product ) {
		if ( ! self::is_enabled() || ! Themezur_Options::get( 'woocommerce.single.sale_percent', true ) ) {
			return $html;
		}
		if ( ! $product instanceof WC_Product || ! $product->is_on_sale() ) {
			return $html;
		}

		$percent = self::sale_percent_for_product( $product );
		if ( $percent <= 0 ) {
			return $html;
		}

		$label = sprintf(
			/* translators: %d: discount percent */
			__( '-%d%%', 'themezur' ),
			$percent
		);

		return '<span class="onsale tz-woo-sale-pct">' . esc_html( $label ) . '</span>';
	}

	/**
	 * @param WC_Product $product Product.
	 * @return int
	 */
	private static function sale_percent_for_product( $product ) {
		if ( $product->is_type( 'variable' ) ) {
			$percents = array();
			foreach ( $product->get_children() as $child_id ) {
				$child = wc_get_product( $child_id );
				if ( ! $child || ! $child->is_on_sale() ) {
					continue;
				}
				$regular = (float) $child->get_regular_price();
				$sale    = (float) $child->get_sale_price();
				if ( $regular > 0 && $sale >= 0 && $sale < $regular ) {
					$percents[] = (int) round( ( ( $regular - $sale ) / $regular ) * 100 );
				}
			}
			return ! empty( $percents ) ? (int) max( $percents ) : 0;
		}

		$regular = (float) $product->get_regular_price();
		$sale    = (float) $product->get_sale_price();
		if ( $regular <= 0 || $sale < 0 || $sale >= $regular ) {
			return 0;
		}
		return (int) round( ( ( $regular - $sale ) / $regular ) * 100 );
	}

	/**
	 * Sticky add-to-cart bar (desktop; product pages only).
	 *
	 * @return void
	 */
	public static function render_sticky_atc() {
		if ( ! self::is_enabled() || ! is_product() ) {
			return;
		}
		if ( ! Themezur_Options::get( 'woocommerce.single.sticky_atc', true ) ) {
			return;
		}
		if ( ! Themezur_Options::get( 'general.scripts_enabled', true ) ) {
			return;
		}

		global $product;
		if ( ! $product instanceof WC_Product ) {
			$product = wc_get_product( get_the_ID() );
		}
		if ( ! $product instanceof WC_Product || ! $product->is_purchasable() ) {
			return;
		}

		$img = $product->get_image( 'thumbnail', array( 'class' => 'tz-woo-sticky-atc__img' ) );
		?>
		<div class="tz-woo-sticky-atc" data-tz-sticky-atc hidden>
			<div class="tz-woo-sticky-atc__inner">
				<div class="tz-woo-sticky-atc__meta">
					<?php echo $img; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- WC product image. ?>
					<div class="tz-woo-sticky-atc__text">
						<span class="tz-woo-sticky-atc__title"><?php echo esc_html( $product->get_name() ); ?></span>
						<span class="tz-woo-sticky-atc__price"><?php echo wp_kses_post( $product->get_price_html() ); ?></span>
					</div>
				</div>
				<button type="button" class="tz-woo-sticky-atc__btn button">
					<?php echo esc_html( $product->add_to_cart_text() ); ?>
				</button>
			</div>
		</div>
		<?php
	}

	/**
	 * Mini-cart drawer shell in footer.
	 *
	 * @return void
	 */
	public static function render_mini_cart_drawer() {
		if ( ! self::mini_cart_enabled() ) {
			return;
		}
		if ( ! Themezur_Options::get( 'general.scripts_enabled', true ) ) {
			return;
		}
		if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
			return;
		}
		if ( is_cart() || is_checkout() ) {
			return;
		}
		?>
		<div class="tz-woo-mini-cart" data-tz-mini-cart hidden>
			<div class="tz-woo-mini-cart__overlay" data-tz-mini-cart-overlay></div>
			<div class="tz-woo-mini-cart__drawer" role="dialog" aria-modal="true" aria-label="<?php echo esc_attr__( 'Cart', 'themezur' ); ?>">
				<div class="tz-woo-mini-cart__head">
					<strong><?php esc_html_e( 'Your cart', 'themezur' ); ?></strong>
					<button type="button" class="tz-woo-mini-cart__close" data-tz-mini-cart-close aria-label="<?php echo esc_attr__( 'Close cart', 'themezur' ); ?>">&times;</button>
				</div>
				<?php self::render_mini_cart_panel(); ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Mini-cart panel markup (also used as fragment).
	 *
	 * @return void
	 */
	public static function render_mini_cart_panel() {
		$template = THEMEZUR_DIR . '/template-parts/woocommerce/mini-cart.php';
		if ( file_exists( $template ) ) {
			include $template;
		}
	}

	/**
	 * @param array $fragments Fragments.
	 * @return array
	 */
	public static function mini_cart_fragment( $fragments ) {
		if ( ! self::mini_cart_enabled() ) {
			return $fragments;
		}
		ob_start();
		self::render_mini_cart_panel();
		$fragments['div.tz-woo-mini-cart__panel'] = ob_get_clean();
		return $fragments;
	}

	/**
	 * AJAX: update / remove mini-cart line quantities.
	 *
	 * @return void
	 */
	public static function ajax_mini_cart() {
		check_ajax_referer( 'themezur_front', 'nonce' );

		if ( ! self::mini_cart_enabled() || ! function_exists( 'WC' ) || ! WC()->cart ) {
			wp_send_json_error( array( 'message' => 'unavailable' ), 400 );
		}

		$key = isset( $_POST['cart_item_key'] ) ? sanitize_text_field( wp_unslash( $_POST['cart_item_key'] ) ) : '';
		$cart_contents = WC()->cart->get_cart();
		if ( '' === $key || ! isset( $cart_contents[ $key ] ) ) {
			wp_send_json_error( array( 'message' => 'invalid_item' ), 400 );
		}

		$remove = ! empty( $_POST['remove'] );
		$qty    = isset( $_POST['quantity'] ) ? absint( $_POST['quantity'] ) : 0;

		if ( $remove ) {
			WC()->cart->remove_cart_item( $key );
		} else {
			WC()->cart->set_quantity( $key, $qty, true );
		}

		WC()->cart->calculate_totals();

		ob_start();
		self::render_mini_cart_panel();
		$panel = ob_get_clean();

		$count = (int) WC()->cart->get_cart_contents_count();

		wp_send_json_success(
			array(
				'fragments' => array(
					'div.tz-woo-mini-cart__panel' => $panel,
					'span.tz-cart-btn__count'     => '<span class="tz-cart-btn__count" data-tz-cart-count>' . esc_html( (string) $count ) . '</span>',
				),
				'cart_hash' => WC()->cart->get_cart_hash(),
			)
		);
	}

	/**
	 * Checkout polish hooks.
	 *
	 * @return void
	 */
	public static function setup_checkout() {
		if ( ! self::is_enabled() || ! is_checkout() || is_order_received_page() ) {
			return;
		}
		$trust = trim( (string) Themezur_Options::get( 'woocommerce.checkout.trust_note', '' ) );
		if ( '' !== $trust ) {
			add_action( 'woocommerce_review_order_before_submit', array( __CLASS__, 'render_checkout_trust' ), 5 );
		}
	}

	/**
	 * @return void
	 */
	public static function render_checkout_trust() {
		$trust = trim( (string) Themezur_Options::get( 'woocommerce.checkout.trust_note', '' ) );
		if ( '' === $trust ) {
			return;
		}
		echo '<p class="tz-woo-checkout-trust">' . esc_html( $trust ) . '</p>';
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
		if ( self::mini_cart_enabled() ) {
			$classes[] = 'tz-woo-has-mini-cart';
		}
		if ( self::is_catalog() ) {
			$side      = self::sidebar_position();
			$classes[] = 'tz-woo-sidebar--' . sanitize_html_class( $side );
			if ( Themezur_Options::get( 'woocommerce.shop.hover_image', true ) ) {
				$classes[] = 'tz-woo-hover-image';
			}
		}
		if ( is_product() ) {
			$layout    = Themezur_Options::get( 'woocommerce.single.layout', 'classic' );
			$allowed   = array( 'classic', 'stacked', 'gallery_wide' );
			$layout    = in_array( $layout, $allowed, true ) ? $layout : 'classic';
			$classes[] = 'tz-woo-layout--' . sanitize_html_class( $layout );
			if ( Themezur_Options::get( 'woocommerce.single.sticky_atc', true ) ) {
				$classes[] = 'tz-woo-has-sticky-atc';
			}
		}
		if ( is_checkout() && ! is_order_received_page() && Themezur_Options::get( 'woocommerce.checkout.sticky_review', true ) ) {
			$classes[] = 'tz-woo-checkout-sticky';
		}
		if ( is_account_page() ) {
			$density   = Themezur_Options::get( 'woocommerce.account.density', 'comfortable' );
			$density   = in_array( $density, array( 'comfortable', 'compact' ), true ) ? $density : 'comfortable';
			$classes[] = 'tz-woo-account--' . sanitize_html_class( $density );
		}
		return $classes;
	}
}
