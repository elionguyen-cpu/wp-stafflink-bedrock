<?php
/**
 * Default site footer.
 *
 * @package WP_Stafflink
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<footer class="footer mt-auto">
		<?php
		if ( is_active_sidebar( 'footer_widget' ) ) {
			dynamic_sidebar( 'footer_widget' );
		}
		?>
</footer>
