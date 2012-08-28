<?php
/**
 * Table Mapper (Read Only)
 *
 * @package Model
 */
namespace Evoke\Model\Mapper\DB;

use Evoke\Model\Mapper\ReadIface,
	Evoke\Persistence\DB\SQLIface,
	InvalidArgumentException;

/**
 * Table Mapper (Read Only)
 *
 * Provide a read only model to a table of data.
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package Model
 */
class TableRead implements ReadIface
{
	/** 
	 * Settings for the selection of records.
	 * @var mixed[]
	 */
	protected $select;

	/** 
	 * SQL Object
	 * @var Evoke\Persistence\DB\SQLIface
	 */
	protected $sql;

	/**
	 * Table name.
	 * @var string
	 */
	protected $tableName;

	/** Construct a mapper for a database table.
	 *
	 *  @param Evoke\Persistence\DB\SQLIface
	 *                 SQL object.
	 *  @param string  The database table that the model represents.
	 *  @param mixed[] Select statement settings.
	 */
	public function __construct(SQLIface     $sql,
	                            /* String */ $tableName,
	                            Array        $select = array())
	{
		if (!is_string($tableName))
		{
			throw new InvalidArgumentException('needs tableName as string');
		}

		$this->select    = array_merge($select,
		                               array('Fields'     => '*',
		                                     'Conditions' => '',
		                                     'Order'      => '',
		                                     'Limit'      => 0));
		$this->sql       = $sql;
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
	public function read(Array $params = array())
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