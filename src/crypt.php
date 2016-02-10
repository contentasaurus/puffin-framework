<?php
namespace puffin;

/*
	$crypt = new crypt();
	$crypt->init([
		'key' => 'Puffins can fly and swim and walk',
		'cipher' => MCRYPT_RIJNDAEL_128,
		'mode' => 'cbc'
	]);
	$enc_result = $crypt->encrypt( 'bird' );

	$dec_result = $crypt->decrypt( $enc_result );
*/

class crypt
{
	protected $key = '';
	protected $key_size = 0;
	protected $cipher = MCRYPT_RIJNDAEL_128;
	protected $mode = MCRYPT_MODE_CBC;
	protected $iv;

	public function __controller(){}

	public function init( $options )
	{
		foreach( $options as $k => $v )
		{
			switch($k)
			{
				case 'key':
					$this->set_key( $v );
					break;
				case 'cipher':
					$this->set_cipher( $v );
					break;
				case 'mode':
					if( in_array( $v, ['ecb','cbc','cfb','ofb','nofb','stream'] ) )
					{
						$this->set_mode( v );
					}
					break;
				default: break;
			}
		}
		$this->set_iv();
	}

	#----

	public function get_key()
	{
		return $this->key;
	}

	public function set_key( $key, $pack = 'H*' )
	{
		$this->key = pack( $pack, $key );
		$this->key_size = strlen($this->key);
	}

	#----

	public function get_mode()
	{
		return $this->mode;
	}

	public function set_mode( $mode )
	{
		$modes = [
			'ecb' => MCRYPT_MODE_ECB,
			'cbc' => MCRYPT_MODE_CBC,
			'cfb' => MCRYPT_MODE_CFB,
			'ofb' => MCRYPT_MODE_OFB,
			'nofb' => MCRYPT_MODE_NOFB,
			'stream' => MCRYPT_MODE_STREAM
		];
		$this->mode = $modes[$mode];
	}

	#----

	public function get_iv()
	{
		return $this->iv;
	}

	public function set_iv()
	{
		$iv_size = mcrypt_get_iv_size( $this->get_cipher() , $this->get_mode() );
		$this->iv = mcrypt_create_iv( $iv_size, MCRYPT_RAND );
	}

	#----

	public function get_cipher()
	{
		return $this->cipher;
	}

	public function set_cipher( $cipher )
	{
		return $this->cipher = $cipher;
	}

	#----

	public function encrypt( $data )
	{
		return mcrypt_encrypt( $this->get_cipher(), $this->get_key(), $data, $this->get_mode(), $this->get_iv() );
	}

	public function decrypt( $data )
	{
		return mcrypt_decrypt( $this->get_cipher(), $this->get_key(), $data, $this->get_mode(), $this->get_iv() );
	}
}
