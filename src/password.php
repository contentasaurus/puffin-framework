<?php
namespace puffin;

#
#	hash::key('some key');
#	hash::method('sha256');
#	$hash = hash::make('some string');
#

class password
{
	const FAILURE = 0;
	const FAILURE_PASSWORD_INVALID = -3;
	const SUCCESS = 1;
	const SUCCESS_PASSWORD_REHASHED = 2;
	const PASSWORD_METHOD = PASSWORD_BCRYPT;

	protected $options = [ 'cost' => 10 ];

	public function __construct( $options = [] )
	{
		$this->set_options( $options );
	}

	public function make( $password )
	{
		$hash = password_hash( $password, self::PASSWORD_METHOD, $this->get_options() );

		if( $hash === false )
		{
			throw new Exception('Bcrypt hashing not supported.');
		}

		return $hash;
	}

	public function is_valid( $password, $password_hash )
	{
		$result = self::FAILURE;

		$is_valid = $this->verify( $password, $password_hash );
		$needs_rehash = $this->needs_rehash( $password_hash, self::PASSWORD_METHOD, $this->get_options() );

		if ($is_valid === true)
		{
			$result = self::SUCCESS;
		}

		if ($is_valid === true && $needs_rehash === true)
		{
			$new_hash = $this->rehash( $password );
			if( $this->verify( $password, $new_hash ) )
			{
				$result = self::SUCCESS_PASSWORD_REHASHED;
			}
		}

		return $result;
	}

	public function verify( $password, $password_hash )
	{
		if( password_verify( $password, $password_hash ) === true )
		{
			return self::SUCCESS;
		}

		return self::FAILURE;
	}

	public function needs_rehash( $password )
	{
		if( password_needs_rehash( $password_hash, self::PASSWORD_METHOD, $this->get_options() ) === true )
		{
			return $this->make( $password );
		}
	}

	public function get_options( $id )
	{
		if( !empty($id) )
		{
			return $this->options[$id];
		}
		else
		{
			return $this->options;
		}
	}

	public function set_option( $key, $value )
	{
		$this->options[$key] = $value;
	}

	public function set_options( $options = [] )
	{
		foreach( $options as $key => $value )
		{
			$this->set_option( $key, $value );
		}
	}
}
