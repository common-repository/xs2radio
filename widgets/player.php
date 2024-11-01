<?php

class XS2Radio_Player_Widget extends WP_Widget
{
	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		$widget_ops = array( 
			'classname' => 'xs2radio_player_widget',
			'description' => '',
		);
		parent::__construct( 'xs2radio_player_widget', __( 'XS2Radio: Player', 'xs2radio' ), $widget_ops );
	}


	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		$post_id = null;

		if( $instance['what'] == 'current' && is_singular() && !is_admin() )
		{
			$post_id = get_the_ID();
		}
		else if( $instance['what'] == 'latest' || is_admin() || defined('REST_REQUEST') )
		{
			$query = new WP_Query([
				'post_type' => XS2Radio_Settings::get_option( 'post-types' ),
				'posts_per_page' => 1,
				'meta_query' => [
					[
						'key'     => 'xs2radio_audio_url',
						'compare' => 'EXISTS',
					],
				]
			]);

			if ( isset( $query->posts[0] ) )
			{
				$post_id = $query->posts[0]->ID;
			}
		}

		if ( $post_id )
		{
			echo $args['before_widget'];

			if ( ! empty( $instance['title'] ) )
			{
				echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
			}

			$player = new XS2Radio_Player();
			$player->set_post_data( $post_id );
			$player->set_setting( 'image', ! empty( $instance['display_image'] ) ? $instance['display_image'] : false );
			$player->set_setting( 'type', 'widget' );

			if ( $player->has_item() )
			{
				echo $player->get_html();
			}
			
			echo $args['after_widget'];
		}
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {
		$title         = ! empty( $instance['title'] ) ? $instance['title'] : '';
		$what          = ! empty( $instance['what'] ) ? $instance['what'] : 'current';
		$display_image = ! empty( $instance['display_image'] ) ? $instance['display_image'] : false;

		$what_options = [
			'current' => __( 'Current page XS2Radio entry', 'xs2radio' ),
			'latest'  => __( 'Latest XS2Radio entry', 'xs2radio' ),
		]
		?>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'xs2radio' ); ?></label> 
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'what' ) ); ?>"><?php esc_attr_e( 'What:', 'xs2radio' ); ?></label> 
			<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'what' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'what' ) ); ?>">
				<?php
				foreach ($what_options as $key => $title) {
					echo '<option value="' . $key . '" ' . selected($key, $what, false) . '>' . $title . '</option>';
				}
				?>
			</select>
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
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? sanitize_text_field( $new_instance['title'] ) : '';
		$instance['what'] = ( ! empty( $new_instance['what'] ) ) ? sanitize_text_field( $new_instance['what'] ) : 'current';
		$instance['display_image'] = ( ! empty( $new_instance['display_image'] ) ) ? (boolean) $new_instance['display_image'] : false;

		return $instance;
	}
}