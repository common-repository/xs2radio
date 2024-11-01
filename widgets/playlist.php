<?php

class XS2Radio_Playlist_Widget extends WP_Widget
{
	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		$widget_ops = array( 
			'classname' => 'xs2radio_playlist_widget',
			'description' => '',
		);
		parent::__construct( 'xs2radio_playlist_widget', __( 'XS2Radio: Playlist', 'xs2radio' ), $widget_ops );
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {		
		$playlist = new XS2Radio_Playlist();
		$playlist->load_from_query( $instance['amount'], $instance['category'] );
		$playlist->set_setting( 'image', ! empty( $instance['display_image'] ) ? $instance['display_image'] : false );
		$playlist->set_setting( 'type', 'widget' );

		if ( $playlist->has_items() )
		{
			echo $args['before_widget'];

			if ( ! empty( $instance['title'] ) ) {
				echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
			}

			echo $playlist->get_html();

			echo $args['after_widget'];

			XS2Radio::load_frontend_styles();
		}
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {
		$title         = ! empty( $instance['title'] ) ? $instance['title'] : '';
		$category      = ! empty( $instance['category'] ) ? $instance['category'] : 0;
		$amount        = ! empty( $instance['amount'] ) ? $instance['amount'] : 5;
		$display_image = ! empty( $instance['display_image'] ) ? $instance['display_image'] : false;
		?>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'xs2radio' ); ?></label> 
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'category' ) ); ?>"><?php esc_attr_e( 'Category:', 'xs2radio' ); ?></label> 
			<?php
			$args = [
				'id'              => esc_attr( $this->get_field_id( 'category' ) ),
				'name'            => esc_attr( $this->get_field_name( 'category' ) ),
				'class'           => 'widefat',
				'show_option_all' => __( 'All', 'xs2radio' ),
				'selected'        => $category,
			];
			wp_dropdown_categories( $args );
			?>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'amount' ) ); ?>"><?php esc_attr_e( 'Amount:', 'xs2radio' ); ?></label> 
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'amount' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'amount' ) ); ?>" type="number" value="<?php echo esc_attr( $amount ); ?>">
		</p>
		<p>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'display_image' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'display_image' ) ); ?>" type="checkbox" value="1" <?php checked( $display_image, true ); ?>>
			<label for="<?php echo esc_attr( $this->get_field_id( 'display_image' ) ); ?>"><?php esc_attr_e( 'Toon afbeelding?', 'xs2radio' ); ?></label> 
		</p>
		<?php 
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 *
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = [];

		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? sanitize_text_field( $new_instance['title'] ) : '';
		$instance['category'] = ( ! empty( $new_instance['category'] ) ) ? absint( $new_instance['category'] ) : 0;
		$instance['amount'] = ( ! empty( $new_instance['amount'] ) ) ? absint( $new_instance['amount'] ) : 5;
		$instance['display_image'] = ( ! empty( $new_instance['display_image'] ) ) ? (boolean) $new_instance['display_image'] : false;

		return $instance;
	}
}