<?php
namespace puffin\view;

class partial
{
	public $_path_ = '';
	public $_params_ = [];
	public $_contents_ = '';

	public function __construct( $path, $params )
	{
		$this->_path_ = PARTIAL_PATH . "/$path.php";
		$this->add_params($params);
	}

	#######################

	public function partial( $path, $params )
	{
		$p = new partial( $path, $params );
		return $p->render();
	}

	public function add_param( $key, $value )
	{
		if( !is_numeric($key) )
		{
			$this->_params_[$key] = $value;
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

	public function render()
	{
		$this->init_params();
		ob_start();
			include $this->_path_;
		$_display_ = ob_get_contents();
		ob_end_clean();
		return $_display_;
	}


}
