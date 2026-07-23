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
?>
<main id="content" class="site-main tz-page tz-page--404">
	<div class="tz-page__inner">
		<?php Themezur_Breadcrumbs::render(); ?>
		<p class="tz-page__code" aria-hidden="true">404</p>
		<h1 class="tz-page__title"><?php echo esc_html( $title ); ?></h1>
		<p class="tz-page__text"><?php echo esc_html( $text ); ?></p>

		<?php if ( ! empty( $nf['show_search'] ) ) : ?>
			<form class="tz-page-search" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
				<label class="screen-reader-text" for="tz-404-search"><?php esc_html_e( 'Search', 'themezur' ); ?></label>
				<input id="tz-404-search" type="search" name="s" placeholder="<?php esc_attr_e( 'Search this site…', 'themezur' ); ?>">
				<button type="submit" class="tz-btn"><?php esc_html_e( 'Search', 'themezur' ); ?></button>
			</form>
		<?php endif; ?>

		<?php if ( ! empty( $nf['show_home_btn'] ) ) : ?>
			<p class="tz-page__actions">
				<a class="tz-btn" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo esc_html( $home_label ); ?></a>
			</p>
		<?php endif; ?>
	</div>
</main>
