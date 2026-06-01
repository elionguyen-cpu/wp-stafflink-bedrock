<?php
/**
 * ACF local fields.
 *
 * @package WP_Stafflink
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'acf/init',
	function () {
		if ( ! function_exists( 'acf_add_local_field_group' ) ) {
			return;
		}

		acf_add_local_field_group(
			array(
				'key'                   => 'group_job',
				'title'                 => __( 'Job', TEXT_DOMAIN ),
				'fields'                => array(
					array(
						'key'   => 'field_job_consultant_code',
						'label' => __( 'Consultant Code', TEXT_DOMAIN ),
						'name'  => 'job_consultant_code',
						'type'  => 'text',
					),
					array(
						'key'   => 'field_job_type',
						'label' => __( 'Job Type', TEXT_DOMAIN ),
						'name'  => 'job_type',
						'type'  => 'text',
					),
					array(
						'key'   => 'field_job_working_hours',
						'label' => __( 'Working Hours', TEXT_DOMAIN ),
						'name'  => 'job_working_hours',
						'type'  => 'text',
					),
					array(
						'key'   => 'field_job_salary',
						'label' => __( 'Salary', TEXT_DOMAIN ),
						'name'  => 'job_salary',
						'type'  => 'text',
					),
					array(
						'key'          => 'field_job_description',
						'label'        => __( 'Job Description', TEXT_DOMAIN ),
						'name'         => 'job_description',
						'type'         => 'wysiwyg',
						'tabs'         => 'all',
						'toolbar'      => 'basic',
						'media_upload' => 0,
					),
					array(
						'key'          => 'field_job_responsibilities',
						'label'        => __( 'Job Responsibilities', TEXT_DOMAIN ),
						'name'         => 'job_responsibilities',
						'type'         => 'wysiwyg',
						'tabs'         => 'all',
						'toolbar'      => 'basic',
						'media_upload' => 0,
					),
					array(
						'key'          => 'field_job_requirements',
						'label'        => __( 'Requirements', TEXT_DOMAIN ),
						'name'         => 'job_requirements',
						'type'         => 'wysiwyg',
						'tabs'         => 'all',
						'toolbar'      => 'basic',
						'media_upload' => 0,
					),
				),
				'location'              => array(
					array(
						array(
							'param'    => 'post_type',
							'operator' => '==',
							'value'    => POST_TYPE_JOB,
						),
					),
				),
				'menu_order'            => 0,
				'position'              => 'normal',
				'style'                 => 'default',
				'label_placement'       => 'top',
				'instruction_placement' => 'label',
				'active'                => true,
				'show_in_rest'          => 0,
			)
		);

		acf_add_local_field_group(
			array(
				'key'                   => 'group_job_application',
				'title'                 => __( 'Job Application', TEXT_DOMAIN ),
				'fields'                => array(
					array(
						'key'           => 'field_applied_job',
						'label'         => __( 'Applied Job', TEXT_DOMAIN ),
						'name'          => 'applied_job_id',
						'type'          => 'post_object',
						'post_type'     => array( POST_TYPE_JOB ),
						'return_format' => 'id',
						'ui'            => 1,
					),
					array(
						'key'     => 'field_application_identification_type',
						'label'   => __( 'Identification Type', TEXT_DOMAIN ),
						'name'    => 'identification_type',
						'type'    => 'select',
						'choices' => array(
							'nric'     => __( 'NRIC', TEXT_DOMAIN ),
							'fin'      => __( 'FIN', TEXT_DOMAIN ),
							'passport' => __( 'Passport', TEXT_DOMAIN ),
						),
					),
					array(
						'key'   => 'field_application_identification_no',
						'label' => __( 'Identification No', TEXT_DOMAIN ),
						'name'  => 'identification_no',
						'type'  => 'text',
					),
					array(
						'key'   => 'field_application_email_address',
						'label' => __( 'Email Address', TEXT_DOMAIN ),
						'name'  => 'email_address',
						'type'  => 'email',
					),
					array(
						'key'   => 'field_application_mobile_no',
						'label' => __( 'Mobile No.', TEXT_DOMAIN ),
						'name'  => 'mobile_no',
						'type'  => 'text',
					),
					array(
						'key'   => 'field_application_home_no',
						'label' => __( 'Home No.', TEXT_DOMAIN ),
						'name'  => 'home_no',
						'type'  => 'text',
					),
					array(
						'key'   => 'field_application_address',
						'label' => __( 'Address', TEXT_DOMAIN ),
						'name'  => 'address',
						'type'  => 'textarea',
						'rows'  => 3,
					),
					array(
						'key'   => 'field_application_postal_code',
						'label' => __( 'Postal Code', TEXT_DOMAIN ),
						'name'  => 'postal_code',
						'type'  => 'text',
					),
					array(
						'key'   => 'field_application_current_salary',
						'label' => __( 'Current Salary', TEXT_DOMAIN ),
						'name'  => 'current_salary',
						'type'  => 'text',
					),
					array(
						'key'     => 'field_application_current_salary_currency',
						'label'   => __( 'Current Salary Currency', TEXT_DOMAIN ),
						'name'    => 'current_salary_currency',
						'type'    => 'select',
						'choices' => array(
							'sgd' => __( 'SGD', TEXT_DOMAIN ),
							'usd' => __( 'USD', TEXT_DOMAIN ),
						),
					),
					array(
						'key'   => 'field_application_expected_salary',
						'label' => __( 'Expected Salary', TEXT_DOMAIN ),
						'name'  => 'expected_salary',
						'type'  => 'text',
					),
					array(
						'key'     => 'field_application_expected_salary_currency',
						'label'   => __( 'Expected Salary Currency', TEXT_DOMAIN ),
						'name'    => 'expected_salary_currency',
						'type'    => 'select',
						'choices' => array(
							'sgd' => __( 'SGD', TEXT_DOMAIN ),
							'usd' => __( 'USD', TEXT_DOMAIN ),
						),
					),
					array(
						'key'   => 'field_application_availability',
						'label' => __( 'Availability', TEXT_DOMAIN ),
						'name'  => 'availability',
						'type'  => 'text',
					),
					array(
						'key'   => 'field_application_image_upload',
						'label' => __( 'Image Upload', TEXT_DOMAIN ),
						'name'  => 'image_upload',
						'type'  => 'image',
					),
					array(
						'key'           => 'field_application_resume',
						'label'         => __( 'Resume', TEXT_DOMAIN ),
						'name'          => 'resume_attachment_id',
						'type'          => 'file',
						'return_format' => 'id',
						'library'       => 'all',
					),
					array(
						'key'           => 'field_application_photo',
						'label'         => __( 'Photo', TEXT_DOMAIN ),
						'name'          => 'photo_attachment_id',
						'type'          => 'image',
						'return_format' => 'id',
						'preview_size'  => 'medium',
						'library'       => 'all',
					),
				),
				'location'              => array(
					array(
						array(
							'param'    => 'post_type',
							'operator' => '==',
							'value'    => POST_TYPE_JOB_APPLICATION,
						),
					),
				),
				'menu_order'            => 0,
				'position'              => 'normal',
				'style'                 => 'default',
				'label_placement'       => 'top',
				'instruction_placement' => 'label',
				'active'                => true,
				'show_in_rest'          => 0,
			)
		);

		acf_add_local_field_group(
			array(
				'key'                   => 'group_mailing',
				'title'                 => __( 'Mailing', TEXT_DOMAIN ),
				'fields'                => array(
					array(
						'key'           => 'field_show_mailing_card',
						'label'         => __( 'Show Mailing Card', TEXT_DOMAIN ),
						'name'          => 'show_mailing_card',
						'type'          => 'true_false',
						'default_value' => 0,
						'ui'            => 1,
					),
					array(
						'key'   => 'field_contact_email',
						'label' => __( 'Contact Email', TEXT_DOMAIN ),
						'name'  => 'contact_email',
						'type'  => 'email',
					),
					array(
						'key'           => 'field_mailing_button_text',
						'label'         => __( 'Mailing Button Text', TEXT_DOMAIN ),
						'name'          => 'mailing_button_text',
						'type'          => 'text',
						'default_value' => __( 'Get in touch', TEXT_DOMAIN ),
					),
				),
				'location'              => array(
					array(
						array(
							'param'    => 'post_type',
							'operator' => '==',
							'value'    => POST_TYPE_MAILING,
						),
					),
				),
				'menu_order'            => 0,
				'position'              => 'normal',
				'style'                 => 'default',
				'label_placement'       => 'top',
				'instruction_placement' => 'label',
				'active'                => true,
			)
		);
	}
);
