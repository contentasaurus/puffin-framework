<?php
namespace puffin\view;

class json
{
	public static function init()
	{
		return self;
	}

	public static function render( $json = '', $response_code = '' )
	{
		if( !empty($response_code) )
		{
			http_response_code($response_code);
		}
		else
		{
			$response_code = http_response_code();
		}

		header('Content-Type: application/json', $replace=true, $response_code);
		echo json_encode($json);
	}
}
