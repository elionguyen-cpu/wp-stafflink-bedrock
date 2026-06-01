<?php
/**
 * Theme Customizer registrations.
 *
 * @package WP_Stafflink
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'customize_register',
	function ( $wp_customize ) {
		$wp_customize->add_section(
			'stafflink_jobseekers',
			array(
				'title'    => __( 'Jobseekers', TEXT_DOMAIN ),
				'priority' => 160,
			)
		);

		$wp_customize->add_setting(
			'stafflink_apply_job_form_id',
			array(
				'default'           => 0,
				'sanitize_callback' => 'absint',
			)
		);

		$wp_customize->add_control(
			'stafflink_apply_job_form_id',
			array(
				'label'       => __( 'Apply Job Form', TEXT_DOMAIN ),
				'description' => __( 'Choose the form.', TEXT_DOMAIN ),
				'section'     => 'stafflink_jobseekers',
				'type'        => 'select',
				'choices'     => get_jobseekers_form_choices(),
			)
		);
	}
);

function get_jobseekers_form_choices() {
	$choices = array(
		0 => __( 'Select a form', TEXT_DOMAIN ),
	);

	$forms = get_posts(
		array(
			'post_type'      => 'wpuf_forms',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'title',
			'order'          => 'ASC',
		)
	);

	foreach ( $forms as $form ) {
		$choices[ $form->ID ] = get_the_title( $form );
	}

	return $choices;
}
