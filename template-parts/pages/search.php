<?php
/**
 * Themezur search results template part.
 *
 * @package Themezur
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$s            = Themezur_Options::get( 'pages.search', array() );
$layout       = isset( $s['layout'] ) && 'grid' === $s['layout'] ? 'grid' : 'list';
$show_details = ! isset( $s['show_product_details'] ) || ! empty( $s['show_product_details'] );
global $wp_query;
$total_found  = $wp_query->found_posts;
$query_str    = get_search_query();
?>
<main id="content" class="site-main tz-page tz-page--search tz-page--<?php echo esc_attr( $layout ); ?>">
	<div class="tz-page__inner">
		<?php Themezur_Breadcrumbs::render(); ?>

		<header class="tz-page-header">
			<div class="tz-search-header-meta">
				<h1 class="tz-page__title">
					<?php
					printf(
						/* translators: %s: search query */
						esc_html__( 'Search results for “%s”', 'themezur' ),
						esc_html( $query_str )
					);
					?>
				</h1>
				<span class="tz-search-badge">
					<?php
					printf(
						/* translators: %d: total count */
						esc_html( _n( '%d item found', '%d items found', $total_found, 'themezur' ) ),
						(int) $total_found
					);
					?>
				</span>
			</div>

			<form class="tz-page-search" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
				<label class="screen-reader-text" for="tz-search-again"><?php esc_html_e( 'Search', 'themezur' ); ?></label>
				<div class="tz-search-input-wrap">
					<input id="tz-search-again" type="search" name="s" value="<?php echo esc_attr( $query_str ); ?>" placeholder="<?php esc_attr_e( 'Search again…', 'themezur' ); ?>">
					<button type="submit" class="button tz-btn"><?php esc_html_e( 'Search', 'themezur' ); ?></button>
				</div>
			</form>
		</header>

		<?php if ( have_posts() ) : ?>
			<div class="tz-search-loop">
				<?php
				while ( have_posts() ) :
					the_post();
					$pid       = get_the_ID();
					$post_type = get_post_type();
					$is_woo    = 'product' === $post_type && class_exists( 'WooCommerce' );
					$type_obj  = get_post_type_object( $post_type );
					$type_lbl  = $type_obj && ! empty( $type_obj->labels->singular_name ) ? $type_obj->labels->singular_name : $post_type;
					?>
					<article <?php post_class( 'tz-search-card' . ( $is_woo ? ' tz-search-card--product' : '' ) ); ?>>
						<?php if ( ! empty( $s['show_image'] ) && has_post_thumbnail() ) : ?>
							<a class="tz-search-card__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
								<?php the_post_thumbnail( 'medium', array( 'class' => 'tz-search-card__img' ) ); ?>
							</a>
						<?php endif; ?>
						<div class="tz-search-card__body">
							<?php if ( ! empty( $s['show_type'] ) ) : ?>
								<span class="tz-search-card__type<?php echo $is_woo ? ' tz-search-card__type--woo' : ''; ?>">
									<?php echo esc_html( $type_lbl ); ?>
								</span>
							<?php endif; ?>

							<h2 class="tz-search-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

							<?php if ( $is_woo && $show_details ) : ?>
								<?php
								$product = wc_get_product( $pid );
								if ( $product ) :
									?>
									<div class="tz-search-card__woo-meta">
										<span class="tz-search-card__price"><?php echo wp_kses_post( $product->get_price_html() ); ?></span>
										<?php if ( $product->is_in_stock() ) : ?>
											<span class="tz-search-card__stock in-stock"><?php esc_html_e( 'In stock', 'themezur' ); ?></span>
										<?php endif; ?>
									</div>
									<div class="tz-search-card__woo-action">
										<?php woocommerce_template_loop_add_to_cart(); ?>
									</div>
								<?php endif; ?>
							<?php elseif ( ! empty( $s['show_excerpt'] ) ) : ?>
								<p class="tz-search-card__excerpt"><?php echo esc_html( Themezur_Blog::excerpt( $pid, 24 ) ); ?></p>
							<?php endif; ?>
						</div>
					</article>
				<?php endwhile; ?>
			</div>

			<?php
			the_posts_pagination(
				array(
					'mid_size'  => 1,
					'prev_text' => __( 'Previous', 'themezur' ),
					'next_text' => __( 'Next', 'themezur' ),
				)
			);
			?>
		<?php else : ?>
			<div class="tz-search-no-results">
				<p class="tz-page__text"><?php esc_html_e( 'Nothing matched your search criteria. Try using different keywords or browse our categories.', 'themezur' ); ?></p>
				<a class="button" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to home', 'themezur' ); ?></a>
			</div>
		<?php endif; ?>
	</div>
</main>
