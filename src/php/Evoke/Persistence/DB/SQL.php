<?php
namespace Evoke\Persistence\DB;

use Evoke\Message\Exception\DB as ExceptionDB,
	Exception;

/**
 * SQL
 *
 * The SQL class provides an implementation of the SQL interface which extends
 * the DB interface in Evoke.
 *
 * Provides simple pass-through wrappers for the DB interface functions.
 *
 * Provides wrappers to help the writing of the following SQL statements:
 *     SELECT, UPDATE, DELETE, INSERT
 *
 * Conditions for WHERE statements are either in string format or as a keyed
 * array which is AND'ed together and the comparison operator '=' is used.
 *     Example: array(ID => 1, Name => '`Peter`') becomes ID=1 AND Name=`Peter`
 * A condition passed in as a string is unchanged and can be used for more
 * complex comparison operations.
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package Persistence
 */
class SQL implements SQLIface
{
	/**
	 * Database object.
	 * @var Evoke\Persistence\DB\DBIface
	 */
	protected $db;

	/**
	 * Whether we are currently in a transaction or not.
	 * @var bool
	 */
	protected $inTransaction = false;

	/**
	 * The classname to use for special PDOStatement processing.
	 * @var string
	 */
	protected $statementClass;
	
	/**
	 * Construct an SQL object.
	 *
	 * @param Evoke\Persistence\DB\DBIface
	 *        Database object to perform the SQL on.
	 * @param string The PDOStatement class to use.
	 */
	public function __construct(
		DBIface      $database,
		/* String */ $statementClass = 'Evoke\Persistence\DB\PDOStatement')
	{
		$this->db             = $database;
		$this->statementClass = $statementClass;
	}
   
	/*****************************/
	/* Public Methods - DB Iface */
	/*****************************/

	/**
	 * Begin a transaction in the database.
	 *
	 * @throw Evoke\Message\Exception\DB If we are already in a transaction.
	 */
	public function beginTransaction()
	{
		if ($this->inTransaction)
		{
			throw new ExceptionDB(__METHOD__, 'Already in a transaction.');
		}
		else
		{
			$this->inTransaction = true;
			return $this->db->beginTransaction();
		}
	}

	/**
	 * Commit the current transaction to the database.
	 */
	public function commit()
	{
		if ($this->inTransaction)
		{
			$this->inTransaction = false;
			return $this->db->commit();
		}
		else
		{
			throw new ExceptionDB(__METHOD__, 'Not in a transaction.');
		}
	}

	/**
	 * Get the error code from the database.
	 */
	public function errorCode()
	{
		return $this->db->errorCode();
	}

	/**
	 * Get the error information associated with the last DB operation.
	 */
	public function errorInfo()
	{
		return $this->db->errorInfo();
	}

	/**
	 * Execute an SQL statement.
	 *
	 * @return int Number of rows affected.
	 */
	public function exec($statement)
	{
		return $this->db->exec($statement);
	}

	/**
	 * Get a database attribute.
	 *
	 * @return mixed The attribute.
	 */
	public function getAttribute($attribute)
	{
		return $this->db->getAttribute($attribute);
	}

	/**
	 * Get an array of available PDO drivers.
	 *
	 * @return mixed[]
	 */
	public function getAvailableDrivers()
	{
		return $this->db->getAvailableDrivers();
	}

	/**
	 * Whether we are in a transaction.
	 *
	 * @return bool Whether we are in a transaction.
	 */
	public function inTransaction()
	{
		return $this->inTransaction;
	}
   
	/**
	 * Get the ID of the last inserted row or sequence value.
	 *
	 * @param string|null The name of the sequence object.
	 *
	 * @return string
	 */
	public function lastInsertId($name=NULL)
	{
		return $this->db->lastInsertId($name);
	}

	/**
	 * Prepares a statement for execution and returns a statement object.
	 *
	 * @return mixed Return the PDO statement object.
	 */
	public function prepare($statement, $driverOptions=array())
	{
		try
		{
			$namedPlaceholders = (strpos($statement, ':') !== false);
	 
			$this->setAttribute(
				\PDO::ATTR_STATEMENT_CLASS,
				array($this->statementClass, array($namedPlaceholders)));
	 
			return $this->db->prepare($statement, $driverOptions);
		}
		catch (Exception $e)
		{
			throw new ExceptionDB(__METHOD__, '', $this->db, $e);
		}
	}

	/**
	 * Executes an SQL statement, returns a result set as a PDOStatement object.
	 * Any supplied object should be filled as per the fetch options.
	 *
	 * @param string     The query string.
	 * @param int        The fetch mode.
	 * @param mixed|null What to fetch the query into.
	 */
	public function query($queryString, $fetchMode=0, $into=NULL)
	{
		$namedPlaceholders = (strpos($queryString, ':') !== false);

		$this->setAttribute(
			\PDO::ATTR_STATEMENT_CLASS,
			array($this->statement, array($namedPlaceholders)));

		if ($fetchMode === 0)
		{
			return $this->db->query($queryString);
		}
		else
		{
			return $this->db->query($queryString, $fetchMode, $into);
		}
	}  

	/**
	 * Quotes the input string (if required) and escapes special characters.
	 *
	 * @param string The string to quote.
	 * @param int    The type to quote it as.
	 */
	public function quote($string, $parameterType=\PDO::PARAM_STR)
	{
		return $this->db->quote($string, $parameterType);
	}
   
	/**
	 * Rolls back the current transaction avoiding any change to the database.
	 */
	public function rollBack()
	{
		if ($this->inTransaction)
		{
			$this->inTransaction = false;
			return $this->db->rollBack();
		}
		else
		{
			throw new ExceptionDB(__METHOD__, 'Not in a transaction.');
		}
	}
	
	/**
	 * Set an attribute on the database
	 *
	 * @param int   The attribute to set.
	 * @param mixed The value to set it to.
	 */
	public function setAttribute($attribute, $value)
	{
		return $this->db->setAttribute($attribute, $value);
	}
   
	/*****************************/
	/* Public Methods - SQLIface */
	/*****************************/
   
	/**
	 * Add a column to a table.
	 *
	 * @param string The table to add the column to.
	 * @param string The column name to add.
	 * @param string The data type of the column to add.
	 *
	 * @return bool Whether the add column was successful.
	 */	
	public function addColumn($table, $column, $fieldType)
	{
		$q = 'ALTER TABLE ' . $table . ' ADD COLUMN `' . $column . '` ' .
			$fieldType;
		return $this->exec($q);
	}

	/**
	 * Change a column in the table.
	 *
	 * @param string The table for the column change.
	 * @param string The column name to change.
	 * @param string The field name to set the column to.
	 * @param string The type of field to create.
	 *
	 * @return \int The number of records modified.
	 */	
	public function changeColumn($table, $oldCol, $newCol, $fieldType)
	{
		$q = 'ALTER TABLE ' . $table . ' CHANGE COLUMN `' . $oldCol . '` `' .
			$newCol . '` ' . $fieldType;
		return $this->exec($q);      
	}

	/**
	 * Simple SQL DELETE statement wrapper.
	 *
	 * @param mixed Tables to delete from.
	 * @param mixed Conditions (see class description).
	 *
	 * @return int The number of rows affected by the delete.
	 */	
	public function delete($tables, $conditions)
	{
		$q = 'DELETE FROM ' . $this->expand($tables) . ' WHERE ';

		foreach ($conditions as $field => $value)
		{
			if (!isset($value))
			{
				$q .= $field . ' IS NULL AND ';
				unset($conditions[$field]);
			}
			else
			{
				$q .= $field . '=? AND ';
			}
		}

		$q = rtrim($q, 'AND ');
      
		try
		{
			$statement = $this->prepare($q);
			$statement->execute($conditions);

			return $statement->rowCount();
		}
		catch (Exception $e)
		{
			throw new ExceptionDB(
				__METHOD__ . ' query: ' . var_export($q, true) .
				' conditions: ' . var_export($conditions, true),
				$this->db,
				$e);
		}
	}
	
	/**
	 * Drop a column from the table.
	 *
	 * @param string The table to drop the column from.
	 * @param string The column name to drop.
	 *
	 * @return int The number of records modified.
	 */	
	public function dropColumn($table, $column)
	{
		$q = 'ALTER TABLE ' . $table . ' DROP COLUMN `' . $column . '`';
		return $this->exec($q);
	}

	/**
	 * Simple SQL INSERT statement wrapper.
	 *
	 * @param string Table to insert into.
	 * @param mixed Fields to insert.
	 * @param mixed[] An array specifying one or more record to insert.
	 */	
	public function insert($table, $fields, $valArr)
	{
		// Prepare
		try
		{
			$statement = $this->prepare(
				'INSERT INTO ' . $table . ' (' . $this->expand($fields) . ') ' .
				'VALUES (' . $this->placeholders($fields) . ')');
		}
		catch (Exception $e)
		{
			$msg = 'Prepare Table: ' . var_export($table, true) . ' Fields: ' .
				var_export($fields, true);
	 
			throw new ExceptionDB(
				__METHOD__, $msg, $this->db, $e);
		}

		if (!is_array($valArr))
		{
			$valArr = array($valArr);
		}
      
		// If the first entry in the values array is an array then we have
		// multiple records that we should be inserting.
		if (is_array(reset($valArr)))
		{
			try
			{
				foreach ($valArr as $entryNum => $entry)
				{
					$statement->execute($entry);
				}
			}
			catch (Exception $e)
			{
				throw new ExceptionDB(
					__METHOD__,
					'Multiple Values: ' . var_export($valArr, true),
					$this->db,
					$e);
			}
		}
		else // There should only be one entry to insert.
		{
			try
			{
				$statement->execute($valArr);
			}
			catch (Exception $e)
			{
				throw new ExceptionDB(
					__METHOD__,
					'Single Value: ' . var_export($valArr, true),
					$this->db,
					$e);
			}
		}
	}
   
	/**
	 * Get an associative array of results for a query.
	 *
	 * @param string Query string.
	 *
	 * @return mixed[] Associative array of results from the query.
	 */
	public function getAssoc($queryString, $params=array())
	{
		try
		{
			$statement = $this->prepare($queryString);
			$params = is_array($params) ? $params : array($params);
			$statement->execute($params);

			return $statement->fetchAll(\PDO::FETCH_ASSOC);
		}
		catch (Exception $e)
		{
			throw new ExceptionDB(
				__METHOD__,
				'Exception Raised for query: ' . var_export($queryString, true) .
				' params: ' . var_export($params, true),
				$this->db,
				$e);
		}
	}

	/**
	 * Get a result set which must contain exactly one row and return it.
	 *
	 * @param string  The query to get exactly one row.
	 * @param mixed[] The parmeters for the sql query.
	 *
	 * @return mixed[] The result as an associative array.
	 */
	public function getSingleRow($queryString, $params=array())
	{
		// Prepare
		try
		{
			$statement = $this->prepare($queryString);
			$params = is_array($params) ? $params : array($params);
			$statement->execute($params);
			$result = $statement->fetch(\PDO::FETCH_ASSOC);

			// Check if there is more than a single row.
			if ($statement->fetch(\PDO::FETCH_ASSOC))
			{
				throw new Exception('Unexpected Multiple rows received.');
			}
	 
			return $result;
		}
		catch (Exception $e)
		{
			throw new ExceptionDB(
				__METHOD__,
				'Exception Raised for query: ' . var_export($queryString, true) .
				' params: ' . var_export($params, true),
				$this->db,
				$e);
		}
	}

	/**
	 * Get a single value result from an sql statement.
	 *
	 * @param string  The query string to get exactly one row.
	 * @param mixed[] The parmeters for the sql query.
	 * @param int     The column of the row to return the value for.
	 *
	 * @return mixed The result value.
	 */
	public function getSingleValue($queryString, $params=array(), $column=0)
	{
		// Prepare
		try
		{
			$statement = $this->prepare($queryString);
			$params = is_array($params) ? $params : array($params);
			$statement->execute($params);
			$result = $statement->fetchColumn($column);

			// Check if there is more than a single row.
			if ($statement->fetchColumn($column))
			{
				throw new Exception('Unexpected multiple rows received.');
			}
	 
			return $result;
		}
		catch (Exception $e)
		{
			throw new ExceptionDB(
				__METHOD__,
				'Exception Raised for query: ' . var_export($queryString, true) .
				' params: ' . var_export($params, true),
				$this->db,
				$e);
		}
	}
   
	/**
	 * Simple SQL SELECT statement wrapper.
	 *
	 * @param mixed Tables to select from.
	 * @param mixed Fields to select.
	 * @param mixed Conditions (see class description).
	 * @param mixed ORDER BY directives.
	 * @param int   Number of records - defaults to unlimited.
	 * @param bool  Whether to return only distinct records.
	 *
	 * @return mixed[] The data returned by the query.
	 */	
	public function select($tables, $fields, $conditions='', $order='', $limit=0,
	                       $distinct=false)
	{
		try
		{
			// SELECT fields FROM tables WHERE conditions ORDER BY order LIMIT lim
			$q  = 'SELECT ';
	 
			if ($distinct)
			{
				$q .= 'DISTINCT ';
			}
	 
			$q .= $this->expand($fields) . ' FROM ' . $this->expand($tables);
	 
			if (!empty($conditions))
			{
				$q .= ' WHERE ' . $this->placeholdersKeyed($conditions);
			}
	 
			if (!empty($order))
			{
				$q .= ' ORDER BY ' . $this->placeholdersKeyed($order, ' ', ',');
			}
	 
			if (!empty($limit) && $limit !== 0)
			{
				$q .= ' LIMIT ' . $limit;
			}

			// Prepare
			$statement = $this->prepare($q);
	 
			if (!is_array($conditions))
			{
				$conditions = array();
			}
	 
			if (!is_array($order))
			{
				$order = array();
			}
	 
			$params = array_merge($conditions, $order);
	 
			// Execute and fetch the results as an associated array.
			$statement->execute($params);
			$assoc = $statement->fetchAll(\PDO::FETCH_ASSOC);

			return $assoc;
		}
		catch(Exception $e)
		{
			throw new ExceptionDB(
				__METHOD__,
				'Tables: ' . var_export($tables, true) .
				' Fields: ' .var_export($fields, true) .
				' Conditions: ' . var_export($conditions, true),
				$this->db, $e);
		}	 
	}

	/**
	 * Get a single value result from an sql statement.
	 *
	 * @param string  The query string to get exactly one row.
	 * @param mixed[] The parmeters for the sql query.
	 * @param int     The column of the row to return the value for.
	 *
	 * @return mixed The result value.
	 */
	public function selectSingleValue($table, $field, $conditions)
	{
		try
		{
			$q  = 'SELECT ' . $field . ' FROM ' . $table;
	 
			if (!empty($conditions))
			{
				$q .= ' WHERE ' . $this->placeholdersKeyed($conditions);
			}
	 
			$statement = $this->prepare($q);
			$statement->execute($conditions);

			return $statement->fetchColumn();
		}
		catch(Exception $e)
		{
			throw new ExceptionDB(
				__METHOD__,
				'Table: ' . var_export($table, true) .
				' Field: ' .var_export($field, true) .
				' Conditions: ' . var_export($conditions, true),
				$this->db, $e);
		}
	}

	/**
	 * Simple SQL UPDATE statement wrapper.
	 *
	 * @param mixed Tables to update.
	 * @param mixed Keyed array of set values.
	 * @param mixed Conditions (see class description).
	 * @param int   Number of records - defaults to unlimited.
	 */
	public function update($tables, $setValues, $conditions='', $limit=0)
	{
		$q  = 'UPDATE ' . $this->expand($tables);
		$q .= ' SET ' . $this->placeholdersKeyed($setValues, '=', ',');

		if (!empty($conditions))
		{
			$q .= ' WHERE ' . $this->placeholdersKeyed($conditions, '=', ' AND ');
		}
      
		if (!empty($limit) && $limit !== 0)
		{
			$q .= ' LIMIT ' . $limit;
		}

		// Prepare
		try
		{
			$statement = $this->prepare($q);
		}
		catch (Exception $e)
		{
			throw new ExceptionDB(__METHOD__, 'Prepare', $this->db, $e);
		}

		$params = array_merge(array_values($setValues),
		                      array_values($conditions));

		// Execute
		if ($statement->execute($params) === false)
		{
			throw new ExceptionDB(__METHOD__, 'Execute', $statement);
		}
	}

	/*******************/
	/* Private Methods */
	/*******************/

	/**
	 * Expand an array using the separator given.
	 *
	 * @param mixed The array to separate or a string to return.
	 * @param string The separator to use between each element of the array.
	 *
	 * @returns string The separated array or the string arg.
	 */
	private function expand($arg, $separator=',')
	{
		try
		{
			if (is_array($arg))
			{
				return implode($separator, $arg);
			}
			else
			{
				return (string)$arg;
			}
		}
		catch (Exception $e)
		{
			throw new Exception(
				__METHOD__,
				'arg: ' . var_export($arg, true) .
				' separator: ' . var_export($separator, true),
				$e);
		}
	}

	/**
	 * Expand a keyed array using the between value between the key and the
	 * value and the separator between each element pair.
	 *
	 * @param mixed  Either a keyed array that is to be expanded or the value to
	 *               be converted to a string.
	 * @param string The separator to use between each key and value.
	 * @param string The separator to use between each key/value pair.
	 *
	 * @return string The separated keyed array or the string for arg.
	 */
	private function expandKeyedArr($arg, $between='=', $separator=' AND ')
	{
		try
		{
			if (is_array($arg))
			{
				$str = '';
	    
				if (!empty($arg))
				{
					foreach ($arg as $key => $val)
					{
						$str .= $key . $between . $val . $separator;
					}
	       
					// The array is not empty so we can cut the last separator which
					// has definitely been added to str.
					$str = substr($str, 0, -1 * strlen((string)$separator));
				}
	 
				return $str;
			}
			else
			{
				return (string)$arg;
			}      
		}
		catch (Exception $e)
		{
			throw new Exception(
				__METHOD__,
				'arg: ' . var_export($arg, true) . ' separator: ' .
				var_export($separator, true) . ' between: ' .
				var_export($between, true),
				$e);
		}
	}

	/**
	 * Create a string with unnamed placeholders for each item specified.
	 *
	 * @param mixed Either an array where every item is replaced or a single
	 *              placeholder for an object or string entry. An empty string
	 *              will be returned for an empty array.
	 * @param string The separator to place between each placeholder.
	 *
	 * @return string The placeholders correctly separated.
	 */
	private function placeholders($arg, $separator=',')
	{
		if (!is_array($arg))
		{
			return '?';
		}
      
		$str = '';
      
		if (!empty($arg))
		{
			foreach ($arg as $item)
			{
				$str .= '?' . $separator;
			}
	 
			// The array is not empty so we can cut the last separator which has
			// definitely been added to str.
			$str = substr($str, 0, -1 * strlen($separator));
		}
      
		return $str;
	}

	/**
	 * Create a string with the array keys and unnamed placeholders. The string
	 * will be of the format: 'key1=? AND key2=? AND key3=?' with default
	 * parameters.
	 *
	 * @param mixed  Either a keyed array that is to be expanded or the value to
	 *               be converted to a string.
	 * @param string The value to use between each key and unnamed placeholder.
	 * @param string The value between each key/placeholder pair.
	 *
	 * @returns A \string with the keys and placeholders in it.
	 */
	private function placeholdersKeyed(
		$arg, $between='=', $separator=' AND ')
	{
		/** \todo Fix for NULL placeholders.  So where conditions can accept NULL
		 *  values.
		 */
		try
		{
			if (is_array($arg))
			{
				$str = '';
	    
				if (!empty($arg))
				{
					foreach ($arg as $key => $val)
					{
						$str .= $key . $between . '?' . $separator;
					}
	       
					// The array is not empty so we can cut the last separator which
					// has definitely been added to str.
					$str = substr($str, 0, -1 * strlen((string)$separator));
				}
	 
				return $str;
			}
			else
			{
				return (string)$arg;
			}      
		}
		catch (Exception $e)
		{
			throw new Exception(
				__METHOD__,
				'arg: ' . var_export($arg, true) .
				' separator: ' . var_export($separator, true) .
				' between: ' . var_export($between, true),
				$e);
		}
	}
}
// EOF