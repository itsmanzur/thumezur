<?php
/**
 * Themezur — Hello Elementor child theme bootstrap.
 *
 * @package Themezur
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'THEMEZUR_VERSION', '2.10.2' );
define( 'THEMEZUR_DIR', get_stylesheet_directory() );
define( 'THEMEZUR_URI', get_stylesheet_directory_uri() );

/**
 * Themezur requires the Hello Elementor parent theme.
 *
 * @return bool
 */
function themezur_parent_theme_ok() {
	return file_exists( get_template_directory() . '/style.css' ) && 'hello-elementor' === get_template();
}

if ( ! themezur_parent_theme_ok() ) {
	add_action(
		'admin_notices',
		static function () {
			if ( ! current_user_can( 'switch_themes' ) ) {
				return;
			}
			echo '<div class="notice notice-error"><p>';
			echo esc_html__( 'Themezur requires the Hello Elementor parent theme. Install and keep Hello Elementor active as the parent, then activate Themezur.', 'themezur' );
			echo '</p></div>';
		}
	);
	return;
}

$themezur_bootstrap = THEMEZUR_DIR . '/inc/class-themezur.php';
if ( ! file_exists( $themezur_bootstrap ) ) {
	add_action(
		'admin_notices',
		static function () {
			if ( ! current_user_can( 'switch_themes' ) ) {
				return;
			}
			echo '<div class="notice notice-error"><p>';
			echo esc_html__( 'Themezur is incomplete (missing /inc files). Re-upload the theme zip — do not extract with broken path separators.', 'themezur' );
			echo '</p></div>';
		}
	);
	return;
}

require_once $themezur_bootstrap;

/**
 * Boot Themezur.
 *
 * @return void
 */
function themezur() {
	return Themezur::instance();
}
add_action( 'after_setup_theme', 'themezur', 20 );

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
		wp_enqueue_style( 'dashicons' );

		wp_enqueue_style(
			'themezur-header',
			THEMEZUR_URI . '/assets/css/header.css',
			array( 'themezur-style' ),
			THEMEZUR_VERSION
		);

		wp_enqueue_style(
			'themezur-megamenu',
			THEMEZUR_URI . '/assets/css/megamenu.css',
			array( 'themezur-header' ),
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

			wp_enqueue_script(
				'themezur-megamenu',
				THEMEZUR_URI . '/assets/js/megamenu.js',
				array( 'themezur-header' ),
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
					'ajaxUrl'            => admin_url( 'admin-ajax.php' ),
					'nonce'              => wp_create_nonce( 'themezur_front' ),
					'searchMin'          => (int) Themezur_Options::get( 'header.middle.search_min_chars', 2 ),
					'i18nNoResults'      => __( 'No products found', 'themezur' ),
					'i18nToggleSubmenu'  => __( 'Toggle sub-menu', 'themezur' ),
					'miniCartOpenOnAdd'  => (bool) Themezur_Options::get( 'woocommerce.cart.open_on_add', true ),
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
		if ( ( $btt_on || 'theme' === $footer_mode ) && ( ! class_exists( 'Themezur_Options' ) || Themezur_Options::get( 'general.scripts_enabled', true ) ) ) {
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
			wp_localize_script(
				'themezur-footer',
				'themezurFooter',
				array(
					'ajaxUrl'     => admin_url( 'admin-ajax.php' ),
					'nonce'       => wp_create_nonce( 'themezur_front' ),
					'i18nSuccess' => __( 'Thanks for subscribing!', 'themezur' ),
					'i18nError'   => __( 'Could not subscribe. Please try again.', 'themezur' ),
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

		if ( Themezur_Options::get( 'general.scripts_enabled', true ) ) {
			wp_enqueue_script(
				'themezur-blog',
				THEMEZUR_URI . '/assets/js/blog.js',
				array(),
				THEMEZUR_VERSION,
				array(
					'in_footer' => true,
					'strategy'  => 'defer',
				)
			);
		}
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

	$woo_on = class_exists( 'Themezur_WooCommerce' ) && Themezur_WooCommerce::should_enqueue_assets();
	if ( $woo_on ) {
		wp_enqueue_style(
			'themezur-woocommerce',
			THEMEZUR_URI . '/assets/css/woocommerce.css',
			array( 'themezur-design' ),
			THEMEZUR_VERSION
		);

		$scripts_on = Themezur_Options::get( 'general.scripts_enabled', true );

		if ( $scripts_on && class_exists( 'Themezur_WooCommerce' ) && Themezur_WooCommerce::should_enqueue_woocommerce_js() ) {
			wp_enqueue_script(
				'themezur-woocommerce',
				THEMEZUR_URI . '/assets/js/woocommerce.js',
				array(),
				THEMEZUR_VERSION,
				array(
					'in_footer' => true,
					'strategy'  => 'defer',
				)
			);
			if ( ! wp_script_is( 'themezur-header', 'enqueued' ) ) {
				wp_localize_script(
					'themezur-woocommerce',
					'themezurFront',
					array(
						'ajaxUrl'           => admin_url( 'admin-ajax.php' ),
						'nonce'             => wp_create_nonce( 'themezur_front' ),
						'miniCartOpenOnAdd' => (bool) Themezur_Options::get( 'woocommerce.cart.open_on_add', true ),
					)
				);
			} else {
				wp_add_inline_script(
					'themezur-woocommerce',
					'if(window.themezurFront){themezurFront.miniCartOpenOnAdd=' . ( Themezur_Options::get( 'woocommerce.cart.open_on_add', true ) ? 'true' : 'false' ) . ';}',
					'before'
				);
			}
		}

		if ( $scripts_on && class_exists( 'Themezur_WooCommerce' ) && Themezur_WooCommerce::quick_view_enabled() ) {
			wp_enqueue_script(
				'themezur-quick-view',
				THEMEZUR_URI . '/assets/js/quick-view.js',
				array(),
				THEMEZUR_VERSION,
				array(
					'in_footer' => true,
					'strategy'  => 'defer',
				)
			);
			// Share ajaxUrl + nonce with quick-view (header.js may not be loaded if header mode = elementor).
			if ( ! wp_script_is( 'themezur-header', 'enqueued' ) && ! wp_script_is( 'themezur-woocommerce', 'enqueued' ) ) {
				wp_localize_script(
					'themezur-quick-view',
					'themezurFront',
					array(
						'ajaxUrl'            => admin_url( 'admin-ajax.php' ),
						'nonce'              => wp_create_nonce( 'themezur_front' ),
						'i18nError'          => __( 'Error loading product.', 'themezur' ),
						'i18nAddWishlist'    => __( 'Add to Wishlist', 'themezur' ),
						'i18nRemoveWishlist' => __( 'Remove from Wishlist', 'themezur' ),
						'miniCartOpenOnAdd'  => (bool) Themezur_Options::get( 'woocommerce.cart.open_on_add', true ),
					)
				);
			} else {
				wp_add_inline_script(
					'themezur-quick-view',
					'if(window.themezurFront){themezurFront.i18nError="' . esc_js( __( 'Error loading product.', 'themezur' ) ) . '";}',
					'before'
				);
			}
		}

		if ( $scripts_on && class_exists( 'Themezur_WooCommerce' ) && Themezur_WooCommerce::wishlist_enabled() ) {
			wp_enqueue_script(
				'themezur-wishlist',
				THEMEZUR_URI . '/assets/js/wishlist.js',
				array(),
				THEMEZUR_VERSION,
				array(
					'in_footer' => true,
					'strategy'  => 'defer',
				)
			);
			// wishlist.js uses window.themezurFront — ensure it's available.
			if ( ! wp_script_is( 'themezur-header', 'enqueued' ) && ! wp_script_is( 'themezur-quick-view', 'enqueued' ) && ! wp_script_is( 'themezur-woocommerce', 'enqueued' ) ) {
				wp_localize_script(
					'themezur-wishlist',
					'themezurFront',
					array(
						'ajaxUrl'            => admin_url( 'admin-ajax.php' ),
						'nonce'              => wp_create_nonce( 'themezur_front' ),
						'i18nAddWishlist'    => __( 'Add to Wishlist', 'themezur' ),
						'i18nRemoveWishlist' => __( 'Remove from Wishlist', 'themezur' ),
					)
				);
			}
		}
	}

	// Instant Page Preloader (Hover Speculation Rules API).
	if ( Themezur_Options::get( 'general.scripts_enabled', true ) && Themezur_Options::get( 'performance.instant_page_preloader', true ) ) {
		wp_enqueue_script(
			'themezur-instant-page',
			THEMEZUR_URI . '/assets/js/instant-page.js',
			array(),
			THEMEZUR_VERSION,
			array(
				'in_footer' => true,
				'strategy'  => 'defer',
			)
		);
	}
}
add_action( 'wp_enqueue_scripts', 'themezur_enqueue_scripts_styles', 20 );
