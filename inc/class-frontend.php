<?php
/**
 * Frontend header/footer rendering + performance hooks.
 *
 * @package Themezur
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Themezur_Frontend
 */
class Themezur_Frontend {

	/**
	 * Register frontend hooks.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'apply_performance_filters' ), 1 );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'output_css_vars' ), 25 );
		add_filter( 'body_class', array( __CLASS__, 'body_classes' ) );
		add_action( 'init', array( __CLASS__, 'apply_early_performance' ) );
		add_filter( 'woocommerce_add_to_cart_fragments', array( __CLASS__, 'cart_count_fragment' ) );
		add_action( 'wp_footer', array( 'Themezur_Footer', 'render_back_to_top' ), 40 );
		add_filter( 'nav_menu_item_title', array( __CLASS__, 'format_menu_badges' ), 10, 4 );
	}

	/**
	 * Automatically format menu badges like [HOT], [NEW], [SALE], [50% OFF] or class badge-hot, badge-new.
	 *
	 * @param string   $title The menu item's title.
	 * @param WP_Post  $item  The current menu item.
	 * @param stdClass $args  An object of wp_nav_menu() arguments.
	 * @param int      $depth Depth of menu item.
	 * @return string
	 */
	public static function format_menu_badges( $title, $item, $args, $depth ) {
		if ( preg_match( '/\[(HOT|NEW|SALE|50% OFF|POPULAR|PROMO|[A-Za-z0-9%\s]+)\]/i', $title, $matches ) ) {
			$badge_text  = esc_html( strtoupper( trim( $matches[1] ) ) );
			$badge_class = 'tz-menu-badge';
			$lower       = strtolower( $badge_text );
			if ( in_array( $lower, array( 'hot', 'sale', '50% off' ), true ) ) {
				$badge_class .= ' tz-menu-badge--hot';
			} elseif ( 'new' === $lower ) {
				$badge_class .= ' tz-menu-badge--new';
			} else {
				$badge_class .= ' tz-menu-badge--accent';
			}

			$badge_html = sprintf( '<span class="%s">%s</span>', esc_attr( $badge_class ), $badge_text );
			$title      = str_replace( $matches[0], $badge_html, $title );
		}

		if ( ! empty( $item->classes ) && is_array( $item->classes ) ) {
			foreach ( $item->classes as $cls ) {
				if ( 0 === strpos( $cls, 'badge-' ) ) {
					$raw_tag     = str_replace( 'badge-', '', $cls );
					$badge_text  = esc_html( strtoupper( $raw_tag ) );
					$badge_class = 'tz-menu-badge tz-menu-badge--' . sanitize_html_class( $raw_tag );
					$title      .= sprintf( ' <span class="%s">%s</span>', esc_attr( $badge_class ), $badge_text );
					break;
				}
			}
		}

		return $title;
	}

	/**
	 * WooCommerce AJAX cart count fragment for the header icon.
	 *
	 * @param array $fragments Fragments.
	 * @return array
	 */
	public static function cart_count_fragment( $fragments ) {
		$count = 0;
		if ( function_exists( 'WC' ) && WC()->cart ) {
			$count = (int) WC()->cart->get_cart_contents_count();
		}
		$fragments['span.tz-cart-btn__count'] = self::cart_count_markup( $count );
		if ( Themezur_Options::get( 'header.middle.mini_cart', true ) ) {
			$fragments['div.widget_shopping_cart_content'] = self::mini_cart_markup();
		}
		return $fragments;
	}

	/**
	 * Render WooCommerce mini-cart contents in the standard fragment wrapper.
	 *
	 * @return string
	 */
	public static function mini_cart_markup() {
		if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
			return '<div class="widget_shopping_cart_content"></div>';
		}

		$template = THEMEZUR_DIR . '/template-parts/woocommerce/mini-cart.php';
		ob_start();
		if ( file_exists( $template ) ) {
			include $template;
		} elseif ( function_exists( 'woocommerce_mini_cart' ) ) {
			woocommerce_mini_cart();
		}
		$content = ob_get_clean();

		return '<div class="widget_shopping_cart_content">' . $content . '</div>';
	}

	/**
	 * Render the visual and screen-reader cart count.
	 *
	 * @param int $count Cart item count.
	 * @return string
	 */
	public static function cart_count_markup( $count ) {
		$count = max( 0, (int) $count );
		$label = self::cart_count_label( $count );

		return sprintf(
			'<span class="tz-cart-btn__count" data-tz-cart-count><span aria-hidden="true">%1$s</span><span class="screen-reader-text">%2$s</span></span>',
			esc_html( (string) $count ),
			esc_html( $label )
		);
	}

	/**
	 * Get the localized accessible cart label.
	 *
	 * @param int $count Cart item count.
	 * @return string
	 */
	public static function cart_count_label( $count ) {
		$count = max( 0, (int) $count );

		return sprintf(
			/* translators: %s: number of products in the cart. */
			_n( 'Cart, %s item', 'Cart, %s items', $count, 'themezur' ),
			number_format_i18n( $count )
		);
	}

	/**
	 * Body classes for layout preferences.
	 *
	 * @param string[] $classes Body classes.
	 * @return string[]
	 */
	public static function body_classes( $classes ) {
		$header_mode = self::resolve_mode( 'header' );
		$footer_mode = self::resolve_mode( 'footer' );

		$classes[] = 'themezur-header-' . sanitize_html_class( $header_mode );
		$classes[] = 'themezur-footer-' . sanitize_html_class( $footer_mode );

		if ( 'theme' === $header_mode && ! Themezur_Options::get( 'header.bottom.show_menu', true ) ) {
			$classes[] = 'themezur-hide-header-menu';
		}
		if ( 'theme' === $footer_mode && ! Themezur_Options::get( 'footer.bottom.show_menu', true ) ) {
			$classes[] = 'themezur-hide-footer-menu';
		}
		if ( Themezur_Options::get( 'header.sticky', false ) && in_array( $header_mode, array( 'theme', 'elementor' ), true ) ) {
			$classes[] = 'themezur-sticky-header';
		}

		$scroll_behavior = Themezur_Options::get( 'header.scroll.behavior', 'none' );
		if ( 'theme' === $header_mode && 'transparent_solid' === $scroll_behavior ) {
			$classes[] = 'tz-has-transparent-header';
		}

		return $classes;
	}

	/**
	 * Cookie name for a dismissible announcement version.
	 *
	 * @param string $version Announce version key.
	 * @return string
	 */
	public static function announce_cookie_name( $version = '1' ) {
		$version = sanitize_key( (string) $version );
		if ( '' === $version ) {
			$version = '1';
		}
		return 'themezur_announce_' . $version;
	}

	/**
	 * Whether the announcement bar should render (enabled, has text, not cookie-dismissed).
	 *
	 * @return bool
	 */
	public static function should_show_announce() {
		$a = Themezur_Options::get( 'header.announce', array() );
		if ( empty( $a['enabled'] ) || '' === trim( (string) ( $a['text'] ?? '' ) ) ) {
			return false;
		}
		if ( empty( $a['dismissible'] ) ) {
			return true;
		}
		$cookie = self::announce_cookie_name( $a['version'] ?? '1' );
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- cookie presence check only.
		return empty( $_COOKIE[ $cookie ] );
	}

	/**
	 * CSS classes for per-element mobile/desktop visibility.
	 *
	 * @param array  $section Row options (top/middle/bottom).
	 * @param string $key     Element key (trending, social, logo, …).
	 * @return string
	 */
	public static function visibility_classes( array $section, $key ) {
		$key     = sanitize_key( $key );
		$classes = array();
		if ( ! empty( $section[ 'hide_mobile_' . $key ] ) ) {
			$classes[] = 'tz-hide-mobile';
		}
		if ( ! empty( $section[ 'hide_desktop_' . $key ] ) ) {
			$classes[] = 'tz-hide-desktop';
		}
		return implode( ' ', $classes );
	}

	/**
	 * Detect primary page context for conditional headers.
	 *
	 * Priority: front_page → shop → blog → other.
	 *
	 * @return string front_page|shop|blog|other
	 */
	public static function get_current_context() {
		if ( is_front_page() ) {
			return 'front_page';
		}

		if ( function_exists( 'is_checkout' ) && is_checkout() && ( ! function_exists( 'is_wc_endpoint_url' ) || ! is_wc_endpoint_url( 'order-received' ) ) ) {
			return 'checkout';
		}

		if ( function_exists( 'is_shop' ) && ( is_shop() || is_product_taxonomy() || is_product() ) ) {
			return 'shop';
		}

		if ( is_home() || is_singular( 'post' ) || is_category() || is_tag() || is_author() || is_date() ) {
			return 'blog';
		}

		return 'other';
	}

	/**
	 * Whether Themezur assignment rules match the current request.
	 *
	 * @return bool
	 */
	public static function assignments_match() {
		$exclude_ids = Themezur_Options::get( 'assignments.exclude_ids', array() );

		if ( ! empty( $exclude_ids ) && is_singular() ) {
			$current = (int) get_queried_object_id();
			if ( $current && in_array( $current, array_map( 'intval', $exclude_ids ), true ) ) {
				return false;
			}
		}

		$scope = Themezur_Options::get( 'assignments.scope', 'entire_site' );
		$ctx   = self::get_current_context();

		switch ( $scope ) {
			case 'front_page':
				return 'front_page' === $ctx;
			case 'shop':
				return 'shop' === $ctx;
			case 'blog':
				return 'blog' === $ctx;
			case 'custom':
				$include = Themezur_Options::get( 'assignments.include', array() );
				return ! empty( $include[ $ctx ] );
			case 'entire_site':
			default:
				return true;
		}
	}

	/**
	 * Resolved header mode + Elementor template for the current request.
	 *
	 * @return array{mode:string,template_id:int}
	 */
	public static function resolve_header_config() {
		static $cache = null;
		if ( null !== $cache ) {
			return $cache;
		}

		$mode        = Themezur_Options::get( 'header.mode', 'theme' );
		$template_id = (int) Themezur_Options::get( 'header.template_id', 0 );

		if ( ! self::assignments_match() ) {
			$cache = array(
				'mode'        => 'theme',
				'template_id' => 0,
			);
			return $cache;
		}

		$ctx       = self::get_current_context();
		$overrides = Themezur_Options::get( 'assignments.header_overrides', array() );
		if ( in_array( $ctx, array( 'front_page', 'shop', 'blog' ), true ) && ! empty( $overrides[ $ctx ]['enabled'] ) ) {
			$om = isset( $overrides[ $ctx ]['mode'] ) ? sanitize_key( $overrides[ $ctx ]['mode'] ) : 'inherit';
			if ( in_array( $om, array( 'theme', 'elementor', 'none' ), true ) ) {
				$mode = $om;
				if ( 'elementor' === $mode ) {
					$template_id = isset( $overrides[ $ctx ]['template_id'] ) ? (int) $overrides[ $ctx ]['template_id'] : 0;
				}
			}
		}

		if ( 'elementor' === $mode ) {
			if ( ! Themezur_Elementor::is_active() || ! Themezur_Elementor::is_valid_template( $template_id ) ) {
				$mode        = 'theme';
				$template_id = 0;
			}
		}

		$cache = array(
			'mode'        => $mode,
			'template_id' => $template_id,
		);
		return $cache;
	}

	/**
	 * Resolve effective mode for header or footer.
	 *
	 * @param string $part header|footer.
	 * @return string theme|elementor|none
	 */
	/**
	 * Resolved footer mode + Elementor template for the current request.
	 *
	 * @return array{mode:string,template_id:int}
	 */
	public static function resolve_footer_config() {
		static $cache = null;
		if ( null !== $cache ) {
			return $cache;
		}

		$mode        = Themezur_Options::get( 'footer.mode', 'theme' );
		$template_id = (int) Themezur_Options::get( 'footer.template_id', 0 );

		if ( ! self::assignments_match() ) {
			$cache = array(
				'mode'        => 'theme',
				'template_id' => 0,
			);
			return $cache;
		}

		$ctx       = self::get_current_context();
		$overrides = Themezur_Options::get( 'assignments.footer_overrides', array() );
		if ( in_array( $ctx, array( 'front_page', 'shop', 'blog', 'checkout' ), true ) && ! empty( $overrides[ $ctx ]['enabled'] ) ) {
			$om = isset( $overrides[ $ctx ]['mode'] ) ? sanitize_key( $overrides[ $ctx ]['mode'] ) : 'inherit';
			if ( in_array( $om, array( 'theme', 'elementor', 'none' ), true ) ) {
				$mode = $om;
				if ( 'elementor' === $mode ) {
					$template_id = isset( $overrides[ $ctx ]['template_id'] ) ? (int) $overrides[ $ctx ]['template_id'] : 0;
				}
			}
		}

		if ( 'elementor' === $mode ) {
			if ( ! Themezur_Elementor::is_active() || ! Themezur_Elementor::is_valid_template( $template_id ) ) {
				$mode        = 'theme';
				$template_id = 0;
			}
		}

		$cache = array(
			'mode'        => $mode,
			'template_id' => $template_id,
		);
		return $cache;
	}

	public static function resolve_mode( $part ) {
		if ( 'header' === $part ) {
			$config = self::resolve_header_config();
			return $config['mode'];
		}

		if ( 'footer' === $part ) {
			$config = self::resolve_footer_config();
			return $config['mode'];
		}

		return 'theme';
	}

	/**
	 * Render site header region (inside body, after skip link).
	 *
	 * @return void
	 */
	public static function render_header() {
		$config = self::resolve_header_config();
		$mode   = $config['mode'];

		if ( 'none' === $mode ) {
			return;
		}

		if ( 'elementor' === $mode ) {
			$template_id = (int) $config['template_id'];
			$html        = Themezur_Elementor::render_template( $template_id );
			if ( $html ) {
				$sticky = Themezur_Options::get( 'header.sticky', false ) ? ' is-sticky' : '';
				echo '<div id="themezur-header" class="themezur-header themezur-header--elementor' . esc_attr( $sticky ) . '">';
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Elementor HTML.
				echo $html;
				echo '</div>';
				return;
			}
		}

		self::render_theme_header();
	}

	/**
	 * Render site footer region.
	 *
	 * @return void
	 */
	public static function render_footer() {
		$config = self::resolve_footer_config();
		$mode   = $config['mode'];

		if ( 'none' === $mode ) {
			return;
		}

		if ( 'elementor' === $mode ) {
			$template_id = (int) $config['template_id'];
			$html        = Themezur_Elementor::render_template( $template_id );
			if ( $html ) {
				echo '<div id="themezur-footer" class="themezur-footer themezur-footer--elementor">';
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Elementor HTML.
				echo $html;
				echo '</div>';
				return;
			}
		}

		self::render_theme_footer();
	}

	/**
	 * Themezur native header template part.
	 *
	 * @return void
	 */
	private static function render_theme_header() {
		get_template_part( 'template-parts/header' );
	}

	/**
	 * Themezur native footer template part.
	 *
	 * @return void
	 */
	private static function render_theme_footer() {
		get_template_part( 'template-parts/footer' );
	}

	/**
	 * Print CSS custom properties from general settings.
	 *
	 * @return void
	 */
	public static function output_css_vars() {
		$g = Themezur_Options::get( 'general', array() );

		// Resolve body/heading weights to a single representative value for CSS var.
		$body_weights = ! empty( $g['font_body_weights'] ) && is_array( $g['font_body_weights'] ) ? $g['font_body_weights'] : array( '400' );
		$head_weights = ! empty( $g['font_heading_weights'] ) && is_array( $g['font_heading_weights'] ) ? $g['font_heading_weights'] : array( '700' );
		$body_weight_val = in_array( '400', $body_weights, true ) ? '400' : $body_weights[0];
		$head_weight_val = in_array( '700', $head_weights, true ) ? '700' : end( $head_weights );

		$css = sprintf(
			':root{--themezur-primary:%1$s;--themezur-accent:%2$s;--themezur-text:%3$s;--themezur-muted:%4$s;--themezur-bg:%5$s;--themezur-surface:%6$s;--themezur-border:%7$s;--themezur-link:%8$s;--themezur-link-hover:%9$s;--themezur-font:%10$s;--themezur-font-heading:%11$s;--themezur-font-size:%12$s;--themezur-line-height:%13$s;--themezur-radius:%14$s;--themezur-container:%15$s;--themezur-btn-bg:%16$s;--themezur-btn-text:%17$s;--themezur-btn-radius:%18$s;--themezur-font-weight:%19$s;--themezur-heading-weight:%20$s;}',
			wp_strip_all_tags( $g['primary_color'] ?? '#0f172a' ),
			wp_strip_all_tags( $g['accent_color'] ?? '#2563eb' ),
			wp_strip_all_tags( $g['text_color'] ?? '#0f172a' ),
			wp_strip_all_tags( $g['muted_color'] ?? '#64748b' ),
			wp_strip_all_tags( $g['bg_color'] ?? '#ffffff' ),
			wp_strip_all_tags( $g['surface_color'] ?? '#f8fafc' ),
			wp_strip_all_tags( $g['border_color'] ?? '#e2e8f0' ),
			wp_strip_all_tags( $g['link_color'] ?? '#2563eb' ),
			wp_strip_all_tags( $g['link_hover'] ?? '#1d4ed8' ),
			wp_strip_all_tags( $g['font_body'] ?? 'system-ui, sans-serif' ),
			wp_strip_all_tags( $g['font_heading'] ?? 'system-ui, sans-serif' ),
			wp_strip_all_tags( $g['font_size'] ?? '16px' ),
			wp_strip_all_tags( $g['line_height'] ?? '1.65' ),
			wp_strip_all_tags( $g['radius'] ?? '8px' ),
			wp_strip_all_tags( $g['container_width'] ?? '1200px' ),
			wp_strip_all_tags( $g['button_bg'] ?? '#2563eb' ),
			wp_strip_all_tags( $g['button_text'] ?? '#ffffff' ),
			wp_strip_all_tags( $g['button_radius'] ?? '8px' ),
			wp_strip_all_tags( $body_weight_val ),
			wp_strip_all_tags( $head_weight_val )
		);

		wp_add_inline_style( 'themezur-style', $css );

		if ( wp_style_is( 'themezur-design', 'enqueued' ) ) {
			wp_add_inline_style( 'themezur-design', $css );
		}

		if ( 'theme' === self::resolve_mode( 'header' ) && wp_style_is( 'themezur-header', 'enqueued' ) ) {
			$top      = Themezur_Options::get( 'header.top', array() );
			$middle   = Themezur_Options::get( 'header.middle', array() );
			$bottom   = Themezur_Options::get( 'header.bottom', array() );
			$typo     = Themezur_Options::get( 'header.typo', array() );
			$spacing  = Themezur_Options::get( 'header.spacing', array() );
			$announce = Themezur_Options::get( 'header.announce', array() );

			// Resolve header font: 'inherit' uses global body font, otherwise use catalog entry.
			$header_font_id = $typo['font_id'] ?? 'inherit';
			if ( 'inherit' === $header_font_id || ! Themezur_Fonts::get( $header_font_id ) ) {
				$resolved_header_font = $g['font_body'] ?? 'system-ui,sans-serif';
			} else {
				$resolved_header_font = Themezur_Fonts::family( $header_font_id );
			}

			$header_css = sprintf(
				'.tz-site-header--triple{--tz-announce-bg:%1$s;--tz-announce-text:%2$s;--tz-top-bg:%3$s;--tz-top-text:%4$s;--tz-top-muted:%5$s;--tz-top-accent:%6$s;--tz-mid-bg:%7$s;--tz-mid-text:%8$s;--tz-mid-muted:%9$s;--tz-mid-accent:%10$s;--tz-mid-border:%11$s;--tz-bot-bg:%12$s;--tz-bot-text:%13$s;--tz-bot-muted:%14$s;--tz-bot-accent:%15$s;--tz-bot-accent-text:%16$s;--tz-font:%17$s;--tz-top-size:%18$s;--tz-top-weight:%19$s;--tz-mid-size:%20$s;--tz-mid-weight:%21$s;--tz-nav-size:%22$s;--tz-nav-weight:%23$s;--tz-logo-max-h:%24$s;--tz-container:%25$s;--tz-side-pad:%26$s;--tz-top-min-h:%27$s;--tz-mid-min-h:%28$s;--tz-bot-min-h:%29$s;}',
				wp_strip_all_tags( $announce['bg'] ?? '#2563eb' ),
				wp_strip_all_tags( $announce['text_color'] ?? '#ffffff' ),
				wp_strip_all_tags( $top['bg'] ?? '#111827' ),
				wp_strip_all_tags( $top['text'] ?? '#f3f4f6' ),
				wp_strip_all_tags( $top['muted'] ?? '#9ca3af' ),
				wp_strip_all_tags( $top['accent'] ?? '#f59e0b' ),
				wp_strip_all_tags( $middle['bg'] ?? '#ffffff' ),
				wp_strip_all_tags( $middle['text'] ?? '#0f172a' ),
				wp_strip_all_tags( $middle['muted'] ?? '#64748b' ),
				wp_strip_all_tags( $middle['accent'] ?? '#2563eb' ),
				wp_strip_all_tags( $middle['border'] ?? '#e2e8f0' ),
				wp_strip_all_tags( $bottom['bg'] ?? '#111827' ),
				wp_strip_all_tags( $bottom['text'] ?? '#ffffff' ),
				wp_strip_all_tags( $bottom['muted'] ?? '#cbd5e1' ),
				wp_strip_all_tags( $bottom['accent'] ?? '#f59e0b' ),
				wp_strip_all_tags( $bottom['accent_text'] ?? '#111827' ),
				wp_strip_all_tags( $resolved_header_font ),
				wp_strip_all_tags( $typo['top_size'] ?? '13px' ),
				wp_strip_all_tags( $typo['top_weight'] ?? '500' ),
				wp_strip_all_tags( $typo['mid_size'] ?? '14px' ),
				wp_strip_all_tags( $typo['mid_weight'] ?? '400' ),
				wp_strip_all_tags( $typo['nav_size'] ?? '15px' ),
				wp_strip_all_tags( $typo['nav_weight'] ?? '500' ),
				wp_strip_all_tags( $typo['logo_max_h'] ?? '44px' ),
				wp_strip_all_tags( $spacing['container_width'] ?? '1200px' ),
				wp_strip_all_tags( $spacing['side_padding'] ?? '1.15rem' ),
				wp_strip_all_tags( $spacing['top_min_h'] ?? '2.25rem' ),
				wp_strip_all_tags( $spacing['mid_min_h'] ?? '4.5rem' ),
				wp_strip_all_tags( $spacing['bot_min_h'] ?? '3rem' )
			);

			wp_add_inline_style( 'themezur-header', $header_css );
		}

		if ( Themezur_Options::get( 'header.sticky', false ) && 'elementor' === self::resolve_mode( 'header' ) ) {
			wp_add_inline_style(
				'themezur-style',
				'.themezur-header.is-sticky{position:sticky;top:0;z-index:100;}'
			);
		}

		if ( 'theme' === self::resolve_mode( 'footer' ) && wp_style_is( 'themezur-footer', 'enqueued' ) ) {
			$ft = Themezur_Options::get( 'footer', array() );
			$footer_css = sprintf(
				'.tz-site-footer--columns4{--tz-ft-bg:%1$s;--tz-ft-text:%2$s;--tz-ft-muted:%3$s;--tz-ft-accent:%4$s;--tz-ft-border:%5$s;--tz-ft-container:%6$s;--tz-ft-side-pad:%7$s;}',
				wp_strip_all_tags( $ft['bg'] ?? '#0f172a' ),
				wp_strip_all_tags( $ft['text'] ?? '#e2e8f0' ),
				wp_strip_all_tags( $ft['muted'] ?? '#94a3b8' ),
				wp_strip_all_tags( $ft['accent'] ?? '#f59e0b' ),
				wp_strip_all_tags( $ft['border'] ?? '#1e293b' ),
				wp_strip_all_tags( $ft['container_width'] ?? '1200px' ),
				wp_strip_all_tags( $ft['side_padding'] ?? '1.15rem' )
			);
			wp_add_inline_style( 'themezur-footer', $footer_css );
		}

		if ( Themezur_Blog::is_enabled() && wp_style_is( 'themezur-blog', 'enqueued' ) ) {
			$blog = Themezur_Options::get( 'blog', array() );
			$accent = Themezur_Options::get( 'general.accent_color', '#2563eb' );
			$blog_css = sprintf(
				'.tz-blog{--tz-blog-container:%1$s;--tz-blog-single-max:%2$s;--tz-blog-accent:%3$s;}',
				wp_strip_all_tags( $blog['container_width'] ?? '1100px' ),
				wp_strip_all_tags( $blog['single']['content_width'] ?? '720px' ),
				wp_strip_all_tags( $accent )
			);
			wp_add_inline_style( 'themezur-blog', $blog_css );
		}
	}

	/**
	 * Apply Hello Elementor style disable filters from performance settings.
	 *
	 * @return void
	 */
	public static function apply_performance_filters() {
		$perf = Themezur_Options::get( 'performance', array() );

		if ( ! empty( $perf['disable_hello_reset'] ) ) {
			add_filter( 'hello_elementor_enqueue_style', '__return_false' );
		}
		if ( ! empty( $perf['disable_hello_theme_style'] ) ) {
			add_filter( 'hello_elementor_enqueue_theme_style', '__return_false' );
		}
		if ( ! empty( $perf['disable_hello_header_footer_css'] ) ) {
			add_action(
				'wp_enqueue_scripts',
				static function () {
					wp_dequeue_style( 'hello-elementor-header-footer' );
					wp_deregister_style( 'hello-elementor-header-footer' );
				},
				100
			);
		}
	}

	/**
	 * Early performance toggles (emoji, embeds).
	 *
	 * @return void
	 */
	public static function apply_early_performance() {
		$perf = Themezur_Options::get( 'performance', array() );

		if ( ! empty( $perf['disable_emoji'] ) ) {
			remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
			remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
			remove_action( 'wp_print_styles', 'print_emoji_styles' );
			remove_action( 'admin_print_styles', 'print_emoji_styles' );
			remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
			remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
			remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
			add_filter( 'emoji_svg_url', '__return_false' );
		}

		if ( ! empty( $perf['disable_wp_embed'] ) ) {
			add_action(
				'wp_footer',
				static function () {
					wp_deregister_script( 'wp-embed' );
				},
				1
			);
		}

		if ( ! empty( $perf['preload_google_fonts'] ) ) {
			add_filter(
				'wp_resource_hints',
				static function ( $urls, $relation_type ) {
					if ( 'preconnect' === $relation_type ) {
						$urls[] = array(
							'href'        => 'https://fonts.gstatic.com',
							'crossorigin' => 'anonymous',
						);
					}
					return $urls;
				},
				10,
				2
			);
		}

		if ( ! empty( $perf['disable_gutenberg_css'] ) ) {
			add_action(
				'wp_enqueue_scripts',
				static function () {
					wp_dequeue_style( 'wp-block-library' );
					wp_dequeue_style( 'wp-block-library-theme' );
					wp_dequeue_style( 'wc-blocks-style' );
				},
				100
			);
		}

		if ( ! empty( $perf['opt_cart_fragments'] ) ) {
			add_action(
				'wp_enqueue_scripts',
				static function () {
					if ( function_exists( 'is_woocommerce' ) && ! is_woocommerce() && ! is_cart() && ! is_checkout() ) {
						wp_dequeue_script( 'wc-cart-fragments' );
					}
				},
				100
			);
		}

		if ( ! empty( $perf['remove_query_strings'] ) ) {
			$strip_ver = static function ( $src ) {
				if ( strpos( (string) $src, 'ver=' ) ) {
					$src = remove_query_arg( 'ver', $src );
				}
				return $src;
			};
			add_filter( 'script_loader_src', $strip_ver, 15 );
			add_filter( 'style_loader_src', $strip_ver, 15 );
		}

		if ( ! empty( $perf['disable_jquery_migrate'] ) ) {
			add_action(
				'wp_default_scripts',
				static function ( $scripts ) {
					if ( ! is_admin() && ! empty( $scripts->registered['jquery'] ) ) {
						$jquery = $scripts->registered['jquery'];
						if ( isset( $jquery->deps ) && is_array( $jquery->deps ) ) {
							$jquery->deps = array_diff( $jquery->deps, array( 'jquery-migrate' ) );
						}
					}
				}
			);
		}
	}
}
