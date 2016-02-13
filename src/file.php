<?php
namespace puffin;

class file
{
	public function __construct(){}

	public static function exists( $path = '' )
	{
		if( !empty($path) )
		{
			return file_exists( $path );
		}

		return false;
	}

	public static function append( $path = '', $data = '' )
	{
		if( !empty($path) && !empty($data) )
		{
			return file_put_contents( $path, $data, FILE_APPEND);
		}

		return false;
	}

	public static function copy( $from = '', $to = '' )
	{
		if( !empty($from) && !empty($to) )
		{
			return copy( $from, $to );
		}

		return false;
	}

	public static function create( $path = '' )
	{
		if( !empty($path) )
		{
			return file_put_contents( $path, '');
		}

		return false;
	}

	public static function delete( $path = '' )
	{
		if( !empty($path) )
		{
			return unlink( $path );
		}

		return false;
	}

	public static function get_mode( $path = '' )
	{
		if( !empty($path) )
		{
			return substr(sprintf('%o', fileperms($path)), -4);
		}

		return false;
	}

	public static function get_size( $path = '' )
	{
		if( !empty($path) )
		{
			return filesize( $path );
		}

		return false;
	}

	public static function read( $path = '' )
	{
		if( !empty($path) )
		{
			return file_get_contents( $path );
		}

		return false;
	}

	public static function rename( $from = '', $to = '' )
	{
		if( !empty($from) && !empty($to) )
		{
			return rename( $from, $to );
		}

		return false;
	}

	public static function set_mode( $path = '', $mode = 0755 )
	{
		if( !empty($path) && !empty($mode) )
		{
			return chmod( $path, $mode );
		}

		return false;
	}

	public static function touch( $path = '' )
	{
		if( !empty($path) )
		{
			return touch( $path );
		}

		return false;
	}

	public static function upload( $file = '', $folder_path = '' )
	{
		if( !empty($folder_path) && !empty($file) )
		{
			return move_uploaded_file( $file['tmp_name'], "$folder_path/{$file['name']}" );
		}

		return false;
	}

	public static function write( $path = '', $data = '' )
	{
		if( !empty($path) && !empty($data) )
		{
			return file_put_contents( $path, $data);
		}

		return false;
	}

}
