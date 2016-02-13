<?php
namespace puffin;

class message
{
	public function __construct(){}

	public static function add( $mixed )
	{
		$messages = session::get('__messages__');

		if( is_null($messages) )
		{
			$messages = [];
		}

		$messages []= $mixed;

		session::set('__messages__', $messages);
	}

	public static function clear()
	{
		session::set('__messages__', []);
	}

	public static function get()
	{
		$messages = session::get('__messages__');
		if( empty($messages) )
		{
			$messages = [];
		}
		return $messages;
	}
}
