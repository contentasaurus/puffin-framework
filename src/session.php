<?php
namespace puffin;

class session
{
	public static $id;
	public static $name = 'PUFFIN';
	public static $breadcrumbs = 10;
	public static $expires = 3600;
	public static $params = array();

	public function __construct(){}

	public static function start()
	{
		session_name( self::get_name() );
		session_start();
		self::$id = session_id();

		if( is_null( $start_time = self::get('start_time') ) )
		{
			self::set('start_time', date('U') );
			$_SESSION['page_views'] = 1;
		}
		else
		{
			$_SESSION['page_views']++;
			$_SESSION['tracking'] []= array
			(
				'timestamp' => date('U'),
				'request_method' => $_SERVER['REQUEST_METHOD'],
				'url' => $_SERVER['REQUEST_URI'],
				'querystring' => @$_SERVER['QUERY_STRING'],
				'referrer' => @$_SERVER['HTTP_REFERER'],
			);
			if( count($_SESSION['tracking']) > self::$breadcrumbs )
			{
				array_shift( $_SESSION['tracking'] );
			}
		}

		self::set( 'session_id', self::$id );
		self::set( 'ip_address', $_SERVER['REMOTE_ADDR'] );
		self::set( 'user_agent', $_SERVER['HTTP_USER_AGENT'] );

		if( is_null( $user = self::get('user') ) )
		{
			self::set('user', false );
		}

	}

	public static function stop()
	{
		if( is_null( $stop_time = self::get('stop_time') ) )
		{
			self::set('stop_time', date('U') );
		}

	}

	public static function destroy()
	{
		session_destroy();
	}

	public static function set_name( $name )
	{
		self::$name = $name;
	}

		public static function get_name()
	{
		return self::$name;
	}

	public static function set_expires( $seconds )
	{
		self::$expires = $seconds;
	}

	public static function get_expires()
	{
		return self::$expires;
	}

	public static function restart()
	{
		session_regenerate_id();
		self::$id = session_id();
	}

	public static function set( $key, $value )
	{
		$_SESSION[ $key ] = $value;
	}

	public static function un_set( $key )
	{
		unset( $_SESSION[ $key ] );
	}

	public static function get( $key )
	{
		if( isset( $_SESSION[$key] ) )
		{
			return $_SESSION[$key];
		}
		else
		{
			return null;
		}
	}
}
