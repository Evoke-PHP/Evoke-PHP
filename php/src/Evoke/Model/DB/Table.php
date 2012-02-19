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
	
	public function __construct($setup=array())
	{
		$setup += array('Select'     => array(
			                'Fields'     => '*',
			                'Conditions' => '',
			                'Order'      => '',
			                'Limit'      => 0),
		                'Table_Name' => NULL);

		if (!is_string($setup['Table_Name']))
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires Table_Name as string');
		}
		
		parent::__construct($setup);

		$this->select    = $setup['Select'];
		$this->tableName = $setup['Table_Name'];
	}

	/******************/
	/* Public Methods */
	/******************/

	public function getData(Array $selectSetup=array())
	{
		parent::getData();

		$selectSetup = array_merge($this->select, $selectSetup);
      
		$results = $this->SQL->select($this->tableName,
		                              $selectSetup['Fields'],
		                              $selectSetup['Conditions'],
		                              $selectSetup['Order'],
		                              $selectSetup['Limit']);

		return $this->offsetData($results);
	}
}
// EOF