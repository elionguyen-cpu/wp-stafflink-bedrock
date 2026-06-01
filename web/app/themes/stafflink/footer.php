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
<?php get_template_part( 'template-parts/footer-default' ); ?>
<?php wp_footer(); ?>
<?php
$footer_script = get_theme_mod( 'footer_script' );
if ( $footer_script ) {
	echo $footer_script;
}
?>
</body>
</html>
