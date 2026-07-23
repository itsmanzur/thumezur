<?php
/**
 * Search results.
 *
 * @package Themezur
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$is_elementor = function_exists( 'elementor_theme_do_location' );
if ( $is_elementor && elementor_theme_do_location( 'archive' ) ) {
	get_footer();
	return;
}

if ( 'theme' === Themezur_Options::get( 'pages.mode', 'theme' ) ) {
	get_template_part( 'template-parts/pages/search' );
} else {
	require get_template_directory() . '/template-parts/search.php';
}

get_footer();
