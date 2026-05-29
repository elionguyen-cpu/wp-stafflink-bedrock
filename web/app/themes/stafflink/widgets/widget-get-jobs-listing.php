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

		echo $args['before_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo get_jobseeker_html(
			array(
				'title'          => $title,
				'posts_per_page' => $posts_per_page,
			)
		); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $args['after_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
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
