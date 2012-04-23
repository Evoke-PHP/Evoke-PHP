<?php
namespace Evoke\Model\DB;

use Evoke\Core\Iface;

/** Represent the data for a joint set of tables in a database. This provides
 *  read-only access to the data for the specified table and its related data
 *  obtained through the \ref Joins.
 */
class Joint extends Base
{
	/** @property $joins
	 *  Joins \object which lists the relationships for the data, allowing it to
	 *  be joint together into a meaningful unit of data.
	 */
	protected $joins;
	
	/** @property $select
	 *  Select \array for the SQL statement.
	 */
	protected $select;
	
	/** @property $tableName
	 * Table name for the data.
	 */
	protected $tableName;

	/** Construct a Model of a joint set of database tables.
	 *  @param tableName  \string The table name where joins start from.
	 *  @param Joins      \object Joins object.
	 *  @param select     \array  Select statement settings.
	 *  @param dataPrefix \array  Any prefix to offset the data with.
	 */
	public function __construct(/* String */         $tableName,
	                            Iface\DB\Table\Joins $joins,
	                            Array                $select=array(),
	                            Array                $dataPrefix=array())
	{
	   
		if (!is_string($tableName))
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires tableName as string');
		}
		
		parent::__construct($dataPrefix);

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

	/** Get the data for the model.
	 *  @param selectSetup \array Optional SQL select settings (default is all).
	 */
	public function getData(Array $selectSetup=array())
	{
		$selectSetup = array_merge($this->select, $selectSetup);
		$tables = $this->tableName . $this->joins->getJoinStatement();

		if ($selectSetup['Fields'] === '*')
		{
			$selectSetup['Fields'] = $this->joins->getAllFields();
		}

		$results = $this->sQL->select($tables,
		                              $selectSetup['Fields'],
		                              $selectSetup['Conditions'],
		                              $selectSetup['Order'],
		                              $selectSetup['Limit']);

		return array_merge(
			parent::getData(),
			$this->offsetData($this->joins->arrangeResults($results)));
	}
}
// EOF