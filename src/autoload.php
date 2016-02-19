<?php
namespace puffin;

class autoload
{
	public static function init()
	{
		spl_autoload_register( function( $class ){
			$classname = str_replace('_', '/', $class);
			@include MODEL_PATH . "/$classname.php"; #shit was throwing warnings like mad, yo
		}, $throw_exceptions = false);

		spl_autoload_register( function( $class ){
			$classname = str_replace('_', '/', str_replace('_controller', '', $class));
			@include CONTROLLER_PATH . "/$classname.php";  #snitches get supressions
		}, $throw_exceptions = false);

	}
}
