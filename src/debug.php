<?php
namespace puffin;

class debug
{
	public static function printr( $input )
	{
		return '<pre>' . print_r( $input, $render_as_string = true ) . '</pre>';
	}

	public static function clog( $input )
	{
		return '<script> console.log("' . print_r( $input, $render_as_string = true ) . '");</script>';
	}

}
