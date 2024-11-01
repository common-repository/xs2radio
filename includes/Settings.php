<?php

class XS2Radio_Settings
{
	private static $defaults = [
		'api-key'           => '',
		'api-key-valid'     => false,
		'feed'              => null,
		'post-types'        => ['post'],
		'default-add'       => 'On',
		'adding-to-post'    => 'no',
		'player-template'   => 'wavesurfer',

		'player-content'    => 'custom',
		'post-player-title' => '',

		'color-title'                     => '#000',
		'color-buttons'                   => '#CA2C23',
		'color-background'                => '#EEE',
		'color-playlist-background'       => '#666',
		'color-visualisation-background'  => '#FFF',
		'color-visualisation-progress'    => '#555',
		'color-visualisation-wave'        => '#999',
		'color-visualisation-currenttime' => '#CA2C23',

		'analytics-enabled' => 'on',
	];

	public static function get_options()
	{
		self::$defaults['post-player-title'] = __( 'Listen to the audio version of this article below', 'xs2radio' );
		return array_merge( self::$defaults, array_filter( get_option('xs2radio', []) ) );
	}

	public static function get_option( $name )
	{
		return self::get_options()[ $name ];
	}

	public static function get_default_option( $name )
	{
		return self::$defaults[ $name ];
	}

	public static function get_api()
	{
		return new XS2Radio_Api( self::get_option('api-key') );
	}

	public static function supported_post_types()
	{
		$post_types = array_filter(
			get_post_types( [ 'public' => true ], 'objects' ),
			function ( $post_type ) {
				return post_type_supports( $post_type->name, 'editor' );
			}
		);

		return $post_types;
	}
}
