<?php
namespace Evoke\Model\DB;

/** Represent the data for a joint set of tables in a database. This provides
 *  read-only access to the data for the specified table and its related data
 *  obtained through the \ref Joins.
 */
class Joint extends Base
{
	/** @property $Joins
	 *  Joins \object which lists the relationships for the data, allowing it to
	 *  be joint together into a meaningful unit of data.
	 */
	protected $Joins;
	
	/** @property $select
	 *  Select \array for the SQL statement.
	 */
	protected $select;
	
	/** @property $tableName
	 * Table name for the data.
	 */
	protected $tableName;

	public function __construct(Array $setup)
	{
		$setup += array('Joins'      => NULL,
		                'Select'     => array('Conditions' => '',
		                                      'Fields'     => '*',
		                                      'Order'      => '',
		                                      'Limit'      => 0),
		                'Table_Name' => NULL);
		
		if (!$setup['Joins'] instanceof \Evoke\Core\DB\Table\Joins)
		{
			throw new \InvalidArgumentException(__METHOD__ . ' requires Joins');
		}

		if (!isset($setup['Table_Name']))
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires Table_Name');
		}
		
		parent::__construct($setup);

		$this->Joins     = $setup['Joins'];
		$this->select    = $setup['Select'];
		$this->tableName = $setup['Table_Name'];
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
		$tables = $this->tableName . $this->Joins->getJoinStatement();

		if ($selectSetup['Fields'] === '*')
		{
			$selectSetup['Fields'] = $this->Joins->getAllFields();
		}

		$results = $this->SQL->select($tables,
		                              $selectSetup['Fields'],
		                              $selectSetup['Conditions'],
		                              $selectSetup['Order'],
		                              $selectSetup['Limit']);

		return array_merge(
			parent::getData(),
			$this->offsetData($this->Joins->arrangeResults($results)));
	}
}
// EOF