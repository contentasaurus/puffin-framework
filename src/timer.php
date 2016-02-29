<?php
namespace puffin;

class timer
{
	protected $timers = [];
	protected $last_marker = [];

	public function __construct(){}

	public function start( $comment = '' )
	{
		$this->set_last_marker($this->timers['start'] = $this->build_array( microtime( $as_float = true ), $comment ) );
		return $this;
	}

	public function mark( $comment = '' )
	{
		$this->set_last_marker( $this->timers['mark'][] = $this->build_array( microtime( $as_float = true ), $comment ) );
		return $this;
	}

	public function end( $comment = '' )
	{
		$this->set_last_marker( $this->timers['end'] = $this->build_array( microtime( $as_float = true ), $comment ) );
		return $this;
	}

	public function out()
	{
		$last_marker = $this->get_last_marker();
		$time = $last_marker['time'];
		$comment = $last_marker['comment'];
		$duration = $last_marker['duration'];
		$total_duration = $last_marker['total_duration'];

		return "[$time] $comment (Time: $duration, Total Time: $total_duration)";
	}

	public function get_timers()
	{
		return $this->timers;
	}

	# ---------------------------------------------------------------------

	protected function get_start_time()
	{
		if( !empty($this->timers) )
		{
			$time = $this->timers['start']['time'];
		}
		else
		{
			$time = 0;
		}
		return $time;
	}

	protected function get_last_marker()
	{
		return $this->last_marker;
	}

	protected function set_last_marker( $marker )
	{
		$this->last_marker = $marker;
	}

	protected function get_last_time()
	{
		if( !empty($this->last_marker) )
		{
			$time = $this->last_marker['time'];
		}
		else
		{
			$time = 0;
		}
		return $time;
	}

	protected function build_array( $marker, $comment )
	{
		return array
		(
			'time' => $marker,
			'comment' => $comment,
			'duration' => $marker - $this->get_start_time(),
			'total_duration' => $marker - $this->get_last_time()
		);
	}

}
