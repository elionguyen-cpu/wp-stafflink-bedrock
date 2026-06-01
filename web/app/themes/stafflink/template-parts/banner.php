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

$banner_image_id = get_post_thumbnail_id();

if ( ! $banner_image_id ) {
	return;
}

$banner_url = wp_get_attachment_image_url( $banner_image_id, 'full' );
$banner_alt = get_post_meta( $banner_image_id, '_wp_attachment_image_alt', true );

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
