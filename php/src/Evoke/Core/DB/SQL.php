<?php
namespace Evoke\Core\DB;

use Evoke\Iface\Core as ICore;
use Evoke\Core\Exception\Base as Exception_Base;
use Evoke\Core\Exception\DB as Exception_DB;

/** The SQL class provides an implementation of the SQL interface which extends
 *  the DB interface in Evoke.
 *
 * Provides simple pass-through wrappers for the DB interface functions.
 *
 * Provides wrappers to help the writing of the following SQL statements:
 *   SELECT, UPDATE, DELETE, INSERT
 *
 *   Conditions for WHERE statements are either in string format or as a keyed
 *   array which is AND'ed together and the comparison operator '=' is used.
 *      Example: array(ID => 1, Name => '`Peter`') becomes ID=1 AND Name=`Peter`
 *   A condition passed in as a string is unchanged and can be used for more
 *   complex comparison operations.
 */
class SQL implements \Evoke\ICore\DB
{
	/** @property $db
	 *  @object Database object.
	 */
	protected $db;

	/** @property $inTransaction
	 *  @bool Whether we are currently in a transaction or not.
	 */
	protected $inTransaction = false;

	/** Construct an SQL object.
	 *  @param db @object DB object to perform the SQL on.
	 */
	public function __construct(ICore\DB $db)
	{
		$this->db = $db;
	}
   
	/*****************************************/
	/* Public Methods - Transaction Handling */
	/*****************************************/

	/// Begin a transaction or raise an exception if we are already in one.
	public function beginTransaction()
	{
		if ($this->inTransaction)
		{
			throw new Exception_DB(__METHOD__, 'Already in a transaction.');
		}
		else
		{
			$this->inTransaction = true;
			return $this->db->beginTransaction();
		}
	}

	/// Commit the current transaction.
	public function commit()
	{
		if ($this->inTransaction)
		{
			$this->inTransaction = false;
			return $this->db->commit();
		}
		else
		{
			throw new Exception_DB(__METHOD__, 'Not in a transaction.');
		}
	}

	/// Return whether we are in a trasaction.
	public function inTransaction()
	{
		return $this->inTransaction;
	}
   
	/// Rolls back the current transaction.
	public function rollBack()
	{
		if ($this->inTransaction)
		{
			$this->inTransaction = false;
			return $this->db->rollBack();
		}
		else
		{
			throw new Exception_DB(__METHOD__, 'Not in a transaction.');
		}
	}
   
	/*************************************************/
	/* Public Methods - Simple Pass-Through Wrappers */
	/*************************************************/

	/// Return the SQLSTATE.
	public function errorCode()
	{
		return $this->db->errorCode();
	}

	/// Get the extended error information associated with the last DB operation.
	public function errorInfo()
	{
		return $this->db->errorInfo();
	}

	/// Execute an SQL statement and return the number of rows affected.
	public function exec($statement)
	{
		return $this->db->exec($statement);
	}

	/// Get a database connection attribute.
	public function getAttribute($attribute)
	{
		return $this->db->getAttribute($attribute);
	}

	/// Get an array of available PDO drivers.
	public function getAvailableDrivers()
	{
		return $this->db->getAvailableDrivers();
	}
   
	/// Get the ID of the last inserted row or sequence value.
	public function lastInsertId($name=NULL)
	{
		return $this->db->lastInsertId($name);
	}

	/// Prepares a statement for execution and returns a statement object.
	public function prepare($statement, $driverOptions=array())
	{
		try
		{
			$namedPlaceholders = (strpos($statement, ':') !== false);
	 
			$this->setAttribute(
				\PDO::ATTR_STATEMENT_CLASS,
				array('\Evoke\Core\DB\PDOStatement', array($namedPlaceholders)));
	 
			return $this->db->prepare($statement, $driverOptions);
		}
		catch (\Exception $e)
		{
			throw new Exception_DB(__METHOD__, '', $this->db, $e);
		}
	}

	/** Executes an SQL statement, returns a result set as a PDOStatement object.
	 *  Any supplied object should be filled as per the fetch options.
	 *  @param queryString \string The query string.
	 */
	public function query($queryString, $fetchMode=0, $into=NULL)
	{
		$namedPlaceholders = (strpos($queryString, ':') !== false);

		$this->setAttribute(
			\PDO::ATTR_STATEMENT_CLASS,
			array('\Evoke\Core\DB\PDOStatement', array($namedPlaceholders)));

		if ($fetchMode === 0)
		{
			return $this->db->query($queryString);
		}
		else
		{
			return $this->db->query($queryString, $fetchMode, $into);
		}
	}  

	/// Quotes the input string (if required) and escapes special characters.
	public function quote($string, $parameterType=\PDO::PARAM_STR)
	{
		return $this->db->quote($string, $parameterType);
	}
   
	/// Sets an attribute on the database
	public function setAttribute($attribute, $value)
	{
		return $this->db->setAttribute($attribute, $value);
	}
   
	/*****************************/
	/* Public Methods - Wrappers */
	/*****************************/
   
	/** Get an associative array of results for a query.
	 *  @param queryString \string Query string.
	 *  \return \array Associative array of results from the query.
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
		catch (\Exception $e)
		{
			throw new Exception_DB(
				__METHOD__,
				'Exception Raised for query: ' . var_export($queryString, true) .
				' params: ' . var_export($params, true),
				$this->db,
				$e);
		}
	}

	/** Get a result set which must contain exactly one row and return it.
	 *  @param queryString \string The query to get exactly one row.
	 *  @param params \array The parmeters for the sql query.
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
				throw new \Exception('Unexpected Multiple rows received.');
			}
	 
			return $result;
		}
		catch (Exception $e)
		{
			throw new Exception_DB(
				__METHOD__,
				'Exception Raised for query: ' . var_export($queryString, true) .
				' params: ' . var_export($params, true),
				$this->db,
				$e);
		}
	}

	/** Get a single value result from an sql statement.
	 *  @param queryString \string The query string to get exactly one row.
	 *  @param params \array The parmeters for the sql query.
	 *  @param column \int The column of the row to return the value for.
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
				throw new \Exception('Unexpected multiple rows received.');
			}
	 
			return $result;
		}
		catch (Exception $e)
		{
			throw new Exception_DB(
				__METHOD__,
				'Exception Raised for query: ' . var_export($queryString, true) .
				' params: ' . var_export($params, true),
				$this->db,
				$e);
		}
	}
   
	/** Simple SQL SELECT statement wrapper.
	 *  @param tables \mixed Tables to select from.
	 *  @param fields \mixed Fields to select.
	 *  @param conditions \mixed Conditions (see class description).
	 *  @param order \mixed ORDER BY directives.
	 *  @param limit \int Number of records - defaults to unlimited.
	 *  @param distinct \bool Return only distinct records.
	 *  \returns An associative \array containing the data returned by the query.
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
		catch(\Exception $e)
		{
			throw new Exception_DB(
				__METHOD__,
				'Tables: ' . var_export($tables, true) .
				' Fields: ' .var_export($fields, true) .
				' Conditions: ' . var_export($conditions, true),
				$this->db, $e);
		}	 
	}

	/** Get a single value result from an sql select statement.
	 *  @param table \string The table to get the value from.
	 *  @param field \string The parmeters for the sql query.
	 *  @param conditions \mixed The conditions for the WHERE.
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
		catch(\Exception $e)
		{
			throw new Exception_DB(
				__METHOD__,
				'Table: ' . var_export($table, true) .
				' Field: ' .var_export($field, true) .
				' Conditions: ' . var_export($conditions, true),
				$this->db, $e);
		}
	}

	/** Simple SQL UPDATE statement wrapper.
	 *  @param tables \mixed Tables to update.
	 *  @param setValues \mixed Keyed array of set values.
	 *  @param conditions \mixed Conditions (see class description).
	 *  @param limit \int Number of records - defaults to unlimited.
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
		catch (\Exception $e)
		{
			throw new Exception_DB(__METHOD__, 'Prepare', $this->db, $e);
		}

		$params = array_merge(array_values($setValues),
		                      array_values($conditions));

		// Execute
		if ($statement->execute($params) === false)
		{
			throw new Exception_DB(__METHOD__, 'Execute', $statement);
		}
      
		return true;
	}

	/** Simple SQL DELETE statement wrapper.
	 *  @param tables \mixed Tables to delete from.
	 *  @param conditions \mixed Conditions (see class description).
	 *  \return The number of rows affected by the delete.
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
		catch (\Exception $e)
		{
			throw new Exception_DB(
				__METHOD__ . ' query: ' . var_export($q, true) .
				' conditions: ' . var_export($conditions, true),
				$this->db,
				$e);
		}
	}

	/** Simple SQL INSERT statement wrapper.
	 *  @param table \string Table to insert into.
	 *  @param fields \mixed Fields to insert.
	 *  @param valArr \array An array specifying one or more record to insert.
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
		catch (\Exception $e)
		{
			$msg = 'Prepare Table: ' . var_export($table, true) . ' Fields: ' .
				var_export($fields, true);
	 
			throw new Exception_DB(
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
			catch (\Exception $e)
			{
				throw new Exception_DB(
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
			catch (\Exception $e)
			{
				throw new Exception_DB(
					__METHOD__,
					'Single Value: ' . var_export($valArr, true),
					$this->db,
					$e);
			}
		}
	}
   
	/** Add a column to a table.
	 *  @param table \string The table to add the column to.
	 *  @param column \string The column name to add.
	 *  @param fieldType \string The data type of the column to add.
	 *  \return \bool Whether the add column was successful.
	 */
	public function addColumn($table, $column, $fieldType)
	{
		$q = 'ALTER TABLE ' . $table . ' ADD COLUMN `' . $column . '` ' .
			$fieldType;
		return $this->exec($q);
	}

	/** Drop a column from the table.
	 *  @param table \string The table to drop the column from.
	 *  @param column \string The column name to drop.
	 *  \return \int The number of records modified.
	 */
	public function dropColumn($table, $column)
	{
		$q = 'ALTER TABLE ' . $table . ' DROP COLUMN `' . $column . '`';
		return $this->exec($q);
	}

	/** Change a column in the table.
	 *  @param table \string The table for the column change.
	 *  @param oldCol \string The column name to change.
	 *  @param newCol \string The field name to set the column to.
	 *  @param fieldType \string The type of field to create.
	 *  \return \int The number of records modified.
	 */
	public function changeColumn($table, $oldCol, $newCol, $fieldType)
	{
		$q = 'ALTER TABLE ' . $table . ' CHANGE COLUMN `' . $oldCol . '` `' .
			$newCol . '` ' . $fieldType;
		return $this->exec($q);      
	}
   
	/*******************/
	/* Private Methods */
	/*******************/

	/** Expand an array using the separator given.
	 *  @param arg \mixed The array to separate or a string to return.
	 *  @param separator \string The separator to use between each element
	 *  of the array.
	 *  \returns A \string of the separated array or the string arg.
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
		catch (\Exception $e)
		{
			throw new Exception_Base(
				__METHOD__,
				'arg: ' . var_export($arg, true) .
				' separator: ' . var_export($separator, true),
				$e);
		}
	}

	/** Expand a keyed array using the between value between the key and the
	 *  value and the separator between each element pair.
	 *  @param arg \mixed Either a keyed array that is to be expanded or the
	 *  value to be converted to a string.
	 *  @param between \string The separator to use between each key and value.
	 *  @param separator \string The separator to use between each key/value
	 *  pair.
	 *  \returns A \string of the separated keyed array or the string for arg.
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
		catch (\Exception $e)
		{
			throw new Exception_Base(
				__METHOD__,
				'arg: ' . var_export($arg, true) . ' separator: ' .
				var_export($separator, true) . ' between: ' .
				var_export($between, true),
				$e);
		}
	}

	/** Create a string with unnamed placeholders for each item specified.
	 *  @param arg \mixed Either an array where every item is replaced or a
	 *  single placeholder for an object or string entry. An empty string will
	 *  be returned for an empty array.
	 *  @param separator \string The separator to place between each placeholder.
	 *  \return A \string of the placeholders correctly separated.
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

	/** Create a string with the array keys and unnamed placeholders. The string
	 *  will be of the format: 'key1=? AND key2=? AND key3=?' with default
	 *  parameters.
	 *  @param arg \mixed Either a keyed array that is to be expanded or the
	 *  value to be converted to a string.
	 *  @param between \string String between each key and unnamed placeholder.
	 *  @param separator \string String between each key/placeholder pair.
	 *  \returns A \string with the keys and placeholders in it.
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
		catch (\Exception $e)
		{
			throw new Exception_Base(
				__METHOD__,
				'arg: ' . var_export($arg, true) .
				' separator: ' . var_export($separator, true) .
				' between: ' . var_export($between, true),
				$e);
		}
	}
}
// EOF