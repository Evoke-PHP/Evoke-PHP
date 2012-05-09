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
			array('Joins'      => array(
				      array('Child_Field'  => 'Menu_ID',
				            'Parent_Field' => 'List_ID',
				            'Table_Name'   => 'Menu_List')),
			      'Select'     => array(
				      'Conditions' => array('Menu.Name' => $menuName),
				      'Fields'     => '*',
				      'Order'      => 'Menu_List_T_Lft ASC',
				      'Limit'      => 0),
			      'Table_Name' => 'Menu'));
	}

	public function buildMapperDBJoint(Array $params)
	{
		if (!isset($params['Sql']))
		{
			$params['Sql'] = $this->sql;
		}

		$params['Joins'] = $this->buildJoins($params);

		return $this->provider->make('Evoke\Model\Mapper\DB\Joint', $params);
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
	
	protected function buildJoins(Array $params)
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
	
	protected function buildMapper($name, $params)
	{
		
	}
}
// EOF