<div class="wrap">
	<h2><?php _e( 'XS2Content', 'xs2radio' ); ?></h2>
	
	<div class="settings-description">
		<?php _e( 'Text-to-speech platform for publishers and company newsrooms.', 'xs2radio' ); ?>
	</div>

	<form action="options.php" method="post">
		<?php

		settings_fields( 'xs2radio-group' );

		do_settings_sections( 'xs2radio' );

		echo '<a href="' . admin_url('options-general.php?page=xs2content&action=analytics-download') . '" class="button button-secondary button-download">' . __( 'Download analytics as CSV', 'xs2radio' ) . '</a>';


		if ( XS2Radio_Settings::get_option( 'api-key-valid' ) === false )
		{
			if ( XS2Radio_Settings::get_option( 'api-key' ) )
			{
				echo '<div class="notice notice-alert inline"><p><strong>' . __( 'The specified API key is invalid.', 'xs2radio' ) . '</strong></p></div>';
			}
			else
			{
				echo '<div class="notice notice-notice inline"><p><strong>' . __( 'You first need to enter the API Key before continue further.', 'xs2radio' ) . '</strong></p></div>';
			}
		}
		?>
	
		<p class="submit">
			<?php submit_button( __( 'Save Changes', 'xs2radio' ), 'primary', 'submit', false ); ?>
		</p>
	</form>
</div>