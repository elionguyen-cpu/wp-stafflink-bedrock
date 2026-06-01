<?php
/**
 * Single jobseekers template.
 *
 * @package WP_Stafflink
 */

get_header();

get_template_part( 'template-parts/banner' );
get_template_part( 'template-parts/mailing' ); 
get_template_part( 'template-parts/breadcrumbs' );

?>

<?php
while ( have_posts() ) :
	the_post();

	$post_content = trim( get_the_content() );
	$apply_form   = get_apply_job_form();
	$locations    = get_the_terms( get_the_ID(), TAX_TYPE_JOB_LOCATION );
	$categories   = get_the_terms( get_the_ID(), TAX_TYPE_JOB_CATEGORY );
	$location     = ( ! empty( $locations ) && ! is_wp_error( $locations ) ) ? implode( ', ', wp_list_pluck( $locations, 'name' ) ) : '';
	$category     = ( ! empty( $categories ) && ! is_wp_error( $categories ) ) ? implode( ', ', wp_list_pluck( $categories, 'name' ) ) : '';
	$job_details  = array(
		array(
			'label' => __( 'Consultant Code', TEXT_DOMAIN ),
			'value' => get_field( 'job_consultant_code' ),
		),
		array(
			'label' => __( 'Zone', TEXT_DOMAIN ),
			'value' => $location,
		),
		array(
			'label' => __( 'Job Type', TEXT_DOMAIN ),
			'value' => get_field( 'job_type' ),
		),
		array(
			'label' => __( 'Job Category', TEXT_DOMAIN ),
			'value' => $category,
		),
		array(
			'label' => __( 'Working Hours', TEXT_DOMAIN ),
			'value' => get_field( 'job_working_hours' ),
		),
		array(
			'label' => __( 'Salary', TEXT_DOMAIN ),
			'value' => get_field( 'job_salary' ),
		),
	);
	$job_sections = array(
		__( 'Job Description', TEXT_DOMAIN )      => get_field( 'job_description' ),
		__( 'Job Responsibilities', TEXT_DOMAIN ) => get_field( 'job_responsibilities' ),
		__( 'Requirements', TEXT_DOMAIN )         => get_field( 'job_requirements' ),
	);
	?>
	<div class="container">
		<section class="single-job">
			<h2 class="single-job-heading"><?php esc_html_e( 'Job Detail', TEXT_DOMAIN ); ?></h2>
			<div class="single-job-layout">
				<div class="single-job-main">
					<a class="single-job-back" href="<?php echo esc_url( wp_get_referer() ); ?>">
						<i class="bi bi-chevron-left" aria-hidden="true"></i>
						<span><?php esc_html_e( 'Back to Listing', TEXT_DOMAIN ); ?></span>
					</a>
					<h1 class="single-job-title"><?php the_title(); ?></h1>
					<div class="single-job-info">
						<?php
						foreach ( $job_details as $item ) :
							if ( '' === $item['value'] || null === $item['value'] ) {
								continue;
							}
							?>
							<div class="single-job-item">
								<strong><?php echo esc_html( $item['label'] ); ?></strong>
								<span><?php echo esc_html( $item['value'] ); ?></span>
							</div>
						<?php endforeach; ?>
					</div>
					<div class="single-job-sections">
						<?php foreach ( $job_sections as $section_title => $section_content ) : ?>
							<?php if ( $section_content ) : ?>
								<div class="single-job-section">
									<h3><?php echo esc_html( $section_title ); ?></h3>
									<div class="single-job-text">
										<?php echo wp_kses_post( $section_content ); ?>
									</div>
								</div>
							<?php endif; ?>
						<?php endforeach; ?>
					</div>
					<?php if ( $post_content ) : ?>
						<div class="single-job-content">
							<?php the_content(); ?>
						</div>
					<?php endif; ?>
				</div>
				<aside class="single-job-sidebar">
					<div class="apply-job">
						<div class="apply-job-heading">
							<span class="apply-job-icon"><i class="bi bi-calendar3" aria-hidden="true"></i></span>
							<h2><?php esc_html_e( 'Apply This Job', TEXT_DOMAIN ); ?></h2>
						</div>
						<?php if ( $apply_form ) : ?>
							<div class="job-apply-form">
								<?php echo $apply_form; ?>
							</div>
						<?php else : ?>
							<p class="apply-job-empty">
								<?php esc_html_e( 'Choose an Apply Job form in the theme customizer to show the application form here.', TEXT_DOMAIN ); ?>
							</p>
						<?php endif; ?>
					</div>
				</aside>
			</div>
		</section>
	</div>
<?php endwhile; ?>

<?php
get_footer();
