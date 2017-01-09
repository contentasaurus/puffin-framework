<?php
namespace puffin\view;

class json
{
	public $template = '';
	public $response_code = '';
	public $params = [];

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

	public function add_param( $key, $value )
	{
		$this->params[$key] = $value;
	}

	public function add_params( $params )
	{
		foreach( $params as $k => $v )
		{
			$this->add_param( $k, $v );
		}
	}

	public function set_response_code( $response_code )
	{
		$this->response_code = $response_code;
	}

	public function get_response_code(){
	    return $this->response_code;
	}


	public function render()
	{
		if( !empty($this->get_response_code()) )
		{
			http_response_code($this->get_response_code());
		}
		else
		{
			$this->set_response_code( http_response_code() );
		}

		header('Content-Type: application/json');
		echo json_encode($this->params);
		exit;
	}
}
