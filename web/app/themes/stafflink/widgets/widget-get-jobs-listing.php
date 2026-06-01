<?php
/**
 * Jobseeker widget.
 *
 * @package WP_Stafflink
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WidgetGetJobListing extends WP_Widget {
	public function __construct() {
		parent::__construct(
			'widget_get_jobseekers',
			__( 'Jobseeker', TEXT_DOMAIN ),
			array( 'description' => __( 'Render the Stafflink Jobseeker.', TEXT_DOMAIN ) )
		);
	}

	public function widget( $args, $instance ) {
		$title          = isset( $instance['title'] ) ? $instance['title'] : __( 'Jobseeker', TEXT_DOMAIN );
		$posts_per_page = isset( $instance['posts_per_page'] ) ? absint( $instance['posts_per_page'] ) : 8;
		$search         = isset( $_GET['job_search'] ) ? sanitize_text_field( wp_unslash( $_GET['job_search'] ) ) : '';
		$location       = isset( $_GET['job_location'] ) ? sanitize_text_field( wp_unslash( $_GET['job_location'] ) ) : '';
		$category       = isset( $_GET['job_category'] ) ? sanitize_text_field( wp_unslash( $_GET['job_category'] ) ) : '';
		$paged          = max( 1, get_query_var( 'paged' ), isset( $_GET['job_page'] ) ? absint( $_GET['job_page'] ) : 1 );

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
				'posts_per_page' => $posts_per_page,
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
		$jobseekers_page     = get_page_by_path( 'jobseekers' );
		$jobseekers_page_url = $jobseekers_page ? get_permalink( $jobseekers_page ) : home_url( '/jobseekers/' );

		echo $args['before_widget']; 
		?>
		<section class="jobseekers">
			<?php if ( $title ) : ?>
				<h1 class="jobseekers-heading"><?php echo esc_html( $title ); ?></h1>
			<?php endif; ?>

			<form class="jobseekers-filters" method="get" action="<?php echo esc_url( $jobseekers_page_url ); ?>">
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
					<a href="<?php echo esc_url( $jobseekers_page_url ); ?>" aria-label="<?php esc_attr_e( 'Reset filters', TEXT_DOMAIN ); ?>">
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
							$job_location  = '';

							if ( ! empty( $job_locations ) && ! is_wp_error( $job_locations ) ) {
								$job_location = implode( ', ', wp_list_pluck( $job_locations, 'name' ) );
							}
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

		echo $args['after_widget'];
	}

	public function form( $instance ) {
		$title          = isset( $instance['title'] ) ? $instance['title'] : __( 'Jobseeker', TEXT_DOMAIN );
		$posts_per_page = isset( $instance['posts_per_page'] ) ? absint( $instance['posts_per_page'] ) : 8;
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title', TEXT_DOMAIN ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'posts_per_page' ) ); ?>"><?php esc_html_e( 'Posts per page', TEXT_DOMAIN ); ?></label>
			<input class="tiny-text" id="<?php echo esc_attr( $this->get_field_id( 'posts_per_page' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'posts_per_page' ) ); ?>" type="number" min="1" value="<?php echo esc_attr( $posts_per_page ); ?>">
		</p>
		<?php
	}

	public function update( $new_instance, $old_instance ) {
		$instance                   = $old_instance;
		$instance['title']          = isset( $new_instance['title'] ) ? sanitize_text_field( $new_instance['title'] ) : '';
		$instance['posts_per_page'] = isset( $new_instance['posts_per_page'] ) ? absint( $new_instance['posts_per_page'] ) : 8;

		return $instance;
	}
}
