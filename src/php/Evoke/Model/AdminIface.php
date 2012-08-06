<?php
namespace Evoke\Model;

/**
 * AdminIface
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package Model
 */
interface AdminIface
{   
	/**
	 * Add a record.
	 *
	 * @param mixed[] The record to add.
	 */
	public function add(Array $record);
	
	/**
	 * Delete a record.
	 *
	 * @param mixed[] The record to delete.
	 */
	public function delete(Array $record);
			
	/**
	 * Modify a record.
	 *
	 * @param mixed[] The record to modify.
	 * @param mixed[] The value to set the record to.
	 */
	public function modify(Array $oldRecord, Array $newRecord);
}
// EOF