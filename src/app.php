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
        $dispatcher = new Dispatcher( self::router()->getData() );
        $dispatcher->dispatch($_SERVER['REQUEST_METHOD'], parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    }

    public static function render()
    {
        return view::render();
    }


}
