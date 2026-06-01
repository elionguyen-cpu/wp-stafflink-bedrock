<?php
/**
 * Jobseeker renderer.
 *
 * @package WP_Stafflink
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function get_jobseekers_page_url() {
	$jobseekers_page = get_page_by_path( 'jobseekers' );

	return $jobseekers_page ? get_permalink( $jobseekers_page ) : home_url( '/jobseekers/' );
}

function get_job_detail_items( $post_id ) {
	return array(
		array(
			'label' => __( 'Consultant Code', TEXT_DOMAIN ),
			'value' => get_job_field_value( 'job_consultant_code', $post_id ),
		),
		array(
			'label' => __( 'Zone', TEXT_DOMAIN ),
			'value' => get_job_terms_text( $post_id, TAX_TYPE_JOB_LOCATION ),
		),
		array(
			'label' => __( 'Job Type', TEXT_DOMAIN ),
			'value' => get_job_field_value( 'job_type', $post_id ),
		),
		array(
			'label' => __( 'Job Category', TEXT_DOMAIN ),
			'value' => get_job_terms_text( $post_id, TAX_TYPE_JOB_CATEGORY ),
		),
		array(
			'label' => __( 'Working Hours', TEXT_DOMAIN ),
			'value' => get_job_field_value( 'job_working_hours', $post_id ),
		),
		array(
			'label' => __( 'Salary', TEXT_DOMAIN ),
			'value' => get_job_field_value( 'job_salary', $post_id ),
		),
	);
}

function get_job_field_value( $field_name, $post_id ) {
	$value = function_exists( 'get_field' ) ? get_field( $field_name, $post_id ) : get_post_meta( $post_id, $field_name, true );

	return ( '' === $value || null === $value ) ? '' : $value;
}

function get_job_terms_text( $post_id, $taxonomy ) {
	$terms = get_the_terms( $post_id, $taxonomy );

	if ( empty( $terms ) || is_wp_error( $terms ) ) {
		return '';
	}

	return implode( ', ', wp_list_pluck( $terms, 'name' ) );
}

function render_job_detail_section( $title, $content ) {
	if ( empty( $content ) ) {
		return;
	}
	?>
	<div class="single-job-section">
		<h3><?php echo esc_html( $title ); ?></h3>
		<div class="single-job-text">
			<?php echo wp_kses_post( format_job_detail_content( $content ) ); ?>
		</div>
	</div>
	<?php
}

function format_job_detail_content( $content ) {
	if ( false !== strpos( $content, '<' ) ) {
		return $content;
	}

	return wpautop( $content );
}
