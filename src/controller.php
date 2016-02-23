<?php
namespace puffin;
use \puffin\controller\param as param;

class controller
{
	public static $default_controller = 'index';
	public static $default_action = 'index';

	public static $controller_instance;
	public static $controller;

	public static function init( $controller )
	{
		if( empty($controller) )
		{
			$controller = self::$default_controller;
		}

		self::$controller = $controller;
		$controller_name = $controller.'_controller';
		self::$controller_instance = new $controller_name;

		self::$controller_instance->get = new param( $_GET );
		self::$controller_instance->post = new param( $_POST );
		self::$controller_instance->input = file_get_contents('php://input');

		plugin::run('__init');
	}

	public static function dispatch( $action, $args )
	{
		if( empty($action) ) { return ''; }

		self::_run_before_call();
		plugin::run('__before_call');

		self::_set_template( self::$controller, $action );

		$con = self::$controller_instance;
		$results = call_user_func_array( [$con, $action] , $args);

		self::_run_after_call();
		plugin::run('__after_call');

		return $results;
	}

	protected static function _set_template( $controller, $action )
	{
		view::template("$controller/$action");
		return true;
	}

	protected static function _run_before_call()
	{
		return self::$controller_instance->__before_call();
	}

	protected static function _run_after_call()
	{
		return self::$controller_instance->__after_call();
	}

}
