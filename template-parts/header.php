<?php
/**
 * Themezur triple-row site header with Off-Canvas Drawer.
 *
 * @package Themezur
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$h         = Themezur_Options::get( 'header', array() );
$top       = isset( $h['top'] ) && is_array( $h['top'] ) ? $h['top'] : array();
$middle    = isset( $h['middle'] ) && is_array( $h['middle'] ) ? $h['middle'] : array();
$bottom    = isset( $h['bottom'] ) && is_array( $h['bottom'] ) ? $h['bottom'] : array();
$scroll    = isset( $h['scroll'] ) && is_array( $h['scroll'] ) ? $h['scroll'] : array();
$announce  = isset( $h['announce'] ) && is_array( $h['announce'] ) ? $h['announce'] : array();
$sticky    = ! empty( $h['sticky'] );
$logo_id   = Themezur_Options::get_logo_id();
$site_name = get_bloginfo( 'name' );

$scroll_behavior = isset( $scroll['behavior'] ) ? $scroll['behavior'] : 'none';
$scroll_offset   = isset( $scroll['offset'] ) ? (int) $scroll['offset'] : 40;
$scroll_progress = ! empty( $scroll['progress'] );
if ( 'none' === $scroll_behavior && $sticky ) {
	$scroll_behavior = 'sticky';
}

$show_announce   = Themezur_Frontend::should_show_announce();
$announce_cookie = Themezur_Frontend::announce_cookie_name( $announce['version'] ?? '1' );
$announce_days   = isset( $announce['cookie_days'] ) ? (int) $announce['cookie_days'] : 7;

$nav_menu = '';
if ( ! empty( $bottom['show_menu'] ) ) {
	$nav_menu = wp_nav_menu(
		array(
			'theme_location' => 'menu-1',
			'fallback_cb'    => false,
			'container'      => false,
			'menu_class'     => 'tz-nav-list',
			'echo'           => false,
			'depth'          => 3,
		)
	);
}

$cart_count = 0;
$cart_url   = '';
$mini_cart  = false;
if ( ! empty( $middle['show_cart'] ) && function_exists( 'WC' ) && WC()->cart ) {
	$cart_count = (int) WC()->cart->get_cart_contents_count();
	$cart_url   = wc_get_cart_url();
	$mini_cart  = ! empty( $middle['mini_cart'] );
} elseif ( ! empty( $middle['show_cart'] ) ) {
	$cart_url = home_url( '/cart/' );
}

$account_url   = '';
$account_label = __( 'Account', 'themezur' );
if ( ! empty( $middle['show_account'] ) ) {
	if ( function_exists( 'wc_get_page_permalink' ) ) {
		$account_url = wc_get_page_permalink( 'myaccount' );
	}
	if ( ! $account_url ) {
		$account_url = is_user_logged_in() ? admin_url( 'profile.php' ) : wp_login_url( home_url( '/' ) );
	}
	$account_label = is_user_logged_in()
		? __( 'My account', 'themezur' )
		: __( 'Log in', 'themezur' );
}

$meta_str = '';
if ( ! empty( $top['show_date'] ) ) {
	$mode = isset( $top['date_mode'] ) ? $top['date_mode'] : 'date';
	if ( 'custom' === $mode ) {
		$meta_str = isset( $top['date_custom'] ) ? (string) $top['date_custom'] : '';
	} else {
		$fmt      = ! empty( $top['date_format'] ) ? $top['date_format'] : 'j M Y';
		$meta_str = wp_date( $fmt );
	}
}

$cat_mode = isset( $bottom['categories_mode'] ) ? $bottom['categories_mode'] : 'mega';
$cat_tree = array();
if ( ! empty( $bottom['show_categories'] ) && 'mega' === $cat_mode ) {
	$cat_tree = Themezur_Categories::get_tree( isset( $bottom['categories_limit'] ) ? (int) $bottom['categories_limit'] : 18 );
}
$cat_cols = isset( $bottom['categories_columns'] ) ? (int) $bottom['categories_columns'] : 3;

$smart_search = ! empty( $middle['smart_search'] ) && ! empty( $middle['show_search'] );

$header_classes   = array( 'tz-site-header', 'tz-site-header--triple' );
$header_classes[] = 'tz-scroll--' . sanitize_html_class( $scroll_behavior );
if ( in_array( $scroll_behavior, array( 'sticky', 'shrink', 'bottom_sticky', 'transparent_solid' ), true ) ) {
	$header_classes[] = 'is-sticky-ready';
}

$vis            = array(
	'trending'   => Themezur_Frontend::visibility_classes( $top, 'trending' ),
	'social'     => Themezur_Frontend::visibility_classes( $top, 'social' ),
	'phone'      => Themezur_Frontend::visibility_classes( $top, 'phone' ),
	'date'       => Themezur_Frontend::visibility_classes( $top, 'date' ),
	'logo'       => Themezur_Frontend::visibility_classes( $middle, 'logo' ),
	'address'    => Themezur_Frontend::visibility_classes( $middle, 'address' ),
	'search'     => Themezur_Frontend::visibility_classes( $middle, 'search' ),
	'dark'       => Themezur_Frontend::visibility_classes( $middle, 'dark' ),
	'account'    => Themezur_Frontend::visibility_classes( $middle, 'account' ),
	'wishlist'   => Themezur_Frontend::visibility_classes( $middle, 'wishlist' ),
	'compare'    => Themezur_Frontend::visibility_classes( $middle, 'compare' ),
	'cart'       => Themezur_Frontend::visibility_classes( $middle, 'cart' ),
	'categories'    => Themezur_Frontend::visibility_classes( $bottom, 'categories' ),
	'menu'          => Themezur_Frontend::visibility_classes( $bottom, 'menu' ),
	'deal'          => Themezur_Frontend::visibility_classes( $bottom, 'deal' ),
	'middle_menu'   => Themezur_Frontend::visibility_classes( $middle, 'menu' ),
	'middle_button' => Themezur_Frontend::visibility_classes( $middle, 'button' ),
);
$promo_messages = array();
if ( ! empty( $top['promos'] ) && is_array( $top['promos'] ) ) {
	foreach ( $top['promos'] as $promo ) {
		$text = is_array( $promo ) ? (string) ( $promo['text'] ?? '' ) : (string) $promo;
		$text = trim( $text );
		if ( '' !== $text ) {
			$promo_messages[] = $text;
		}
	}
}
if ( empty( $promo_messages ) && ! empty( $top['trending_text'] ) ) {
	$promo_messages[] = (string) $top['trending_text'];
}
$promo_rotate   = ! empty( $top['promo_rotate'] ) && count( $promo_messages ) > 1;
$promo_interval = isset( $top['promo_interval'] ) ? (int) $top['promo_interval'] : 4;

$wishlist_url = '';
if ( ! empty( $middle['show_wishlist'] ) ) {
	if ( ! empty( $middle['wishlist_url'] ) ) {
		$wishlist_url = $middle['wishlist_url'];
	} elseif ( function_exists( 'YITH_WCWL' ) && function_exists( 'yith_wcwl_get_wishlist_url' ) ) {
		$wishlist_url = yith_wcwl_get_wishlist_url();
	} elseif ( function_exists( 'wc_get_page_id' ) ) {
		$page_id = (int) get_option( 'themezur_wishlist_page_id', 0 );
		if ( $page_id > 0 ) {
			$wishlist_url = get_permalink( $page_id );
		}
	}
}
$compare_url = ! empty( $middle['show_compare'] ) && ! empty( $middle['compare_url'] ) ? $middle['compare_url'] : '';
?>
<header
	id="themezur-header"
	class="<?php echo esc_attr( implode( ' ', $header_classes ) ); ?>"
	data-tz-header
	data-tz-scroll="<?php echo esc_attr( $scroll_behavior ); ?>"
	data-tz-scroll-offset="<?php echo esc_attr( (string) $scroll_offset ); ?>"
>
	<?php if ( $show_announce ) : ?>
		<div
			class="tz-announce"
			data-tz-announce
			data-tz-announce-key="<?php echo esc_attr( $announce_cookie ); ?>"
			data-tz-announce-days="<?php echo esc_attr( (string) $announce_days ); ?>"
			<?php echo ! empty( $announce['dismissible'] ) ? 'data-tz-announce-dismissible' : ''; ?>
		>
			<div class="tz-announce__inner">
				<p class="tz-announce__text"><?php echo esc_html( $announce['text'] ); ?></p>
				<?php if ( ! empty( $announce['link_url'] ) && ! empty( $announce['link_text'] ) ) : ?>
					<a class="tz-announce__link" href="<?php echo esc_url( $announce['link_url'] ); ?>">
						<?php echo esc_html( $announce['link_text'] ); ?>
					</a>
				<?php endif; ?>
				<?php if ( ! empty( $announce['dismissible'] ) ) : ?>
					<button type="button" class="tz-announce__dismiss" data-tz-announce-dismiss aria-label="<?php echo esc_attr__( 'Dismiss announcement', 'themezur' ); ?>">
						<span aria-hidden="true">&times;</span>
					</button>
				<?php endif; ?>
			</div>
		</div>
	<?php endif; ?>

	<?php if ( ! empty( $top['enabled'] ) ) : ?>
		<div class="tz-header-top">
			<div class="tz-header-top__inner">
				<div class="tz-header-top__left">
					<?php if ( ! empty( $top['show_trending'] ) && ( ! empty( $top['trending_label'] ) || ! empty( $promo_messages ) ) ) : ?>
						<div
							class="tz-header-top__trending<?php echo $vis['trending'] ? ' ' . esc_attr( $vis['trending'] ) : ''; ?>"
							<?php
							if ( $promo_rotate ) {
								echo ' data-tz-promo-rotate';
								echo ' data-tz-promo-interval="' . esc_attr( (string) $promo_interval ) . '"';
								echo ' data-tz-promo-messages="' . esc_attr( wp_json_encode( array_values( $promo_messages ) ) ) . '"';
							}
							?>
						>
							<?php if ( ! empty( $top['trending_label'] ) ) : ?>
								<span class="tz-header-top__trending-label">
									<span class="tz-header-top__flame" aria-hidden="true"></span>
									<?php echo esc_html( $top['trending_label'] ); ?>
								</span>
							<?php endif; ?>
							<?php if ( ! empty( $promo_messages ) ) : ?>
								<span class="tz-header-top__trending-text" data-tz-promo-text><?php echo esc_html( $promo_messages[0] ); ?></span>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</div>
				<div class="tz-header-top__right">
					<?php if ( ! empty( $top['show_phone'] ) && ! empty( $top['phone_number'] ) ) : ?>
						<?php
						$phone_display = (string) $top['phone_number'];
						$phone_tel     = preg_replace( '/[^\d+]/', '', $phone_display );
						$phone_label   = ! empty( $top['phone_label'] ) ? (string) $top['phone_label'] : '';
						?>
						<?php if ( $phone_tel ) : ?>
							<a
								class="tz-header-top__phone<?php echo $vis['phone'] ? ' ' . esc_attr( $vis['phone'] ) : ''; ?>"
								href="<?php echo esc_url( 'tel:' . $phone_tel ); ?>"
							>
								<span class="tz-header-top__phone-icon" aria-hidden="true"></span>
								<?php if ( $phone_label ) : ?>
									<span class="tz-header-top__phone-label"><?php echo esc_html( $phone_label ); ?></span>
								<?php endif; ?>
								<span class="tz-header-top__phone-number"><?php echo esc_html( $phone_display ); ?></span>
							</a>
						<?php endif; ?>
					<?php endif; ?>
					<?php if ( ! empty( $top['show_social'] ) ) : ?>
						<?php
						$socials = Themezur_Social::normalize_list( $top );
						?>
						<?php if ( ! empty( $socials ) ) : ?>
							<div class="tz-header-top__social<?php echo $vis['social'] ? ' ' . esc_attr( $vis['social'] ) : ''; ?>">
								<?php foreach ( $socials as $social ) : ?>
									<?php
									$network = $social['network'];
									$label   = Themezur_Social::networks()[ $network ] ?? $network;
									$href    = $social['url'];
									if ( 'email' === $network ) {
										$email = sanitize_email( str_replace( 'mailto:', '', $href ) );
										$href  = $email ? 'mailto:' . $email : $href;
									}
									?>
									<a
										class="tz-social tz-social--<?php echo esc_attr( $network ); ?>"
										href="<?php echo esc_url( $href ); ?>"
										<?php echo ( 'email' === $network ) ? '' : 'target="_blank" rel="noopener noreferrer"'; ?>
										aria-label="<?php echo esc_attr( $label ); ?>"
									>
										<?php echo Themezur_Social::icon_svg( $network ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted SVG paths. ?>
									</a>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
					<?php endif; ?>
					<?php if ( '' !== trim( $meta_str ) ) : ?>
						<span class="tz-header-top__date<?php echo $vis['date'] ? ' ' . esc_attr( $vis['date'] ) : ''; ?>"><?php echo esc_html( $meta_str ); ?></span>
					<?php endif; ?>
				</div>
			</div>
		</div>
	<?php endif; ?>

	<?php if ( ! empty( $middle['enabled'] ) ) : ?>
		<div class="tz-header-middle">
			<div class="tz-header-middle__inner">
				<div class="tz-header-middle__brand">
					<?php if ( ! empty( $middle['show_logo'] ) ) : ?>
						<?php if ( $logo_id ) : ?>
							<a class="tz-header-middle__logo<?php echo $vis['logo'] ? ' ' . esc_attr( $vis['logo'] ) : ''; ?>" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
								<?php
								echo wp_get_attachment_image(
									$logo_id,
									'full',
									false,
									array(
										'class'    => 'tz-header-middle__logo-img',
										'alt'      => $site_name ? $site_name : __( 'Site logo', 'themezur' ),
										'loading'  => 'eager',
										'decoding' => 'async',
									)
								);
								?>
							</a>
						<?php elseif ( $site_name ) : ?>
							<a class="tz-header-middle__title<?php echo $vis['logo'] ? ' ' . esc_attr( $vis['logo'] ) : ''; ?>" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
								<?php echo esc_html( $site_name ); ?>
							</a>
						<?php endif; ?>
					<?php endif; ?>

					<?php if ( ! empty( $middle['show_address'] ) && ! empty( $middle['address'] ) ) : ?>
						<span class="tz-header-middle__address<?php echo $vis['address'] ? ' ' . esc_attr( $vis['address'] ) : ''; ?>"><?php echo esc_html( $middle['address'] ); ?></span>
					<?php endif; ?>
				</div>

				<?php if ( ! empty( $middle['show_search'] ) ) : ?>
					<div class="tz-header-middle__search-wrap<?php echo $vis['search'] ? ' ' . esc_attr( $vis['search'] ) : ''; ?>" <?php echo $smart_search ? 'data-tz-smart-search' : ''; ?>>
						<form class="tz-header-middle__search" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" <?php echo $smart_search && post_type_exists( 'product' ) ? 'data-tz-product-search-form' : ''; ?>>
							<label class="screen-reader-text" for="tz-header-search"><?php echo esc_html__( 'Search', 'themezur' ); ?></label>
							<?php if ( $smart_search && post_type_exists( 'product' ) ) : ?>
								<input type="hidden" name="post_type" value="product" />
							<?php endif; ?>
							<input
								id="tz-header-search"
								type="search"
								name="s"
								value="<?php echo esc_attr( get_search_query() ); ?>"
								placeholder="<?php echo esc_attr( ! empty( $middle['search_placeholder'] ) ? $middle['search_placeholder'] : __( 'Search…', 'themezur' ) ); ?>"
								autocomplete="off"
								<?php if ( $smart_search ) : ?>
									role="combobox"
									aria-autocomplete="list"
									aria-controls="tz-search-suggestions"
									aria-expanded="false"
									data-tz-search-input
								<?php endif; ?>
							/>
							<button type="button" class="tz-header-middle__search-clear" data-tz-search-clear aria-label="<?php echo esc_attr__( 'Clear search', 'themezur' ); ?>" hidden>×</button>
							<button type="submit" class="tz-header-middle__search-btn" aria-label="<?php echo esc_attr__( 'Search', 'themezur' ); ?>">
								<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg>
							</button>
						</form>
						<?php if ( $smart_search ) : ?>
							<div
								id="tz-search-suggestions"
								class="tz-search-suggest"
								role="listbox"
								aria-label="<?php echo esc_attr__( 'Product suggestions', 'themezur' ); ?>"
								hidden
								data-tz-search-suggest
							></div>
						<?php endif; ?>
					<?php if ( ! empty( $middle['show_menu'] ) ) : ?>
					<?php
					$middle_nav = wp_nav_menu(
						array(
							'theme_location' => 'menu-1',
							'fallback_cb'    => false,
							'container'      => false,
							'menu_class'     => 'tz-nav-list tz-header-middle__nav-list',
							'echo'           => false,
							'depth'          => 3,
						)
					);
					?>
					<?php if ( $middle_nav ) : ?>
						<nav class="tz-header-middle__nav<?php echo $vis['middle_menu'] ? ' ' . esc_attr( $vis['middle_menu'] ) : ''; ?>">
							<?php echo $middle_nav; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</nav>
					<?php endif; ?>
				<?php endif; ?>

				<div class="tz-header-middle__actions">
					<?php if ( ! empty( $middle['show_button'] ) && ! empty( $middle['button_text'] ) ) : ?>
						<a
							class="tz-btn tz-header-middle__btn<?php echo $vis['middle_button'] ? ' ' . esc_attr( $vis['middle_button'] ) : ''; ?>"
							href="<?php echo esc_url( ! empty( $middle['button_url'] ) ? $middle['button_url'] : '#' ); ?>"
							target="<?php echo esc_attr( ! empty( $middle['button_target'] ) ? $middle['button_target'] : '_self' ); ?>"
						>
							<?php echo esc_html( $middle['button_text'] ); ?>
						</a>
					<?php endif; ?>

					<?php if ( ! empty( $middle['show_dark_mode'] ) ) : ?>
						<button type="button" class="tz-icon-btn<?php echo $vis['dark'] ? ' ' . esc_attr( $vis['dark'] ) : ''; ?>" data-tz-dark-toggle aria-label="<?php echo esc_attr__( 'Toggle dark mode', 'themezur' ); ?>">
							<svg class="tz-icon-moon" width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M21 14.3A8.5 8.5 0 119.7 3a7 7 0 0011.3 11.3z"/></svg>
						</button>
					<?php endif; ?>

					<?php if ( ! empty( $middle['show_account'] ) && $account_url ) : ?>
						<a
							class="tz-icon-btn tz-account-btn<?php echo $vis['account'] ? ' ' . esc_attr( $vis['account'] ) : ''; ?><?php echo is_user_logged_in() ? ' is-logged-in' : ''; ?>"
							href="<?php echo esc_url( $account_url ); ?>"
							aria-label="<?php echo esc_attr( $account_label ); ?>"
						>
							<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 20c1.5-3.5 4.5-5 8-5s6.5 1.5 8 5"/></svg>
						</a>
					<?php endif; ?>

					<?php if ( ! empty( $middle['show_wishlist'] ) && $wishlist_url ) : ?>
						<a class="tz-icon-btn tz-wishlist-btn<?php echo $vis['wishlist'] ? ' ' . esc_attr( $vis['wishlist'] ) : ''; ?>" href="<?php echo esc_url( $wishlist_url ); ?>" aria-label="<?php echo esc_attr__( 'Wishlist', 'themezur' ); ?>">
							<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 21s-7-4.5-9.5-9A5.5 5.5 0 0112 6a5.5 5.5 0 019.5 6C19 16.5 12 21 12 21z"/></svg>
							<span class="tz-wl-count" hidden></span>
						</a>
					<?php endif; ?>

					<?php if ( ! empty( $middle['show_compare'] ) && $compare_url ) : ?>
						<a class="tz-icon-btn tz-compare-btn<?php echo $vis['compare'] ? ' ' . esc_attr( $vis['compare'] ) : ''; ?>" href="<?php echo esc_url( $compare_url ); ?>" aria-label="<?php echo esc_attr__( 'Compare', 'themezur' ); ?>">
							<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M8 7h12M8 12h12M8 17h12"/><path d="M4 7h.01M4 12h.01M4 17h.01"/></svg>
						</a>
					<?php endif; ?>

					<?php if ( ! empty( $middle['show_cart'] ) ) : ?>
						<a
							class="tz-icon-btn tz-cart-btn<?php echo $vis['cart'] ? ' ' . esc_attr( $vis['cart'] ) : ''; ?>"
							href="<?php echo esc_url( $cart_url ); ?>"
							<?php if ( $mini_cart ) : ?>
								aria-controls="tz-mini-cart"
								aria-expanded="false"
								data-tz-mini-cart-toggle
							<?php endif; ?>
						>
							<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M6 7h15l-1.5 9h-12z"/><path d="M6 7L5 3H2"/><circle cx="9" cy="20" r="1.5"/><circle cx="17" cy="20" r="1.5"/></svg>
							<?php echo Themezur_Frontend::cart_count_markup( $cart_count ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in helper. ?>
						</a>
					<?php endif; ?>

					<?php if ( $nav_menu || ! empty( $bottom['show_categories'] ) ) : ?>
						<button type="button" class="tz-icon-btn tz-nav-toggle" aria-expanded="false" aria-controls="tz-mobile-drawer" data-tz-nav-toggle aria-label="<?php echo esc_attr__( 'Menu', 'themezur' ); ?>">
							<span class="tz-nav-toggle__bars" aria-hidden="true"></span>
						</button>
					<?php endif; ?>
				</div>
			</div>
		</div>
	<?php endif; ?>

	<?php if ( ! empty( $bottom['enabled'] ) ) : ?>
		<div class="tz-header-bottom">
			<div class="tz-header-bottom__inner">
				<?php if ( ! empty( $bottom['show_categories'] ) && ! empty( $bottom['categories_text'] ) ) : ?>
					<?php if ( 'mega' === $cat_mode && ! empty( $cat_tree ) ) : ?>
						<div class="tz-cats<?php echo $vis['categories'] ? ' ' . esc_attr( $vis['categories'] ) : ''; ?>" data-tz-cats>
							<button type="button" class="tz-header-bottom__cats" aria-expanded="false" aria-controls="tz-cats-panel" data-tz-cats-toggle>
								<?php echo esc_html( $bottom['categories_text'] ); ?>
								<span class="tz-cats__caret" aria-hidden="true"></span>
							</button>
							<div id="tz-cats-panel" class="tz-cats__panel" hidden data-tz-cats-panel style="--tz-cats-cols: <?php echo esc_attr( (string) $cat_cols ); ?>">
								<?php foreach ( $cat_tree as $parent ) : ?>
									<div class="tz-cats__col">
										<a class="tz-cats__parent" href="<?php echo esc_url( $parent['url'] ); ?>"><?php echo esc_html( $parent['name'] ); ?></a>
										<?php if ( ! empty( $parent['children'] ) ) : ?>
											<ul class="tz-cats__children">
												<?php foreach ( $parent['children'] as $child ) : ?>
													<li><a href="<?php echo esc_url( $child['url'] ); ?>"><?php echo esc_html( $child['name'] ); ?></a></li>
												<?php endforeach; ?>
											</ul>
										<?php endif; ?>
									</div>
								<?php endforeach; ?>
							</div>
						</div>
					<?php else : ?>
						<?php
						$cat_url = ! empty( $bottom['categories_url'] ) ? $bottom['categories_url'] : ( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' ) );
						?>
						<a class="tz-header-bottom__cats<?php echo $vis['categories'] ? ' ' . esc_attr( $vis['categories'] ) : ''; ?>" href="<?php echo esc_url( $cat_url ); ?>">
							<?php echo esc_html( $bottom['categories_text'] ); ?>
						</a>
					<?php endif; ?>
				<?php endif; ?>

				<?php if ( $nav_menu ) : ?>
					<nav class="tz-header-bottom__nav<?php echo $vis['menu'] ? ' ' . esc_attr( $vis['menu'] ) : ''; ?>" aria-label="<?php echo esc_attr__( 'Main menu', 'themezur' ); ?>">
						<?php echo $nav_menu; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</nav>
				<?php endif; ?>

				<?php if ( ! empty( $bottom['show_deal'] ) && ! empty( $bottom['deal_text'] ) ) : ?>
					<?php
					$deal_url = ! empty( $bottom['deal_url'] ) ? $bottom['deal_url'] : '#';
					?>
					<a class="tz-header-bottom__deal<?php echo $vis['deal'] ? ' ' . esc_attr( $vis['deal'] ) : ''; ?>" href="<?php echo esc_url( $deal_url ); ?>">
						<span class="tz-header-bottom__deal-icon" aria-hidden="true"></span>
						<?php echo esc_html( $bottom['deal_text'] ); ?>
					</a>
				<?php endif; ?>
			</div>
		</div>
	<?php endif; ?>

	<!-- Mobile Off-Canvas Drawer -->
	<?php if ( $nav_menu || ! empty( $bottom['show_categories'] ) ) : ?>
		<div id="tz-mobile-drawer" class="tz-mobile-drawer" hidden data-tz-mobile-drawer>
			<div class="tz-mobile-drawer__overlay" data-tz-drawer-close aria-hidden="true"></div>
			<div class="tz-mobile-drawer__panel" role="dialog" aria-modal="true" aria-label="<?php echo esc_attr__( 'Navigation Menu', 'themezur' ); ?>">
				<div class="tz-mobile-drawer__header">
					<div class="tz-mobile-drawer__brand">
						<?php if ( $logo_id ) : ?>
							<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
								<?php
								echo wp_get_attachment_image(
									$logo_id,
									'full',
									false,
									array(
										'class' => 'tz-mobile-drawer__logo-img',
										'alt'   => $site_name ? $site_name : __( 'Site logo', 'themezur' ),
									)
								);
								?>
							</a>
						<?php else : ?>
							<span class="tz-mobile-drawer__title"><?php echo esc_html( $site_name ); ?></span>
						<?php endif; ?>
					</div>
					<button type="button" class="tz-mobile-drawer__close" data-tz-drawer-close aria-label="<?php echo esc_attr__( 'Close menu', 'themezur' ); ?>">
						<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
					</button>
				</div>
				<div class="tz-mobile-drawer__body">
					<?php if ( ! empty( $middle['show_search'] ) ) : ?>
						<form class="tz-mobile-drawer__search" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
							<input type="search" name="s" placeholder="<?php echo esc_attr( ! empty( $middle['search_placeholder'] ) ? $middle['search_placeholder'] : __( 'Search…', 'themezur' ) ); ?>" autocomplete="off" />
							<button type="submit" aria-label="<?php echo esc_attr__( 'Search', 'themezur' ); ?>">
								<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg>
							</button>
						</form>
					<?php endif; ?>

					<?php if ( $nav_menu ) : ?>
						<nav class="tz-mobile-drawer__nav" aria-label="<?php echo esc_attr__( 'Mobile navigation', 'themezur' ); ?>">
							<?php echo $nav_menu; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</nav>
					<?php endif; ?>

					<?php if ( ! empty( $bottom['show_deal'] ) && ! empty( $bottom['deal_text'] ) ) : ?>
						<a class="tz-header-bottom__deal tz-mobile-drawer__deal" href="<?php echo esc_url( ! empty( $bottom['deal_url'] ) ? $bottom['deal_url'] : '#' ); ?>">
							<span class="tz-header-bottom__deal-icon" aria-hidden="true"></span>
							<?php echo esc_html( $bottom['deal_text'] ); ?>
						</a>
					<?php endif; ?>
				</div>

				<div class="tz-mobile-drawer__footer">
					<?php if ( ! empty( $middle['show_account'] ) && $account_url ) : ?>
						<a class="tz-mobile-drawer__footer-btn" href="<?php echo esc_url( $account_url ); ?>">
							<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 20c1.5-3.5 4.5-5 8-5s6.5 1.5 8 5"/></svg>
							<span><?php echo esc_html( $account_label ); ?></span>
						</a>
					<?php endif; ?>
					<?php if ( ! empty( $middle['show_wishlist'] ) && $wishlist_url ) : ?>
						<a class="tz-mobile-drawer__footer-btn" href="<?php echo esc_url( $wishlist_url ); ?>">
							<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 21s-7-4.5-9.5-9A5.5 5.5 0 0112 6a5.5 5.5 0 019.5 6C19 16.5 12 21 12 21z"/></svg>
							<span><?php esc_html_e( 'Wishlist', 'themezur' ); ?></span>
						</a>
					<?php endif; ?>
				</div>
			</div>
		</div>
	<?php endif; ?>

	<?php if ( $scroll_progress ) : ?>
		<div class="tz-scroll-progress" data-tz-scroll-progress aria-hidden="true">
			<span class="tz-scroll-progress__bar" data-tz-scroll-progress-bar></span>
		</div>
	<?php endif; ?>
</header>

<?php if ( $mini_cart ) : ?>
	<div id="tz-mini-cart" class="tz-mini-cart" hidden data-tz-mini-cart>
		<div class="tz-mini-cart__overlay" aria-hidden="true" data-tz-mini-cart-close></div>
		<aside class="tz-mini-cart__panel" role="dialog" aria-modal="true" aria-labelledby="tz-mini-cart-title" tabindex="-1" data-tz-mini-cart-panel>
			<div class="tz-mini-cart__header">
				<h2 id="tz-mini-cart-title" class="tz-mini-cart__title"><?php esc_html_e( 'Your cart', 'themezur' ); ?></h2>
				<button type="button" class="tz-mini-cart__close" aria-label="<?php echo esc_attr__( 'Close cart', 'themezur' ); ?>" data-tz-mini-cart-close>
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<?php echo Themezur_Frontend::mini_cart_markup(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- WooCommerce template output. ?>
		</aside>
	</div>
<?php endif; ?>
