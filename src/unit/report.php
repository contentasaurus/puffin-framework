<?php

namespace puffin\unit;

class report
{
	protected $num_success = 0;
	protected $num_failure = 0;

	protected $report_name;

	protected $report_start;
	protected $report_end;
	protected $total_time;

	protected $test_records = array();

	public function __construct()
	{
		$this->test_records = array();
	}

	public function set_name( $name )
	{
		$this->report_name = $name;
	}

	public function record( $name, $start, $end, $result )
	{
		$test_record = array
		(
			'name' => $name,
			'start' => $start,
			'end' => $end,
			'total' => $end - $start,
			'result' => $result
		);

		array_push( $this->test_records, $test_record);

		return $this->_talley( $result );
	}

	public function display()
	{
		$results = [];

		$i = 0;
		foreach( $this->test_records as $record )
		{
			$results[$i]['name'] = $record['name'];
			$results[$i]['status'] = $record['result'] ? 'pass' : 'fail';
			$i++;
		}

		return [
			'class' => $this->report_name,
			'total_runtime' => number_format( $this->total_time, 2 ) . ' Seconds',
			'successes' => $this->num_success,
			'failures' => $this->num_failure,
			'results' => $results
		];

	}

	public function start()
	{
		$this->report_start = microtime(true);
	}

	public function end()
	{
		$this->report_end = microtime(true);
		$this->total_time = $this->report_end - $this->report_start;
	}

	# -------------------------------------------

	protected function _talley( $result = false )
	{
		if( $result )
		{
			return $this->_success();
		}
		else
		{
			return $this->_failure();
		}
	}

	protected function _success()
	{
		if( $this->num_success++ )
		{
			return true;
		}
		return false;
	}

	protected function _failure()
	{
		if( $this->num_failure++ )
		{
			return true;
		}
		return false;
	}

}
