<?php

class XS2Radio_Api
{
	const url = 'https://api.xs2content.com/api/v1/';

	private $api_key;
	private $last_error = null;

	public function __construct( $api_key )
	{
		$this->api_key = $api_key;
	}

	public function is_connected()
	{
		$response = $this->request( 'feeds.json' );

		if ( wp_remote_retrieve_response_code( $response ) === 200 )
		{
			return true;
		}

		if ( wp_remote_retrieve_response_code( $response ) === 401 )
		{
			$this->last_error = __( 'API Token is invalid.', 'xs2radio' );
		}
		else if ( is_wp_error( $response ) && isset( $response->errors['http_request_failed'] ) )
		{
			$this->last_error = __( 'API could not be reached.', 'xs2radio' );
		}
		else
		{
			$this->last_error = __( 'API is currently unavailable.', 'xs2radio' );
		}

		return false;
	}

	public function get_feeds()
	{
		return $this->request_with_body( 'feeds.json' );
	}

	public function get_feeds_selection()
	{
		$feeds = $this->get_feeds();
		$data = [];

		foreach ($feeds as $feed)
		{
			$data[ $feed->slug ] = $feed->name;
		}

		return $data;
	}

	public function add_entry( $feed, $data )
	{
		return $this->request_with_body( 'feeds/' . $feed . '/entries.json', 'POST', $data, ini_get('max_execution_time') );
	}

	public function get_entry( $feed, $id )
	{
		return $this->request_with_body( 'feeds/' . $feed . '/entries/' . $id . '.json' );
	}

	public function update_entry( $feed, $id, $data = [] )
	{
		return $this->request_with_body( 'feeds/' . $feed . '/entries/' . $id . '.json', 'PATCH', $data );
	}

	public function delete_entry( $feed, $id )
	{
		return $this->request_with_body( 'feeds/' . $feed . '/entries/' . $id . '.json', 'DELETE' );
	}

	public function get_last_error()
	{
		return $this->last_error;
	}

	private function request_with_body( $url, $method = 'GET', $data = [], $timeout = 15 )
	{
		$request = $this->request( $url, $method, $data, $timeout );

		return json_decode( wp_remote_retrieve_body( $request ) );
	}

	private function request( $url, $method = 'GET', $data = [], $timeout = 15 )
	{
		$url  = self::url . $url;
		$args = [
			'method'    => $method,
			'timeout'   => $timeout,
			'headers'   => [
				'Authorization' => 'Bearer ' . $this->api_key,
				'Content-Type'  => 'application/json',
			]
		];

		if ( $data )
		{
			$args['body']        = json_encode( $data );
			$args['data_format'] = 'body';
		}
		
		$response = wp_remote_request( $url, $args );

		if ( is_wp_error( $response ) ) {
			error_log( $response->get_error_message() );
		}

		return $response;
	}
}