<?php
namespace Evoke\Model\DB;
/// Provide a read only model to a table of data.
class Table extends Base
{
	/** @property $select
	 *  Settings \array for the selection of records.
	 */
	protected $select;

	/** @property $tableName
	 *  Table name \string
	 */
	protected $tableName;

	/** Create a model for a database table.
	 *  @param tableName  \string The database table that the model represents.
	 *  @param select     \array  Select statement settings.
	 *  @param dataPrefix \array  Prefix to the data
	public function __construct(/*s*/ $tableName,
	                            Array $select=array(),
	                            Array $dataPrefix=array())
	{
		
		$setup += array('Select'     => array(
			                'Fields'     => '*',
			                'Conditions' => '',
			                'Order'      => '',
			                'Limit'      => 0),
		                'Table_Name' => NULL);

		if (!is_string($tableName))
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires Table_Name as string');
		}
		
		parent::__construct($dataPrefix);

		$this->select    = $select;
		$this->tableName = $tableName;
	}

	/******************/
	/* Public Methods */
	/******************/

	public function getData(Array $selectSetup=array())
	{
		parent::getData();

		$selectSetup = array_merge($this->select, $selectSetup);
      
		$results = $this->sQL->select($this->tableName,
		                              $selectSetup['Fields'],
		                              $selectSetup['Conditions'],
		                              $selectSetup['Order'],
		                              $selectSetup['Limit']);

		return $this->offsetData($results);
	}
}
// EOF