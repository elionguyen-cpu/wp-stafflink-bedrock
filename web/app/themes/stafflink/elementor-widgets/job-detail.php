<?php
/**
 * Job detail Elementor widget.
 *
 * @package WP_Stafflink
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( '\Elementor\Widget_Base' ) ) {
	return;
}

class Stafflink_Job_Detail_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'stafflink_job_detail';
	}

	public function get_title() {
		return __( '[BE] Job Detail', TEXT_DOMAIN );
	}

	public function get_icon() {
		return 'eicon-post-content';
	}

	public function get_categories() {
		return array( 'be-elements' );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'content_section',
			array(
				'label' => __( 'Content', TEXT_DOMAIN ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'job_id',
			array(
				'label'       => __( 'Job', TEXT_DOMAIN ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'options'     => $this->get_job_options(),
				'default'     => '',
				'label_block' => true,
			)
		);

		$this->add_control(
			'heading',
			array(
				'label'   => __( 'Heading', TEXT_DOMAIN ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Job Detail', TEXT_DOMAIN ),
			)
		);

		$this->add_control(
			'back_label',
			array(
				'label'   => __( 'Back Label', TEXT_DOMAIN ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Back to Listing', TEXT_DOMAIN ),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$job_id   = $this->get_job_id( $settings );

		if ( ! $job_id ) {
			return;
		}

		$job = get_post( $job_id );

		if ( ! $job || POST_TYPE_JOB !== $job->post_type ) {
			return;
		}

		$jobseekers_page = get_page_by_path( 'jobseekers' );
		$back_url        = $jobseekers_page ? get_permalink( $jobseekers_page ) : home_url( '/jobseekers/' );
		?>
		<section class="single-job">
			<?php if ( ! empty( $settings['heading'] ) ) : ?>
				<h1 class="single-job-heading"><?php echo esc_html( $settings['heading'] ); ?></h1>
			<?php endif; ?>

			<div class="single-job-main">
				<a class="single-job-back" href="<?php echo esc_url( $back_url ); ?>">
					<i class="bi bi-chevron-left" aria-hidden="true"></i>
					<span><?php echo esc_html( $settings['back_label'] ); ?></span>
				</a>

				<h2 class="single-job-title"><?php echo esc_html( get_the_title( $job ) ); ?></h2>

				<div class="single-job-info">
					<?php
					foreach ( $this->get_job_meta_items( $job_id ) as $item ) :
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
					<?php $this->render_job_section( __( 'Job Description', TEXT_DOMAIN ), $this->get_field_value( 'job_description', $job_id ) ); ?>
					<?php $this->render_job_section( __( 'Job Responsibilities', TEXT_DOMAIN ), $this->get_field_value( 'job_responsibilities', $job_id ) ); ?>
					<?php $this->render_job_section( __( 'Requirements', TEXT_DOMAIN ), $this->get_field_value( 'job_requirements', $job_id ) ); ?>
				</div>

				<?php if ( empty( $this->get_field_value( 'job_description', $job_id ) ) && empty( $this->get_field_value( 'job_responsibilities', $job_id ) ) && empty( $this->get_field_value( 'job_requirements', $job_id ) ) && ! empty( $job->post_content ) ) : ?>
					<div class="single-job-content">
						<?php echo apply_filters( 'the_content', $job->post_content ); ?>
					</div>
				<?php endif; ?>
			</div>
		</section>
		<?php
	}

	private function get_job_options() {
		$options = array(
			'' => __( 'Current Job', TEXT_DOMAIN ),
		);

		$jobs = get_posts(
			array(
				'post_type'      => POST_TYPE_JOB,
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'orderby'        => 'title',
				'order'          => 'ASC',
			)
		);

		foreach ( $jobs as $job ) {
			$options[ $job->ID ] = get_the_title( $job );
		}

		return $options;
	}

	private function get_job_id( $settings ) {
		if ( ! empty( $settings['job_id'] ) ) {
			return absint( $settings['job_id'] );
		}

		if ( is_singular( POST_TYPE_JOB ) ) {
			return get_the_ID();
		}

		$jobs = get_posts(
			array(
				'post_type'      => POST_TYPE_JOB,
				'post_status'    => 'publish',
				'posts_per_page' => 1,
				'orderby'        => 'date',
				'order'          => 'DESC',
				'fields'         => 'ids',
			)
		);

		return $jobs ? absint( $jobs[0] ) : 0;
	}

	private function get_job_meta_items( $job_id ) {
		return array(
			array(
				'label' => __( 'Consultant Code', TEXT_DOMAIN ),
				'value' => $this->get_field_value( 'job_consultant_code', $job_id ),
			),
			array(
				'label' => __( 'Zone', TEXT_DOMAIN ),
				'value' => $this->get_terms_text( $job_id, TAX_TYPE_JOB_LOCATION ),
			),
			array(
				'label' => __( 'Job Type', TEXT_DOMAIN ),
				'value' => $this->get_field_value( 'job_type', $job_id ),
			),
			array(
				'label' => __( 'Job Category', TEXT_DOMAIN ),
				'value' => $this->get_terms_text( $job_id, TAX_TYPE_JOB_CATEGORY ),
			),
			array(
				'label' => __( 'Working Hours', TEXT_DOMAIN ),
				'value' => $this->get_field_value( 'job_working_hours', $job_id ),
			),
			array(
				'label' => __( 'Salary', TEXT_DOMAIN ),
				'value' => $this->get_field_value( 'job_salary', $job_id ),
			),
		);
	}

	private function get_field_value( $field_name, $post_id, $fallback = '' ) {
		$value = function_exists( 'get_field' ) ? get_field( $field_name, $post_id ) : get_post_meta( $post_id, $field_name, true );

		if ( '' === $value || null === $value ) {
			return $fallback;
		}

		return $value;
	}

	private function get_terms_text( $post_id, $taxonomy ) {
		$terms = get_the_terms( $post_id, $taxonomy );

		if ( empty( $terms ) || is_wp_error( $terms ) ) {
			return '';
		}

		return implode( ', ', wp_list_pluck( $terms, 'name' ) );
	}

	private function render_job_section( $title, $content ) {
		if ( empty( $content ) ) {
			return;
		}
		?>
		<div class="single-job-section">
			<h3><?php echo esc_html( $title ); ?></h3>
			<div class="single-job-text">
				<?php echo wp_kses_post( $this->format_rich_text( $content ) ); ?>
			</div>
		</div>
		<?php
	}

	private function format_rich_text( $content ) {
		if ( false !== strpos( $content, '<' ) ) {
			return $content;
		}

		return wpautop( $content );
	}
}

if ( isset( $widgets_manager ) ) {
	$widgets_manager->register( new Stafflink_Job_Detail_Widget() );
}
