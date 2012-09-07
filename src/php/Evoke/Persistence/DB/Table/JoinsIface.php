<?php
namespace Evoke\Persistence\DB\Table;

/**
 * JoinsIface
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package Persistence
 */
interface JoinsIface
{
	/**
	 * Arrange a set of results for the database that match the Join tree.
	 *
	 * @param mixed[] The results from the database.
	 * @param mixed[] The data already processed from the results.
	 *
	 * @return mixed[] The data that was arranged from the results.
	 */
	public function arrangeResults(Array $results, Array $data=array());
   
	/**
	 * Get a list of all fields fully named by their table alias.
	 */
	public function getAllFields();

	/**
	 * Get the fields that should be left for auto filling.
	 */
	public function getAutoFields();
   
	/**
	 * Get the child field.
	 */
	public function getChildField();
   
	/**
	 * Get the compare type.
	 */
	public function getCompareType();

	/**
	 * Get an empty joint data record.
	 */
	public function getEmpty();

	/**
	 * Return any failures from validation of data.
	 */
	public function getFailures();
	
	/**
	 * Get the joins.
	 */
	public function getJoins();
      
	/**
	 * Get the join statement for the tables.
	 */
	public function getJoinStatement();

	/**
	 * Get the join type.
	 */
	public function getJoinType();
   
	/**
	 * Get the Joint_Key.
	 */
	public function getJointKey();

	/**
	 * Get the parent field.
	 */
	public function getParentField();

	/**
	 * Get the primary keys for the table (not all primary keys for all
	 * referenced tables).
	 */
	public function getPrimaryKeys();
   
	/**
	 * Get the table name that has possibly been aliassed.
	 */
	public function getTableAlias();

	/**
	 * Get the table name.
	 */
	public function getTableName();

	/**
	 * Get the table separator.
	 */
	public function getTableSeparator();
   
	/**
	 * Whether the table reference is administratively managed.
	 */
	public function isAdminManaged();
   
	/**
	 * Return whether the table has an alias that is set.
	 */
	public function isTableAliassed();
}