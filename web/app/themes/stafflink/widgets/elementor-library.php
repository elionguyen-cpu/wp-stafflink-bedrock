<?php
/**
 * Elementor saved template widget.
 *
 * @package WP_Stafflink
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'elementor_template' ) ) {
	/**
	 * Render an Elementor saved template.
	 *
	 * @param int $template_id Elementor template post ID.
	 */
	function elementor_template( $template_id ) {
		$template_id = absint( $template_id );

		if ( ! $template_id || ! class_exists( '\Elementor\Plugin' ) ) {
			return;
		}

		echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $template_id );
	}
}

class ElementorLibraryWidget extends WP_Widget {
	/**
	 * Register widget.
	 */
	public function __construct() {
		parent::__construct(
			'elementor_library_widget',
			__( 'Elementor Library Template', TEXT_DOMAIN ),
			array(
				'classname'   => 'elementor_library_widget',
				'description' => __( 'Render an Elementor saved template by template ID.', TEXT_DOMAIN ),
			)
		);
	}

	/**
	 * Render widget output.
	 *
	 * @param array $args Widget args.
	 * @param array $instance Widget instance.
	 */
	public function widget( $args, $instance ) {
		$template_id = isset( $instance['template_id'] ) ? absint( $instance['template_id'] ) : 0;

		if ( ! $template_id || ! function_exists( 'elementor_template' ) ) {
			return;
		}

		echo $args['before_widget'];
		elementor_template( $template_id );
		echo $args['after_widget'];
	}

	/**
	 * Render admin form.
	 *
	 * @param array $instance Widget instance.
	 */
	public function form( $instance ) {
		$template_id = isset( $instance['template_id'] ) ? absint( $instance['template_id'] ) : '';
		$templates   = get_posts(
			array(
				'post_type'      => 'elementor_library',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'orderby'        => 'title',
				'order'          => 'ASC',
			)
		);
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'template_id' ) ); ?>">
				<?php esc_html_e( 'Elementor Template ID', TEXT_DOMAIN ); ?>
			</label>
			<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'template_id' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'template_id' ) ); ?>">
				<option value="0"><?php esc_html_e( 'Select a template', TEXT_DOMAIN ); ?></option>
				<?php foreach ( $templates as $template ) : ?>
					<option value="<?php echo esc_attr( $template->ID ); ?>" <?php selected( $template_id, $template->ID ); ?>>
						<?php echo esc_html( $template->post_title ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</p>
		<?php
	}

	/**
	 * Save admin form.
	 *
	 * @param array $new_instance New instance.
	 * @param array $old_instance Old instance.
	 *
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		$instance                = $old_instance;
		$instance['template_id'] = isset( $new_instance['template_id'] ) ? absint( $new_instance['template_id'] ) : 0;

		return $instance;
	}
}
