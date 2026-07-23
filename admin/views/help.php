<?php
/**
 * Help / Docs panel partial — category tabs + accordion.
 *
 * @package Themezur
 *
 * @var array[] $themezur_docs Sections from Themezur_Docs::get_sections().
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( empty( $themezur_docs ) || ! is_array( $themezur_docs ) ) {
	return;
}

$first_id = isset( $themezur_docs[0]['id'] ) ? $themezur_docs[0]['id'] : '';
?>
<section x-show="tab === 'help'" class="tz-section tz-help" x-cloak>
	<div x-data="{ helpTab: '<?php echo esc_js( $first_id ); ?>', openItem: 0 }">
		<h2><?php esc_html_e( 'Help / Docs', 'themezur' ); ?></h2>
		<p class="tz-muted"><?php esc_html_e( 'Usage guides for every Themezur feature. New features should add docs here.', 'themezur' ); ?></p>

		<div class="tz-help__tabs" role="tablist" aria-label="<?php esc_attr_e( 'Documentation topics', 'themezur' ); ?>">
			<?php foreach ( $themezur_docs as $section ) : ?>
				<?php
				$sid = isset( $section['id'] ) ? $section['id'] : '';
				if ( '' === $sid ) {
					continue;
				}
				?>
				<button
					type="button"
					class="tz-help__tab"
					role="tab"
					:class="{ 'is-active': helpTab === '<?php echo esc_js( $sid ); ?>' }"
					:aria-selected="helpTab === '<?php echo esc_js( $sid ); ?>'"
					@click="helpTab = '<?php echo esc_js( $sid ); ?>'; openItem = 0"
				>
					<?php echo esc_html( isset( $section['title'] ) ? $section['title'] : $sid ); ?>
				</button>
			<?php endforeach; ?>
		</div>

		<?php foreach ( $themezur_docs as $section ) : ?>
			<?php
			$sid   = isset( $section['id'] ) ? $section['id'] : '';
			$items = isset( $section['items'] ) && is_array( $section['items'] ) ? $section['items'] : array();
			if ( '' === $sid ) {
				continue;
			}
			?>
			<div class="tz-help__panel" x-show="helpTab === '<?php echo esc_js( $sid ); ?>'" role="tabpanel">
				<div class="tz-accordion">
					<?php foreach ( $items as $index => $item ) : ?>
						<?php
						$title = isset( $item['title'] ) ? $item['title'] : '';
						$body  = isset( $item['body'] ) ? $item['body'] : '';
						?>
						<div class="tz-accordion__item" :class="{ 'is-open': openItem === <?php echo (int) $index; ?> }">
							<button
								type="button"
								class="tz-accordion__trigger"
								@click="openItem = openItem === <?php echo (int) $index; ?> ? -1 : <?php echo (int) $index; ?>"
								:aria-expanded="openItem === <?php echo (int) $index; ?>"
							>
								<span><?php echo esc_html( $title ); ?></span>
								<span class="tz-accordion__icon" aria-hidden="true"></span>
							</button>
							<div
								class="tz-accordion__panel"
								x-show="openItem === <?php echo (int) $index; ?>"
								x-transition.opacity.duration.150ms
							>
								<div class="tz-accordion__body">
									<?php echo wp_kses_post( $body ); ?>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</section>
