<?php
namespace Evoke\Persistence\DB;

/**
 * SQLIface
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package Persistence
 */
interface SQLIface extends DBIface
{
	/**
	 * Add a column to a table.
	 *
	 * @param string The table to add the column to.
	 * @param string The column name to add.
	 * @param string The data type of the column to add.
	 *
	 * @return bool Whether the add column was successful.
	 */	
	public function addColumn($table, $column, $fieldType);

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
	public function changeColumn($table, $oldCol, $newCol, $fieldType);

	/**
	 * Simple SQL DELETE statement wrapper.
	 *
	 * @param mixed Tables to delete from.
	 * @param mixed Conditions (see class description).
	 *
	 * @return int The number of rows affected by the delete.
	 */	
	public function delete($tables, $conditions);

	/**
	 * Drop a column from the table.
	 *
	 * @param string The table to drop the column from.
	 * @param string The column name to drop.
	 *
	 * @return int The number of records modified.
	 */	
	public function dropColumn($table, $column);

	/**
	 * Get an associative array of results for a query.
	 *
	 * @param string Query string.
	 *
	 * @return mixed[] Associative array of results from the query.
	 */
	public function getAssoc($queryString, Array $params=array());
	
	/**
	 * Get a result set which must contain exactly one row and return it.
	 *
	 * @param string  The query to get exactly one row.
	 * @param mixed[] The parmeters for the sql query.
	 *
	 * @return mixed[] The result as an associative array.
	 */
	public function getSingleRow($queryString, Array $params=array());

	/**
	 * Get a single value result from an sql statement.
	 *
	 * @param string  The query string to get exactly one row.
	 * @param mixed[] The parmeters for the sql query.
	 * @param int     The column of the row to return the value for.
	 *
	 * @return mixed The result value.
	 */
	public function getSingleValue(
		$queryString, Array $params=array(), $column=0);

	/**
	 * Simple SQL INSERT statement wrapper.
	 *
	 * @param string Table to insert into.
	 * @param mixed Fields to insert.
	 * @param mixed[] An array specifying one or more record to insert.
	 */	
	public function insert($table, $fields, $valArr);

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
	public function select($tables, $fields, $conditions='', $order='',
	                       $limit=0, $distinct=false);

	/**
	 * A simple way to select a single value result.
	 *
	 * @param string  Table to get a single result from.
	 * @param string  Field for the result.
	 * @param mixed[] Conditions for the WHERE.
	 *
	 * @return mixed The result value.
	 */
	public function selectSingleValue($table, $field, $conditions);

	/**
	 * Simple SQL UPDATE statement wrapper.
	 *
	 * @param mixed Tables to update.
	 * @param mixed Keyed array of set values.
	 * @param mixed Conditions (see class description).
	 * @param int   Number of records - defaults to unlimited.
	 */
	public function update($tables, $setValues, $conditions='', $limit=0);	
}
// EOF