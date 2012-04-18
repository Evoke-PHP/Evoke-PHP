<?php
namespace Evoke\Iface\Model;

interface Admin extends \Evoke\Iface\Model
{   
	/// Add a record.
	public function add($record);

	/// Cancel any currently edited record.
	public function cancel();

	/// Begin creating a new record.
	public function createNew();

	/// Cancel the currently requested deletion.
	public function deleteCancel();
   
	/// Delete a record (normally after confirmation from the user).
	public function deleteConfirm(Array $record);

	/** Request that a record should be deleted, but only after confirmation
	 *  from the user.
	 */
	public function deleteRequest($record);   
   
	/// Set a record for editing.
	public function edit($record);

	/// Modify a record.
	public function modify($record);
}
// EOF