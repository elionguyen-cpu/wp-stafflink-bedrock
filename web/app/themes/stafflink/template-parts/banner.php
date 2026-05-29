<?php
/**
 * Page banner template.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! is_page() || is_front_page() ) {
	return;
}

$banner_image = function_exists( 'get_field' ) ? get_field( 'page_banner_image' ) : null;

if ( empty( $banner_image ) ) {
	return;
}

$banner_url = '';
$banner_alt = '';

if ( is_array( $banner_image ) ) {
	$banner_url = $banner_image['url'] ?? '';
	$banner_alt = $banner_image['alt'] ?? '';
} elseif ( is_numeric( $banner_image ) ) {
	$banner_url = wp_get_attachment_image_url( (int) $banner_image, 'full' );
	$banner_alt = get_post_meta( (int) $banner_image, '_wp_attachment_image_alt', true );
} elseif ( is_string( $banner_image ) ) {
	$banner_url = $banner_image;
}

if ( empty( $banner_url ) ) {
	return;
}
?>

<section class="page-banner">
	<img
		class="page-banner-image"
		src="<?php echo esc_url( $banner_url ); ?>"
		alt="<?php echo esc_attr( $banner_alt ); ?>"
	>
</section>
