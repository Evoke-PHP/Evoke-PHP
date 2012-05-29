<?php
namespace Evoke\Model\Mapper\DB;

use Evoke\Model\AdminIface;

/** Provide an Admin (CRUD) interface to a database table.
 *  (CRUD - Create=Add, Read=Fetch, Update=Modify, Delete=Delete).
 */
class TableAdmin extends Table implements AdminIface
{
	/******************/
	/* Public Methods */
	/******************/

	/** Add a record to the table.
	 *  @param record @array The record to add.
	 */
	public function add(Array $record)
	{
		$this->sql->insert(
			$this->tableName, array_keys($record), $record);
	}
	
	/** Delete a record from the table.
	 *  @param record @array The record to delete.
	 */
	public function delete(Array $record)
	{
		$this->sql->delete($this->tableName, $record);
	}
			
	/** Modify a record in the table.
	 *  @param oldRecord @array The record to modify.
	 *  @param newRecord @array The value to set the record to.
	 */
	public function modify(Array $oldRecord, Array $newRecord)
	{
		$this->sql->update($this->tableName, $record, $oldRecord);
	}
}
// EOF