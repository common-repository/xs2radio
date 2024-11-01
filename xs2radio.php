<?php
/**
 * @package XS2Radio
 * @version 1.0.2
 */

/*
Plugin Name: XS2Content
Plugin URI: https://secure.xs2content.com
Description: Text-to-speech platform for publishers and company newsrooms.
Author: XS2Content
Version: 1.5.4

Tested up to: 6.2
Text Domain: xs2radio
*/

if ( ! defined( 'ABSPATH' ) )
{
	exit;
}

include 'includes/Api.php';
include 'includes/Player.php';
include 'includes/Playlist.php';
include 'includes/Post-settings.php';
include 'includes/Settings.php';

final class XS2Radio
{
	const version = '1.5.4';

	private static $backend_loaded = false;
	private static $frontend_loaded = false;

	public function __construct()
	{
		add_action( 'plugins_loaded', [ $this, 'load' ] );
		add_action( 'widgets_init', [ $this, 'load_widgets' ] );
		add_action( 'init', [ $this, 'register_frontend_styles' ] );
		add_action( 'current_screen', [ $this, 'register_admin_scripts' ] );

		if ( is_admin() ) {
			add_action( 'plugins_loaded', array( $this, 'load_admin' ) );
		}
	}

	public function load()
	{
		include 'includes/Frontend.php';

		new XS2Radio_Frontend();
	}

	public function load_admin()
	{
		include 'includes/Analytics.php';
		include 'includes/Metabox.php';
		include 'includes/Settings-page.php';

		new XS2Radio_Metabox();
		new XS2Radio_Settings_Page();
	}

	public function load_widgets()
	{
		include 'widgets/player.php';
		include 'widgets/playlist.php';

		register_widget( 'XS2Radio_Player_Widget' );
		register_widget( 'XS2Radio_Playlist_Widget' );
	}

	public function register_frontend_styles()
	{
		wp_register_style( 'xs2content_frontend', plugins_url( 'assets/frontend.css', __FILE__ ), [], XS2Radio::version );

		wp_register_script( 'wavesurfer', plugins_url( 'assets/wavesurfer.min.js', __FILE__ ), [], '6.6.0'  );
		wp_register_script( 'xs2content_frontend', plugins_url( 'assets/frontend.js', __FILE__ ), ['wavesurfer'], XS2Radio::version );
		wp_register_script( 'xs2content_frontend-wavesurfer', plugins_url( 'assets/wavesurfer.min.js', __FILE__ ), ['xs2content_frontend'], XS2Radio::version );
	}

	public function register_admin_scripts()
	{
		wp_register_style( 'xs2content_admin', plugins_url( 'assets/admin.css', __FILE__ ), ['wp-color-picker'], XS2Radio::version );

		wp_register_script( 'xs2content_admin', plugins_url( 'assets/admin.js', __FILE__ ), ['wp-color-picker'], XS2Radio::version );
	}

	public static function load_frontend_styles()
	{
		if (self::$frontend_loaded === false)
		{
			self::$frontend_loaded = true;

			wp_enqueue_style( 'xs2content_frontend' );
			wp_enqueue_script( 'xs2content_frontend' );

			$custom_css = '
				.xs2player-style-default.xs2player-player {
					background: ' . XS2Radio_Settings::get_option('color-background') . ';
					color: ' . XS2Radio_Settings::get_option('color-title') . ';
				}
				.xs2player-style-default .xs2player-player-title {
					color: ' . XS2Radio_Settings::get_option('color-title') . ';
				}
				.xs2player-style-default button {
					color: ' . XS2Radio_Settings::get_option('color-buttons') . ' !important;
				}
				.xs2player-style-default button.xs2player-play {
					border-color: ' . XS2Radio_Settings::get_option('color-buttons') . ';
				}
				.xs2player-currenttime {
					background: ' . XS2Radio_Settings::get_option('color-visualisation-currenttime') . ';
				}
				.xs2player-playlist .xs2player-style-default + ul,
				.xs2player-playlist .xs2player-style-default + audio + ul {
					background: ' . XS2Radio_Settings::get_option('color-playlist-background') . ';
				}

				.xs2player-style-default .xs2player-waveform > * {
					background: ' . XS2Radio_Settings::get_option('color-visualisation-background') . ';
				}';
			wp_add_inline_style('xs2content_frontend', $custom_css);

			$settings = [
				'template'         => XS2Radio_Settings::get_option('player-template'),
				'barWidth'         => 2,
				'barHeight'        => 1,
				'barGap'           => 2,
				'cursorWidth'      => 0,
				'height'           => 90,
				'skipLength'       => 15,
				'progressColor'    => XS2Radio_Settings::get_option('color-visualisation-progress'),
				'waveColor'        => XS2Radio_Settings::get_option('color-visualisation-wave'),
				'restUrl'          => rest_url('xs2content'),
				'restNonce'        => wp_create_nonce( 'wp_rest' ),
				'analyticsEnabled' => XS2Radio_Settings::get_option('analytics-enabled'),
			];
			
			wp_localize_script( 'xs2content_frontend', 'xs2content', $settings );
		}
	}

	public static function load_scripts()
	{
		if (self::$backend_loaded === false)
		{
			self::$backend_loaded = true;

			wp_enqueue_style( 'xs2content_admin' );
			wp_enqueue_script( 'xs2content_admin' );
		}
	}
}

$GLOBALS['xs2radio'] = new XS2Radio;
