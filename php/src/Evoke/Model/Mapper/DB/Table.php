<?php
namespace Evoke\Model\Mapper\DB;

use Evoke\Persistance\DB\SQLIface,
	InvalidArgumentException;

/**
 * Table
 *
 * Provide a read only model to a table of data.
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package Model
 */
class Table extends DB
{
	/** 
	 * Settings for the selection of records.
	 * @var mixed[]
	 */
	protected $select;

	/**
	 * Table name.
	 * @var string
	 */
	protected $tableName;

	/** Construct a mapper for a database table.
	 *
	 *  @param Evoke\Persistance\DB\SQLIface
	 *                 SQL object.
	 *  @param string  The database table that the model represents.
	 *  @param mixed[] Select statement settings.
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

	/**
	 * Fetch some data from the mapper (specified by params).
	 *
	 * @param mixed[] The conditions to match in the mapped data.
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