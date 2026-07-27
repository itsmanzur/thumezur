<?php
/**
 * Performance asset audit (from current Themezur options).
 *
 * @package Themezur
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Themezur_Performance
 */
class Themezur_Performance {

	/**
	 * Expected frontend assets based on saved options (not a live browser scan).
	 *
	 * @param array|null $options Options snapshot.
	 * @return array[]
	 */
	public static function audit( $options = null ) {
		if ( null === $options ) {
			$options = Themezur_Options::get_all();
		}

		$header_mode = isset( $options['header']['mode'] ) ? $options['header']['mode'] : 'theme';
		$footer_mode = isset( $options['footer']['mode'] ) ? $options['footer']['mode'] : 'theme';
		$blog_mode   = isset( $options['blog']['mode'] ) ? $options['blog']['mode'] : 'theme';
		$pages_mode  = isset( $options['pages']['mode'] ) ? $options['pages']['mode'] : 'theme';
		$woo_mode    = isset( $options['woocommerce']['mode'] ) ? $options['woocommerce']['mode'] : 'theme';
		$scripts_on  = ! empty( $options['general']['scripts_enabled'] );
		$btt_on      = ! empty( $options['footer']['back_to_top']['enabled'] );
		$perf        = isset( $options['performance'] ) && is_array( $options['performance'] ) ? $options['performance'] : array();

		$body_id = isset( $options['general']['font_body_id'] ) ? $options['general']['font_body_id'] : 'system';
		$head_id = isset( $options['general']['font_heading_id'] ) ? $options['general']['font_heading_id'] : 'system';
		$needs_gfont = false;
		if ( class_exists( 'Themezur_Fonts' ) ) {
			foreach ( array( $body_id, $head_id ) as $fid ) {
				$font = Themezur_Fonts::get( $fid );
				if ( $font && ! empty( $font['google'] ) ) {
					$needs_gfont = true;
					break;
				}
			}
		}

		$rows = array(
			array(
				'handle'  => 'themezur-style',
				'type'    => 'css',
				'when'    => __( 'Always', 'themezur' ),
				'status'  => 'on',
				'note'    => __( 'Child theme style.css', 'themezur' ),
			),
			array(
				'handle'  => 'themezur-design',
				'type'    => 'css',
				'when'    => __( 'Always', 'themezur' ),
				'status'  => 'on',
				'note'    => __( 'Design tokens + utilities', 'themezur' ),
			),
			array(
				'handle'  => 'themezur-google-fonts',
				'type'    => 'css',
				'when'    => __( 'When a Google font is selected', 'themezur' ),
				'status'  => $needs_gfont ? 'on' : 'off',
				'note'    => __( 'fonts.googleapis.com', 'themezur' ),
			),
			array(
				'handle'  => 'themezur-header',
				'type'    => 'css',
				'when'    => __( 'Header = Themezur', 'themezur' ),
				'status'  => ( 'theme' === $header_mode ) ? 'on' : 'off',
				'note'    => 'header.css',
			),
			array(
				'handle'  => 'themezur-header',
				'type'    => 'js',
				'when'    => __( 'Header = Themezur + scripts enabled', 'themezur' ),
				'status'  => ( 'theme' === $header_mode && $scripts_on ) ? 'on' : 'off',
				'note'    => 'header.js',
			),
			array(
				'handle'  => 'themezur-footer',
				'type'    => 'css',
				'when'    => __( 'Footer = Themezur (or BTT on)', 'themezur' ),
				'status'  => ( 'theme' === $footer_mode || $btt_on ) ? 'on' : 'off',
				'note'    => 'footer.css',
			),
			array(
				'handle'  => 'themezur-footer',
				'type'    => 'js',
				'when'    => __( 'Back to top + scripts enabled', 'themezur' ),
				'status'  => ( $btt_on && $scripts_on ) ? 'on' : 'off',
				'note'    => 'footer.js',
			),
			array(
				'handle'  => 'themezur-blog',
				'type'    => 'css',
				'when'    => __( 'Blog pages when Blog = Themezur', 'themezur' ),
				'status'  => ( 'theme' === $blog_mode ) ? 'cond' : 'off',
				'note'    => 'blog.css',
			),
			array(
				'handle'  => 'themezur-pages',
				'type'    => 'css',
				'when'    => __( '404 / Search when Pages = Themezur', 'themezur' ),
				'status'  => ( 'theme' === $pages_mode ) ? 'cond' : 'off',
				'note'    => 'pages.css',
			),
			array(
				'handle'  => 'themezur-woocommerce',
				'type'    => 'css',
				'when'    => __( 'Shop pages when Woo polish = Themezur', 'themezur' ),
				'status'  => ( 'theme' === $woo_mode && class_exists( 'WooCommerce' ) ) ? 'cond' : 'off',
				'note'    => class_exists( 'WooCommerce' ) ? 'woocommerce.css' : __( 'WooCommerce inactive', 'themezur' ),
			),
			array(
				'handle'  => 'themezur-woocommerce',
				'type'    => 'js',
				'when'    => __( 'Sticky ATC / filters / mini-cart', 'themezur' ),
				'status'  => (
					'theme' === $woo_mode
					&& class_exists( 'WooCommerce' )
					&& ! empty( $scripts_on )
					&& (
						! empty( $options['woocommerce']['single']['sticky_atc'] )
						|| (
							isset( $options['woocommerce']['shop']['sidebar'] )
							&& in_array( $options['woocommerce']['shop']['sidebar'], array( 'left', 'right' ), true )
						)
						|| ! empty( $options['woocommerce']['cart']['mini_cart'] )
					)
				) ? 'cond' : 'off',
				'note'    => 'woocommerce.js',
			),
			array(
				'handle'  => 'themezur-mobile',
				'type'    => 'css',
				'when'    => __( 'Always', 'themezur' ),
				'status'  => 'on',
				'note'    => __( 'Mobile polish layer', 'themezur' ),
			),
			array(
				'handle'  => 'hello-elementor (reset)',
				'type'    => 'css',
				'when'    => __( 'Parent theme', 'themezur' ),
				'status'  => empty( $perf['disable_hello_reset'] ) ? 'on' : 'off',
				'note'    => 'reset.css',
			),
			array(
				'handle'  => 'hello-elementor (theme)',
				'type'    => 'css',
				'when'    => __( 'Parent theme', 'themezur' ),
				'status'  => empty( $perf['disable_hello_theme_style'] ) ? 'on' : 'off',
				'note'    => 'theme.css',
			),
			array(
				'handle'  => 'hello-elementor-header-footer',
				'type'    => 'css',
				'when'    => __( 'Parent header/footer CSS', 'themezur' ),
				'status'  => empty( $perf['disable_hello_header_footer_css'] ) ? 'on' : 'off',
				'note'    => 'header-footer.css',
			),
			array(
				'handle'  => 'wp-emoji',
				'type'    => 'js',
				'when'    => __( 'WordPress core', 'themezur' ),
				'status'  => empty( $perf['disable_emoji'] ) ? 'on' : 'off',
				'note'    => __( 'Emoji detection', 'themezur' ),
			),
			array(
				'handle'  => 'wp-embed',
				'type'    => 'js',
				'when'    => __( 'WordPress core', 'themezur' ),
				'status'  => empty( $perf['disable_wp_embed'] ) ? 'on' : 'off',
				'note'    => __( 'oEmbed script', 'themezur' ),
			),
		);

		/**
		 * Filter performance audit rows.
		 *
		 * @param array[] $rows    Rows.
		 * @param array   $options Options.
		 */
		return apply_filters( 'themezur_performance_audit', $rows, $options );
	}
}
