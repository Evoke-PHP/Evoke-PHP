<?php
/**
 * Model Factory
 *
 * @package Model
 */
namespace Evoke\Model;

use DomainException,
	Evoke\Model\Data\Data,
	Evoke\Model\Data\RecordList,
	Evoke\Model\Mapper\DB\Joint,
	Evoke\Model\Mapper\DB\Table,
	Evoke\Model\Mapper\DB\Tables,
	Evoke\Model\Mapper\Session\Session as MapperSession,
	Evoke\Persistence\DB\SQLIface,
	Evoke\Persistence\DB\Table\Info,
	Evoke\Persistence\DB\Table\Joins,
	Evoke\Persistence\DB\Table\ListID,
	Evoke\Persistence\Session,
	Evoke\Persistence\SessionManager;

/**
 * Model Factory
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package Model
 */
class Factory implements FactoryIface
{
	/**
	 * SQL Object.
	 * @var Evoke\Persistence\DB\SQLIface
	 */
	protected $sql;

	/**
	 * Construct the model factory.
	 *
	 * @param SQLIface SQL object for DB based models.
	 */
	public function __construct(SQLIface $sql)
	{
		$this->sql = $sql;
	}

	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Build all of the data models using an associative array of table joins
	 * and an array of object types for the data models.  The associative array
	 * used in this method is shared with the buildMapperDBJoint method.  This
	 * method does not set the data.  A separate call must be made to set the
	 * data.
	 *
	 * @param string   The table name for the primary table.
	 * @param string[] Joins listed in a simple string format
	 *
	 * <pre><code>
	 *     array(Parent_Table =>
	 * 	             "(Data_Type.)?Parent_Field=Child_Table.Child_Field," .
	 * 	             "(Data_Type.)?Parent_Field=Child_Table.Child_Field," .
	 * 	             etc.),
	 * 	         Another_Parent_Table => etc.)
	 * </code></pre>
	 *
	 * @param string   The data type to create the data object as.
	 * @return Data
	 */
	public function buildData(/* String */ $tableName = '',
	                          Array        $dataJoins = array(),
	                          /* String */ $dataType  = 'Evoke\Model\Data\Data')
	{
		if (!isset($dataJoins[$tableName]))
		{
			return new $dataType;
		}

		$tableJoins = explode(',', $dataJoins[$tableName]);
 		$builtData = array();
		$pattern = '(^((?<Data_Type>[\w\\\]+)\.)?(?<Parent_Field>\w+)=' .
			'(?<Child_Table>\w+)\.(?<Child_Field>\w+)$)';
		
		foreach ($tableJoins as $index => $tableJoin)
		{
			if (!preg_match($pattern, $tableJoin, $matches))
			{
				throw new DomainException(
					__METHOD__ . var_export($tableJoin, true) .
					' join for table: ' . $tableName . ' at index: ' . $index .
					' is not valid.');
			}

			if (!empty($matches['Data_Type']))
			{
				$builtData[$matches['Parent_Field']] =
					$this->buildData($matches['Child_Table'],
					                 $dataJoins,
					                 $matches['Data_Type']);
			}
			else
			{
				$builtData[$matches['Parent_Field']] =
					$this->buildData($matches['Child_Table'], $dataJoins);
			}
		}

		return new $dataType(array(), $builtData);
	}
	
	/**
	 * Build a mapper that maps a menu from the DB.
	 *
	 * @param string The menu name.
	 *
	 * @return Joint
	 */
	public function buildMapperDBMenu(/* String */ $menuName)
	{
		return $this->buildMapperDBJoint(
			'Menu',                                                // Table Name
			array('Menu' => 'List_ID=Menu_List.Menu_ID'),          // Joins
			array('Conditions' => array('Menu.Name' => $menuName), // Select
			      'Fields'     => '*',
			      'Order'      => 'Menu_List_T_Lft ASC',
			      'Limit'      => 0));
	}

	/**
	 * Build a mapper that maps a joint set of data from the DB.
	 *
	 * @param string   The name of the primary table.
	 * @param string[] The joins for the data set.
	 * @param mixed[]  The select statement settings.
	 *
	 * @return Evoke\Model\Mapper\DB\Joint
	 */	 
	public function buildMapperDBJoint(/* String */ $tableName,
	                                   Array        $joins,
	                                   Array        $select =  array())
	{
		return new Joint($this->sql,
		                 $tableName,
		                 new Joins(
			                 new Info($this->sql, $tableName),
			                 $tableName,
			                 NULL,
			                 NULL,
			                 $this->buildJoins($joins, $tableName)),
		                 new ListID($this->sql),
		                 $select);
	}

	/**
	 * Build a mapper for a database table.
	 *
	 * @param string  The database table to map.
	 * @param mixed[] SQL select settings for the table.
	 *
	 * @return Table
	 */
	public function buildMapperDBTable(/* String */ $tableName,
	                                   Array        $select = array())
	{
		return new Table($this->sql, $tableName, $select);
	}
	
	/**
	 * Build a mapper for a database tables list.
	 *
	 * @param string[] Extra tables to list.
	 * @param string[] Tables to ignore.
	 *
	 * @return Tables
	 */
	public function buildMapperDBTables(Array $extraTables   = array(),
	                                    Array $ignoredTables = array())
	{
		return new Tables($this->sql, $extraTables, $ignoredTables);
	}

	/**
	 * Build a Session Mapper.
	 *
	 * @param string[] The session domain to map.
	 *
	 * @return MapperSession The session mapper.
	 */
	public function buildMapperSession(Array $domain)
	{
		return new MapperSession(new SessionManager(new Session, $domain));
	}

	/**
	 * Build record list data.
	 *
	 * @param string   Table Name.
	 * @param string[] Joins.
	 *
	 * @return RecordList The record list.
	 */
	public function buildRecordList(/* String */ $tableName,
	                                Array        $joins = array())
	{
		return new RecordList($this->buildData($tableName, $joins));
	}

	/*********************/
	/* Protected Methods */
	/*********************/

	/**
	 * Build all of the joins using an associative array of table joins.  The
	 * keys of the array represent the table names.  The value for each table
	 * is a string that specifies the joins from the table as a string using
	 * the following grammar:
	 * 
	 *     Parent_Field  <Parent field name for the Join>
	 *     Child_Field   <Child field name for the join>
	 *     Child_Table   <Table name for the child field that is being joint>
	 *     Join          Parent_Field=Child_Table.Child_Field
	 *     Table_Joins   Join(,Join)*
	 *
	 * @param mixed[] The joins to be built.
	 * @param string  The initial table name to start the joins from.
	 */
	protected function buildJoins(Array $joins, $tableName)
	{
		if (!isset($joins[$tableName]))
		{
			return array();
		}

		$tableJoins = explode(',', $joins[$tableName]);
		$builtJoins = array();
		$pattern =
			'(^(?<Parent_Field>\w+)=(?<Child_Table>\w+)\.(?<Child_Field>\w+)$)';
		
		foreach ($tableJoins as $index => $tableJoin)
		{
			if (!preg_match($pattern, $tableJoin, $matches))
			{
				throw new DomainException(
					__METHOD__ . var_export($tableJoin, true) .
					' join for table: ' . $tableName . ' at index: ' . $index .
					' is not valid.');
			}
			else
			{
				$builtJoins[] = new Joins(
					new Info($this->sql, $matches['Child_Table']),
					$matches['Child_Table'],
					$matches['Parent_Field'],
					$matches['Child_Field'],
					$this->buildJoins($joins, $matches['Child_Table']));
			}
		}

		return $builtJoins;
	}
}
// EOF