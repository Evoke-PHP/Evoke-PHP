<?php
namespace Evoke\Model\Mapper\DB;

use Evoke\Persistence\DB\SQLIface;

/**
 * Tables Mapper
 *
 * Get a list of tables from the database.
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package Model
 */
class Tables extends DB
{
	/**
	 * Extra tables to include in the data.
	 * @var mixed[]
	 */
	protected $extraTables;

	/**
	 * Tables to ignore in the data.
	 * @var mixed[]
	 */
	protected $ignoredTables;

	/**
	 * Construct a model for a list of database tables.
	 *
	 * @param Evoke\Persistence\DB\SQLIface
	 *                SQL object.
	 * @param mixed[] Extra tables to list.
	 * @param mixed[] Tables to ignore for the list.
	 */
	public function __construct(SQLIface $sql,
	                            Array    $extraTables   = array(),
	                            Array    $ignoredTables = array())
	{
		parent::__construct($sql);

		$this->extraTables   = $extraTables;
		$this->ignoredTables = $ignoredTables;
	}

	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Fetch the specified tables if they are in the database or extra tables,
	 * and are not being ignored.
	 *
	 * @param mixed[] The conditions to match in the mapped data.  If no
	 *                parameters are passed then it is assumed that all matching
	 *                tables should be returned, otherwise a table must be
	 *                within the passed parameters to match.
	 */
	public function fetch(Array $params = array())
	{
		$tableResults = $this->sql->getAssoc('SHOW TABLES');
		$allTables = array();

		foreach ($tableResults as $result)
		{
			foreach ($result as $table)
			{
				$allTables[] = $table;
			}
		}

		$tables =
			empty($params) ? $allTables : array_intersect($params, $allTables);
		$tables = array_merge($tables, $this->extraTables);
		$tables = array_diff($tables, $this->ignoredTables);
		ksort($tables);	

		return $tables;
	}
}
// EOF