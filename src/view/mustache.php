<?php
namespace puffin\view;

class mustache
{
	public $engine = false;

	public $params = [];

	public $title = '';
	public $title_template = '';

	public $css = [];
	public $css_template = '';

	public $js = [];
	public $js_template = '';

	public $nonblocking_js = [];
	public $nonblocking_js_template = '';

	public $template = '';
	public $template_template = '';

	public $layout = '';
	public $layout_template = '';

	public function __construct()
	{
		$this->init();
	}

	public function init()
	{
		\Mustache_Autoloader::register();

		$this->$engine = new \Mustache_Engine
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

		return $this;
	}

	public function title( $title = '' )
	{
		if( !empty($title) )
		{
			$this->$title = $title;
			$this->$title_template = '{{$ TITLE }}'. $title .'{{/ TITLE }}';
		}
	}

	public function template( $new_template = '' )
	{
		if( empty($new_template) )
		{
			return $this->$template;
		}
		else
		{
			$path = SCRIPT_PATH . "/$new_template" . MUSTACHE_EXT;
			$this->$template = $new_template;
			$this->$template_template = '{{$ CONTENTS }}' . file_get_contents( $path ) . '{{/ CONTENTS }}';
		}
	}

	public function layout( $new_layout = '' )
	{
		if( empty($new_layout) )
		{
			return $this->$layout;
		}
		else
		{
			$path = LAYOUT_PATH . "/$new_layout" . MUSTACHE_EXT;
			$this->$layout = $new_layout;
			$this->$layout_template = file_get_contents( $path );
		}
	}

	public function add_css( $path, $media='all', $condition=false )
	{
		$temp['src'] = $path;
		$temp['media'] = $media;
		$temp['condition'] = $condition;

		$this->$css []= $temp;

		$this->prepare_css();
	}

	public function prepare_css()
	{
		$css_string = '';

		foreach( $this->$css as $stylesheet )
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

		$this->$css_template = '{{$ CSS }}' . $css_string . '{{/ CSS }}';
	}

	#######################

	public function add_js( $path, $nonblocking = false )
	{
		if( $nonblocking )
		{
			$this->$nonblocking_js []= '<script type="text/javascript" src="'.$path.'"></script>' . chr(10) . chr(13);
		}
		else
		{
			$this->$js []= '<script type="text/javascript" src="'.$path.'"></script>' . chr(10) . chr(13);
		}

		$this->prepare_js( $nonblocking );
	}

	public function prepare_js( $nonblocking )
	{
		if( $nonblocking )
		{
			$this->$nonblocking_js_template = '{{$ NONBLOCKING_JS }}' . implode($this->$nonblocking_js, '') . '{{/ NONBLOCKING_JS }}';
		}
		else
		{
			$this->$js_template = '{{$ JS }}'. implode($this->$js, '') . '{{/ JS }}';
		}
	}

	public function prepare_nonblocking_js()
	{

	}

	#######################

	public function add_param( $key, $value )
	{
		if( !is_numeric($key) )
		{
			$this->$params[$key] = $value;
		}
	}

	public function add_params( $array )
	{
		if( is_array( $array ) )
		{
			foreach( $array as $k=>$v )
			{
				$this->add_param( $k, $v );
			}
		}
	}

	public function prepare()
	{
		$layout = $this->$layout;
		$prepared_template = "{{% BLOCKS }}{{< $layout }}"
							. $this->$title_template
							. $this->$css_template
							. $this->$js_template
							. $this->$template_template
							. $this->$nonblocking_js_template
							. "{{/ $layout }}";

		app::$template = $prepared_template;
	}

	public function render()
	{
		$this->prepare();
		$this->$engine->render( $this->$template, view::$params );
	}


}
