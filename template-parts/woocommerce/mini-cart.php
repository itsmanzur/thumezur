<?php
/**
 * Mini-cart drawer panel (fragment target).
 *
 * @package Themezur
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
	return;
}

$cart     = WC()->cart;
$items    = $cart->get_cart();
$cart_url = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : home_url( '/' );
$checkout = function_exists( 'wc_get_checkout_url' ) ? wc_get_checkout_url() : $cart_url;
?>
<div class="tz-woo-mini-cart__panel" data-tz-mini-cart-panel>
	<?php if ( empty( $items ) ) : ?>
		<div class="tz-woo-mini-cart__empty">
			<p><?php esc_html_e( 'Your cart is empty.', 'themezur' ); ?></p>
			<a class="button tz-woo-mini-cart__shop" href="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' ) ); ?>">
				<?php esc_html_e( 'Continue shopping', 'themezur' ); ?>
			</a>
		</div>
	<?php else : ?>
		<ul class="tz-woo-mini-cart__items">
			<?php foreach ( $items as $cart_item_key => $cart_item ) : ?>
				<?php
				$product = isset( $cart_item['data'] ) ? $cart_item['data'] : null;
				if ( ! $product instanceof WC_Product || ! $product->exists() || $cart_item['quantity'] <= 0 ) {
					continue;
				}
				$permalink = $product->is_visible() ? $product->get_permalink( $cart_item ) : '';
				$thumbnail = $product->get_image( 'thumbnail', array( 'class' => 'tz-woo-mini-cart__thumb' ) );
				$name      = $product->get_name();
				$qty       = (int) $cart_item['quantity'];
				$max       = $product->get_max_purchase_quantity();
				$max_attr  = ( $max > 0 ) ? (string) $max : '';
				?>
				<li class="tz-woo-mini-cart__item" data-key="<?php echo esc_attr( $cart_item_key ); ?>">
					<div class="tz-woo-mini-cart__media">
						<?php if ( $permalink ) : ?>
							<a href="<?php echo esc_url( $permalink ); ?>"><?php echo $thumbnail; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
						<?php else : ?>
							<?php echo $thumbnail; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php endif; ?>
					</div>
					<div class="tz-woo-mini-cart__meta">
						<?php if ( $permalink ) : ?>
							<a class="tz-woo-mini-cart__name" href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $name ); ?></a>
						<?php else : ?>
							<span class="tz-woo-mini-cart__name"><?php echo esc_html( $name ); ?></span>
						<?php endif; ?>
						<div class="tz-woo-mini-cart__price"><?php echo wp_kses_post( $cart->get_product_subtotal( $product, $qty ) ); ?></div>
						<div class="tz-woo-mini-cart__qty">
							<button type="button" class="tz-woo-mini-cart__qty-btn" data-tz-mini-qty="-1" aria-label="<?php echo esc_attr__( 'Decrease quantity', 'themezur' ); ?>">−</button>
							<input
								type="number"
								class="tz-woo-mini-cart__qty-input"
								value="<?php echo esc_attr( (string) $qty ); ?>"
								min="0"
								<?php echo '' !== $max_attr ? 'max="' . esc_attr( $max_attr ) . '"' : ''; ?>
								data-tz-mini-qty-input
								aria-label="<?php echo esc_attr__( 'Quantity', 'themezur' ); ?>"
							>
							<button type="button" class="tz-woo-mini-cart__qty-btn" data-tz-mini-qty="1" aria-label="<?php echo esc_attr__( 'Increase quantity', 'themezur' ); ?>">+</button>
						</div>
					</div>
					<button type="button" class="tz-woo-mini-cart__remove" data-tz-mini-remove aria-label="<?php echo esc_attr__( 'Remove item', 'themezur' ); ?>">&times;</button>
				</li>
			<?php endforeach; ?>
		</ul>
		<div class="tz-woo-mini-cart__footer">
			<div class="tz-woo-mini-cart__subtotal">
				<span><?php esc_html_e( 'Subtotal', 'themezur' ); ?></span>
				<strong><?php echo wp_kses_post( $cart->get_cart_subtotal() ); ?></strong>
			</div>
			<div class="tz-woo-mini-cart__actions">
				<a class="button tz-woo-mini-cart__cart" href="<?php echo esc_url( $cart_url ); ?>"><?php esc_html_e( 'View cart', 'themezur' ); ?></a>
				<a class="button tz-woo-mini-cart__checkout" href="<?php echo esc_url( $checkout ); ?>"><?php esc_html_e( 'Checkout', 'themezur' ); ?></a>
			</div>
		</div>
	<?php endif; ?>
</div>
