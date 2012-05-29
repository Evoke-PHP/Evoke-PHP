<?php
namespace Evoke\DB\Table;

interface InfoIface extends \Evoke\Iface\Validity
{
	/// Get the description of the database table.
	public function getDescription();
   
	/// Get the fields in the database table.
	public function getFields();
	
	/// Get the foreign keys.
	public function getForeignKeys();
   
	/// Get the primary keys.
	public function getPrimaryKeys();

	/// Get the required fields.
	public function getRequired();

	/// Get the table name.
	public function getTableName();
   
	/// Get the type of the specified field.
	public function getType($field);

	/// Get the types of all of the fields in the table.
	public function getTypes();

	/// Return whether the database requires the field.
	public function isRequired($field);
   
	/** Get a copy of the failure array object showing the last failures from an
	 *  action.
	 *  \return The failure array object.
	 */
	public function getFailures();

	/** Check whether a set of fields would be valid for an insert or delete
	 *  statement.  
	 *  @param fieldset \array The set of fields to check.
	 *  @param ignoredFields \array Any fields that should be ignored in the
	 *  calculation of the validity.
	 *  \return A \bool of whether the fieldset is valid for an insert or
	 *  delete statement. If the return is false \ref getFailures can be used
	 *  to retrieve the errors.
	 */
	public function isValid($fieldset, $ignoredFields=array());

}
// EOF