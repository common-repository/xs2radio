<?php

class XS2Radio_Post_Settings
{
	private $post_id;

	private $defaults = [
		'transfer'       => 'inherit',
		'which'          => 'inherit',
		'text'           => '',
		'image'          => '',
		'last_revision'  => 0,
		'entry_id'       => 0,
		'audio_url'      => null,
		'seconds'        => null,


		'stats_started'  => 0,
		'stats_halfway'  => 0,
		'stats_finished' => 0,
	];

	public function __construct( $post_id )
	{
		$this->post_id = $post_id;
	}

	public function get_option( $name )
	{
		$value = get_post_meta( $this->post_id, 'xs2radio_' . $name, true );
		
		if ( !$value ) {
			$value = $this->defaults[ $name ];
		}

		return $value;
	}

	public function delete_option( $name )
	{
		return delete_post_meta( $this->post_id, 'xs2radio_' . $name );
	}

	public function update_option( $name, $value )
	{
		return update_post_meta( $this->post_id, 'xs2radio_' . $name, $value );
	}

	public function transfer( $force = false )
	{
		if (
			$this->get_option('transfer') === 'off' ||
			( $this->get_option('transfer') === 'inherit' && XS2Radio_Settings::get_option('default-add') === 'Off' )
		)
		{
			return false;
		}


		$current_revision = $this->get_option( 'last_revision' );
		$last_revision    = current( wp_get_post_revisions( $this->post_id ) )->ID;
		$force            = true;

		if ( $current_revision < $last_revision || $force ) {
			$api  = XS2Radio_Settings::get_api();
			$post = get_post( $this->post_id );

			if ( $this->get_option( 'which' ) == 'custom' )
			{
				$content = $this->get_option( 'text' );
			}
			else
			{
				$content = $post->post_content;
			}

			if (!$content) {
				$this->delete_option( 'audio_url' );
				return;
			}

			$data = [
				'entry' => [
					'title'     => $post->post_title,
					'published' => $post->post_date,
					'content'   => $content,
					'url'       => get_the_permalink( $post ),
					'author'    => get_the_author_meta( 'display_name', $post->post_author ),
					'visible'   => true,
					'image_url' => $this->get_featured_image( $post ),
				],
			];

			if ( $this->get_option('entry_id') > 0 )
			{
				$data = $api->update_entry( XS2Radio_Settings::get_option( 'feed' ), $this->get_option( 'entry_id' ), $data );
			}
			else
			{
				$data = $api->add_entry( XS2Radio_Settings::get_option( 'feed' ), $data );
			}


			$this->update_option( 'last_revision', $last_revision );
			$this->update_option( 'entry_id', $data->id );
			$this->update_option( 'audio_url', $data->audio_url );
			$this->update_option( 'image', $data->image_url );
			$this->update_option( 'seconds', $data->seconds );

			return true;
		}

		return false;
	}

	private function get_featured_image( $post )
	{
		$image_size = apply_filters( 'xs2radio_image_size', 'full' );
		$image = get_the_post_thumbnail_url( $post, $image_size );
		
		if ( $image )
		{
			return $image;
		}

		preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*.+class=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $images);

		if ( isset( $images[2] ) )
		{
			$image = wp_get_attachment_image_src( str_replace( 'wp-image-', '', $images[2][0] ), $image_size )[0];

			if ( $image )
			{
				return $image;
			}
		}

		if ( isset( $images[1] ) )
		{
			return $images[1][0];
		}

		return null;
	}
}