<?php
namespace Evoke\Model\Mapper\DB;

use Evoke\Persistance\DB\SQLIface,
	InvalidArgumentException;

/// Provide a read only model to a table of data.
class Table extends DB
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
		SQLIface     $sql,
		/* String */ $tableName,
		Array        $select = array())
	{
		if (!is_string($tableName))
		{
			throw new InvalidArgumentException(
				__METHOD__ . ' requires tableName as string');
		}
		
		parent::__construct($sql);

		$this->select    = array_merge($select,
		                               array('Fields'     => '*',
		                                     'Conditions' => '',
		                                     'Order'      => '',
		                                     'Limit'      => 0));
		$this->tableName = $tableName;
	}

	/******************/
	/* Public Methods */
	/******************/

	/** Fetch some data from the mapper (specified by params).
	 *  @param params @array The conditions to match in the mapped data.
	 */
	public function fetch(Array $params = array())
	{
		$params = array_merge($this->select, $params);

		return $this->sql->select($this->tableName,
		                          $params['Fields'],
		                          $params['Conditions'],
		                          $params['Order'],
		                          $params['Limit']);
	}
}
// EOF