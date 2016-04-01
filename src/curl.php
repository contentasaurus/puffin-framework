<?php
namespace puffin;

# Examples
#
# $curl = new curl();
# $response = $curl->get( 'http://example.com/', ['param' => 1] );
# $response = $curl->post( 'http://example.com/', ['param' => 1] )
# $response = $curl->put( 'http://example.com/', ['param' => 1] )
# $response = $curl->delete( 'http://example.com/', ['param' => 1] )

class curl
{
	public $responseText = '';
	public $start_time = 0;
	public $end_time = 0;
	public $run_time = 0;

	protected $connect_timeout = 2;
	protected $method = 'get';
	protected $params = '';
	protected $timeout = 2;
	protected $ssl_verify = true;
	protected $get_response = true;
	protected $url = '';
	protected $headers = [];


	public function __construct( $options = [] )
	{
		foreach( $options as $key => $value )
		{
			$this->$key = $value;
		}
	}

	public function set_headers( $headers )
	{
		foreach( $headers as $header )
		{
			$this->headers []= $header;
		}
	}

	public function __call( $function, $arguments )
	{
		list($url, $params) = $arguments;

		$this->start_time = microtime( $get_as_float = true );

		$this->curl_handle = curl_init();

		curl_setopt_array( $this->curl_handle, [
			CURLOPT_TIMEOUT => $this->timeout,
			CURLOPT_CONNECTTIMEOUT => $this->connect_timeout,
			CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'],
			CURLOPT_RETURNTRANSFER => $this->get_response,
			CURLOPT_SSL_VERIFYPEER => $this->ssl_verify,
			CURLOPT_SSL_VERIFYHOST => 2,
			CURLOPT_HEADER => 0,
			CURLOPT_FRESH_CONNECT => 1
		]);

		$params = ( is_array($params) ) ? http_build_query($params) : $params;

		switch( $function )
		{
			case 'get':
				curl_setopt_array( $this->curl_handle, [
					CURLOPT_URL => "$url?$params",
					CURLOPT_HTTPGET => 1,
					CURLOPT_HTTPHEADER => $this->headers
				]);
				break;

			case 'post':
			case 'put':
			case 'delete':
				$this->set_headers(['Content-Length: ' . strlen($params)]);

				curl_setopt_array( $this->curl_handle, [
					CURLOPT_URL => $url,
					CURLOPT_CUSTOMREQUEST => strtoupper($function),
					CURLOPT_RETURNTRANSFER => 1,
					CURLOPT_HTTPHEADER => $this->headers,
					CURLOPT_POSTFIELDS => $params
				]);
				break;

			default:
				return false;
				break;
		}

		$this->responseText = curl_exec( $this->curl_handle );

		if( !$this->status = curl_errno( $this->curl_handle ) )
		{
			$this->status = true;
		}

		curl_close( $this->curl_handle );

		$this->end_time = microtime( $get_as_float = true );
		$this->run_time = $this->end_time - $this->start_time;

		return $this->responseText;

	}
}
