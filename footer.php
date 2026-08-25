<?php
/**
 * The template for displaying the footer.
 *
 * @package Themezur
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( 'Themezur_Frontend' ) ) {
	Themezur_Frontend::render_footer();
}
?>

<?php wp_footer(); ?>

</body>
</html>
