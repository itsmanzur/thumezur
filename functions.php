<?php
/**
 * Themezur — Hello Elementor child theme bootstrap.
 *
 * @package Themezur
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'THEMEZUR_VERSION', '2.6.3' );
define( 'THEMEZUR_DIR', get_stylesheet_directory() );
define( 'THEMEZUR_URI', get_stylesheet_directory_uri() );

require_once THEMEZUR_DIR . '/inc/class-themezur.php';

/**
 * Boot Themezur.
 *
 * @return void
 */
function themezur() {
	return Themezur::instance();
}
themezur();

/**
 * Enqueue child theme assets on the frontend.
 *
 * @return void
 */
function themezur_enqueue_scripts_styles() {
	$deps = array();
	if ( wp_style_is( 'hello-elementor-theme-style', 'enqueued' ) ) {
		$deps[] = 'hello-elementor-theme-style';
	} elseif ( wp_style_is( 'hello-elementor', 'enqueued' ) ) {
		$deps[] = 'hello-elementor';
	}

	wp_enqueue_style(
		'themezur-style',
		THEMEZUR_URI . '/style.css',
		$deps,
		THEMEZUR_VERSION
	);

	wp_enqueue_style(
		'themezur-design',
		THEMEZUR_URI . '/assets/css/design.css',
		array( 'themezur-style' ),
		THEMEZUR_VERSION
	);

	wp_enqueue_style(
		'themezur-mobile',
		THEMEZUR_URI . '/assets/css/mobile.css',
		array( 'themezur-design' ),
		THEMEZUR_VERSION
	);

	if ( class_exists( 'Themezur_Fonts' ) ) {
		Themezur_Fonts::enqueue();
	}

	if ( class_exists( 'Themezur_Breadcrumbs' ) && Themezur_Breadcrumbs::is_enabled() ) {
		wp_enqueue_style(
			'themezur-breadcrumbs',
			THEMEZUR_URI . '/assets/css/breadcrumbs.css',
			array( 'themezur-design' ),
			THEMEZUR_VERSION
		);
	}

	$header_mode = class_exists( 'Themezur_Frontend' ) ? Themezur_Frontend::resolve_mode( 'header' ) : 'theme';
	$footer_mode = class_exists( 'Themezur_Frontend' ) ? Themezur_Frontend::resolve_mode( 'footer' ) : 'theme';

	if ( 'theme' === $header_mode ) {
		wp_enqueue_style(
			'themezur-header',
			THEMEZUR_URI . '/assets/css/header.css',
			array( 'themezur-style' ),
			THEMEZUR_VERSION
		);

		if ( Themezur_Options::get( 'general.scripts_enabled', true ) ) {
			wp_enqueue_script(
				'themezur-header',
				THEMEZUR_URI . '/assets/js/header.js',
				array(),
				THEMEZUR_VERSION,
				array(
					'in_footer' => true,
					'strategy'  => 'defer',
				)
			);

			wp_localize_script(
				'themezur-header',
				'themezurFront',
				array(
					'ajaxUrl'    => admin_url( 'admin-ajax.php' ),
					'nonce'      => wp_create_nonce( 'themezur_front' ),
					'searchMin'  => (int) Themezur_Options::get( 'header.middle.search_min_chars', 2 ),
					'i18nNoResults' => __( 'No products found', 'themezur' ),
				)
			);
		}
	}

	if ( 'theme' === $footer_mode ) {
		wp_enqueue_style(
			'themezur-footer',
			THEMEZUR_URI . '/assets/css/footer.css',
			array( 'themezur-style' ),
			THEMEZUR_VERSION
		);
	}

	$btt_on = class_exists( 'Themezur_Options' ) && Themezur_Options::get( 'footer.back_to_top.enabled', true );
	if ( $btt_on || 'theme' === $footer_mode ) {
		if ( 'theme' !== $footer_mode ) {
			wp_enqueue_style(
				'themezur-footer',
				THEMEZUR_URI . '/assets/css/footer.css',
				array( 'themezur-style' ),
				THEMEZUR_VERSION
			);
		}
		if ( $btt_on && ( ! class_exists( 'Themezur_Options' ) || Themezur_Options::get( 'general.scripts_enabled', true ) ) ) {
			wp_enqueue_script(
				'themezur-footer',
				THEMEZUR_URI . '/assets/js/footer.js',
				array(),
				THEMEZUR_VERSION,
				array(
					'in_footer' => true,
					'strategy'  => 'defer',
				)
			);
		}
	}

	$blog_on = class_exists( 'Themezur_Blog' ) && Themezur_Blog::is_enabled();
	if ( $blog_on && ( is_home() || is_singular( 'post' ) || ( class_exists( 'Themezur_Blog' ) && Themezur_Blog::is_archive_context() ) ) ) {
		wp_enqueue_style(
			'themezur-blog',
			THEMEZUR_URI . '/assets/css/blog.css',
			array( 'themezur-style' ),
			THEMEZUR_VERSION
		);
	}

	$pages_on = class_exists( 'Themezur_Options' ) && 'theme' === Themezur_Options::get( 'pages.mode', 'theme' );
	if ( $pages_on && ( is_404() || is_search() ) ) {
		wp_enqueue_style(
			'themezur-pages',
			THEMEZUR_URI . '/assets/css/pages.css',
			array( 'themezur-design' ),
			THEMEZUR_VERSION
		);
	}

	$woo_on = class_exists( 'Themezur_WooCommerce' ) && Themezur_WooCommerce::is_enabled() && class_exists( 'WooCommerce' );
	if ( $woo_on && ( is_shop() || is_product_taxonomy() || is_product() || is_cart() || is_checkout() || is_account_page() ) ) {
		wp_enqueue_style(
			'themezur-woocommerce',
			THEMEZUR_URI . '/assets/css/woocommerce.css',
			array( 'themezur-design' ),
			THEMEZUR_VERSION
		);
	}
}
add_action( 'wp_enqueue_scripts', 'themezur_enqueue_scripts_styles', 20 );
