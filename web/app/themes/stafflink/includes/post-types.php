<?php
/**
 * Custom post types and taxonomies.
 *
 * @package WP_Stafflink
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'init',
	function () {
		register_post_type(
			POST_TYPE_JOB,
			array(
				'labels'       => array(
					'name'          => __( 'Jobs', TEXT_DOMAIN ),
					'singular_name' => __( 'Job', TEXT_DOMAIN ),
					'add_new_item'  => __( 'Add New Job', TEXT_DOMAIN ),
					'edit_item'     => __( 'Edit Job', TEXT_DOMAIN ),
					'menu_name'     => __( 'Jobs', TEXT_DOMAIN ),
				),
				'public'       => true,
				'has_archive'  => false,
				'rewrite'      => array( 'slug' => 'jobseekers' ),
				'menu_icon'    => 'dashicons-businessperson',
				'show_in_rest' => true,
				'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'elementor' , 'page-attributes'),
			)
		);

		register_post_type(
			POST_TYPE_JOB_APPLICATION,
			array(
				'labels'       => array(
					'name'          => __( 'Job Applications', TEXT_DOMAIN ),
					'singular_name' => __( 'Job Application', TEXT_DOMAIN ),
					'menu_name'     => __( 'Job Applications', TEXT_DOMAIN ),
				),
				'public'       => false,
				'show_ui'      => true,
				'show_in_menu' => true,
				'menu_icon'    => 'dashicons-clipboard',
				'show_in_rest' => true,
				'supports'     => array( 'title', 'editor', 'custom-fields', 'revisions' ),
			)
		);

		register_post_type(
			POST_TYPE_MAILING,
			array(
				'labels'       => array(
					'name'          => __( 'Mailing', TEXT_DOMAIN ),
					'singular_name' => __( 'Mailing', TEXT_DOMAIN ),
					'add_new_item'  => __( 'Add New Mailing', TEXT_DOMAIN ),
					'edit_item'     => __( 'Edit Mailing', TEXT_DOMAIN ),
					'menu_name'     => __( 'Mailing', TEXT_DOMAIN ),
				),
				'public'       => false,
				'show_ui'      => true,
				'show_in_menu' => true,
				'menu_icon'    => 'dashicons-email-alt',
				'show_in_rest' => true,
				'supports'     => array( 'title', 'revisions' ),
			)
		);

		register_taxonomy(
			TAX_TYPE_JOB_LOCATION,
			POST_TYPE_JOB,
			array(
				'labels'       => array(
					'name'          => __( 'Job Locations', TEXT_DOMAIN ),
					'singular_name' => __( 'Job Location', TEXT_DOMAIN ),
					'menu_name'     => __( 'Job Locations', TEXT_DOMAIN ),
				),
				'public'       => true,
				'hierarchical' => true,
				'rewrite'      => array( 'slug' => 'job-location' ),
				'show_in_rest' => true,
			)
		);

		register_taxonomy(
			TAX_TYPE_JOB_CATEGORY,
			POST_TYPE_JOB,
			array(
				'labels'       => array(
					'name'          => __( 'Job Categories', TEXT_DOMAIN ),
					'singular_name' => __( 'Job Category', TEXT_DOMAIN ),
					'menu_name'     => __( 'Job Categories', TEXT_DOMAIN ),
				),
				'public'       => true,
				'hierarchical' => true,
				'rewrite'      => array( 'slug' => 'job-category' ),
				'show_in_rest' => true,
			)
		);
	}
);

add_action(
	'add_meta_boxes',
	function () {
		remove_meta_box( 'wpuf-select-form', POST_TYPE_JOB, 'side' );
		remove_meta_box( 'wpuf-post-lock', POST_TYPE_JOB, 'side' );
		remove_meta_box( 'wpuf-custom-fields', POST_TYPE_JOB, 'normal' );
	},
	99
);
