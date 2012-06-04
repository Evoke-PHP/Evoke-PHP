<?php
namespace Evoke\Model;

use DomainException,
	Evoke\Persistance\DB\SQLIface,
	Evoke\Service\ProviderIface;

class Factory implements FactoryIface
{
	/** @property $provider
	 *  @object Provider
	 */
	protected $provider;

	/** @property $sql
	 *  @object SQL
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

	/** Build all of the data models using an associative array of table joins
	 *  and an array of object types for the data models.  The associative array
	 *  used in this method is shared with the buildMapperDBJoint method.  This
	 *  method does not set the data.  A separate call must be made to set the
	 *  data.
	 *
	 *  @param dataJoins @array The keys of the array represent the table names.
	 *  The value for each table is a string that specifies the joins from the
	 *  table as a string using the following grammar:
	 *  @code
	 *  // <Parent_Field>  Parent field name for the Join.
	 *  // <Child_Field>   Child field name for the join.
	 *  // <Child_Table>   Table name for the child field that is being joint.
	 *  <Join>        <Parent_Field>=<Child_Table>.<Child_Field>
	 *  <Table_Joins> <Join>(,<Join>)*
	 *  @endcode
	 *
	 *  @param premadeObjects @array A list of the non-standard data objects by
	 *  their table name.
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

	public function buildInfo(Array $params)
	{
		if (!isset($params['Sql']))
		{
			$params['Sql'] = $this->sql;
		}

		return $this->provider->make(
			'Evoke\Persistance\DB\Table\Info', $params);
	}
	
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

	public function buildMapperDBJoint(Array $params)
	{
		$params += array('Sql' => $this->sql);

		// Build the joins.
		if (!empty($params['Joins']) && isset($params['Table_Name']))
		{
			$params['Joins'] = $this->provider->make(
				'Evoke\Persistance\DB\Table\Joins',
				array('Info'       => $this->buildInfo(
					      array('Table_Name' => $params['Table_Name'])),
				      'Joins'      => $this->buildJoins(
					      $params['Joins'], $params['Table_Name']),
				      'Table_Name' => $params['Table_Name']));
		}

		return $this->provider->make('Evoke\Model\Mapper\DB\Joint', $params);
	}

	/** Build a mapper for a database table.
	 *  @param tableName @string The database table to map.
	 *  @param select    @array  SQL select settings for the table.
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

	/** Build an administrative mapper for a database table.
	 *  @param tableName @string The database table to map.
	 *  @param select    @array  SQL select settings for the table.
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
	
	/** Build a mapper for a database tables list.
	 *  @param extraTables   @array Extra tables to list.
	 *  @param ignoredTables @array Tables to ignore.
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

	/** Build all of the joins using an associative array of table joins.  The
	 *  keys of the array represent the table names.  The value for each table
	 *  is a string that specifies the joins from the table as a string using
	 *  the following grammar:
	 *  \code
	 *  // <Parent_Field>  Parent field name for the Join.
	 *  // <Child_Field>   Child field name for the join.
	 *  // <Child_Table>   Table name for the child field that is being joint.
	 *  <Join>        <Parent_Field>=<Child_Table>.<Child_Field>
	 *  <Table_Joins> <Join>(,<Join>)*
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
					'Evoke\Persistance\DB\Table\Joins',
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