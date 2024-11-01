<?php

class XS2Radio_Player
{
	private $settings = [
		'type'      => 'custom',
		'style'     => 'default',
		'template'  => '',
		'showImage' => true,
	];

	private $post_id      = false;
	private $post_title   = null;
	private $title        = null;
	private $description  = null;
	private $audio_url    = null;
	private $image        = null;
	private $seconds      = null;

	public function set_post_data( $post_id )
	{
		$post_settings = new XS2Radio_Post_Settings( $post_id );

		$this->post_id      = $post_id;
		$this->post_title   = get_the_title( $post_id );
		$this->title        = $this->post_title;
		$this->audio_url    = $post_settings->get_option( 'audio_url' );
		$this->image        = $post_settings->get_option( 'image' );
		$this->seconds      = $post_settings->get_option( 'seconds' );
	}

	public function has_item()
	{
		return (bool) $this->audio_url;
	}

	public function set_title( $title )
	{
		$this->title = $title;
	}

	public function set_description( $description )
	{
		$this->description = $description;
	}

	public function set_excerpt_as_description()
	{
		$this->description = get_the_excerpt($this->post_id);
	}

	public function set_setting( $name, $value )
	{
		$this->settings[ $name ] = $value;
	}

	public function get_html()
	{
		XS2Radio::load_frontend_styles();

		$this->filter_settings();

		if ( $this->description === null && $this->settings['type'] != 'article' ) {
			$this->set_excerpt_as_description();
		}

		$show_image   = $this->settings['showImage'] && $this->image;
		$column_class = !$show_image ? ' no-image' : '';
		$image   = $show_image ? $this->image : '';

		$total_time = '';
		if ($this->seconds) {
			$minutes = floor(($this->seconds / 60) % 60);
			$seconds = $this->seconds % 60;

			$total_time = "$minutes:$seconds";
		}

		return '<div><audio preload="none" src="' . $this->audio_url . '" class="xs2player" data-post-image="' . $image . '" data-template="' . $this->settings['template'] . '" data-style="' . $this->settings['style'] . '" data-post-id="' . $this->post_id . '" data-title="' . esc_attr($this->title) . '" data-description="' . esc_attr($this->description) . '" data-post-title="' . esc_attr($this->post_title) . '" data-total-time="' . $total_time . '" data-type="' . $this->settings['type'] . '" controls></audio></div>';

	}

	private function filter_settings()
	{
		foreach( $this->settings as $key => $value )
		{
			$this->settings[ $key ] = apply_filters( 'xs2radio_player_' . $key, $value, $this->post_id );
		}
	}
}