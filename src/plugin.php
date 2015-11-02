<?php
namespace puffin;

class plugin
{
	protected static $plugins = array();

	public static function register( $name )
	{
		include_once PLUGIN_PATH . '/' . $name . '.php';
		self::$plugins[$name] = new $name();
	}

	public static function run( $method )
	{
		foreach( self::$plugins as $plugin )
		{
			if( method_exists($plugin, $method) && is_callable( array($plugin, $method) ) )
			{
				$plugin->$method();
			}
		}
	}
}
