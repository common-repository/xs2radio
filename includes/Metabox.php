<?php

class XS2Radio_Metabox
{
	public function __construct()
	{
		add_action( 'add_meta_boxes', [ $this, 'add_meta_boxes' ] );
		add_action( 'save_post', [ $this, 'save_post' ] );
	}

	public function add_meta_boxes()
	{
		$post_types = XS2Radio_Settings::get_option( 'post-types' );

		foreach ( $post_types as $post_type )
		{
			add_meta_box(
				'xs2radio_box',
				__( 'XS2Radio', 'xs2radio' ),
				[ $this, 'meta_box' ],
				$post_type
			);
		}
	}

	public function meta_box( $post )
	{
		XS2Radio::load_scripts();
		wp_nonce_field( 'xs2radio_box', 'xs2radio_box_nonce' );

		$settings = new XS2Radio_Post_Settings( $post->ID );
		$transfer = $settings->get_option( 'transfer' );
		$which    = $settings->get_option( 'which' );
		$text     = $settings->get_option( 'text' );

		$transfer_options = [
			'inherit' => sprintf( __( 'Default (%s)', 'xs2radio' ), __( XS2Radio_Settings::get_option('default-add'), 'xs2radio' ) ),
			'on'      => __( 'On', 'xs2radio' ),
			'off'     => __( 'Off', 'xs2radio' ),
		];
		$content_options = [
			'inherit' => __( 'Content', 'xs2radio' ),
			'custom'  => __( 'Custom text-to-speech', 'xs2radio' ),
		];

		echo '<p><span class="xs2radio-label">' . __( 'Transfer to XS2Radio', 'xs2radio' ) . ':</span>';
		foreach ( $transfer_options as $value => $label ) {
			$checked = checked( $value, $transfer, false );
			echo '<input id="xs2radio_transfer-' . $value . '" name="xs2radio_transfer" size="40" type="radio" value="' . $value . '"' . $checked . '><label for="xs2radio_transfer-' . $value . '">' . $label . '</label> &nbsp; ';
		}
		echo '</p>';

		echo '<p><span class="xs2radio-label">' . __( 'Which content', 'xs2radio' ) . ':</span>';
		foreach ( $content_options as $value => $label ) {
			$checked = checked( $value, $which, false );
			echo '<input id="xs2radio_which_content-' . $value . '" name="xs2radio_which_content" size="40" type="radio" value="' . $value . '"' . $checked . '><label for="xs2radio_which_content-' . $value . '">' . $label . '</label> &nbsp; ';
		}
		echo '</p>';

		echo '<p><label id="xs2radio_text" class="xs2radio-label">' . __( 'Custom text-to-speech', 'xs2radio' ) . ':</label>';
		echo '<textarea id="xs2radio_text" name="xs2radio_text" rows="6">' . $text . '</textarea>';
		echo '</p>';


		if ( $settings->get_option( 'entry_id' ) ) {
			echo '<hr />';
			echo '<p><span class="xs2radio-label">' . __( 'Entry ID', 'xs2radio' ) . ':</span>';
			echo $settings->get_option( 'entry_id' );
			echo '</p>';
		}

		if ( $settings->get_option( 'audio_url' ) ) {
			echo '<p><span class="xs2radio-label">' . __( 'Audio', 'xs2radio' ) . ':</span>';
			echo '<audio controls src="' . $settings->get_option( 'audio_url' ) . '" preload="none">' . $settings->get_option( 'audio_url' ) . '</audio>';

			echo '</p>';
		}

		if (XS2Radio_Settings::get_option( 'analytics-enabled' ) == 'on') {
			echo '<hr />';
			echo '<h4>' . __( 'Analytics', 'xs2radio' ) . '</h4>';

			echo '<p><span class="xs2radio-label">' . __( 'Started listening', 'xs2radio' ) . ':</span>';
			echo $settings->get_option( 'stats_started' );
			echo '</p>';

			echo '<p><span class="xs2radio-label">' . __( 'Hallway with listening', 'xs2radio' ) . ':</span>';
			echo $settings->get_option( 'stats_halfway' );
			echo '</p>';

			echo '<p><span class="xs2radio-label">' . __( 'Finished listening', 'xs2radio' ) . ':</span>';
			echo $settings->get_option( 'stats_finished' );
			echo '</p>';
		}
	}

	public function save_post( $post_id )
	{
		// Check if our nonce is set.
		if ( ! isset( $_POST['xs2radio_box_nonce'] ) ) {
			return $post_id;
		}

		$nonce = $_POST['xs2radio_box_nonce'];

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $nonce, 'xs2radio_box' ) ) {
			return $post_id;
		}

		// Check if this is an autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		// Check the user's permissions.
		if ( 'page' == $_POST['post_type'] ) {
			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return $post_id;
			}
		} else {
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return $post_id;
			}
		}

		$settings = new XS2Radio_Post_Settings( $post_id );
		$old_transfer = $settings->get_option( 'transfer' );
		$old_which    = $settings->get_option( 'which' );
		$old_text     = $settings->get_option( 'text' );

		$transfer = sanitize_text_field( $_POST['xs2radio_transfer'] );
		$which = sanitize_text_field( $_POST['xs2radio_which_content'] );
		$text = sanitize_text_field( $_POST['xs2radio_text'] );

		$settings->update_option( 'text', $text );

		if ( in_array( $transfer, [ 'inherit', 'on', 'off' ] ) ) {
			$settings->update_option( 'transfer', $transfer );
		}
		if ( in_array( $which, [ 'inherit', 'custom' ] ) ) {
			$settings->update_option( 'which', $which );
		}

		$forced = false;

		if ( 
			( $old_transfer != $transfer && $transfer == 'on' ) ||
			$old_which != $which ||
			$old_text != $text
		)
		{
			$forced = true;
		}

		$settings->transfer( $forced );
	}
}