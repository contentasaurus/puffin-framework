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
	protected $onComplete = '';
	protected $onCreate = '';
	protected $onFailure = '';
	protected $onSuccess = '';
	protected $params = '';
	protected $timeout = 2;
	protected $ssl_verify = true;
	protected $get_response = true;
	protected $url = '';


	public function __construct( $options = [] )
	{
		foreach( $options as $key => $value )
		{
			$this->$key = $value;
		}
	}

	public function __call( $function, $arguments )
	{
		list($url, $params) = $arguments;

		$this->start_time = microtime( $get_as_float = true );

		$this->curl_handle = curl_init();

		curl_setopt( $this->curl_handle, CURLOPT_TIMEOUT, $this->timeout );
		curl_setopt( $this->curl_handle, CURLOPT_CONNECTTIMEOUT, $this->connect_timeout );
		curl_setopt( $this->curl_handle, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT'] );
		curl_setopt( $this->curl_handle, CURLOPT_RETURNTRANSFER, $this->get_response );
		curl_setopt( $this->curl_handle, CURLOPT_SSL_VERIFYPEER, $this->ssl_verify );
		curl_setopt( $this->curl_handle, CURLOPT_SSL_VERIFYHOST, $this->ssl_verify );
		curl_setopt( $this->curl_handle, CURLOPT_HEADER, 0 );
		curl_setopt( $this->curl_handle, CURLOPT_FRESH_CONNECT, 1 );

		$params = ( is_array($params) ) ? http_build_query($params) : $params;

		switch( $function )
		{
			case 'get':
				curl_setopt( $this->curl_handle, CURLOPT_URL, "$url?$params" );
				curl_setopt( $this->curl_handle, CURLOPT_HTTPGET, 1 );
				break;

			case 'post':
				curl_setopt( $this->curl_handle, CURLOPT_URL, $url );
				curl_setopt( $this->curl_handle, CURLOPT_POST, 1 );
				curl_setopt( $this->curl_handle, CURLOPT_POSTFIELDS, $params );
				break;

			case 'put':
				curl_setopt( $this->curl_handle, CURLOPT_URL, $url );
				curl_setopt( $this->curl_handle, CURLOPT_CUSTOMREQUEST, 'PUT' );
				curl_setopt( $this->curl_handle, CURLOPT_RETURNTRANSFER, 1 );
				curl_setopt( $this->curl_handle, CURLOPT_HTTPHEADER, array('Content-Length: ' . strlen($params)) );
				curl_setopt( $this->curl_handle, CURLOPT_POSTFIELDS, $params );
				break;

			case 'delete':
				curl_setopt( $this->curl_handle, CURLOPT_URL, $url );
				curl_setopt( $this->curl_handle, CURLOPT_CUSTOMREQUEST, 'DELETE' );
				curl_setopt( $this->curl_handle, CURLOPT_RETURNTRANSFER, 1 );
				curl_setopt( $this->curl_handle, CURLOPT_HTTPHEADER, array('Content-Length: ' . strlen($params)) );
				curl_setopt( $this->curl_handle, CURLOPT_POSTFIELDS, $params );
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
