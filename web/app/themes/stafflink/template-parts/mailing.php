<?php
/**
 * Mailing card template.
 *
 * @package WP_Stafflink
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$mailing_posts = get_posts(
	array(
		'post_type'      => POST_TYPE_MAILING,
		'post_status'    => 'publish',
		'posts_per_page' => 1,
		'orderby'        => 'date',
		'order'          => 'DESC',
	)
);

$mailing_id = ! empty( $mailing_posts ) ? $mailing_posts[0]->ID : 0;

if ( ! $mailing_id || ! function_exists( 'get_field' ) ) {
	return;
}

$show_mailing_card = get_field( 'show_mailing_card', $mailing_id );
$contact_email     = get_field( 'contact_email', $mailing_id );
$button_text       = get_field( 'mailing_button_text', $mailing_id );

if ( empty( $button_text ) ) {
	$button_text = __( 'Get in touch', TEXT_DOMAIN );
}

if ( ! $show_mailing_card || empty( $contact_email ) ) {
	return;
}
?>

<div class="mailing-card">
	<a href="mailto:<?php echo esc_attr( $contact_email ); ?>" class="btn-mailing">
		<i class="bi bi-envelope" aria-hidden="true"></i>
		<span><?php echo esc_html( $button_text ); ?></span>
	</a>
</div>
