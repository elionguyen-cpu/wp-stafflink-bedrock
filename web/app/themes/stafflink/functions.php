<?php
if ( ! defined( 'TEXT_DOMAIN' ) ) {
	define( 'TEXT_DOMAIN', 'stafflink-bedrock' );
}

defined( 'POST_TYPE_JOB' ) || define( 'POST_TYPE_JOB', 'jobseekers' );
defined( 'POST_TYPE_JOB_APPLICATION' ) || define( 'POST_TYPE_JOB_APPLICATION', 'job-application' );
defined( 'POST_TYPE_MAILING' ) || define( 'POST_TYPE_MAILING', 'mailing' );
defined( 'TAX_TYPE_JOB_LOCATION' ) || define( 'TAX_TYPE_JOB_LOCATION', 'job_location' );
defined( 'TAX_TYPE_JOB_CATEGORY' ) || define( 'TAX_TYPE_JOB_CATEGORY', 'job_category' );

require_once get_stylesheet_directory() . '/includes/bootstrap-navwalker.php';
require_once get_stylesheet_directory() . '/includes/theme-customize.php';
require_once get_stylesheet_directory() . '/includes/widgets.php';
require_once get_stylesheet_directory() . '/includes/post-types.php';
require_once get_stylesheet_directory() . '/includes/acf-custom-fields.php';
require_once get_stylesheet_directory() . '/includes/elementor.php';
require_once get_stylesheet_directory() . '/includes/job-apply-form.php';

add_action( 'after_setup_theme', function () {
	load_theme_textdomain( TEXT_DOMAIN, get_template_directory() . '/languages' );

	add_theme_support( 'custom-logo', [
		'height'               => 100,
		'width'                => 400,
		'flex-height'          => true,
		'flex-width'           => true,
		'header-text'          => [ 'site-title', 'site-description' ],
		'unlink-homepage-logo' => true,
	] );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', [ 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ] );
} );

add_action( 'init', function () {
	register_nav_menu( 'header-menu', __( 'Header Menu', TEXT_DOMAIN ) );
	register_nav_menu( 'footer-menu', __( 'Footer Menu', TEXT_DOMAIN ) );
} );

add_action( 'wp_enqueue_scripts', function () {
	wp_register_script(
		'bootstrap-js',
		get_theme_file_uri( '/assets/vendor/bootstrap/js/bootstrap.bundle.min.js' ),
		[ 'jquery' ],
		filemtime( get_theme_file_path( '/assets/vendor/bootstrap/js/bootstrap.bundle.min.js' ) ),
		true
	);

	wp_register_script(
		'select2-js',
		get_theme_file_uri( '/assets/vendor/select2/js/select2.min.js' ),
		[ 'jquery' ],
		filemtime( get_theme_file_path( '/assets/vendor/select2/js/select2.min.js' ) ),
		true
	);

	wp_register_script(
		'main-js',
		get_theme_file_uri( '/assets/js/main.js' ),
		[ 'jquery', 'bootstrap-js', 'select2-js' ],
		filemtime( get_theme_file_path( '/assets/js/main.js' ) ),
		true
	);

	wp_localize_script(
		'main-js',
		'resumeUpload',
		[
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'resume_upload' ),
		]
	);

	wp_enqueue_script( 'bootstrap-js' );
	wp_enqueue_script( 'select2-js' );
	wp_enqueue_script( 'main-js' );

	wp_register_style(
		'bootstrap-css',
		get_theme_file_uri( '/assets/vendor/bootstrap/css/bootstrap.min.css' ),
		[],
		filemtime( get_theme_file_path( '/assets/vendor/bootstrap/css/bootstrap.min.css' ) )
	);

	wp_register_style(
		'bootstrap-icons-css',
		get_theme_file_uri( '/assets/vendor/bootstrap/icon/bootstrap-icons.css' ),
		[],
		filemtime( get_theme_file_path( '/assets/vendor/bootstrap/icon/bootstrap-icons.css' ) )
	);

	wp_register_style(
		'select2-css',
		get_theme_file_uri( '/assets/vendor/select2/css/select2.min.css' ),
		[],
		filemtime( get_theme_file_path( '/assets/vendor/select2/css/select2.min.css' ) )
	);

	wp_register_style(
		'main-css',
		get_theme_file_uri( '/assets/css/main.css' ),
		[ 'bootstrap-css', 'bootstrap-icons-css', 'select2-css' ],
		filemtime( get_theme_file_path( '/assets/css/main.css' ) )
	);

	wp_enqueue_style( 'bootstrap-css' );
	wp_enqueue_style( 'bootstrap-icons-css' );
	wp_enqueue_style( 'select2-css' );
	wp_enqueue_style( 'main-css' );
} );

add_filter( 'nav_menu_link_attributes', function ( $atts, $item, $args ) {
	if ( isset( $args->theme_location ) && 'header-menu' === $args->theme_location ) {
		$atts['class'] = isset( $atts['class'] ) ? $atts['class'] . ' nav-link' : 'nav-link';
	}

	return $atts;
}, 10, 3 );

add_filter( 'nav_menu_css_class', function ( $classes, $item, $args ) {
	if ( isset( $args->theme_location ) && 'header-menu' === $args->theme_location ) {
		$classes[] = 'nav-item';
	}

	return $classes;
}, 10, 3 );

add_filter( 'upload_mimes', function ( $mimes ) {
	$mimes['svg'] = 'image/svg+xml';

	return $mimes;
} );

add_filter( 'wpcf7_autop_or_not', '__return_false' );
add_filter( 'xmlrpc_enabled', '__return_false' );
add_filter( 'widget_text', 'shortcode_unautop' );
add_filter( 'widget_text', 'do_shortcode' );
