<?php
/**
 * Breadcrumb template part.
 *
 * @package WP_Stafflink
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( function_exists( 'get_breadcrumb' ) ) {
	echo get_breadcrumb();
}
