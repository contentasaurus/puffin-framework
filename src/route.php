<?php
namespace puffin;
use \puffin\controller as controller;

class route
{
	public $app = false;
	protected $controller_name;
	protected $action;

	public function __construct( $controller_name, &$app )
	{
		$this->app = $app;
		$this->controller_name = $controller_name;
	}

	public function __call( $func, $args )
	{
		list($route, $this->action) = $args;

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
				$this->app->router()->$func( $route , [ $this, 'add_route' ] );
				break;

			default:
				break;
		}

		return $this;
	}

	public function add_route()
	{
		controller::init( $this->controller_name );
		controller::dispatch( $this->action, func_get_args() );
	}

}
