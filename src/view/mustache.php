<?php
namespace puffin\view;

class mustache
{
	public static $engine = false;

	public static $params = [];

	public static $title = '';
	public static $title_template = '';

	public static $css = [];
	public static $css_template = '';

	public static $js = [];
	public static $js_template = '';

	public static $nonblocking_js = [];
	public static $nonblocking_js_template = '';

	public static $template = '';
	public static $template_template = '';

	public static $layout = '';
	public static $layout_template = '';

	public static function init()
	{
		\Mustache_Autoloader::register();

		self::$engine = new \Mustache_Engine
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

		return self;
	}

	public static function title( $title = '' )
	{
		if( !empty($title) )
		{
			self::$title = $title;
			self::$title_template = '{{$ TITLE }}'. $title .'{{/ TITLE }}';
		}
	}

	public static function template( $new_template = '' )
	{
		if( empty($new_template) )
		{
			return self::$template;
		}
		else
		{
			$path = SCRIPT_PATH . "/$new_template" . MUSTACHE_EXT;
			self::$template = $new_template;
			self::$template_template = '{{$ CONTENTS }}' . file_get_contents( $path ) . '{{/ CONTENTS }}';
		}
	}

	public static function layout( $new_layout = '' )
	{
		if( empty($new_layout) )
		{
			return self::$layout;
		}
		else
		{
			$path = LAYOUT_PATH . "/$new_layout" . MUSTACHE_EXT;
			self::$layout = $new_layout;
			self::$layout_template = file_get_contents( $path );
		}
	}

	public static function add_css( $path, $media='all', $condition=false )
	{
		$temp['src'] = $path;
		$temp['media'] = $media;
		$temp['condition'] = $condition;

		self::$css []= $temp;

		self::prepare_css();
	}

	public static function prepare_css()
	{
		$css_string = '';

		foreach( self::$css as $stylesheet )
		{
			if( $stylesheet['condition'] )
			{
				$css_string .= '<!--[if '.$stylesheet['condition'].']>' . chr(10) . chr(13);
			}
			$css_string .= '<link rel="stylesheet" type="text/css" href="'. $stylesheet['src'] . '" media="'.$stylesheet['media'].'" />' . chr(10) . chr(13);
			if( $stylesheet['condition'] )
			{
				$css_string .= '<![endif]-->' . chr(10) . chr(13);
			}
		}

		self::$css_template = '{{$ CSS }}' . $css_string . '{{/ CSS }}';
	}

	#######################

	public static function add_js( $path, $nonblocking = false )
	{
		if( $nonblocking )
		{
			self::$nonblocking_js []= '<script type="text/javascript" src="'.$path.'"></script>' . chr(10) . chr(13);
		}
		else
		{
			self::$js []= '<script type="text/javascript" src="'.$path.'"></script>' . chr(10) . chr(13);
		}

		self::prepare_js( $nonblocking );
	}

	public static function prepare_js( $nonblocking )
	{
		if( $nonblocking )
		{
			self::$nonblocking_js_template = '{{$ NONBLOCKING_JS }}' . implode(self::$nonblocking_js, '') . '{{/ NONBLOCKING_JS }}';
		}
		else
		{
			self::$js_template = '{{$ JS }}'. implode(self::$js, '') . '{{/ JS }}';
		}
	}

	public static function prepare_nonblocking_js()
	{

	}

	#######################

	public static function add_param( $key, $value )
	{
		if( !is_numeric($key) )
		{
			self::$params[$key] = $value;
		}
	}

	public static function add_params( $array )
	{
		if( is_array( $array ) )
		{
			foreach( $array as $k=>$v )
			{
				self::add_param( $k, $v );
			}
		}
	}

	public static function prepare()
	{
		$layout = self::$layout;
		$prepared_template = "{{% BLOCKS }}{{< $layout }}"
							. self::$title_template
							. self::$css_template
							. self::$js_template
							. self::$template_template
							. self::$nonblocking_js_template
							. "{{/ $layout }}";

		app::$template = $prepared_template;
	}

	public static function render()
	{
		view::prepare();
		self::$engine->render( self::$template, view::$params );
	}


}
