<?php
namespace puffin;
use \puffin\controller as controller;

class route
{
	public $app = false;
	protected $controller_name;

	public function __construct( $controller_name, &$app )
	{
		$this->app = $app;
		$this->controller_name = $controller_name;
	}

	public function __call( $func, $args )
	{
		list($route, $action) = $args;

		switch( $func )
		{
			case 'any':
			case 'get':
			case 'head':
			case 'post':
			case 'put':
			case 'patch':
			case 'delete':
			case 'options':
				$handler = eval("return function(){
					\puffin\controller::init( '{$this->controller_name}' );
					\puffin\controller::dispatch( '$action', func_get_args() );
				};");
				$this->app->$func( $route , $handler );
				break;

			default:
				break;
		}

		return $this;
	}

}
