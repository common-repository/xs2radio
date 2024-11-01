<?php

class XS2Radio_Playlist
{
	private $settings = [
		'type'      => 'custom',
		'style'     => 'default',
		'template'  => '',
		'showImage' => true,
	];

	private $query;

	private $tracks = [];

	public function has_items()
	{
		return (bool) $this->tracks;
	}

	public function set_setting( $name, $value )
	{
		$this->settings[ $name ] = $value;
	}

	public function load_from_query( $amount = 5, $category = 0 )
	{
		$args = [
			'post_type' => XS2Radio_Settings::get_option( 'post-types' ),
			'posts_per_page' => $amount,
			'meta_query' => [
				[
					'key'     => 'xs2radio_audio_url',
					'compare' => 'EXISTS',
				],
			]
		];

		if ( $category > 0 )
		{
			$args['cat'] = $category;
		}

		$query = new WP_Query( $args );

		foreach ( $query->posts as $post ) {
			$settings = new XS2Radio_Post_Settings( $post->ID );
			$track    = array(
				'post_id'   => $post->ID,
				'audio_url' => $settings->get_option('audio_url'),
				'title'     => $post->post_title,
				'image'     => $settings->get_option('image'),
				'seconds'   => $settings->get_option('seconds'),
			);

			$this->tracks[] = $track;
		}
	}

	public function get_html()
	{
		if ( is_feed() )
		{
			$output = "\n";
			foreach ( $this->tracks as $track ) {
				$output .= $track['audio_url'];
			}
			return $output;
		}

		XS2Radio::load_frontend_styles();

		$this->filter_settings();

		$show_image    = $this->settings['showImage'] && $this->tracks[0]['image'];
		$column_class  = !$show_image ? ' no-image' : '';
		$image    = $show_image ? $this->tracks[0]['image'] : '';
		$playlist_html = '';

		$total_time = '';
		if ($this->tracks[0]['seconds']) {
			$minutes = floor(($this->tracks[0]['seconds'] / 60) % 60);
			$seconds = $this->tracks[0]['seconds'] % 60;

			$total_time = "$minutes:$seconds";
		}

		foreach ( $this->tracks as $track )
		{
			$playlist_html .= '<li data-url="' . $track['audio_url'] . '" data-image="' . $track['image'] . '" data-post-id="' . $track['post_id'] . '">' . $track['title'] . '</li>';
		}

		return '<div class="xs2player-playlist">
					<audio preload="none" src="' . $this->tracks[0]['audio_url'] . '" class="xs2player" data-post-title="' . esc_attr($this->tracks[0]['title']) . '" data-post-image="' . $image . '" data-template="' . $this->settings['template'] . '" data-style="' . $this->settings['style'] . '" data-post-id="' . $this->tracks[0]['post_id']  . '" data-total-time="' . $total_time . '" data-type="' . $this->settings['type'] . '"  controls></audio>

					<ul>
						' . $playlist_html . '
					</ul>
			    </div>';
	}

	private function filter_settings()
	{
		foreach( $this->settings as $key => $value )
		{
			$this->settings[ $key ] = apply_filters( 'xs2radio_playlist_' . $key, $value );
		}
	}
}