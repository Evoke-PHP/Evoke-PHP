<?php
namespace Evoke\Persistence\DB\Table;

use Exception,
	LogicException;

/**
 * ListID
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package Persistence
 */
class ListID implements ListIDIface
{
	/**
	 * The fields used in the List ID table.
	 * @var string[]
	 */
	private $fields;

	/**
	 * The name of the list id table.
	 * @var string
	 */
	private $tableName;

	/**
	 * PDO object.
	 * @var PDO
	 */
	protected $pdo;

	/**
	 * Construct a List ID object.
	 *
	 * @param Evoke\Persistence\DB\SQLIface
	 *                 SQL object.
	 * @param string[] Fields used in the List ID table.
	 * @param string   The table name of the List ID table.
	 */
	public function __construct(
		PDO          $pdo,
		Array        $fields    = array('Counter'  => 'Counter',
		                                'DB_Table' => 'DB_Table',
		                                'DB_Field' => 'DB_Field'),
		/* String */ $tableName = 'List_IDs')
	{
		$this->fields    = $fields;
		$this->pdo       = $pdo;
		$this->tableName = $tableName;
	}

	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Get a new List ID from the List_IDs table.
	 *
	 * @param string The table name to get the List_ID for.
	 * @param string The table field to get the List_ID for.
	 *
	 * @return mixed The new List_ID value or an exception is raised.
	 */
	public function getNew($table, $field)
	{
		if (!$this->pdo->inTransaction())
		{
			throw new LogicException(
				__METHOD__ . ' we must be in a transaction to get a new List ' .
				'ID.');
		}
	 
		try
		{
			$listID = $this->pdo->selectSingleValue(
				$this->tableName,
				$this->fields['Counter'],
				array($this->fields['DB_Table'] => $table,
				      $this->fields['DB_Field'] => $field));

			if ($listID === false)
			{
				return NULL;
			}
	 
			$this->pdo->update($this->tableName,
			                   array($this->fields['Counter'] => ++$listID),
			                   array($this->fields['DB_Table'] => $table,
			                         $this->fields['DB_Field'] => $field));

			return $listID;
		}
		catch (Exception $e)
		{
			throw new ExceptionDB(
				__METHOD__, 'Unable to get new list ID for table: ' . $table .
				' field: ' . $field, $this->pdo, $e);
		}
	}   
}
// EOF