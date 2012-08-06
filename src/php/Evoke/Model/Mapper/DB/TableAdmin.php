<?php
namespace Evoke\Model\Mapper\DB;

use Evoke\Model\AdminIface;

/**
 * TableAdmin
 *
 * Provide an Admin (CRUD) interface to a database table.
 * (CRUD - Create=Add, Read=Fetch, Update=Modify, Delete=Delete).
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package Model
 */
class TableAdmin extends Table implements AdminIface
{
	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Add a record to the table.
	 *
	 * @param mixed[] The record to add.
	 */
	public function add(Array $record)
	{
		$this->sql->insert(
			$this->tableName, array_keys($record), $record);
	}
	
	/**
	 * Delete a record from the table.
	 *
	 * @param mixed[] The record to delete.
	 */
	public function delete(Array $record)
	{
		$this->sql->delete($this->tableName, $record);
	}
			
	/**
	 * Modify a record in the table.
	 *  
	 * @param mixed[] The record to modify.
	 * @param mixed[] The value to set the record to.
	 */
	public function modify(Array $oldRecord, Array $newRecord)
	{
		$this->sql->update($this->tableName, $record, $oldRecord);
	}
}
// EOF