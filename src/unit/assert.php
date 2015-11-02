<?php

namespace puffin\unit;

class assert
{
	public function assert_array_contains( $array, $what )
	{
		return in_array( $what, $array );
	}

	public function assert_array_contains_only_type( $type, $array )
	{
		#
		#	$type should be function name like is_null, is_string, etc
		#
		foreach( $array as $key=>$value )
		{
			if( !$type($value) )
			{
				return false;
			}
		}
		return true;
	}

	public function assert_array_has_key( $array, $key )
	{
		return array_key_exists( $key, $array );
	}

	public function assert_array_size_equals( $array, $size )
	{
		return count( $array, $recursive = false) == $size ;
	}

	public function assert_array_not_contains( $array, $what )
	{
		return !in_array( $what, $array );
	}

	public function assert_class_has_method( $class, $method )
	{
		return method_exists( $class, $method );
	}

	public function assert_class_has_property( $class, $property )
	{
		return property_exists( $class, $property );
	}

	public function assert_equals( $x, $y )
	{
		if( $x == $y )
		{
			return true;
		}
		return false;
	}

	public function assert_empty( $x )
	{
		if( empty($x) )
		{
			return true;
		}
		return false;
	}

	public function assert_false( $php_code )
	{
		if( !$php_code )
		{
			return true;
		}
		return false;
	}

	public function assert_file_exists( $path )
	{
		return file_exists( $path );
	}

	public function assert_greater_than( $x, $y )
	{
		return $x > $y;
	}

	public function assert_greater_than_or_equal( $x, $y )
	{
		return $x >= $y;
	}

	public function assert_identical( $x, $y )
	{
		if( $x === $y )
		{
			return true;
		}
		return false;
	}

	public function assert_less_than( $x, $y )
	{
		return $x < $y;
	}

	public function assert_less_than_or_equal( $x, $y )
	{
		return $x <= $y;
	}

	public function assert_not_equals( $x, $y )
	{
		if( $x != $y )
		{
			return true;
		}
		return false;
	}

	public function assert_not_empty( $x )
	{
		if( !empty($x) )
		{
			return true;
		}
		return false;
	}

	public function assert_not_null( $x )
	{
		return !is_null( $x );
	}

	public function assert_null( $x )
	{
		return is_null( $x );
	}

	public function assert_preg_match( $regex, $string )
	{
		return preg_match( $regex, $string );
	}

	public function assert_string_ends_with( $string, $substring )
	{
		return preg_match( "/$substring$/", $string );
	}

	public function assert_string_starts_with( $string, $substring )
	{
		return preg_match( "/^$substring/", $string );
	}

	public function assert_true( $php_code )
	{
		if( $php_code )
		{
			return true;
		}
		return false;
	}

	public function assert_type( $x, $type )
	{
		if( gettype($x) == $type  )
		{
			return true;
		}
		return false;
	}

	public function assert_class_name( $x, $type )
	{
		if( get_class($x) == $type  )
		{
			return true;
		}
		return false;
	}
}
