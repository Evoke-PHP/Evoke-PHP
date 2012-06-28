<?php
namespace Evoke\Model;

use DomainException,
	Evoke\Persistence\DB\SQLIface,
	Evoke\Service\ProviderIface;

/**
 * Factory
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package Model
 */
class Factory implements FactoryIface
{
	/**
	 * Provider for dependency injection.
	 * @var Evoke\Service\ProviderIface
	 */
	protected $provider;

	/**
	 * SQL Object.
	 * @var Evoke\Persistence\DB\SQLIface
	 */
	protected $sql;
	
	public function __construct(ProviderIface $provider,
	                            SQLIface      $sql)
	{
		$this->provider = $provider;
		$this->sql      = $sql;
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
	                          Array        $premadeObjects = array())
	{
		if (isset($premadeObjects[$tableName]))
		{
			return $premadeObjects[$tableName];
		}
		elseif (!isset($dataJoins[$tableName]))
		{
			return $this->provider->make('Evoke\Model\Data');
		}

		$tableJoins = explode(',', $dataJoins[$tableName]);
		$builtData = array();

		foreach ($tableJoins as $index => $tableJoin)
		{
			if (!preg_match('(^(\w+)=(\w+)\.(\w+)$)', $tableJoin, $matches))
			{
				throw new DomainException(
					__METHOD__ . var_export($tableJoin, true) .
					' join for table: ' . $tableName . ' at index: ' . $index .
					' is not valid.');
			}
			else
			{
				// Build the data model for the child table (match 2) from the
				// joint field match 1.
				$builtData[$matches[1]] =
					$this->buildData($matches[2], $dataJoins, $premadeObjects);
			}
		}

		return $this->provider->make('Evoke\Model\Data',
		                             array('Data_Joins' => $builtData));
	}

	/**
	 * Build a table info object.
	 *
	 * @param mixed[] Parameters for the table info.
	 *
	 * @return Evoke\Persistence\DB\Table\Info
	 */
	public function buildInfo(Array $params)
	{
		if (!isset($params['Sql']))
		{
			$params['Sql'] = $this->sql;
		}

		return $this->provider->make(
			'Evoke\Persistence\DB\Table\Info', $params);
	}

	/**
	 * Build a mapper that maps a menu from the DB.
	 *
	 * @param string The menu name.
	 *
	 * @return Evoke\Model\Mapper\DB\Joint
	 */
	public function buildMapperDBMenu(/* String */ $menuName)
	{
		return $this->buildMapperDBJoint(
			array('Joins'      => array('Menu' => 'List_ID=Menu_List.Menu_ID'),
			      'Select'     => array(
				      'Conditions' => array('Menu.Name' => $menuName),
				      'Fields'     => '*',
				      'Order'      => 'Menu_List_T_Lft ASC',
				      'Limit'      => 0),
			      'Table_Name' => 'Menu'));
	}

	/**
	 * Build a mapper that maps a joint set of data from the DB.
	 *
	 * @param mixed[] The parameters for the Joint Mapper construction, with any
	 *                joins built using the simple buildJoins method.
	 *
	 * @return Evoke\Model\Mapper\DB\Joint
	 */	 
	public function buildMapperDBJoint(Array $params)
	{
		$params += array('Sql' => $this->sql);

		// Build the joins.
		if (!empty($params['Joins']) && isset($params['Table_Name']))
		{
			$params['Joins'] = $this->provider->make(
				'Evoke\Persistence\DB\Table\Joins',
				array('Info'       => $this->buildInfo(
					      array('Table_Name' => $params['Table_Name'])),
				      'Joins'      => $this->buildJoins(
					      $params['Joins'], $params['Table_Name']),
				      'Table_Name' => $params['Table_Name']));
		}

		return $this->provider->make('Evoke\Model\Mapper\DB\Joint', $params);
	}

	/**
	 * Build a mapper for a database table.
	 *
	 * @param string  The database table to map.
	 * @param mixed[] SQL select settings for the table.
	 *
	 * @return Evoke\Model\Mapper\DB\Table
	 */
	public function buildMapperDBTable(/* String */ $tableName,
	                                   Array        $select = array())
	{
		return $this->provider->make(
			'Evoke\Model\Mapper\DB\Table',
			array('Select'     => $select,
			      'Sql'        => $this->sql,
			      'Table_Name' => $tableName));
	}

	/**
	 * Build an administrative mapper for a database table.
	 *
	 * @param string  The database table to map.
	 * @param mixed[] SQL select settings for the table.
	 *
	 * @return Evoke\Model\Mapper\DB\TableAdmin
	 */
	public function buildMapperDBTableAdmin(/* String */ $tableName,
	                                        Array        $select = array())
	{
		return $this->provider->make(
			'Evoke\Model\Mapper\DB\TableAdmin',
			array('Select'     => $select,
			      'Sql'        => $this->sql,
			      'Table_Name' => $tableName));
	}
	
	/**
	 * Build a mapper for a database tables list.
	 *
	 * @param string[] Extra tables to list.
	 * @param string[] Tables to ignore.
	 *
	 * @return Evoke\Model\Mapper\DB\Tables
	 */
	public function buildMapperDBTables(Array $extraTables   = array(),
	                                    Array $ignoredTables = array())
	{
		return $this->provider->make(
			'Evoke\Model\Mapper\DB\Tables',
			array('Extra_Tables'   => $extraTables,
			      'Ignored_Tables' => $ignoredTables,
			      'Sql'            => $this->sql));
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
	 *     // <Parent_Field> Parent field name for the Join.
	 *     // <Child_Field>  Child field name for the join.
	 *     // <Child_Table>  Table name for the child field that is being joint.
	 *     <Join>            <Parent_Field>=<Child_Table>.<Child_Field>
	 *     <Table_Joins>     <Join>(,<Join>)*
	 */
	protected function buildJoins(Array $joins, $tableName)
	{
		if (!isset($joins[$tableName]))
		{
			return array();
		}

		$tableJoins = explode(',', $joins[$tableName]);
		$builtJoins = array();

		foreach ($tableJoins as $index => $tableJoin)
		{
			if (!preg_match('(^(\w+)=(\w+)\.(\w+)$)', $tableJoin, $matches))
			{
				throw new DomainException(
					__METHOD__ . var_export($tableJoin, true) .
					' join for table: ' . $tableName . ' at index: ' . $index .
					' is not valid.');
			}
			else
			{
				$childTable = $matches[2];
				$builtJoins[] = $this->provider->make(
					'Evoke\Persistence\DB\Table\Joins',
					array('Child_Field'  => $matches[3],
					      'Info'         => $this->buildInfo(
						      array('Table_Name' => $childTable)),
					      'Joins'        => $this->buildJoins(
						      $joins, $childTable),
					      'Parent_Field' => $matches[1],
					      'Table_Name'   => $childTable));
			}
		}

		return $builtJoins;
	}
}
// EOF