<?php
namespace puffin\controller;

class param
{
	public $original_array;
	public $sanitized_array;

	public function __construct( $input_array )
	{
		$this->set( $input_array );
	}

	public function set( $input_array )
	{
		$this->original_array = $input_array;
		$this->sanitized_array = $this->sanitize( $input_array );
	}

	public function params( $raw = false )
	{
		if( $raw )
		{
			return $this->original_array;
		}
		return $this->sanitized_array;
	}

	public function param( $param, $default_value = false )
	{
		return $this->_get_param( $this->sanitized_array, $param, $default_value);
	}

	public function original_param( $param, $default_value )
	{
		return $this->_get_param( $this->original_array, $param, $default_value);
	}

	protected function _get_param( $array, $param, $default_value )
	{
		if( isset( $array[$param] ) )
		{
			return $array[$param];
		}
		return $default_value;
	}

	public function sanitize( $input )
	{
		$output = '';
		if( is_array($input) )
		{
			foreach( $input as $var => $val )
			{
				$output[$var] = $this->sanitize($val);
			}
		}
		else
		{
			if( get_magic_quotes_gpc() )
			{
				$input = stripslashes($input);
			}
			$output  = $this->cleanInput($input);
		}
		return $output;
	}

	protected function cleanInput($input)
	{
		$output = '';
		$search = array
		(
			'/<script[^>]*?>.*?<\/script>/si',   // Strip out javascript
			'/<[\/\!]*?[^<>]*?>/si',            // Strip out HTML tags
			'/<style[^>]*?>.*?<\/style>/siU',    // Strip style tags properly
			'/<![\s\S]*?--[ \t\n\r]*>/'         // Strip multi-line comments
		);

		$output = preg_replace($search, '', $input);
		return $output;
	}

}
