<?php
/**
 * Job application form helpers.
 *
 * @package WP_Stafflink
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function get_apply_job_form_id() {
	return absint( get_theme_mod( 'stafflink_apply_job_form_id', 0 ) );
}

function get_apply_job_form(): string {
	$form_id = get_apply_job_form_id();

	if ( ! $form_id ) {
		return '';
	}

	return prepare_job_apply_form( do_shortcode( sprintf( '[wpuf_form id="%d"]', $form_id ) ) );
}

function prepare_job_apply_form( $form_html ) {
	if ( empty( $form_html ) ) {
		return '';
	}

	if ( false === strpos( $form_html, 'enctype=' ) ) {
		$form_html = preg_replace( '/<form\b([^>]*)>/i', '<form$1 enctype="multipart/form-data">', $form_html, 1 );
	}

	if ( false === strpos( $form_html, 'name="applied_job_id"' ) ) {
		$form_html = preg_replace( '/<form\b([^>]*)>/i', '<form$1>' . get_applied_job_field(), $form_html, 1 );
	}

	if ( false === strpos( $form_html, 'name="resume_attachment_id"' ) ) {
		$resume_field = get_resume_upload_field();
		$submit_pos   = strpos( $form_html, '<li class="wpuf-submit">' );
		$form_html    = false === $submit_pos
			? str_replace( '</ul>', $resume_field . '</ul>', $form_html )
			: substr_replace( $form_html, $resume_field, $submit_pos, 0 );
	}

	return structure_job_apply_form( $form_html );
}

function get_applied_job_field() {
	$queried = get_queried_object_id();

	if ( $queried && POST_TYPE_JOB === get_post_type( $queried ) ) {
		$job_id = absint( $queried );
	} else {
		$current = get_the_ID();
		$job_id  = $current && POST_TYPE_JOB === get_post_type( $current ) ? absint( $current ) : 0;
	}

	if ( ! $job_id ) {
		return '';
	}

	return sprintf(
		'<input type="hidden" name="applied_job_id" value="%d">',
		$job_id
	);
}

function structure_job_apply_form( $form_html ) {
	if ( false !== strpos( $form_html, 'apply-group-fields' ) || ! class_exists( 'DOMDocument' ) ) {
		return $form_html;
	}

	$previous_errors = libxml_use_internal_errors( true );
	$dom             = new DOMDocument( '1.0', 'UTF-8' );
	$loaded          = $dom->loadHTML(
		'<?xml encoding="UTF-8"><div id="apply-form-fragment">' . $form_html . '</div>',
		LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
	);

	libxml_clear_errors();
	libxml_use_internal_errors( $previous_errors );

	if ( ! $loaded ) {
		return $form_html;
	}

	$xpath = new DOMXPath( $dom );
	$forms = $xpath->query( '//*[contains(concat(" ", normalize-space(@class), " "), " wpuf-form ")]' );

	if ( ! $forms || ! $forms->length ) {
		return $form_html;
	}

	$list      = $forms->item( 0 );
	$children  = iterator_to_array( $list->childNodes );
	$groups    = collect_apply_form_groups( $children );
	$help_node = $groups['help'];

	foreach ( $children as $child ) {
		if ( $child->parentNode === $list ) {
			$list->removeChild( $child );
		}
	}

	if ( $groups['contact'] ) {
		$list->appendChild( build_form_group( $dom, 'apply-group-contact', __( 'Contact Info', TEXT_DOMAIN ), $groups['contact'], $help_node ) );
	}

	if ( $groups['info'] ) {
		$list->appendChild( build_form_group( $dom, 'apply-group-info', __( 'Other Information', TEXT_DOMAIN ), $groups['info'] ) );
	}

	if ( $groups['attach'] ) {
		$list->appendChild( build_form_group( $dom, 'apply-group-attach', __( 'Attachment', TEXT_DOMAIN ), $groups['attach'] ) );
	}

	foreach ( $groups['other'] as $child ) {
		$list->appendChild( $child );
	}

	$fragment = $dom->getElementById( 'apply-form-fragment' );
	$output   = '';

	if ( ! $fragment ) {
		return $form_html;
	}

	foreach ( $fragment->childNodes as $child ) {
		$output .= $dom->saveHTML( $child );
	}

	return $output;
}

function collect_apply_form_groups( array $children ) {
	$groups = array(
		'contact' => array(),
		'info'    => array(),
		'attach'  => array(),
		'other'   => array(),
		'help'    => null,
	);
	$contact_fields = array( 'post_title', 'identification_type', 'identification_no', 'email_address', 'mobile_no', 'home_no', 'address', 'postal_code' );
	$info_fields    = array( 'current_salary', 'current_salary_currency', 'expected_salary', 'expected_salary_currency', 'availability' );

	foreach ( $children as $child ) {
		if ( ! $child instanceof DOMElement || 'li' !== strtolower( $child->tagName ) ) {
			$groups['other'][] = $child;
			continue;
		}

		$field_name = get_field_name( $child );

		if ( has_dom_class( $child, 'contact' ) || in_array( $field_name, $contact_fields, true ) ) {
			$groups['contact'][] = $child;
		} elseif ( has_dom_class( $child, 'infor' ) || in_array( $field_name, $info_fields, true ) ) {
			$groups['info'][] = $child;
		} elseif ( has_dom_class( $child, 'attach' ) ) {
			$groups['attach'][] = $child;
		} elseif ( has_dom_class( $child, 'custom_html' ) && false !== strpos( $child->textContent, 'Please input the last 6 characters' ) ) {
			$groups['help'] = $child;
		} else {
			$groups['other'][] = $child;
		}
	}

	return $groups;
}

function build_form_group( DOMDocument $dom, $group_class, $title, array $fields, $help_node = null ) {
	$group       = $dom->createElement( 'li' );
	$title_node  = $dom->createElement( 'div', $title );
	$fields_node = $dom->createElement( 'div' );

	set_dom_classes( $group, array( 'wpuf-el', 'apply-group', $group_class ) );
	set_dom_classes( $title_node, array( 'apply-group-title' ) );
	set_dom_classes( $fields_node, array( 'apply-group-fields' ) );

	$group->appendChild( $title_node );

	if ( 'apply-group-contact' === $group_class ) {
		append_contact_fields( $dom, $fields_node, $fields, $help_node );
	} elseif ( 'apply-group-info' === $group_class ) {
		append_info_fields( $dom, $fields_node, $fields );
	} else {
		foreach ( $fields as $field ) {
			$fields_node->appendChild( li_to_div( $dom, $field ) );
		}
	}

	$group->appendChild( $fields_node );

	return $group;
}

function append_contact_fields( DOMDocument $dom, DOMElement $container, array $fields, $help_node = null ) {
	$used = array();
	$map  = map_fields_by_name( $fields );

	if ( ! empty( $map['post_title'] ) ) {
		append_form_field( $dom, $container, $map['post_title'], $used );
	}

	if ( ! empty( $map['identification_type'] ) && ! empty( $map['identification_no'] ) ) {
		$row = $dom->createElement( 'div' );
		set_dom_classes( $row, array( 'identification-row' ) );
		append_form_field( $dom, $row, $map['identification_type'], $used );
		append_form_field( $dom, $row, $map['identification_no'], $used );
		$container->appendChild( $row );
	}

	if ( $help_node instanceof DOMElement ) {
		$container->appendChild( extract_form_help( $dom, $help_node ) );
	}

	foreach ( $fields as $field ) {
		append_form_field( $dom, $container, $field, $used );
	}
}

function append_info_fields( DOMDocument $dom, DOMElement $container, array $fields ) {
	$used = array();
	$map  = map_fields_by_name( $fields );

	foreach ( $fields as $field ) {
		$name = get_field_name( $field );

		if ( 'current_salary' === $name && ! empty( $map['current_salary_currency'] ) ) {
			$container->appendChild( build_field_pair( $dom, 'current-salary-row', $field, $map['current_salary_currency'] ) );
			$used[ spl_object_hash( $field ) ]                         = true;
			$used[ spl_object_hash( $map['current_salary_currency'] ) ] = true;
			continue;
		}

		if ( 'expected_salary' === $name && ! empty( $map['expected_salary_currency'] ) ) {
			$container->appendChild( build_field_pair( $dom, 'expected-salary-row', $field, $map['expected_salary_currency'] ) );
			$used[ spl_object_hash( $field ) ]                          = true;
			$used[ spl_object_hash( $map['expected_salary_currency'] ) ] = true;
			continue;
		}

		append_form_field( $dom, $container, $field, $used );
	}
}

function append_form_field( DOMDocument $dom, DOMElement $container, DOMElement $field, array &$used, array $extra_classes = array() ) {
	$key = spl_object_hash( $field );

	if ( ! empty( $used[ $key ] ) ) {
		return;
	}

	$container->appendChild( li_to_div( $dom, $field, $extra_classes ) );
	$used[ $key ] = true;
}

function build_field_pair( DOMDocument $dom, $row_class, DOMElement $main_field, DOMElement $suffix_field ) {
	$used = array();
	$row  = $dom->createElement( 'div' );

	set_dom_classes( $row, array( 'field-pair', $row_class ) );
	append_form_field( $dom, $row, $main_field, $used, array( 'field-pair-main' ) );
	append_form_field( $dom, $row, $suffix_field, $used, array( 'field-pair-suffix' ) );

	return $row;
}

function extract_form_help( DOMDocument $dom, DOMElement $help_node ) {
	$help   = $dom->createElement( 'div' );
	$source = $help_node;

	set_dom_classes( $help, array( 'identification-help' ) );

	foreach ( $help_node->childNodes as $child ) {
		if ( $child instanceof DOMElement && has_dom_class( $child, 'wpuf-fields' ) ) {
			$source = $child;
			break;
		}
	}

	while ( $source->firstChild ) {
		$help->appendChild( $source->firstChild );
	}

	return $help;
}

function map_fields_by_name( array $fields ) {
	$map = array();

	foreach ( $fields as $field ) {
		$name = get_field_name( $field );

		if ( $name ) {
			$map[ $name ] = $field;
		}
	}

	return $map;
}

function li_to_div( DOMDocument $dom, DOMElement $field, array $extra_classes = array() ) {
	$div = $dom->createElement( 'div' );

	foreach ( iterator_to_array( $field->attributes ) as $attribute ) {
		$div->setAttribute( $attribute->name, $attribute->value );
	}

	set_dom_classes( $div, array_merge( dom_classes( $field ), $extra_classes ) );

	while ( $field->firstChild ) {
		$div->appendChild( $field->firstChild );
	}

	return $div;
}

function get_field_name( DOMElement $field ) {
	foreach ( array( 'input', 'select', 'textarea' ) as $tag ) {
		foreach ( $field->getElementsByTagName( $tag ) as $control ) {
			if ( $control instanceof DOMElement && $control->hasAttribute( 'name' ) ) {
				return $control->getAttribute( 'name' );
			}
		}
	}

	return '';
}

function has_dom_class( DOMElement $node, $class_name ) {
	return in_array( $class_name, dom_classes( $node ), true );
}

function dom_classes( DOMElement $node ) {
	$class = $node->getAttribute( 'class' );

	return '' === $class ? array() : preg_split( '/\s+/', trim( $class ) );
}

function set_dom_classes( DOMElement $node, array $classes ) {
	$classes = array_values( array_unique( array_filter( $classes ) ) );

	if ( $classes ) {
		$node->setAttribute( 'class', implode( ' ', $classes ) );
	}
}

function get_resume_upload_field() {
	ob_start();
	?>
	<li class="wpuf-el attach resume-upload">
		<div class="wpuf-label">&nbsp;</div>
		<div class="wpuf-fields">
			<div class="input-group mb-3 file-upload">
				<input type="file" id="resume" hidden name="resume" accept=".pdf,.doc,.docx">
				<label class="form-control file-label" for="resume" id="resume-label"><?php esc_html_e( 'Attach Resume', TEXT_DOMAIN ); ?></label>
				<label class="input-group-text" for="resume"><?php esc_html_e( 'Browse', TEXT_DOMAIN ); ?></label>
			</div>
			<input type="hidden" name="resume_attachment_id" value="">
			<span class="resume-upload-message" aria-live="polite"></span>
			<div class="input-group file-upload photo-upload">
				<label class="form-control file-label" for="photo" id="photo-label"><?php esc_html_e( 'Attach Photo', TEXT_DOMAIN ); ?></label>
				<label class="input-group-text" for="photo"><?php esc_html_e( 'Browse', TEXT_DOMAIN ); ?></label>
				<input type="file" id="photo" hidden name="photo" accept="image/*">
			</div>
			<input type="hidden" name="photo_attachment_id" value="">
			<span class="photo-upload-message" aria-live="polite"></span>
		</div>
	</li>
	<?php

	return ob_get_clean();
}

add_action( 'wp_ajax_upload_resume', 'upload_resume' );
add_action( 'wp_ajax_nopriv_upload_resume', 'upload_resume' );
add_action( 'wp_ajax_upload_photo', 'upload_photo' );
add_action( 'wp_ajax_nopriv_upload_photo', 'upload_photo' );

function upload_resume() {
	handle_application_upload(
		'resume',
		array(
			'pdf'  => 'application/pdf',
			'doc'  => 'application/msword',
			'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
		),
		__( 'Please choose a resume file.', TEXT_DOMAIN )
	);
}

function upload_photo() {
	handle_application_upload(
		'photo',
		array(
			'jpg'  => 'image/jpeg',
			'jpeg' => 'image/jpeg',
			'png'  => 'image/png',
		),
		__( 'Please choose a photo file.', TEXT_DOMAIN )
	);
}

function handle_application_upload( $field_name, array $allowed_mimes, $missing_message ) {
	check_ajax_referer( 'resume_upload', 'nonce' );

	if ( empty( $_FILES[ $field_name ] ) || ! isset( $_FILES[ $field_name ]['tmp_name'] ) ) {
		wp_send_json_error( $missing_message );
	}

	$file = $_FILES[ $field_name ];

	if ( ! empty( $file['size'] ) && $file['size'] > 5 * 1024 * 1024 ) {
		wp_send_json_error( __( 'The uploaded file is too large.', TEXT_DOMAIN ) );
	}

	$filetype = wp_check_filetype_and_ext( $file['tmp_name'], $file['name'], $allowed_mimes );

	if ( empty( $filetype['ext'] ) || empty( $filetype['type'] ) ) {
		wp_send_json_error( __( 'You are not allowed to upload files of this type.', TEXT_DOMAIN ) );
	}

	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$upload = wp_handle_upload(
		$file,
		array(
			'test_form' => false,
			'mimes'     => $allowed_mimes,
		)
	);

	if ( isset( $upload['error'] ) ) {
		wp_send_json_error( $upload['error'] );
	}

	$attachment_id = wp_insert_attachment(
		array(
			'post_mime_type' => $upload['type'],
			'post_title'     => sanitize_file_name( pathinfo( $upload['file'], PATHINFO_FILENAME ) ),
			'post_content'   => '',
			'post_status'    => 'inherit',
		),
		$upload['file']
	);

	if ( is_wp_error( $attachment_id ) ) {
		wp_send_json_error( $attachment_id->get_error_message() );
	}

	wp_update_attachment_metadata( $attachment_id, wp_generate_attachment_metadata( $attachment_id, $upload['file'] ) );
	wp_send_json_success(
		array(
			'attachment_id' => $attachment_id,
			'filename'      => basename( $upload['file'] ),
			'url'           => $upload['url'],
		)
	);
}

add_action( 'wpuf_add_post_after_insert', 'save_application_meta', 10, 4 );

function save_application_meta( $post_id, $form_id, $form_settings, $meta_vars ) {
	if ( get_apply_job_form_id() !== absint( $form_id ) || POST_TYPE_JOB_APPLICATION !== get_post_type( $post_id ) ) {
		return;
	}

	attach_application_job( $post_id );
	attach_application_upload( $post_id, 'resume_attachment_id', 'resume' );
	attach_application_upload( $post_id, 'photo_attachment_id', 'photo' );
}

function attach_application_job( $post_id ) {
	$job_id = isset( $_POST['applied_job_id'] ) ? absint( wp_unslash( $_POST['applied_job_id'] ) ) : 0;

	if ( ! $job_id || POST_TYPE_JOB !== get_post_type( $job_id ) ) {
		return;
	}

	update_post_meta( $post_id, 'applied_job_id', $job_id );
	update_post_meta( $post_id, '_applied_job_id', 'field_application_applied_job' );
}

add_filter( 'manage_' . POST_TYPE_JOB_APPLICATION . '_posts_columns', 'add_application_columns' );

function add_application_columns( $columns ) {
	$updated = array();

	foreach ( $columns as $key => $label ) {
		$updated[ $key ] = $label;

		if ( 'title' === $key ) {
			$updated['applied-job'] = __( 'Applied Job', TEXT_DOMAIN );
		}
	}

	return $updated;
}

add_action( 'manage_' . POST_TYPE_JOB_APPLICATION . '_posts_custom_column', 'render_application_columns', 10, 2 );

function render_application_columns( $column, $post_id ) {
	if ( 'applied-job' !== $column ) {
		return;
	}

	$job_id = absint( get_post_meta( $post_id, 'applied_job_id', true ) );

	if ( ! $job_id || POST_TYPE_JOB !== get_post_type( $job_id ) ) {
		echo '&mdash;';
		return;
	}

	printf(
		'<a href="%s">%s</a>',
		esc_url( get_edit_post_link( $job_id ) ),
		esc_html( get_the_title( $job_id ) )
	);
}

function attach_application_upload( $post_id, $attachment_field, $url_field ) {
	$attachment_id = isset( $_POST[ $attachment_field ] ) ? absint( wp_unslash( $_POST[ $attachment_field ] ) ) : 0;

	if ( ! $attachment_id || 'attachment' !== get_post_type( $attachment_id ) ) {
		return;
	}

	update_post_meta( $post_id, $attachment_field, $attachment_id );
	update_post_meta( $post_id, $url_field, wp_get_attachment_url( $attachment_id ) );
	wp_update_post(
		array(
			'ID'          => $attachment_id,
			'post_parent' => $post_id,
		)
	);
}

add_filter( 'wpuf_add_post_redirect', 'keep_application_on_current_page', 10, 4 );

function keep_application_on_current_page( $response, $post_id, $form_id, $form_settings ) {
	if ( get_apply_job_form_id() !== absint( $form_id ) || POST_TYPE_JOB_APPLICATION !== get_post_type( $post_id ) ) {
		return $response;
	}

	$current_page = wp_get_referer();

	if ( ! $current_page && ! empty( $_POST['page_id'] ) ) {
		$current_page = get_permalink( absint( wp_unslash( $_POST['page_id'] ) ) );
	}

	if ( ! $current_page ) {
		$current_page = home_url( '/' );
	}

	$response['redirect_to']  = esc_url_raw( $current_page );
	$response['show_message'] = false;

	return $response;
}
