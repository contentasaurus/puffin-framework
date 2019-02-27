<?php
namespace puffin;
use puffin\route as route;
use puffin\transformer as transformer;
use \Phroute\Phroute\RouteCollector as RouteCollector;
use \Phroute\Phroute\Dispatcher as Dispatcher;

class app
{
	protected $routes = [];

	public $router = false;
	public $presenter;
	public $template;
	public $presenter_template;

	public function __construct()
	{
		transformer::_load();
	}

	public function router()
	{
		if( !$this->router )
		{
			$this->init_router();
		}
		return $this->router;
	}

	protected function init_router()
	{
		return $this->router = new RouteCollector();
	}

	public function controller( $controller_name )
	{
		return new route( $controller_name, $this->router() );
	}

	public function route()
	{
		try
		{
			$dispatcher = new Dispatcher( $this->router()->getData() );
			$results = $dispatcher->dispatch($_SERVER['REQUEST_METHOD'], parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
		}
		catch( \Exception $e )
		{
			switch( get_class($e) )
			{
				case 'Phroute\Phroute\Exception\HttpRouteNotFoundException':
					throw new \Exception($e->getMessage(), 404);
					http_response_code(404);
					break;
				case 'Phroute\Phroute\Exception\HttpMethodNotAllowedException':
					throw new \Exception($e->getMessage(), 403);
					http_response_code(403);
					break;
				default:
					throw new \Exception($e->getMessage(), 400);
					http_response_code(400);
					break;
			}
		}

	}

	public function render()
	{
		return view::render();
	}


}
