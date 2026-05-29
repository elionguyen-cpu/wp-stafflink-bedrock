<?php
/**
 * Elementor integrations.
 *
 * @package WP_Stafflink
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'elementor/elements/categories_registered',
	function ( $elements_manager ) {
		$elements_manager->add_category(
			'be-elements',
			array(
				'title' => __( '[BE] Elements', TEXT_DOMAIN ),
				'icon'  => 'fa fa-plug',
			)
		);
	}
);

add_action(
	'elementor/widgets/register',
	function ( $widgets_manager ) {
		foreach ( glob( dirname( __DIR__ ) . '/elementor-widgets/*.php' ) as $filename ) {
			require_once $filename;
		}

		foreach ( glob( dirname( __DIR__ ) . '/elementor-widgets/*/*.php' ) as $filename ) {
			require_once $filename;
		}
	}
);

add_action(
	'admin_head',
	function () {
		?>
		<style>
			#menu-posts-elementor_library {
				display: list-item !important;
			}
		</style>
		<?php
	},
	99999
);
