<?php
namespace Evoke\Model\DB;

use namespace Evoke\Iface;

/// Provide a read only model to a table of data.
class Table extends Base
{
	/** @property $select
	 *  @array Settings for the selection of records.
	 */
	protected $select;

	/** @property $tableName
	 *  @string Table name.
	 */
	protected $tableName;

	/** Create a model for a database table.
	 *  @param sql        @object SQL.
	 *  @param tableName  @string The database table that the model represents.
	 *  @param select     @array  Select statement settings.
	 *  @param dataPrefix @array  Prefix to the data
	 */
	public function __construct(
		Iface\DB\SQL $sql,
		/* String */ $tableName,
		Array        $select     = array('Fields'     => '*',
		                                 'Conditions' => '',
		                                 'Order'      => '',
		                                 'Limit'      => 0),
		Array        $dataPrefix = array())
	{
		if (!is_string($tableName))
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires tableName as string');
		}
		
		parent::__construct($sql, $dataPrefix);

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
      
		$results = $this->sql->select($this->tableName,
		                              $selectSetup['Fields'],
		                              $selectSetup['Conditions'],
		                              $selectSetup['Order'],
		                              $selectSetup['Limit']);

		return $this->offsetData($results);
	}
}
// EOF