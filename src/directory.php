<?php

namespace puffin;

class directory
{
	public function __construct()
	{
		return false;
	}

	public static function exists( $path = '' )
	{
		if( !empty($path) )
		{
			return file_exists( $path );
		}

		return false;
	}

	public static function create( $path = '', $mode = 0755 )
	{
		if( !empty($path) && !empty($mode) )
		{
			return mkdir( $path, $mode, $recursive = true);
		}

		return false;
	}

	public static function delete( $path = '' )
	{
		if( !empty($path) )
		{
			return rmdir( $path );
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

	public static function scan( $path = '' )
	{
		if( !empty($path) )
		{
			$ignore_array = array( '.', '..' );
			$return = array();
			$contents = scandir( $path );
			foreach( $contents as $fs_element )
			{
				if( !in_array( $fs_element, $ignore_array ) )
				{
					$return []= array
					(
						'name' => $fs_element,
						'full_path' => "$path/$fs_element",
						'server_path' => str_replace( SERVER_ROOT . '/', '', $path ) . "/$fs_element",
						'contents' => self::count("$path/$fs_element")
					);
				}
			}
			return $return;
		}

		return false;
	}

	public static function rscan( $path )
	{
		if( !empty($path) )
		{
			$ignore_array = array( '.', '..' );
			$return = array();
			$contents = scandir( $path );
			if( empty($contents) )
			{
				return false;
			}
			foreach( $contents as $fs_element )
			{
				if( !in_array( $fs_element, $ignore_array ) )
				{
					$is_dir = is_dir("$path/$fs_element");
					if( !$is_dir )
					{
						$fileparts = explode('.',$fs_element);
						list( $filename, $ext ) = $fileparts;
					}
					else
					{
						$filename = '';
						$ext = '';
					}

					$return []= array
					(
						'name' => $fs_element,
						'filename' => $is_dir ? '' : $filename,
						'ext' => $is_dir ? '' : $ext,
						'full_path' => "$path/$fs_element",
						'server_path' => str_replace( SERVER_ROOT . '/', '', $path ) . "/$fs_element",
						'contents' => self::count("$path/$fs_element"),
						'type' => is_dir("$path/$fs_element") ? 'path' : 'file'
					);
					if( is_dir("$path/$fs_element") )
					{
						$subscan = self::rscan( "$path/$fs_element" );
						if( !empty($subscan) )
						{
							$return = array_merge( $return, $subscan );
						}
					}
				}
			}
			return $return;
		}

		return false;
	}

	public static function count( $path = '' )
	{
		if( !empty($path) )
		{
			$ignore_array = array( '.', '..' );
			$return = 0;
			if( $contents = @scandir( $path ) )
			{
				foreach( $contents as $fs_element )
				{
					if( !in_array( $fs_element, $ignore_array ) )
					{
						$return++;
					}
				}
				return $return;
			}
		}

		return false;
	}

}
