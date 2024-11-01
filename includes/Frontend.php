<?php

if ( ! defined( 'ABSPATH' ) )
{
	exit;
}

class XS2Radio_Frontend
{
	public function __construct()
	{
		add_action( 'init', [ $this, 'post_selector_block_register_block' ] );

		add_filter( 'the_content', [ $this, 'the_content' ] );

		add_shortcode( 'xs2content-player', [ $this, 'shortcode_player' ] );
		add_shortcode( 'xs2content-playlist', [ $this, 'shortcode_playlist' ] );

		add_action( 'rest_api_init', array( $this, 'rest_api_init') ); 
	}

	function post_selector_block_register_block() {
		if (!function_exists('register_block_type')) {
			return;
		}

		wp_register_script(
			'xs2content_block',
			plugins_url( 'assets/block.js', dirname( __FILE__ ) ),
			array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-components', 'wp-editor', 'wp-api', 'xs2content_frontend' ),
			filemtime( plugin_dir_path( dirname( __FILE__ ) ) . 'assets/block.js' )
		);

		register_block_type( 'xs2content/player', [
			'editor_script' => 'xs2content_block',
			'editor_style'  => 'xs2content_frontend',
			'render_callback' => [ $this, 'shortcode_player' ],
			'attributes'      => [
				'postid' => [
					'type' => 'number'
				],
				'showImage' => [
					'type' => 'boolean'
				],
			],
		] );

		register_block_type( 'xs2content/playlist', [
			'editor_script' => 'xs2content_block',
			'editor_style'  => 'xs2content_frontend',
			'render_callback' => [ $this, 'shortcode_playlist' ],
			'attributes'      => [
				'amount' => [
					'type' => 'number'
				],
				'category' => [
					'type' => 'number'
				],
				'showImage' => [
					'type' => 'boolean'
				],
			],
		] );
	}

	public function the_content( $content )
	{
		if( is_singular() )
		{
			$location = XS2Radio_Settings::get_option('adding-to-post');

			if ( $location != 'no' )
			{
				$player_content = XS2Radio_Settings::get_option('player-content');

				$player = new XS2Radio_Player();
				$player->set_post_data( get_the_ID() );
				$player->set_setting( 'type', 'article' );

				if ($player_content === 'custom') {
					$player->set_title( XS2Radio_Settings::get_option('post-player-title') );
				}
				elseif ($player_content === 'title_excerpt') {
					// Remove to be able to use the excerpt inside the content
					remove_filter( 'the_content', [ $this, 'the_content' ] );
					
					$player->set_excerpt_as_description();

					// Readding it for all other possible items
					add_filter( 'the_content', [ $this, 'the_content' ] );
				}

				if( $player->has_item() )
				{
					$player_html = '<p>' . $player->get_html() . '</p>';

					if ( $location = 'before_post' )
					{
						$content = $player_html . $content;
					}
					else if ( $location = 'after_post' )
					{
						$content .= $player_html();
					}
				}
			}
		}

		return $content;
	}

	public function shortcode_player( $atts )
	{
		$atts = shortcode_atts( array(
			'postid'    => 0,
			'showImage' => true,
		), $atts, 'xs2radio-player' );
		$player = new XS2Radio_Player();
		$player->set_post_data( $atts['postid'] );
		$player->set_setting( 'showImage', wp_validate_boolean( $atts['showImage'] ) );
		$player->set_setting( 'type', 'shortcode' );

		if ( $player->has_item() )
		{
			return $player->get_html();
		}
	}

	public function shortcode_playlist( $atts )
	{
		$atts = shortcode_atts( array(
			'amount'    => 5,
			'category'  => 0,
			'showImage' => true,
		), $atts, 'xs2radio-playlist' );

		$playlist = new XS2Radio_Playlist();
		$playlist->load_from_query( $atts['amount'], $atts['category'] );
		$playlist->set_setting( 'showImage', wp_validate_boolean( $atts['showImage'] ) );
		$playlist->set_setting( 'type', 'shortcode' );

		return $playlist->get_html();
	}

	public function rest_api_init() {
		register_rest_route( 'xs2content', '/stats', array(
			'methods'  => 'POST',
			'callback' => array( $this, 'rest_api_save_stats'),
			'permission_callback' => function( WP_REST_Request $request ) { 
				return true;
			}
		) );
	}

	public function rest_api_save_stats( WP_REST_Request $request ) {
		$postId = $request->get_param( 'postId' );
		$type   = $request->get_param( 'type' );

		if (!in_array($type, ['started', 'halfway', 'finished'])) {
			return new WP_REST_Response([
				'error' => 'Invalid type'
			], 400);
		}

		$post_settings = new XS2Radio_Post_Settings( $postId );

		$count = $post_settings->get_option('stats_' . $type);
		$count++;
		$post_settings->update_option('stats_' . $type, $count);
	}
}