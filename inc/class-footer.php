<?php
/**
 * Footer helpers: posts, products, back-to-top, payment icons.
 *
 * @package Themezur
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Themezur_Footer
 */
class Themezur_Footer {

	/**
	 * Recent posts for a footer column.
	 *
	 * @param array $col Column options.
	 * @return WP_Post[]
	 */
	public static function query_posts( array $col ) {
		$count = isset( $col['posts_count'] ) ? (int) $col['posts_count'] : 3;
		$count = max( 1, min( 8, $count ) );
		$args  = array(
			'post_type'           => 'post',
			'post_status'         => 'publish',
			'posts_per_page'      => $count,
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		);
		$cat = isset( $col['posts_category'] ) ? (int) $col['posts_category'] : 0;
		if ( $cat > 0 ) {
			$args['cat'] = $cat;
		}
		$query = new WP_Query( $args );
		return $query->posts;
	}

	/**
	 * Products for a footer column (WooCommerce).
	 *
	 * @param array $col Column options.
	 * @return WC_Product[]
	 */
	public static function query_products( array $col ) {
		if ( ! class_exists( 'WooCommerce' ) || ! function_exists( 'wc_get_products' ) ) {
			return array();
		}

		$count  = isset( $col['products_count'] ) ? (int) $col['products_count'] : 3;
		$count  = max( 1, min( 8, $count ) );
		$source = isset( $col['products_source'] ) ? $col['products_source'] : 'recent';
		$args   = array(
			'status'  => 'publish',
			'limit'   => $count,
			'orderby' => 'date',
			'order'   => 'DESC',
			'return'  => 'objects',
		);

		switch ( $source ) {
			case 'featured':
				$args['featured'] = true;
				break;
			case 'on_sale':
				$args['include'] = array_map( 'absint', wc_get_product_ids_on_sale() );
				if ( empty( $args['include'] ) ) {
					return array();
				}
				break;
			case 'top_rated':
				$args['orderby'] = 'rating';
				$args['order']   = 'DESC';
				break;
			default:
				$source = 'recent';
				break;
		}

		$products = wc_get_products( $args );
		return is_array( $products ) ? $products : array();
	}

	/**
	 * Print back-to-top button markup with circular progress ring.
	 *
	 * @return void
	 */
	public static function render_back_to_top() {
		$btt = Themezur_Options::get( 'footer.back_to_top', array() );
		if ( empty( $btt['enabled'] ) ) {
			return;
		}
		$threshold = isset( $btt['threshold'] ) ? (int) $btt['threshold'] : 400;
		$threshold = max( 100, min( 2000, $threshold ) );
		?>
		<button
			type="button"
			class="tz-btt"
			data-tz-btt
			data-tz-threshold="<?php echo esc_attr( (string) $threshold ); ?>"
			aria-label="<?php echo esc_attr__( 'Back to top', 'themezur' ); ?>"
			hidden
		>
			<svg class="tz-btt-ring" width="44" height="44" viewBox="0 0 44 44" aria-hidden="true">
				<circle class="tz-btt-ring__bg" cx="22" cy="22" r="18" fill="none" stroke-width="3" />
				<circle class="tz-btt-ring__progress" id="tz-btt-progress" cx="22" cy="22" r="18" fill="none" stroke-width="3" />
			</svg>
			<span class="tz-btt__icon" aria-hidden="true">
				<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 19V5"/><path d="M5 12l7-7 7 7"/></svg>
			</span>
		</button>
		<?php
	}

	/**
	 * Print Trust Badges section (Pre-Footer Bar).
	 *
	 * @return void
	 */
	public static function render_trust_badges() {
		$tb = Themezur_Options::get( 'footer.trust_badges', array() );
		if ( empty( $tb['enabled'] ) || empty( $tb['items'] ) || ! is_array( $tb['items'] ) ) {
			return;
		}

		$icons = array(
			'shipping' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>',
			'return'   => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/></svg>',
			'secure'   => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>',
			'support'  => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>',
		);

		echo '<div class="tz-trust-badges"><div class="tz-trust-badges__inner">';
		foreach ( $tb['items'] as $item ) {
			if ( empty( $item['title'] ) ) {
				continue;
			}
			$key = isset( $item['icon'] ) ? $item['icon'] : 'shipping';
			$svg = $icons[ $key ] ?? $icons['shipping'];
			echo '<div class="tz-trust-badge">';
			echo '<div class="tz-trust-badge__icon">' . $svg . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo '<div class="tz-trust-badge__info">';
			echo '<h4 class="tz-trust-badge__title">' . esc_html( $item['title'] ) . '</h4>';
			if ( ! empty( $item['subtitle'] ) ) {
				echo '<p class="tz-trust-badge__desc">' . esc_html( $item['subtitle'] ) . '</p>';
			}
			echo '</div></div>';
		}
		echo '</div></div>';
	}

	/**
	 * Print SVG Payment Method Badges.
	 *
	 * @return void
	 */
	public static function render_payment_icons() {
		$bottom = Themezur_Options::get( 'footer.bottom', array() );
		if ( empty( $bottom['show_payments'] ) ) {
			return;
		}
		$methods = ! empty( $bottom['payments'] ) && is_array( $bottom['payments'] )
			? $bottom['payments']
			: array( 'bkash', 'nagad', 'rocket', 'cod', 'visa', 'mastercard', 'amex', 'paypal' );

		if ( empty( $methods ) ) {
			return;
		}

		echo '<div class="tz-footer-payments" aria-label="' . esc_attr__( 'Payment methods', 'themezur' ) . '">';
		foreach ( $methods as $method ) {
			echo self::get_payment_icon_svg( $method ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		echo '</div>';
	}

	/**
	 * Return clean SVG badge markup for payment methods.
	 *
	 * @param string $key Method key.
	 * @return string
	 */
	private static function get_payment_icon_svg( $key ) {
		$labels = array(
			'visa'       => 'Visa',
			'mastercard' => 'Mastercard',
			'amex'       => 'American Express',
			'paypal'     => 'PayPal',
			'applepay'   => 'Apple Pay',
			'bkash'      => 'bKash',
			'nagad'      => 'Nagad',
			'rocket'     => 'Rocket',
			'cod'        => 'Cash on Delivery',
		);
		$label = $labels[ $key ] ?? ucfirst( $key );

		$svgs = array(
			'visa'       => '<svg class="tz-pay-badge tz-pay-badge--visa" width="38" height="24" viewBox="0 0 38 24" fill="none" aria-label="Visa"><rect width="38" height="24" rx="4" fill="#1434CB"/><path d="M14.5 16.5l1.6-9.5h2.6l-1.6 9.5h-2.6zm7.8-9.3c-.5-.2-1.3-.4-2.2-.4-2.4 0-4.1 1.2-4.1 3 0 1.3 1.2 2 2.1 2.4.9.4 1.2.7 1.2 1.1 0 .6-.7.9-1.4.9-1 0-1.5-.1-2.3-.5l-.3-.2-.3 2c.6.3 1.7.5 2.8.5 2.6 0 4.3-1.2 4.3-3.1 0-1-.6-1.8-2-2.4-.8-.4-1.3-.7-1.3-1.1 0-.4.4-.8 1.4-.8.8 0 1.4.2 1.8.4l.2.1.4-1.9zm5.9-.2h-2c-.6 0-1.1.2-1.3.8l-3.7 8.7h2.7l.5-1.5h3.3l.3 1.5h2.4l-2.2-9.5zm-2.8 6.1l1.4-3.7.8 3.7h-2.2zM12.3 7.2L9.8 13.7l-.3-1.4c-.5-1.6-2-3.4-3.7-4.2l2.3 8.4h2.7l4.1-9.3h-2.6z" fill="#fff"/></svg>',
			'mastercard' => '<svg class="tz-pay-badge tz-pay-badge--mc" width="38" height="24" viewBox="0 0 38 24" fill="none" aria-label="Mastercard"><rect width="38" height="24" rx="4" fill="#1E293B"/><circle cx="15" cy="12" r="7" fill="#EB001B"/><circle cx="23" cy="12" r="7" fill="#F79E1B"/><path d="M19 6.8a6.96 6.96 0 012.5 5.2 6.96 6.96 0 01-2.5 5.2 6.96 6.96 0 01-2.5-5.2c0-2 0.9-3.8 2.5-5.2z" fill="#FF5F00"/></svg>',
			'amex'       => '<svg class="tz-pay-badge tz-pay-badge--amex" width="38" height="24" viewBox="0 0 38 24" fill="none" aria-label="American Express"><rect width="38" height="24" rx="4" fill="#006FCF"/><text x="19" y="15" font-family="sans-serif" font-size="8" font-weight="bold" fill="#fff" text-anchor="middle">AMEX</text></svg>',
			'paypal'     => '<svg class="tz-pay-badge tz-pay-badge--paypal" width="38" height="24" viewBox="0 0 38 24" fill="none" aria-label="PayPal"><rect width="38" height="24" rx="4" fill="#003087"/><path d="M13 6h4.5c2 0 3.2 1 2.8 3-.4 2.2-2 3.5-4 3.5h-1.5l-1 5h-2.5l1.7-11.5z" fill="#0079C1"/><path d="M15 8h4.5c2 0 3.2 1 2.8 3-.4 2.2-2 3.5-4 3.5h-1.5l-1 5h-2.5l1.7-11.5z" fill="#00457C" opacity="0.4"/><path d="M15.5 8h4.2c1.8 0 2.9.9 2.5 2.7-.4 2-1.8 3.1-3.6 3.1h-1.4l-.9 4.5h-2.3l1.5-10.3z" fill="#0079C1"/></svg>',
			'applepay'   => '<svg class="tz-pay-badge tz-pay-badge--apple" width="38" height="24" viewBox="0 0 38 24" fill="none" aria-label="Apple Pay"><rect width="38" height="24" rx="4" fill="#000"/><text x="19" y="15" font-family="sans-serif" font-size="9" font-weight="bold" fill="#fff" text-anchor="middle">Pay</text></svg>',
			'bkash'      => '<svg class="tz-pay-badge tz-pay-badge--bkash" width="38" height="24" viewBox="0 0 38 24" fill="none" aria-label="bKash"><rect width="38" height="24" rx="4" fill="#E2136E"/><text x="19" y="15" font-family="sans-serif" font-size="8.5" font-weight="bold" fill="#fff" text-anchor="middle">bKash</text></svg>',
			'nagad'      => '<svg class="tz-pay-badge tz-pay-badge--nagad" width="38" height="24" viewBox="0 0 38 24" fill="none" aria-label="Nagad"><rect width="38" height="24" rx="4" fill="#F7941D"/><text x="19" y="15" font-family="sans-serif" font-size="8.5" font-weight="bold" fill="#fff" text-anchor="middle">নগদ</text></svg>',
			'rocket'     => '<svg class="tz-pay-badge tz-pay-badge--rocket" width="38" height="24" viewBox="0 0 38 24" fill="none" aria-label="Rocket"><rect width="38" height="24" rx="4" fill="#8C3494"/><text x="19" y="15" font-family="sans-serif" font-size="8" font-weight="bold" fill="#fff" text-anchor="middle">Rocket</text></svg>',
			'cod'        => '<svg class="tz-pay-badge tz-pay-badge--cod" width="38" height="24" viewBox="0 0 38 24" fill="none" aria-label="Cash on Delivery"><rect width="38" height="24" rx="4" fill="#059669"/><text x="19" y="15" font-family="sans-serif" font-size="8" font-weight="bold" fill="#fff" text-anchor="middle">COD</text></svg>',
		);

		return $svgs[ $key ] ?? '<span class="tz-pay-badge">' . esc_html( $label ) . '</span>';
	}
}
