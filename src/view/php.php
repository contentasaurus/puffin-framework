<?php
namespace puffin\view;

class php
{
	public $_params_ = [];

	public $_title_ = '';
	public $meta = [];
	public $_css_ = [];
	public $_js_ = [];
	public $_nonblocking_js_ = [];
	public $_script_ = '';
	public $_layout_ = '';

	public function __construct()
	{
		$this->init();
	}

	public function init()
	{
		return $this;
	}

	public function title( $_title_ = '' )
	{
		if( !empty($_title_) )
		{
			$this->_title_ = $_title_;
		}
	}

	public function template( $new_script = '' )
	{
		if( empty($new_script) )
		{
			return $this->_script_;
		}
		else
		{
			$this->_script_ = SCRIPT_PATH . "/$new_script.php";
		}
	}

	public function layout( $new_layout = '' )
	{
		if( empty($new_layout) )
		{
			return $this->_layout_;
		}
		else
		{
			$this->_layout_ = LAYOUT_PATH . "/$new_layout.php";
		}
	}

	public function add_css( $path, $media='all', $condition=false )
	{
		$temp['src'] = $path;
		$temp['media'] = $media;
		$temp['condition'] = $condition;

		$this->_css_ []= $temp;
	}

	public function prepare_css()
	{
		$_css_string = '';

		foreach( $this->_css_ as $stylesheet )
		{
			if( $stylesheet['condition'] )
			{
				$_css_string .= '<!--[if '.$stylesheet['condition'].']>' . chr(10) . chr(13);
			}
			$_css_string .= '<link rel="stylesheet" type="text/css" href="'. $stylesheet['src'] . '" media="'.$stylesheet['media'].'" />' . chr(10) . chr(13);
			if( $stylesheet['condition'] )
			{
				$_css_string .= '<![endif]-->' . chr(10) . chr(13);
			}
		}

		return $_css_string;
	}

	#######################

	public function add_js( $path, $nonblocking = false )
	{
		if( $nonblocking )
		{
			$this->_nonblocking_js_ []= '<script type="text/javascript" src="'.$path.'"></script>' . chr(10) . chr(13);
		}
		else
		{
			$this->_js_ []= '<script type="text/javascript" src="'.$path.'"></script>' . chr(10) . chr(13);
		}
	}

	public function prepare_js( $nonblocking )
	{
		if( $nonblocking )
		{
			return implode($this->_nonblocking_js_, '');
		}
		else
		{
			return implode($this->_js_, '');
		}
	}

	#######################

	public function add_param( $key, $value )
	{
		if( !is_numeric($key) )
		{
			$this->$_params_[$key] = $value;
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

	private function init_params()
	{
		foreach( $this->_params_ as $k => $v )
		{
			$this->$k = $v;
		}
	}

	public function prepare()
	{
		$p = new partial( $this->_script_, $this->_params_ );

		$this->TITLE = $this->_title_;
		$this->META = '';
		$this->CSS = $this->prepare_css();
		$this->JS = $this->prepare_js( $nonblocking = false );
		$this->CONTENTS = $p->render();
		$this->NONBLOCKING_JS = $this->prepare_js( $nonblocking = true );
	}

	public function render()
	{
		$this->prepare();
		$this->init_params();
		ob_start();
			include $this->layout();
		$_display_ = ob_get_contents();
		ob_end_clean();
		return $_display_;
	}
}
