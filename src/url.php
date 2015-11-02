<?php
namespace puffin;

class url
{
	public static function redirect( $location = false )
	{
		if(!$location)
		{
			return false;
		}
		header("Location: $location");
		exit;
	}
}
