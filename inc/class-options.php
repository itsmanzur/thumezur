<?php
/**
 * Themezur options store — single autoloaded option.
 *
 * @package Themezur
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Themezur_Options
 */
class Themezur_Options {

	const OPTION_KEY = 'themezur_options';

	/**
	 * Cached options for the request.
	 *
	 * @var array|null
	 */
	private static $cache = null;

	/**
	 * Default settings.
	 *
	 * @return array
	 */
	public static function defaults() {
		return array(
			'general'     => array(
				'primary_color'   => '#0f172a',
				'accent_color'    => '#2563eb',
				'text_color'      => '#0f172a',
				'muted_color'     => '#64748b',
				'bg_color'        => '#ffffff',
				'surface_color'   => '#f8fafc',
				'border_color'    => '#e2e8f0',
				'link_color'      => '#2563eb',
				'link_hover'      => '#1d4ed8',
				'font_body_id'      => 'system',
				'font_heading_id'   => 'system',
				'font_body'         => 'system-ui, -apple-system, "Segoe UI", Roboto, "Noto Sans Bengali", sans-serif',
				'font_heading'      => 'system-ui, -apple-system, "Segoe UI", Roboto, "Noto Sans Bengali", sans-serif',
				'font_body_weights'    => array( '400' ),
				'font_heading_weights' => array( '700' ),
				'font_size'       => '16px',
				'line_height'     => '1.65',
				'radius'          => '8px',
				'container_width' => '1200px',
				'button_bg'       => '#2563eb',
				'button_text'     => '#ffffff',
				'button_radius'   => '8px',
				'breadcrumbs'     => true,
				'scripts_enabled' => true,
				'logo_id'         => 0,
				'show_tagline'    => false,
			),
			'header'      => array(
				'mode'        => 'theme',
				'template_id' => 0,
				'layout'      => 'triple',
				'sticky'      => false,
				'scroll'      => array(
					'behavior' => 'none',
					'offset'   => 40,
					'progress' => false,
				),
				'announce'    => array(
					'enabled'     => false,
					'text'        => '',
					'link_url'    => '',
					'link_text'   => 'Learn more',
					'bg'          => '#2563eb',
					'text_color'  => '#ffffff',
					'dismissible' => true,
					'cookie_days' => 7,
					'version'     => '1',
				),
				'typo'        => array(
					'font_id'     => 'inherit',
					'font_family' => 'system-ui, -apple-system, "Segoe UI", Roboto, "Noto Sans Bengali", sans-serif',
					'top_size'    => '13px',
					'top_weight'  => '500',
					'mid_size'    => '14px',
					'mid_weight'  => '400',
					'nav_size'    => '15px',
					'nav_weight'  => '500',
					'logo_max_h'  => '44px',
				),
				'spacing'     => array(
					'container_width' => '1200px',
					'side_padding'    => '1.15rem',
					'top_min_h'       => '2.25rem',
					'mid_min_h'       => '4.5rem',
					'bot_min_h'       => '3rem',
				),
				'top'         => array(
					'enabled'               => true,
					'bg'                    => '#111827',
					'text'                  => '#f3f4f6',
					'muted'                 => '#9ca3af',
					'accent'                => '#f59e0b',
					'show_trending'         => true,
					'trending_label'        => 'TRENDING',
					'trending_text'         => '',
					'promos'                => array(),
					'promo_rotate'          => false,
					'promo_interval'        => 4,
					'hide_mobile_trending'  => false,
					'hide_desktop_trending' => false,
					'show_social'           => true,
					'socials'               => array(),
					'hide_mobile_social'    => false,
					'hide_desktop_social'   => false,
					'show_phone'            => false,
					'phone_label'           => 'Hotline',
					'phone_number'          => '',
					'hide_mobile_phone'     => false,
					'hide_desktop_phone'    => false,
					'show_date'             => true,
					'date_mode'             => 'date',
					'date_format'           => 'j M Y',
					'date_custom'           => '',
					'hide_mobile_date'      => false,
					'hide_desktop_date'     => false,
				),
				'middle'      => array(
					'enabled'               => true,
					'bg'                    => '#ffffff',
					'text'                  => '#0f172a',
					'muted'                 => '#64748b',
					'accent'                => '#2563eb',
					'border'                => '#e2e8f0',
					'show_logo'             => true,
					'hide_mobile_logo'      => false,
					'hide_desktop_logo'     => false,
					'show_address'          => true,
					'address'               => '',
					'hide_mobile_address'   => false,
					'hide_desktop_address'  => false,
					'show_search'           => true,
					'search_placeholder'    => 'Search…',
					'smart_search'          => true,
					'search_min_chars'      => 2,
					'search_limit'          => 8,
					'hide_mobile_search'    => false,
					'hide_desktop_search'   => false,
					'show_dark_mode'        => true,
					'hide_mobile_dark'      => false,
					'hide_desktop_dark'     => false,
					'show_account'          => true,
					'hide_mobile_account'   => false,
					'hide_desktop_account'  => false,
					'show_wishlist'         => false,
					'wishlist_url'          => '',
					'hide_mobile_wishlist'  => false,
					'hide_desktop_wishlist' => false,
					'show_compare'          => false,
					'compare_url'           => '',
					'hide_mobile_compare'   => false,
					'hide_desktop_compare'  => false,
					'show_cart'             => true,
					'mini_cart'             => true,
					'hide_mobile_cart'      => false,
					'hide_desktop_cart'     => false,
					'show_menu'             => false,
					'menu_id'               => 0,
					'menu_position'         => 'inline',
					'hide_mobile_menu'      => false,
					'hide_desktop_menu'     => false,
					'show_button'           => false,
					'button_text'           => 'Order Now',
					'button_url'            => '',
					'button_target'         => '_self',
					'hide_mobile_button'    => false,
					'hide_desktop_button'   => false,
				),
				'bottom'      => array(
					'enabled'                => true,
					'bg'                     => '#111827',
					'text'                   => '#ffffff',
					'muted'                  => '#cbd5e1',
					'accent'                 => '#f59e0b',
					'accent_text'            => '#111827',
					'show_categories'        => true,
					'categories_text'        => 'Categories',
					'categories_url'         => '',
					'categories_mode'        => 'mega',
					'categories_columns'     => 3,
					'categories_limit'       => 18,
					'hide_mobile_categories' => false,
					'hide_desktop_categories' => false,
					'show_menu'              => true,
					'menu_id'                => 0,
					'hide_mobile_menu'       => false,
					'hide_desktop_menu'      => false,
					'show_deal'              => true,
					'deal_text'              => "Today's Deal",
					'deal_url'               => '',
					'hide_mobile_deal'       => false,
					'hide_desktop_deal'      => false,
				),
				'mobile_nav'  => array(
					'enabled'      => true,
					'show_home'    => true,
					'show_cats'    => true,
					'show_search'  => true,
					'show_cart'    => true,
					'show_account' => true,
					'bg'           => '#ffffff',
					'text'         => '#0f172a',
					'accent'       => '#2563eb',
				),
			),
			'footer'      => array(
				'mode'        => 'theme',
				'template_id' => 0,
				'layout'      => 'columns4',
				'bg'          => '#0f172a',
				'text'        => '#e2e8f0',
				'muted'       => '#94a3b8',
				'accent'      => '#f59e0b',
				'border'      => '#1e293b',
				'container_width' => '1200px',
				'side_padding'    => '1.15rem',
				'columns'     => array(
					'1' => array(
						'enabled'     => true,
						'title'       => 'About',
						'subtitle'    => '',
						'type'        => 'about',
						'text'        => '',
						'logo_source' => 'site',
						'logo_id'     => 0,
						'show_logo'   => true,
						'show_social' => true,
						'cta_label'   => '',
						'cta_url'     => '',
						'menu_id'     => 0,
						'links'       => array(),
						'address'     => '',
						'phone'       => '',
						'email'       => '',
						'whatsapp'    => '',
						'hours'       => '',
						'map_url'     => '',
						'posts_count'            => 3,
						'posts_show_thumb'       => true,
						'posts_show_date'        => true,
						'posts_category'         => 0,
						'products_count'         => 3,
						'products_source'        => 'recent',
						'products_show_thumb'    => true,
						'products_show_price'    => true,
						'shortcode'              => '',
						'newsletter_mode'        => 'form',
						'newsletter_text'        => '',
						'newsletter_placeholder' => 'Your email',
						'newsletter_button'      => 'Subscribe',
						'newsletter_action'      => '',
						'newsletter_email_name'  => 'EMAIL',
						'newsletter_shortcode'   => '',
					),
					'2' => array(
						'enabled'     => true,
						'title'       => 'Quick Links',
						'subtitle'    => '',
						'type'        => 'menu',
						'text'        => '',
						'logo_source' => 'none',
						'logo_id'     => 0,
						'show_logo'   => false,
						'show_social' => false,
						'cta_label'   => '',
						'cta_url'     => '',
						'menu_id'     => 0,
						'links'       => array(),
						'address'     => '',
						'phone'       => '',
						'email'       => '',
						'whatsapp'    => '',
						'hours'       => '',
						'map_url'     => '',
						'posts_count'            => 3,
						'posts_show_thumb'       => true,
						'posts_show_date'        => true,
						'posts_category'         => 0,
						'products_count'         => 3,
						'products_source'        => 'recent',
						'products_show_thumb'    => true,
						'products_show_price'    => true,
						'shortcode'              => '',
						'newsletter_mode'        => 'form',
						'newsletter_text'        => '',
						'newsletter_placeholder' => 'Your email',
						'newsletter_button'      => 'Subscribe',
						'newsletter_action'      => '',
						'newsletter_email_name'  => 'EMAIL',
						'newsletter_shortcode'   => '',
					),
					'3' => array(
						'enabled'     => true,
						'title'       => 'Customer Care',
						'subtitle'    => '',
						'type'        => 'links',
						'text'        => '',
						'logo_source' => 'none',
						'logo_id'     => 0,
						'show_logo'   => false,
						'show_social' => false,
						'cta_label'   => '',
						'cta_url'     => '',
						'menu_id'     => 0,
						'links'       => array(
							array(
								'label' => 'Shipping Info',
								'url'   => '',
							),
							array(
								'label' => 'Returns',
								'url'   => '',
							),
							array(
								'label' => 'FAQ',
								'url'   => '',
							),
						),
						'address'     => '',
						'phone'       => '',
						'email'       => '',
						'whatsapp'    => '',
						'hours'       => '',
						'map_url'     => '',
						'posts_count'            => 3,
						'posts_show_thumb'       => true,
						'posts_show_date'        => true,
						'posts_category'         => 0,
						'products_count'         => 3,
						'products_source'        => 'recent',
						'products_show_thumb'    => true,
						'products_show_price'    => true,
						'shortcode'              => '',
						'newsletter_mode'        => 'form',
						'newsletter_text'        => '',
						'newsletter_placeholder' => 'Your email',
						'newsletter_button'      => 'Subscribe',
						'newsletter_action'      => '',
						'newsletter_email_name'  => 'EMAIL',
						'newsletter_shortcode'   => '',
					),
					'4' => array(
						'enabled'     => true,
						'title'       => 'Contact',
						'subtitle'    => '',
						'type'        => 'contact',
						'text'        => '',
						'logo_source' => 'none',
						'logo_id'     => 0,
						'show_logo'   => false,
						'show_social' => false,
						'cta_label'   => '',
						'cta_url'     => '',
						'menu_id'     => 0,
						'links'       => array(),
						'address'     => '',
						'phone'       => '',
						'email'       => '',
						'whatsapp'    => '',
						'hours'       => '',
						'map_url'     => '',
						'posts_count'            => 3,
						'posts_show_thumb'       => true,
						'posts_show_date'        => true,
						'posts_category'         => 0,
						'products_count'         => 3,
						'products_source'        => 'recent',
						'products_show_thumb'    => true,
						'products_show_price'    => true,
						'shortcode'              => '',
						'newsletter_mode'        => 'form',
						'newsletter_text'        => '',
						'newsletter_placeholder' => 'Your email',
						'newsletter_button'      => 'Subscribe',
						'newsletter_action'      => '',
						'newsletter_email_name'  => 'EMAIL',
						'newsletter_shortcode'   => '',
					),
				),
				'bottom'      => array(
					'enabled'       => true,
					'copyright'     => '',
					'show_menu'     => true,
					'menu_id'       => 0,
					'show_payments' => true,
					'payments'      => array( 'visa', 'mastercard', 'amex', 'paypal', 'applepay', 'bkash', 'nagad' ),
				),
				'back_to_top' => array(
					'enabled'   => true,
					'threshold' => 400,
				),
			),
			'blog'        => array(
				'mode'             => 'theme',
				'container_width'  => '1100px',
				'archive'          => array(
					'layout'            => 'grid',
					'columns'           => 3,
					'card_style'        => 'soft',
					'featured_hero'     => true,
					'show_title'        => true,
					'show_description'  => true,
					'show_image'        => true,
					'show_excerpt'      => true,
					'excerpt_length'    => 22,
					'show_date'         => true,
					'show_author'       => false,
					'show_category'     => true,
					'show_reading_time' => true,
					'show_badge'        => true,
					'show_read_more'    => true,
					'read_more_text'    => 'Read more',
				),
				'single'           => array(
					'layout'            => 'standard',
					'content_width'     => '720px',
					'show_progress_bar' => true,
					'show_toc'          => true,
					'show_social_share' => true,
					'show_reading_time' => true,
					'show_image'        => true,
					'show_date'         => true,
					'show_author'       => true,
					'show_category'     => true,
					'show_tags'         => true,
					'show_author_box'   => true,
					'show_nav'          => true,
					'show_related'      => true,
					'related_count'     => 3,
					'show_comments'     => true,
				),
			),
			'pages'       => array(
				'mode'      => 'theme',
				'not_found' => array(
					'title'            => 'Page not found',
					'text'             => 'The page you are looking for may have been moved or no longer exists.',
					'show_search'      => true,
					'show_home_btn'    => true,
					'home_label'       => 'Back to home',
					'show_quick_links' => true,
				),
				'search'    => array(
					'layout'               => 'list',
					'show_image'           => true,
					'show_excerpt'         => true,
					'show_type'            => true,
					'show_product_details' => true,
					'show_tabs'            => true,
				),
			),
			'woocommerce' => array(
				'mode'   => 'theme',
				'shop'   => array(
					'columns'            => 3,
					'products_per_page'  => 12,
					'card_style'         => 'soft',
					'show_result_count'  => true,
					'show_ordering'      => true,
					'quick_view'         => true,
					'wishlist'           => true,
				),
				'single' => array(
					'related_count'      => 4,
					'upsells_count'      => 4,
					'sticky_cart'        => true,
					'quantity_stepper'   => true,
				),
			),
			'assignments' => array(
				'scope'           => 'entire_site',
				'exclude_ids'     => array(),
				'include'         => array(
					'front_page' => true,
					'shop'       => true,
					'blog'       => true,
					'other'      => false,
				),
				'header_overrides' => array(
					'front_page' => array(
						'enabled'     => false,
						'mode'        => 'inherit',
						'template_id' => 0,
					),
					'shop'       => array(
						'enabled'     => false,
						'mode'        => 'inherit',
						'template_id' => 0,
					),
					'blog'       => array(
						'enabled'     => false,
						'mode'        => 'inherit',
						'template_id' => 0,
					),
				),
			),
			'performance' => array(
				'disable_hello_reset'             => false,
				'disable_hello_theme_style'       => false,
				'disable_hello_header_footer_css' => false,
				'disable_emoji'                   => false,
				'disable_wp_embed'                => false,
				'disable_gutenberg_css'           => false,
				'opt_cart_fragments'              => true,
				'remove_query_strings'            => false,
				'disable_jquery_migrate'          => false,
				'preload_google_fonts'            => true,
			),
		);
	}

	/**
	 * Get all options (merged with defaults).
	 *
	 * @return array
	 */
	public static function get_all() {
		if ( null !== self::$cache ) {
			return self::$cache;
		}

		$stored = get_option( self::OPTION_KEY, array() );
		if ( ! is_array( $stored ) ) {
			$stored = array();
		}

		// Migrate legacy fixed social URL fields into socials[].
		if ( isset( $stored['header']['top'] ) && is_array( $stored['header']['top'] ) ) {
			$top = $stored['header']['top'];
			if ( empty( $top['socials'] ) || ! is_array( $top['socials'] ) ) {
				$stored['header']['top']['socials'] = Themezur_Social::normalize_list( $top );
			}
		}

		self::$cache = self::deep_merge( self::defaults(), $stored );
		return self::$cache;
	}

	/**
	 * Get a section or nested value via dot path (e.g. header.top.bg).
	 *
	 * @param string $path    Dot path.
	 * @param mixed  $default Fallback.
	 * @return mixed
	 */
	public static function get( $path, $default = null ) {
		$options = self::get_all();
		$keys    = explode( '.', $path );
		$value   = $options;

		foreach ( $keys as $key ) {
			if ( ! is_array( $value ) || ! array_key_exists( $key, $value ) ) {
				return $default;
			}
			$value = $value[ $key ];
		}

		return $value;
	}

	/**
	 * Update options (full replace after sanitize).
	 *
	 * @param array $raw Raw input.
	 * @return array Sanitized saved options.
	 */
	public static function update( array $raw ) {
		$clean = self::sanitize( $raw );
		update_option( self::OPTION_KEY, $clean, true );
		self::$cache = $clean;
		return $clean;
	}

	/**
	 * Clear request cache.
	 *
	 * @return void
	 */
	public static function flush_cache() {
		self::$cache = null;
	}

	/**
	 * Recursively merge stored onto defaults (defaults win for unknown keys).
	 *
	 * @param array $defaults Defaults.
	 * @param array $stored   Stored.
	 * @return array
	 */
	private static function deep_merge( array $defaults, array $stored ) {
		$merged = $defaults;
		foreach ( $stored as $key => $value ) {
			if ( ! array_key_exists( $key, $defaults ) ) {
				continue;
			}
			if ( is_array( $defaults[ $key ] ) && self::is_assoc( $defaults[ $key ] ) && is_array( $value ) ) {
				$merged[ $key ] = self::deep_merge( $defaults[ $key ], $value );
			} else {
				$merged[ $key ] = $value;
			}
		}
		return $merged;
	}

	/**
	 * Whether array is associative.
	 *
	 * @param array $arr Array.
	 * @return bool
	 */
	private static function is_assoc( array $arr ) {
		if ( array() === $arr ) {
			return false;
		}
		return array_keys( $arr ) !== range( 0, count( $arr ) - 1 );
	}

	/**
	 * Sanitize full options payload.
	 *
	 * @param array $raw Raw input.
	 * @return array
	 */
	public static function sanitize( array $raw ) {
		$defaults = self::defaults();
		$merged   = self::deep_merge( $defaults, is_array( $raw ) ? $raw : array() );
		$clean    = $defaults;

		$modes = array( 'theme', 'elementor', 'none' );

		// General.
		$g = isset( $merged['general'] ) && is_array( $merged['general'] ) ? $merged['general'] : array();
		$clean['general']['primary_color']   = self::sanitize_hex_color_value( $g['primary_color'] ?? '', $defaults['general']['primary_color'] );
		$clean['general']['accent_color']    = self::sanitize_hex_color_value( $g['accent_color'] ?? '', $defaults['general']['accent_color'] );
		$clean['general']['text_color']      = self::sanitize_hex_color_value( $g['text_color'] ?? '', $defaults['general']['text_color'] );
		$clean['general']['muted_color']     = self::sanitize_hex_color_value( $g['muted_color'] ?? '', $defaults['general']['muted_color'] );
		$clean['general']['bg_color']        = self::sanitize_hex_color_value( $g['bg_color'] ?? '', $defaults['general']['bg_color'] );
		$clean['general']['surface_color']   = self::sanitize_hex_color_value( $g['surface_color'] ?? '', $defaults['general']['surface_color'] );
		$clean['general']['border_color']    = self::sanitize_hex_color_value( $g['border_color'] ?? '', $defaults['general']['border_color'] );
		$clean['general']['link_color']      = self::sanitize_hex_color_value( $g['link_color'] ?? '', $defaults['general']['link_color'] );
		$clean['general']['link_hover']      = self::sanitize_hex_color_value( $g['link_hover'] ?? '', $defaults['general']['link_hover'] );
		$clean['general']['button_bg']       = self::sanitize_hex_color_value( $g['button_bg'] ?? '', $defaults['general']['button_bg'] );
		$clean['general']['button_text']     = self::sanitize_hex_color_value( $g['button_text'] ?? '', $defaults['general']['button_text'] );
		$body_id = isset( $g['font_body_id'] ) ? sanitize_key( $g['font_body_id'] ) : $defaults['general']['font_body_id'];
		$head_id = isset( $g['font_heading_id'] ) ? sanitize_key( $g['font_heading_id'] ) : $defaults['general']['font_heading_id'];
		if ( ! Themezur_Fonts::get( $body_id ) ) {
			$body_id = 'system';
		}
		if ( ! Themezur_Fonts::get( $head_id ) ) {
			$head_id = 'system';
		}
		$clean['general']['font_body_id']    = $body_id;
		$clean['general']['font_heading_id'] = $head_id;
		$clean['general']['font_body']       = Themezur_Fonts::family( $body_id, $g['font_body'] ?? '' );
		$clean['general']['font_heading']    = Themezur_Fonts::family( $head_id, $g['font_heading'] ?? '' );
		if ( 'custom' === $body_id ) {
			$clean['general']['font_body'] = self::sanitize_font_stack( $g['font_body'] ?? '', $defaults['general']['font_body'] );
		}
		if ( 'custom' === $head_id ) {
			$clean['general']['font_heading'] = self::sanitize_font_stack( $g['font_heading'] ?? '', $defaults['general']['font_heading'] );
		}
		// Font weights — filter to valid numeric weight strings.
		$allowed_weights = array( '100', '200', '300', '400', '500', '600', '700', '800', '900' );
		$raw_bw = isset( $g['font_body_weights'] ) && is_array( $g['font_body_weights'] ) ? $g['font_body_weights'] : array( '400' );
		$raw_hw = isset( $g['font_heading_weights'] ) && is_array( $g['font_heading_weights'] ) ? $g['font_heading_weights'] : array( '700' );
		$clean_bw = array_values( array_intersect( array_map( 'strval', $raw_bw ), $allowed_weights ) );
		$clean_hw = array_values( array_intersect( array_map( 'strval', $raw_hw ), $allowed_weights ) );
		$clean['general']['font_body_weights']    = ! empty( $clean_bw ) ? $clean_bw : array( '400' );
		$clean['general']['font_heading_weights'] = ! empty( $clean_hw ) ? $clean_hw : array( '700' );
		$clean['general']['font_size']       = self::sanitize_css_size( $g['font_size'] ?? '', $defaults['general']['font_size'] );
		$clean['general']['line_height']     = self::sanitize_unitless_number( $g['line_height'] ?? '', $defaults['general']['line_height'] );
		$clean['general']['radius']          = self::sanitize_css_size( $g['radius'] ?? '', $defaults['general']['radius'] );
		$clean['general']['button_radius']   = self::sanitize_css_size( $g['button_radius'] ?? '', $defaults['general']['button_radius'] );
		$clean['general']['container_width'] = self::sanitize_css_size( $g['container_width'] ?? '', $defaults['general']['container_width'] );
		$clean['general']['breadcrumbs']     = ! empty( $g['breadcrumbs'] );
		$clean['general']['scripts_enabled'] = ! empty( $g['scripts_enabled'] );
		$clean['general']['show_tagline']    = ! empty( $g['show_tagline'] );
		$logo_id = isset( $g['logo_id'] ) ? absint( $g['logo_id'] ) : 0;
		$clean['general']['logo_id'] = ( $logo_id && wp_attachment_is_image( $logo_id ) ) ? $logo_id : 0;

		// Header.
		$h = isset( $merged['header'] ) && is_array( $merged['header'] ) ? $merged['header'] : array();
		$mode = isset( $h['mode'] ) ? sanitize_key( $h['mode'] ) : 'theme';
		$clean['header']['mode']        = in_array( $mode, $modes, true ) ? $mode : 'theme';
		$clean['header']['template_id'] = isset( $h['template_id'] ) ? absint( $h['template_id'] ) : 0;
		$layout = isset( $h['layout'] ) ? sanitize_key( $h['layout'] ) : 'triple';
		$clean['header']['layout'] = in_array( $layout, array( 'triple', 'classic' ), true ) ? $layout : 'triple';
		$clean['header']['sticky'] = ! empty( $h['sticky'] );
		$clean['header']['scroll'] = self::sanitize_header_scroll( isset( $h['scroll'] ) && is_array( $h['scroll'] ) ? $h['scroll'] : array(), $defaults['header']['scroll'] );
		$clean['header']['announce'] = self::sanitize_header_announce( isset( $h['announce'] ) && is_array( $h['announce'] ) ? $h['announce'] : array(), $defaults['header']['announce'] );

		$clean['header']['typo']    = self::sanitize_header_typo( isset( $h['typo'] ) && is_array( $h['typo'] ) ? $h['typo'] : array(), $defaults['header']['typo'] );
		$clean['header']['spacing'] = self::sanitize_header_spacing( isset( $h['spacing'] ) && is_array( $h['spacing'] ) ? $h['spacing'] : array(), $defaults['header']['spacing'] );
		$clean['header']['top']     = self::sanitize_header_top( isset( $h['top'] ) && is_array( $h['top'] ) ? $h['top'] : array(), $defaults['header']['top'] );
		$clean['header']['middle'] = self::sanitize_header_middle( isset( $h['middle'] ) && is_array( $h['middle'] ) ? $h['middle'] : array(), $defaults['header']['middle'] );
		$clean['header']['bottom']     = self::sanitize_header_bottom( isset( $h['bottom'] ) && is_array( $h['bottom'] ) ? $h['bottom'] : array(), $defaults['header']['bottom'] );
		$clean['header']['mobile_nav'] = self::sanitize_header_mobile_nav( isset( $h['mobile_nav'] ) && is_array( $h['mobile_nav'] ) ? $h['mobile_nav'] : array(), $defaults['header']['mobile_nav'] );

		// Footer.
		$f = isset( $merged['footer'] ) && is_array( $merged['footer'] ) ? $merged['footer'] : array();
		$fmode = isset( $f['mode'] ) ? sanitize_key( $f['mode'] ) : 'theme';
		$clean['footer']['mode']        = in_array( $fmode, $modes, true ) ? $fmode : 'theme';
		$clean['footer']['template_id'] = isset( $f['template_id'] ) ? absint( $f['template_id'] ) : 0;
		$clean['footer']['layout']      = 'columns4';
		$clean['footer']['bg']          = self::sanitize_hex_color_value( $f['bg'] ?? '', $defaults['footer']['bg'] );
		$clean['footer']['text']        = self::sanitize_hex_color_value( $f['text'] ?? '', $defaults['footer']['text'] );
		$clean['footer']['muted']       = self::sanitize_hex_color_value( $f['muted'] ?? '', $defaults['footer']['muted'] );
		$clean['footer']['accent']      = self::sanitize_hex_color_value( $f['accent'] ?? '', $defaults['footer']['accent'] );
		$clean['footer']['border']      = self::sanitize_hex_color_value( $f['border'] ?? '', $defaults['footer']['border'] );
		$clean['footer']['container_width'] = self::sanitize_css_size( $f['container_width'] ?? '', $defaults['footer']['container_width'] );
		$clean['footer']['side_padding']    = self::sanitize_css_size( $f['side_padding'] ?? '', $defaults['footer']['side_padding'] );
		$clean['footer']['columns'] = self::sanitize_footer_columns(
			isset( $f['columns'] ) && is_array( $f['columns'] ) ? $f['columns'] : array(),
			$defaults['footer']['columns']
		);
		$clean['footer']['bottom'] = self::sanitize_footer_bottom(
			isset( $f['bottom'] ) && is_array( $f['bottom'] ) ? $f['bottom'] : array(),
			$defaults['footer']['bottom']
		);
		$clean['footer']['back_to_top'] = self::sanitize_footer_back_to_top(
			isset( $f['back_to_top'] ) && is_array( $f['back_to_top'] ) ? $f['back_to_top'] : array(),
			$defaults['footer']['back_to_top']
		);

		// Blog.
		$b = isset( $merged['blog'] ) && is_array( $merged['blog'] ) ? $merged['blog'] : array();
		$clean['blog'] = self::sanitize_blog( $b, $defaults['blog'] );

		// Pages (404 + search).
		$pg = isset( $merged['pages'] ) && is_array( $merged['pages'] ) ? $merged['pages'] : array();
		$clean['pages'] = self::sanitize_pages( $pg, $defaults['pages'] );

		// WooCommerce.
		$woo = isset( $merged['woocommerce'] ) && is_array( $merged['woocommerce'] ) ? $merged['woocommerce'] : array();
		$clean['woocommerce'] = self::sanitize_woocommerce( $woo, $defaults['woocommerce'] );

		// Assignments.
		$a = isset( $merged['assignments'] ) && is_array( $merged['assignments'] ) ? $merged['assignments'] : array();
		$scope = isset( $a['scope'] ) ? sanitize_key( $a['scope'] ) : 'entire_site';
		$allowed_scopes = array( 'entire_site', 'front_page', 'shop', 'blog', 'custom' );
		$clean['assignments']['scope'] = in_array( $scope, $allowed_scopes, true ) ? $scope : 'entire_site';

		$exclude = array();
		if ( ! empty( $a['exclude_ids'] ) ) {
			$parts = is_string( $a['exclude_ids'] ) ? preg_split( '/[\s,]+/', $a['exclude_ids'] ) : (array) $a['exclude_ids'];
			foreach ( $parts as $id ) {
				$id = absint( $id );
				if ( $id > 0 ) {
					$exclude[] = $id;
				}
			}
		}
		$clean['assignments']['exclude_ids'] = array_values( array_unique( $exclude ) );

		$inc_raw = isset( $a['include'] ) && is_array( $a['include'] ) ? $a['include'] : array();
		$clean['assignments']['include'] = array(
			'front_page' => ! empty( $inc_raw['front_page'] ),
			'shop'       => ! empty( $inc_raw['shop'] ),
			'blog'       => ! empty( $inc_raw['blog'] ),
			'other'      => ! empty( $inc_raw['other'] ),
		);

		$clean['assignments']['header_overrides'] = self::sanitize_header_overrides(
			isset( $a['header_overrides'] ) && is_array( $a['header_overrides'] ) ? $a['header_overrides'] : array(),
			$defaults['assignments']['header_overrides']
		);

		// Performance.
		$p = isset( $merged['performance'] ) && is_array( $merged['performance'] ) ? $merged['performance'] : array();
		foreach ( array_keys( $defaults['performance'] ) as $key ) {
			$clean['performance'][ $key ] = ! empty( $p[ $key ] );
		}

		return $clean;
	}

	/**
	 * @param array $raw Raw.
	 * @param array $d   Defaults.
	 * @return array
	 */
	private static function sanitize_header_scroll( array $raw, array $d ) {
		$allowed  = array( 'none', 'sticky', 'shrink', 'bottom_sticky', 'transparent_solid', 'auto_hide' );
		$behavior = isset( $raw['behavior'] ) ? sanitize_key( $raw['behavior'] ) : $d['behavior'];
		$offset   = isset( $raw['offset'] ) ? absint( $raw['offset'] ) : (int) $d['offset'];
		return array(
			'behavior' => in_array( $behavior, $allowed, true ) ? $behavior : $d['behavior'],
			'offset'   => max( 0, min( 500, $offset ) ),
			'progress' => ! empty( $raw['progress'] ),
		);
	}

	/**
	 * @param array $raw Raw.
	 * @param array $d   Defaults.
	 * @return array
	 */
	private static function sanitize_header_announce( array $raw, array $d ) {
		$days = isset( $raw['cookie_days'] ) ? absint( $raw['cookie_days'] ) : (int) $d['cookie_days'];
		$ver  = isset( $raw['version'] ) ? sanitize_key( $raw['version'] ) : $d['version'];
		if ( '' === $ver ) {
			$ver = $d['version'];
		}
		return array(
			'enabled'     => ! empty( $raw['enabled'] ),
			'text'        => isset( $raw['text'] ) ? sanitize_text_field( $raw['text'] ) : '',
			'link_url'    => isset( $raw['link_url'] ) ? esc_url_raw( $raw['link_url'] ) : '',
			'link_text'   => isset( $raw['link_text'] ) ? sanitize_text_field( $raw['link_text'] ) : $d['link_text'],
			'bg'          => self::sanitize_hex_color_value( $raw['bg'] ?? '', $d['bg'] ),
			'text_color'  => self::sanitize_hex_color_value( $raw['text_color'] ?? '', $d['text_color'] ),
			'dismissible' => ! empty( $raw['dismissible'] ),
			'cookie_days' => max( 1, min( 365, $days ) ),
			'version'     => $ver,
		);
	}

	/**
	 * @param array $raw Raw.
	 * @param array $d   Defaults.
	 * @return array
	 */
	private static function sanitize_header_spacing( array $raw, array $d ) {
		return array(
			'container_width' => self::sanitize_css_size( $raw['container_width'] ?? '', $d['container_width'] ),
			'side_padding'    => self::sanitize_css_size( $raw['side_padding'] ?? '', $d['side_padding'] ),
			'top_min_h'       => self::sanitize_css_size( $raw['top_min_h'] ?? '', $d['top_min_h'] ),
			'mid_min_h'       => self::sanitize_css_size( $raw['mid_min_h'] ?? '', $d['mid_min_h'] ),
			'bot_min_h'       => self::sanitize_css_size( $raw['bot_min_h'] ?? '', $d['bot_min_h'] ),
		);
	}

	/**
	 * @param array $raw Raw.
	 * @param array $d   Defaults.
	 * @return array
	 */
	private static function sanitize_header_typo( array $raw, array $d ) {
		// 'inherit' means "use the global body font" — any valid catalog id is also allowed.
		$font_id = isset( $raw['font_id'] ) ? sanitize_key( $raw['font_id'] ) : ( $d['font_id'] ?? 'inherit' );
		if ( 'inherit' !== $font_id && ! Themezur_Fonts::get( $font_id ) ) {
			$font_id = 'inherit';
		}
		return array(
			'font_id'     => $font_id,
			'font_family' => isset( $raw['font_family'] ) ? sanitize_text_field( $raw['font_family'] ) : $d['font_family'],
			'top_size'    => self::sanitize_css_size( $raw['top_size'] ?? '', $d['top_size'] ),
			'top_weight'  => self::sanitize_font_weight( $raw['top_weight'] ?? '', $d['top_weight'] ),
			'mid_size'    => self::sanitize_css_size( $raw['mid_size'] ?? '', $d['mid_size'] ),
			'mid_weight'  => self::sanitize_font_weight( $raw['mid_weight'] ?? '', $d['mid_weight'] ),
			'nav_size'    => self::sanitize_css_size( $raw['nav_size'] ?? '', $d['nav_size'] ),
			'nav_weight'  => self::sanitize_font_weight( $raw['nav_weight'] ?? '', $d['nav_weight'] ),
			'logo_max_h'  => self::sanitize_css_size( $raw['logo_max_h'] ?? '', $d['logo_max_h'] ),
		);
	}

	/**
	 * @param array $raw Raw.
	 * @param array $d   Defaults.
	 * @return array
	 */
	private static function sanitize_header_top( array $raw, array $d ) {
		$socials = array();
		if ( ! empty( $raw['socials'] ) && is_array( $raw['socials'] ) ) {
			$allowed = Themezur_Social::networks();
			foreach ( $raw['socials'] as $item ) {
				if ( ! is_array( $item ) ) {
					continue;
				}
				$network = isset( $item['network'] ) ? sanitize_key( $item['network'] ) : '';
				$url     = isset( $item['url'] ) ? esc_url_raw( $item['url'] ) : '';
				if ( $network && $url && isset( $allowed[ $network ] ) ) {
					$socials[] = array(
						'network' => $network,
						'url'     => $url,
					);
				}
			}
		}

		// Legacy fallback if UI still sent old keys once.
		if ( empty( $socials ) ) {
			$socials = Themezur_Social::normalize_list( $raw );
		}

		$promos = array();
		if ( ! empty( $raw['promos'] ) && is_array( $raw['promos'] ) ) {
			foreach ( $raw['promos'] as $item ) {
				$text = '';
				if ( is_array( $item ) && isset( $item['text'] ) ) {
					$text = sanitize_text_field( $item['text'] );
				} elseif ( is_string( $item ) ) {
					$text = sanitize_text_field( $item );
				}
				if ( '' !== $text ) {
					$promos[] = array( 'text' => $text );
				}
			}
		}

		$interval = isset( $raw['promo_interval'] ) ? absint( $raw['promo_interval'] ) : (int) $d['promo_interval'];

		return array(
			'enabled'               => ! empty( $raw['enabled'] ),
			'bg'                    => self::sanitize_hex_color_value( $raw['bg'] ?? '', $d['bg'] ),
			'text'                  => self::sanitize_hex_color_value( $raw['text'] ?? '', $d['text'] ),
			'muted'                 => self::sanitize_hex_color_value( $raw['muted'] ?? '', $d['muted'] ),
			'accent'                => self::sanitize_hex_color_value( $raw['accent'] ?? '', $d['accent'] ),
			'show_trending'         => ! empty( $raw['show_trending'] ),
			'trending_label'        => isset( $raw['trending_label'] ) ? sanitize_text_field( $raw['trending_label'] ) : $d['trending_label'],
			'trending_text'         => isset( $raw['trending_text'] ) ? sanitize_text_field( $raw['trending_text'] ) : '',
			'promos'                => $promos,
			'promo_rotate'          => ! empty( $raw['promo_rotate'] ),
			'promo_interval'        => max( 2, min( 30, $interval ) ),
			'hide_mobile_trending'  => ! empty( $raw['hide_mobile_trending'] ),
			'hide_desktop_trending' => ! empty( $raw['hide_desktop_trending'] ),
			'show_social'           => ! empty( $raw['show_social'] ),
			'socials'               => $socials,
			'hide_mobile_social'    => ! empty( $raw['hide_mobile_social'] ),
			'hide_desktop_social'   => ! empty( $raw['hide_desktop_social'] ),
			'show_phone'            => ! empty( $raw['show_phone'] ),
			'phone_label'           => isset( $raw['phone_label'] ) ? sanitize_text_field( $raw['phone_label'] ) : $d['phone_label'],
			'phone_number'          => isset( $raw['phone_number'] ) ? sanitize_text_field( $raw['phone_number'] ) : '',
			'hide_mobile_phone'     => ! empty( $raw['hide_mobile_phone'] ),
			'hide_desktop_phone'    => ! empty( $raw['hide_desktop_phone'] ),
			'show_date'             => ! empty( $raw['show_date'] ),
			'date_mode'             => ( isset( $raw['date_mode'] ) && 'custom' === sanitize_key( $raw['date_mode'] ) ) ? 'custom' : 'date',
			'date_format'           => isset( $raw['date_format'] ) ? sanitize_text_field( $raw['date_format'] ) : $d['date_format'],
			'date_custom'           => isset( $raw['date_custom'] ) ? sanitize_text_field( $raw['date_custom'] ) : '',
			'hide_mobile_date'      => ! empty( $raw['hide_mobile_date'] ),
			'hide_desktop_date'     => ! empty( $raw['hide_desktop_date'] ),
		);
	}

	/**
	 * @param array $raw Raw.
	 * @param array $d   Defaults.
	 * @return array
	 */
	private static function sanitize_header_middle( array $raw, array $d ) {
		$min = isset( $raw['search_min_chars'] ) ? absint( $raw['search_min_chars'] ) : (int) $d['search_min_chars'];
		$lim = isset( $raw['search_limit'] ) ? absint( $raw['search_limit'] ) : (int) $d['search_limit'];
		return array(
			'enabled'              => ! empty( $raw['enabled'] ),
			'bg'                   => self::sanitize_hex_color_value( $raw['bg'] ?? '', $d['bg'] ),
			'text'                 => self::sanitize_hex_color_value( $raw['text'] ?? '', $d['text'] ),
			'muted'                => self::sanitize_hex_color_value( $raw['muted'] ?? '', $d['muted'] ),
			'accent'               => self::sanitize_hex_color_value( $raw['accent'] ?? '', $d['accent'] ),
			'border'               => self::sanitize_hex_color_value( $raw['border'] ?? '', $d['border'] ),
			'show_logo'            => ! empty( $raw['show_logo'] ),
			'hide_mobile_logo'     => ! empty( $raw['hide_mobile_logo'] ),
			'hide_desktop_logo'    => ! empty( $raw['hide_desktop_logo'] ),
			'show_address'         => ! empty( $raw['show_address'] ),
			'address'              => isset( $raw['address'] ) ? sanitize_text_field( $raw['address'] ) : '',
			'hide_mobile_address'  => ! empty( $raw['hide_mobile_address'] ),
			'hide_desktop_address' => ! empty( $raw['hide_desktop_address'] ),
			'show_search'          => ! empty( $raw['show_search'] ),
			'search_placeholder'   => isset( $raw['search_placeholder'] ) ? sanitize_text_field( $raw['search_placeholder'] ) : $d['search_placeholder'],
			'smart_search'         => ! empty( $raw['smart_search'] ),
			'search_min_chars'     => max( 1, min( 10, $min ) ),
			'search_limit'         => max( 3, min( 20, $lim ) ),
			'hide_mobile_search'   => ! empty( $raw['hide_mobile_search'] ),
			'hide_desktop_search'  => ! empty( $raw['hide_desktop_search'] ),
			'show_dark_mode'       => ! empty( $raw['show_dark_mode'] ),
			'hide_mobile_dark'     => ! empty( $raw['hide_mobile_dark'] ),
			'hide_desktop_dark'    => ! empty( $raw['hide_desktop_dark'] ),
			'show_account'         => ! empty( $raw['show_account'] ),
			'hide_mobile_account'  => ! empty( $raw['hide_mobile_account'] ),
			'hide_desktop_account' => ! empty( $raw['hide_desktop_account'] ),
			'show_wishlist'        => ! empty( $raw['show_wishlist'] ),
			'wishlist_url'         => isset( $raw['wishlist_url'] ) ? esc_url_raw( $raw['wishlist_url'] ) : '',
			'hide_mobile_wishlist' => ! empty( $raw['hide_mobile_wishlist'] ),
			'hide_desktop_wishlist' => ! empty( $raw['hide_desktop_wishlist'] ),
			'show_compare'         => ! empty( $raw['show_compare'] ),
			'compare_url'          => isset( $raw['compare_url'] ) ? esc_url_raw( $raw['compare_url'] ) : '',
			'hide_mobile_compare'  => ! empty( $raw['hide_mobile_compare'] ),
			'hide_desktop_compare' => ! empty( $raw['hide_desktop_compare'] ),
			'show_cart'            => ! empty( $raw['show_cart'] ),
			'mini_cart'            => ! empty( $raw['mini_cart'] ),
			'hide_mobile_cart'     => ! empty( $raw['hide_mobile_cart'] ),
			'hide_desktop_cart'    => ! empty( $raw['hide_desktop_cart'] ),
			'show_menu'            => ! empty( $raw['show_menu'] ),
			'menu_id'              => isset( $raw['menu_id'] ) ? absint( $raw['menu_id'] ) : 0,
			'menu_position'        => in_array( $raw['menu_position'] ?? '', array( 'inline', 'center', 'right' ), true ) ? $raw['menu_position'] : 'inline',
			'hide_mobile_menu'     => ! empty( $raw['hide_mobile_menu'] ),
			'hide_desktop_menu'    => ! empty( $raw['hide_desktop_menu'] ),
			'show_button'          => ! empty( $raw['show_button'] ),
			'button_text'          => isset( $raw['button_text'] ) ? sanitize_text_field( $raw['button_text'] ) : ( $d['button_text'] ?? 'Order Now' ),
			'button_url'           => isset( $raw['button_url'] ) ? esc_url_raw( $raw['button_url'] ) : '',
			'button_target'        => ( isset( $raw['button_target'] ) && '_blank' === $raw['button_target'] ) ? '_blank' : '_self',
			'hide_mobile_button'   => ! empty( $raw['hide_mobile_button'] ),
			'hide_desktop_button'  => ! empty( $raw['hide_desktop_button'] ),
		);
	}

	/**
	 * @param array $raw Raw.
	 * @param array $d   Defaults.
	 * @return array
	 */
	private static function sanitize_header_bottom( array $raw, array $d ) {
		$mode = isset( $raw['categories_mode'] ) ? sanitize_key( $raw['categories_mode'] ) : 'mega';
		$cols = isset( $raw['categories_columns'] ) ? absint( $raw['categories_columns'] ) : (int) $d['categories_columns'];
		$lim  = isset( $raw['categories_limit'] ) ? absint( $raw['categories_limit'] ) : (int) $d['categories_limit'];
		return array(
			'enabled'                 => ! empty( $raw['enabled'] ),
			'bg'                      => self::sanitize_hex_color_value( $raw['bg'] ?? '', $d['bg'] ),
			'text'                    => self::sanitize_hex_color_value( $raw['text'] ?? '', $d['text'] ),
			'muted'                   => self::sanitize_hex_color_value( $raw['muted'] ?? '', $d['muted'] ),
			'accent'                  => self::sanitize_hex_color_value( $raw['accent'] ?? '', $d['accent'] ),
			'accent_text'             => self::sanitize_hex_color_value( $raw['accent_text'] ?? '', $d['accent_text'] ),
			'show_categories'         => ! empty( $raw['show_categories'] ),
			'categories_text'         => isset( $raw['categories_text'] ) ? sanitize_text_field( $raw['categories_text'] ) : $d['categories_text'],
			'categories_url'          => isset( $raw['categories_url'] ) ? esc_url_raw( $raw['categories_url'] ) : '',
			'categories_mode'         => in_array( $mode, array( 'link', 'mega' ), true ) ? $mode : 'mega',
			'categories_columns'      => max( 2, min( 4, $cols ) ),
			'categories_limit'        => max( 4, min( 48, $lim ) ),
			'hide_mobile_categories'  => ! empty( $raw['hide_mobile_categories'] ),
			'hide_desktop_categories' => ! empty( $raw['hide_desktop_categories'] ),
			'show_menu'               => ! empty( $raw['show_menu'] ),
			'menu_id'                 => isset( $raw['menu_id'] ) ? absint( $raw['menu_id'] ) : 0,
			'hide_mobile_menu'        => ! empty( $raw['hide_mobile_menu'] ),
			'hide_desktop_menu'       => ! empty( $raw['hide_desktop_menu'] ),
			'show_deal'               => ! empty( $raw['show_deal'] ),
			'deal_text'               => isset( $raw['deal_text'] ) ? sanitize_text_field( $raw['deal_text'] ) : $d['deal_text'],
			'deal_url'                => isset( $raw['deal_url'] ) ? esc_url_raw( $raw['deal_url'] ) : '',
			'hide_mobile_deal'        => ! empty( $raw['hide_mobile_deal'] ),
			'hide_desktop_deal'       => ! empty( $raw['hide_desktop_deal'] ),
		);
	}

	/**
	 * @param array $raw Raw.
	 * @param array $d   Defaults.
	 * @return array
	 */
	private static function sanitize_header_mobile_nav( array $raw, array $d ) {
		return array(
			'enabled'      => ! empty( $raw['enabled'] ),
			'show_home'    => ! empty( $raw['show_home'] ),
			'show_cats'    => ! empty( $raw['show_cats'] ),
			'show_search'  => ! empty( $raw['show_search'] ),
			'show_cart'    => ! empty( $raw['show_cart'] ),
			'show_account' => ! empty( $raw['show_account'] ),
			'bg'           => self::sanitize_hex_color_value( $raw['bg'] ?? '', $d['bg'] ),
			'text'         => self::sanitize_hex_color_value( $raw['text'] ?? '', $d['text'] ),
			'accent'       => self::sanitize_hex_color_value( $raw['accent'] ?? '', $d['accent'] ),
		);
	}

	/**
	 * Sanitize per-context header overrides (home / shop / blog).
	 *
	 * @param array $raw Raw overrides.
	 * @param array $d   Defaults.
	 * @return array
	 */
	private static function sanitize_header_overrides( array $raw, array $d ) {
		$modes = array( 'inherit', 'theme', 'elementor', 'none' );
		$out   = array();
		foreach ( array( 'front_page', 'shop', 'blog' ) as $ctx ) {
			$row  = isset( $raw[ $ctx ] ) && is_array( $raw[ $ctx ] ) ? $raw[ $ctx ] : array();
			$mode = isset( $row['mode'] ) ? sanitize_key( $row['mode'] ) : $d[ $ctx ]['mode'];
			$out[ $ctx ] = array(
				'enabled'     => ! empty( $row['enabled'] ),
				'mode'        => in_array( $mode, $modes, true ) ? $mode : $d[ $ctx ]['mode'],
				'template_id' => isset( $row['template_id'] ) ? absint( $row['template_id'] ) : 0,
			);
		}
		return $out;
	}

	/**
	 * Sanitize footer columns (1–4).
	 *
	 * @param array $raw Raw columns.
	 * @param array $d   Defaults.
	 * @return array
	 */
	private static function sanitize_footer_columns( array $raw, array $d ) {
		$types           = array( 'about', 'menu', 'links', 'contact', 'posts', 'products', 'shortcode', 'newsletter' );
		$logo_sources    = array( 'none', 'site', 'custom' );
		$product_sources = array( 'recent', 'featured', 'on_sale', 'top_rated' );
		$nl_modes        = array( 'form', 'shortcode' );
		$out             = array();
		foreach ( array( '1', '2', '3', '4' ) as $key ) {
			$row = isset( $raw[ $key ] ) && is_array( $raw[ $key ] ) ? $raw[ $key ] : ( isset( $raw[ (int) $key ] ) && is_array( $raw[ (int) $key ] ) ? $raw[ (int) $key ] : array() );
			$def = $d[ $key ];
			$type = isset( $row['type'] ) ? sanitize_key( $row['type'] ) : $def['type'];

			$links = array();
			if ( ! empty( $row['links'] ) && is_array( $row['links'] ) ) {
				foreach ( $row['links'] as $link ) {
					if ( ! is_array( $link ) ) {
						continue;
					}
					$label = isset( $link['label'] ) ? sanitize_text_field( $link['label'] ) : '';
					$url   = isset( $link['url'] ) ? esc_url_raw( $link['url'] ) : '';
					if ( '' !== $label ) {
						$links[] = array(
							'label' => $label,
							'url'   => $url,
						);
					}
				}
			}

			if ( isset( $row['logo_source'] ) ) {
				$logo_source = sanitize_key( $row['logo_source'] );
			} elseif ( ! empty( $row['show_logo'] ) ) {
				$logo_source = 'site';
			} else {
				$logo_source = isset( $def['logo_source'] ) ? $def['logo_source'] : 'none';
			}
			if ( ! in_array( $logo_source, $logo_sources, true ) ) {
				$logo_source = 'none';
			}

			$logo_id = isset( $row['logo_id'] ) ? absint( $row['logo_id'] ) : 0;
			if ( $logo_id && ! wp_attachment_is_image( $logo_id ) ) {
				$logo_id = 0;
			}

			$psource = isset( $row['products_source'] ) ? sanitize_key( $row['products_source'] ) : 'recent';
			$nl_mode = isset( $row['newsletter_mode'] ) ? sanitize_key( $row['newsletter_mode'] ) : 'form';

			$out[ $key ] = array(
				'enabled'                 => ! empty( $row['enabled'] ),
				'title'                   => isset( $row['title'] ) ? sanitize_text_field( $row['title'] ) : $def['title'],
				'subtitle'                => isset( $row['subtitle'] ) ? sanitize_text_field( $row['subtitle'] ) : '',
				'type'                    => in_array( $type, $types, true ) ? $type : $def['type'],
				'text'                    => isset( $row['text'] ) ? sanitize_textarea_field( $row['text'] ) : '',
				'logo_source'             => $logo_source,
				'logo_id'                 => $logo_id,
				'show_logo'               => 'none' !== $logo_source,
				'show_social'             => ! empty( $row['show_social'] ),
				'cta_label'               => isset( $row['cta_label'] ) ? sanitize_text_field( $row['cta_label'] ) : '',
				'cta_url'                 => isset( $row['cta_url'] ) ? esc_url_raw( $row['cta_url'] ) : '',
				'menu_id'                 => isset( $row['menu_id'] ) ? absint( $row['menu_id'] ) : 0,
				'links'                   => $links,
				'address'                 => isset( $row['address'] ) ? sanitize_textarea_field( $row['address'] ) : '',
				'phone'                   => isset( $row['phone'] ) ? sanitize_text_field( $row['phone'] ) : '',
				'email'                   => isset( $row['email'] ) ? sanitize_email( $row['email'] ) : '',
				'whatsapp'                => isset( $row['whatsapp'] ) ? sanitize_text_field( $row['whatsapp'] ) : '',
				'hours'                   => isset( $row['hours'] ) ? sanitize_textarea_field( $row['hours'] ) : '',
				'map_url'                 => isset( $row['map_url'] ) ? esc_url_raw( $row['map_url'] ) : '',
				'posts_count'             => max( 1, min( 8, isset( $row['posts_count'] ) ? absint( $row['posts_count'] ) : 3 ) ),
				'posts_show_thumb'        => ! isset( $row['posts_show_thumb'] ) ? true : ! empty( $row['posts_show_thumb'] ),
				'posts_show_date'         => ! isset( $row['posts_show_date'] ) ? true : ! empty( $row['posts_show_date'] ),
				'posts_category'          => isset( $row['posts_category'] ) ? absint( $row['posts_category'] ) : 0,
				'products_count'          => max( 1, min( 8, isset( $row['products_count'] ) ? absint( $row['products_count'] ) : 3 ) ),
				'products_source'         => in_array( $psource, $product_sources, true ) ? $psource : 'recent',
				'products_show_thumb'     => ! isset( $row['products_show_thumb'] ) ? true : ! empty( $row['products_show_thumb'] ),
				'products_show_price'     => ! isset( $row['products_show_price'] ) ? true : ! empty( $row['products_show_price'] ),
				'shortcode'               => isset( $row['shortcode'] ) ? self::sanitize_footer_markup( $row['shortcode'] ) : '',
				'newsletter_mode'         => in_array( $nl_mode, $nl_modes, true ) ? $nl_mode : 'form',
				'newsletter_text'         => isset( $row['newsletter_text'] ) ? sanitize_textarea_field( $row['newsletter_text'] ) : '',
				'newsletter_placeholder'  => isset( $row['newsletter_placeholder'] ) ? sanitize_text_field( $row['newsletter_placeholder'] ) : 'Your email',
				'newsletter_button'       => isset( $row['newsletter_button'] ) ? sanitize_text_field( $row['newsletter_button'] ) : 'Subscribe',
				'newsletter_action'       => isset( $row['newsletter_action'] ) ? esc_url_raw( $row['newsletter_action'] ) : '',
				'newsletter_email_name'   => isset( $row['newsletter_email_name'] ) ? preg_replace( '/[^A-Za-z0-9_\-\[\]]/', '', (string) $row['newsletter_email_name'] ) : 'EMAIL',
				'newsletter_shortcode'    => isset( $row['newsletter_shortcode'] ) ? self::sanitize_footer_markup( $row['newsletter_shortcode'] ) : '',
			);
			if ( '' === $out[ $key ]['newsletter_email_name'] ) {
				$out[ $key ]['newsletter_email_name'] = 'EMAIL';
			}
			if ( '' === $out[ $key ]['newsletter_placeholder'] ) {
				$out[ $key ]['newsletter_placeholder'] = 'Your email';
			}
			if ( '' === $out[ $key ]['newsletter_button'] ) {
				$out[ $key ]['newsletter_button'] = 'Subscribe';
			}
		}
		return $out;
	}

	/**
	 * Allow shortcodes + safe HTML in footer markup fields.
	 *
	 * @param string $raw Raw markup.
	 * @return string
	 */
	private static function sanitize_footer_markup( $raw ) {
		$raw = (string) $raw;
		if ( '' === trim( $raw ) ) {
			return '';
		}
		return wp_kses_post( $raw );
	}

	/**
	 * Sanitize footer bottom bar.
	 *
	 * @param array $raw Raw.
	 * @param array $d   Defaults.
	 * @return array
	 */
	private static function sanitize_footer_bottom( array $raw, array $d ) {
		$raw_payments = isset( $raw['payments'] ) && is_array( $raw['payments'] ) ? $raw['payments'] : $d['payments'];
		$clean_payments = array_values( array_unique( array_map( 'sanitize_key', $raw_payments ) ) );

		return array(
			'enabled'       => ! empty( $raw['enabled'] ),
			'copyright'     => isset( $raw['copyright'] ) ? sanitize_text_field( $raw['copyright'] ) : '',
			'show_menu'     => ! empty( $raw['show_menu'] ),
			'menu_id'       => isset( $raw['menu_id'] ) ? absint( $raw['menu_id'] ) : 0,
			'show_payments' => isset( $raw['show_payments'] ) ? (bool) $raw['show_payments'] : (bool) $d['show_payments'],
			'payments'      => $clean_payments,
		);
	}

	/**
	 * Sanitize back-to-top settings.
	 *
	 * @param array $raw Raw.
	 * @param array $d   Defaults.
	 * @return array
	 */
	private static function sanitize_footer_back_to_top( array $raw, array $d ) {
		$threshold = isset( $raw['threshold'] ) ? absint( $raw['threshold'] ) : (int) $d['threshold'];
		return array(
			'enabled'   => ! empty( $raw['enabled'] ),
			'threshold' => max( 100, min( 2000, $threshold ) ),
		);
	}

	/**
	 * Sanitize WooCommerce options.
	 *
	 * @param array $raw Raw.
	 * @param array $d   Defaults.
	 * @return array
	 */
	private static function sanitize_woocommerce( array $raw, array $d ) {
		$mode  = isset( $raw['mode'] ) ? sanitize_key( $raw['mode'] ) : $d['mode'];
		$shop  = isset( $raw['shop'] ) && is_array( $raw['shop'] ) ? $raw['shop'] : array();
		$sing  = isset( $raw['single'] ) && is_array( $raw['single'] ) ? $raw['single'] : array();
		$ds    = $d['shop'];
		$dsi   = $d['single'];
		$style = isset( $shop['card_style'] ) ? sanitize_key( $shop['card_style'] ) : $ds['card_style'];

		return array(
			'mode'   => in_array( $mode, array( 'theme', 'default' ), true ) ? $mode : 'theme',
			'shop'   => array(
				'columns'            => max( 2, min( 5, isset( $shop['columns'] ) ? absint( $shop['columns'] ) : (int) $ds['columns'] ) ),
				'products_per_page'  => max( 4, min( 48, isset( $shop['products_per_page'] ) ? absint( $shop['products_per_page'] ) : (int) $ds['products_per_page'] ) ),
				'card_style'         => in_array( $style, array( 'soft', 'minimal', 'bordered' ), true ) ? $style : 'soft',
				'show_result_count'  => ! empty( $shop['show_result_count'] ),
				'show_ordering'      => ! empty( $shop['show_ordering'] ),
				'quick_view'         => isset( $shop['quick_view'] ) ? (bool) $shop['quick_view'] : (bool) $ds['quick_view'],
				'wishlist'           => isset( $shop['wishlist'] ) ? (bool) $shop['wishlist'] : (bool) $ds['wishlist'],
			),
			'single' => array(
				'related_count'    => max( 0, min( 8, isset( $sing['related_count'] ) ? absint( $sing['related_count'] ) : (int) $dsi['related_count'] ) ),
				'upsells_count'    => max( 0, min( 8, isset( $sing['upsells_count'] ) ? absint( $sing['upsells_count'] ) : (int) $dsi['upsells_count'] ) ),
				'sticky_cart'      => isset( $sing['sticky_cart'] ) ? (bool) $sing['sticky_cart'] : (bool) $dsi['sticky_cart'],
				'quantity_stepper' => isset( $sing['quantity_stepper'] ) ? (bool) $sing['quantity_stepper'] : (bool) $dsi['quantity_stepper'],
			),
		);
	}

	/**
	 * Sanitize 404 / search page options.
	 *
	 * @param array $raw Raw.
	 * @param array $d   Defaults.
	 * @return array
	 */
	private static function sanitize_pages( array $raw, array $d ) {
		$mode   = isset( $raw['mode'] ) ? sanitize_key( $raw['mode'] ) : $d['mode'];
		$nf     = isset( $raw['not_found'] ) && is_array( $raw['not_found'] ) ? $raw['not_found'] : array();
		$sr     = isset( $raw['search'] ) && is_array( $raw['search'] ) ? $raw['search'] : array();
		$dnf    = $d['not_found'];
		$dsr    = $d['search'];
		$layout = isset( $sr['layout'] ) ? sanitize_key( $sr['layout'] ) : $dsr['layout'];

		return array(
			'mode'      => in_array( $mode, array( 'theme', 'default' ), true ) ? $mode : 'theme',
			'not_found' => array(
				'title'            => isset( $nf['title'] ) ? sanitize_text_field( $nf['title'] ) : $dnf['title'],
				'text'             => isset( $nf['text'] ) ? sanitize_textarea_field( $nf['text'] ) : $dnf['text'],
				'show_search'      => ! empty( $nf['show_search'] ),
				'show_home_btn'    => ! empty( $nf['show_home_btn'] ),
				'home_label'       => isset( $nf['home_label'] ) ? sanitize_text_field( $nf['home_label'] ) : $dnf['home_label'],
				'show_quick_links' => isset( $nf['show_quick_links'] ) ? (bool) $nf['show_quick_links'] : (bool) $dnf['show_quick_links'],
			),
			'search'    => array(
				'layout'               => in_array( $layout, array( 'list', 'grid' ), true ) ? $layout : 'list',
				'show_image'           => ! empty( $sr['show_image'] ),
				'show_excerpt'         => ! empty( $sr['show_excerpt'] ),
				'show_type'            => ! empty( $sr['show_type'] ),
				'show_product_details' => isset( $sr['show_product_details'] ) ? (bool) $sr['show_product_details'] : (bool) $dsr['show_product_details'],
				'show_tabs'            => isset( $sr['show_tabs'] ) ? (bool) $sr['show_tabs'] : (bool) $dsr['show_tabs'],
			),
		);
	}

	/**
	 * Sanitize blog options.
	 *
	 * @param array $raw Raw.
	 * @param array $d   Defaults.
	 * @return array
	 */
	private static function sanitize_blog( array $raw, array $d ) {
		$mode   = isset( $raw['mode'] ) ? sanitize_key( $raw['mode'] ) : $d['mode'];
		$modes  = array( 'theme', 'default' );
		$arch   = isset( $raw['archive'] ) && is_array( $raw['archive'] ) ? $raw['archive'] : array();
		$single = isset( $raw['single'] ) && is_array( $raw['single'] ) ? $raw['single'] : array();
		$da     = $d['archive'];
		$ds     = $d['single'];

		$layout        = isset( $arch['layout'] ) ? sanitize_key( $arch['layout'] ) : $da['layout'];
		$columns       = isset( $arch['columns'] ) ? absint( $arch['columns'] ) : (int) $da['columns'];
		$card_style    = isset( $arch['card_style'] ) ? sanitize_key( $arch['card_style'] ) : $da['card_style'];
		$single_layout = isset( $single['layout'] ) ? sanitize_key( $single['layout'] ) : $ds['layout'];

		return array(
			'mode'            => in_array( $mode, $modes, true ) ? $mode : 'theme',
			'container_width' => self::sanitize_css_size( $raw['container_width'] ?? '', $d['container_width'] ),
			'archive'         => array(
				'layout'            => in_array( $layout, array( 'grid', 'list' ), true ) ? $layout : 'grid',
				'columns'           => max( 2, min( 4, $columns ) ),
				'card_style'        => in_array( $card_style, array( 'soft', 'bordered', 'minimal' ), true ) ? $card_style : 'soft',
				'featured_hero'     => isset( $arch['featured_hero'] ) ? (bool) $arch['featured_hero'] : (bool) $da['featured_hero'],
				'show_title'        => ! empty( $arch['show_title'] ),
				'show_description'  => ! empty( $arch['show_description'] ),
				'show_image'        => ! empty( $arch['show_image'] ),
				'show_excerpt'      => ! empty( $arch['show_excerpt'] ),
				'excerpt_length'    => max( 5, min( 80, isset( $arch['excerpt_length'] ) ? absint( $arch['excerpt_length'] ) : (int) $da['excerpt_length'] ) ),
				'show_date'         => ! empty( $arch['show_date'] ),
				'show_author'       => ! empty( $arch['show_author'] ),
				'show_category'     => ! empty( $arch['show_category'] ),
				'show_reading_time' => isset( $arch['show_reading_time'] ) ? (bool) $arch['show_reading_time'] : (bool) $da['show_reading_time'],
				'show_badge'        => isset( $arch['show_badge'] ) ? (bool) $arch['show_badge'] : (bool) $da['show_badge'],
				'show_read_more'    => ! empty( $arch['show_read_more'] ),
				'read_more_text'    => isset( $arch['read_more_text'] ) ? sanitize_text_field( $arch['read_more_text'] ) : $da['read_more_text'],
			),
			'single'          => array(
				'layout'            => in_array( $single_layout, array( 'standard', 'sidebar' ), true ) ? $single_layout : 'standard',
				'content_width'     => self::sanitize_css_size( $single['content_width'] ?? '', $ds['content_width'] ),
				'show_progress_bar' => isset( $single['show_progress_bar'] ) ? (bool) $single['show_progress_bar'] : (bool) $ds['show_progress_bar'],
				'show_toc'          => isset( $single['show_toc'] ) ? (bool) $single['show_toc'] : (bool) $ds['show_toc'],
				'show_social_share' => isset( $single['show_social_share'] ) ? (bool) $single['show_social_share'] : (bool) $ds['show_social_share'],
				'show_reading_time' => isset( $single['show_reading_time'] ) ? (bool) $single['show_reading_time'] : (bool) $ds['show_reading_time'],
				'show_image'        => ! empty( $single['show_image'] ),
				'show_date'         => ! empty( $single['show_date'] ),
				'show_author'       => ! empty( $single['show_author'] ),
				'show_category'     => ! empty( $single['show_category'] ),
				'show_tags'         => ! empty( $single['show_tags'] ),
				'show_author_box'   => ! empty( $single['show_author_box'] ),
				'show_nav'          => ! empty( $single['show_nav'] ),
				'show_related'      => ! empty( $single['show_related'] ),
				'related_count'     => max( 1, min( 6, isset( $single['related_count'] ) ? absint( $single['related_count'] ) : (int) $ds['related_count'] ) ),
				'show_comments'     => ! empty( $single['show_comments'] ),
			),
		);
	}


	/**
	 * @param string $color    Input.
	 * @param string $fallback Fallback.
	 * @return string
	 */
	private static function sanitize_hex_color_value( $color, $fallback ) {
		$sanitized = sanitize_hex_color( $color );
		return $sanitized ? $sanitized : $fallback;
	}

	/**
	 * @param string $size     CSS size.
	 * @param string $fallback Fallback.
	 * @return string
	 */
	private static function sanitize_css_size( $size, $fallback ) {
		$size = is_string( $size ) ? trim( $size ) : '';
		if ( preg_match( '/^\d+(\.\d+)?(px|rem|em|%)$/', $size ) ) {
			return $size;
		}
		return $fallback;
	}

	/**
	 * @param string $stack    Font stack.
	 * @param string $fallback Fallback.
	 * @return string
	 */
	private static function sanitize_font_stack( $stack, $fallback ) {
		$stack = is_string( $stack ) ? trim( $stack ) : '';
		if ( '' === $stack ) {
			return $fallback;
		}
		$stack = wp_strip_all_tags( $stack );
		$stack = preg_replace( '/[<>{};]/', '', $stack );
		return $stack ? $stack : $fallback;
	}

	/**
	 * @param string $value    Unitless number (e.g. line-height).
	 * @param string $fallback Fallback.
	 * @return string
	 */
	private static function sanitize_unitless_number( $value, $fallback ) {
		$value = is_string( $value ) || is_numeric( $value ) ? trim( (string) $value ) : '';
		if ( preg_match( '/^\d+(\.\d+)?$/', $value ) ) {
			return $value;
		}
		return $fallback;
	}

	/**
	 * @param string $weight   Font weight.
	 * @param string $fallback Fallback.
	 * @return string
	 */
	private static function sanitize_font_weight( $weight, $fallback ) {
		$allowed = array( '300', '400', '500', '600', '700', '800' );
		$weight  = (string) $weight;
		return in_array( $weight, $allowed, true ) ? $weight : $fallback;
	}

	/**
	 * Logo attachment ID from Themezur panel.
	 *
	 * @return int
	 */
	public static function get_logo_id() {
		return (int) self::get( 'general.logo_id', 0 );
	}

	/**
	 * Logo image URL (empty if none).
	 *
	 * @param string $size Image size.
	 * @return string
	 */
	public static function get_logo_url( $size = 'full' ) {
		$logo_id = self::get_logo_id();
		if ( $logo_id < 1 ) {
			return '';
		}
		$url = wp_get_attachment_image_url( $logo_id, $size );
		return $url ? $url : '';
	}

	/**
	 * Options array enriched for the admin UI (includes logo_url).
	 *
	 * @return array
	 */
	public static function get_all_for_admin() {
		$options = self::get_all();
		$options['general']['logo_url'] = self::get_logo_url( 'medium' );

		if ( ! empty( $options['footer']['columns'] ) && is_array( $options['footer']['columns'] ) ) {
			foreach ( array( '1', '2', '3', '4' ) as $key ) {
				if ( empty( $options['footer']['columns'][ $key ] ) || ! is_array( $options['footer']['columns'][ $key ] ) ) {
					continue;
				}
				$col = &$options['footer']['columns'][ $key ];
				if ( empty( $col['logo_source'] ) ) {
					$col['logo_source'] = ! empty( $col['show_logo'] ) ? 'site' : 'none';
				}
				$fid = isset( $col['logo_id'] ) ? (int) $col['logo_id'] : 0;
				$col['logo_url'] = ( $fid > 0 ) ? (string) wp_get_attachment_image_url( $fid, 'medium' ) : '';
				unset( $col );
			}
		}

		return $options;
	}
}
