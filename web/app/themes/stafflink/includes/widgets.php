<?php
/**
 * Widget areas and custom widgets.
 *
 * @package WP_Stafflink
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

foreach ( glob( dirname( __DIR__ ) . '/widgets/*.php' ) as $filename ) {
	include $filename;
}

add_action(
	'widgets_init',
	function () {
		$widget_areas = array(
			'main_widget'           => __( 'Main Widget', TEXT_DOMAIN ),
			'sidebar_widget'        => __( 'Sidebar Widget', TEXT_DOMAIN ),
			'footer_widget'         => __( 'Footer Widget', TEXT_DOMAIN ),
			'job_apply_form_widget' => __( 'Job Apply Form Widget', TEXT_DOMAIN ),
		);

		foreach ( $widget_areas as $id => $name ) {
			register_sidebar(
				array(
					'id'            => $id,
					'name'          => $name,
					'before_widget' => '<div id="%1$s" class="widget %2$s">',
					'after_widget'  => '</div>',
					'before_title'  => '<h2 class="widget-title">',
					'after_title'   => '</h2>',
				)
			);
		}

		if ( class_exists( 'ElementorLibraryWidget' ) ) {
			register_widget( 'ElementorLibraryWidget' );
		}

		if ( class_exists( 'WidgetGetJobListing' ) ) {
			register_widget( 'WidgetGetJobListing' );
		}
	}
);
