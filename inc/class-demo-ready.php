<?php
/**
 * Demo-readiness: textdomain, general widget areas, One Click Demo Import compat.
 *
 * @package Themezur
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Themezur_Demo_Ready
 */
class Themezur_Demo_Ready {

	/**
	 * @return void
	 */
	public static function init() {
		add_action( 'after_setup_theme', array( __CLASS__, 'load_textdomain' ) );
		add_action( 'widgets_init', array( __CLASS__, 'register_sidebars' ) );
		add_filter( 'ocdi/import_files', array( __CLASS__, 'ocdi_import_files' ) );
		add_action( 'ocdi/after_import', array( __CLASS__, 'ocdi_after_import' ) );
	}

	/**
	 * @return void
	 */
	public static function load_textdomain() {
		load_theme_textdomain( 'themezur', get_stylesheet_directory() . '/languages' );
	}

	/**
	 * General-purpose widget areas. Placeable anywhere via Elementor Pro's Sidebar widget —
	 * so any plugin that ships a WP_Widget can be demoed without template edits.
	 *
	 * @return void
	 */
	public static function register_sidebars() {
		register_sidebar(
			array(
				'name'          => __( 'Themezur Widget Area 1', 'themezur' ),
				'id'            => 'themezur-widget-1',
				'description'   => __( 'General-purpose widget area. Drop it anywhere via Elementor Pro\'s Sidebar widget.', 'themezur' ),
				'before_widget' => '<div id="%1$s" class="widget tz-widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h3 class="widget-title tz-widget__title">',
				'after_title'   => '</h3>',
			)
		);

		register_sidebar(
			array(
				'name'          => __( 'Themezur Widget Area 2', 'themezur' ),
				'id'            => 'themezur-widget-2',
				'description'   => __( 'Second general-purpose widget area for demo content.', 'themezur' ),
				'before_widget' => '<div id="%1$s" class="widget tz-widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h3 class="widget-title tz-widget__title">',
				'after_title'   => '</h3>',
			)
		);
	}

	/**
	 * One Click Demo Import file list. Fill in real URLs once a demo package
	 * (content.xml / widgets.wie / customizer.dat / preview.jpg) is built and
	 * placed under /demo.
	 *
	 * @return array[]
	 */
	public static function ocdi_import_files() {
		return array(
			array(
				'import_file_name'           => 'Themezur Demo',
				'categories'                 => array( 'Themezur' ),
				'import_file_url'            => THEMEZUR_URI . '/demo/content.xml',
				'import_widget_file_url'     => THEMEZUR_URI . '/demo/widgets.wie',
				'import_customizer_file_url' => THEMEZUR_URI . '/demo/customizer.dat',
				'import_preview_image_url'   => THEMEZUR_URI . '/demo/preview.jpg',
				'preview_url'                => 'https://themezur.com/demo',
			),
		);
	}

	/**
	 * Post-import cleanup: static front page, blog page, main menu, rewrite rules.
	 *
	 * @param string $selected_import Slug of the selected demo import.
	 * @return void
	 */
	public static function ocdi_after_import( $selected_import ) {
		$front_page = get_page_by_title( 'Home' );
		$blog_page  = get_page_by_title( 'Blog' );

		if ( $front_page ) {
			update_option( 'show_on_front', 'page' );
			update_option( 'page_on_front', $front_page->ID );
		}
		if ( $blog_page ) {
			update_option( 'page_for_posts', $blog_page->ID );
		}

		$main_menu = get_term_by( 'name', 'Main Menu', 'nav_menu' );
		if ( $main_menu ) {
			set_theme_mod(
				'nav_menu_locations',
				array(
					'menu-1' => $main_menu->term_id,
				)
			);
		}

		flush_rewrite_rules();
	}
}
