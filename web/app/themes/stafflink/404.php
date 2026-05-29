<?php
/**
 * 404 template.
 *
 * @package WP_Stafflink
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
get_template_part( 'template-parts/breadcrumbs' );
?>

<section class="not-found-page">
	<div class="container">
		<h1><?php esc_html_e( 'Page not found', TEXT_DOMAIN ); ?></h1>
		<p><?php esc_html_e( 'The page you are looking for does not exist or has been moved.', TEXT_DOMAIN ); ?></p>
		<a class="btn btn-primary" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<?php esc_html_e( 'Back to homepage', TEXT_DOMAIN ); ?>
		</a>
	</div>
</section>

<?php
get_footer();
