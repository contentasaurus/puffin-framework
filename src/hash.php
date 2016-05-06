<?php
namespace puffin;

#
#	hash::key('some key');
#	hash::method('sha256');
#	$hash = hash::make('some string');
#

class hash
{
	private static $method = 'sha256';
	private static $key = '';

	public function __construct(){}

	public static function make( $string )
	{
		return hash_hmac( self::get('method'), $string, self::salt($string, self::get('key')) );
	}

	public static function get_supported()
	{
		return hash_algos();

		//
		//  Example return
		//	[
		// 		[0] => md2
		// 		[1] => md4
		// 		[2] => md5
		// 		[3] => sha1
		// 		[4] => sha224
		// 		[5] => sha256
		// 		[6] => sha384
		// 		[7] => sha512
		// 		[8] => ripemd128
		// 		[9] => ripemd160
		// 		...
		// 	]
		//
	}

	public static function set( $key, $value )
	{
		self::$$key = $value;
	}

	public static function get( $key )
	{
		if( !isset(self::$$key) )
		{
			return false;
		}
		return self::$$key;
	}

	public static function salt( $string, $key = '' )
	{
		$hash = '';

	    if( !empty($string) )
		{
			foreach( str_split($string) as $char )
			{
	        	$hash = hash( self::get('method'), $hash.$key.$char );
	    	}
		}

	    return $hash;
	}

}
