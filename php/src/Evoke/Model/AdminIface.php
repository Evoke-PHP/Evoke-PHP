<?php
namespace Evoke\Model;

interface AdminIface
{   
	/** Add a record.
	 *  @param record @array The record to add.
	 */
	public function add(Array $record);
	
	/** Delete a record.
	 *  @param record @array The record to delete.
	 */
	public function delete(Array $record);
			
	/** Modify a record.
	 *  @param oldRecord @array The record to modify.
	 *  @param newRecord @array The value to set the record to.
	 */
	public function modify(Array $oldRecord, Array $newRecord);
}
// EOF