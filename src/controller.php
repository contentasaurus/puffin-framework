<?php
namespace puffin;
use \puffin\controller\param as param;

class controller
{
	public static $default_controller = 'index';
	public static $default_action = 'index';

	public static $controller_instance;
	public static $controller;
	public static $action;

	public static function init( $controller )
	{
		if( empty($controller) )
		{
			$controller = self::$default_controller;
		}

		self::$controller = $controller;
		$controller_name = $controller.'_controller';
		self::$controller_instance = new $controller_name;

		$_PUT = [];
		$_DELETE = [];

		self::$controller_instance->get = new param( $_GET );
		self::$controller_instance->post = new param( $_POST );

		if( strtoupper($_SERVER['REQUEST_METHOD']) == 'PUT' )
		{
			parse_str(file_get_contents('php://input'), $_PUT);
		}

		if( strtoupper($_SERVER['REQUEST_METHOD']) == 'DELETE' )
		{
			parse_str( file_get_contents('php://input'), $_DELETE );
		}

		self::$controller_instance->put = new param( $_PUT );
		self::$controller_instance->delete = new param( $_DELETE );

		self::$controller_instance->input = file_get_contents('php://input');

		self::_run_init();
		plugin::run('__init');
	}

	public static function dispatch( $action, $args )
	{
		self::$action = $action;

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

	protected static function _run_init()
	{
		return self::$controller_instance->__init();
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
