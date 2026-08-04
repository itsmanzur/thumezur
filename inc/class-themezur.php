<?php
/**
 * Themezur bootstrap singleton.
 *
 * @package Themezur
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Themezur
 */
final class Themezur {

	/**
	 * @var Themezur|null
	 */
	private static $instance = null;

	/**
	 * Get instance.
	 *
	 * @return Themezur
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		$this->includes();
		$this->boot();
	}

	/**
	 * Load required files.
	 *
	 * @return void
	 */
	private function includes() {
		require_once THEMEZUR_DIR . '/inc/class-social.php';
		require_once THEMEZUR_DIR . '/inc/class-categories.php';
		require_once THEMEZUR_DIR . '/inc/class-options.php';
		require_once THEMEZUR_DIR . '/inc/class-elementor.php';
		require_once THEMEZUR_DIR . '/inc/class-footer.php';
		require_once THEMEZUR_DIR . '/inc/class-blog.php';
		require_once THEMEZUR_DIR . '/inc/class-fonts.php';
		require_once THEMEZUR_DIR . '/inc/class-breadcrumbs.php';
		require_once THEMEZUR_DIR . '/inc/class-performance.php';
		require_once THEMEZUR_DIR . '/inc/class-frontend.php';
		require_once THEMEZUR_DIR . '/inc/class-woocommerce.php';
		require_once THEMEZUR_DIR . '/inc/class-wishlist.php';
		require_once THEMEZUR_DIR . '/inc/class-ajax.php';
		require_once THEMEZUR_DIR . '/inc/class-megamenu.php';
		require_once THEMEZUR_DIR . '/inc/class-megamenu-walker.php';

		if ( is_admin() ) {
			require_once THEMEZUR_DIR . '/admin/class-admin.php';
		}
	}

	/**
	 * Initialize modules.
	 *
	 * @return void
	 */
	private function boot() {
		Themezur_Elementor::init();
		Themezur_Frontend::init();
		Themezur_Breadcrumbs::init();
		Themezur_WooCommerce::init();
		Themezur_Wishlist::init();
		Themezur_Ajax::init();

		if ( is_admin() ) {
			Themezur_Admin::init();
		}
	}
}
