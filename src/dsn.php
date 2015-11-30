<?php

namespace puffin;

define("DEFAULT_DSN", 'default');

#
#	Different DB types have different requirements
#
#	pdo:  	type, name, user, pass, addr, [port] (default = 3306), [socket]
#	mongo:	type, name, user, pass, addr, [port] (default = 27017)
#
#	Dsn::set
#	(
#		'default',
#		array
#		(
#			'type' => 'mysql',
#			'name' => 'default_database',
#			'user' => 'default_user',
#			'pass' => 'default_password',
#			'addr' => 'localhost'
#		)
#	);
#
#
#	To get the database reference in the module or application code
#	simply use the following static method
#
#	Dsn::get(); 			Returns the default DSN as
#							defined in DEFAULT_DSN.
#
#	Dsn::get('default');	Returns the specified DSN, if
#							it exists. Otherwise returns false.
#

class dsn
{
	protected static $dictionary = array();
	public static $defined_dsns = array();

	public static function set( $name = '', $connection_info = array() )
	{
		if( !empty($connection_info) )
		{
			self::$dictionary[$name] = $connection_info;
			self::register( $name, $connection_info );
		}
	}

	public static function get( $name = '' )
	{
		if( !empty($name) )
		{
			if( isset(self::$defined_dsns[$name]) )
			{
				return self::$defined_dsns[$name];
			}
			return false;
		}
		else
		{
			return self::$defined_dsns['default'];
		}
	}

	public static function register( $connection_name, $connection_info )
	{
		self::$defined_dsns[$connection_name] = self::initialize( $connection_info );

		if( $connection_name == DEFAULT_DSN )
		{
			self::$defined_dsns['default'] = self::initialize( $connection_info );
		}

	}

	public static function initialize( $connection_info )
	{
		extract($connection_info);
		switch ($type)
		{
			case 'mongo':
				return self::initialize_mongo( $connection_info );
				break;
			default:
				return self::initialize_pdo( $connection_info );
			break;
		}
	}

	public static function initialize_pdo( $connection_info )
	{
		extract($connection_info);

		$dsn = "$type:host=$addr;";

		if( isset($name) &&!empty($name) )
		{
			$dsn .= "dbname=$name;";
		}

		if( isset($port) &&!empty($port) )
		{
			$dsn .= "port=$port;";
		}

		if( isset($socket) &&!empty($socket) )
		{
			$dsn .= "unix_socket=$socket";
		}

		return new PDO( $dsn, $user, $pass );
	}

	public static function initialize_mongo( $connection_info )
	{

		extract($connection_info);

		$dbparams = array();

		if( isset($user) && !empty($user) ) {

			$dbparams['username'] = $user;
			$dbparams['password'] = $pass;

		}

		if( isset($authdb) && !empty($authdb) ) {

			$dbparams['db'] = $authdb;

		} else {

			$dbparams['db'] = 'admin';
		}

		if( !isset($port) && empty($port) ) {

			$port = 27017;

		}

		return new \MongoClient
		(
				"mongodb://$addr:$port;", $dbparams
		);

	}

	public static function register_all()
	{
		foreach( self::$dictionary as $connection_name => $connection_info )
		{
			self::register( $connection_name, $connection_info );
		}
	}

	public static function destroy( $connection_name )
	{
		self::get($connection_name)->disconnect();
	}

	public static function destroy_all()
	{
		foreach( self::$defined_dsns as $connection_name )
		{
			self::destroy($connection_name);
		}
	}
}
