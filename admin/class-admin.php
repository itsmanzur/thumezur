<?php
/**
 * Themezur admin panel.
 *
 * @package Themezur
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Themezur_Admin
 */
class Themezur_Admin {

	/**
	 * Hook admin menu and assets.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'register_menu' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue' ) );
	}

	/**
	 * Register top-level admin menu.
	 *
	 * @return void
	 */
	public static function register_menu() {
		add_menu_page(
			__( 'Themezur', 'themezur' ),
			__( 'Themezur', 'themezur' ),
			'manage_options',
			'themezur',
			array( __CLASS__, 'render_page' ),
			'dashicons-admin-customizer',
			59
		);

		add_submenu_page(
			'themezur',
			__( 'Settings', 'themezur' ),
			__( 'Settings', 'themezur' ),
			'manage_options',
			'themezur',
			array( __CLASS__, 'render_page' )
		);

		add_submenu_page(
			'themezur',
			__( 'Help / Docs', 'themezur' ),
			__( 'Help / Docs', 'themezur' ),
			'manage_options',
			'themezur-help',
			array( __CLASS__, 'render_help_page' )
		);
	}

	/**
	 * Enqueue assets only on Themezur admin page.
	 *
	 * @param string $hook Current admin hook.
	 * @return void
	 */
	public static function enqueue( $hook ) {
		$allowed = array( 'toplevel_page_themezur', 'themezur_page_themezur-help' );
		if ( ! in_array( $hook, $allowed, true ) ) {
			return;
		}

		$uri = THEMEZUR_URI . '/admin/assets';
		$ver = THEMEZUR_VERSION;

		wp_enqueue_media();

		wp_enqueue_style(
			'themezur-admin-font',
			'https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap',
			array(),
			null
		);

		wp_enqueue_style(
			'themezur-admin',
			$uri . '/css/admin.css',
			array( 'themezur-admin-font' ),
			$ver
		);

		// jQuery + media; register alpine:init before Alpine boots.
		wp_enqueue_script(
			'themezur-admin',
			$uri . '/js/admin.js',
			array( 'jquery' ),
			$ver,
			true
		);

		wp_enqueue_script(
			'themezur-alpine',
			$uri . '/js/alpine.min.js',
			array( 'themezur-admin' ),
			'3.14.8',
			array(
				'in_footer' => true,
				'strategy'  => 'defer',
			)
		);

		$initial_tab = 'dashboard';
		if ( ! empty( $GLOBALS['themezur_initial_tab'] ) ) {
			$initial_tab = sanitize_key( $GLOBALS['themezur_initial_tab'] );
		} elseif ( isset( $_GET['page'] ) && 'themezur-help' === $_GET['page'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$initial_tab = 'help';
		}

		$networks = array();
		foreach ( Themezur_Social::networks() as $value => $label ) {
			$networks[] = array(
				'value' => $value,
				'label' => $label,
			);
		}

		$options = Themezur_Options::get_all_for_admin();
		if ( empty( $options['header']['top']['socials'] ) || ! is_array( $options['header']['top']['socials'] ) ) {
			$options['header']['top']['socials'] = array();
		}

		$menus = array();
		foreach ( wp_get_nav_menus() as $menu ) {
			$menus[] = array(
				'id'   => (int) $menu->term_id,
				'name' => $menu->name,
			);
		}

		$categories = array();
		foreach ( get_categories( array( 'hide_empty' => false ) ) as $cat ) {
			$categories[] = array(
				'id'   => (int) $cat->term_id,
				'name' => $cat->name,
			);
		}

		wp_localize_script(
			'themezur-admin',
			'themezurAdmin',
			array(
				'ajaxUrl'         => admin_url( 'admin-ajax.php' ),
				'nonce'           => wp_create_nonce( 'themezur_admin' ),
				'options'         => $options,
				'elementor'       => Themezur_Elementor::is_active(),
				'woocommerce'     => class_exists( 'WooCommerce' ),
				'initialTab'      => $initial_tab,
				'socialNetworks'  => $networks,
				'menus'           => $menus,
				'categories'      => $categories,
				'fontCatalog'     => class_exists( 'Themezur_Fonts' ) ? Themezur_Fonts::catalog_for_js() : array(),
				'i18n'            => array(
					'saved'         => __( 'Settings saved.', 'themezur' ),
					'error'         => __( 'Something went wrong. Please try again.', 'themezur' ),
					'saving'        => __( 'Saving…', 'themezur' ),
					'loading'       => __( 'Loading templates…', 'themezur' ),
					'noTpl'         => __( 'No Elementor templates found.', 'themezur' ),
					'logoTitle'     => __( 'Select logo', 'themezur' ),
					'logoButton'    => __( 'Use this logo', 'themezur' ),
					'presetApplied' => __( 'Preset applied — Save to publish', 'themezur' ),
				),
			)
		);
	}

	/**
	 * Render admin panel view.
	 *
	 * @return void
	 */
	public static function render_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		require THEMEZUR_DIR . '/admin/views/panel.php';
	}

	/**
	 * Render Help / Docs (opens the same panel focused on Help).
	 *
	 * @return void
	 */
	public static function render_help_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$GLOBALS['themezur_initial_tab'] = 'help';
		require THEMEZUR_DIR . '/admin/views/panel.php';
	}
}
