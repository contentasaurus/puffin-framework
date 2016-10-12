<?php
namespace puffin;

class environment
{
	private static $environments = false;

	public function __construct(){}

	public static function init( $path )
	{
		self::$environments = json_decode( file_get_contents( $path ), $assoc = true );
	}

	public static function load( $which = '' )
	{
		if( empty($which) )
		{
			self::load_by_domain( $_SERVER['SERVER_NAME'] );
			return;
		}

		self::unpack( self::$environments[$which] );
	}

	public static function load_by_domain( $domain )
	{
		foreach( self::$environments as $name => $environment )
		{
			if( $environment['url'] == $domain )
			{
				self::unpack( $environment );
				return;
			}
		}
	}

	public static function unpack( $environment )
	{
		self::unpack_dsn($environment['dsn'] );
		self::unpack_session($environment['session'] );
	}

	private static function unpack_dsn( $DSNs )
	{
		foreach( $DSNs as $dsn )
		{
			dsn::set( $dsn['name'], $dsn['credentials'] );
		}
	}

	private static function unpack_session( $session )
	{
		$_SESSION['environment'] = $session;
	}

}
