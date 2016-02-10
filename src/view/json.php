<?php
namespace puffin\view;

class json
{
	public $template = '';
	public $response_code = '';

	public function __construct()
	{
		$this->init();
	}

	public function init()
	{
		return $this;
	}

	public function template( $new_template = '' )
	{
		if( empty($new_template) )
		{
			return $this->template;
		}
		else
		{
			$this->template = $new_template;
		}
	}

	public function set_response_code( $response_code )
	{
		$this->$response_code = $response_code;
	}

	public function render()
	{
		if( !empty($this->response_code) )
		{
			http_response_code($this->response_code);
		}
		else
		{
			$this->set_response_code( http_response_code() );
		}

		header('Content-Type: application/json', $replace=true, $this->response_code);
		return json_encode($this->template);
	}
}
