<?php
namespace Evoke\Persistance\DB\Table;

use Evoke\Message\Exception\DB as ExceptionDB,
	Evoke\Persistance\DB\SQLIface,
	Exception,
	LogicException;

class ListID
{
	private $fields;
	private $tableName;

	protected $sql;
   
	public function __construct(
		SQLIface     $sql,
		Array        $fields    = array('Counter'  => 'Counter',
		                                'DB_Table' => 'DB_Table',
		                                'DB_Field' => 'DB_Field'),
		/* String */ $tableName = 'List_IDs')
	{
		$this->fields    = $fields;
		$this->sql       = $sql;
		$this->tableName = $tableName;
	}

	/******************/
	/* Public Methods */
	/******************/

	/** Get a new List ID from the List_IDs table.
	 *  @param table @string The table name to get the List_ID for.
	 *  @param field @string The table field to get the List_ID for.
	 *  @return The new List_ID value or an exception is raised.
	 */
	public function getNew($table, $field)
	{
		if (!$this->sql->inTransaction())
		{
			throw new LogicException(
				__METHOD__ . ' we must be in a transaction to get a new List ID.');
		}
	 
		try
		{
			$listID = $this->sql->selectSingleValue(
				$this->tableName,
				$this->fields['Counter'],
				array($this->fields['DB_Table'] => $table,
				      $this->fields['DB_Field'] => $field));

			if ($listID === false)
			{
				return NULL;
			}
	 
			$this->sql->update($this->tableName,
			                   array($this->fields['Counter'] => ++$listID),
			                   array($this->fields['DB_Table'] => $table,
			                         $this->fields['DB_Field'] => $field));

			return $listID;
		}
		catch (Exception $e)
		{
			throw new ExceptionDB(
				__METHOD__, 'Unable to get new list ID for table: ' . $table .
				' field: ' . $field, $this->sql, $e);
		}
	}   
}
// EOF