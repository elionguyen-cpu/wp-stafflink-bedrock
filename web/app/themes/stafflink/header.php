<?php
/**
 * Global header.
 *
 * @package WP_Stafflink
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
	<?php
	$header_script = get_theme_mod( 'header_script' );
	if ( $header_script ) {
		echo $header_script;
	}
	?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<?php
$header_template_id = get_theme_mod( 'header_template_id', 0 );

if ( $header_template_id && function_exists( 'elementor_template' ) ) {
	?>
	<header id="sticky-header" class="header fixed-top">
		<?php elementor_template( $header_template_id ); ?>
	</header>
	<?php
}
?>
<section class="main">
