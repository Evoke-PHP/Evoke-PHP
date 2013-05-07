<?php
namespace Evoke\Model;

/**
 * FactoryIface
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package Model
 */
interface FactoryIface
{
	/**
	 * Create all of the data models using an associative array of table joins
	 * and an array of object types for the data models.  The associative array
	 * used in this method is shared with the createMapperDBJoint method.  This
	 * method does not set the data.  A separate call must be made to set the
	 * data.
	 *
	 * @param string   The table name for the primary table.
	 * @param string[] Joins listed in a simple string format:
	 *
	 *     Key:   <Table Name for parent field>
	 *     Value: [<Parent_Field>=<Child_Table>.<Child_Field>,]*
	 *
	 * This is basically a comma separated list of joins for each table.
	 * (No comma is required at the very end of this list.)
	 *
	 * @return mixed Evoke\Model\Data\Data or the premade object passed in.
	 */
	public function createData(/* String */ $tableName      = '',
	                           Array        $dataJoins      = array());

	/**
	 * Create a mapper that maps a menu from the DB.
	 *
	 * @param string The menu name.
	 *
	 * @return Evoke\Model\Mapper\DB\Joint
	 */
	public function createMapperDBMenu(/* String */ $menuName);

	/**
	 * Create a mapper that maps a joint set of data from the DB.
	 *
	 * @param string   The name of the primary table.
	 * @param string[] The joins for the data set.
	 * @param mixed[]  The select statement settings.
	 *
	 * @return Evoke\Model\Mapper\DB\Joint
	 */	 
	public function createMapperDBJoint(/* String */ $tableName,
	                                    Array        $joins,
	                                    Array        $select =  array());

	/**
	 * Create a mapper for a database table.
	 *
	 * @param string  The database table to map.
	 * @param mixed[] SQL select settings for the table.
	 *
	 * @return Evoke\Model\Mapper\DB\Table
	 */
	public function createMapperDBTable(/* String */ $tableName,
	                                    Array        $select = array());
	
	/**
	 * Create a mapper for a database tables list.
	 *
	 * @param string[] Extra tables to list.
	 * @param string[] Tables to ignore.
	 *
	 * @return Evoke\Model\Mapper\DB\Tables
	 */
	public function createMapperDBTables(Array $extraTables   = array(),
	                                     Array $ignoredTables = array());

	/**
	 * Create a Session Mapper.
	 *
	 * @param string[] The session domain to map.
	 *
	 * @return Evoke\Model\Mapper\Session The session mapper.
	 */
	public function createMapperSession(Array $domain);
	
	/**
	 * Create record list data.
	 *
	 * @param string   Table Name.
	 * @param string[] Joins.
	 *
	 * @return Evoke\Model\Data\RecordList The record list.
	 */
	public function createRecordList(/* String */ $tableName,
	                                 Array        $joins = array());	
}
// EOF