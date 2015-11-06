<?php
namespace puffin;
use \Phroute\Phroute\RouteCollector as RouteCollector;
use \Phroute\Phroute\Dispatcher as Dispatcher;

require_once '../init.php';

class app
{
    public static $router;
    public static $presenter;
    public static $template;
    public static $presenter_template;

    public static function init()
    {
        self::init_router();
        self::init_mustache();
    }

    public static function init_router()
    {
        self::$router = new RouteCollector();
    }

    public static function router()
    {
        return self::$router;
    }

    public static function route()
    {
        $dispatcher = new Dispatcher( self::router()->getData() );
        $dispatcher->dispatch($_SERVER['REQUEST_METHOD'], parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    }

    public static function init_mustache()
    {
        // $partials = self::get_partials();
        // $layouts = self::get_layouts();

        self::$presenter = new \Mustache_Engine
        (
            [
                'pragmas' => [\Mustache_Engine::PRAGMA_BLOCKS],
                'template_class_prefix' => '__Mustache_',
                'partials_loader' => new \Mustache_Loader_CascadingLoader([
                    new \Mustache_Loader_FilesystemLoader( LAYOUT_PATH, $options = ['extension' => MUSTACHE_EXT] ),
                    new \Mustache_Loader_FilesystemLoader( PARTIAL_PATH, $options = ['extension' => MUSTACHE_EXT] )
                ]),
                'charset' => 'UTF-8',
                'logger' => new \Mustache_Logger_StreamLogger('php://stderr'),
                'strict_callables' => true
            ]
        );
    }

    public static function render()
    {
        view::prepare();
        return self::$presenter->render( self::$template, view::$params );
    }


}
