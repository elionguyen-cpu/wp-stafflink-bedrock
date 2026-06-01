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
	?>
	<div class="container">
		<section class="single-job">
			<h2 class="single-job-heading"><?php esc_html_e( 'Job Detail', TEXT_DOMAIN ); ?></h2>

			<div class="single-job-layout">
				<div class="single-job-main">
					<a class="single-job-back" href="<?php echo esc_url( get_jobseekers_page_url() ); ?>">
						<i class="bi bi-chevron-left" aria-hidden="true"></i>
						<span><?php esc_html_e( 'Back to Listing', TEXT_DOMAIN ); ?></span>
					</a>

					<h1 class="single-job-title"><?php the_title(); ?></h1>

					<div class="single-job-info">
						<?php
						foreach ( get_job_detail_items( get_the_ID() ) as $item ) :
							if ( '' === $item['value'] ) {
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
						<?php render_job_detail_section( __( 'Job Description', TEXT_DOMAIN ), get_job_field_value( 'job_description', get_the_ID() ) ); ?>
						<?php render_job_detail_section( __( 'Job Responsibilities', TEXT_DOMAIN ), get_job_field_value( 'job_responsibilities', get_the_ID() ) ); ?>
						<?php render_job_detail_section( __( 'Requirements', TEXT_DOMAIN ), get_job_field_value( 'job_requirements', get_the_ID() ) ); ?>
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
