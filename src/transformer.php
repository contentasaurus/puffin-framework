<?php
namespace puffin;

class transformer
{
	protected static $library = [];

	public static __callStatic( $name, $params )
	{
		if( array_key_exists( $name, self::$library) )
		{
			return self::$library[$name]->$name( implode(',',$params) );
		}
		else
		{
			return false;
		}
	}

	public static function _load()
	{
		$dir = new directory();
		$files = $dir->rscan( TRANSFORMER_PATH );

		foreach( $files as $file )
		{
			if( $file['type'] == 'file' && $file['ext'] == 'php' )
			{
				include_once $file['full_path'];
				$filename = $file['name'];
				self::$library[$filename] = new $filename();
			}
		}

	}
}
