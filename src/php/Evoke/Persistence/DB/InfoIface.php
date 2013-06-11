<?php
namespace Evoke\Persistence\DB;

/**
 * InfoIface
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package Persistence
 */
interface InfoIface
{
	/**
	 * Get the description of the database table.
	 *
	 * @return string
	 */
	public function getDescription();
   
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
}
// EOF