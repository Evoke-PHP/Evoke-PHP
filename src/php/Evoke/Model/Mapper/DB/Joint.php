<?php
namespace Evoke\Model\Mapper\DB;

use Evoke\Persistence\DB\SQLIface,
	Evoke\Persistence\DB\Table\JoinsIface,
	InvalidArgumentException;

/**
 * Joint
 *
 * Represent the data for a joint set of tables in a database. This provides
 * read-only access to the data for the specified table and its related data
 * obtained through the SQL interface using the Joins.
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package Model
 */
class Joint extends DB
{
	/**
	 * The joins provide the relationships of the data, allowing it to be joint
	 * together into a meaningful unit of data.
	 *
	 * @var Evoke\Persistence\DB\Table\JoinsIface
	 */
	protected $joins;
	
	/**
	 * Select for the SQL statement.
	 * @var mixed[]
	 */
	protected $select;
	
	/**
	 * Table name for the data.
	 * @var string
	 */
	protected $tableName;

	/**
	 * Construct a Mapper for of a joint set of database tables.
	 *
	 * @param Evoke\Persistence\DB\SQLIface
	 *                SQL object.
	 * @param string  The table name where joins start from.
	 * @param Evoke\Persistence\DB\Table\JoinsIface
	 *                Joins object.
	 * @param mixed[] Select statement settings.
	 */
	public function __construct(SQLIface     $sql, 
	                            /* String */ $tableName,
	                            JoinsIface   $joins,
	                            Array        $select = array())
	{
	   
		if (!is_string($tableName))
		{
			throw new InvalidArgumentException(
				__METHOD__ . ' requires tableName as string');
		}
		
		parent::__construct($sql);

		$select += array('Conditions' => '',
		                 'Fields'     => '*',
		                 'Order'      => '',
		                 'Limit'      => 0);
	
		$this->joins     = $joins;
		$this->select    = $select;
		$this->tableName = $tableName;
	}

	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Fetch some data from the mapper (specified by params).
	 *
	 * @param mixed[] The conditions to match in the mapped data.
	 */
	public function fetch(Array $params = array())
	{
		$params += $this->select;
		$tables = $this->tableName . $this->joins->getJoinStatement();

		if ($params['Fields'] === '*')
		{
			$params['Fields'] = $this->joins->getAllFields();
		}

		return $this->joins->arrangeResults(
			$this->sql->select($tables,
			                   $params['Fields'],
			                   $params['Conditions'],
			                   $params['Order'],
			                   $params['Limit']));
	}
}
// EOF