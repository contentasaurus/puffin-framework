<?php

namespace puffin\unit;

class test extends assert
{
	protected $report;
	protected $tests = array();

	public function _start()
	{
		$this->report = new report;
		$this->tests = $this->_get_tests( $this );
		$this->report->set_name( get_class($this) );
		$this->report->start();
		foreach( $this->tests as $test )
		{
			$this->_run_test( $test );
		}
		return $this->_end();
	}

	protected function _get_tests( $class )
	{
		$tests = array();
		$methods = get_class_methods( $class );

		foreach( $methods as $method )
		{
			if( strpos( $method, 'test_' ) === 0 )
			{
				$tests []= "$method";
			}
		}
		return $tests;
	}

	protected function _run_test( $name )
	{
		$testclass = get_class($this);
		$testcase = new $testclass;
		$start = microtime(true);
		$result = $testcase->$name();
		$end = microtime(true);
		unset($testcase);
		$this->report->record( $name, $start, $end, $result );
	}

	protected function _end()
	{
		$this->report->end();
		return $this->report->display();
	}

}
