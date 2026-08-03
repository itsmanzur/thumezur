<?php
/**
 * Themezur 404 template part.
 *
 * @package Themezur
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$nf         = Themezur_Options::get( 'pages.not_found', array() );
$title      = ! empty( $nf['title'] ) ? $nf['title'] : __( 'Page not found', 'themezur' );
$text       = ! empty( $nf['text'] ) ? $nf['text'] : __( 'The page you are looking for may have been moved or no longer exists.', 'themezur' );
$home_label = ! empty( $nf['home_label'] ) ? $nf['home_label'] : __( 'Back to home', 'themezur' );
$quick_links = ! isset( $nf['show_quick_links'] ) || ! empty( $nf['show_quick_links'] );
?>
<main id="content" class="site-main tz-page tz-page--404">
	<div class="tz-page__inner">
		<?php Themezur_Breadcrumbs::render(); ?>

		<div class="tz-404-hero">
			<div class="tz-404-badge" aria-hidden="true">
				<span class="tz-404-badge__num">404</span>
				<div class="tz-404-badge__glow"></div>
			</div>
			<h1 class="tz-page__title"><?php echo esc_html( $title ); ?></h1>
			<p class="tz-page__text"><?php echo esc_html( $text ); ?></p>
		</div>

		<?php if ( ! empty( $nf['show_search'] ) ) : ?>
			<form class="tz-page-search tz-404-search" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
				<label class="screen-reader-text" for="tz-404-search"><?php esc_html_e( 'Search', 'themezur' ); ?></label>
				<div class="tz-404-search__wrap">
					<input id="tz-404-search" type="search" name="s" placeholder="<?php esc_attr_e( 'What are you looking for?', 'themezur' ); ?>">
					<button type="submit" class="button tz-404-search__btn">
						<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg>
						<span><?php esc_html_e( 'Search', 'themezur' ); ?></span>
					</button>
				</div>
			</form>
		<?php endif; ?>

		<?php if ( $quick_links ) : ?>
			<div class="tz-404-quick">
				<p class="tz-404-quick__heading"><?php esc_html_e( 'Or explore popular sections:', 'themezur' ); ?></p>
				<div class="tz-404-quick__grid">
					<a class="tz-404-card" href="<?php echo esc_url( home_url( '/' ) ); ?>">
						<span class="tz-404-card__icon" aria-hidden="true">🏠</span>
						<span class="tz-404-card__title"><?php esc_html_e( 'Home', 'themezur' ); ?></span>
					</a>
					<?php if ( class_exists( 'WooCommerce' ) ) : ?>
						<a class="tz-404-card" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">
							<span class="tz-404-card__icon" aria-hidden="true">🛍️</span>
							<span class="tz-404-card__title"><?php esc_html_e( 'Shop Store', 'themezur' ); ?></span>
						</a>
					<?php endif; ?>
					<a class="tz-404-card" href="<?php echo esc_url( get_permalink( get_option( 'page_for_posts' ) ) ?: home_url( '/' ) ); ?>">
						<span class="tz-404-card__icon" aria-hidden="true">📰</span>
						<span class="tz-404-card__title"><?php esc_html_e( 'Blog', 'themezur' ); ?></span>
					</a>
				</div>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $nf['show_home_btn'] ) ) : ?>
			<p class="tz-page__actions">
				<a class="button tz-btn--primary" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo esc_html( $home_label ); ?> →</a>
			</p>
		<?php endif; ?>
	</div>
</main>
