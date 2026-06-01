<?php
/**
 * Global footer.
 *
 * @package WP_Stafflink
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
</section>
<?php
$footer_template_id = get_theme_mod( 'footer_template_id', 0 );

if ( $footer_template_id && function_exists( 'elementor_template' ) ) {
	?>
	<footer class="footer mt-auto">
		<?php elementor_template( $footer_template_id ); ?>
	</footer>
	<?php
}
?>
<?php wp_footer(); ?>
<?php
$footer_script = get_theme_mod( 'footer_script' );
if ( $footer_script ) {
	echo $footer_script;
}
?>
</body>
</html>
