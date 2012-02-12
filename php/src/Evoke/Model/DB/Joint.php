<?php
namespace Evoke\Model\DB;

/** Represent the data for a joint set of tables in a database. This provides
 *  read-only access to the data for the specified table and its related data
 *  obtained through the \ref Joins.
 */
class Joint extends Base
{
	/** @property $tableName
	 * Table name for the data.
	 */
	protected $tableName;

	/** @property $Joins
	 *  Joins \object which lists the relationships for the data, allowing it to
	 *  be joint together into a meaningful unit of data.
	 */
	protected $Joins;
	
	public function __construct(Array $setup)
	{
		$setup += array('Joins'      => NULL,
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

		$this->tableName = $setup['Table_Name'];
		$this->Joins = $setup['Joins'];
	}

	/******************/
	/* Public Methods */
	/******************/

	/** Get the data for the model.
	 *  @param getSetup \array Optional SQL select settings (default is all).
	 */
	public function getData(Array $getSetup=array())
	{
		$getSetup = array_merge(array('Conditions' => '',
		                              'Fields'     => '*',
		                              'Order'      => '',
		                              'Limit'      => 0),
		                        $getSetup);

		$tables = $this->tableName . $this->Joins->getJoins();

		if ($getSetup['Fields'] === '*')
		{
			$getSetup['Fields'] = $this->Joins->getAllFields();
		}

		$results = $this->SQL->select($tables,
		                              $getSetup['Fields'],
		                              $getSetup['Conditions'],
		                              $getSetup['Order'],
		                              $getSetup['Limit']);

		return array_merge(
			parent::getData(),
			$this->offsetData($this->Joins->arrangeResults($results)));
	}
}
// EOF