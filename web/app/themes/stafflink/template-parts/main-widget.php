<?php
/**
 * Main widget area.
 *
 * @package WP_Stafflink
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( is_active_sidebar( 'main_widget' ) ) {
	dynamic_sidebar( 'main_widget' );
}
