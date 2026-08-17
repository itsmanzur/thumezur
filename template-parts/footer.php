<?php
/**
 * Themezur 4-column store footer.
 *
 * Enabled columns flex-grow to fill remaining space.
 *
 * @package Themezur
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$f       = Themezur_Options::get( 'footer', array() );
$columns = isset( $f['columns'] ) && is_array( $f['columns'] ) ? $f['columns'] : array();
$bottom  = isset( $f['bottom'] ) && is_array( $f['bottom'] ) ? $f['bottom'] : array();
$logo_id = Themezur_Options::get_logo_id();
$site_name = get_bloginfo( 'name' );

$order = isset( $f['column_order'] ) && is_array( $f['column_order'] ) ? $f['column_order'] : array( '1', '2', '3', '4', '5' );
$widths = isset( $f['column_widths'] ) ? sanitize_key( $f['column_widths'] ) : 'equal';
$active_cols = array();
foreach ( $order as $key ) {
	$key = (string) $key;
	if ( ! empty( $columns[ $key ]['enabled'] ) ) {
		$active_cols[ $key ] = $columns[ $key ];
	}
}
// Append any enabled cols missing from order.
foreach ( array( '1', '2', '3', '4', '5' ) as $key ) {
	if ( ! isset( $active_cols[ $key ] ) && ! empty( $columns[ $key ]['enabled'] ) ) {
		$active_cols[ $key ] = $columns[ $key ];
	}
}

$col_count = count( $active_cols );
if ( $col_count < 1 && empty( $bottom['enabled'] ) ) {
	return;
}

/**
 * Render one footer column body.
 *
 * @param array $col Column options.
 * @return void
 */
$render_col = static function ( $col ) use ( $logo_id, $site_name ) {
	$type = isset( $col['type'] ) ? $col['type'] : 'about';

	if ( 'about' === $type ) {
		$logo_source = isset( $col['logo_source'] ) ? $col['logo_source'] : ( ! empty( $col['show_logo'] ) ? 'site' : 'none' );
		$display_id  = 0;
		if ( 'site' === $logo_source ) {
			$display_id = $logo_id;
		} elseif ( 'custom' === $logo_source ) {
			$display_id = isset( $col['logo_id'] ) ? (int) $col['logo_id'] : 0;
		}
		if ( $display_id > 0 ) {
			echo '<div class="tz-footer-col__logo">';
			echo wp_get_attachment_image(
				$display_id,
				'medium',
				false,
				array(
					'class' => 'tz-footer-col__logo-img',
					'alt'   => $site_name ? $site_name : __( 'Logo', 'themezur' ),
				)
			);
			echo '</div>';
		} elseif ( 'site' === $logo_source && $site_name ) {
			echo '<p class="tz-footer-col__brand">' . esc_html( $site_name ) . '</p>';
		}
		if ( ! empty( $col['text'] ) ) {
			$paras = preg_split( '/\r\n|\r|\n/', (string) $col['text'] );
			foreach ( $paras as $para ) {
				$para = trim( $para );
				if ( '' !== $para ) {
					echo '<p class="tz-footer-col__text">' . esc_html( $para ) . '</p>';
				}
			}
		}
		if ( ! empty( $col['cta_label'] ) && ! empty( $col['cta_url'] ) ) {
			echo '<p class="tz-footer-col__cta"><a class="tz-footer-cta" href="' . esc_url( $col['cta_url'] ) . '">' . esc_html( $col['cta_label'] ) . '</a></p>';
		}
		if ( ! empty( $col['show_social'] ) ) {
			$socials = Themezur_Social::normalize_list( Themezur_Options::get( 'header.top', array() ) );
			if ( ! empty( $socials ) ) {
				echo '<div class="tz-footer-col__social">';
				foreach ( $socials as $social ) {
					$network = $social['network'];
					$label   = Themezur_Social::networks()[ $network ] ?? $network;
					$href    = $social['url'];
					if ( 'email' === $network ) {
						$email = sanitize_email( str_replace( 'mailto:', '', $href ) );
						$href  = $email ? 'mailto:' . $email : $href;
					}
					echo '<a class="tz-footer-social tz-footer-social--' . esc_attr( $network ) . '" href="' . esc_url( $href ) . '"';
					echo ( 'email' === $network ) ? '' : ' target="_blank" rel="noopener noreferrer"';
					echo ' aria-label="' . esc_attr( $label ) . '">';
					echo Themezur_Social::icon_svg( $network ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo '</a>';
				}
				echo '</div>';
			}
		}
		return;
	}

	if ( 'menu' === $type ) {
		$menu_id = isset( $col['menu_id'] ) ? (int) $col['menu_id'] : 0;
		if ( $menu_id > 0 ) {
			wp_nav_menu(
				array(
					'menu'        => $menu_id,
					'container'   => false,
					'menu_class'  => 'tz-footer-menu',
					'depth'       => 1,
					'fallback_cb' => false,
				)
			);
		} else {
			echo '<p class="tz-footer-col__hint">' . esc_html__( 'Assign a menu in Themezur → Footer.', 'themezur' ) . '</p>';
		}
		return;
	}

	if ( 'links' === $type ) {
		$links = isset( $col['links'] ) && is_array( $col['links'] ) ? $col['links'] : array();
		if ( empty( $links ) ) {
			echo '<p class="tz-footer-col__hint">' . esc_html__( 'Add links in Themezur → Footer.', 'themezur' ) . '</p>';
			return;
		}
		echo '<ul class="tz-footer-links">';
		foreach ( $links as $link ) {
			$label = isset( $link['label'] ) ? (string) $link['label'] : '';
			$url   = ! empty( $link['url'] ) ? (string) $link['url'] : '#';
			if ( '' === $label ) {
				continue;
			}
			echo '<li><a href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a></li>';
		}
		echo '</ul>';
		return;
	}

	if ( 'contact' === $type ) {
		$has = false;
		if ( ! empty( $col['address'] ) ) {
			$has = true;
			echo '<p class="tz-footer-contact__row tz-footer-contact__address">' . nl2br( esc_html( $col['address'] ) ) . '</p>';
		}
		if ( ! empty( $col['phone'] ) ) {
			$has = true;
			$tel = preg_replace( '/[^\d+]/', '', (string) $col['phone'] );
			echo '<p class="tz-footer-contact__row"><span class="tz-footer-contact__label">' . esc_html__( 'Phone', 'themezur' ) . '</span> <a href="' . esc_url( 'tel:' . $tel ) . '">' . esc_html( $col['phone'] ) . '</a></p>';
		}
		if ( ! empty( $col['whatsapp'] ) ) {
			$has = true;
			$wa  = preg_replace( '/\D+/', '', (string) $col['whatsapp'] );
			echo '<p class="tz-footer-contact__row"><span class="tz-footer-contact__label">' . esc_html__( 'WhatsApp', 'themezur' ) . '</span> <a href="' . esc_url( 'https://wa.me/' . $wa ) . '" target="_blank" rel="noopener noreferrer">' . esc_html( $col['whatsapp'] ) . '</a></p>';
		}
		if ( ! empty( $col['email'] ) ) {
			$has = true;
			echo '<p class="tz-footer-contact__row"><span class="tz-footer-contact__label">' . esc_html__( 'Email', 'themezur' ) . '</span> <a href="' . esc_url( 'mailto:' . $col['email'] ) . '">' . esc_html( $col['email'] ) . '</a></p>';
		}
		if ( ! empty( $col['hours'] ) ) {
			$has = true;
			echo '<p class="tz-footer-contact__row tz-footer-contact__hours"><span class="tz-footer-contact__label">' . esc_html__( 'Hours', 'themezur' ) . '</span><br>' . nl2br( esc_html( $col['hours'] ) ) . '</p>';
		}
		if ( ! empty( $col['map_url'] ) ) {
			$has = true;
			echo '<p class="tz-footer-contact__row"><a class="tz-footer-cta tz-footer-cta--ghost" href="' . esc_url( $col['map_url'] ) . '" target="_blank" rel="noopener noreferrer">' . esc_html__( 'View on map', 'themezur' ) . '</a></p>';
		}
		if ( ! $has ) {
			echo '<p class="tz-footer-col__hint">' . esc_html__( 'Add contact details in Themezur → Footer.', 'themezur' ) . '</p>';
		}
		return;
	}

	if ( 'posts' === $type ) {
		$posts = Themezur_Footer::query_posts( $col );
		if ( empty( $posts ) ) {
			echo '<p class="tz-footer-col__hint">' . esc_html__( 'No posts found.', 'themezur' ) . '</p>';
			return;
		}
		$show_thumb = ! isset( $col['posts_show_thumb'] ) || ! empty( $col['posts_show_thumb'] );
		$show_date  = ! isset( $col['posts_show_date'] ) || ! empty( $col['posts_show_date'] );
		echo '<ul class="tz-footer-posts">';
		foreach ( $posts as $post ) {
			$pid = (int) $post->ID;
			echo '<li class="tz-footer-posts__item">';
			if ( $show_thumb && has_post_thumbnail( $pid ) ) {
				echo '<a class="tz-footer-posts__thumb" href="' . esc_url( get_permalink( $pid ) ) . '">';
				echo get_the_post_thumbnail( $pid, 'thumbnail', array( 'class' => 'tz-footer-posts__img', 'alt' => esc_attr( get_the_title( $pid ) ) ) );
				echo '</a>';
			}
			echo '<div class="tz-footer-posts__meta">';
			echo '<a class="tz-footer-posts__title" href="' . esc_url( get_permalink( $pid ) ) . '">' . esc_html( get_the_title( $pid ) ) . '</a>';
			if ( $show_date ) {
				echo '<time class="tz-footer-posts__date" datetime="' . esc_attr( get_the_date( DATE_W3C, $pid ) ) . '">' . esc_html( get_the_date( '', $pid ) ) . '</time>';
			}
			echo '</div></li>';
		}
		echo '</ul>';
		return;
	}

	if ( 'products' === $type ) {
		if ( ! class_exists( 'WooCommerce' ) ) {
			echo '<p class="tz-footer-col__hint">' . esc_html__( 'WooCommerce is not active.', 'themezur' ) . '</p>';
			return;
		}
		$products = Themezur_Footer::query_products( $col );
		if ( empty( $products ) ) {
			echo '<p class="tz-footer-col__hint">' . esc_html__( 'No products found.', 'themezur' ) . '</p>';
			return;
		}
		$show_thumb = ! isset( $col['products_show_thumb'] ) || ! empty( $col['products_show_thumb'] );
		$show_price = ! isset( $col['products_show_price'] ) || ! empty( $col['products_show_price'] );
		echo '<ul class="tz-footer-products">';
		foreach ( $products as $product ) {
			if ( ! $product instanceof WC_Product ) {
				continue;
			}
			echo '<li class="tz-footer-products__item">';
			if ( $show_thumb ) {
				$img = $product->get_image(
					'woocommerce_gallery_thumbnail',
					array(
						'class' => 'tz-footer-products__img',
					)
				);
				echo '<a class="tz-footer-products__thumb" href="' . esc_url( $product->get_permalink() ) . '">' . $img . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			echo '<div class="tz-footer-products__meta">';
			echo '<a class="tz-footer-products__title" href="' . esc_url( $product->get_permalink() ) . '">' . esc_html( $product->get_name() ) . '</a>';
			if ( $show_price ) {
				echo '<span class="tz-footer-products__price">' . wp_kses_post( $product->get_price_html() ) . '</span>';
			}
			echo '</div></li>';
		}
		echo '</ul>';
		return;
	}

	if ( 'shortcode' === $type ) {
		$content = isset( $col['shortcode'] ) ? (string) $col['shortcode'] : '';
		if ( '' === trim( $content ) ) {
			echo '<p class="tz-footer-col__hint">' . esc_html__( 'Add a shortcode or HTML in Themezur → Footer.', 'themezur' ) . '</p>';
			return;
		}
		echo '<div class="tz-footer-shortcode">';
		echo do_shortcode( $content ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- sanitized on save via wp_kses_post.
		echo '</div>';
		return;
	}

	if ( 'newsletter' === $type ) {
		$mode = isset( $col['newsletter_mode'] ) ? $col['newsletter_mode'] : 'form';
		if ( ! empty( $col['newsletter_text'] ) ) {
			echo '<p class="tz-footer-col__text">' . esc_html( $col['newsletter_text'] ) . '</p>';
		}
		if ( 'shortcode' === $mode ) {
			$sc = isset( $col['newsletter_shortcode'] ) ? (string) $col['newsletter_shortcode'] : '';
			if ( '' === trim( $sc ) ) {
				echo '<p class="tz-footer-col__hint">' . esc_html__( 'Paste a newsletter shortcode in Themezur → Footer.', 'themezur' ) . '</p>';
				return;
			}
			echo '<div class="tz-footer-newsletter tz-footer-newsletter--shortcode">';
			echo do_shortcode( $sc ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo '</div>';
			return;
		}
		$action      = isset( $col['newsletter_action'] ) ? (string) $col['newsletter_action'] : '';
		$placeholder = ! empty( $col['newsletter_placeholder'] ) ? $col['newsletter_placeholder'] : __( 'Your email', 'themezur' );
		$button      = ! empty( $col['newsletter_button'] ) ? $col['newsletter_button'] : __( 'Subscribe', 'themezur' );
		$email_name  = ! empty( $col['newsletter_email_name'] ) ? $col['newsletter_email_name'] : 'EMAIL';
		echo '<form class="tz-footer-newsletter" method="post" data-tz-newsletter';
		if ( $action ) {
			echo ' action="' . esc_url( $action ) . '" data-tz-nl-action="' . esc_url( $action ) . '" data-tz-nl-email-name="' . esc_attr( $email_name ) . '"';
		} else {
			echo ' action="#" data-tz-nl-empty="1"';
		}
		echo '>';
		$uid = 'tz-ft-nl-' . uniqid();
		echo '<label class="screen-reader-text" for="' . esc_attr( $uid ) . '">' . esc_html( $placeholder ) . '</label>';
		echo '<div class="tz-footer-newsletter__row">';
		echo '<input id="' . esc_attr( $uid ) . '" type="email" name="' . esc_attr( $email_name ) . '" placeholder="' . esc_attr( $placeholder ) . '" required autocomplete="email">';
		echo '<button type="submit"' . ( $action ? '' : ' disabled' ) . '>' . esc_html( $button ) . '</button>';
		echo '</div>';
		echo '<p class="tz-footer-newsletter__msg" data-tz-nl-msg hidden></p>';
		if ( ! $action ) {
			echo '<p class="tz-footer-col__hint">' . esc_html__( 'Set a form action URL (e.g. Mailchimp) or switch to Shortcode mode.', 'themezur' ) . '</p>';
		}
		echo '</form>';
	}
};
Themezur_Footer::render_trust_badges();
?>
<footer
	id="themezur-footer"
	class="tz-site-footer tz-site-footer--columns<?php echo esc_attr( (string) max( 1, $col_count ) ); ?> tz-site-footer--widths-<?php echo esc_attr( $widths ); ?>"
	data-tz-footer
	data-tz-footer-cols="<?php echo esc_attr( (string) max( 1, $col_count ) ); ?>"
>
	<?php
	$nl_row = isset( $f['newsletter_row'] ) && is_array( $f['newsletter_row'] ) ? $f['newsletter_row'] : array();
	if ( ! empty( $nl_row['enabled'] ) ) :
		$action      = ! empty( $nl_row['action'] ) ? $nl_row['action'] : '';
		$placeholder = ! empty( $nl_row['placeholder'] ) ? $nl_row['placeholder'] : __( 'Enter your email address', 'themezur' );
		$button      = ! empty( $nl_row['button'] ) ? $nl_row['button'] : __( 'Subscribe Now', 'themezur' );
		$email_name  = ! empty( $nl_row['email_name'] ) ? $nl_row['email_name'] : 'EMAIL';
		?>
		<div class="tz-footer-newsletter-row">
			<div class="tz-footer-newsletter-row__inner">
				<div class="tz-footer-newsletter-row__text">
					<?php if ( ! empty( $nl_row['title'] ) ) : ?>
						<h3 class="tz-footer-newsletter-row__title"><?php echo esc_html( $nl_row['title'] ); ?></h3>
					<?php endif; ?>
					<?php if ( ! empty( $nl_row['subtitle'] ) ) : ?>
						<p class="tz-footer-newsletter-row__subtitle"><?php echo esc_html( $nl_row['subtitle'] ); ?></p>
					<?php endif; ?>
				</div>
				<form
					class="tz-footer-newsletter-row__form"
					method="post"
					action="<?php echo $action ? esc_url( $action ) : '#'; ?>"
					data-tz-newsletter
					<?php if ( $action ) : ?>
						data-tz-nl-action="<?php echo esc_url( $action ); ?>"
						data-tz-nl-email-name="<?php echo esc_attr( $email_name ); ?>"
					<?php else : ?>
						data-tz-nl-empty="1"
					<?php endif; ?>
				>
					<input type="email" name="<?php echo esc_attr( $email_name ); ?>" placeholder="<?php echo esc_attr( $placeholder ); ?>" required autocomplete="email" />
					<button type="submit" class="tz-btn" <?php disabled( ! $action ); ?>><?php echo esc_html( $button ); ?></button>
					<p class="tz-footer-newsletter__msg" data-tz-nl-msg hidden></p>
				</form>
			</div>
		</div>
	<?php endif; ?>

	<?php
	$store = isset( $f['store_row'] ) && is_array( $f['store_row'] ) ? $f['store_row'] : array();
	if ( ! empty( $store['enabled'] ) && ( ! empty( $store['map_url'] ) || ! empty( $store['address_text'] ) ) ) :
		?>
		<div class="tz-footer-store-row">
			<div class="tz-footer-store-row__inner">
				<?php if ( ! empty( $store['address_text'] ) ) : ?>
					<p class="tz-footer-store-row__address"><?php echo esc_html( $store['address_text'] ); ?></p>
				<?php endif; ?>
				<?php if ( ! empty( $store['map_url'] ) ) : ?>
					<a class="tz-footer-store-row__link" href="<?php echo esc_url( $store['map_url'] ); ?>" target="_blank" rel="noopener noreferrer">
						<?php echo esc_html( ! empty( $store['label'] ) ? $store['label'] : __( 'Find a store', 'themezur' ) ); ?>
					</a>
				<?php endif; ?>
			</div>
		</div>
	<?php endif; ?>

	<?php if ( $col_count > 0 ) : ?>
		<div class="tz-footer-main">
			<div class="tz-footer-main__inner">
				<div class="tz-footer-cols tz-footer-cols--<?php echo esc_attr( $widths ); ?>" style="--tz-footer-col-count: <?php echo esc_attr( (string) $col_count ); ?>">
					<?php foreach ( $active_cols as $key => $col ) : ?>
						<div class="tz-footer-col<?php echo ( '1' === (string) $key && 'about_wide' === $widths ) ? ' tz-footer-col--wide' : ''; ?>" data-tz-footer-col="<?php echo esc_attr( $key ); ?>">
							<?php if ( ! empty( $col['title'] ) ) : ?>
								<h3 class="tz-footer-col__title"><?php echo esc_html( $col['title'] ); ?></h3>
							<?php endif; ?>
							<?php if ( ! empty( $col['subtitle'] ) ) : ?>
								<p class="tz-footer-col__subtitle"><?php echo esc_html( $col['subtitle'] ); ?></p>
							<?php endif; ?>
							<div class="tz-footer-col__body">
								<?php $render_col( $col ); ?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	<?php endif; ?>

	<?php
	$apps = isset( $f['app_badges'] ) && is_array( $f['app_badges'] ) ? $f['app_badges'] : array();
	if ( ! empty( $apps['enabled'] ) && ( ! empty( $apps['play_url'] ) || ! empty( $apps['appstore_url'] ) || ! empty( $apps['qr_image_id'] ) ) ) :
		?>
		<div class="tz-footer-apps">
			<div class="tz-footer-apps__inner">
				<?php if ( ! empty( $apps['title'] ) ) : ?>
					<p class="tz-footer-apps__title"><?php echo esc_html( $apps['title'] ); ?></p>
				<?php endif; ?>
				<div class="tz-footer-apps__badges">
					<?php if ( ! empty( $apps['play_url'] ) ) : ?>
						<a class="tz-footer-apps__badge" href="<?php echo esc_url( $apps['play_url'] ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Google Play', 'themezur' ); ?></a>
					<?php endif; ?>
					<?php if ( ! empty( $apps['appstore_url'] ) ) : ?>
						<a class="tz-footer-apps__badge" href="<?php echo esc_url( $apps['appstore_url'] ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'App Store', 'themezur' ); ?></a>
					<?php endif; ?>
					<?php
					if ( ! empty( $apps['qr_image_id'] ) ) {
						echo wp_get_attachment_image( (int) $apps['qr_image_id'], 'thumbnail', false, array( 'class' => 'tz-footer-apps__qr' ) );
					}
					?>
				</div>
			</div>
		</div>
	<?php endif; ?>

	<?php if ( ! empty( $bottom['enabled'] ) ) : ?>
		<?php
		$copy = ! empty( $bottom['copyright'] )
			? $bottom['copyright']
			: sprintf(
				/* translators: 1: site name, 2: year */
				__( '© %1$s %2$s. All rights reserved.', 'themezur' ),
				$site_name ? $site_name : 'Themezur',
				wp_date( 'Y' )
			);
		$copy = str_replace( array( '{year}', '{site_name}' ), array( wp_date( 'Y' ), $site_name ? $site_name : 'Themezur' ), $copy );
		$bottom_menu = '';
		if ( ! empty( $bottom['show_menu'] ) && ! empty( $bottom['menu_id'] ) ) {
			$bottom_menu = wp_nav_menu(
				array(
					'menu'        => (int) $bottom['menu_id'],
					'container'   => false,
					'menu_class'  => 'tz-footer-bottom__menu',
					'depth'       => 1,
					'echo'        => false,
					'fallback_cb' => false,
				)
			);
		}
		?>
		<div class="tz-footer-bottom">
			<div class="tz-footer-bottom__inner">
				<p class="tz-footer-bottom__copy"><?php echo esc_html( $copy ); ?></p>
				<?php if ( $bottom_menu ) : ?>
					<nav class="tz-footer-bottom__nav" aria-label="<?php echo esc_attr__( 'Footer secondary', 'themezur' ); ?>">
						<?php echo $bottom_menu; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</nav>
				<?php endif; ?>
				<?php Themezur_Footer::render_payment_icons(); ?>
			</div>
		</div>
	<?php endif; ?>
</footer>
