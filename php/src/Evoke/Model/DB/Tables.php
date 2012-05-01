<?php
namespace Evoke\Model\DB;

use Evoke\Iface;

/// Get a list of tables from the database.
class Tables extends \Evoke\Model\DB
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
	 *  @param dataPrefix    @array  Data prefix to offset the data to.
	 */
	public function __construct(Iface\DB\SQL $sql,
	                            Array        $extraTables   = array(),
	                            Array        $ignoredTables = array(),
	                            Array        $dataPrefix    = array())
	{
		parent::__construct($sql, $dataPrefix=array());

		$this->extraTables   = $extraTables;
		$this->ignoredTables = $ignoredTables;
	}

	/******************/
	/* Public Methods */
	/******************/
      
	/** Get the list of tables in the database.
	 *  \returns \bool False for failure or an \array of tables.
	 */
	public function getData()
	{
		$tables = array();

		try
		{
			$tableResults = $this->sql->getAssoc('SHOW TABLES');
		}
		catch (\Exception $e)
		{
			$this->eventManager->notify(
				'Log',
				array('Level'   => LOG_ERR,
				      'Method'  => __METHOD__,
				      'Message' => 'Unable to get tables in database due to ' .
				      'exception: ' . $e->getMessage()));

			return array();
		}
      
		foreach ($tableResults as $result)
		{
			foreach($result as $tableName)
			{
				$tables[$tableName] = $tableName;
			}
		}

		$tables = array_merge($tables, $this->extraTables);
		$tables = array_diff($tables, $this->ignoredTables);
		ksort($tables);

		return $this->offsetData($tables);
	}
}
// EOF