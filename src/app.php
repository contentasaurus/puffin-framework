<?php
namespace puffin;
use \Phroute\Phroute\RouteCollector as RouteCollector;
use \Phroute\Phroute\Dispatcher as Dispatcher;

class app
{
    public static $router = false;
    public static $presenter;
    public static $template;
    public static $presenter_template;

    public static function init_router()
    {
        return self::$router = new RouteCollector();
    }

    public static function router()
    {
		if( !self::$router )
		{
			self::init_router();
		}
        return self::$router;
    }

    public static function route()
    {
		try
		{
			$dispatcher = new Dispatcher( self::router()->getData() );
	        $results = $dispatcher->dispatch($_SERVER['REQUEST_METHOD'], parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
		}
		catch( \Exception $e )
		{
			switch( get_class($e) )
			{
				case 'Phroute\Phroute\Exception\HttpRouteNotFoundException':
					#debug('HttpRouteNotFoundException');
					http_response_code(404);
					break;
				case 'Phroute\Phroute\Exception\HttpMethodNotAllowedException':
					#debug('HttpMethodNotAllowedException');
					http_response_code(403);
					break;
				default:
					#debug($e);
					http_response_code(400);
					break;
			}
		}

    }

    public static function render()
    {
        return view::render();
    }


}
