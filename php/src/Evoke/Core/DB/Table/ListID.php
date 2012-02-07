<?php
namespace Evoke\Core\DB\Table;

class List_ID
{
	private $setup;
	protected $sql;
   
	public function __construct($setup=array())
	{
		$this->setup = array_merge(
			array('Fields' => array('Counter'  => 'Counter',
			                        'DB_Table' => 'DB_Table',
			                        'DB_Field' => 'DB_Field'),
			      'Table_Name' => 'List_IDs',
			      'SQL' => NULL),
			$setup);

		if (!$this->setup['SQL'] instanceof \Evoke\Core\DB\SQL)
		{
			throw new \InvalidArgumentException(__METHOD__ . ' needs SQL');
		}

		$this->sql = $this->setup['SQL'];
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
		try
		{
			/** \todo Trigger a warning or error if we are not in a transaction
			 *  already.
			 */
	 
			$listID = $this->sql->selectSingleValue(
				$this->setup['Table_Name'],
				$this->setup['Fields']['Counter'],
				array($this->setup['Fields']['DB_Table'] => $table,
				      $this->setup['Fields']['DB_Field'] => $field));

			if ($listID === false)
			{
				return NULL;
			}
	 
			$this->sql->update(
				$this->setup['Table_Name'],
				array($this->setup['Fields']['Counter'] => ++$listID),
				array($this->setup['Fields']['DB_Table'] => $table,
				      $this->setup['Fields']['DB_Field'] => $field));

			return $listID;
		}
		catch (\Exception $e)
		{
			throw new \Evoke\Core\Exception\DB(
				__METHOD__, 'Unable to get new list ID for table: ' . $table .
				' field: ' . $field, $this->sql, $e);
		}
	}   
}
// EOF