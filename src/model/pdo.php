<?php
namespace puffin\model;
use puffin\dsn as dsn;

if( !defined('DB_DEBUG_QUERY') )
{
	define('DB_DEBUG_QUERY', false);
}

class pdo
{
	public $db = false;
	protected $connection = 'default';
	protected $last_statement = false;
	protected $dynamic_columns = [];

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

	public function get_last_statement()
	{
		return $this->last_statement;
	}

	#======================================================================
	# dynamic columns
	#======================================================================

	public function column_check( $id = false, $column = false )
	{
		$sql = "SELECT COLUMN_CHECK($column) FROM `{$this->table}` WHERE id = :id";
		$params = [ ':id' => $id ];

		return $this->select_one( $sql, $params );
	}

	public function column_create( $id = false, $column = false, $values = [] )
	{
		$sql_part = [];
		foreach( $values as $k => $v )
		{
			$sql_part []= "'$k','$v'";
		}

		$kvs = implode(',',$sql_part);

		$sql = "UPDATE `{$this->table}`
				SET `$column` = column_create($kvs)
				WHERE `id` = :id";
		$params = [ ':id' => $id ];

		return $this->execute( $sql, $params );
	}

	public function column_add( $id = false, $column = false, $values = [] )
	{
		if( !count($values) % 2 )
		{
			#only $values with even numbers of elements allowed
			return false;
		}

		$sql_part = [];
		foreach( $values as $k => $v )
		{
			$sql_part []= "$column = column_add($column, '$k', '$v' )";
		}

		$sql = "UPDATE `{$this->table}` SET " .
				implode( ', ', $sql_parts ) .
			   "WHERE id = :id";
		$params = [ ':id' => $id ];

		return $this->execute( $sql, $params );
	}

	#======================================================================
	# Builders
	#======================================================================

	public function create( $inputs = [] )
	{
		$columns = [];
		$placeholders = [];
		$values = [];

		foreach( $inputs as $column => $value )
		{
			$columns []= "`$column`";
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
		$dyn_select = [];

		$dyn = '';

		if( !empty($this->dynamic_columns) )
		{
			foreach( $this->dynamic_columns as $column )
			{
				$dyn_select []= "column_json($column) as $column";
			}

			$dyn = ', ';
		}

		$dyn .= implode(', ', $dyn_select);

		$query = 'select';
		$sql = "select * $dyn from {$this->table}";
		$params = [];

		if( is_numeric($id) )
		{
			$query = 'select_row';
			$sql .= " where id = :id";
			$params = [ ':id' => $id ];
		}

		return $this->$query( $sql, $params );
	}

	public function read_ordered( $column = 'id', $direction = 'asc' )
	{
		return  $this->select( "select * from `{$this->table}` order by `$column` $direction" );
	}

	public function read_dynamic( $id, $column )
	{
		$results = $this->select_one( "select column_json($column) as $column from {$this->table} where `id` = :id", [ ':id' => $id ] );
		return json_decode( $results, $assoc = true );
	}

	#======================================================================
	# Interfaces
	#======================================================================

	public function select( $request, $query_params = [] )
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

		$results = [];
		try {
			$results = $statement->fetchAll( \PDO::FETCH_ASSOC );
		} catch (PDOException $e) {
			if( DB_DEBUG_QUERY )
			{
				debug($e->getMessage());
				exit;
			} 
		}

		return $results;
	}

	public function select_raw( $request, $query_params = [] )
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

	public function select_row( $request, $query_params = [] )
	{
		$result = $this->select( $request, $query_params );
		return reset( $result );
	}

	public function select_one( $request, $query_params = [] )
	{
		$result = $this->select_row( $request, $query_params );
		return reset( $result );
	}

	#----------------------------------------------------------------------

	public function update( $id = false, $inputs = [] )
	{
		if( is_numeric($id) )
		{
			$update_inputs = [];
			$values = [];

			foreach( $inputs as $key => $value )
			{
				$update_inputs []= "`$key` = :$key";
				$values[":$key"] = $value;
			}

			$values[":id"] = $id;

			return $this->execute( "UPDATE `{$this->table}` SET " . implode(',',$update_inputs) . " WHERE `id` = :id" , $values );
		}
		return false;
	}

	public function delete( $id )
	{
		if( is_numeric( $id ) )
		{
			return $this->execute( "DELETE FROM `{$this->table}` WHERE `id` = :id" , [ ':id' => $id ] );
		}
		return false;
	}

	public function execute( $request, $query_params = [] )
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

	private function _exec_( $template, $query_params = [] )
	{
		$statement = $this->_query_( $template, $query_params );
		return $statement->rowCount();
	}

	private function _query_( $template, $query_params = [] )
	{
		$this->db = $this->connect();

		try {
			$statement = $this->db->prepare( $template );
		} catch (PDOException $e) {
			if( DB_DEBUG_QUERY )
			{
				debug($e->getMessage());
				exit;
			}
		}

		try {
			$statement->execute( $query_params );
		} catch (PDOException $e) {
			if( DB_DEBUG_QUERY )
			{
				debug($e->getMessage());
				exit;
			}
		}

		$this->last_statement = $statement;

		if( $statement->errorCode() != '00000' )
		{
			if( DB_DEBUG_QUERY )
			{
				$error_msg = $statement->errorInfo();
				echo('<h1>Query Error: '.$error_msg[0].'</h1>');
				echo('<h4>('.$error_msg[1].') '.$error_msg[2].'</h4>');
				echo('<h5>Statement</h4>');
				echo('<blockquote><code>'.nl2br($template).'</code></blockquote>');
				echo('<h5>Params</h5>');
				debug($query_params);
				exit;
			}
		}

		return $statement;
	}

}
