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

	public static function back()
	{
		header('Location: ' . $_SERVER['HTTP_REFERER']);
		exit;
	}
}
