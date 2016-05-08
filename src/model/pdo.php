<?php
namespace puffin\model;
use puffin\dsn as dsn;

class pdo
{
	public $db = false;
	protected $connection = 'default';


	public function __construct(){}

	public function use_connection( $connection )
	{
		if( $connection )
		{
			$this->connection = $connection;
		}
	}

	public function insert_id()
	{
		return $this->db->lastInsertId();
	}

	#======================================================================
	# Builders
	#======================================================================

	public function create( $inputs = array() )
	{
		$columns = array();
		$placeholders = array();
		$values = array();

		foreach( $inputs as $column => $value )
		{
			$columns []= $column;
			$placeholders []= ":$column";
			$values[":$column"] = $value;
		}

		$keys = implode(',', $columns);
		$placeholders = implode(',', $placeholders);

		$this->_exec_( "INSERT INTO `{$this->table}` ( $keys ) VALUES ( $placeholders )", $values );

		return $this->insert_id();
	}

	public function read( $id = false )
	{
		if( is_numeric($id) )
		{
			return  $this->select_row( "select * from {$this->table} where id = :id" , [ ':id' => $id ] );
		}
		else
		{
			return  $this->select( "select * from {$this->table}" );
		}
	}

	public function read_ordered( $column = 'id', $direction = 'asc' )
	{
		return  $this->select( "select * from {$this->table} order by $column $direction" );
	}

	#======================================================================
	# Interfaces
	#======================================================================

	public function select( $request, $query_params = array() )
	{
		if( is_array($request) )
		{
			$template = reset($request);
			$query_params = end($request);
		}
		else if( is_string($request) )
		{
			$template = $request;
		}

		$statement = $this->_query_( $template, $query_params );

		return $statement->fetchAll( \PDO::FETCH_ASSOC );
	}

	public function select_raw( $request, $query_params = array() )
	{
		if( is_array($request) )
		{
			$template = reset($request);
			$query_params = end($request);
		}
		else if( is_string($request) )
		{
			$template = $request;
		}

		$statement = $this->_query_( $template, $query_params );

		return $statement;
	}

	public function select_row( $request, $query_params = array() )
	{
		$result = $this->select( $request, $query_params );
		return reset( $result );
	}

	public function select_one( $request, $query_params = array() )
	{
		$result = $this->select_row( $request, $query_params );
		return reset( $result );
	}

	#----------------------------------------------------------------------

	public function update( $id = false, $inputs = array() )
	{
		if( is_numeric($id) )
		{
			$update_inputs = array();
			$values = array();

			foreach( $inputs as $key => $value )
			{
				$update_inputs []= "`$key` = :$key";
				$values[":$key"] = $value;
			}

			$values[":id"] = $id;

			return $this->execute( "UPDATE {$this->table} SET " . implode(',',$update_inputs) . " where `id` = :id" , $values );
		}
		return false;
	}

	public function delete( $id )
	{
		if( is_numeric( $id ) )
		{
			return $this->execute( "delete from {$this->table} where id = :id" , [ ':id' => $id ] );
		}
		return false;
	}

	public function execute( $request, $query_params = array() )
	{
		if( is_array($request) )
		{
			$template = reset($request);
			$query_params = end($request);
		}
		else if( is_string($request) )
		{
			$template = $request;
		}

		return $this->_exec_( $template, $query_params );
	}

	#======================================================================
	# Doers
	#======================================================================

	private function connect()
	{
		return dsn::get( $this->connection );
	}

	private function _query_( $template, $query_params = array() )
	{
		$this->db = $this->connect();

		$statement = $this->db->prepare( $template );
		$statement->execute( $query_params );

		return $statement;
	}

	private function _exec_( $template, $query_params = array() )
	{
		$this->db = $this->connect();

		$statement = $this->db->prepare( $template );
		$statement->execute( $query_params );

		return $statement->rowCount();
	}

}
