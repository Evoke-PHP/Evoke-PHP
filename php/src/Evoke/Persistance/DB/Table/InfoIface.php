<?php
namespace Evoke\Persistance\DB\Table;

/**
 * InfoIface
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package Persistance
 */
interface InfoIface extends \Evoke\Service\ValiditionIface
{
	/**
	 * Get the description of the database table.
	 *
	 * @return string
	 */
	public function getDescription();

	/**
	 * Get a copy of the failure array object showing the last failures from an
	 * action.
	 *
	 * @return The failure array object.
	 */
	public function getFailures();
   
	/**
	 * Get the fields in the database table.
	 *
	 * @return mixed[]
	 */
	public function getFields();

	/**
	 * Get the foreign keys.
	 *
	 * @return mixed[]
	 */
	public function getForeignKeys();
   
	/**
	 * Get the primary keys.
	 *
	 * @return mixed[]
	 */
	public function getPrimaryKeys();

	/**
	 * Get the required fields.
	 *
	 * @return mixed[]
	 */
	public function getRequired();

	/**
	 * Get the table name.
	 *
	 * @return string
	 */
	public function getTableName();
   
	/**
	 * Get the type of the specified field.
	 *
	 * @return string
	 */
	public function getType($field);

	/**
	 * Get the types of all of the fields in the table.
	 *
	 * @return mixed[]
	 */
	public function getTypes();

	/**
	 * Return whether the database requires the field.
	 *
	 * @param string The field to check.
	 *
	 * @return bool
	 */
	public function isRequired($field);
   
	/**
	 * Check whether a set of fields would be valid for an insert or delete
	 * statement.
	 *
	 * @param mixed[] The set of fields to check.
	 * @param mixed[] Any fields that should be ignored in the calculation of
	 *                the validity.
	 *
	 * @return bool Whether the fieldset is valid for an insert or delete
	 *              statement. If the return is false `getFailures` can be used
	 *              to retrieve the errors.
	 */
	public function isValid($fieldset, $ignoredFields=array());
}
// EOF