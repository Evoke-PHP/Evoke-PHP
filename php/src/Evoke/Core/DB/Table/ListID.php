<?php
namespace Evoke\Core\DB\Table;

class List_ID
{
	private $fields;
	private $tableName;

	protected $SQL;
   
	public function __construct($setup=array())
	{
		$setup += array('Fields'     => array('Counter'  => 'Counter',
		                                      'DB_Table' => 'DB_Table',
		                                      'DB_Field' => 'DB_Field'),
		                'SQL'        => NULL,
		                'Table_Name' => 'List_IDs');

		if (!$sQL instanceof \Evoke\Core\DB\SQL)
		{
			throw new \InvalidArgumentException(__METHOD__ . ' needs SQL');
		}

		$this->fields    = $fields;
		$this->tableName = $tableName;
		$this->SQL       = $sQL;
	}

	/******************/
	/* Public Methods */
	/******************/

	/** Get a new List ID from the List_IDs table.
	 *  @param table \string The table name to get the List_ID for.
	 *  @param field \string The table field to get the List_ID for.
	 *  \return The new List_ID value or an exception is raised.
	 */
	public function getNew($table, $field)
	{
		if (!$this->SQL->inTransaction())
		{
			throw new \LogicException(
				__METHOD__ . ' we must be in a transaction to get a new List ID.');
		}
	 
		try
		{
			$listID = $this->SQL->selectSingleValue(
				$this->tableName,
				$this->fields['Counter'],
				array($this->fields['DB_Table'] => $table,
				      $this->fields['DB_Field'] => $field));

			if ($listID === false)
			{
				return NULL;
			}
	 
			$this->SQL->update($this->tableName,
			                   array($this->fields['Counter'] => ++$listID),
			                   array($this->fields['DB_Table'] => $table,
			                         $this->fields['DB_Field'] => $field));

			return $listID;
		}
		catch (\Exception $E)
		{
			throw new \Evoke\Core\Exception\DB(
				__METHOD__, 'Unable to get new list ID for table: ' . $table .
				' field: ' . $field, $this->SQL, $E);
		}
	}   
}
// EOF