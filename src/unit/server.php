<?php

namespace puffin\unit;

class server
{
	protected $tests = array();
	protected $filters = array();

	public function __construct()
	{
		return false;
	}

	public function go()
	{
		$this->set_tests( TEST_PATH );
		return $this->do_unit_testing();
	}

	protected function do_unit_testing()
	{
		$filters = $this->get_filters();
		$tests = $this->get_tests();
		$test_results = array();

		foreach( $tests as $test )
		{
			if( strpos($test['name'], '.php') !== false )
			{
				$test_file = str_replace( '/', '_', str_replace( TEST_PATH . '/', '', $test['full_path'] ) );
				if( empty($filters ) || in_array( $test_file, $filters ) )
				{
					include $test['full_path'];
					$class = basename($test_file, '.php') . '_test';

					$test = new $class;
					$test_results []= $test->_start();
				}
			}
		}
		return $test_results;
	}

	protected function get_filters()
	{
		return $this->filters;
	}

	protected function set_filters( $arg_array )
	{
		$this->filters = $arg_array;
	}

	protected function get_tests()
	{
		return $this->tests;
	}

	protected function set_tests( $path )
	{
		$this->tests = \puffin\directory::rscan( $path );
	}
}
