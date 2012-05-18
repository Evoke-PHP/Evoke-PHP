<?php
namespace Evoke\Model;

use Evoke\Iface;

class Factory implements Iface\Model\Factory
{
	/** @property $db
	 *  @object DB
	 */
	protected $db;

	/** @property $provider
	 *  @object Provider
	 */
	protected $provider;

	/** @property $sql
	 *  @object SQL
	 */
	protected $sql;
	
	public function __construct(Iface\DB       $database,
	                            Iface\Provider $provider)
	{
		$this->db       = $database;
		$this->provider = $provider;
		$this->sql      = $provider->make(
			'Evoke\DB\SQL',	array('Database' => $database));
	}

	/******************/
	/* Public Methods */
	/******************/

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
				'Evoke\DB\Table\Joins',
				array('Info'       => $this->buildInfo(
					      array('Table_Name' => $params['Table_Name'])),
				      'Joins'      => $this->buildJoins(
					      $params['Joins'], $params['Table_Name']),
				      'Table_Name' => $params['Table_Name']));
				
		}

		return $this->provider->make('Evoke\Model\Mapper\DB\Joint', $params);
	}

	public function buildMapperDBTable(Array $params)
	{
		$params += array('Sql' => $this->sql);

		return $this->provider->make('Evoke\Model\Mapper\DB\Table', $params);
	}
	
	/*********************/
	/* Protected Methods */
	/*********************/

	protected function buildInfo(Array $params)
	{
		if (!isset($params['Sql']))
		{
			$params['Sql'] = $this->sql;
		}

		return $this->provider->make('Evoke\DB\Table\Info', $params);
	}

	/** Build a complex tree of joins.  This method provides full configuration
	 *  of the table joins.  The simpler: \ref buildJoinsSimple can be used in
	 *  most cases to build the table joins.
	 */
	protected function buildJoinsComplex(Array $params)
	{
		if (!empty($params['Joins']))
		{
			foreach ($params['Joins'] as &$join)
			{
				$join = $this->buildjoins($join);
			}
		}

		if (!isset($params['Info']))
		{
			$params['Info'] = $this->buildInfo(
				array('Table_Name' => $params['Table_Name']));
		}
		
		return $this->provider->make('Evoke\DB\Table\Joins', $params);
	}

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
				throw new \DomainException(
					__METHOD__ . var_export($tableJoin, true) .
					' join for table: ' . $tableName . ' at index: ' . $index .
					' is not valid.');
			}
			else
			{
				$childTable = $matches[2];
				$builtJoins[] = $this->provider->make(
					'Evoke\DB\Table\Joins',
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
	
	protected function buildMapper($name, $params)
	{
		
	}
}
// EOF