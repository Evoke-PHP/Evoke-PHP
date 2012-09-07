<?php
namespace Evoke\Model;

/**
 * @todo Add the interface.
 */

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
	 * Build all of the data models using an associative array of table joins
	 * and an array of object types for the data models.  The associative array
	 * used in this method is shared with the buildMapperDBJoint method.  This
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
	 * @param Evoke\Model\Data\DataIface[]
	 *                 Array non-standard data objects by their table name.
	 *
	 * @return mixed Evoke\Model\Data\Data or the premade object passed in.
	 */
	public function buildData(/* String */ $tableName      = '',
	                          Array        $dataJoins      = array(),
	                          Array        $premadeObjects = array());

	/**
	 * Build a table info object.
	 *
	 * @param mixed[] Parameters for the table info.
	 *
	 * @return Evoke\Persistence\DB\Table\Info
	 */
	public function buildInfo(Array $params);

	/**
	 * Build a mapper that maps a menu from the DB.
	 *
	 * @param string The menu name.
	 *
	 * @return Evoke\Model\Mapper\DB\Joint
	 */
	public function buildMapperDBMenu(/* String */ $menuName);

	/**
	 * Build a mapper that maps a joint set of data from the DB.
	 *
	 * @param mixed[] The parameters for the Joint Mapper construction, with any
	 *                joins built using the simple buildJoins method.
	 *
	 * @return Evoke\Model\Mapper\DB\Joint
	 */	 
	public function buildMapperDBJoint(Array $params);

	/**
	 * Build a mapper for a database table.
	 *
	 * @param string  The database table to map.
	 * @param mixed[] SQL select settings for the table.
	 *
	 * @return Evoke\Model\Mapper\DB\Table
	 */
	public function buildMapperDBTable(/* String */ $tableName,
	                                   Array        $select = array());

	/**
	 * Build an administrative mapper for a database table.
	 *
	 * @param string  The database table to map.
	 * @param mixed[] SQL select settings for the table.
	 *
	 * @return Evoke\Model\Mapper\DB\TableAdmin
	 */
	public function buildMapperDBTableAdmin(/* String */ $tableName,
	                                        Array        $select = array());
	
	/**
	 * Build a mapper for a database tables list.
	 *
	 * @param string[] Extra tables to list.
	 * @param string[] Tables to ignore.
	 *
	 * @return Evoke\Model\Mapper\DB\Tables
	 */
	public function buildMapperDBTables(Array $extraTables   = array(),
	                                    Array $ignoredTables = array());
}
// EOF