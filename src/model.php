<?php
namespace puffin;

class model
{
	public static function load( $model )
	{
		$path = MODEL_PATH . "/$model.php";
		require_once $path;
		$model_file = str_replace( '/', '_', $model );
		return new $model_file();
	}
}
