<?php
/**
 * Blog posts index (Posts page / home).
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

if ( Themezur_Blog::is_enabled() ) {
	get_template_part( 'template-parts/blog/archive' );
} else {
	require get_template_directory() . '/template-parts/archive.php';
}

get_footer();
