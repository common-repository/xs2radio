<?php

class XS2Radio_Settings_Page
{
	public function __construct()
	{
		add_action( 'admin_menu', [ $this, 'register_menu' ] );
		add_action( 'current_screen', [ $this, 'register_settings' ] );
	}

	public function register_menu()
	{
		add_options_page(
			__( 'XS2Content', 'xs2radio' ),
			__( 'XS2Content', 'xs2radio' ),
			'manage_options',
			'xs2content',
			array( $this, 'settings_page' )
		);
	}

	public function register_settings( $screen )
	{
		if ( 'settings_page_xs2content' != $screen->base && 'options' != $screen->base )
		{
			return;
		}

		if ( isset($_GET['action']) && 'analytics-download' == $_GET['action'] ) {
			$analytics = new XS2Radio_Analytics;
			$analytics->generate_csv();
		}

		if ( isset( $_GET['delete-token'] ) && wp_verify_nonce( $_GET['delete-token'], 'xs2radio_remove_token' ) === 1 )
		{
			$options = get_option( 'xs2radio' );
			$options['api-key'] = '';
			$options['api-key-valid'] = false;
			
			update_option( 'xs2radio', $options );
		}

		register_setting( 'xs2radio-group', 'xs2radio', array( $this, 'sanitize_checkboxes' ) );
		add_settings_section( 'xs2radio-main', __( 'General Settings', 'xs2radio' ), [ $this, 'render_main_settings' ], 'xs2radio' );
		add_settings_section( 'xs2radio-content', __( 'Content Settings', 'xs2radio' ), [ $this, 'render_content_settings' ], 'xs2radio' );
		add_settings_section( 'xs2radio-styling', __( 'Styling Settings', 'xs2radio' ), [ $this, 'render_styling_settings' ], 'xs2radio' );
		add_settings_section( 'xs2radio-analytics', __( 'Analytics Settings', 'xs2radio' ), [ $this, 'render_analytics_settings' ], 'xs2radio' );
	}

	public function render_main_settings()
	{
		add_settings_field('api-key', __( 'API key', 'xs2radio' ), [ $this, 'setting_api_key_fn' ], 'xs2radio', 'xs2radio-main', ['name' => 'api-key']);

		if ( XS2Radio_Settings::get_option( 'api-key-valid' ) === true )
		{
			$api = XS2Radio_Settings::get_api();

			add_settings_field(
				'feed', __( 'Feed', 'xs2radio' ), [ $this, 'setting_select_fn' ], 'xs2radio', 'xs2radio-main',
				[
					'name' => 'feed',
					'options' => $api->get_feeds_selection()
				]
			);
			add_settings_field(
				'post-types', __( 'Supported Post Types', 'xs2radio' ), [ $this, 'setting_post_types_fn' ], 'xs2radio', 'xs2radio-main',
				[
					'name' => 'post-types'
				]
			);
			add_settings_field(
				'default-add', __( 'Transfer automatically', 'xs2radio' ), [ $this, 'setting_radio_fn' ], 'xs2radio', 'xs2radio-main',
				[
					'name' => 'default-add',
					'options' => [ 'On' => __( 'On', 'xs2radio' ), 'Off' => __( 'Off', 'xs2radio' ) ]
				]
			);
			add_settings_field(
				'player-template', __( 'Player template', 'xs2radio' ), [ $this, 'setting_radio_fn' ], 'xs2radio', 'xs2radio-main',
				[
					'name'    => 'player-template',
					'options' => [
						'wavesurfer' => __( 'Wavesurfer', 'xs2radio' ),
						'block'      => __( 'Block', 'xs2radio' ),
						'simple'     => __( 'Simple', 'xs2radio' ),
					]
				]
			);
		}
	}

	public function render_content_settings()
	{
		if ( XS2Radio_Settings::get_option( 'api-key-valid' ) === true )
		{
			add_settings_field(
				'adding-to-post', __( 'Adding to post', 'xs2radio' ), [ $this, 'setting_radio_fn' ], 'xs2radio', 'xs2radio-content',
				[
					'name' => 'adding-to-post',
					'options' => [
						'no' => __( 'No', 'xs2radio' ),
						'before_content' => __( 'Before content', 'xs2radio' ),
						'after_content' => __( 'After content', 'xs2radio' )
					]
				]
			);
			add_settings_field(
				'player-content', __( 'Player Content', 'xs2radio' ), [ $this, 'setting_radio_fn' ], 'xs2radio', 'xs2radio-content',
				[
					'name' => 'player-content',
					'options' => [
						'custom'        => __( 'Custom title', 'xs2radio' ),
						'title'         => __( 'Title of post', 'xs2radio' ),
						'title_excerpt' => __( 'Title and excerpt of post', 'xs2radio' )
					]
				]
			);
			add_settings_field(
				'post-player-title', __( 'Player title in post', 'xs2radio' ), [ $this, 'setting_string_fn' ], 'xs2radio', 'xs2radio-content',
				[
					'name'    => 'post-player-title',
					'default' => __( 'Listen to the audio version of this article below', 'xs2radio' ),
				]
			);
		}
	}


	public function render_styling_settings()
	{
		if ( XS2Radio_Settings::get_option( 'api-key-valid' ) === true )
		{
			echo '<p>' . __( 'The usage of these colors depends on the selected player template.', 'xs2radio' ) . '</p>';

			add_settings_field(
				'color-title', __( 'Title color', 'xs2radio' ), [ $this, 'setting_color_fn' ], 'xs2radio', 'xs2radio-styling',
				[
					'name' => 'color-title'
				]
			);
			add_settings_field(
				'color-background', __( 'Background color', 'xs2radio' ), [ $this, 'setting_color_fn' ], 'xs2radio', 'xs2radio-styling',
				[
					'name' => 'color-background',
				]
			);
			add_settings_field(
				'color-buttons', __( 'Buttons color', 'xs2radio' ), [ $this, 'setting_color_fn' ], 'xs2radio', 'xs2radio-styling',
				[
					'name' => 'color-buttons'
				]
			);

			add_settings_field(
				'color-visualisation-background', __( 'Visualisation background color', 'xs2radio' ), [ $this, 'setting_color_fn' ], 'xs2radio', 'xs2radio-styling',
				[
					'name' => 'color-visualisation-background'
				]
			);
			add_settings_field(
				'color-visualisation-wave', __( 'Visualisation bars color', 'xs2radio' ), [ $this, 'setting_color_fn' ], 'xs2radio', 'xs2radio-styling',
				[
					'name' => 'color-visualisation-wave'
				]
			);
			add_settings_field(
				'color-visualisation-progress', __( 'Visualisation progress color', 'xs2radio' ), [ $this, 'setting_color_fn' ], 'xs2radio', 'xs2radio-styling',
				[
					'name' => 'color-visualisation-progress'
				]
			);
			add_settings_field(
				'color-visualisation-currenttime', __( 'Visualisation current time', 'xs2radio' ), [ $this, 'setting_color_fn' ], 'xs2radio', 'xs2radio-styling',
				[
					'name' => 'color-visualisation-currenttime'
				]
			);

			add_settings_field(
				'color-playlist-background', __( 'Playlist Background color', 'xs2radio' ), [ $this, 'setting_color_fn' ], 'xs2radio', 'xs2radio-styling',
				[
					'name' => 'color-playlist-background'
				]
			);
		}
	}

	public function render_analytics_settings()
	{
		add_settings_field(
			'analytics-enabled', __( 'Analytics enabled', 'xs2radio' ), [ $this, 'setting_radio_fn' ], 'xs2radio', 'xs2radio-analytics',
			[
				'name' => 'analytics-enabled',
				'options' => [ 'on' => __( 'On', 'xs2radio' ), 'off' => __( 'Off', 'xs2radio' ) ]
			]
		);
	}

	public function settings_page() {
		load_template( dirname( dirname( __FILE__ ) ) . '/templates/settings.php' );

		XS2Radio::load_scripts();
	}

	public function sanitize_checkboxes( $values )
	{
		$values = array_merge( XS2Radio_Settings::get_options(), $values );

		if ( XS2Radio_Settings::get_option( 'api-key-valid' ) !== true )
		{
			$api = new XS2Radio_Api( $values['api-key'] );

			if ( $api->is_connected() )
			{
				$values['api-key-valid'] = true;
			}
			else if ( $api->get_last_error() )
			{
				add_settings_error(
			        'api-key',
			        'xs2radio-api-error',
			        $api->get_last_error()
			    );
			}
		}

		return $values;
	}



	public function setting_api_key_fn( $args )
	{
		if ( XS2Radio_Settings::get_option( 'api-key-valid' ) === true )
		{
			$api = new XS2Radio_Api( XS2Radio_Settings::get_option( $args['name'] ) );

			if ( $api->is_connected() ) {
				echo '<span class="xs2radio-valid-token">' . __( 'Valid', 'xs2radio' ) . '</span>';
			}
			else {
				echo '<span class="xs2radio-invalid-token">' . __( 'Invalid', 'xs2radio' ) . '</span>';
			}

			echo ' <a href="' . admin_url('options-general.php?page=xs2radio&delete-token') . '=' . wp_create_nonce( 'xs2radio_remove_token' ) . '" class="xs2radio-delete-token">' . __( 'Remove token', 'xs2radio' ) . '</a>';
		}
		else
		{
			$name = 'xs2radio[' . $args['name'] . ']';
			$value = XS2Radio_Settings::get_option( $args['name'] );

			echo '<input name="' . $name . '" size="40" type="password" value="' . $value .'" />';
		}
	}

	public function setting_string_fn( $args )
	{
		$name = 'xs2radio[' . $args['name'] . ']';
		$value = XS2Radio_Settings::get_option( $args['name'] );

		echo '<input name="' . $name . '" size="40" type="text" value="' . $value .'" />';
	}

	public function setting_color_fn( $args )
	{
		$name = 'xs2radio[' . $args['name'] . ']';
		$value = XS2Radio_Settings::get_option( $args['name'] );
		$default = XS2Radio_Settings::get_default_option( $args['name'] );

		echo '<input name="' . $name . '" size="40" type="text" value="' . $value .'" class="xs2radio-color-field" data-default-color="' . $default . '" />';
	}

	public function setting_select_fn( $args )
	{
		$name = 'xs2radio[' . $args['name'] . ']';
		$selected = XS2Radio_Settings::get_option( $args['name'] );

		echo '<select name="' . $name . '">';
		foreach ( $args['options'] as $key => $title )
		{
			$checked = selected( $selected, $key, false );
			echo '<option value="' . $key .'" ' . $checked . '>' . $title . '</option>';
		}
		echo '</select>';
	}

	public function setting_radio_fn( $args )
	{
		$name = 'xs2radio[' . $args['name'] . ']';
		$selected = XS2Radio_Settings::get_option( $args['name'] );

		foreach ( $args['options'] as $key => $title )
		{
			$checked = checked( $selected, $key, false );

			echo '<p><input name="' . $name . '" size="40" type="radio" value="' . $key .'" ' . $checked . ' />' . $title . '</p>';
		}
	}

	public function setting_post_types_fn( $args )
	{
		$selected = XS2Radio_Settings::get_option( $args['name'] );
		$post_types = XS2Radio_Settings::supported_post_types();

		foreach ( $post_types as $post_type )
		{
			$name = 'xs2radio[' . $args['name'] . '][' . $post_type->name . ']';
			$checked = in_array( $post_type->name, $selected ) ? 'checked' : '';

			echo '<p><input name="' . $name . '" size="40" type="checkbox" value="' . $post_type->name .'" ' . $checked . ' />' . $post_type->label . '</p>';
		}
	}
}