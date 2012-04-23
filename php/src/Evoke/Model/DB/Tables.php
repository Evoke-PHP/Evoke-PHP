<?php
namespace Evoke\Model\DB;
/// Get a list of tables from the database.
class Tables extends Base
{
	/** @property $extraTables
	 *  \array Extra tables to include in the data.
	 */
	protected $extraTables;

	/** @property $ignoredTables
	 *  \array Table to ignore in the data.
	 */
	protected $ignoredTables;

	public function __construct(Array $setup)
	{
		$setup += array('Extra_Tables'   => array(),
		                'Ignored_Tables' => array());
      
		parent::__construct($setup);

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
			$tableResults = $this->SQL->getAssoc('SHOW TABLES');
		}
		catch (\Exception $e)
		{
			$this->EventManager->notify(
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