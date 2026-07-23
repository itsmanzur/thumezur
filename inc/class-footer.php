<?php
/**
 * Footer helpers: posts, products, back-to-top.
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
	 * Print back-to-top button markup.
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
			<span class="tz-btt__icon" aria-hidden="true">
				<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M12 19V5"/><path d="M5 12l7-7 7 7"/></svg>
			</span>
		</button>
		<?php
	}
}
