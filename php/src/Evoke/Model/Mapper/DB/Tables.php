<?php
namespace Evoke\Model\Mapper\DB;

use Evoke\Iface;

/// Get a list of tables from the database.
class Tables extends \Evoke\Model\Mapper\DB
{
	/** @property $extraTables
	 *  \array Extra tables to include in the data.
	 */
	protected $extraTables;

	/** @property $ignoredTables
	 *  \array Table to ignore in the data.
	 */
	protected $ignoredTables;

	/** Construct a model for a list of database tables.
	 *  @param sql           @object SQL object.
	 *  @param extraTables   @array  Extra tables to list.
	 *  @param ignoredTables @array  Tables to ignore for the list.
	 */
	public function __construct(Iface\DB\SQL $sql,
	                            Array        $extraTables   = array(),
	                            Array        $ignoredTables = array())
	{
		parent::__construct($sql);

		$this->extraTables   = $extraTables;
		$this->ignoredTables = $ignoredTables;
	}

	/******************/
	/* Public Methods */
	/******************/

	/** Fetch the specified tables if they are in the database or extra tables,
	 *  and are not being ignored.
	 *  @param params @array The conditions to match in the mapped data.  If no
	 *  parameters are passed then it is assumed that all matching tables should
	 *  be returned, otherwise the passed parameters are returned if they match.
	 */
	public function fetch(Array $params=array())
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