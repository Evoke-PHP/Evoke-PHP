<?php
namespace Evoke\Model\Mapper\DB;

use Evoke\Iface;

/// Provide a read only model to a table of data.
class Table extends \Evoke\Model\Mapper\DB
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
	 */
	public function __construct(
		Iface\DB\SQL $sql,
		/* String */ $tableName,
		Array        $select     = array('Fields'     => '*',
		                                 'Conditions' => '',
		                                 'Order'      => '',
		                                 'Limit'      => 0))
	{
		if (!is_string($tableName))
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires tableName as string');
		}
		
		parent::__construct($sql);

		$this->select    = $select;
		$this->tableName = $tableName;
	}

	/******************/
	/* Public Methods */
	/******************/

	public function fetch(Array $selectSetup=array())
	{
		$selectSetup = array_merge($this->select, $selectSetup);
      
		return $this->sql->select($this->tableName,
		                          $selectSetup['Fields'],
		                          $selectSetup['Conditions'],
		                          $selectSetup['Order'],
		                          $selectSetup['Limit']);
	}
}
// EOF