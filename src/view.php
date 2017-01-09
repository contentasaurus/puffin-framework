<?php
namespace puffin;

class view
{
	public static $engine = false;

	public static function init( $engine )
	{
		switch( $engine )
		{
			case 'json':
				self::$engine = new view\json();
				break;
			case 'php':
				self::$engine = new view\php();
				break;
			case 'mustache':
			default:
				self::$engine = new view\mustache();
				break;
		}
	}

	public static function set_response_code($code){
	    // this is a bit sloppy. Prob better to have some kind of 
	    // a string or constant with the specific engine name to eval
	    // against or figure some other way out of allowing you to set <thead>
	    // the response code for json responses
	    if(get_class(self::$engine) === view\json::class){
		self::$engine->set_response_code( $code );
	    }
	}
    

	public static function title( $title = '' )
	{
		self::$engine->title( $title );
	}

	public static function template( $new_template = '' )
	{
		self::$engine->template( $new_template );
	}

	public static function layout( $new_layout = '' )
	{
		self::$engine->layout( $new_layout );
	}

	public static function add_css( $path, $media='all', $condition=false )
	{
		self::$engine->add_css( $path, $media, $condition );
	}

	#######################

	public static function add_js( $path, $nonblocking = false )
	{
		self::$engine->add_js( $path, $nonblocking );
	}

	#######################

	public static function add_param( $key, $value )
	{
		self::$engine->add_param( $key, $value );
	}

	public static function add_params( $array )
	{
		self::$engine->add_params( $array );
	}

	public static function render()
	{
		return self::$engine->render();
	}


}
