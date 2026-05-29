<?php
/**
 * Jobseeker renderer.
 *
 * @package WP_Stafflink
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function get_jobseeker_html( $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'title'          => __( 'Jobseeker', TEXT_DOMAIN ),
			'posts_per_page' => 8,
		)
	);

	$search   = isset( $_GET['job_search'] ) ? sanitize_text_field( wp_unslash( $_GET['job_search'] ) ) : '';
	$location = isset( $_GET['job_location'] ) ? sanitize_text_field( wp_unslash( $_GET['job_location'] ) ) : '';
	$category = isset( $_GET['job_category'] ) ? sanitize_text_field( wp_unslash( $_GET['job_category'] ) ) : '';
	$paged    = max( 1, get_query_var( 'paged' ), isset( $_GET['job_page'] ) ? absint( $_GET['job_page'] ) : 1 );

	$tax_query = array();

	if ( $location ) {
		$tax_query[] = array(
			'taxonomy' => TAX_TYPE_JOB_LOCATION,
			'field'    => 'slug',
			'terms'    => $location,
		);
	}

	if ( $category ) {
		$tax_query[] = array(
			'taxonomy' => TAX_TYPE_JOB_CATEGORY,
			'field'    => 'slug',
			'terms'    => $category,
		);
	}

	if ( count( $tax_query ) > 1 ) {
		$tax_query['relation'] = 'AND';
	}

	$query = new WP_Query(
		array(
			'post_type'      => POST_TYPE_JOB,
			'post_status'    => 'publish',
			's'              => $search,
			'posts_per_page' => absint( $args['posts_per_page'] ),
			'paged'          => $paged,
			'orderby'        => array(
				'menu_order' => 'ASC',
				'date'       => 'DESC',
			),
			'tax_query'      => $tax_query,
		)
	);

	$locations  = get_terms( array( 'taxonomy' => TAX_TYPE_JOB_LOCATION, 'hide_empty' => false ) );
	$categories = get_terms( array( 'taxonomy' => TAX_TYPE_JOB_CATEGORY, 'hide_empty' => false ) );

	ob_start();
	?>
	<section class="jobseekers">
		<?php if ( $args['title'] ) : ?>
			<h1 class="jobseekers-heading"><?php echo esc_html( $args['title'] ); ?></h1>
		<?php endif; ?>

		<form class="jobseekers-filters" method="get" action="<?php echo esc_url( get_permalink() ); ?>">
			<div class="jobseekers-field search-input">
				<i class="bi bi-search" aria-hidden="true"></i>
				<input class="form-control" type="search" name="job_search" value="<?php echo esc_attr( $search ); ?>" placeholder="<?php esc_attr_e( 'Search Job', TEXT_DOMAIN ); ?>">
			</div>

			<div class="jobseekers-field">
				<select class="form-select js-select2 js-job-filter-select" name="job_location" data-placeholder="<?php esc_attr_e( 'Location', TEXT_DOMAIN ); ?>" data-placeholder-icon="bi bi-geo-alt">
					<option value=""><?php esc_html_e( 'Location', TEXT_DOMAIN ); ?></option>
					<?php foreach ( $locations as $term ) : ?>
						<option value="<?php echo esc_attr( $term->slug ); ?>" <?php selected( $location, $term->slug ); ?>><?php echo esc_html( $term->name ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>

			<div class="jobseekers-field">
				<select class="form-select js-select2 js-job-filter-select" name="job_category" data-placeholder="<?php esc_attr_e( 'Job Category', TEXT_DOMAIN ); ?>" data-placeholder-icon="bi bi-briefcase">
					<option value=""><?php esc_html_e( 'Job Category', TEXT_DOMAIN ); ?></option>
					<?php foreach ( $categories as $term ) : ?>
						<option value="<?php echo esc_attr( $term->slug ); ?>" <?php selected( $category, $term->slug ); ?>><?php echo esc_html( $term->name ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>

			<div class="jobseekers-actions">
				<a href="<?php echo esc_url( get_permalink() ); ?>" aria-label="<?php esc_attr_e( 'Reset filters', TEXT_DOMAIN ); ?>">
					<i class="bi bi-arrow-clockwise" aria-hidden="true"></i>
				</a>
				<button type="submit" aria-label="<?php esc_attr_e( 'Search jobs', TEXT_DOMAIN ); ?>">
					<i class="bi bi-search" aria-hidden="true"></i>
				</button>
			</div>
		</form>

		<div class="jobseekers-list">
			<div class="jobseekers-items">
				<?php if ( $query->have_posts() ) : ?>
					<?php
					while ( $query->have_posts() ) :
						$query->the_post();
						$job_locations = get_the_terms( get_the_ID(), TAX_TYPE_JOB_LOCATION );
						$job_location  = ! empty( $job_locations ) && ! is_wp_error( $job_locations ) ? $job_locations[0]->name : '';
						?>
						<article <?php post_class( 'job-card' ); ?>>
							<a class="job-card-title" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							<span class="job-card-location">
								<i class="bi bi-geo-alt" aria-hidden="true"></i>
								<?php echo esc_html( $job_location ); ?>
							</span>
							<time class="job-card-date" datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>">
								<i class="bi bi-calendar-date" aria-hidden="true"></i>
								<?php echo esc_html( get_the_date( 'd M Y' ) ); ?>
							</time>
						</article>
					<?php endwhile; ?>
				<?php else : ?>
					<p class="jobseekers-empty"><?php esc_html_e( 'No jobs found.', TEXT_DOMAIN ); ?></p>
				<?php endif; ?>
			</div>

			<?php
			$pagination = paginate_links(
				array(
					'total'     => $query->max_num_pages,
					'current'   => $paged,
					'format'    => '?job_page=%#%',
					'prev_text' => '<i class="bi bi-chevron-left" aria-hidden="true"></i>',
					'next_text' => '<i class="bi bi-chevron-right" aria-hidden="true"></i>',
				)
			);
			?>
			<?php if ( $pagination ) : ?>
				<nav class="jobseekers-pagination" aria-label="<?php esc_attr_e( 'Jobs pagination', TEXT_DOMAIN ); ?>">
					<?php echo wp_kses_post( $pagination ); ?>
				</nav>
			<?php endif; ?>
		</div>
	</section>
	<?php
	wp_reset_postdata();

	return ob_get_clean();
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
