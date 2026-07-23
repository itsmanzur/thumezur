<?php
/**
 * 404 page.
 *
 * @package Themezur
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$is_elementor = function_exists( 'elementor_theme_do_location' );
if ( $is_elementor && elementor_theme_do_location( 'single' ) ) {
	get_footer();
	return;
}

if ( 'theme' === Themezur_Options::get( 'pages.mode', 'theme' ) ) {
	get_template_part( 'template-parts/pages/404' );
} else {
	require get_template_directory() . '/template-parts/404.php';
}

get_footer();
